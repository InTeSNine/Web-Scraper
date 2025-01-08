<?php

namespace App\Services;

use OpenAI\Client;
use OpenAI;

class OpenAIService
{
    protected $client;

    public function __construct()
    {

    }

    public function generateSchema(string $userPrompt): ?array
    {
        $response = OpenAI::client()->chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are an assistant that generates JSON schemas based on user requests for extracting structured data from websites.'
                ],
                [
                    'role' => 'user',
                    'content' => "Generate a JSON schema to extract the following information: {$userPrompt}. The schema should include detailed descriptions of each property like this:
                                    {
                type: object,
                properties: {
                   Name: {
                        type: string,
                        description: Title of the product
                    },
                    Price: {
                        type: string,
                        description: Price of the product
                    }
                },
                required: [Name, Price]
            }.
            just write the schema and nothing else"
                ],
            ],
            // 'max_tokens' => 300,
        ]);

        $schemaContent = $response['choices'][0]['message']['content'] ?? null;

        if (!$schemaContent) {
            dd('Empty response from OpenAI');
            return null;
        }

        $cleanedSchema = trim(str_replace(['```json', '```'], '', $schemaContent));

        $schemaArray = json_decode($cleanedSchema, true); 
        if (json_last_error() !== JSON_ERROR_NONE) {
            dd('JSON Error: ' . json_last_error_msg()); 
        }

        return $schemaArray;
    }


    public function extractInformation(string $markdownContent, array $schema): ?array
    {
        $response = OpenAI::client()->chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are an assistant that extracts structured data from Markdown content based on a given JSON schema.'
                ],
                [
                    'role' => 'user',
                    'content' => "Here is the website content in Markdown format:\n\n{$markdownContent}\n\nUse the following JSON schema to extract data(just write the output and nothing else):\n" . json_encode($schema, JSON_PRETTY_PRINT )
                ],
            ],
            // 'max_tokens' => 900,
        ]);

        $extractedContent = $response['choices'][0]['message']['content'] ?? null;

        $cleanedJson = trim(str_replace(['```json', '```'], '', $extractedContent));
                
        if ($cleanedJson) {
            $dataArray = json_decode($extractedContent, true); 
                        if (json_last_error() === JSON_ERROR_NONE) {
                return $dataArray;
            }
        }

        return null; 
    }
}
