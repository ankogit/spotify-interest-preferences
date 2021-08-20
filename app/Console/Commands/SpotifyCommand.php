<?php

namespace App\Console\Commands;

use Spotify;
use SpotifySeed;
use Illuminate\Console\Command;

class SpotifyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spotify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'spotify';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
//        $data = Spotify::searchTracks('Behind blue')->limit(50)->offset(50)->get();
//'6sscf43j3hj8vjfenksv10u08'
//        $data = Spotify::availableGenreSeeds()->get(); // все жанры
//        $data = Spotify::availableGenreSeeds()->get(); // все жанры

        $seed = SpotifySeed::setGenres([
            "russian punk"]);
        $data = Spotify::recommendations($seed)->get();
        dd($data);
        return 0;
    }
}
