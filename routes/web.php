<?php

use \Illuminate\Http\Request;
use App\Http\Controllers\ProductsController;

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('/products', function (Request $request) use ($router) {
    //CONTROLLER
    $controller = new ProductsController($request);

    //RESPONSE
    return $response = $controller->getProducts();
});
