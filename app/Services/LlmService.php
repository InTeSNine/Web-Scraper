<?php
namespace App\Services;

use OpenAI;
use OpenAI\Client;

class LlmService
{
 

    public function __construct()
    {
    }

    public function getExtractionStructure(array $htmlStructure, string $userRequest)
    {
        $response = OpenAI::client()->chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are an assistant that helps extract structured data from HTML based on user requests.'
                ],
                [
                    'role' => 'user',
                    'content' => json_encode([
                        'html_structure' => $htmlStructure,
                        'user_request' => $userRequest
                    ])
                ]
            ]
        ]);

        return json_decode($response['choices'][0]['message']['content'], true);
    }
}
