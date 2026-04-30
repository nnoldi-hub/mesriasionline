<?php

namespace App\Services;

use App\Models\Category;
use App\Models\ChatbotKnowledge;
use Illuminate\Support\Facades\Log;
use OpenAI;

class ChatbotService
{
    private string $apiKey;
    private string $model;
    private int $maxTokens;
    private int $maxHistoryMessages = 10;

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key', '');
        $this->model = config('services.openai.model', 'gpt-4o-mini');
        $this->maxTokens = (int) config('services.openai.max_tokens', 500);
    }

    /**
     * Generează un răspuns AI pe baza mesajului și istoricului.
     */
    public function getResponse(string $userMessage, array $history): array
    {
        // 1. Verifică mai întâi baza de cunoștințe (răspuns direct, fără OpenAI)
        $knowledgeResult = $this->checkKnowledgeBase($userMessage);
        if ($knowledgeResult !== null) {
            return $knowledgeResult;
        }

        if (empty($this->apiKey)) {
            return [
                'message' => 'Chatbotul nu este configurat momentan. Vă rugăm să ne contactați direct.',
                'actions' => [],
            ];
        }

        try {
            $client = OpenAI::client($this->apiKey);

            $messages = $this->buildMessages($userMessage, $history);

            $response = $client->chat()->create([
                'model'       => $this->model,
                'messages'    => $messages,
                'max_tokens'  => $this->maxTokens,
                'temperature' => 0.7,
            ]);

            $assistantMessage = $response->choices[0]->message->content ?? '';

            return [
                'message' => $assistantMessage,
                'actions' => $this->detectActions($userMessage, $assistantMessage),
            ];
        } catch (\Exception $e) {
            Log::warning('ChatbotService error: ' . $e->getMessage());

            return [
                'message' => 'Ne pare rău, am întâmpinat o problemă temporară. Vă rugăm să încercați din nou sau să ne contactați la contact@meseriasionline.ro.',
                'actions' => [],
            ];
        }
    }

    /**
     * Verifică baza de cunoștințe pentru un răspuns direct.
     * Returnează array cu message+actions sau null dacă nu există match.
     */
    private function checkKnowledgeBase(string $userMessage): ?array
    {
        try {
            $entries = ChatbotKnowledge::active()->get();

            foreach ($entries as $entry) {
                if ($entry->matchesMessage($userMessage)) {
                    $actions = [];
                    if ($entry->cta_label && $entry->cta_url) {
                        $actions[] = [
                            'label' => $entry->cta_label,
                            'url'   => $entry->cta_url,
                            'type'  => 'primary',
                        ];
                    }

                    return [
                        'message' => $entry->answer,
                        'actions' => $actions,
                    ];
                }
            }
        } catch (\Exception $e) {
            // Nu blocăm dacă tabela nu există încă
        }

        return null;
    }

    /**
     * Construiește array-ul de mesaje pentru OpenAI (system + history + user).
     */
    private function buildMessages(string $userMessage, array $history): array
    {
        $messages = [
            [
                'role'    => 'system',
                'content' => $this->buildSystemPrompt(),
            ],
        ];

        // Adaugă ultimele N mesaje din istoric (protecție context overflow)
        $recentHistory = array_slice($history, -$this->maxHistoryMessages);
        foreach ($recentHistory as $entry) {
            if (isset($entry['role'], $entry['content'])) {
                $messages[] = [
                    'role'    => $entry['role'],
                    'content' => $entry['content'],
                ];
            }
        }

        $messages[] = [
            'role'    => 'user',
            'content' => $userMessage,
        ];

        return $messages;
    }

    /**
     * Construiește system prompt-ul complet cu contextul platformei.
     */
    private function buildSystemPrompt(): string
    {
        $categories = $this->getTopCategories();

        return <<<PROMPT
Ești asistentul oficial al platformei MeseriasiOnline.ro (cunoscut și ca "Omul Potrivit").
Răspunzi EXCLUSIV în limba română, ești prietenos, profesional și concis.

## CE EȘTI
Un asistent AI integrat în platforma MeseriasiOnline.ro, un marketplace românesc care conectează meseriași profesioniști cu clienți care au nevoie de servicii.

## DESPRE PLATFORMĂ
- **Tip**: Marketplace de servicii (meseriași ↔ clienți)
- **Cum funcționează**: Clienții postează cereri → Meseriașii trimit oferte → Clientul alege
- **Cont meseriaș**: Gratuit, se poate înscrie la /register
- **Cont client**: Gratuit, se poate înscrie la /register  
- **Comisioane**: Platforma are planuri (Gratuit, Standard, Premium) — detalii la /plans
- **Verificare meseriași**: Profiluri verificate cu recenzii reale
- **Acoperire**: Toată România

## CATEGORII PRINCIPALE DE SERVICII
{$categories}

## ROLUL TĂU
1. **Recrutare meseriași**: Explică beneficiile, ghidează spre înregistrare
2. **Asistență clienți**: Ajută să creeze cereri, recomandă categorii
3. **Suport general**: Răspunde la întrebări despre platformă

## REGULI STRICTE
- Răspunzi DOAR în română
- Nu inventezi funcționalități care nu există
- Nu promiți prețuri fixe sau termene garantate
- Nu dai sfaturi juridice sau medicale
- Nu oferi informații personale despre utilizatori
- Dacă nu știi ceva, spune că nu ești sigur și sugerezi contactul direct
- Răspunsuri scurte și clare (maxim 3-4 propoziții per răspuns)
- Nu folosi markdown excesiv în răspunsuri (fără **bold** sau # headings)

## LINK-URI UTILE (menționează-le când e relevant)
- Înregistrare meseriaș: /register
- Înregistrare client: /register/client
- Postare cerere: /cere-oferte  
- Categorii: /#categories
- Planuri și prețuri: /planuri
- Contact: /contact

## INFORMAȚII VERIFICATE DIN BAZA DE CUNOȘTINȚE
{$this->getKnowledgeContext()}

## TON
Prietenos, ca un coleg de ajutor. Folosește "tu" (informal dar respectuos).
Exemplu bun: "Bună! Poți crea un cont gratuit accesând /register — durează 2 minute."
Exemplu rău: "Bine ați venit la MeseriasiOnline! Suntem o platformă inovatoare care..."
PROMPT;
    }

    /**
     * Extrage cunoștințele active pentru injectare în prompt OpenAI.
     */
    private function getKnowledgeContext(): string
    {
        try {
            $entries = ChatbotKnowledge::active()->limit(20)->get();
            if ($entries->isEmpty()) {
                return '(nicio intrare adăugată încă)';
            }

            return $entries->map(function ($e) {
                $line = "- {$e->question_example}: {$e->answer}";
                if ($e->cta_url) {
                    $line .= " Link: {$e->cta_url}";
                }
                return $line;
            })->implode("\n");
        } catch (\Exception $e) {
            return '(nicio intrare adăugată încă)';
        }
    }

    /**
     * Extrage categoriile principale din baza de date pentru context.
     */
    private function getTopCategories(): string
    {
        try {
            $categories = Category::where('is_active', true)
                ->whereNull('parent_id')
                ->orderBy('name')
                ->limit(20)
                ->pluck('name')
                ->toArray();

            if (empty($categories)) {
                return 'Construcții, Instalații, Electricitate, Curățenie, Grădinărit, IT, Transport, Reparații';
            }

            return implode(', ', $categories);
        } catch (\Exception $e) {
            return 'Construcții, Instalații, Electricitate, Curățenie, Grădinărit, IT, Transport, Reparații';
        }
    }

    /**
     * Detectează acțiuni sugerate pe baza mesajului (butoane CTA).
     */
    private function detectActions(string $userMessage, string $assistantMessage): array
    {
        $combined = strtolower($userMessage . ' ' . $assistantMessage);
        $actions = [];

        // Intenție: înregistrare meseriaș
        if (preg_match('/meserias|înregistr|cont nou|vreau cont|fac cont|inscri|lucrez ca|ofer servicii|devino meserias|deveni meserias/u', $combined)) {
            $actions[] = [
                'label' => '👷 Înscrie-te ca meseriaș',
                'url'   => '/register',
                'type'  => 'primary',
            ];
        }

        // Intenție: înregistrare client
        if (preg_match('/vreau cont client|inscriu client|creez cont|inregistrez client|cont client|ca client/u', $combined)) {
            $actions[] = [
                'label' => '👤 Creează cont client',
                'url'   => '/register/client',
                'type'  => 'primary',
            ];
        }

        // Intenție: postare cerere / caută meseriaș
        if (preg_match('/cerere|am nevoie|caut meserias|angajez|ajutor cu|reparat|instalat|constru|renovare|zugrav|cere oferta|oferta|montaj/u', $combined)) {
            $actions[] = [
                'label' => '📋 Postează cerere gratuită',
                'url'   => '/cere-oferte',
                'type'  => 'primary',
            ];
        }

        // Intenție: browse meseriași / categorii
        if (preg_match('/cauta meserias|gasesc|lista meseriasi|categorii|servicii disponibile|ce meseriasi/u', $combined)) {
            $actions[] = [
                'label' => '🔍 Caută meseriași',
                'url'   => '/#categories',
                'type'  => 'secondary',
            ];
        }

        // Intenție: prețuri / planuri
        if (preg_match('/pret|cost|comision|plan|abonament|gratuit|platesc|cat costa|tarif|premium|standard/u', $combined)) {
            $actions[] = [
                'label' => '💰 Vezi planuri și prețuri',
                'url'   => '/planuri',
                'type'  => 'secondary',
            ];
        }

        // Intenție: cum funcționează
        if (preg_match('/cum functioneaza|cum merge|cum se foloseste|cum lucreaza|despre platforma|explica/u', $combined)) {
            $actions[] = [
                'label' => 'ℹ️ Despre platformă',
                'url'   => '/despre',
                'type'  => 'secondary',
            ];
        }

        // Intenție: login
        if (preg_match('/autentific|login|intru in cont|am cont deja|parola/u', $combined)) {
            $actions[] = [
                'label' => '🔑 Intră în cont',
                'url'   => '/login',
                'type'  => 'secondary',
            ];
        }

        // Intenție: contact / suport
        if (preg_match('/contact|email|telefon|suport|problema|reclamatie|ajutor urgent/u', $combined)) {
            $actions[] = [
                'label' => '📞 Contactează-ne',
                'url'   => '/contact',
                'type'  => 'secondary',
            ];
        }

        return array_slice($actions, 0, 2); // Max 2 butoane
    }

    /**
     * Detectează intenția principală a mesajului utilizatorului.
     */
    public function detectIntent(string $message): string
    {
        $msg = strtolower($message);

        if (preg_match('/meserias|înregistr|cont nou|vreau cont|fac cont|inscri|lucrez ca|ofer servicii|devino|deveni meserias/u', $msg)) {
            return 'craftsman_register';
        }

        if (preg_match('/cerere|am nevoie|caut meserias|angajez|ajutor cu|reparat|instalat|constru|renovare|zugrav|cere oferta|montaj/u', $msg)) {
            return 'client_request';
        }

        if (preg_match('/pret|cost|comision|plan|abonament|cat costa|platesc|gratuit|tarif|premium/u', $msg)) {
            return 'pricing';
        }

        if (preg_match('/problem|reclamatie|nu functioneaza|nu merge|eroare|bug|nemultumit|suport/u', $msg)) {
            return 'support';
        }

        if (preg_match('/cum|ce este|ce face|despre|functionalit|explicati|ajuta/u', $msg)) {
            return 'info';
        }

        return 'unknown';
    }

    /**
     * Verifică dacă mesajul conține tentative de prompt injection / jailbreak.
     */
    public function isSecurityRisk(string $message): bool
    {
        $dangerousPatterns = [
            '/ignore (previous|all|your) (instructions|prompt|rules)/i',
            '/you are now/i',
            '/pretend (you are|to be)/i',
            '/act as (a|an)/i',
            '/forget (your|all)/i',
            '/new instructions:/i',
            '/\[system\]/i',
            '/###\s*system/i',
        ];

        foreach ($dangerousPatterns as $pattern) {
            if (preg_match($pattern, $message)) {
                return true;
            }
        }

        return false;
    }
}
