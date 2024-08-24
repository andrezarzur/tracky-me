<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\SpotifyService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class SpotifyController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    private SpotifyService $spotifyService;
    public function __construct(SpotifyService $spotifyService)
    {
        $this->spotifyService = $spotifyService;
    }

    public function search(Request $request)
    {
        $response = $this->spotifyService->search($request->input('query'), 'track');

        $user = User::where('id', auth()->user()->id)->first();
        $metrics = $user->metrics;
        $metrics['timesSearched'] += 1;
        $user->update(['metrics' => $metrics]);

        return $response;
    }

    public function recommendations(Request $request)
    {
        $response = $this->spotifyService->getRecommendations($request->input('amount'), $request->input('seed_artists'), $request->input('seed_tracks'), $request->input('seed_genres'));

        $user = User::where('id', auth()->user()->id)->first();
        $metrics = $user->metrics;
        $metrics['recommendationsCreated'] += 1;
        $user->update(['metrics' => $metrics]);

        return $response;
    }

    public function login()
    {
        $response = $this->spotifyService->login();
        
        return $response;
    }

    public function callback(Request $request)
    {
        $this->spotifyService->getUserToken($request->input('code'));
        $user = User::where('id', auth()->user()->id)->first();
        $metrics = $user->metrics;
        $metrics['visits'] += 1;
        $user->update(['metrics' => $metrics]);
        return redirect()->route('home');
    }

    public function refreshAccessToken()
    {
        $this->spotifyService->refreshAccessToken();
    }

    public function createPlaylist(Request $request)
    {
        $tracksIds = $request->params['tracks_ids'];
        $name = $request->params['name'];
        $playlistInfo = $this->spotifyService->createPlaylist($name);

        $this->spotifyService->addSongsToPlaylist($playlistInfo['id'], $tracksIds);

        $user = User::where('id', auth()->user()->id)->first();
        $metrics = $user->metrics;
        $metrics['playlistsCreated'] += 1;
        $user->update(['metrics' => $metrics]);

        return;
    }
}
