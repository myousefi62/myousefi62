<?php
// DIC configuration

//get all container items
$container = $app->getContainer();

// Inject a new instance of PDO in the container
$container['dbh'] = function($container) {
    $config = $container->get('settings')['pdo'];
    $dsn = "{$config['engine']}:host={$config['host']};dbname={$config['database']};charset={$config['charset']}";
    $username = $config['username'];
    $password = $config['password'];

    return new \Slim\PDO\Database($dsn, $username, $password, $config['options']);

};
//mail config
$container['mailer'] = function ($container) {
	$mailer = new PHPMailer;

	$mailer->Host = 'smtp.gmail.com';           // your email host, to test I use localhost and check emails using test mail server application (catches all  sent mails)
	$mailer->SMTPAuth = true;                  // I set false for localhost
	$mailer->SMTPSecure = 'ssl';               // set blank for localhost
	$mailer->Port = 465;                        // 25 for local host
	$mailer->Username = 'myousefi62@gmail.com';    // I set sender email in my mailer call
	$mailer->Password = 'fslhggi26315592@*!';
	$mailer->isHTML(true);

	return new \App\Mail\Mailer($container->view, $mailer);
};

//sub domain start
$container['SubDomain']="";
$domain =  $container->get('settings')['domain'];
//echo $domain;
$url = $_SERVER['HTTP_HOST'] ;
//echo $url;
$url = str_replace($domain,"",$url);
//echo $url;
$host = explode('.', $url);
//print_r($host);
//echo count($host) ;
if ((count($host)-1) == 1) {
    $container['SubDomain'] = $host[0];  
    //echo $url;
}
// $container['SubDomain'] = 'sub'; 
//echo $url; 
//$parsedUrl = parse_url($url);
//print_r( $parsedUrl);
//echo $parsedUrl['path'];
//echo count($host)-1 ;
//print_r($host); 
// sub domain End

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};
//ادرس پوشه اپلود فایلها
$container['upload_Address'] ='files';
//پوشه اپلود 
$container['upload_directory'] ='../public/files';
//پوشه اپلود تصویر
$container['upload_directory_img'] ='../public/img';
//پوشه اپلود اواتار
$container['upload_directory_avatar'] ='../public/img/avatar';
// دامنه سایت 
$container['domain'] ='slimshop.ir';