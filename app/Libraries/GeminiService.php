<?php

namespace App\Libraries;

use Config\Services;
use Config\Gemini as GeminiConfig;

class GeminiService
{
    protected GeminiConfig $config;

    public function __construct(?GeminiConfig $config = null)
    {
        $this->config = $config ?? config('Gemini');
    }

    /**
     * Generate content from prompt using Gemini REST API.
     *
     * @param string $prompt
     * @param array $options
     * @return array ['success' => bool, 'text' => string|null, 'error' => string|null]
     */
    public function generateContent(string $prompt, array $options = []): array
    {
        if (empty(trim($this->config->apiKey))) {
            return [
                'success' => false,
                'text'    => null,
                'error'   => 'GEMINI_API_KEY belum dikonfigurasi di file .env'
            ];
        }

        $model = $options['model'] ?? $this->config->model;
        $url = rtrim($this->config->baseUrl, '/') . '/' . $model . ':generateContent?key=' . $this->config->apiKey;

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ]
        ];

        if (isset($options['temperature'])) {
            $payload['generationConfig']['temperature'] = (float)$options['temperature'];
        }

        $client = Services::curlrequest([
            'timeout'     => 30,
            'http_errors' => false,
            'verify'      => false,
        ]);

        try {
            $response = $client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body' => json_encode($payload),
            ]);

            $statusCode = $response->getStatusCode();
            $bodyRaw    = $response->getBody();
            $data       = json_decode($bodyRaw, true);

            if ($statusCode === 429) {
                return [
                    'success' => false,
                    'text'    => null,
                    'error'   => 'Kuota pemanggilan Gemini AI sedang penuh / telah mencapai batas (Rate Limit 429). Silakan tunggu beberapa saat lalu coba lagi.'
                ];
            }

            if ($statusCode !== 200) {
                $errorMsg = $data['error']['message'] ?? "HTTP Error {$statusCode}: " . ($data['error']['status'] ?? 'Unknown error');
                return [
                    'success' => false,
                    'text'    => null,
                    'error'   => $errorMsg
                ];
            }

            $generatedText = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if ($generatedText === null) {
                return [
                    'success' => false,
                    'text'    => null,
                    'error'   => 'Response Gemini API tidak berisi teks kandidat yang valid.'
                ];
            }

            return [
                'success' => true,
                'text'    => trim($generatedText),
                'error'   => null
            ];

        } catch (\Throwable $e) {
            return [
                'success' => false,
                'text'    => null,
                'error'   => 'Koneksi ke Gemini API gagal: ' . $e->getMessage()
            ];
        }
    }
}
