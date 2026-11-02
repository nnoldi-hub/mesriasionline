<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CraftsmanLead;
use App\Models\CraftsmanLeadCall;
use App\Support\CallOutcome;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * „De sunat azi”: lista zilnică de apeluri pentru recrutarea meseriașilor.
 * Regula de bază: niciun lead deschis fără următorul pas programat.
 */
class LeadCallController extends Controller
{
    /** Ținta zilnică de apeluri. */
    private const DAILY_GOAL = 20;

    public function today()
    {
        $open = CraftsmanLead::query()
            ->whereNotIn('status', ['inregistrat', 'respins'])
            ->with('latestCall');

        // 1. Restanțe și programate pentru azi (cele mai vechi primele)
        $due = (clone $open)
            ->whereNotNull('next_action_at')
            ->where('next_action_at', '<=', now()->endOfDay())
            ->orderBy('next_action_at')
            ->get();

        // 2. Lead-uri nesunate niciodată (inclusiv cele venite din formularul public)
        $fresh = (clone $open)
            ->whereNull('next_action_at')
            ->whereNull('last_contacted_at')
            ->latest()
            ->limit(50)
            ->get();

        $todayCalls = CraftsmanLeadCall::whereDate('created_at', today())->get();

        $stats = [
            'calls'    => $todayCalls->count(),
            'talked'   => $todayCalls->whereIn('outcome', CallOutcome::TALKED)->count(),
            'accepted' => $todayCalls->where('outcome', 'accepta')->count(),
            'accounts' => CraftsmanLead::whereDate('account_created_at', today())->count(),
            'goal'     => self::DAILY_GOAL,
        ];

        // Ce te învață refuzurile din ultimele 30 de zile
        $refusals = CraftsmanLeadCall::where('outcome', 'refuzat')
            ->where('created_at', '>=', now()->subDays(30))
            ->whereNotNull('refusal_reason')
            ->selectRaw('refusal_reason, count(*) as total')
            ->groupBy('refusal_reason')
            ->orderByDesc('total')
            ->pluck('total', 'refusal_reason');

        return view('admin.leads.today', compact('due', 'fresh', 'stats', 'refusals'));
    }

    /** Adăugare rapidă direct din anunț: telefon + meserie + oraș + sursă. */
    public function quickStore(Request $request)
    {
        $data = $request->validate([
            'phone'      => ['required', 'string', 'max:20'],
            'name'       => ['nullable', 'string', 'max:100'],
            'trade'      => ['required', Rule::in(array_keys(CraftsmanLead::$tradeLabels))],
            'city'       => ['required', 'string', 'max:100'],
            'source'     => ['required', Rule::in(array_keys(CallOutcome::SOURCES))],
            'source_url' => ['nullable', 'url', 'max:255'],
        ]);

        $data['phone'] = CallOutcome::normalizePhone($data['phone']);
        $data['name']  = $data['name'] ?: 'Necunoscut';

        $existing = CraftsmanLead::where('phone', $data['phone'])->first();
        if ($existing) {
            return back()->withInput()->with('error',
                "Numărul {$data['phone']} există deja: {$existing->name} ({$existing->status_label}).");
        }

        CraftsmanLead::create($data + [
            'status'         => 'nou',
            'utm_source'     => 'admin',
            'next_action_at' => now(),
        ]);

        return back()->with('success', 'Adăugat în lista de azi.');
    }

    /** Înregistrează rezultatul unui apel și programează următorul pas. */
    public function log(Request $request, CraftsmanLead $lead)
    {
        $data = $request->validate([
            'outcome'          => ['required', Rule::in(array_keys(CallOutcome::formOutcomes()))],
            'refusal_reason'   => ['nullable', 'required_if:outcome,refuzat', Rule::in(array_keys(CallOutcome::REFUSAL_REASONS))],
            'next_action_at'   => ['nullable', 'date', 'after_or_equal:today'],
            'next_action_note' => ['nullable', 'string', 'max:255'],
            'note'             => ['nullable', 'string', 'max:2000'],
        ], [
            'refusal_reason.required_if'    => 'Alege motivul refuzului. Din motive înveți cel mai mult.',
            'next_action_at.after_or_equal' => 'Data următorului pas nu poate fi în trecut.',
        ]);

        $outcome  = $data['outcome'];
        $attempts = $lead->call_attempts + 1;
        $next     = CallOutcome::nextActionAt($outcome, $attempts, $data['next_action_at'] ?? null);

        if (! CallOutcome::isClosed($outcome) && ! $next) {
            return back()->withErrors(['next_action_at' => "Alege data la care revii la {$lead->name}."]);
        }

        $finalOutcome = $outcome;
        if ($outcome === 'fara_raspuns' && $attempts >= CallOutcome::MAX_NO_ANSWER) {
            $finalOutcome = 'nu_raspunde';
            $next = null;
        }

        $nextNote = $data['next_action_note'] ?? null;
        if ($outcome === 'accepta' && ! $nextNote) {
            $nextNote = 'Creează contul (buton în pagina lead-ului)';
        }

        DB::transaction(function () use ($lead, $data, $outcome, $finalOutcome, $attempts, $next, $nextNote, $request) {
            CraftsmanLeadCall::create([
                'craftsman_lead_id' => $lead->id,
                'user_id'           => $request->user()?->id,
                'outcome'           => $outcome,
                'refusal_reason'    => $data['refusal_reason'] ?? null,
                'note'              => $data['note'] ?? null,
            ]);

            $lead->update([
                'call_outcome'      => $finalOutcome,
                'call_attempts'     => $attempts,
                'last_contacted_at' => now(),
                'next_action_at'    => $next,
                'next_action_note'  => $nextNote,
                'refusal_reason'    => $data['refusal_reason'] ?? $lead->refusal_reason,
                'status'            => CallOutcome::statusFor($finalOutcome) ?? $lead->status,
                // nu mai trimite alertă de „lead netratat” pentru cineva deja sunat
                'stale_reminder_sent_at' => $lead->stale_reminder_sent_at ?? now(),
            ]);
        });

        if ($outcome === 'accepta') {
            return redirect()->route('admin.leads.show', $lead)
                ->with('success', "{$lead->name} a acceptat. Creează-i contul acum sau trimite-i linkul de activare pe WhatsApp.");
        }

        $msg = match ($finalOutcome) {
            'nu_raspunde' => "{$lead->name} scos din listă după {$attempts} apeluri fără răspuns.",
            'refuzat', 'numar_gresit' => "{$lead->name} marcat ca respins.",
            default => 'Salvat. Următorul pas: ' . $next->format('d.m H:i'),
        };

        return back()->with('success', $msg);
    }
}
