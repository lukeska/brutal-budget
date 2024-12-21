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

    const SUCCESS = 0;
    const ERROR_CODE_MISSING_API_KEY = 1;
    const ERROR_CODE_API_REQUESTS_FAILED = 1;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (! config('services.exchangerate.key')) {
            $this->error('No Exchangerate-API key available. Rates were not imported');
            return self::ERROR_CODE_MISSING_API_KEY;
        }

        CurrencyExchangeRate::truncate();

        $currencies = Currency::all();

        $errors = 0;
        foreach ($currencies as $fromCurrency) {
            try {
                $exchangeRatesData = Http::get('https://v6.exchangerate-api.com/v6/'.config('services.exchangerate.key').'/latest/'.$fromCurrency->code)
                    ->throw()
                    ->json();
            } catch (\Exception $e) {
                $this->error('Failed importing exception rates for ' . $fromCurrency->code . ': ' . $e->getMessage());
                $errors++;
                continue;
            }

            $convertionRates = $exchangeRatesData['conversion_rates'];
            foreach ($currencies as $toCurrency) {
                CurrencyExchangeRate::create([
                    'from_currency_id' => $fromCurrency->id,
                    'to_currency_id' => $toCurrency->id,
                    'rate' => $convertionRates[$toCurrency->code],
                ]);
            }

            $this->info('Exchange rates for '.$fromCurrency->code.' loaded successfully');
        }

        return $errors == 0 ? self::SUCCESS : self::ERROR_CODE_API_REQUESTS_FAILED;
    }
}
