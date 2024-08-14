<?php
namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SpotifyService {
    private function getToken() 
    {
        $clientId = config('services.spotify.client_id');
        $clientSecret = config('services.spotify.client_secret');
        $url = "https://accounts.spotify.com/api/token";

        $body = [
            "grant_type" => "client_credentials",
            "client_id" => $clientId,
            "client_secret" => $clientSecret
        ];

        try {
            $response = Http::asForm()->post($url, $body);
            $response = $response->json();

            if(array_key_exists('error', $response)) {
                return false;
            }
            return $response;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }

    public function getUserToken($code) {
        $clientId = config('services.spotify.client_id');
        $clientSecret = config('services.spotify.client_secret');
        $redirectUri = config('services.spotify.redirect');
        $url = "https://accounts.spotify.com/api/token";   

        $body = [
            "grant_type" => "authorization_code",
            "code" => $code,
            "redirect_uri" => $redirectUri,
            "client_id" => $clientId,
            "client_secret" => $clientSecret
        ];

        try {
            $response = Http::asForm()->post($url, $body);
            $response = $response->json();
            

            $spotifyUser = $this->getUser($response['access_token']);

            $largestImage = array_reduce($spotifyUser['images'], function ($carry, $image) {
                if (!$carry || ($image['height'] > $carry['height'])) {
                    return $image;
                }
                return $carry;
            });
            
            $user = User::updateOrCreate(
                ['spotify_id' => $spotifyUser['id']],
                [
                    'name' => $spotifyUser['display_name'],
                    'email' => $spotifyUser['email'],
                    'password' => Hash::make(Str::random(10)), // Set a temporary password
                    'spotify_access_token' => $response['access_token'],
                    'spotify_refresh_token' => $response['refresh_token'],
                    'spotify_expires_in' => now()->addSeconds($response['expires_in']),
                    'spotify_image' => $largestImage['url'] ?? null,
                    'spotify_product' => $spotifyUser['product'] ?? null,
                    'spotify_followers' => $spotifyUser['followers']['total'] ?? null,
                    'password_set' => false,
                ]
            );
            

            Auth::login($user);

            return $response;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }

    public function refreshAccessToken()
    {
        $user = auth()->user();

        $response = Http::asForm()->post('https://accounts.spotify.com/api/token', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $user->spotify_refresh_token,
            'client_id' => config('services.spotify.client_id'),
            'client_secret' => config('services.spotify.client_secret'),
        ]);

        $data = $response->json();

        $user->update([
            'spotify_access_token' => $data['access_token'],
            'spotify_expires_in' => now()->addSeconds($data['expires_in']),
        ]);
    }

    public function login() {
        $url = "https://accounts.spotify.com/authorize";

        $clientId = config('services.spotify.client_id');
        $redirectUri = config('services.spotify.redirect');

        $query = http_build_query([
            // 'show_dialog' => 'true',
            'response_type' => 'code',
            'client_id' => $clientId,
            'scope' => 'user-read-private user-read-email playlist-modify-private playlist-read-private playlist-read-collaborative',
            'redirect_uri' => $redirectUri,
            'state' => csrf_token(),
        ]);

        return $url . '?' . $query;
    }

    public function search($search, $type) {
        $url = "https://api.spotify.com/v1/search";

        $token = auth()->user()->spotify_access_token;

        $response = Http::withHeaders([
            "Authorization" => "Bearer " . $token
        ])->get($url, [
            "q" => $search,
            "type" => $type,
            "limit" => 50
        ]);

        return $response->json();
    }

    public function getRecommendations($amount, $seedArtists, $seedTracks, $seedGenres) {
        $url = "https://api.spotify.com/v1/recommendations";
        $veryAlike = false;
        $underground = false;

        $token = auth()->user()->spotify_access_token;
        
        $body = [
            "seed_tracks" => $seedTracks,
            "limit" => $amount
        ];
        
        if ($seedArtists) {
            $body['seed_artists'] = $seedArtists;
        }
        
        if ($seedGenres) {
            $body['seed_genres'] = $seedGenres;
        }
        
        if ($veryAlike) {
            $trackDetails = $this->getTrackDetails($seedTracks);
            $body['min_danceability'] = $trackDetails['danceability'] - 0.24;
            $body['max_danceability'] = $trackDetails['danceability'] + 0.24;
            $body['min_tempo'] = $trackDetails['tempo'] - 12;
            $body['max_tempo'] = $trackDetails['tempo'] + 12;
            $body['min_energy'] = $trackDetails['energy'] - 0.2;
            $body['max_energy'] = $trackDetails['energy'] + 0.2;
            $body['min_acousticness'] = $trackDetails['acousticness'] - 0.4;
            $body['max_acousticness'] = $trackDetails['acousticness'] + 0.4;
            $body['min_instrumentalness'] = $trackDetails['instrumentalness'] - 0.4;
            $body['max_instrumentalness'] = $trackDetails['instrumentalness'] + 0.4;
            $body['min_liveness'] = $trackDetails['liveness'] - 0.4;
            $body['max_liveness'] = $trackDetails['liveness'] + 0.4;
            $body['min_valence'] = $trackDetails['valence'] - 0.4;
            $body['max_valence'] = $trackDetails['valence'] + 0.4;
        }

        if ($underground) {
            $body['min_popularity'] = 0;
            $body['max_popularity'] = 70;
        }

        $response = Http::withHeaders([
            "Authorization" => "Bearer " . $token
        ])->get($url, $body);
        return $response->json();
    }

    public function getTrackDetails($trackId) {
        $url = "https://api.spotify.com/v1/audio-features/" . $trackId;

        $token = auth()->user()->spotify_access_token;

        $response = Http::withHeaders([
            "Authorization" => "Bearer " . $token  
        ])->get($url);
        return $response->json();
    }

    public function getUser($userToken) {
        $url = "https://api.spotify.com/v1/me";

        $response = Http::withHeaders([
            "Authorization" => "Bearer " . $userToken
        ])->get($url);
        return $response->json();
    }

    public function createPlaylist($name) {
        $user = auth()->user();
        $url = "https://api.spotify.com/v1/users/" . $user->spotify_id . "/playlists";

        $token = $user->spotify_access_token;

        $body = [
            "name" => $name,
            "description" => "Recommendations generated by Spotify Rec",
            "public" => false
        ];

        $response = Http::withHeaders([
            "Authorization" => "Bearer " . $token
        ])->post($url, $body);
        return $response->json();
    }

    public function addSongsToPlaylist($playlistId, $trackIds) {
        $user = auth()->user();
        $url = "https://api.spotify.com/v1/playlists/" . $playlistId . "/tracks";

        $token = $user->spotify_access_token;

        $body = [
            "uris" => $trackIds,
        ];

        $response = Http::withHeaders([
            "Authorization" => "Bearer " . $token
        ])->post($url, $body);
        return $response->json();
    }

    public function getUserPlaylists() {
        $user = auth()->user();
        $url = "https://api.spotify.com/v1/users/" . $user->spotify_id . "/playlists";

        $token = $user->spotify_access_token;

        $response = Http::withHeaders([
            "Authorization" => "Bearer " . $token,
        ])->get($url);

        return $response->json();
    }

    public function getUserPlaylist($playlistId) {
        $user = auth()->user();
        $url = "https://api.spotify.com/v1/playlists/" . $playlistId;

        $token = $user->spotify_access_token;

        $response = Http::withHeaders([
            "Authorization" => "Bearer " . $token,
        ])->get($url);
        return $response->json();
    }

    public function getUserPlaylistTracks($playlistId, $total) {
        $user = auth()->user();
        $url = "https://api.spotify.com/v1/playlists/" . $playlistId . "/tracks";

        $token = $user->spotify_access_token;

        $tracks = [];

        
        if ($total > 100) {
            for ($i = 0; $i < ceil($total / 100); ++$i) {
                $response = Http::withHeaders([
                    "Authorization" => "Bearer " . $token,
                ])->get($url, [
                    "limit" => 100,
                    "offset" => $i * 100
                ]);

                $tracks = array_merge($tracks, $response->json()['items']);
            }
        } else {
            $response = Http::withHeaders([
                "Authorization" => "Bearer " . $token,
            ])->get($url);

            $tracks = $response->json()['items'];
        }

        return $tracks;
    }
}
