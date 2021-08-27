<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\AuthenticationException;
use Laravel\Socialite\Facades\Socialite;
use SpotifyWebAPI\Session;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

//    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Redirect the user to the provider authentication page.
     *
     */
    public function redirectToProvider($provider)
    {
//        $session = new Session(
//            env('SPOTIFY_CLIENT_ID'),
//            env('SPOTIFY_CLIENT_SECRET'),
//            env('SPOTIFY_REDIRECT_URI')
//        );
//
//        $state = $session->generateState();
//        $options = [
//            'scope' => [
//                'playlist-read-private',
//                'user-read-private',
//                'user-top-read',
//                'user-read-currently-playing',
//            ],
//            'state' => $state,
//        ];
//
//        header('Location: ' . $session->getAuthorizeUrl($options));
//        die();
        return Socialite::driver($provider)->scopes([
            'playlist-read-private',
            'user-read-private',
            'user-top-read',
            'user-read-currently-playing',
        ])->redirect();
    }


    /**
     * Obtain the user information from provider.
     *
     */
    public function handleProviderCallback(Request $request, $provider)
    {
        $session = new Session(
            env('SPOTIFY_CLIENT_ID'),
            env('SPOTIFY_CLIENT_SECRET'),
            env('SPOTIFY_REDIRECT_URI')
        );

        $state = $_GET['state'];
        $session->requestAccessToken($_GET['code']);

        $accessToken = $session->getAccessToken();
        $refreshToken = $session->getRefreshToken();

        session(['accessTokenSpotify' => $accessToken]);

        $authUser = User::byOAuthByToken($accessToken, $provider);
        Auth::login($authUser, true);

        return redirect($this->redirectTo);
    }

    public function buildCredentials(array $args = [], $grantType = 'password')
    {
        $args = collect($args);
        $credentials = $args->except(['directive', 'administration'])->toArray();
        $credentials['client_id'] = $args->get('client_id', config('lighthouse-graphql-passport.client_id'));
        $credentials['client_secret'] = $args->get('client_secret', config('lighthouse-graphql-passport.client_secret'));
        $credentials['grant_type'] = $grantType;

        return $credentials;
    }

    /**
     * @param array $credentials
     *
     * @throws AuthenticationException
     *
     * @return mixed
     */
    public function makeRequest(array $credentials)
    {
        $request = Request::create('oauth/token', 'POST', $credentials, [], [], [
            'HTTP_Accept' => 'application/json',
        ]);
        $response = app()->handle($request);
        $decodedResponse = json_decode($response->getContent(), true);
        if ($response->getStatusCode() != 200) {
            if ($decodedResponse['message'] === 'The provided authorization grant (e.g., authorization code, resource owner credentials) or refresh token is invalid, expired, revoked, does not match the redirection URI used in the authorization request, or was issued to another client.') {
                throw new AuthenticationException(__('Authentication exception'), __('Incorrect username or password'));
            }
            throw new AuthenticationException(__($decodedResponse['message']), __($decodedResponse['exception'] ?? $decodedResponse['error']));
        }

        return $decodedResponse;
    }

    /**
     * If a user has registered before using social auth, return the user
     * else, create a new user object.
     * @param  $user Socialite user object
     * @return  User
     */
    public function findOrCreateUser($user, $provider)
    {
        $authUser = User::where('provider_id', $user->id)->first();
        if ($authUser) {
            return $authUser;
        }
        return User::create([
            'name'     => $user->name,
            'email'    => $user->email,
            'provider' => $provider,
            'provider_id' => $user->id
        ]);
    }
}
