<?php
use Slim\Http\Request;
use Slim\Http\Response;
//ثبت نام و ورود به سیستم 
$app->post('/user/login', function (Request $request, Response $response, array $args) {

    $Mobile = $request->getParsedBodyParam('Mobile', $default = null);
    $ReagentID = $request->getParsedBodyParam('ReagentID', $default = null);

    //$result = array() ; 
   // شماره موبایل ولید می باشد یعنی فورمت شماره موبایل می باشد
    if (isset($Mobile) && ValidMobile($Mobile))
    {       
        $newResponse;
        //$dbhandler = $this->get("dbh");
        $pdo = $this->get("dbh");
        $pdo->beginTransaction();
        $IP = GetClientIP();
        $Code = "";
         
        try{
        
                 // این شماره موباید در سیستم ما وجود دارد
                $existent = ExistMobil($Mobile,$pdo);
                if ($existent == 1)
                {
                    //echo "existMobile : 1 IP : $IP";
                    // شناسه فرد مالک این شماره موبایل در یافت می گردد 
                    $stmtUserID = $pdo->prepare("SELECT UserID  FROM tbuser where  `Mobile` = '$Mobile'"); 
                    $stmtUserID->execute(); 
                    $row = $stmtUserID->fetch();
                    $UserID = $row ["UserID"];
                    // برسی می گردد این فرد در این بازه زمانی در خواست کد فعال سازی فعالی نداشته باشد
                    $ExistVerificationCode = ExistVerificationCode($UserID,$pdo);
                    // در صورتی که کد فعال سازی فعالی نداشته باشد اقدام به ارسال کد فعال سازی میکند 
                    if($ExistVerificationCode == 0){    
                        
                        date_default_timezone_set('UTC');
                        $Flag = 1;
                        // جهت تولید کد فعال یونیک از یک حلقه استفاده می کنیم تا به کد فعال یونیک برسیم
                        while ($Flag == 1) {
                            $timestamp=strtotime("now"); 
                            $DateExpire =  date('Y-m-d H:i:s', $timestamp);
                            $Code = getCode(5);
                            $stmtUserID = $pdo->prepare("SELECT count(1) Count FROM `tbmobileverified` WHERE VerificationCode = '$Code' and DateExpire > '$DateExpire' "); 
                            $stmtUserID->execute(); 
                            $row = $stmtUserID->fetch();
                            $count = $row ["Count"];
                            //echo $count ;
                            if ($Count == 0)
                                $Flag = 0;
                        }

                        //echo convertNumbers($Code);
                           // ثبت کد فعال سازی به همراه توکن مورد استفاده برای این کد 

                           $timestamp=strtotime("now");
                           $DateInsert =  date('Y-m-d H:i:s', $timestamp);
                           $timestamp=strtotime("+120 seconds");
                           $DateExpire =  date('Y-m-d H:i:s', $timestamp);
                           //$token = getToken(50);

                           $stmtlogin = $pdo->prepare("INSERT INTO tbmobileverified    ( `UserID`, `Mobile`,`IP` ,`VerificationCode`, `DateInsert`, `DateExpire`) 
                                                                                       VALUES 
                                                                                       ( :UserID, :Mobile, :IP, :VerificationCode, :DateInsert, :DateExpire)");
                           $stmtlogin->bindParam(':UserID'	,$UserID);
                           $stmtlogin->bindParam(':Mobile'	,$Mobile);
                           $stmtlogin->bindParam(':IP'	,$IP);
                           $stmtlogin->bindParam(':VerificationCode',$Code);
                           $stmtlogin->bindParam(':DateInsert'	,$DateInsert);
                           $stmtlogin->bindParam(':DateExpire'	,$DateExpire);
                           $stmtlogin->execute();

                           // // مدت اعتبار توکن ایجاد شده برابر با دو دقیقه می باشد
                           //$timestamp=strtotime("+60 Days");
                           $timestamp=strtotime("now");
                           $DateLogin =  date('Y-m-d H:i:s', $timestamp);
                           $timestamp=strtotime("+120 seconds");
                           $DateExpire =  date('Y-m-d H:i:s', $timestamp);
                           $token = getToken(50);
                           // کاربر جدید به سیستم لاگین می کند
                           $stmtlogin = $pdo->prepare("INSERT INTO tblogin
                                       (UserID, Token, IP,DateLogin,DateExpire) 
                                   VALUES 
                                       (:UserID,:token,:IP,:DateLogin,:DateExpire)");
                           $stmtlogin->bindParam(':UserID'	,$UserID);
                           $stmtlogin->bindParam(':token'	,$token);
                           $stmtlogin->bindParam(':IP'		,$IP);
                           $stmtlogin->bindParam(':DateLogin'	,$DateLogin);
                           $stmtlogin->bindParam(':DateExpire'	,$DateExpire);
                           $stmtlogin->execute();
                           
                           //$sendSms = SendSms ("100065995" ,$Code ,$Mobile);
                           //$Code = convertNumbers($Code);
                           //$message = "‏‏کد احراز هویت:$Code ‏‏با این کد می‌توانید به فروشگاه خود‌ وارد شوید.";
                           $sendSms = SendSms ("100065995" ,$Code ,$Mobile);
                           $result['Token'] =$token  ;
                           $result['FirstLogin'] = false;
                           $result['Code'] =convertNumbers($Code)  ;
                           //$result['Message'] =$message  ;
                           $newResponse = $response->withStatus(200)->withJson(ApiResult($result,null,1));

                        
                    }else {
                        $error['Title'] = "عملیات نا موفق";
                        $error['Body'] ="دو دقیقه دیگر مجددا تلاش کنید"  ;
                        $newResponse = $response->withStatus(400)->withJson(ApiResult(null,$error,0));
                        //echo "در خواست مکررد داشته اید زمان دیگر تلاش کنید";
                    }
                    //INSERT INTO `tbmobileverified` (`MobileVerifiedID`, `UserID`, `Mobile`, `VerificationCode`, `DateInsert`, `DateExpire`) VALUES ('', '11', '22', '33', '2018-03-07 00:00:00.000000', '2018-03-12 00:00:00.000000');
                }
                // اولین ورود این کار بر می باشد 
                // اکانتی برای این کاربر در سیتم ثبت می کنیم 
                else {
                       // echo "existMobile : 0 IP : $IP";

                        $stmt = $pdo->prepare	("INSERT INTO tbuser 
                                                                (`ReagentID`, `Mobile`)
                                                            VALUES 
                                                                ( :ReagentID, :Mobile)");
                            $stmt->bindParam(':ReagentID' 	    ,$ReagentID);
                            $stmt->bindParam(':Mobile'	        ,$Mobile);
                            
                            $stmt->execute();		
                            // شناسه کاربر جدید را بدست می اوریم
                            $stmtUserID = $pdo->prepare("SELECT UserID  FROM tbuser where Mobile = '$Mobile'"); 
                            $stmtUserID->execute(); 
                            $row = $stmtUserID->fetch();
                            $UserID = $row ["UserID"];
                            
                            ///اختصاص کد ارجاع به فرد 
                            ///TODO در حال حاضر شماسه فرد به عنوان شناسه ارجاع در نظر گرفته می شود و در اینده برا این بخش برنامه ریزی خواهد شد
                            $RefrencsID = $UserID+110;
                            $stmt = $pdo->prepare	("Update tbuser set RefrencsID = :UserID  where Mobile = :Mobile");
                            $stmt->bindParam(':UserID' 	    ,$RefrencsID);
                            $stmt->bindParam(':Mobile'	    ,$Mobile);
                            
                            $stmt->execute();	

                            echo $UserID;
                            //مقدمات تولید کد فعال سازی را فراهم می کنیم
                            // جهت تولید کد فعال یونیک از یک حلقه استفاده می کنیم تا به کد فعال یونیک برسیم
                            $Flag = 1;
                            while ($Flag == 1) {
                                $timestamp=strtotime("now"); 
                                $DateExpire =  date('Y-m-d H:i:s', $timestamp);
                                $Code = getCode(5);
                                $stmtUserID = $pdo->prepare("SELECT count(1) Count FROM `tbmobileverified` WHERE VerificationCode = '$Code' and DateExpire > '$DateExpire' "); 
                                $stmtUserID->execute(); 
                                $row = $stmtUserID->fetch();
                                $count = $row ["Count"];
                                //echo $count ;
                                if ($Count == 0)
                                    $Flag = 0;
                            }

                            // ثبت کد فعال سازی به همراه توکن مورد استفاده برای این کد 

                            $timestamp=strtotime("now");
                            $DateInsert =  date('Y-m-d H:i:s', $timestamp);
                            $timestamp=strtotime("+120 seconds");
                            $DateExpire =  date('Y-m-d H:i:s', $timestamp);
                            //$token = getToken(50);

                            $stmtlogin = $pdo->prepare("INSERT INTO tbmobileverified    ( `UserID`, `Mobile`,`IP` ,`VerificationCode`, `DateInsert`, `DateExpire`) 
                                                                                        VALUES 
                                                                                        ( :UserID, :Mobile, :IP, :VerificationCode, :DateInsert, :DateExpire)");
                            $stmtlogin->bindParam(':UserID'	,$UserID);
                            $stmtlogin->bindParam(':Mobile'	,$Mobile);
                            $stmtlogin->bindParam(':IP'	,$IP);
                            $stmtlogin->bindParam(':VerificationCode',$Code);
                            $stmtlogin->bindParam(':DateInsert'	,$DateInsert);
                            $stmtlogin->bindParam(':DateExpire'	,$DateExpire);
                            $stmtlogin->execute();

                            // // مدت اعتبار توکن ایجاد شده برابر با دو دقیقه می باشد
                            //$timestamp=strtotime("+60 Days");
                            $timestamp=strtotime("now");
                            $DateLogin =  date('Y-m-d H:i:s', $timestamp);
                            $timestamp=strtotime("+120 seconds");
                            $DateExpire =  date('Y-m-d H:i:s', $timestamp);
                            $token = getToken(50);
                            // کاربر جدید به سیستم لاگین می کند
                            $stmtlogin = $pdo->prepare("INSERT INTO tblogin
                                        (UserID, Token, IP,DateLogin,DateExpire) 
                                    VALUES 
                                        (:UserID,:token,:IP,:DateLogin,:DateExpire)");
                            $stmtlogin->bindParam(':UserID'	,$UserID);
                            $stmtlogin->bindParam(':token'	,$token);
                            $stmtlogin->bindParam(':IP'		,$IP);
                            $stmtlogin->bindParam(':DateLogin'	,$DateLogin);
                            $stmtlogin->bindParam(':DateExpire'	,$DateExpire);
                            $stmtlogin->execute();
                            
                            // $Code = convertNumbers($Code)  ;
                            // $message = "‏‏کد احراز هویت:$Code ‏‏با این کد می‌توانید به فروشگاه خود‌ وارد شوید.";
                            
                            $result['Token'] =$token  ;
                            $result['FirstLogin'] = true;
                            $result['Code'] =convertNumbers($Code)  ;
                            //$result['Message'] =$message  ;
                            $sendSms = SendSms ("100065995" ,$Code ,$Mobile);
                            $newResponse = $response->withStatus(200)->withJson(ApiResult($result,null,1));

                }
              
            $pdo->commit();
        //در صورت بروز خطا
        }catch(Exception $e){
            $pdo->rollBack();
            $newResponse = $response->withStatus(400)->withJson(ApiResult(null,$e,0));
        }
      
    
    }else{
        $error['Title'] = "عملیات نا موفق";
        $error['Body'] ="خطا در پارامتر های ارسالی"  ;
        $newResponse = $response->withStatus(400)->withJson(ApiResult(null,$error,0));
    }

//print_r($request);
//print_r($request->getUri()->getPath());
//print_r($response);
// print_r($request->getHeaders());
//print_r($request->getParsedBody() );
  
       return $newResponse;
});
// تایید ورود به سیستم 
$app->post('/user/confirm', function (Request $request, Response $response, array $args) {
    //print_r($request->getHeaders());
    // نمونه خروجی خام مورد استفاده در زوند اجرای پروژه
    $newResponse;

    $Code = $request->getParsedBodyParam('Code', $default = null);
    $Token = $request->getHeader('Token')[0];
    //Echo "$Code , $Token";
    // در صورتی که کد به همراه توکن ارسال شده بود امکان ادامه روند تایید وجود خواهد داشت
    if (isset($Code) && isset($Token) ){
        // عدد اگر فارسی ارسال شده باشد به عدد لاتین تغییر می کند
        $Code = convertNumbers($Code , false);
        // کانکشن باز شده و دارای ترنزکشن
        $pdo = $this->get("dbh");
        $pdo->beginTransaction();
        //ای پی کاربر
        $IP = GetClientIP();
        //برسی فعال بودن و در دسترسی بودن توکن
        $TokenData =  VerifyToken($Token,"tmp",$pdo);
        //print_r($TokenData);
        if($TokenData['Verify']){
            try{
                $Mobile = $TokenData['Mobile'];
                $UserID = $TokenData['UserID'];
                $timestamp=strtotime("now"); 
                $DateNow =  date('Y-m-d H:i:s', $timestamp);
                $UserData = $pdo->prepare("SELECT count(1) count FROM `tbmobileverified` where UserID = '$UserID' and Mobile = '$Mobile' and IP = '$IP' and VerificationCode = $Code and DateExpire > '$DateNow'"); 
                $UserData->execute(); 
                $rowCount = $UserData->rowCount();
                if ($rowCount >0) {
                    //توکن ایجاد شده به شکل فعال به مدت ۲ ماه می باشد بعد از این دوماه در صورت استفاده نشدن از باطل خواهد شد 
                    $timestamp=strtotime("now");
                    $DateLogin =  date('Y-m-d H:i:s', $timestamp);
                    $timestamp=strtotime("+60 Days");
                    //$timestamp=strtotime("+120 seconds");
                    $DateExpire =  date('Y-m-d H:i:s', $timestamp);
                    $token = getToken(50);
                    // کاربر جدید به سیستم لاگین می کند
                    $stmtlogin = $pdo->prepare("INSERT INTO tblogin
                                (UserID, Token, IP,Type,DateLogin,DateExpire) 
                            VALUES 
                                (:UserID,:token,:IP,'continuous',:DateLogin,:DateExpire)");
                    $stmtlogin->bindParam(':UserID'	,$UserID);
                    $stmtlogin->bindParam(':token'	,$token);
                    $stmtlogin->bindParam(':IP'		,$IP);
                    $stmtlogin->bindParam(':DateLogin'	,$DateLogin);
                    $stmtlogin->bindParam(':DateExpire'	,$DateExpire);
                    $stmtlogin->execute();
                    
                    $result['Token'] =$token  ;
                    $newResponse = $response->withStatus(200)->withJson(ApiResult($result,null,1));
                }else{
                    $error['title'] = "عملیات نا موفق";
                    $error['body'] ="کد ارسالی نامعتبر می باشد"  ;
                    $newResponse = $response->withStatus(400)->withJson(ApiResult(null,$error,0));
                }
                //در صورتی که عملیات بالا تماما انجام شد همه تغییرات کاملین می شود
                $pdo->commit();
            }catch(Exception $e){
                $pdo->rollBack();
                $newResponse = $response->withStatus(400)->withJson(ApiResult(null,$e,0));
            }


        }else{
            $error['title'] = "عملیات نا موفق";
            $error['body'] ="عملیات در بازه زمانی مورد نظر انجام نشده است"  ;
            $newResponse = $response->withStatus(400)->withJson(ApiResult(null,$error,0));
        }
    }else{
        $error['title'] = "عملیات نا موفق";
        $error['body'] ="پارامتر های ارسالی نا معتبر است"  ;
        $newResponse = $response->withStatus(400)->withJson(ApiResult(null,$error,0));
    }  
       return $newResponse;
});
// ثبت نام کاربر جدید
// $app->post('/user/reg', function (Request $request, Response $response, array $args) {
//     $UserName = $request->getParsedBodyParam('UserName', $default = null);
//     $Password = $request->getParsedBodyParam('Password', $default = null);
//     $Mobile = $request->getParsedBodyParam('Mobile', $default = null);
//     $ReagentID = $request->getParsedBodyParam('ReagentID', $default = 1);

//     //$result = array() ; 
   
//     if (isset($UserName)&&isset($Password)&&isset($Mobile))
//     {
//         //echo count(ValidPassword($Password));
//         // print_r(ValidPassword($Password));
//         // echo  ValidMobile($Mobile);
        
       
//         $newResponse;
//         $dbhandler = $this->get("dbh");
//         $errorPassword = ValidPassword($Password);
//         $errorUser = ValidUser($UserName , $Mobile,$dbhandler);
//         // print_r($errorPassword);
//         // print_r($errorUser);
//         if (ValidText($UserName)&& count($errorPassword) == 0  && ValidMobile($Mobile)&& count($errorUser) == 0 )
//             {
//                 $pdo = $this->get("dbh");
//                 $pdo->beginTransaction();
    
//                 //این عملیات با ترنز اکشن انجام خواهد شد و در صورتی که هر مرحله با خطایی مواجه گردید عملیات رول بک انجام خواهد شد
//                 //That way, we can rollback the transaction if a query fails and a PDO exception occurs.
//                 try{
//                     // بدست اوردن ip کاربر
//                     $IP = GetClientIP();
//                     // رمز نگاری رمز عبور کاربر
//                     $Password = HashPass($UserName,$UserPassword);
//                     // نام نویسی کاربر جدید
//                     $stmt = $pdo->prepare	("INSERT INTO tbuser 
//                                                         (`UserName`, `Password`, `ReagentID`,  `Mobile`)
//                                                     VALUES 
//                                                         (:UserName, :Password, :ReagentID, :Mobile)");
//                     $stmt->bindParam(':UserName' 		,$UserName);
//                     $stmt->bindParam(':Password' 		,$Password);
//                     $stmt->bindParam(':ReagentID' 	    ,$UserPassword);
//                     $stmt->bindParam(':Mobile'	        ,$Mobile);
                    
//                     $stmt->execute();		
//                     // شناسه کاربر جدید را بدست می اوریم
//                     $stmtUserID = $pdo->prepare("SELECT UserID  FROM tbuser where UserName = '$UserName' and Password = '$Password'"); 

//                     $stmtUserID->execute(); 
//                     $row = $stmtUserID->fetch();
                
//                     $UserID = $row ["UserID"];
//                     //$sendSms = SendSms();
//                     //echo $UserID ;
//                     date_default_timezone_set('UTC');
//                     // مدت اعتبار توکن ایجاد شده برابر با دو ماه می باشد
//                     //$timestamp=strtotime("+60 Days");
//                     $timestamp=strtotime("now");
//                     $DateLogin =  date('Y-m-d H:i:s', $timestamp);
//                     $timestamp=strtotime("+120 seconds");
//                     $DateExpire =  date('Y-m-d H:i:s', $timestamp);
//                     $token = getToken(50);
//                     // کاربر جدید به سیستم لاگین می کند
//                     $stmtlogin = $pdo->prepare("INSERT INTO tblogin
//                                 (UserID, Token,Type ,IP,DateLogin,DateExpire) 
//                             VALUES 
//                                 (:UserID,:token,:Type,:IP,:DateLogin,:DateExpire)");
//                     $stmtlogin->bindParam(':UserID'	,$UserID);
//                     $stmtlogin->bindParam(':token'	,$token);
//                     $stmtlogin->bindParam(':Type'	,'tmp');
//                     $stmtlogin->bindParam(':IP'		,$IP);
//                     $stmtlogin->bindParam(':DateLogin'	,$DateLogin);
//                     $stmtlogin->bindParam(':DateExpire'	,$DateExpire);
//                     $stmtlogin->execute();
                    
//                     $result['token'] =$token  ;

                                    
//                     //در صورتی که هیچ خطایی اتفاق نیوفتاد تغییرات کامیت می شود
//                     $pdo->commit();
//                     // خروجی این ای پی ای با ساختار از پیش تعیین شده به سمت کلاینت ارسال خواهید شد 
//                     $newResponse = $response->withStatus(200)->withJson(ApiResult($result,null,null));
                    
//                 } 
//                 //در صورت بروز خطا
//                 catch(Exception $e){
//                     $pdo->rollBack();
//                     $error['regerror']='خطا در مراحل ثبت نام لطفا بعدا تلاش نمایید';
//                 $newResponse = $response->withStatus(401)->withJson($error);
//                 }
//             }
//             else {
//                 $error ;
//                 if (!ValidText($UserName))
//                     $error['ValidText']='نام کاربر معتبر نمی باشد';
//                 if (count($errorPassword) != 0  )
//                     $error['errorPassword']=$errorPassword;
//                 if (!ValidMobile($Mobile))
//                     $error['ValidMobile'] = 'شماره موبایل معتبر نمی باشد';
//                 if (count($errorUser) != 0 )
//                     $error['errorUser']=$errorUser;
//                 $newResponse = $response->withStatus(401)->withJson(ApiResult(null,$error,null));
//             }
//             //     echo $token = getToken(50);
//             // $UserIP = GetClientIP();
//             // echo $UserIP;
//     }else{
//         $newResponse = $response->withStatus(400)->withJson($request->getHeader(null));
//     }

// //print_r($request);
// //print_r($request->getUri()->getPath());
// //print_r($response);
// // print_r($request->getHeaders());
// //print_r($request->getParsedBody() );
    
//        return $newResponse;
// });
// // ورود کاربر به سیستم
// $app->post('/user/log', function (Request $request, Response $response, array $args){
//     $UserName = $request->getParsedBodyParam('UserName', $default = null);
//     $Mobile = $request->getParsedBodyParam('Mobile', $default = null);
//     $Email = $request->getParsedBodyParam('Email', $default = null);
//     $Password = $request->getParsedBodyParam('Password', $default = null);
//     $pdo = $this->get("dbh");
//     if(!isset($UserName) &&((isset($Mobile)||isset($Email)))){
//         $stmt = $pdo->prepare("SELECT UserName  FROM tbuser where Email = '$Email' or Mobile = '$Mobile' "); 
//         $stmt->execute(); 
//         $row = $stmt->fetch();
//         $UserName = $row ["UserName"];
//     }
//     if(isset($UserName) && isset($Password)){
//         $Password = HashPass($UserName,$UserPassword);

//         $stmt = $pdo->prepare("SELECT count(*) count , UserID  FROM tbuser where Username = '$UserName' and Password = '$Password'"); 
//         $stmt->execute(); 
//         $row = $stmt->fetch();
//         $count = $row ["count"]; 
//         $UserID = $row ["UserID"]; 
//         //echo $count;
//         if (intval($count) == 1 )	
//         {
//             date_default_timezone_set('UTC');
//             // مدت اعتبار توکن ایجاد شده برابر با دو ماه می باشد
//             $timestamp=strtotime("+60 Days");
//             $DateExpire =  date('Y-m-d H:i:s', $timestamp);
//             $token = getToken(50);
//             $IP = GetClientIP();
//             // کاربر جدید به سیستم لاگین می کند
//             $stmtlogin = $pdo->prepare("INSERT INTO tblogin
//                         (UserID, Token, IP,DateExpire) 
//                     VALUES 
//                         (:UserID,:token,:IP,:DateExpire)");
//             $stmtlogin->bindParam(':UserID'	,$UserID);
//             $stmtlogin->bindParam(':token'	,$token);
//             $stmtlogin->bindParam(':IP'		,$IP);
//             $stmtlogin->bindParam(':DateExpire'	,$DateExpire);
//             $stmtlogin->execute();
            
//             $result['token'] =$token  ;
            
//             $newResponse = $response->withStatus(200)->withJson(ApiResult($result,null,null));
//         }
//         else{  
//         $message['Usernotfound']='کاربر مورد نظر یافت نشد';	
//         $newResponse = $response->withStatus(400)->withJson(ApiResult(null,$message,null));	
//     }
//     }else{
//         $message= array();
//         if(!isset($UserName)){
//             $message['UsernameError']='نام کاربری ,شماره همراه,پست الکترونیکی را وارد کنید';
//         }
//         if(!isset($Password)){
//             $message['password'] = 'رمز عبور را وارد کنید';
//         }
//         $newResponse = $response->withStatus(400)->withJson(ApiResult(null,$message,null));	
//     }

//     return $newResponse;
// });