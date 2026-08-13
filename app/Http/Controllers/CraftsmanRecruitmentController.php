<?php

namespace App\Http\Controllers;

use App\Models\CraftsmanLead;
use App\Models\Category;
use App\Models\Location;
use App\Models\User;
use App\Notifications\ReferralConvertedNotification;
use App\Services\AdminNotificationService;
use App\Services\ImageCompressionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CraftsmanRecruitmentController extends Controller
{
    // Meserii disponibile în formular
    public const TRADES = [
        'electrician' => 'Electrician',
        'instalator'  => 'Instalator',
        'tamplar'     => 'Tâmplar',
        'zugrav'      => 'Zugrav',
        'mecanic'     => 'Mecanic',
    ];

    /**
     * Pagina publică cu formularul de recrutare.
     */
    public function showForm(Request $request)
    {
        $trade = $request->query('meserie');
        return view('recruitment.form', [
            'trades'         => self::TRADES,
            'selectedTrade'  => $trade,
            'referralCode'   => $request->query('ref'),
        ]);
    }

    /**
     * Procesarea submitului formularului.
     */
    public function store(Request $request, AdminNotificationService $adminNotifier)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:100',
            'phone'            => 'required|string|max:20',
            'city'             => 'required|string|max:100',
            'trade'            => 'required|in:electrician,instalator,tamplar,zugrav,mecanic',
            'experience_range' => 'required|in:0-2,3-5,5+',
            'email'            => 'nullable|email|max:255',
            'profile_photo'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'work_photo'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'ref'              => 'nullable|string|max:255',
        ]);

        $referrer = null;
        if (!empty($validated['ref'])) {
            $referrer = User::where('slug', $validated['ref'])
                ->where('role', 'specialist')
                ->first();
        }

        // Upload-uri opționale
        $profilePhotoPath = null;
        if ($request->hasFile('profile_photo')) {
            $profilePhotoPath = $request->file('profile_photo')
                ->store('leads/photos', 'public');
        }

        $workPhotoPath = null;
        if ($request->hasFile('work_photo')) {
            $workPhotoPath = $request->file('work_photo')
                ->store('leads/work', 'public');
        }

        $lead = CraftsmanLead::create([
            'name'             => $validated['name'],
            'phone'            => $validated['phone'],
            'city'             => $validated['city'],
            'trade'            => $validated['trade'],
            'experience_range' => $validated['experience_range'],
            'email'            => $validated['email'] ?? null,
            'profile_photo'    => $profilePhotoPath,
            'work_photo'       => $workPhotoPath,
            'status'           => 'nou',
            'utm_source'       => $request->query('utm_source'),
            'utm_medium'       => $request->query('utm_medium'),
            'utm_campaign'     => $request->query('utm_campaign'),
            'referred_by_user_id' => $referrer?->id,
        ]);

        // Trimite email de confirmare dacă a furnizat adresa
        if ($lead->email) {
            $this->sendConfirmationEmail($lead);
        }

        // Notifică adminul despre lead-ul nou
        $leadUrl = url('/admin/leads');
        $adminNotifier->send(
            "Lead nou recrutare meseriaș: {$lead->name} (" . (self::TRADES[$lead->trade] ?? $lead->trade) . ")",
            "Un meseriaș nou s-a înscris prin formularul public de recrutare:\n\n" .
            "Nume: {$lead->name}\n" .
            "Telefon: {$lead->phone}\n" .
            "Oraș: {$lead->city}\n" .
            "Meserie: " . (self::TRADES[$lead->trade] ?? $lead->trade) . "\n" .
            "Experiență: {$lead->experience_range} ani\n" .
            "Email: " . ($lead->email ?: '-') . "\n" .
            ($referrer ? "Recomandat de: {$referrer->name}\n" : '') .
            "\nVezi lead-ul: {$leadUrl}"
        );

        return redirect()->route('recruitment.success', ['id' => $lead->id])
            ->with('success', true);
    }

    /**
     * Pagina de confirmare după submit.
     */
    public function success(Request $request, int $id)
    {
        $lead = CraftsmanLead::findOrFail($id);
        return view('recruitment.success', compact('lead'));
    }

    /**
     * Activarea contului prin token de invitație (trimis de admin).
     */
    public function activateForm(string $token)
    {
        $lead = CraftsmanLead::where('invite_token', $token)
            ->whereNull('account_created_at')
            ->firstOrFail();

        return view('recruitment.activate', [
            'lead'   => $lead,
            'token'  => $token,
            'trades' => self::TRADES,
        ]);
    }

    /**
     * Crearea contului din formularul de activare.
     */
    public function activateStore(Request $request, string $token, AdminNotificationService $adminNotifier)
    {
        $lead = CraftsmanLead::where('invite_token', $token)
            ->whereNull('account_created_at')
            ->firstOrFail();

        $validated = $request->validate([
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'terms'    => 'accepted',
        ]);

        // Găsim sau creăm categoria după meserie
        $categoryMap = [
            'electrician' => 'Electrician',
            'instalator'  => 'Instalator',
            'tamplar'     => 'Tâmplar',
            'zugrav'      => 'Zugrav',
            'mecanic'     => 'Mecanic',
        ];
        $category = Category::where('name', 'LIKE', '%' . ($categoryMap[$lead->trade] ?? $lead->trade) . '%')->first();

        // Slug unic
        $slug = Str::slug($lead->name);
        $originalSlug = $slug;
        $counter = 1;
        while (User::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        $user = User::create([
            'name'             => $lead->name,
            'email'            => $validated['email'],
            'password'         => Hash::make($validated['password']),
            'phone'            => $lead->phone,
            'role'             => 'specialist',
            'slug'             => $slug,
            'category_id'      => $category?->id,
            'experience_years' => $this->parseExperienceYears($lead->experience_range),
            'profile_photo'    => $lead->profile_photo,
            'is_active'        => false, // Necesită aprobare admin
        ]);

        $lead->markAsRegistered($user->id);

        // Trimite notificare email la admin
        $profileUrl = url('/admin/craftsmen/' . $user->id . '/edit');
        $adminNotifier->send(
            "Cont nou meseriaș (recrutare): {$user->name} - meseriasionline.ro",
            "Un lead de recrutare și-a activat contul:\n\n" .
            "Nume: {$user->name}\n" .
            "Email: {$user->email}\n" .
            "Telefon: {$user->phone}\n" .
            "Meserie: " . (self::TRADES[$lead->trade] ?? $lead->trade) . "\n" .
            "Oraș: {$lead->city}\n\n" .
            "Contul este inactiv și necesită aprobare.\n" .
            "Activează contul: {$profileUrl}"
        );

        if ($lead->referred_by_user_id) {
            $this->rewardReferrer($lead, $adminNotifier);
        }

        auth()->login($user);

        return redirect()->route('onboarding.step', ['step' => 1])
            ->with('success', 'Contul tău a fost creat! Completează profilul pentru a primi clienți.');
    }

    // ─── Helpers private ─────────────────────────────────────────────────────

    /**
     * Recompensează meseriașul care a recomandat lead-ul, la conversia acestuia.
     */
    private function rewardReferrer(CraftsmanLead $lead, AdminNotificationService $adminNotifier): void
    {
        $referrer = $lead->referredBy;
        if (!$referrer) {
            return;
        }

        $subscription = $referrer->activeSubscription();
        $rewardGiven = false;

        if ($subscription && $subscription->ends_at) {
            $subscription->update(['ends_at' => $subscription->ends_at->addDays(30)]);
            $rewardGiven = true;
        }

        $lead->update(['referral_reward_given' => $rewardGiven]);

        $referrer->notify(new ReferralConvertedNotification($lead, $rewardGiven));

        $adminNotifier->send(
            "Recomandare convertită: {$referrer->name} → {$lead->name}",
            "{$referrer->name} a recomandat un coleg care tocmai și-a creat cont: {$lead->name} (" .
            (self::TRADES[$lead->trade] ?? $lead->trade) . ").\n\n" .
            ($rewardGiven
                ? "Recompensă acordată automat: 30 de zile în plus la abonamentul activ al lui {$referrer->name}.\n"
                : "{$referrer->name} nu are un abonament activ cu dată de expirare — nu s-a acordat automat nicio recompensă. Poți decide manual o recompensă.\n") .
            "Profil recomandant: " . url('/admin/craftsmen/' . $referrer->id . '/edit')
        );
    }

    private function sendConfirmationEmail(CraftsmanLead $lead): void
    {
        try {
            Mail::send('emails.recruitment-confirmation', ['lead' => $lead], function ($message) use ($lead) {
                $message->to($lead->email, $lead->name)
                    ->subject('Înregistrarea ta pe meseriasionline.ro — confirmare');
            });
        } catch (\Throwable $e) {
            // Email eșuat — nu blocăm flow-ul
            \Log::warning('Recruitment confirmation email failed: ' . $e->getMessage());
        }
    }

    private function parseExperienceYears(string $range): int
    {
        return match ($range) {
            '0-2'  => 1,
            '3-5'  => 4,
            '5+'   => 6,
            default => 0,
        };
    }
}
