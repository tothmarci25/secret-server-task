<?php

namespace App\Providers;

use App\Services\ResponseSerializer;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        JsonResource::withoutWrapping();

        Schema::defaultStringLength(191);

        Response::macro('serializeAsRequested', function ($value, $xmlRootNodeName = null) {
            $serializer = new ResponseSerializer();
            return Response::make($serializer->serialize($value, $xmlRootNodeName));
        });
    }
}
