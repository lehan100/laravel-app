<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SessionClear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'session:clear';

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
        //
        $files = File::allFiles(storage_path('framework/sessions/'));
        foreach ($files as $file) {
            File::delete(storage_path('framework/sessions/' . $file->getFilename()));
        }
        $this->info('The clear all session was successful!');
    }
}
