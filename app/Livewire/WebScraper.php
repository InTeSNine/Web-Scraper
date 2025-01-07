<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\MarkdownConverterService;
use App\Services\OpenAIService;

class WebScraper extends Component
{
    public $url = "https://torob.com/browse/99/%D9%84%D9%BE-%D8%AA%D8%A7%D9%BE-%D9%88-%D9%86%D9%88%D8%AA-%D8%A8%D9%88%DA%A9-laptop/b/14/apple-%D9%8A%D8%B7%D9%84%D8%A7%D8%A8";
    public $UserPrompt = "  تایتل و قیمت ها و تعداد فروشگاه ها";
    public $markdownContent = ""; // محتوای مارک‌داون
    public $LLMrespond = '';    // پاسخ از LLM
    public $results = [];       // نتایج استخراج شده
    public $errorMessage = '';
    public $base_url = "https://r.jina.ai/";

    protected $rules = [
        'url' => 'required|url',
        'UserPrompt' => 'required|string',
    ];

    protected $messages = [
        'url.required' => 'URL is required',
        'url.url' => 'Please provide a valid URL',
        'UserPrompt.required' => 'Request is required',
    ];

    public function render()
    {
        return view('livewire.web-scraper');
    }

    public function handle()
    {
        $this->validate();

        try {
            $markdownService = new MarkdownConverterService();
            $openAIService = new OpenAIService();

            // مرحله اول: تبدیل وب‌سایت به محتوای مارک‌داون
            // $markdownContent = $markdownService->markdown(base_url: $this->base_url, $this->url);
            $markdownContent = "Apple MacBook Pro 16Inch M4 Pro 24GB 512GB

از ۱۹۰٫۹۹۹٫۰۰۰ تومان
در ۱۵ فروشگاه
لپ تاپ مک بوک ایر | 8GB RAM | 256GB SSD | M2
از ۶۰٫۹۷۹٫۰۰۰ تومان
در ۴۴ فروشگاه
مک بوک ایر 8GB RAM | 256GB SSD | M1 |
از ۴۸٫۰۰۰٫۰۰۰ تومان
در ۶۳ فروشگاه
لپ تاپ اپل 13.6 اینچی مدل MacBook Air MRX
از ۷۰٫۳۶۹٫۰۰۰ تومان
در ۵۵ فروشگاه";

// ۶

// لپ تاپ اپل 14 اینچی مدل MacBook Pro M

// از ۹۷٫۰۰۰٫۰۰۰ تومان

// در ۲۷ فروشگاه

// ۴

// لپ تاپ 13 اینچی اپل مدل MacBook Air CTO 2022 M2 16GB 256GB

// از ۶۸٫۷۹۰٫۰۰۰ تومان

// در ۱۹ فروشگاه

// ۶

// لپ تاپ اپل 14 اینچی مدل MacBook Pro MRX

// از ۱۳۳٫۲۵۰٫۰۰۰ تومان

// در ۴۲ فروشگاه

// ۷

// لپ تاپ اپل 15.3 اینچی مدل MacBook Air M 16GB RAM 512GB SSD

// از ۱۰۸٫۷۴۰٫۰۰۰ تومان

// در ۴۶ فروشگاه

// ۷

// لپ تاپ اپل 15.3 اینچی مدل MacBook Air MRY

// از ۸۶٫۹۰۰٫۰۰۰ تومان

// در ۴۱ فروشگاه

// ۷

// لپ تاپ اپل 16 اینچی مدل MacBook Pro MUW

// از ۲۴۴٫۸۰۰٫۰۰۰ تومان

// در ۲۵ فروشگاه

// ۷

// Apple MacBook Pro 14 MW2

// از ۱۳۴٫۹۰۰٫۰۰۰ تومان

// در ۱۰ فروشگاه

// ۶

// لپ تاپ اپل 14 اینچی مدل MacBook Pro MRX43 2023 M3 Pro 18GB 1TB

// از ۱۴۷٫۴۰۰٫۰۰۰ تومان

// در ۲۱ فروشگاه

// ۷

// لپ تاپ اپل 13.6 اینچی مدل MacBook Air MXC

// از ۹۷٫۹۹۹٫۰۰۰ تومان

// در ۲۵ فروشگاه

// ۷

// لپ تاپ اپل 15.3 اینچی مدل Apple MacBook Air 2023

// از ۸۶٫۷۰۰٫۰۰۰ تومان

// در ۴ فروشگاه

// ۶

// لپ تاپ اپل 16 اینچی مدل MacBook Pro MRW

// از ۱۶۵٫۹۸۹٫۰۰۰ تومان

// در ۲۶ فروشگاه

// ۷

// لپ تاپ MacBook air 2020

// کارکرده

// ۳۰٫۶۷۳٫۰۰۰ تومان

// در پاساریا

// مک بوک پرو

// ۱۴۱٫۰۰۰٫۰۰۰ تومان

// در آل ماکو

// ۷

// Laptop Macbook Air تاپ مک بوک ایر | 8GB RAM | 512GB SSD | M2

// از ۶۰٫۵۰۰٫۰۰۰ تومان

// در ۳۴ فروشگاه

// ۷

// لپ تاپ اپل 14 اینچی مدل MacBook Pro M

// از ۱۰۸٫۴۰۰٫۰۰۰ تومان

// در ۲۵ فروشگاه

// ۶

// لپ تاپ استوک APPLE Macbook Pro 2019

// کارکرده

// از ۴۶٫۹۰۰٫۰۰۰ تومان

// در ۳ فروشگاه

// ۶

// MacBook Pro اپل 13 اینچ مدل 5YD82 پردازنده M1 رم 8GB حافظه 256GB SSD

// کارکرده

// از ۴۹٫۴۰۰٫۰۰۰ تومان

// در ۴ فروشگاه

// ۷

// مک بوک پرو 14.2 اینچMacBook Pro MCX

// از ۱۷۵٫۰۰۰٫۰۰۰ تومان

// در ۴ فروشگاه

// لپ تاپ استوک مک بوک پرو Apple MacBook Pro A1990 2018 - i7

// کارکرده

// از ۴۴٫۰۰۰٫۰۰۰ تومان

// در ۲ فروشگاه

// ۷

// مک بوک پرو 16اینچ MX2V3 پردازنده M4 مدل 2024 حافظه 36گیگ/یک ترابایت سیلور

// از ۲۸۶٫۰۰۰٫۰۰۰ تومان

// در ۱۰ فروشگاه
// ";
            $this->markdownContent = $markdownContent;
            // dd($markdownContent);

            // مرحله دوم: تولید JSON Schema بر اساس درخواست کاربر
            $schema = $openAIService->generateSchema($this->UserPrompt);
            if (!$schema) {
                throw new \Exception('Failed to generate JSON schema.');
            }
            // dd( $schema,$markdownContent);

            // مرحله سوم: استخراج اطلاعات با استفاده از Markdown و JSON Schema
            $structuredData = $openAIService->extractInformation($markdownContent, $schema); // $schema یک آرایه است
            if (!$structuredData) {
                throw new \Exception('Failed to extract information.');
            }
            // dd($structuredData);
            $this->results = $structuredData; // ذخیره نتایج در متغیر
            // dd($markdownContent, $schema, $structuredData);
        } catch (\Exception $e) {
            $this->errorMessage = 'Error: ' . $e->getMessage();
        }
    }
}
