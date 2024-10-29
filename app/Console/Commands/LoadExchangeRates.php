<?php

namespace App\Console\Commands;

use App\Models\Currency;
use App\Models\CurrencyExchangeRate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class LoadExchangeRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exchange-rates:load';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load exchange rates for all available currencies';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        CurrencyExchangeRate::truncate();

        $currencies = Currency::all();

        foreach ($currencies as $fromCurrency) {
            $exchangeRatesData = Http::get('https://v6.exchangerate-api.com/v6/6680e39093b150914b8c3d21/latest/'.$fromCurrency->code);
            $convertionRates = $exchangeRatesData->json()['conversion_rates'];
            foreach ($currencies as $toCurrency) {
                CurrencyExchangeRate::create([
                    'from_currency_id' => $fromCurrency->id,
                    'to_currency_id' => $toCurrency->id,
                    'rate' => $convertionRates[$toCurrency->code],
                ]);
            }
        }

        // TODO: add some extra messages, then schedule and pass it on to Sentry
    }
}
