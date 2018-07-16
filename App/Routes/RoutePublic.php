<?php
use Slim\Http\Request;
use Slim\Http\Response;
// Routes لیست ای پی ای های عمومی که تنها با داشتن یک توکن فعال قابل اجرا می باشند
//لیست بانکهای موجود در سیستم را در خروجی به نمایش در می اورد
$app->get('/public/bank', function (Request $request, Response $response, array $args) {
  // دریافت دیتا از میدل وار جهت استفاده در طول در خواست
  $TokenData = $request->getAttribute("TokenData");
  //print_r($TokenData);
    $pdo = $this->get("dbh");
    $query = $pdo->prepare("SELECT * FROM `tbBank` "); 
    //$query->execute(); 
    if ( $query->execute()){
      $row = $query->fetchAll();
      return $response->withStatus(400)->withHeader('Content-Type', 'application/json')->write(json_encode(ApiResult($row,null,1)));
    } else {
        $error['Title'] = "عملیات نا موفق";
        $error['Body'] ="خطا در ارسال تصویر"  ;
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json')->write(json_encode(ApiResult(null,$error,0)));
      }
 })->add($validToken);
  // در دسترس بودن فروشگاه یا نام کاربری
$app->get('/public/availableaccount', function (Request $request, Response $response, array $args) {
    // دریافت دیتا از میدل وار جهت استفاده در طول در خواست
    $TokenData = $request->getAttribute("TokenData");
    // print_r($TokenData);
    // print_r($request->getQueryParams());
    $AccountName = $request->getQueryParams()['account'];
    $UserID = $TokenData['UserID'];
    $pdo = $this->get("dbh");
    
    $result['account'] = $AccountName;
    if ( AvailableAccount($AccountName,$UserID,$pdo)){
      $result['available']=true;
      return $response->withStatus(200)->withHeader('Content-Type', 'application/json')->write(json_encode(ApiResult($result,null,1)));
    } else {
      $result['available']=false;
      return $response->withStatus(200)->withHeader('Content-Type', 'application/json')->write(json_encode(ApiResult($result,null,1)));
      }
    })->add($validToken);
    // در دسترس بودن پست الکترونیکی
$app->get('/public/availableemail', function (Request $request, Response $response, array $args) {
    // دریافت دیتا از میدل وار جهت استفاده در طول در خواست
    $TokenData = $request->getAttribute("TokenData");
    // print_r($TokenData);
    // print_r($request->getQueryParams());
    $Email = $request->getQueryParams()['email'];
    $UserID = $TokenData['UserID'];
    $pdo = $this->get("dbh");
    
    $result['email'] = $Email;
    if ( AvailableEmail($Email,$UserID,$pdo)){
      $result['available']=true;
      return $response->withStatus(200)->withHeader('Content-Type', 'application/json')->write(json_encode(ApiResult($result,null,1)));
    } else {
      $result['available']=false;
      return $response->withStatus(200)->withHeader('Content-Type', 'application/json')->write(json_encode(ApiResult($result,null,1)));
      }
    })->add($validToken);
        // در دسترس بودن معرف
$app->get('/public/availablereagent', function (Request $request, Response $response, array $args) {
    // دریافت دیتا از میدل وار جهت استفاده در طول در خواست
    $TokenData = $request->getAttribute("TokenData");
    // print_r($TokenData);
    // print_r($request->getQueryParams());
    $Reagent = $request->getQueryParams()['reagent'];
    $UserID = $TokenData['UserID'];
    $pdo = $this->get("dbh");
    //شناسه معرف قابل استفاده
    // در صورتی که کاربر قبلا معرفی را ثبت کرده باشد تنها امکان ثبت معرف قبلی را خواهد داشت که در عمل به روز رسانی انجام نمی شود
    // حال اگر شماسه معرف ارسالی با شناسه ارسالی از فانکشن مربوطه یکی باشد مفهوم ان امکان ثبت معرف می باشد در غیر این صورت امکان ثبت معرف جدید وجود ندارد
    $AvailableReagentID = AvailableReagentID($Reagent,$UserID,$pdo);
    $result['Reagent'] = $Reagent;
    if ( $AvailableReagentID == $Reagent){
      $result['available']=true;
      return $response->withStatus(200)->withHeader('Content-Type', 'application/json')->write(json_encode(ApiResult($result,null,1)));
    } else {
      $result['available']=false;
      return $response->withStatus(200)->withHeader('Content-Type', 'application/json')->write(json_encode(ApiResult($result,null,1)));
      }
    })->add($validToken);