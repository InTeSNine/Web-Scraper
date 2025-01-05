<?php
namespace App\Services;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class WebCrawlerService
{
    protected $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client();
    }

    public function crawl(string $url)
    {
        $response = $this->httpClient->get($url);
        $html = $response->getBody()->getContents();

        $crawler = new Crawler($html);

        // مرحله اول: گرفتن ساختار نمونه از صفحه
        $htmlStructure = $this->extractSample($crawler->filter('body'));

        return $htmlStructure;
    }

    private function extractSample(Crawler $crawler)
    {
        $structure = [];
        $structure['tag'] = $crawler->nodeName();
        $structure['attributes'] = $this->getAttributes($crawler);

        $childTags = [];
        $crawler->children()->each(function (Crawler $child) use (&$childTags) {
            $tagName = $child->nodeName();
            if (!isset($childTags[$tagName])) {
                $childTags[$tagName] = $this->extractSample($child);
            }
        });

        $structure['children'] = array_values($childTags);

        return $structure;
    }

    private function getAttributes(Crawler $crawler)
    {
        $node = $crawler->getNode(0);

        if (!$node || !$node->attributes) {
            return [];
        }

        $attributes = [];
        foreach ($node->attributes as $attr) {
            $attributes[$attr->nodeName] = $attr->nodeValue;
        }

        return $attributes;
    }

    public function extractDeep(Crawler $crawler, array $structure)
    {
        $results = [];

        $crawler->filter("{$structure['tag']}" . ($structure['class'] ? ".{$structure['class']}" : ''))->each(function (Crawler $node) use (&$results, $structure) {
            $item = [];

            if (isset($structure['attributes_to_extract'])) {
                foreach ($structure['attributes_to_extract'] as $key => $selector) {
                    $item[$key] = $node->filter($selector)->count() > 0
                        ? $node->filter($selector)->text()
                        : null;
                }
            }

            if (isset($structure['children'])) {
                foreach ($structure['children'] as $childStructure) {
                    $item['children'][] = $this->extractDeep($node, $childStructure);
                }
            }

            $results[] = $item;
        });

        return $results;
    }
}
