<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
class InitialState extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            echo PHP_EOL;
            echo "Refactor DB: " . PHP_EOL;
            Artisan::call('migrate:fresh');
            echo "Seeders: " . PHP_EOL;
            Artisan::call('db:seed');
            echo "Passport: " . PHP_EOL;
            Artisan::call('passport:install');
            echo "Success load DB" . PHP_EOL;
        } catch (\Exception $exception) {
            dd($exception->getMessage());
        }
    }
}
