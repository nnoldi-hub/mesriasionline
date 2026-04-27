<?php

namespace App\Http\Controllers\Craftsman;

use App\Http\Controllers\Controller;
use App\Models\Certification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificationController extends Controller
{
    /**
     * Display certifications list.
     */
    public function index()
    {
        $craftsman = auth()->user();
        $certifications = $craftsman->certificationDocuments()->latest()->get();
        
        return view('craftsman.certifications.index', compact('certifications'));
    }

    /**
     * Show form to add new certification.
     */
    public function create()
    {
        return view('craftsman.certifications.create');
    }

    /**
     * Store a new certification.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'issuing_organization' => 'nullable|string|max:255',
            'issue_date' => 'nullable|date|before_or_equal:today',
            'expiry_date' => 'nullable|date|after:issue_date',
            'credential_id' => 'nullable|string|max:255',
            'credential_url' => 'nullable|url|max:500',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);
        
        $craftsman = auth()->user();
        
        $documentPath = null;
        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('certifications/' . $craftsman->id, 'public');
        }
        
        $craftsman->certificationDocuments()->create([
            'title' => $validated['title'],
            'issuing_organization' => $validated['issuing_organization'],
            'issue_date' => $validated['issue_date'],
            'expiry_date' => $validated['expiry_date'],
            'credential_id' => $validated['credential_id'],
            'credential_url' => $validated['credential_url'],
            'document_path' => $documentPath,
        ]);
        
        return redirect()->route('craftsman.certifications.index')
            ->with('success', 'Certificarea a fost adăugată cu succes!');
    }

    /**
     * Show form to edit certification.
     */
    public function edit(Certification $certification)
    {
        $craftsman = auth()->user();
        
        if ($certification->user_id !== $craftsman->id) {
            abort(403);
        }
        
        return view('craftsman.certifications.edit', compact('certification'));
    }

    /**
     * Update certification.
     */
    public function update(Request $request, Certification $certification)
    {
        $craftsman = auth()->user();
        
        if ($certification->user_id !== $craftsman->id) {
            abort(403);
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'issuing_organization' => 'nullable|string|max:255',
            'issue_date' => 'nullable|date|before_or_equal:today',
            'expiry_date' => 'nullable|date|after:issue_date',
            'credential_id' => 'nullable|string|max:255',
            'credential_url' => 'nullable|url|max:500',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);
        
        $documentPath = $certification->document_path;
        if ($request->hasFile('document')) {
            // Delete old document
            if ($documentPath) {
                Storage::disk('public')->delete($documentPath);
            }
            $documentPath = $request->file('document')->store('certifications/' . $craftsman->id, 'public');
        }
        
        $certification->update([
            'title' => $validated['title'],
            'issuing_organization' => $validated['issuing_organization'],
            'issue_date' => $validated['issue_date'],
            'expiry_date' => $validated['expiry_date'],
            'credential_id' => $validated['credential_id'],
            'credential_url' => $validated['credential_url'],
            'document_path' => $documentPath,
        ]);
        
        return redirect()->route('craftsman.certifications.index')
            ->with('success', 'Certificarea a fost actualizată.');
    }

    /**
     * Delete certification.
     */
    public function destroy(Certification $certification)
    {
        $craftsman = auth()->user();
        
        if ($certification->user_id !== $craftsman->id) {
            abort(403);
        }
        
        // Delete document file
        if ($certification->document_path) {
            Storage::disk('public')->delete($certification->document_path);
        }
        
        $certification->delete();
        
        return redirect()->route('craftsman.certifications.index')
            ->with('success', 'Certificarea a fost ștearsă.');
    }
}
