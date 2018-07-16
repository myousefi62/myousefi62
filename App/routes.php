<?php

use Slim\Http\Request;
use Slim\Http\Response;
date_default_timezone_set('UTC');
//////////////start Add Allow-Origin
$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
 });
$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization , Token')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
 });
////////////////// end Allow-Origin
//ای پی ای های موجود در این روت نیاز به توکن داییمی ندارد 
require __DIR__. '/Routes/RouteQoust.php';
// در این روت فیاز به فقط یک توکن فعال می باشد
// در این روت ای پی ای هایی قرار میگیرد که به شکل عممومی مورد استفاده قرار می گیرند 
// به عبارت بهتر ای پی ای های پابلیک در این روت قرار میگیرند
require __DIR__. '/Routes/RoutePublic.php';
// این روت نیاز به توکن فعال دارد و تمام اعمال بر اساس توکن و یوزری که یه این توکن تعلق دارد انجام می شود
// در این روت تمام ای پی ای های با محوریت کاربر قرار میگیرند
 require __DIR__. '/Routes/RouteUser.php';
//تمام ای پی ای هایی که به فروشگاه ارتباط پیدا میکند در این روت قرار می گیرد
 require __DIR__. '/Routes/RouteMarket.php';
 // هنوز تصمصمی گرفته نشده است
 require __DIR__. '/Routes/RouteAdmin.php';
 // هنوز تصمصمی گرفته نشده است
 require __DIR__. '/Routes/RouteSuperAdmin.php';


// Catch-all route to serve a 404 Not Found page if none of the routes match
// NOTE: make sure this route is defined last
$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
    $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
    return $handler($req, $res);
 });
