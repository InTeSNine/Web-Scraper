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

    // مرحله اول: تولید JSON Schema
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
            'max_tokens' => 200,
        ]);

        // بررسی محتوای پاسخ- دیباگ شده و بدون مشکل
        // $schemaContent = $response['choices'][0]['message']['content'] ?? null;
        $schemaContent = 
        '```json
        {
            "type": "object",
            "properties": {
                "Title": {
                    "type": "string",
                    "description": "تایتل محصول"
                },
                "Price": {
                    "type": "string",
                    "description": "قیمت محصول"
                }
            },
            "required": ["Title", "Price"]
        }
        ```';
        // dd($schemaContent);
        if (!$schemaContent) {
            dd('Empty response from OpenAI');
            return null;
        }

        // تمیز کردن JSON
        $cleanedSchema = trim(str_replace(['```json', '```'], '', $schemaContent));
        // dd($cleanedSchema); // بررسی محتوای تمیز شده

        // تبدیل JSON به آرایه
        $schemaArray = json_decode($cleanedSchema, true); // true برای آرایه
        if (json_last_error() !== JSON_ERROR_NONE) {
            dd('JSON Error: ' . json_last_error_msg()); // نمایش خطا در صورت وجود
        }

        return $schemaArray; // بازگشت آرایه معتبر
    }


    // مرحله دوم: استخراج اطلاعات با استفاده از JSON Schema و Markdown
    public function extractInformation(string $markdownContent, array $schema): ?array
    {
    //     $response = OpenAI::client()->chat()->create([
    //         'model' => 'gpt-4o-mini',
    //         'messages' => [
    //             [
    //                 'role' => 'system',
    //                 'content' => 'You are an assistant that extracts structured data from Markdown content based on a given JSON schema.'
    //             ],
    //             [
    //                 'role' => 'user',
    //                 'content' => "Here is the website content in Markdown format:\n\n{$markdownContent}\n\nUse the following JSON schema to extract data(just write the output and nothing else):\n" . json_encode($schema, JSON_PRETTY_PRINT )
    //             ],
    //         ],
    //         'max_tokens' => 900,
    //     ]);

        // دریافت محتوا و تبدیل آن به آرایه
        // $extractedContent = $response['choices'][0]['message']['content'] ?? null;
        $extractedContent = '[

    {

        "Title": "Apple MacBook Pro 16Inch M4 Pro 24GB 512GB",

        "Price": "۱۹۰٫۹۹۹٫۰۰۰ تومان"

    },

    {

        "Title": "لپ تاپ مک بوک ایر | 8GB RAM | 256GB SSD | M2",

        "Price": "۶۰٫۹۷۹٫۰۰۰ تومان"

    },

    {

        "Title": "مک بوک ایر 8GB RAM | 256GB SSD | M1 |",

        "Price": "۴۸٫۰۰۰٫۰۰۰ تومان"

    },

    {

        "Title": "لپ تاپ اپل 13.6 اینچی مدل MacBook Air MRX",

        "Price": "۷۰٫۳۶۹٫۰۰۰ تومان"

    }

]';
        // dd($extractedContent);
        // تمیز کردن JSON
        $cleanedJson = trim(str_replace(['```json', '```'], '', $extractedContent));
                
        if ($cleanedJson) {
            $dataArray = json_decode($extractedContent, true); // تبدیل رشته به آرایه
            if (json_last_error() === JSON_ERROR_NONE) {
                // dd($extractedContent,$dataArray);
                return $dataArray; // بازگشت آرایه معتبر
            }
        }

        return null; // بازگشت null در صورت عدم موفقیت
    }
}
