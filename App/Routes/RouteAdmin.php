<?php
use Slim\Http\Request;
use Slim\Http\Response;
// Routes
$app->get('/test3/{name}', function (Request $request, Response $response, array $args) {
    $name = $args['name'];
    $response->getBody()->write("test ok , $name");

    return $response;
});