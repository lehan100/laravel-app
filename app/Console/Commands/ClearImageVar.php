<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearImageVar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:clear-image-var';

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
        array_map('unlink', glob(public_path('var/product') . '/*'));
        array_map('unlink', glob(public_path('var/attribute') . '/*'));
        array_map('unlink', glob(public_path('var/option') . '/*'));
        array_map('unlink', glob(public_path('var/temp') . '/*'));
        $this->info('The clear image tmp was successful!');
    }
}
