<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CraftsmanLead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CraftsmanLeadController extends Controller
{
    /**
     * Lista tuturor lead-urilor cu filtre.
     */
    public function index(Request $request)
    {
        $query = CraftsmanLead::query()->latest();

        if ($request->filled('trade')) {
            $query->where('trade', $request->trade);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('city')) {
            $query->where('city', 'LIKE', '%' . $request->city . '%');
        }

        $leads = $query->paginate(25)->withQueryString();

        // Statistici rapide
        $stats = [
            'total'       => CraftsmanLead::count(),
            'new'         => CraftsmanLead::where('status', 'nou')->count(),
            'invited'     => CraftsmanLead::where('status', 'invitat')->count(),
            'converted'   => CraftsmanLead::where('status', 'inregistrat')->count(),
        ];

        // Per meserie
        $perTrade = CraftsmanLead::selectRaw("trade, count(*) as total, sum(status = 'inregistrat') as converted")
            ->groupBy('trade')
            ->get()
            ->keyBy('trade')
            ->toArray();

        return view('admin.leads.index', compact('leads', 'stats', 'perTrade'));
    }

    /**
     * Formular adăugare manuală lead (prospect identificat de admin).
     */
    public function create()
    {
        return view('admin.leads.create');
    }

    /**
     * Salvare lead adăugat manual din admin.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:100',
            'phone'            => 'required|string|max:20',
            'city'             => 'required|string|max:100',
            'trade'            => 'required|in:electrician,instalator,tamplar,zugrav,mecanic',
            'experience_range' => 'required|in:0-2,3-5,5+',
            'email'            => 'nullable|email|max:255',
            'status'           => 'required|in:nou,contactat,invitat,inregistrat,respins',
            'admin_notes'      => 'nullable|string|max:1000',
        ]);

        $lead = CraftsmanLead::create([
            ...$validated,
            'utm_source' => 'admin',
        ]);

        return redirect()->route('admin.leads.show', $lead)
            ->with('success', 'Lead adăugat cu succes.');
    }

    /**
     * Detaliu lead.
     */
    public function show(CraftsmanLead $lead)
    {
        return view('admin.leads.show', compact('lead'));
    }

    /**
     * Actualizare status și note admin.
     */
    public function update(Request $request, CraftsmanLead $lead)
    {
        $validated = $request->validate([
            'status'      => 'required|in:nou,contactat,invitat,inregistrat,respins',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $lead->update($validated);

        return back()->with('success', 'Lead actualizat cu succes.');
    }

    /**
     * Trimite invitație prin email.
     */
    public function sendInvite(CraftsmanLead $lead)
    {
        if (! $lead->email) {
            return back()->with('error', 'Lead-ul nu are adresă de email.');
        }

        if ($lead->status === 'inregistrat') {
            return back()->with('error', 'Acest lead a creat deja un cont.');
        }

        $token = $lead->generateInviteToken();
        $activationUrl = route('recruitment.activate', ['token' => $token]);

        try {
            Mail::send('emails.recruitment-invite', [
                'lead'          => $lead,
                'activationUrl' => $activationUrl,
            ], function ($message) use ($lead) {
                $message->to($lead->email, $lead->name)
                    ->subject('Invitație să îți creezi contul pe meseriasionline.ro');
            });

            return back()->with('success', 'Invitația a fost trimisă la ' . $lead->email);
        } catch (\Throwable $e) {
            \Log::error('Lead invite email failed: ' . $e->getMessage());
            return back()->with('error', 'Eroare la trimiterea emailului. Verifică configurarea mail.');
        }
    }

    /**
     * Copiază link-ul de activare (fără email — pentru WhatsApp manual).
     */
    public function getActivationLink(CraftsmanLead $lead)
    {
        if ($lead->status === 'inregistrat') {
            return response()->json(['error' => 'Lead deja înregistrat.'], 422);
        }

        // Dacă nu are deja token, generează unul
        if (! $lead->invite_token) {
            $lead->generateInviteToken();
        }

        return response()->json([
            'link' => route('recruitment.activate', ['token' => $lead->invite_token]),
        ]);
    }

    /**
     * Ștergere lead.
     */
    public function destroy(CraftsmanLead $lead)
    {
        $lead->delete();
        return redirect()->route('admin.leads.index')
            ->with('success', 'Lead-ul a fost șters.');
    }
}
