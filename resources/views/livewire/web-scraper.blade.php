<div>
    <!-- فرم ورودی -->
    <div class="mb-4">
        <label for="url" class="block text-gray-700">URL</label>
        <input type="text" wire:model="url" id="url" class="border p-2 w-full" placeholder="Enter URL">
        @error('url') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div class="mb-4">
        <label for="userRequest" class="block text-gray-700">Request</label>
        <input type="text" wire:model="userRequest" id="userRequest" class="border p-2 w-full" placeholder="Enter your scraping request">
        @error('userRequest') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div class="mb-4">
        <label for="limit" class="block text-gray-700">Limit</label>
        <input type="number" wire:model="limit" id="limit" class="border p-2 w-full" placeholder="Enter number of results">
    </div>

    <!-- دکمه ارسال -->
    <button wire:click="handleCrawl" class="bg-blue-500 text-white py-2 px-4 rounded mt-4">Crawl</button>

    <!-- نمایش خطای احتمالی -->
    @if($errorMessage)
        <div class="mt-4 text-red-500">{{ $errorMessage }}</div>
    @endif

    <!-- نمایش نتایج به صورت JSON -->
    @if($results)
        <div class="mt-6">
            <h2 class="text-lg font-semibold mb-4">Scraping Results (Raw JSON)</h2>
            <pre class="bg-gray-100 p-4 rounded overflow-auto text-sm">
                {{ json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}
            </pre>
        </div>
    @endif
</div>
