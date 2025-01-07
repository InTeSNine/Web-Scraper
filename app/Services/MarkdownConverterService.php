<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;


class MarkdownConverterService
{

    public function markdown($base_url,$url)
    {
        
        $response = Http::get($base_url . $url);

        if ($response->failed()) {
            $this->content = "Failed to load markdown content";
            return;
        }

        $content = $response->body();
        return $content;
    }
}
