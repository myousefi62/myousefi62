<?php
use Slim\Http\Request;
use Slim\Http\Response;
// Routes این فانکشن ها بوسیله کاربر اجرا شده و تنها با داشتن یک توکن فعال امکان اجرا را دارند
$app->post('/market/reg', function (Request $request, Response $response, array $args) {
  // دریافت دیتا از میدل وار جهت استفاده در طول در خواست
  $TokenData = $request->getAttribute("TokenData");
  // print_r($TokenData);
  $UserID = $TokenData['UserID'];
  // ارتباط با دیتا بیس
  $pdo = $this->get("dbh");
  //لیست خطا ها در پارامتر های ارسالی
  $ٍError;
  // فیلدهای مورد استفاده برای بروز رسانی
  //UserID,MarketName,Title,Adress,Latitude,Longitude,PhonNumber,BankAccount,bankID,IBAN,telegram_bot,Telegram_chanel,Description 
  //نام کاربری فروشگاه
  //نام فروشگاه انتخابی باید برسی گردد که در دسترس می باشد یا خیر
  $MarketName = $request->getParsedBodyParam('MarketName', $default = null);
  // echo AvailableUserName($UserName,$pdo);
  //در صورتیکه مقدار نام کاربری ارسال شده باشد و قابل استفاده نباشد مقدار ارسالی برابر با نال در نظر گرفته می شود
  //نام کاربری غیر قابل استفاده نام کاربری است که از کاراکتر های غیر استاندارد یا قبلا مورد استفاده قرار گرفته باشد
    if($UserName != null &&  !AvailableAccount($MarketName,$UserID,$pdo))
  {
    $MarketName = null;
    $ٍError['MarketName'] = " نام کاربری در دسترس نمی باشد.";
  }
  //عنوان فروشگاه
  $Title = $request->getParsedBodyParam('Title', $default = null);
  //ادرس فروشگاه
  $Adress = $request->getParsedBodyParam('Adress', $default = null);
   //طول جعرافیایی
   $Latitude = $request->getParsedBodyParam('Latitude', $default = null);
    //عرض جعرافیایی
  $Longitude = $request->getParsedBodyParam('Longitude', $default = null);
   //تلفن تماس فروشگاه
   $PhonNumber = $request->getParsedBodyParam('PhonNumber', $default = null);
    //شماره حساب
  $BankAccount = $request->getParsedBodyParam('BankAccount', $default = null);
   //شناسه بانک
   $BankID = $request->getParsedBodyParam('BankID', $default = null);
    //شبای حساب بانکی
  $IBAN = $request->getParsedBodyParam('IBAN', $default = null);
   //ربات تلگرامی فروشگاه
   $Telegram_bot = $request->getParsedBodyParam('Telegram_bot', $default = null);
    //کانال تلگرامی فروشگاه
  $Telegram_chanel = $request->getParsedBodyParam('Telegram_chanel', $default = null);
  //تگهای فروشگاه
  $Tags = $request->getParsedBodyParam('Tags', $default = null); 
  //توضیحات فروشگاه
   $Description = $request->getParsedBodyParam('Description', $default = null);
   
  
  //پارامتر های خالص شده جهت ثبت در دیتا بیس
  $result ;
  $result['MarketName']=$MarketName;
  $result['Title']=$Title;
  $result['Adress']=$Adress;
  $result['Latitude']=$Latitude;
  $result['Longitude']=$Longitude;
  $result['PhonNumber']=$PhonNumber;
  $result['BankAccount']=$BankAccount;
  $result['BankID']=$BankID;
  $result['IBAN']=$IBAN;
  $result['Telegram_bot']=$Telegram_bot;
  $result['Telegram_chanel']=$Telegram_chanel;
  $result['Tags']=$Tags;
  $result['Description']=$Description;

  //print_r($result);

  if(1==1){
    $query = $pdo->prepare("INSERT INTO `tbmarket`  (`UserID`, `MarketName`, `Title`, `Adress`, `Latitude`, `Longitude`, `PhonNumber`, `BankAccount`, `BankID`, `IBAN`, `Telegram_bot`, `Telegram_chanel`, `Tags`,`Description`) 
                                                  VALUES 
                                                            (:UserID,   :MarketName, :Title , :Adress , :Latitude , :Longitude , :PhonNumber , :BankAccount ,  :BankID , :IBAN , :Telegram_bot , :Telegram_chanel ,:Tags, :Description); ");
    $query->bindParam(':UserID'	,$UserID);
    $query->bindParam(':MarketName'	,$MarketName);
    $query->bindParam(':Title'	,$Title);
    $query->bindParam(':Adress'	,$Adress);
    $query->bindParam(':Latitude'	,$Latitude);
    $query->bindParam(':Longitude'	,$Longitude);
    $query->bindParam(':PhonNumber'	,$PhonNumber);
    $query->bindParam(':BankAccount'	,$BankAccount);
    $query->bindParam(':BankID'	,$BankID);
    $query->bindParam(':IBAN'	,$IBAN);
    $query->bindParam(':Telegram_bot'	,$Telegram_bot);
    $query->bindParam(':Telegram_chanel'	,$Telegram_chanel);
    $query->bindParam(':Tags'	,$Tags);
    $query->bindParam(':Description'	,$Description);
    if ( $query->execute()){
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
$app->post('/market/avatar', function (Request $request, Response $response, array $args) {
  
  // print_r($request);
  // دریافت دیتا از میدل وار جهت استفاده در طول در خواست
  $TokenData = $request->getAttribute("TokenData");
  $UserID = $TokenData['UserID'];
  $MarketID = $request->getParsedBodyParam('MarketID', $default = null);
  //echo "MarketID $MarketID";
  //print_r($TokenData);
    $pdo = $this->get("dbh");


    $Query = $pdo->prepare("SELECT count(*) count FROM `tbmarket`where MarketID = '$MarketID'  and UserID = $UserID"); 
    $Query->execute(); 
    $rowmarket = $Query->fetch();
    if ($rowmarket["count"] == 0){
        //یا فروشگاه یافت نشده یا این فروشگاه در دسترس کاربر نمی باشد
        $error['Title'] = "عملیات نا موفق";
        $error['Body'] ="عدم دسترسی به تغییر اواتار فروشگاه"  ;
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json')->write(json_encode(ApiResult(null,$error,0)));
    }


    $directory = $this->get('upload_directory');
    $uploadedFile = $request->getUploadedFiles()['avatar'];
    $result ;
      if($uploadedFile->getError() === UPLOAD_ERR_OK) {
            $newResponse;
            $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
            //$basename = bin2hex(random_bytes(8));
            $basename = "photo_".$TokenData["UserID"]."_".date("Y-m-d_H-i-s");  
            $filename = sprintf('%s.%0.8s', $basename, $extension);
            $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);
          //بروز رسانی اواتار کاربر
            $Update_Avatar = $pdo->prepare("UPDATE `tbmarket` SET `Avatar` = :Avatar WHERE `tbmarket`.`UserID` = :UserID and `tbmarket`.`MarketID` = :MarketID ; ");
            $Update_Avatar->bindParam(':Avatar'	,$filename);
            $Update_Avatar->bindParam(':UserID'	,$UserID);
            $Update_Avatar->bindParam(':MarketID'	,$MarketID);
            
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

      }else {
        $error['Title'] = "عملیات نا موفق";
        $error['Body'] ="خطا در ارسال تصویر"  ;
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json')->write(json_encode(ApiResult(null,$error,0)));
      }
  })->add($validToken);//->add($mw2);
$app->post('/market/profile', function (Request $request, Response $response, array $args) {
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

$app->get('/market/testfun2', function (Request $request, Response $response, array $args) {
   //ectr111();
  return;
  });
$app->group('/market', function () use ($app) {
  $app->post('/reg2', function ( $request, Response $response, array $args) {
    // دریافت دیتا از میدل وار جهت استفاده در طول در خواست
    $TokenData = $request->getAttribute("TokenData");
    // print_r($TokenData);
    $UserID = $TokenData['UserID'];
    // ارتباط با دیتا بیس
    $pdo = $this->get("dbh");
    //لیست خطا ها در پارامتر های ارسالی
    $ٍError;
    // فیلدهای مورد استفاده برای بروز رسانی
    //UserID,MarketName,Title,Adress,Latitude,Longitude,PhonNumber,BankAccount,bankID,IBAN,telegram_bot,Telegram_chanel,Description 
    //نام کاربری فروشگاه
    //نام فروشگاه انتخابی باید برسی گردد که در دسترس می باشد یا خیر
    $MarketName = $request->getParsedBodyParam('MarketName', $default = null);
    // echo AvailableUserName($UserName,$pdo);
    //در صورتیکه مقدار نام کاربری ارسال شده باشد و قابل استفاده نباشد مقدار ارسالی برابر با نال در نظر گرفته می شود
    //نام کاربری غیر قابل استفاده نام کاربری است که از کاراکتر های غیر استاندارد یا قبلا مورد استفاده قرار گرفته باشد
      if($UserName != null &&  !AvailableAccount($MarketName,$UserID,$pdo))
    {
      $MarketName = null;
      $ٍError['MarketName'] = " نام کاربری در دسترس نمی باشد.";
    }
    //عنوان فروشگاه
    $Title = $request->getParsedBodyParam('Title', $default = null);
    //ادرس فروشگاه
    $Adress = $request->getParsedBodyParam('Adress', $default = null);
     //طول جعرافیایی
     $Latitude = $request->getParsedBodyParam('Latitude', $default = null);
      //عرض جعرافیایی
    $Longitude = $request->getParsedBodyParam('Longitude', $default = null);
     //تلفن تماس فروشگاه
     $PhonNumber = $request->getParsedBodyParam('PhonNumber', $default = null);
      //شماره حساب
    $BankAccount = $request->getParsedBodyParam('BankAccount', $default = null);
     //شناسه بانک
     $BankID = $request->getParsedBodyParam('BankID', $default = null);
      //شبای حساب بانکی
    $IBAN = $request->getParsedBodyParam('IBAN', $default = null);
     //ربات تلگرامی فروشگاه
     $Telegram_bot = $request->getParsedBodyParam('Telegram_bot', $default = null);
      //کانال تلگرامی فروشگاه
    $Telegram_chanel = $request->getParsedBodyParam('Telegram_chanel', $default = null);
    //تگهای فروشگاه
    $Tags = $request->getParsedBodyParam('Tags', $default = null); 
    //توضیحات فروشگاه
     $Description = $request->getParsedBodyParam('Description', $default = null);
     
    
    //پارامتر های خالص شده جهت ثبت در دیتا بیس
    $result ;
    $result['MarketName']=$MarketName;
    $result['Title']=$Title;
    $result['Adress']=$Adress;
    $result['Latitude']=$Latitude;
    $result['Longitude']=$Longitude;
    $result['PhonNumber']=$PhonNumber;
    $result['BankAccount']=$BankAccount;
    $result['BankID']=$BankID;
    $result['IBAN']=$IBAN;
    $result['Telegram_bot']=$Telegram_bot;
    $result['Telegram_chanel']=$Telegram_chanel;
    $result['Tags']=$Tags;
    $result['Description']=$Description;
  
    //print_r($result);
  
    if(1==1){
      $query = $pdo->prepare("INSERT INTO `tbmarket`  (`UserID`, `MarketName`, `Title`, `Adress`, `Latitude`, `Longitude`, `PhonNumber`, `BankAccount`, `BankID`, `IBAN`, `Telegram_bot`, `Telegram_chanel`, `Tags`,`Description`) 
                                                    VALUES 
                                                              (:UserID,   :MarketName, :Title , :Adress , :Latitude , :Longitude , :PhonNumber , :BankAccount ,  :BankID , :IBAN , :Telegram_bot , :Telegram_chanel ,:Tags, :Description); ");
      $query->bindParam(':UserID'	,$UserID);
      $query->bindParam(':MarketName'	,$MarketName);
      $query->bindParam(':Title'	,$Title);
      $query->bindParam(':Adress'	,$Adress);
      $query->bindParam(':Latitude'	,$Latitude);
      $query->bindParam(':Longitude'	,$Longitude);
      $query->bindParam(':PhonNumber'	,$PhonNumber);
      $query->bindParam(':BankAccount'	,$BankAccount);
      $query->bindParam(':BankID'	,$BankID);
      $query->bindParam(':IBAN'	,$IBAN);
      $query->bindParam(':Telegram_bot'	,$Telegram_bot);
      $query->bindParam(':Telegram_chanel'	,$Telegram_chanel);
      $query->bindParam(':Tags'	,$Tags);
      $query->bindParam(':Description'	,$Description);
      if ( $query->execute()){
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
    
   })->add($mw2);
  $app->get('/date', function ($request, $response) {

      return $response->getBody()->write(date('Y-m-d H:i:s'));
  });
  $app->get('/time', function ($request, $response) {

      return $response->getBody()->write(time());
  });
  })->add($validToken);