<?php
use Slim\Http\Request;
use Slim\Http\Response;
// Routes
$app->get('/yyy/[{name}]', function (Request $request, Response $response, array $args) {
    // Sample log message
    print_r($this->get("SubDomain"));
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});

$app->get('/mot', function (Request $request, Response $response, array $args) {
    
$dbhandler = $this->get("dbh");

      $queryUsers = $dbhandler->prepare(" SELECT * FROM `tbuser` ORDER BY `password` ASC  ");
     $queryUsers->execute();
     $users = $queryUsers->fetchAll();
     print_r($this->get("SubDomain"));
        //$users  = json_encode($users);
        //print_r($users);
        //$response->getBody()->write(json_encode($users));
        //    return $response;
        // $newResponse = $response->withStatus(400)->withJson([
        //     'error'=>'Unknown Entity',
        //     'code'=>404
        // ]);
        
    $newResponse = $response->withStatus(200)->withJson($users);
   return $newResponse;
});
$app->get('/sendmail', function (Request $request, Response $response, array $args) {
    
//     $meailhandler = $this->get("mailer");
//     //$meailhandler->send('template/directory/template-name.twig', ['data' => $data, 'moreData' => $moreData] , function($message) use ($user){
//         $meailhandler->send('template/directory/template-name.twig', ['data' => $data, 'moreData' => $moreData] , function($message) use ($user){
//         $message->to('myousefi62@gmail.com');
//         $message->subject('test Emailer');
//         $message->from('myousefi62@gmail.com'); // if you want different sender email in mailer call function
//         $message->fromName('mohammad'); // if you want different sender name in mailer call function
//   });
$UserName="UserName";
echo " SELECT count(1) count FROM `tbuser` where UserName = '".$UserName."' ";
    //     $newResponse = $response->withStatus(200)->withJson(null);
    //    return $newResponse;
    });
    