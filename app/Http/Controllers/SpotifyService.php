<?php


namespace App\Http\Controllers;


use App\Models\Genre;
use App\Models\UserGenre;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Spotify;
use SpotifySeed;
use SpotifyWebAPI\SpotifyWebAPI;

class SpotifyService extends Controller
{

    /**
     * @var \Illuminate\Contracts\Foundation\Application|mixed
     */
    private $spotify;

    public function __construct()
    {
        $this->spotify = new SpotifyWebApi;
    }

    public function analyzeMyProfile()
    {
        $accessToken = session('accessTokenSpotify');
        $this->spotify->setAccessToken($accessToken);
        $artists = $this->spotify->getMyTop('artists');
        foreach ($artists->items as $artist) {
            foreach ($artist->genres as $genre) {
                Genre::updateOrInsert(['name' => $genre], ['name' => $genre]);
                $genreObj = Genre::where('name', $genre)->first();
                UserGenre::updateOrInsert(['genre_id' => $genreObj->id, 'user_id' => Auth::id()], ['genre_id' => $genreObj->id, 'user_id' => Auth::id()]);

            }
        }
        return redirect(RouteServiceProvider::HOME);
    }
}
