<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;
use Michelf\Markdown; // استفاده از کتابخانه Markdown

class WebScraper extends Component
{
    public $url="https://rubikamp.org/"; // ورودی URL
    public $elements = []; // المنت‌های استخراج‌شده
    public $markdown = ""; // داده‌های مارک‌داون

    protected $rules = [
        'url' => 'required|url', // اعتبارسنجی URL
    ];

    public function extractElements()
    {
        $this->elements = [];
        $this->validate(); // بررسی صحت ورودی‌ها

        try {
            // دریافت HTML صفحه از URL
            $response = Http::get($this->url);
            $html = $response->body();

            // استفاده از DOM Crawler برای استخراج داده‌ها
            $crawler = new Crawler($html);

            // استخراج تمام المنت‌های HTML
            $this->elements = $crawler->filter('*')->each(function ($node) {
                return [
                    'tag' => $node->nodeName(), // تگ HTML
                    'content' => $node->text(), // محتوای متن
                ];
            });
            // تبدیل داده‌های استخراج‌شده به مارک‌داون
            $this->convertToMarkdown();

        } catch (\Exception $e) {
            $this->elements = ['error' => 'Failed to scrape the website. Please check the URL.'];
        }

    }
    public function convertToMarkdown()
    {
        // تبدیل تمام تگ‌ها و محتواها به قالب مارک‌داون
        $markdownContent = "";

        foreach ($this->elements as $element) {
            $tag = $element['tag'];
            $content = $element['content'];

            switch ($tag) {
                case 'h1':
                    $markdownContent .= "# {$content}\n\n";
                    break;
                case 'h2':
                    $markdownContent .= "## {$content}\n\n";
                    break;
                case 'h3':
                    $markdownContent .= "### {$content}\n\n";
                    break;
                case 'p':
                    $markdownContent .= "{$content}\n\n";
                    break;
                case 'ul':
                case 'ol':
                    $markdownContent .= "- {$content}\n";
                    break;
                default:
                    $markdownContent .= "{$content}\n";
                    break;
            }
        }

        // تنظیم مارک‌داون به متغیر
        $this->markdown = Markdown::defaultTransform($markdownContent);
    }

    public function render()
    {
        return view('livewire.web-scraper');
    }
}
