<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Libraries\GeminiService;
use App\Models\Content;
use App\Models\ContentBrief;

class AiController extends ResourceController
{
    protected GeminiService $geminiService;

    public function __construct()
    {
        $this->geminiService = new GeminiService();
    }

    /**
     * Helper to clean markdown JSON codeblock wrapper from AI response.
     */
    protected function cleanJsonText(string $text): string
    {
        $text = trim($text);
        if (preg_match('/```(?:json)?\s*(.*?)\s*```/s', $text, $matches)) {
            return trim($matches[1]);
        }
        return $text;
    }

    /**
     * Generate Content Ideas based on a topic prompt.
     * Expected POST JSON: { "prompt": "Topic or keyword" }
     */
    public function generateIdeas()
    {
        $json = $this->request->getJSON();
        $promptInput = $json->prompt ?? '';

        if (empty(trim($promptInput))) {
            return $this->fail('Prompt tidak boleh kosong', 400);
        }

        $prompt = "Kamu adalah seorang Social Media Manager profesional. Hasilkan 3 ide konten media sosial yang kreatif dan relevan berdasarkan topik/kata kunci berikut: \"" . htmlspecialchars($promptInput) . "\".\n\n"
            . "Wajib kembalikan response HANYA dalam format JSON array berisi objek dengan property \"title\" dan \"description\" (dalam bahasa Indonesia), tanpa teks pendahuluan atau penutup lainnya.\n\n"
            . "Contoh format JSON:\n"
            . "[\n"
            . "  {\"title\": \"Top 5 Tips Memaksimalkan Topik\", \"description\": \"Penjelasan singkat ide konten...\"},\n"
            . "  {\"title\": \"Mitos vs Fakta Topik\", \"description\": \"Penjelasan singkat ide konten...\"},\n"
            . "  {\"title\": \"Kenapa Harus Peduli Topik?\", \"description\": \"Penjelasan singkat ide konten...\"}\n"
            . "]";

        $aiRes = $this->geminiService->generateContent($prompt);

        if (!$aiRes['success']) {
            return $this->fail('Gagal menghasilkan ide konten via AI: ' . $aiRes['error'], 500);
        }

        $cleanedJson = $this->cleanJsonText($aiRes['text']);
        $ideas = json_decode($cleanedJson, true);

        // Fallback parsing jika AI tidak mengembalikan JSON array terstruktur
        if (!is_array($ideas) || empty($ideas)) {
            $rawLines = array_values(array_filter(explode("\n", $aiRes['text']), 'trim'));
            $ideas = [];
            foreach ($rawLines as $idx => $line) {
                if ($idx >= 3) break;
                $cleanLine = preg_replace('/^[\d\.\-\*\# ]+/', '', $line);
                if (!empty($cleanLine)) {
                    $ideas[] = [
                        'title' => 'Ide Konten #' . ($idx + 1) . ': ' . mb_substr($cleanLine, 0, 50),
                        'description' => $cleanLine
                    ];
                }
            }
        }

        if (empty($ideas)) {
            $ideas = [
                ['title' => "Rencana Konten: " . mb_substr($promptInput, 0, 40), 'description' => $aiRes['text']]
            ];
        }

        return $this->respond([
            'status' => 'success',
            'data'   => $ideas
        ]);
    }

    /**
     * Polish and professionalize raw text.
     * Expected POST JSON: { "text": "Raw text", "context": "objective|message|audience|general" }
     */
    public function polishText()
    {
        $json = $this->request->getJSON();
        $text = $json->text ?? '';
        $context = $json->context ?? 'general';

        if (empty(trim($text))) {
            return $this->fail('Teks tidak boleh kosong', 400);
        }

        $contextDescriptions = [
            'objective' => 'tujuan/objektif pemasaran (campaign objective)',
            'message'   => 'pesan utama (key message / value proposition)',
            'audience'  => 'deskripsi target audiens (target audience)',
            'general'   => 'konten umum media sosial'
        ];

        $desc = $contextDescriptions[$context] ?? $contextDescriptions['general'];

        $prompt = "Kamu adalah seorang Copywriter & Social Media Strategist profesional. Perbaiki, perhalus, dan buat teks berikut menjadi lebih profesional, rapi, persuasif, dan efektif untuk konteks {$desc}.\n\n"
            . "Teks asli: \"" . trim($text) . "\"\n\n"
            . "Ketentuan:\n"
            . "- Tuliskan HANYA teks yang sudah diperhalus dalam bahasa Indonesia.\n"
            . "- Jangan sertakan tanda kutip pembuka/penutup, judul, atau kalimat pengantar seperti \"Berikut hasilnya:\".";

        $aiRes = $this->geminiService->generateContent($prompt);

        if (!$aiRes['success']) {
            return $this->fail('Gagal memperhalus teks via AI: ' . $aiRes['error'], 500);
        }

        return $this->respond([
            'status'   => 'success',
            'original' => $text,
            'polished' => trim($aiRes['text'])
        ]);
    }

    /**
     * Generate Caption and Hashtags based on brief content.
     * Expected POST JSON: { "content_id": 123 }
     */
    public function generateCaption()
    {
        $json = $this->request->getJSON();
        $contentId = $json->content_id ?? null;

        if (!$contentId) {
            return $this->fail('Content ID is required', 400);
        }

        // Fetch content & brief context from database
        $contentModel = new Content();
        $briefModel   = new ContentBrief();

        $content = $contentModel->find($contentId);
        $brief   = $briefModel->where('content_id', $contentId)->first();

        $title     = $content['title'] ?? 'Konten Media Sosial';
        $objective = $brief['objective'] ?? 'Meningkatkan engagement & kesadaran merek';
        $audience  = $brief['target_audience'] ?? 'Masyarakat umum & pengguna media sosial';
        $keyMsg    = $brief['key_message'] ?? 'Solusi praktis dan bermanfaat';
        $cta       = $brief['call_to_action'] ?? 'Komentar dan bagikan postingan ini';
        $tone      = $brief['tone'] ?? 'Ramah, Profesional & Persuasif';

        $prompt = "Kamu adalah seorang Content Creator & Social Media Specialist profesional. Buatkan caption media sosial yang menarik (engaging) lengkap dengan emoji dan daftar hashtag yang relevan berdasarkan detail brief berikut:\n\n"
            . "- Judul Konten: {$title}\n"
            . "- Objective: {$objective}\n"
            . "- Target Audiens: {$audience}\n"
            . "- Key Message: {$keyMsg}\n"
            . "- Call to Action (CTA): {$cta}\n"
            . "- Tone of Voice: {$tone}\n\n"
            . "Wajib kembalikan response HANYA dalam format JSON dengan dua property: \"caption\" (string caption lengkap) dan \"hashtags\" (string hashtag yang dipisahkan spasi).\n\n"
            . "Contoh format JSON:\n"
            . "{\n"
            . "  \"caption\": \"Teks caption lengkap dengan emoji dan pemicu interaksi...\",\n"
            . "  \"hashtags\": \"#Hashtag1 #Hashtag2 #Hashtag3\"\n"
            . "}";

        $aiRes = $this->geminiService->generateContent($prompt);

        if (!$aiRes['success']) {
            return $this->fail('Gagal menghasilkan caption via AI: ' . $aiRes['error'], 500);
        }

        $cleanedJson = $this->cleanJsonText($aiRes['text']);
        $parsed = json_decode($cleanedJson, true);

        $caption  = $parsed['caption'] ?? $aiRes['text'];
        $hashtags = $parsed['hashtags'] ?? '#ABSYS #SMMS #ContentCreation';

        return $this->respond([
            'status'   => 'success',
            'caption'  => $caption,
            'hashtags' => $hashtags
        ]);
    }

    /**
     * Generate Campaign brief based on a simple prompt.
     * Expected POST JSON: { "prompt": "Campaign Idea" }
     */
    public function generateCampaign()
    {
        $json = $this->request->getJSON();
        $promptInput = $json->prompt ?? '';

        if (empty(trim($promptInput))) {
            return $this->fail('Topik campaign tidak boleh kosong', 400);
        }

        $prompt = "Kamu adalah seorang Marketing Strategist profesional. Buatkan rancangan brief kampanye pemasaran terintegrasi berdasarkan topik/ide berikut: \"" . htmlspecialchars($promptInput) . "\".\n\n"
            . "Wajib kembalikan response HANYA dalam format JSON objek dengan property:\n"
            . "- \"name\": Nama kampanye yang menarik dan profesional (string)\n"
            . "- \"description\": Deskripsi singkat strategi kampanye (string)\n"
            . "- \"objective\": Target/objektif spesifik kampanye (string)\n"
            . "- \"target_audience\": Demografi & profil audiens sasaran (string)\n"
            . "- \"budget\": Estimasi anggaran dalam angka numerik Rupiah tanpa titik/koma, misal 5000000 (integer/number)\n\n"
            . "Contoh format JSON:\n"
            . "{\n"
            . "  \"name\": \"Promo Tematik Kemerdekaan 2026\",\n"
            . "  \"description\": \"Kampanye pemasaran terintegrasi berfokus pada promo diskon...\",\n"
            . "  \"objective\": \"Meningkatkan konversi penjualan sebesar 20%...\",\n"
            . "  \"target_audience\": \"Pria & Wanita usia 18-35 tahun...\",\n"
            . "  \"budget\": 5000000\n"
            . "}";

        $aiRes = $this->geminiService->generateContent($prompt);

        if (!$aiRes['success']) {
            return $this->fail('Gagal menghasilkan rancangan kampanye via AI: ' . $aiRes['error'], 500);
        }

        $cleanedJson = $this->cleanJsonText($aiRes['text']);
        $campaignData = json_decode($cleanedJson, true);

        // Fallback parsing jika AI tidak mengembalikan JSON terstruktur
        if (!is_array($campaignData) || !isset($campaignData['name'])) {
            $campaignData = [
                'name'            => 'Kampanye: ' . mb_substr($promptInput, 0, 50),
                'description'     => mb_substr($aiRes['text'], 0, 300) . '...',
                'objective'       => 'Meningkatkan awareness dan engagement sesuai ide kampanye: ' . $promptInput,
                'target_audience' => 'Audiens pengguna media sosial umum (18-35 tahun)',
                'budget'          => 5000000
            ];
        }

        return $this->respond([
            'status' => 'success',
            'data'   => $campaignData
        ]);
    }
}
