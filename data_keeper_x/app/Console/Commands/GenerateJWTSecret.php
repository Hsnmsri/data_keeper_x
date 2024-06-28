<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;

class GenerateJWTSecret extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jwt:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a JWT secret key and add it to .env file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $key = Str::random(64);

        // Update .env file
        $path = base_path('.env');
        if (file_exists($path)) {
            file_put_contents($path, str_replace(
                'JWT_SECRET=' . env('JWT_SECRET'),
                'JWT_SECRET=' . $key,
                file_get_contents($path)
            ));
        }
    }
}
