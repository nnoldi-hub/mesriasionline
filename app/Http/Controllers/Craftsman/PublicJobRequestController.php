<?php

namespace App\Http\Controllers\Craftsman;

use App\Http\Controllers\Controller;
use App\Models\PublicJobRequest;
use App\Models\PublicJobRequestResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PublicJobRequestController extends Controller
{
    /**
     * Lista cereri publice disponibile pentru meseriaș.
     */
    public function index()
    {
        $craftsman = Auth::user();

        $jobRequests = PublicJobRequest::with(['category', 'location'])
            ->where('status', 'open')
            ->where('category_id', $craftsman->category_id)
            ->where(function ($q) use ($craftsman) {
                // Aceeași locație SAU fără locație specificată
                $q->whereNull('location_id')
                  ->orWhere('location_id', $craftsman->location_id);
            })
            ->orderByRaw("FIELD(urgency, 'urgent', 'this_week', 'flexible')")
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Marchează care au răspuns deja
        $respondedIds = PublicJobRequestResponse::where('craftsman_id', $craftsman->id)
            ->pluck('public_job_request_id')
            ->toArray();

        return view('craftsman.public-requests.index', compact('jobRequests', 'respondedIds'));
    }

    /**
     * Detaliu cerere publică.
     */
    public function show(PublicJobRequest $publicJobRequest)
    {
        $craftsman = Auth::user();

        $publicJobRequest->load(['category', 'location']);

        $myResponse = PublicJobRequestResponse::where('public_job_request_id', $publicJobRequest->id)
            ->where('craftsman_id', $craftsman->id)
            ->first();

        // Marchează ca văzut dacă nu a răspuns încă
        if (!$myResponse) {
            PublicJobRequestResponse::create([
                'public_job_request_id' => $publicJobRequest->id,
                'craftsman_id'          => $craftsman->id,
                'action'                => 'viewed',
            ]);
        }

        return view('craftsman.public-requests.show', compact('publicJobRequest', 'myResponse'));
    }

    /**
     * Meseriaș marchează interes / trimite mesaj clientului.
     */
    public function respond(Request $request, PublicJobRequest $publicJobRequest)
    {
        $craftsman = Auth::user();

        $validated = $request->validate([
            'action'  => 'required|in:interested,not_interested',
            'message' => 'nullable|string|max:1000',
        ]);

        PublicJobRequestResponse::updateOrCreate(
            [
                'public_job_request_id' => $publicJobRequest->id,
                'craftsman_id'          => $craftsman->id,
            ],
            [
                'action'  => $validated['action'],
                'message' => $validated['message'] ?? null,
            ]
        );

        if ($validated['action'] === 'interested') {
            // Trimite email clientului cu datele meseriaşului
            try {
                \Illuminate\Support\Facades\Mail::mailer('log')->send(
                    ['html' => 'emails.craftsman-interested', 'text' => 'emails.craftsman-interested-text'],
                    [
                        'jobRequest'    => $publicJobRequest,
                        'craftsman'     => $craftsman,
                        'message'       => $validated['message'] ?? null,
                        'profileUrl'    => route('craftsman.show', $craftsman->slug),
                        'facebookUrl'   => \App\Models\PlatformSetting::getValue('facebook_url'),
                        'instagramUrl'  => \App\Models\PlatformSetting::getValue('instagram_url'),
                        'tiktokUrl'     => \App\Models\PlatformSetting::getValue('tiktok_url'),
                        'youtubeUrl'    => \App\Models\PlatformSetting::getValue('youtube_url'),
                    ],
                    function ($mail) use ($publicJobRequest, $craftsman) {
                        $mail->to($publicJobRequest->email)
                             ->subject("Ofertă de la {$craftsman->name} pentru cererea ta");
                    }
                );
            } catch (\Exception $e) {
                \Log::warning("Client notification email failed for public job request #{$publicJobRequest->id}: " . $e->getMessage());
            }

            return back()->with('success', 'Clientul a fost notificat cu datele tale de contact!');
        }

        return back()->with('success', 'Răspuns înregistrat.');
    }
}
