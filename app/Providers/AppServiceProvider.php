<?php

namespace App\Providers;

use App\Services\ResponseSerializer;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpKernel\Exception\HttpException;

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

        Response::macro('serializeAsRequested', function ($value, $xmlRootNodeName = null, $headers = []) {
            $serializer = new ResponseSerializer();
            try {
                $data = $serializer->serialize($value, $xmlRootNodeName);
            } catch (HttpException $exception) {
                return Response::make($exception->getMessage(), $exception->getStatusCode());
            }

            if (!isset($headers['Content-Type'])) {
                $headers = array_merge($headers, ['Content-Type' => $serializer->getRequestedMimeType()]);
            }
            return Response::make(
                $data,
                200,
                $headers
            );
        });
    }
}
