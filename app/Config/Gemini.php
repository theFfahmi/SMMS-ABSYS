<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Gemini extends BaseConfig
{
    public string $apiKey = '';
    public string $model = 'gemini-2.5-flash';
    public string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/';

    public function __construct()
    {
        parent::__construct();
        $this->apiKey = env('GEMINI_API_KEY', $this->apiKey);
    }
}
