<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\MarkdownConverterService;
use App\Services\OpenAIService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WebScraper extends Component
{
    public $url;
        public $UserPrompt;
    public $markdownContent = ""; 
    public $LLMrespond = '';  
    public $results = [];    
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

            $markdownContent = $markdownService->markdown($this->base_url, $this->url);

            $this->markdownContent = $markdownContent;

            $schema = $openAIService->generateSchema($this->UserPrompt);
            if (!$schema) {
                throw new \Exception('Failed to generate JSON schema.');
            }

            $structuredData = $openAIService->extractInformation($markdownContent, $schema); // $schema یک آرایه است
            if (!$structuredData) {
                throw new \Exception('Failed to extract information.');
            }
            $this->results = $structuredData; 
        } catch (\Exception $e) {
            $this->errorMessage = 'Error: ' . $e->getMessage();
        }
    }
    public function downloadCsv()
{
    if (empty($this->results) || !is_array($this->results)) {
        $this->errorMessage = 'No data available to download.';
        return;
    }

    $response = new StreamedResponse(function () {
        $handle = fopen('php://output', 'w');

        $headers = array_keys(reset($this->results));
        fputcsv($handle, $headers);

        $rows = [];
        foreach ($headers as $key) {
            $row = [];
            foreach ($this->results as $result) {
                $row[] = $result[$key] ?? ''; 
            }
            $rows[] = $row;
        }

        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }

        fclose($handle);
    });

    $response->headers->set('Content-Type', 'text/csv');
    $response->headers->set('Content-Disposition', 'attachment; filename="results.csv"');

    return $response;
}


}
