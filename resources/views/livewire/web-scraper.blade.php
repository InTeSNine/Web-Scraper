<div class="w-full bg-gray-50 py-8 px-4 space-y-8 min-h-screen relative">
    <h1 class="text-2xl font-bold mb-4 text-center">Web Scraper</h1>

    <!-- فرم دریافت URL -->
    <form wire:submit.prevent="handle" class="mb-4 max-w-screen-md mx-auto">
        <div class="flex gap-4">
            <input type="url" wire:model="url" placeholder="Enter website URL"
                class="border border-gray-300 rounded p-2 flex-1" />
            <input type="text" wire:model="UserPrompt" placeholder="Write what you want (e.g., titles and prices)"
                class="border border-gray-300 rounded p-2 flex-1" />
            <button type="submit"
                class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 focus:ring focus:ring-blue-300 transition"
                wire:loading.attr="disabled">
                Scrape
            </button>
        </div>
        @error('url')
            <span class="text-red-500">{{ $message }}</span>
        @enderror
    </form>

    <!-- پیام در حال بارگذاری -->
    <div wire:loading class="fixed inset-0 bg-gray-100 bg-opacity-75 flex items-center justify-center z-50">
        <div class="flex items-center gap-4 bg-white p-6 rounded shadow-lg">
            <svg class="animate-spin h-6 w-6 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <span class="text-blue-500 font-semibold text-lg">Scraping data... Please wait.</span>
        </div>
    </div>

    <!-- نمایش نتایج نهایی -->
    <div class="mt-8 max-w-screen-md mx-auto">
        @if ($results)
            <!-- دکمه دانلود -->
            <div class="text-center mb-6">
                <button wire:click="downloadCsv" class="bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg hover:bg-green-600 focus:ring focus:ring-green-300 transition transform hover:scale-105">
                    Download CSV
                </button>
            </div>
        @endif

        <!-- نمایش خطاها -->
        @if ($errorMessage)
            <div class="alert alert-danger">{{ $errorMessage }}</div>
        @endif

        <h2 class="text-xl font-bold mb-4 text-green-600 text-center">Extracted Information</h2>
        <div class="border p-6 rounded bg-white shadow-lg max-h-96 overflow-y-auto text-gray-800 w-full">
            @if (!empty($results))
                <ul class="list-disc pl-6">
                    @foreach ($results as $key => $value)
                        <li class="mb-2">
                            <strong class="text-gray-700">{{ ucfirst($key) }}:</strong>
                            @if (is_array($value))
                                <ul class="list-circle pl-4">
                                    @foreach ($value as $subKey => $subValue)
                                        <li>{{ ucfirst($subKey) }}: {{ $subValue }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-gray-600">{{ $value }}</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="text-center text-gray-400">
                    <p class="text-xl">📋 No extracted data available yet.</p>
                    <p>Enter a URL and click <strong>Scrape</strong> to get results!</p>
                </div>
            @endif
        </div>
    </div>

    <!-- نمایش خطاها دوباره در صورت وجود -->
    @if ($errorMessage)
        <div class="alert alert-danger">{{ $errorMessage }}</div>
    @endif
</div>
