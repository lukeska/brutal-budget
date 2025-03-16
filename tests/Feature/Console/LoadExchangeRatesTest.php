<?php

use App\Models\CurrencyExchangeRate;
use Illuminate\Support\Facades\Http;

pest()->group('brutal');

test('existing exchange rates are preserved when API request fails', function () {
    // Mock the HTTP client to simulate a failed request
    Http::fake([
        '*' => Http::response(null, 500),
    ]);

    $exchangeRateRecordsCount = CurrencyExchangeRate::count();

    // Run the command
    $this->artisan('exchange-rates:load')
        ->assertFailed()
        ->expectsOutputToContain('Failed importing exception rates for USD: HTTP request returned status code 500');

    // Assert that the original exchange rate still exists
    expect(CurrencyExchangeRate::count())->toBe($exchangeRateRecordsCount);
});

test('existing exchange rates are preserved when API returns invalid response', function () {
    // Mock the HTTP client to return invalid data
    Http::fake([
        '*' => Http::response([
            'conversion_rates' => null
        ], 200),
    ]);

    $exchangeRateRecordsCount = CurrencyExchangeRate::count();

    // Run the command
    $this->artisan('exchange-rates:load')
        ->assertFailed();

    // Assert that the original exchange rate still exists
    expect(CurrencyExchangeRate::count())->toBe($exchangeRateRecordsCount);
}); 