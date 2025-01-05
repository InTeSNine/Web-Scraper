<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WebCrawlerService;
use App\Services\LlmService;
use Symfony\Component\DomCrawler\Crawler;

class CrawlerController extends Controller
{
    protected $crawlerService;
    protected $llmService;

    public function __construct(WebCrawlerService $crawlerService, LlmService $llmService)
    {
        $this->crawlerService = $crawlerService;
        $this->llmService = $llmService;
    }

    public function handleCrawl(Request $request)
    {
        $url = $request->input('url');
        $userRequest = $request->input('request');
        $limit = $request->input('limit');

        if (!$url || !$userRequest) {
            return response()->json(['error' => 'URL and request are required'], 400);
        }

        try {
            // مرحله اول: استخراج ساختار اولیه HTML
            $htmlStructure = $this->crawlerService->crawl($url);

            // مرحله دوم: دریافت ساختار دقیق مورد نیاز از LLM
            $structure = $this->llmService->getExtractionStructure($htmlStructure, $userRequest);

            // مرحله سوم: کراولینگ عمیق بر اساس ساختار دریافتی
            $crawler = new Crawler(file_get_contents($url));
            $results = $this->crawlerService->extractDeep($crawler, $structure);

            // محدودسازی تعداد نتایج
            if ($limit) {
                $results = array_slice($results, 0, $limit);
            }

            // مرحله نهایی: ساخت خروجی ساختار‌یافته
            return response()->json([
                'metadata' => [
                    'url' => $url,
                    'request' => $userRequest,
                    'date' => now()->toISOString(),
                    'total_results' => count($results)
                ],
                'data' => [
                    'results' => $results
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
