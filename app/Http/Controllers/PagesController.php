<?php


namespace App\Http\Controllers;




use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PagesController extends Controller
{



    public function friends()
    {
        $myGenres = Auth::user()->genres->pluck('id');
        $users =  User::whereHas('genres', function ($q) use ($myGenres) {
            return $q->whereIn('genre_id', $myGenres);
        })->get();
        return view('find-friends')->with('users', $users);
    }
}
