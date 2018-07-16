<?php
// Application middleware
//TODO
// e.g: $app->add(new \Slim\Csrf\Guard);
// $pdo = $app->getContainer()->settings['pdo'];
// class ExampleMiddleware
// {
//     /**
//      * Example middleware invokable class
//      *
//      * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
//      * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
//      * @param  callable                                 $next     Next middleware
//      *
//      * @return \Psr\Http\Message\ResponseInterface
//      */
//     // private $pdo = $app->getContainer()->settings['pdo'];
//     public function __invoke($request, $response, $next)
//     {
//         GetAppData();
//         echo $settings;
//         //$container = $app->getContainer();
//         //return $response;
//         $Token = $request->getHeader('token')[0];
//         // $pdo  = function($container) {
//         //     $config = $container->get('settings')['pdo'];
//         //     $dsn = "{$config['engine']}:host={$config['host']};dbname={$config['database']};charset={$config['charset']}";
//         //     $username = $config['username'];
//         //     $password = $config['password'];
        
//         //     return new \Slim\PDO\Database($dsn, $username, $password, $config['options']);
        
//         // };
//         // $stmt = $pdo->prepare("SELECT *    FROM tbuser "); 
//         // $stmt->execute(); 
//         // $row = $stmt->fetchAll();
//         // print_r($row);
//         // print_r($pdo);
//         // $pdo->beginTransaction();
//          //ای پی کاربر
//          $IP = GetClientIP();
//         //برسی فعال بودن و در دسترسی بودن توکن
//         //$TokenData =  VerifyToken($Token,"tmp",$pdo);



//         ECHO $Token;
//         $response->getBody()->write('BEFORE');
//        // print_r($request);
//         $response = $next($request, $response);
//         $response->getBody()->write('AFTER');

//         return $response;
//     }

// // }
// print_r($app->getContainer()->settings['pdo']);
//دیتای مربوط به ارتباط با دیتابیس از این طریق استخراج می گردد 
$pdoD = $app->getContainer()->settings['pdo'];

$validToken = function ($request, $response, $next   ) use($pdoD) {
    // کانکشن ایجاد با نتطیمات مورد نطر ایجاد شده و از این پس می توان جهت ارتباط با دیتا بیس از این استفاده کرد
    // این اطلاعات باید ار ریکوست در یافت گردد این روش غیر استاندارد می باشد
    $config = $pdoD;
    $dsn = "{$config['engine']}:host={$config['host']};dbname={$config['database']};charset={$config['charset']}";
    $username = $config['username'];
    $password = $config['password'];
    $pdo = new \Slim\PDO\Database($dsn, $username, $password, $config['options']);

    // $Token = $mysql_escape_string($request->getHeader('token')[0]);
    $Token = $request->getHeader('token')[0];
    // دریافت اطلاعات بر اساس توکن ارسالی 
    //قبل از اجرای اکشن این بخش اچرا می گردد 
    $TokenData =  VerifyToken($Token,"continuous",$pdo);
   
    // در صورتی که کاربیر با توکن فعال وارد شده باشد اطلاعات کاربر فراخوانی می شود
    if($TokenData['Verify']){
        try{
           
          //  $response->getBody()->write('BEFORE');
            
            //print_r($request->getserverParams());
            // فانکشن فراخوانی شده 
            $function = $request->getserverParams()["PATH_INFO"];
            // TODO
            //TODO sdfjslkjdfls
            
            // افزودن ارگومان به ریکوست
            $response = $next($request->withAttribute("TokenData",$TokenData), $response);   
            //$response = $next($request, $response);
          //  print_r ($TokenData);
        
          //  $response->getBody()->write('AFTER');
        
            // $route = $request->getAttribute('route');
            // print_r( $request->getParsedBodyParam());
            //print_r($request->getAttribute('callable'));
            // print_r($request->getRequestTarget());
            // print_r($request->getUri());
            // $Uri = $request->getUri();
            // echo "\n".$request->getUri('path');
            // echo "\n".$request->getUri();
            // print_r($Uri);
            //echo $Uri['path'];
            }
        catch(Exception $e){

            }
    }else{
        $error['Title'] = "عملیات نا موفق";
        $error['Body'] ="توکن نا معتبر"  ;
        $newResponse = $response->withStatus(400)->withJson(ApiResult(null,$error,-1));
        return  $newResponse;
    }
    // $stmt = $pdo->prepare("SELECT *    FROM tbuser "); 
    // $stmt->execute(); 
    // $row = $stmt->fetchAll();
    // print_r($row);
    
    // $route = $request->getAttribute('route');
    // $routeName = $route->getName();
    // $groups = $route->getGroups();
    // $methods = $route->getMethods();
    // $arguments = $route->getArguments();
//     $response->getBody()->write('AFTER');
//   print_r($response);
  //$response->getBody();
//  print_r (ApiResult($TokenData,null,1));
  //echo json_encode(ApiResult($TokenData,null,1));
 // $response->withJson(json_encode(ApiResult($TokenData,null,1)),  400, 0);
//  $body = $response->getBody();
//  echo $body;

//  return $response->withStatus(200)
//         ->withHeader('Content-Type', 'application/json')
//         ->write(json_encode(ApiResult($TokenData,null,1)));



  //$response->getBody()->write(json_encode(ApiResult($TokenData,null,1)));
    return $response;
};
$mw2 = function ($request, $response, $next) {
    //return $response;
    $response->getBody()->write('BEFdddORE');
    $response = $next($request, $response);
    $response->getBody()->write('AFTdddER');

    return $response;
};