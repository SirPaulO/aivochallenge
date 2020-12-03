<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Traits\SpotifyTrait;

class AlbumController extends Controller  {
  use SpotifyTrait;

  /**
   * Display a listing of the resource.
   *
   * @param Request $request
   *
   * @return JsonResponse
   */
  public function index(Request $request) {
    if(!isset($request['q']))
      return $this->quickResponse('The query (q) param is required.',true, 400);

    try {
      $albums = $this->getAlbumsByArtist($request['q']);
      if(!$albums)
        return $this->quickResponse('There is no artists matching you search.', true, 400);
    } catch (\Throwable $th) {
      return $this->quickResponse("Error fetching Spotify's API. Did you set your credentials?", true, 400);
    }

    $parsedAlbums = [];

    // Parse albums to match required output format
    foreach ($albums as $album) {
      $tmpAlbum = [
        'name' => $album->name,
        'released' => $album->release_date,
        'tracks' => $album->total_tracks,
        'cover' => $album->images[0]
      ];
      $parsedAlbums[] = $tmpAlbum;
    }

    return response()->json($parsedAlbums);
  }
}
