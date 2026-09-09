<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductionStocksPriceFinancialsSeeder extends Seeder
{
    /**
     * Fills local unlisted_stocks / unlisted_price_data / unlisted_financials with the
     * remaining production companies (FINCODE 100021-100129) that the local dev DB is
     * missing (it only had the first 20). Uses INSERT IGNORE so re-running is safe.
     */
    public function run(): void
    {
        $sql = file_get_contents(__DIR__ . '/data/production_stocks_price_financials.sql');

        DB::unprepared($sql);
    }
}
