<?php

namespace App\Http\Traits;
use GuzzleHttp\Client;
use stdClass;

trait SpotifyTrait {

  protected $client, $appToken ;

  public function __construct() {
    $this->client = new Client();
  }

  /**
   * Returns App token for authenticated API calls
   *
   * @return string|null
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  protected function getAppToken() {
    if(!$this->appToken) {
      $this->appToken = $this->appLogin();
    }
    return $this->appToken;
  }

  /**
   * Login into Spotify and set appToken for future calls
   *
   * @return string|null
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  protected function appLogin() {
    // Generate base64 token
    $authToken = base64_encode(env('SPOTIFY_APP_ID') . ':' . env('SPOTIFY_APP_SECRET'));

    // Get the App's authorization token
    $authResponse = $this->client->post('https://accounts.spotify.com/api/token', [
      'headers'=> ['Authorization' => "Basic $authToken"],
      'form_params' => ['grant_type' => 'client_credentials'],
    ]);

    // Get App Token
    $responseBody = json_decode($authResponse->getBody()->getContents());
    return $responseBody->access_token;
  }

  /**
   * Returns Array with auth headers for API calls
   *
   * @return string[]
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  protected function getAuthHeaders() {
    return ['Authorization'=> 'Bearer '.$this->getAppToken()];
  }

  /**
   * Get albums by Artist
   *
   * @param $artist
   *
   * @param int $limit
   * @param int $offset
   *
   * @return array|null
   */
  public function getAlbumsByArtist($artist, $limit=20, $offset=0) {
    $results = $this->search($artist, 'artist', $limit, $offset);
    if(!$results || !count($results->artists->items))
      return null;

    $artistID = $results->artists->items[0]->id; // NOTE: Here we're taking the first artist from the results

    return $this->getArtistAlbums($artistID);
  }

  /**
   * Search Spotify API for specified query and type
   *
   * @param $query
   * @param string $type
   * @param int $limit
   * @param int $offset
   *
   * @return stdClass|null
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function search($query, $type='', $limit=20, $offset=0) {
    // Search the Spotify's API matching our query
    $searchResponse = $this->client->get('https://api.spotify.com/v1/search', [
      'headers' => $this->getAuthHeaders(),
      'query' => [
        'q' => $query,
        'type' => $type,
        'limit' => $limit,
        'offset' => $offset
      ],
    ]);
    return json_decode($searchResponse->getBody()->getContents());
  }

  /**
   * Get albums by artist ID
   *
   * @param $artistID
   * @param int $limit
   *
   * @return array|null
   */
  public function getArtistAlbums($artistID, $limit=50) {
    try {
      $artistResponse = $this->client->get("https://api.spotify.com/v1/artists/$artistID/albums", [
        'headers'=> $this->getAuthHeaders(),
        'query' => ['limit' => $limit, 'include_groups' => 'album'],
      ]);
      $responseBody = json_decode($artistResponse->getBody()->getContents());
      return $responseBody->items;
    } catch (\Throwable $th) {
      return null;
    }
  }

}