<?php

namespace App\Console\Commands;

use App\Models\Currency;
use App\Models\CurrencyExchangeRate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

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

        $currencies = Currency::all();

        $errors = 0;
        foreach ($currencies as $fromCurrency) {
            try {
                $exchangeRatesData = Http::get('https://v6.exchangerate-api.com/v6/'.config('services.exchangerate.key').'/latest/'.$fromCurrency->code)
                    ->throw()
                    ->json();

                $currencyCodes = $currencies->pluck('code')->toArray();
                
                Validator::make($exchangeRatesData, [
                    'conversion_rates' => [
                        'required', 
                        'array',
                        function ($attribute, $value, $fail) use ($currencyCodes) {
                            if (!is_array($value)) return; // Skip if not array, let 'array' rule handle it
                            $missingCodes = array_diff($currencyCodes, array_keys($value));
                            if (!empty($missingCodes)) {
                                $fail('Missing conversion rates for currencies: ' . implode(', ', $missingCodes));
                            }
                        },
                    ],
                    'conversion_rates.*' => ['required', 'numeric'],
                ])->validate();

                // Only delete and create new records if the response is valid
                CurrencyExchangeRate::query()->where('from_currency_id', $fromCurrency->id)->delete();

                $convertionRates = $exchangeRatesData['conversion_rates'];
                foreach ($currencies as $toCurrency) {
                    CurrencyExchangeRate::create([
                        'from_currency_id' => $fromCurrency->id,
                        'to_currency_id' => $toCurrency->id,
                        'rate' => $convertionRates[$toCurrency->code],
                    ]);
                }

                $this->info('Exchange rates for '.$fromCurrency->code.' loaded successfully');
            } catch (\Exception $e) {
                $this->error('Failed importing exception rates for ' . $fromCurrency->code . ': ' . $e->getMessage());
                $errors++;
                continue;
            }
        }

        return $errors == 0 ? self::SUCCESS : self::ERROR_CODE_API_REQUESTS_FAILED;
    }
}
