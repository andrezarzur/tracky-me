<?php

namespace App\Http\Controllers;

use App\Services\SpotifyService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PlaylistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $spotifyService = new SpotifyService();

        $playlists = $spotifyService->getUserPlaylists();
        return Inertia::render('Playlists/Index', [
            'playlists' => $playlists
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $spotifyService = new SpotifyService();
        $playlist = $spotifyService->getUserPlaylist($id);

        // $playlistTracks = collect($playlist['tracks']['items'])->map(function ($item) {
        //     return $item['track'];
        // });

        $playlistTracks =  $spotifyService->getUserPlaylistTracks($playlist['id'], $playlist['tracks']['total']);

        $flattenedCollection = collect($playlistTracks)->map(function ($item) {
            if (isset($item['track'])) {
                return array_merge($item, $item['track']);
            }
            return $item;
        })->map(function ($item) {
            unset($item['track']);
            return $item;
        });
        
        return Inertia::render('Playlists/Show', [
            'playlist' => $playlist,
            'playlistTracks' => $flattenedCollection
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
