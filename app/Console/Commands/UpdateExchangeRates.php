<?php

namespace App\Console\Commands;

use App\Services\CurrencyService;
use Illuminate\Console\Command;

class UpdateExchangeRates extends Command
{
    protected $signature = 'currency:update-rates';

    protected $description = 'Fetch the latest exchange rates from the API and update the database';

    public function handle(CurrencyService $currencyService): int
    {
        $this->info('Fetching latest exchange rates…');
        $currencyService->updateExchangeRates();
        $this->info('Exchange rates updated successfully.');

        return self::SUCCESS;
    }
}
