<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Joselfonseca\LighthouseGraphQLPassport\HasSocialLogin;
use Joselfonseca\LighthouseGraphQLPassport\Models\SocialProvider;
use Laravel\Passport\HasApiTokens;
use Laravel\Socialite\Facades\Socialite;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    use HasSocialLogin;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',

    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'user_genres');
    }

    public function socialProviders()
    {
        return $this->hasMany(SocialProvider::class);
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public static function byOAuthSocialite($userData, $provider)
    {
        try {
            $user = static::whereHas('socialProviders', function ($query) use ($provider, $userData) {
                $query->where('provider', Str::lower($provider))->where('provider_id', $userData->getId());
            })->firstOrFail();
        } catch (ModelNotFoundException $e) {
            $user = static::where('email', $userData->getEmail())->first();
            if (! $user) {
                $user = static::create([
                    'name' => $userData->getName(),
                    'email' => $userData->getEmail(),
                    'uuid' => Str::uuid(),
                    'password' => Hash::make(Str::random(16)),
                    'email_verified_at' => now(),
                    'avatar' => $userData->getAvatar(),
                ]);
            }
            SocialProvider::create([
                'user_id' => $user->id,
                'provider' => $provider,
                'provider_id' => $userData->getId(),
            ]);
        }
        Auth::setUser($user);

        return $user;
    }
    /**
     * @param Request $request
     *
     * @return mixed
     */
    public static function byOAuthToken(Request $request)
    {
        $userData = Socialite::driver($request->get('provider'))->userFromToken($request->get('token'));
        try {
            $user = static::whereHas('socialProviders', function ($query) use ($request, $userData) {
                $query->where('provider', Str::lower($request->get('provider')))->where('provider_id', $userData->getId());
            })->firstOrFail();
        } catch (ModelNotFoundException $e) {
            $user = static::where('email', $userData->getEmail())->first();
            if (! $user) {
                $user = static::create([
                    'name' => $userData->getName(),
                    'email' => $userData->getEmail(),
                    'uuid' => Str::uuid(),
                    'password' => Hash::make(Str::random(16)),
                    'email_verified_at' => now(),
                ]);
            }
            SocialProvider::create([
                'user_id' => $user->id,
                'provider' => $request->get('provider'),
                'provider_id' => $userData->getId(),
            ]);
        }
        Auth::setUser($user);

        return $user;
    }
    /**
     * @param Request $request
     *
     * @return mixed
     */
    public static function byOAuthByToken($token, $provider)
    {
        $userData = Socialite::driver($provider)->userFromToken($token);
        try {
            $user = static::whereHas('socialProviders', function ($query) use ($provider, $userData) {
                $query->where('provider', Str::lower($provider))->where('provider_id', $userData->getId());
            })->firstOrFail();
        } catch (ModelNotFoundException $e) {
            $user = static::where('email', $userData->getEmail())->first();
            if (! $user) {
                $user = static::create([
                    'name' => $userData->getName(),
                    'email' => $userData->getEmail(),
                    'uuid' => Str::uuid(),
                    'password' => Hash::make(Str::random(16)),
                    'email_verified_at' => now(),
                ]);
            }
            SocialProvider::create([
                'user_id' => $user->id,
                'provider' => $provider,
                'provider_id' => $userData->getId(),
            ]);
        }
        Auth::setUser($user);

        return $user;
    }
}
