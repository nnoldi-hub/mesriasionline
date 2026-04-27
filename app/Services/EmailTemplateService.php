<?php

namespace App\Services;

use App\Models\EmailTemplate;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;

class EmailTemplateService
{
    /**
     * Construiește un MailMessage folosind un template personalizat
     */
    public function buildMailMessage(
        string $notificationType,
        array $data = [],
        ?string $defaultSubject = null,
        ?callable $defaultBuilder = null
    ): MailMessage {
        $template = EmailTemplate::getDefaultForType($notificationType);

        if (!$template) {
            // Folosește builder-ul default dacă nu există template
            if ($defaultBuilder) {
                return $defaultBuilder(new MailMessage);
            }

            return (new MailMessage)
                ->subject($defaultSubject ?? 'Notificare')
                ->line('Ai primit o notificare nouă.');
        }

        return $this->buildFromTemplate($template, $data);
    }

    /**
     * Construiește MailMessage din template
     */
    public function buildFromTemplate(EmailTemplate $template, array $data = []): MailMessage
    {
        $mail = new MailMessage;

        // Setează subiectul
        $mail->subject($template->parseSubject($data));

        // Parsează body-ul
        $body = $template->parseBody($data);

        // Procesează body-ul - suportă Markdown simplu
        $lines = explode("\n", $body);
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            if (empty($line)) {
                continue;
            }

            // Detectează butonul de acțiune: [Text Buton](url)
            if (preg_match('/^\[(.+?)\]\((.+?)\)$/', $line, $matches)) {
                $mail->action($matches[1], $matches[2]);
                continue;
            }

            // Greeting: # sau ##
            if (preg_match('/^#{1,2}\s+(.+)$/', $line, $matches)) {
                $mail->greeting($matches[1]);
                continue;
            }

            // Salutare finală: -- urmat de text
            if (preg_match('/^--\s*(.+)$/', $line, $matches)) {
                $mail->salutation($matches[1]);
                continue;
            }

            // Linie normală
            $mail->line($this->parseInlineMarkdown($line));
        }

        return $mail;
    }

    /**
     * Parsează Markdown inline (bold, italic)
     */
    protected function parseInlineMarkdown(string $text): string
    {
        // Bold: **text** sau __text__
        $text = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $text);
        $text = preg_replace('/__(.+?)__/', '<strong>$1</strong>', $text);
        
        // Italic: *text* sau _text_
        $text = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $text);
        $text = preg_replace('/_(.+?)_/', '<em>$1</em>', $text);
        
        return $text;
    }

    /**
     * Previzualizează un template cu date de test
     */
    public function preview(EmailTemplate $template): array
    {
        $testData = $this->getTestDataForType($template->notification_type);

        return [
            'subject' => $template->parseSubject($testData),
            'body' => $template->parseBody($testData),
            'mail' => $this->buildFromTemplate($template, $testData),
        ];
    }

    /**
     * Generează date de test pentru previzualizare
     */
    protected function getTestDataForType(?string $notificationType): array
    {
        $commonData = [
            'user_name' => 'Ion Popescu',
            'action_url' => url('/'),
        ];

        $specificData = match ($notificationType) {
            'new_appointment' => [
                'appointment_date' => '15.01.2026 la 10:00',
                'service_name' => 'Instalații electrice',
                'notes' => 'Rog confirmare cu o zi înainte.',
            ],
            'new_review' => [
                'rating' => '5',
                'rating_stars' => '⭐⭐⭐⭐⭐',
                'comment' => 'Foarte mulțumit de servicii! Recomand cu încredere.',
            ],
            'new_message' => [
                'sender_name' => 'Maria Ionescu',
                'message_preview' => 'Bună ziua, aș dori să vă întreb despre...',
            ],
            'new_quote_request' => [
                'request_title' => 'Renovare baie completă',
                'request_description' => 'Doresc renovare completă baie, inclusiv înlocuire instalații.',
                'urgency' => 'Normal',
                'budget' => '5000 - 8000 RON',
            ],
            'quote_received' => [
                'craftsman_name' => 'Mihai Popa - Instalații',
                'price' => '6500 RON',
                'description' => 'Preț include manoperă și materiale de bază.',
            ],
            'quote_accepted' => [
                'client_name' => 'Ana Georgescu',
                'request_title' => 'Montaj aer condiționat',
                'price' => '850 RON',
            ],
            'welcome' => [
                'email' => 'ion.popescu@example.com',
                'role' => 'client',
            ],
            default => [],
        };

        return array_merge($commonData, $specificData);
    }

    /**
     * Creează template-uri default pentru toate tipurile de notificări
     */
    public function seedDefaultTemplates(): void
    {
        $templates = [
            [
                'name' => 'Programare Nouă',
                'slug' => 'new-appointment',
                'notification_type' => 'new_appointment',
                'category' => 'appointments',
                'subject' => 'Programare nouă: {{appointment_date}}',
                'body' => "# Bună, {{user_name}}!\n\nAi primit o nouă programare.\n\n**Data și ora:** {{appointment_date}}\n**Serviciu:** {{service_name}}\n**Note:** {{notes}}\n\n[Vezi programările]({{action_url}})\n\nNu uita să confirmi programarea!\n\n-- Cu stimă, Echipa Meseriași",
            ],
            [
                'name' => 'Recenzie Nouă',
                'slug' => 'new-review',
                'notification_type' => 'new_review',
                'category' => 'reviews',
                'subject' => 'Ai primit o recenzie nouă {{rating_stars}}',
                'body' => "# Bună, {{user_name}}!\n\nUn client ți-a lăsat o recenzie nouă.\n\n**Rating:** {{rating_stars}} ({{rating}}/5)\n**Comentariu:** \"{{comment}}\"\n\n[Vezi recenzia]({{action_url}})\n\nPoți răspunde la recenzie pentru a-ți arăta aprecierea sau pentru a clarifica orice neînțelegere.\n\n-- Cu stimă, Echipa Meseriași",
            ],
            [
                'name' => 'Mesaj Nou',
                'slug' => 'new-message',
                'notification_type' => 'new_message',
                'category' => 'messages',
                'subject' => 'Mesaj nou de la {{sender_name}}',
                'body' => "# Bună, {{user_name}}!\n\nAi primit un mesaj nou de la **{{sender_name}}**:\n\n\"{{message_preview}}\"\n\n[Vezi conversația]({{action_url}})\n\nRăspunde cât mai curând pentru a menține comunicarea activă.\n\n-- Cu stimă, Echipa Meseriași",
            ],
            [
                'name' => 'Cerere Ofertă Nouă',
                'slug' => 'new-quote-request',
                'notification_type' => 'new_quote_request',
                'category' => 'quotes',
                'subject' => 'Cerere ofertă nouă: {{request_title}}',
                'body' => "# Bună, {{user_name}}!\n\nAi primit o cerere de ofertă nouă.\n\n**Titlu:** {{request_title}}\n**Descriere:** {{request_description}}\n**Urgență:** {{urgency}}\n**Buget:** {{budget}}\n\n[Vezi și răspunde la cerere]({{action_url}})\n\nRăspunde rapid pentru a crește șansele de a obține proiectul!\n\n-- Cu stimă, Echipa Meseriași",
            ],
            [
                'name' => 'Ofertă Primită',
                'slug' => 'quote-received',
                'notification_type' => 'quote_received',
                'category' => 'quotes',
                'subject' => 'Ofertă nouă de la {{craftsman_name}}',
                'body' => "# Bună, {{user_name}}!\n\nAi primit o ofertă nouă pentru cererea ta.\n\n**De la:** {{craftsman_name}}\n**Preț:** {{price}}\n**Descriere:** {{description}}\n\n[Vezi și compară ofertele]({{action_url}})\n\nCompară mai multe oferte pentru a alege cea mai potrivită!\n\n-- Cu stimă, Echipa Meseriași",
            ],
            [
                'name' => 'Ofertă Acceptată',
                'slug' => 'quote-accepted',
                'notification_type' => 'quote_accepted',
                'category' => 'quotes',
                'subject' => '✅ Oferta ta a fost acceptată!',
                'body' => "# Felicitări, {{user_name}}!\n\n**{{client_name}}** ți-a acceptat oferta pentru **{{request_title}}**!\n\n**Preț agreat:** {{price}}\n\n[Vezi detaliile]({{action_url}})\n\nContactează clientul pentru a stabili detaliile finale.\n\n-- Cu stimă, Echipa Meseriași",
            ],
        ];

        foreach ($templates as $templateData) {
            EmailTemplate::firstOrCreate(
                ['slug' => $templateData['slug']],
                array_merge($templateData, ['is_active' => true, 'is_default' => true])
            );
        }
    }
}
