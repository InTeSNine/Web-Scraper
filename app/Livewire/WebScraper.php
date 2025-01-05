<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\WebCrawlerService;
use App\Services\LlmService;

class WebScraper extends Component
{
    public $url="https://torob.com/browse/471/%D8%A7%D8%AC%DB%8C%D9%84-%D9%88-%D8%AE%D8%B4%DA%A9%D8%A8%D8%A7%D8%B1/";
    public $userRequest="عکس ها و تایتل و قیمت های";
    public $limit=10;
    public $results = [];
    public $errorMessage = '';

    protected $rules = [
        'url' => 'required|url',
        'userRequest' => 'required|string',
        'limit' => 'nullable|integer|min:1'
    ];

    protected $messages = [
        'url.required' => 'URL is required',
        'url.url' => 'Please provide a valid URL',
        'userRequest.required' => 'Request is required'
    ];

    public function render()
    {
        return view('livewire.web-scraper');
    }

    public function handleCrawl()
    {
        $this->validate();

        try {
            // اتصال به WebCrawlerService و LlmService برای استخراج داده‌ها
            $crawlerService = new WebCrawlerService();
            $llmService = new LlmService();

            // مرحله اول: استخراج ساختار اولیه HTML
            $htmlStructure = $crawlerService->crawl($this->url);
            dd($htmlStructure);
            // مرحله دوم: دریافت ساختار دقیق مورد نیاز از LLM
            $structure = $llmService->getExtractionStructure($htmlStructure, $this->userRequest);

            // مرحله سوم: کراولینگ عمیق بر اساس ساختار دریافتی
            $results = $crawlerService->extractDeep(new \Symfony\Component\DomCrawler\Crawler(file_get_contents($this->url)), $structure);

            // محدودسازی تعداد نتایج
            if ($this->limit) {
                $results = array_slice($results, 0, $this->limit);
            }

            // ذخیره نتایج برای نمایش
            $this->results = $results;

        } catch (\Exception $e) {
            $this->errorMessage = 'Error: ' . $e->getMessage();
        }
    }
}
