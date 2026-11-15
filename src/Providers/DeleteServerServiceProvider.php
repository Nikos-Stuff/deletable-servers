<?php

namespace NikosStuff\DeleteServerPlugin\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use NikosStuff\DeleteServerPlugin\Livewire\DeleteServerSection;

class DeleteServerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Same external connection the Niko's Stuff theme uses. If the theme (or anything
        // else) already defined it, leave it alone; otherwise define it from the same env keys.
        if (config('database.connections.hosting') === null) {
            config([
                'database.connections.hosting' => [
                    'driver' => 'mysql',
                    'host' => env('DB_HOSTING_HOST', '127.0.0.1'),
                    'port' => env('DB_HOSTING_PORT', '3306'),
                    'database' => env('DB_HOSTING_DATABASE', 'hosting_orders'),
                    'username' => env('DB_HOSTING_USERNAME', 'manager'),
                    'password' => env('DB_HOSTING_PASSWORD', ''),
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'prefix' => '',
                    'strict' => true,
                ],
            ]);
        }
    }

    public function boot(): void
    {
        // Views (delete-server::) and translations (delete-server::strings.*) are
        // registered automatically by the panel's plugin loader.
        Livewire::component('delete-server-section', DeleteServerSection::class);
    }
}
