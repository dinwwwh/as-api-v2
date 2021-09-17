<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Storage;
use League\Flysystem\Filesystem;
use Spatie\Dropbox\Client;
use Spatie\FlysystemDropbox\DropboxAdapter;

class DropboxServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     *
     */
    public function boot(): void
    {
        Storage::extend('dropbox', function ($app, $config) {
            $client = new Client(
                $config['access_token']
            );

            return new Filesystem(new DropboxAdapter($client));
        });
    }
}
