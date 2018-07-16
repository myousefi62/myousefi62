<?php
use Slim\Http\Request;
use Slim\Http\Response;
// Routes این فانکشن ها بوسیله کاربر اجرا شده و تنها با داشتن یک توکن فعال امکان اجرا را دارند
$app->post('/user/upload_avatar', function (Request $request, Response $response, array $args) {
    
  // print_r($request);
  // دریافت دیتا از میدل وار جهت استفاده در طول در خواست
  $TokenData = $request->getAttribute("TokenData");

  //print_r($TokenData);
    $pdo = $this->get("dbh");
    // $stmt = $pdo->prepare("SELECT *    FROM tbuser "); 
    // $stmt->execute(); 
    // $row = $stmt->fetchAll();
    // print_r($row);

    $directory = $this->get('upload_directory');
    $uploadedFile = $request->getUploadedFiles()['avatar'];
  // $uploadedFiles = $request->getUploadedFiles();
    //$uploadedFile = $uploadedFiles['avatar'];
    // print_r($uploadedFiles);
    //print_r ($uploadedFile);
    //echo $this->get('upload_directory')."img";
  //echo date("Y-m-d_H-i-s");  
    //echo uniqid();
    // echo md5(uniqid(mt_rand(), true).time());
    // echo time() . substr(md5(uniqid(mt_rand(), true)), 0);
        //$filename = moveUploadedFile($this->get('upload_directory')."img", $request->getUploadedFiles());
    $result ;
      if($uploadedFile->getError() === UPLOAD_ERR_OK) {
            $newResponse;
            $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
            //$basename = bin2hex(random_bytes(8));
            $basename = "photo_".$TokenData["UserID"]."_".date("Y-m-d_H-i-s");  
            $filename = sprintf('%s.%0.8s', $basename, $extension);
            $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);
            $UserID = $TokenData['UserID'];
          //بروز رسانی اواتار کاربر
            $Update_Avatar = $pdo->prepare("UPDATE `tbuser` SET `Avatar` = :Avatar WHERE `tbuser`.`UserID` = :UserID; ");
            $Update_Avatar->bindParam(':UserID'	,$UserID);
            $Update_Avatar->bindParam(':Avatar'	,$filename);

            if ( $Update_Avatar->execute()){
              //print_r($request->getserverParams());// $request->getserverParams()["HTTP_HOST"];
              $REQUEST_SCHEME = "http";
              if ($request->getserverParams()["REQUEST_SCHEME"] != null)
                $REQUEST_SCHEME = $request->getserverParams()["REQUEST_SCHEME"];
              
              $result['urlAvatar'] = $REQUEST_SCHEME.":". DIRECTORY_SEPARATOR. DIRECTORY_SEPARATOR. $request->getserverParams()["HTTP_HOST"]. DIRECTORY_SEPARATOR.$this->get('upload_Address') . DIRECTORY_SEPARATOR . $filename;
              
              return $response->withStatus(200)->withHeader('Content-Type', 'application/json')->write(json_encode(ApiResult($result,null,1)));
            } else {
              $error['Title'] = "عملیات نا موفق";
              $error['Body'] ="خطا در ارسال تصویر"  ;
              return $response->withStatus(400)->withHeader('Content-Type', 'application/json')->write(json_encode(ApiResult(null,$error,0)));
            }
          
            // $stmt = $pdo->prepare("UPDATE `tbuser` SET `avatar` = '64564646' WHERE `tbuser`.`UserID` = 65; "); 
            // $stmt->execute(); 
            // $row = $stmt->fetchAll();
            // print_r($row);
            
            //($directory . DIRECTORY_SEPARATOR . $filename);
            //$filename = moveUploadedFile($directory, $uploadedFile);
          //  $response->write('uploaded ' . $directory . DIRECTORY_SEPARATOR . $filename. '<br/>');
          // $result['urlAvatar'] = $directory . DIRECTORY_SEPARATOR . $filename;
            //$response= $response->withStatus(400)->withJson(ApiResult($resut,null,1));
      }else {
        $error['Title'] = "عملیات نا موفق";
        $error['Body'] ="خطا در ارسال تصویر"  ;
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json')->write(json_encode(ApiResult(null,$error,0)));
      }
    // print_r($result);
      //$response->write('uploaded ' . $directory . DIRECTORY_SEPARATOR . $filename. '/n');
      //return $response->withStatus(200)->withHeader('Content-Type', 'application/json')->write(json_encode(ApiResult($result,null,1)));
      //$response->withJson(ApiResult($result,null,1),  400, 0);
    //  $response->withStatus(400)->withJson(ApiResult($resut,null,1));
    //return $newResponse;
  })->add($validToken);//->add($mw2);
$app->post('/user/update_profile', function (Request $request, Response $response, array $args) {
  // دریافت دیتا از میدل وار جهت استفاده در طول در خواست
  $TokenData = $request->getAttribute("TokenData");
  $UserID = $TokenData['UserID'];
  // ارتباط با دیتا بیس
  $pdo = $this->get("dbh");
  //لیست خطا ها در پارامتر های ارسالی
  $ٍError;
  // فیلدهای مورد استفاده برای بروز رسانی
  $FirstName = $request->getParsedBodyParam('FirstName', $default = null);
  $LastName = $request->getParsedBodyParam('LastName', $default = null);
  //نام کاربری انتخابی باید برسی گردد که در دسترس می باشد یا خیر
  $UserName = $request->getParsedBodyParam('UserName', $default = null);
  // echo AvailableUserName($UserName,$pdo);
  //در صورتیکه مقدار نام کاربری ارسال شده باشد و قابل استفاده نباشد مقدار ارسالی برابر با نال در نظر گرفته می شود
  //نام کاربری غیر قابل استفاده نام کاربری است که از کاراکتر های غیر استاندارد یا قبلا مورد استفاده قرار گرفته باشد
  
  if($UserName != null &&  !AvailableUserName($UserName,$UserID,$pdo))
  {
    $UserName = null;
    $ٍError['UserName'] = " نام کاربری در دسترس نمی باشد.";
  }
  //باید برسی گردد این پست الکترونیکی در دسترس می باشد یا خیر 
  $Email = $request->getParsedBodyParam('Email', $default = null);
  // echo AvailableEmail($Email,$pdo);
  // در صورتی که پست الکترونیکی غیر استاندارد و یا قبلا مورد استفاده قرار گرفته باشد مقدار این فیلد نال در نظر گرفته می شود
  if($Email != null &&  !AvailableEmail($Email,$UserID,$pdo)){
    $Email = null;
    $ٍError['Email']="پست الکترونیکی ارسالی در دسترس نمی باشد";
  }
  // فردی با این کد ارجاع در دسترس می باشد یا خیر 
  $ReagentID = $request->getParsedBodyParam('ReagentID', $default = null);
  // echo AvailableReagentID($ReagentID,$TokenData['UserID'],$pdo);
  if($ReagentID != null){
    $ReagentID = AvailableReagentID($ReagentID,$UserID,$pdo);
    //$Error['ReagentID']="شخصی با این شماره اجاع یافت نشد";
  }
  //پارامتر های خالص شده جهت ثبت در دیتا بیس
  $result ;
  $result['FirstName']=$FirstName;
  $result['LastName']=$LastName;
  $result['UserName']=$UserName;
  $result['Email']=$Email;
  $result['ReagentID']=$ReagentID;
  //print_r($result);

  if(1==1){
    $Update_Avatar = $pdo->prepare("UPDATE `tbuser` SET 
                                                      `FirstName` = :FirstName 
                                                      ,`LastName` = :LastName 
                                                      ,`UserName` = :UserName 
                                                      ,`Email`    = :Email 
                                                      ,`ReagentID`= :ReagentID 
                                    WHERE `tbuser`.`UserID` = :UserID; ");
    $Update_Avatar->bindParam(':UserID'	,$UserID);
    $Update_Avatar->bindParam(':FirstName'	,$FirstName);
    $Update_Avatar->bindParam(':LastName'	,$LastName);
    $Update_Avatar->bindParam(':UserName'	,$UserName);
    $Update_Avatar->bindParam(':Email'	,$Email);
    $Update_Avatar->bindParam(':ReagentID'	,$ReagentID);
    if ( $Update_Avatar->execute()){
      //$result['urlAvatar'] = $request->getserverParams()["REQUEST_SCHEME"].":". DIRECTORY_SEPARATOR. DIRECTORY_SEPARATOR. $request->getserverParams()["HTTP_HOST"]. DIRECTORY_SEPARATOR.$this->get('upload_Address') . DIRECTORY_SEPARATOR ;
      return $response->withStatus(200)->withHeader('Content-Type', 'application/json')->write(json_encode(ApiResult($result,null,1)));
    } else {
      $error['Title'] = "عملیات نا موفق";
      $error['Body'] ="خطا در ارسال تصویر"  ;
      return $response->withStatus(400)->withHeader('Content-Type', 'application/json')->write(json_encode(ApiResult(null,$error,0)));
    }

  }else{

    $error['Title'] = "عملیات نا موفق";
    $error['Body'] ="خطا در ارسال تصویر"  ;
    return $response->withStatus(400)->withHeader('Content-Type', 'application/json')->write(json_encode(ApiResult(null,$error,0)));
  }
 
  })->add($validToken);//->add($mw2);
// $app->get('/yyy/[{name}]', function (Request $request, Response $response, array $args) {
//     // Sample log message
//     print_r($this->get("SubDomain"));
//     $this->logger->info("Slim-Skeleton '/' route");
$app->get('/user/profile', function (Request $request, Response $response, array $args) {
    // دریافت دیتا از میدل وار جهت استفاده در طول در خواست
    $TokenData = $request->getAttribute("TokenData");
    $UserID = $TokenData['UserID'];
    // ارتباط با دیتا بیس
    $pdo = $this->get("dbh");
    //لیست خطا ها در پارامتر های ارسالی
    $ٍError;
    // فیلدهای مورد استفاده برای بروز رسانی
    $FirstName = $request->getParsedBodyParam('FirstName', $default = null);
    $LastName = $request->getParsedBodyParam('LastName', $default = null);
    //نام کاربری انتخابی باید برسی گردد که در دسترس می باشد یا خیر
    $UserName = $request->getParsedBodyParam('UserName', $default = null);
    // echo AvailableUserName($UserName,$pdo);
    //در صورتیکه مقدار نام کاربری ارسال شده باشد و قابل استفاده نباشد مقدار ارسالی برابر با نال در نظر گرفته می شود
    //نام کاربری غیر قابل استفاده نام کاربری است که از کاراکتر های غیر استاندارد یا قبلا مورد استفاده قرار گرفته باشد

    if($UserName != null &&  !AvailableUserName($UserName,$UserID,$pdo))
    {
        $UserName = null;
        $ٍError['UserName'] = " نام کاربری در دسترس نمی باشد.";
    }
    //باید برسی گردد این پست الکترونیکی در دسترس می باشد یا خیر
    $Email = $request->getParsedBodyParam('Email', $default = null);
    // echo AvailableEmail($Email,$pdo);
    // در صورتی که پست الکترونیکی غیر استاندارد و یا قبلا مورد استفاده قرار گرفته باشد مقدار این فیلد نال در نظر گرفته می شود
    if($Email != null &&  !AvailableEmail($Email,$UserID,$pdo)){
        $Email = null;
        $ٍError['Email']="پست الکترونیکی ارسالی در دسترس نمی باشد";
    }
    // فردی با این کد ارجاع در دسترس می باشد یا خیر
    $ReagentID = $request->getParsedBodyParam('ReagentID', $default = null);
    // echo AvailableReagentID($ReagentID,$TokenData['UserID'],$pdo);
    if($ReagentID != null){
        $ReagentID = AvailableReagentID($ReagentID,$UserID,$pdo);
        //$Error['ReagentID']="شخصی با این شماره اجاع یافت نشد";
    }
    //پارامتر های خالص شده جهت ثبت در دیتا بیس
    $result ;
    $result['FirstName']=$FirstName;
    $result['LastName']=$LastName;
    $result['UserName']=$UserName;
    $result['Email']=$Email;
    $result['ReagentID']=$ReagentID;
    //print_r($result);

    if(1==1){
        $Update_Avatar = $pdo->prepare("UPDATE `tbuser` SET 
                                                      `FirstName` = :FirstName 
                                                      ,`LastName` = :LastName 
                                                      ,`UserName` = :UserName 
                                                      ,`Email`    = :Email 
                                                      ,`ReagentID`= :ReagentID 
                                    WHERE `tbuser`.`UserID` = :UserID; ");
        $Update_Avatar->bindParam(':UserID'	,$UserID);
        $Update_Avatar->bindParam(':FirstName'	,$FirstName);
        $Update_Avatar->bindParam(':LastName'	,$LastName);
        $Update_Avatar->bindParam(':UserName'	,$UserName);
        $Update_Avatar->bindParam(':Email'	,$Email);
        $Update_Avatar->bindParam(':ReagentID'	,$ReagentID);
        if ( $Update_Avatar->execute()){
            //$result['urlAvatar'] = $request->getserverParams()["REQUEST_SCHEME"].":". DIRECTORY_SEPARATOR. DIRECTORY_SEPARATOR. $request->getserverParams()["HTTP_HOST"]. DIRECTORY_SEPARATOR.$this->get('upload_Address') . DIRECTORY_SEPARATOR ;
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json')->write(json_encode(ApiResult($result,null,1)));
        } else {
            $error['Title'] = "عملیات نا موفق";
            $error['Body'] ="خطا در ارسال تصویر"  ;
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json')->write(json_encode(ApiResult(null,$error,0)));
        }

    }else{

        $error['Title'] = "عملیات نا موفق";
        $error['Body'] ="خطا در ارسال تصویر"  ;
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json')->write(json_encode(ApiResult(null,$error,0)));
    }

  })->add($validToken);
//آپلود فایل به شکل بیس ۶۴
  $app->post('/user/upload_avatar2', function (Request $request, Response $response, array $args) {
    
    // print_r($request);
    // دریافت دیتا از میدل وار جهت استفاده در طول در خواست
    $TokenData = $request->getAttribute("TokenData");
    //print_r($TokenData);
    
    $pdo = $this->get("dbh");  
    $directory = $this->get('upload_directory');
    // رشته بیس ۶۴ درسافت می شود 
    $FirstName = $request->getParsedBodyParam('avatar', $default = null);
      $result ;
      if($uploadedFile->getError() === UPLOAD_ERR_OK) {
              $newResponse;
              $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
              //$basename = bin2hex(random_bytes(8));
              $basename = "photo_".$TokenData["UserID"]."_".date("Y-m-d_H-i-s");  
              $filename = sprintf('%s.%0.8s', $basename, $extension);
              $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);
              $UserID = $TokenData['UserID'];
            //بروز رسانی اواتار کاربر
              $Update_Avatar = $pdo->prepare("UPDATE `tbuser` SET `Avatar` = :Avatar WHERE `tbuser`.`UserID` = :UserID; ");
              $Update_Avatar->bindParam(':UserID'	,$UserID);
              $Update_Avatar->bindParam(':Avatar'	,$filename);
  
              if ( $Update_Avatar->execute()){
                //print_r($request->getserverParams());// $request->getserverParams()["HTTP_HOST"];
                $REQUEST_SCHEME = "http";
                if ($request->getserverParams()["REQUEST_SCHEME"] != null)
                  $REQUEST_SCHEME = $request->getserverParams()["REQUEST_SCHEME"];
                
                $result['urlAvatar'] = $REQUEST_SCHEME.":". DIRECTORY_SEPARATOR. DIRECTORY_SEPARATOR. $request->getserverParams()["HTTP_HOST"]. DIRECTORY_SEPARATOR.$this->get('upload_Address') . DIRECTORY_SEPARATOR . $filename;
                
                return $response->withStatus(200)->withHeader('Content-Type', 'application/json')->write(json_encode(ApiResult($result,null,1)));
              } else {
                $error['Title'] = "عملیات نا موفق";
                $error['Body'] ="خطا در ارسال تصویر"  ;
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json')->write(json_encode(ApiResult(null,$error,0)));
              }
            
              // $stmt = $pdo->prepare("UPDATE `tbuser` SET `avatar` = '64564646' WHERE `tbuser`.`UserID` = 65; "); 
              // $stmt->execute(); 
              // $row = $stmt->fetchAll();
              // print_r($row);
              
              //($directory . DIRECTORY_SEPARATOR . $filename);
              //$filename = moveUploadedFile($directory, $uploadedFile);
            //  $response->write('uploaded ' . $directory . DIRECTORY_SEPARATOR . $filename. '<br/>');
            // $result['urlAvatar'] = $directory . DIRECTORY_SEPARATOR . $filename;
              //$response= $response->withStatus(400)->withJson(ApiResult($resut,null,1));
        }else {
          $error['Title'] = "عملیات نا موفق";
          $error['Body'] ="خطا در ارسال تصویر"  ;
          return $response->withStatus(400)->withHeader('Content-Type', 'application/json')->write(json_encode(ApiResult(null,$error,0)));
        }
      // print_r($result);
        //$response->write('uploaded ' . $directory . DIRECTORY_SEPARATOR . $filename. '/n');
        //return $response->withStatus(200)->withHeader('Content-Type', 'application/json')->write(json_encode(ApiResult($result,null,1)));
        //$response->withJson(ApiResult($result,null,1),  400, 0);
      //  $response->withStatus(400)->withJson(ApiResult($resut,null,1));
      //return $newResponse;
    })->add($validToken);//->add($mw2);
//     // Render index view
//     return $this->renderer->render($response, 'index.phtml', $args);
// });

// $app->get('/mot', function (Request $request, Response $response, array $args) {
    
// $dbhandler = $this->get("dbh");

//       $queryUsers = $dbhandler->prepare(" SELECT * FROM `tbuser` ORDER BY `password` ASC  ");
//      $queryUsers->execute();
//      $users = $queryUsers->fetchAll();
//      print_r($this->get("SubDomain"));
//         //$users  = json_encode($users);
//         //print_r($users);
//         //$response->getBody()->write(json_encode($users));
//         //    return $response;
//         // $newResponse = $response->withStatus(400)->withJson([
//         //     'error'=>'Unknown Entity',
//         //     'code'=>404
//         // ]);
        
//     $newResponse = $response->withStatus(200)->withJson($users);
//    return $newResponse;
// });
// $app->get('/sendmail', function (Request $request, Response $response, array $args) {
    
// //     $meailhandler = $this->get("mailer");
// //     //$meailhandler->send('template/directory/template-name.twig', ['data' => $data, 'moreData' => $moreData] , function($message) use ($user){
// //         $meailhandler->send('template/directory/template-name.twig', ['data' => $data, 'moreData' => $moreData] , function($message) use ($user){
// //         $message->to('myousefi62@gmail.com');
// //         $message->subject('test Emailer');
// //         $message->from('myousefi62@gmail.com'); // if you want different sender email in mailer call function
// //         $message->fromName('mohammad'); // if you want different sender name in mailer call function
// //   });
// $UserName="UserName";
// echo " SELECT count(1) count FROM `tbuser` where UserName = '".$UserName."' ";
//     //     $newResponse = $response->withStatus(200)->withJson(null);
//     //    return $newResponse;
//     });
    