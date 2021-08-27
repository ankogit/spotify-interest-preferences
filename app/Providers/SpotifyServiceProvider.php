<?php


namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI;


class SpotifyServiceProvider extends ServiceProvider
{
    public function register()
    {
        $request = app(\Illuminate\Http\Request::class);

//        $this->app->singleton('SpotifyWebApi\SpotifyWebApi', function ($app) {
//            $client = new SpotifyWebApi;
//
////            $session = new Session(
////                env('SPOTIFY_CLIENT_ID'),
////                env('SPOTIFY_CLIENT_SECRET'),
////                env('SPOTIFY_CALLBACK_URL')
////            );
//
//            $scopes = [
//                'playlist-read-private',
//                'user-read-private',
//                'user-top-read',
//                'user-read-currently-playing',
//            ];
////            $session->requestCredentialsToken();
////
////            $accessToken = $session->getAccessToken();
//            $accessToken = session('accessTokenSpotify');
//            dd($accessToken);
//            $client->setAccessToken($accessToken);
//dd($session->getScope());
//            return $client;
//        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
    }
}
