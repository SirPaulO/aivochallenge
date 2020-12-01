<?php
/**
 * Disclaimer: the current setup is NOT recommended for final/production purposes.
 * It's just made as a simple demo challenge.
 */

require 'vendor/autoload.php';

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

$app = new App();

// API v1 group
$app->group('/api/v1', function () use ($app) {

  // Search Albums by Artist
  $app->get('/albums', function (Request $request, Response $response, array $args) {

    // Check for query param
    $query = $request->getQueryParam('q');
    if(!$query)
      return $response
        ->withStatus(400)
        ->withJson(['message'=>'The query (q) param is required.']);

    // Create a guzzle client
    $client = new Client();

    try {
      // Get the App's authorization token
      $authResponse = $client->post('https://accounts.spotify.com/api/token', [
        'headers'=> ['Authorization'=>'Basic Y2RhZTNjYzlmYzFmNDczNzgzOGNiYWU5YzE5M2JjNWQ6OTIzYmNkNzVhNDEwNDRjNmE4MTM1ZDQzZmUxZGMwMzI='],
        'form_params' => ['grant_type' => 'client_credentials'],
      ]);

      $responseBody = json_decode($authResponse->getBody()->getContents());
      $appToken = $responseBody->access_token;

      // Search the Spotify's API for artists matching our query
      $searchResponse = $client->get('https://api.spotify.com/v1/search', [
        'headers' => ['Authorization'=> "Bearer $appToken"],
        'query' => ['q' => $query, 'type' => 'artist'],
      ]);

      $responseBody = json_decode($searchResponse->getBody()->getContents());

      if(!count($responseBody->artists->items))
        return $response
          ->withStatus(400)
          ->withJson(['message'=>'There is no artists matching you search.']);

      $artistHref = $responseBody->artists->items[0]->href; // NOTE: Here we're taking the first artist from the results

      // Get artist albums
      $artistResponse = $client->get($artistHref.'/albums', [
        'headers'=> ['Authorization'=> "Bearer $appToken"],
        'query' => ['limit' => 50],
      ]);

      $responseBody = json_decode($artistResponse->getBody()->getContents());

      $albums = [];

      // Parse albums to match required output format
      foreach ($responseBody->items as $album) {
        $tmpAlbum = [
          'name' => $album->name,
          'released' => $album->release_date,
          'tracks' => $album->total_tracks,
          'cover' => $album->images[0] // Note: it looks like all responses are in the same order, it might not be always.
        ];
        $albums[] = $tmpAlbum;
      }

      $response = $response->withJson($albums);

    } catch (\Exception $e) {
      $response = $response
        ->withStatus(500)
        ->withJson(['message'=>'Oops! We have a problem, please try again later.']);
    }
    return $response;
  });

});

try {
  $app->run();
} catch (\Exception $e) {
  echo json_encode(['message'=>'Oops! We have a problem, please try again later.']);
}
