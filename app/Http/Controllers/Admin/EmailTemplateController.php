<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Services\EmailTemplateService;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    protected EmailTemplateService $templateService;

    public function __construct(EmailTemplateService $templateService)
    {
        $this->templateService = $templateService;
    }

    /**
     * Afișează lista de template-uri email.
     */
    public function index(Request $request)
    {
        $query = EmailTemplate::query();

        // Filtrare după categorie
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filtrare după tip notificare
        if ($request->filled('notification_type')) {
            $query->where('notification_type', $request->notification_type);
        }

        // Filtrare după status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $templates = $query->orderBy('category')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.email-templates.index', [
            'templates' => $templates,
            'categories' => EmailTemplate::CATEGORIES,
            'notificationTypes' => EmailTemplate::NOTIFICATION_TYPES,
        ]);
    }

    /**
     * Afișează formularul de creare template.
     */
    public function create()
    {
        return view('admin.email-templates.create', [
            'categories' => EmailTemplate::CATEGORIES,
            'notificationTypes' => EmailTemplate::NOTIFICATION_TYPES,
            'availableVariables' => EmailTemplate::AVAILABLE_VARIABLES,
        ]);
    }

    /**
     * Salvează un template nou.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'notification_type' => 'nullable|string|in:' . implode(',', array_keys(EmailTemplate::NOTIFICATION_TYPES)),
            'category' => 'required|string|in:' . implode(',', array_keys(EmailTemplate::CATEGORIES)),
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        $template = EmailTemplate::create([
            'name' => $validated['name'],
            'subject' => $validated['subject'],
            'body' => $validated['body'],
            'notification_type' => $validated['notification_type'] ?? null,
            'category' => $validated['category'],
            'is_active' => $request->boolean('is_active', true),
            'is_default' => $request->boolean('is_default', false),
        ]);

        return redirect()
            ->route('admin.email-templates.index')
            ->with('success', 'Template-ul "' . $template->name . '" a fost creat cu succes.');
    }

    /**
     * Afișează formularul de editare template.
     */
    public function edit(EmailTemplate $emailTemplate)
    {
        return view('admin.email-templates.edit', [
            'template' => $emailTemplate,
            'categories' => EmailTemplate::CATEGORIES,
            'notificationTypes' => EmailTemplate::NOTIFICATION_TYPES,
            'availableVariables' => EmailTemplate::AVAILABLE_VARIABLES,
        ]);
    }

    /**
     * Actualizează un template existent.
     */
    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'notification_type' => 'nullable|string|in:' . implode(',', array_keys(EmailTemplate::NOTIFICATION_TYPES)),
            'category' => 'required|string|in:' . implode(',', array_keys(EmailTemplate::CATEGORIES)),
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        $emailTemplate->update([
            'name' => $validated['name'],
            'subject' => $validated['subject'],
            'body' => $validated['body'],
            'notification_type' => $validated['notification_type'] ?? null,
            'category' => $validated['category'],
            'is_active' => $request->boolean('is_active', true),
            'is_default' => $request->boolean('is_default', false),
        ]);

        return redirect()
            ->route('admin.email-templates.index')
            ->with('success', 'Template-ul "' . $emailTemplate->name . '" a fost actualizat cu succes.');
    }

    /**
     * Șterge un template.
     */
    public function destroy(EmailTemplate $emailTemplate)
    {
        $name = $emailTemplate->name;
        $emailTemplate->delete();

        return redirect()
            ->route('admin.email-templates.index')
            ->with('success', 'Template-ul "' . $name . '" a fost șters.');
    }

    /**
     * Previzualizează un template.
     */
    public function preview(EmailTemplate $emailTemplate)
    {
        $preview = $this->templateService->preview($emailTemplate);

        return response()->json([
            'subject' => $preview['subject'],
            'body' => $preview['body'],
            'html' => $preview['mail']->render(),
        ]);
    }

    /**
     * Previzualizează un template în timp real (fără salvare).
     */
    public function previewLive(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string',
            'body' => 'required|string',
            'notification_type' => 'nullable|string',
        ]);

        $template = new EmailTemplate([
            'subject' => $validated['subject'],
            'body' => $validated['body'],
            'notification_type' => $validated['notification_type'],
        ]);

        $preview = $this->templateService->preview($template);

        return response()->json([
            'subject' => $preview['subject'],
            'body' => $preview['body'],
        ]);
    }

    /**
     * Toggle status activ/inactiv.
     */
    public function toggleStatus(EmailTemplate $emailTemplate)
    {
        $emailTemplate->update([
            'is_active' => !$emailTemplate->is_active,
        ]);

        $status = $emailTemplate->is_active ? 'activat' : 'dezactivat';

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'is_active' => $emailTemplate->is_active,
                'message' => "Template-ul a fost {$status}.",
            ]);
        }

        return back()->with('success', "Template-ul \"{$emailTemplate->name}\" a fost {$status}.");
    }

    /**
     * Setează un template ca default pentru tipul său.
     */
    public function setDefault(EmailTemplate $emailTemplate)
    {
        if (!$emailTemplate->notification_type) {
            return back()->with('error', 'Template-ul nu are un tip de notificare asociat.');
        }

        // Dezactivează alte template-uri default pentru acest tip
        EmailTemplate::where('notification_type', $emailTemplate->notification_type)
            ->where('id', '!=', $emailTemplate->id)
            ->update(['is_default' => false]);

        $emailTemplate->update(['is_default' => true]);

        return back()->with('success', "Template-ul \"{$emailTemplate->name}\" este acum default pentru {$emailTemplate->notification_type_name}.");
    }

    /**
     * Generează template-urile default.
     */
    public function seedDefaults()
    {
        $this->templateService->seedDefaultTemplates();

        return back()->with('success', 'Template-urile default au fost generate cu succes.');
    }

    /**
     * Duplică un template existent.
     */
    public function duplicate(EmailTemplate $emailTemplate)
    {
        $newTemplate = $emailTemplate->replicate();
        $newTemplate->name = $emailTemplate->name . ' (Copie)';
        $newTemplate->slug = null; // Va fi regenerat automat
        $newTemplate->is_default = false;
        $newTemplate->save();

        return redirect()
            ->route('admin.email-templates.edit', $newTemplate)
            ->with('success', 'Template-ul a fost duplicat. Poți edita copia.');
    }
}
