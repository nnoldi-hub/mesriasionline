<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CraftsmanLead;
use App\Services\AdminNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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
     * Ghid cu idei de recrutare pentru admin.
     */
    public function guide()
    {
        return view('admin.leads.guide');
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
     * Creare directă a contului de meseriaș de către admin (fără să aștepte
     * auto-activarea lead-ului). Generează o parolă temporară, o trimite
     * meseriașului prin email și îi trimite și adminului o copie cu datele.
     */
    public function createAccount(Request $request, CraftsmanLead $lead, AdminNotificationService $adminNotifier)
    {
        if ($lead->status === 'inregistrat') {
            return back()->with('error', 'Acest lead are deja un cont creat.');
        }

        $validated = $request->validate([
            'email' => 'required|email|max:255|unique:users,email',
        ]);

        $password = Str::random(10);

        $user = $lead->createUserAccount($validated['email'], $password);

        try {
            Mail::send('emails.account-created', [
                'lead'     => $lead,
                'user'     => $user,
                'password' => $password,
                'loginUrl' => route('login'),
            ], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                    ->subject('Contul tău pe meseriasionline.ro a fost creat');
            });
        } catch (\Throwable $e) {
            Log::error('Account-created email failed: ' . $e->getMessage());
        }

        $adminNotifier->send(
            "Cont creat manual: {$user->name} ({$user->email})",
            "Ai creat manual contul pentru {$user->name} ({$lead->trade_label}, {$lead->city}).\n\n" .
            "Email: {$user->email}\n" .
            "Parolă temporară: {$password}\n\n" .
            "Contul este inactiv și necesită aprobare: " . url('/admin/craftsmen/' . $user->id . '/edit')
        );

        if ($lead->referred_by_user_id) {
            $lead->rewardReferrer($adminNotifier);
        }

        return redirect()->route('admin.leads.show', $lead)
            ->with('success', "Cont creat cu succes pentru {$user->email}. Datele de conectare au fost trimise pe email, cu o copie la tine.");
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
