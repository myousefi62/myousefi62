<?php

use Slim\Http\Request;
use Slim\Http\Response;
// all function needed 
date_default_timezone_set('UTC');

function crypto_rand_secure($min, $max)
{
    $range = $max - $min;
    if ($range < 1) return $min; // not so random...
    $log = ceil(log($range, 2));
    $bytes = (int) ($log / 8) + 1; // length in bytes
    $bits = (int) $log + 1; // length in bits
    $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
    do {
        $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
        $rnd = $rnd & $filter; // discard irrelevant bits
    } while ($rnd > $range);
    return $min + $rnd;
}
// تولید کد احراط هویت
function getCode($length)
{
    $Code = "";
    $codeNumber = "0123456789";
    $max = strlen($codeNumber); // edited

    for ($i=0; $i < $length; $i++) {
        $Code .= $codeNumber[crypto_rand_secure(0, $max-1)];
    }

    return $Code;
}
// تبدیل عدد فارسی به انگلیسی یا انگلیسی به فارسی
function convertNumbers($srting,$toPersian=true)
{
    $en_num = array('0','1','2','3','4','5','6','7','8','9');
    $fa_num = array('۰','۱','۲','۳','۴','۵','۶','۷','۸','۹');
    if( $toPersian ) return str_replace($en_num, $fa_num, $srting);
        else return str_replace($fa_num, $en_num, $srting);
}

function getToken($length)
{
    $token = "";
    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
    $codeAlphabet.= "0123456789";
    $max = strlen($codeAlphabet); // edited

    for ($i=0; $i < $length; $i++) {
        $token .= $codeAlphabet[crypto_rand_secure(0, $max-1)];
    }

    return $token;
}

//GetIp Client 

function GetClientIP($validate = False){
  $ipkeys = array(
  'REMOTE_ADDR', 
  'HTTP_CLIENT_IP', 
  'HTTP_X_FORWARDED_FOR', 
  'HTTP_X_FORWARDED', 
  'HTTP_FORWARDED_FOR', 
  'HTTP_FORWARDED', 
  'HTTP_X_CLUSTER_CLIENT_IP'
  );

  /*
  now we check each key against $_SERVER if contain such value
  */
  $ip = array();
  foreach($ipkeys as $keyword){
    if( isset($_SERVER[$keyword]) ){
      if($validate){
        if( ValidatePublicIP($_SERVER[$keyword]) ){
          $ip[] = $_SERVER[$keyword];
        }
      }else{
        $ip[] = $_SERVER[$keyword];
      }
    }
  }

  $ip = ( empty($ip) ? 'Unknown' : implode(", ", $ip) );
  return $ip;

}
function ValidatePublicIP($ip){
//echo $ip ;
  if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
    return true;
  }
  else {
    return false;
  }
} 
// کدینگ روز عبور 
function HashPass($UserName,$UserPassword){
	return sha1(md5($UserName).md5($UserPassword));
}
// اعتبار سنجی رمز عبور
 function ValidPassword($pwd) {
    $errors = null;

    if (strlen($pwd) < 8) {
        $errors[] = "طول رمز عبور باید بیش از ۸ کاراکتر باشد";
    }

    if (!preg_match("#[0-9]+#", $pwd)) {
        $errors[] = "رمز عبور باید دارای اعداد باشد";
    }

    if (!preg_match("#[a-zA-Z]+#", $pwd)) {
        $errors[] = "رمز عبور باید کاراکتر داشته باشد";
    }     

    return $errors ;
}
// شماره موبایل معتبر 
function ValidMobile($mobilenumber){
if(!preg_match("/^[0]{1}[9]{1}[1-9]{1}[0-9]{8}$/", $mobilenumber)) 
        return False; 
	else 
		return True;
}
// کاراکتر های وارده جزو اعداد و حروف انگلیسی می باشد
function ValidText($Text){
	if (!preg_match("/^[a-zA-Z0-9]*$/",$Text)) 
		return False; 
	else 
		return True;
    }
    // مقدار وارد شده جز عدد نمی باشد
function ValidNumber($Text){
	if (!preg_match("/^[0-9]*$/",$Text)) 
		return False; 
	else 
		return True;
    }
    // پست الکترونیکی معتبر می باشد (ساختار ایمیل را دارد) م
function ValidEmail($email){
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
		return False; 
	else 
		return True;
    }
    // با این شماره موبایل حسابی فعال شده است
    function ExistMobil($mobilenumber,$dbhandler){
        $existent = 0;
        $stmt2 = $dbhandler->prepare(" SELECT count(1) count FROM `tbuser` where Mobile = '".$mobilenumber."' ");
        $stmt2->execute();
        $rowMobile = $stmt2->fetch();
        $countMobile = $rowMobile["count"];

        if ($countMobile >0)
            $existent = 1;
    
        return $existent;
        
    }
    //کد فعال سازی ارسالی معتبر و مخصوص این کاربر می باشدیا خیر  
    function VerificationCode($Code,$Token,$dbhandler){
        $existent = 0;
        $timestamp=strtotime("now"); 
        $DateNow =  date('Y-m-d H:i:s', $timestamp);
        echo strtotime($DateExpire);    
       // echo "SELECT count(1) Count FROM `tbmobileverified` WHERE UserID = '$UserID' and DateExpire > '$DateExpire' ";
        $stmtVerificationCodeCount = $dbhandler->prepare("SELECT count(1) Count FROM `tbmobileverified` WHERE UserID = '$UserID' and DateExpire > '$DateNow' "); 
        $stmtVerificationCodeCount->execute(); 
        $row = $stmtVerificationCodeCount->fetch();
        $count = $row ["Count"];

        if ($count >0)
            $existent = 1;
    
        $stmtVerificationCodeCount = $dbhandler->prepare("SELECT DateExpire FROM `tbmobileverified` WHERE UserID = '$UserID' and DateExpire > '$DateNow' "); 
        $stmtVerificationCodeCount->execute(); 
        $row = $stmtVerificationCodeCount->fetch();
        $DateExpire = $row ["DateExpire"];
         //echo timeDiff($DateExpire ,$DateNow);
        return $existent;
        
    }
    //در زمان حاظر کد فعال سازی در خواست داده شده و رکوردی موجود می باشد
    function ExistVerificationCode($UserID,$dbhandler){
        $existent = 0;
        $timestamp=strtotime("now"); 
        $DateNow =  date('Y-m-d H:i:s', $timestamp);
        //echo strtotime($DateExpire);    
       // echo "SELECT count(1) Count FROM `tbmobileverified` WHERE UserID = '$UserID' and DateExpire > '$DateExpire' ";
        $stmtVerificationCodeCount = $dbhandler->prepare("SELECT count(1) Count FROM `tbmobileverified` WHERE UserID = '$UserID' and DateExpire > '$DateNow' "); 
        $stmtVerificationCodeCount->execute(); 
        $row = $stmtVerificationCodeCount->fetch();
        $count = $row ["Count"];

        if ($count >0)
            $existent = 1;
    
        $stmtVerificationCodeCount = $dbhandler->prepare("SELECT DateExpire FROM `tbmobileverified` WHERE UserID = '$UserID' and DateExpire > '$DateNow' "); 
        $stmtVerificationCodeCount->execute(); 
        $row = $stmtVerificationCodeCount->fetch();
        $DateExpire = $row ["DateExpire"];
         //echo timeDiff($DateExpire ,$DateNow);
        return $existent;
        
    }
    // اختلاف زمانی بین دو تاریخ 
    function timeDiff($time2,$time1){
        $diff = strtotime($time2) - strtotime($time1);
        if($diff > 0){
        if($diff < 60){
            return $diff.' ثانیه قبل';
        }
        elseif($diff < 3600){
            return round($diff / 60,0,1).' دقیقه قبل';
        }
        elseif($diff >= 3660 && $diff < 86400){
            return round($diff / 3600,0,1).' ساعت قبل';
        }
        elseif($diff > 86400){
            return round($diff / 86400,0,1).' روز قبل';
        }
    }
    }
    
function ValidUser($UserName , $mobilenumber,$dbhandler){
    $errors = null;
    //print_r($this);
    // $dbhandler = $this->get("dbh");
    // $stmt1 = $dbhandler->prepare(" SELECT count(1) FROM `tbuser` where UserName = ':UserName' ");
    // $stmt1->bindParam(':UserName'	,$UserName);
    
    $stmt1 = $dbhandler->prepare(" SELECT count(1) count FROM `tbuser` where UserName = '".$UserName."'  ");
    $stmt1->execute();
    $rowUser = $stmt1->fetch();
	$countUser = $rowUser["count"];
    //echo $countUser;
    // $stmt2 = $dbhandler->prepare(" SELECT count(1) count FROM `tbuser` where Mobile = ':Mobile' ");
    // $stmt2->bindParam(':Mobile'	,$mobilenumber);
    $stmt2 = $dbhandler->prepare(" SELECT count(1) count FROM `tbuser` where Mobile = '".$mobilenumber."' ");
    $stmt2->execute();
    $rowMobile = $stmt2->fetch();
    $countMobile = $rowMobile["count"];
    
    if ($countUser >0)
        $errors[] = 'نام کاربری در دسترس نیست';
    if ($countMobile >0)
        $errors[] = 'شماره موبایل تکراری می باشد';
    
        //print_r($errors);
	return $errors;
	
}
//برسی فعال بودن ودر بازه زمانی بودن توکن
function VerifyToken($token,$type,$dbhandler){
    $timestamp=strtotime("now"); 
    $DateNow =  date('Y-m-d H:i:s', $timestamp);
    
   //echo "SELECT UserID FROM tblogin where Token = '$token' and DateExpire > '$DateNow'";
	$stmt = $dbhandler->prepare("SELECT  tu.UserID , tu.Mobile FROM tblogin tl inner join tbuser tu on tu.UserID = tl.UserID  where Token = '$token' and Type = '$type' and DateExpire > '$DateNow'"); 
    $stmt->execute(); 
    $result = array(); 
    if ($stmt->rowCount() > 0){
        $row = $stmt->fetch();
        $result['UserID'] = $row ["UserID"];
        $result['Mobile'] = $row ["Mobile"];
        $result['Verify']= true;
    }else{
        $result['Verify']= false;
    }
    
	return $result;
	
}
function VerifyUser($token,$Code){
	$conn = Flight::DbConn();
	$token	=	getallheaders()['token'] ;
	$UserIP	=	GetClientIP();
	$stmt = $conn->prepare("SELECT count(*) count ,UserID FROM `userlogin` WHERE UserIP = '$UserIP' and token = '$token' "); 
	$stmt->execute(); 
	$row = $stmt->fetch();
	$count = $row ["count"];
	$result = array(); 
	if ($count >0){
		$result['Verify']= true;
		$UserID = $row ["UserID"];
		$result['UserID']=$UserID  ; 
		$stmt = $conn->prepare("SELECT UserType  FROM `users`  where UserID = $UserID "); 
		$stmt->execute(); 
		$row = $stmt->fetch();
		$result['UserType'] = $row ["UserType"];
		}
	else 
		$result['Verify']= false;
	return $result;
	
}
//ساختار خروجی ای پی ای ها از همین ابتدا مشخص   می شود
function ApiResult($data,$message,$state = 1){
$result = array();
$result['data'] = $data;
$result['message'] = $message ;
$result['state']=$state;
return $result;

}

///send sms 
function SendSms ($sender = "100065995" ,$Code ,$receptor){
                ///////////////////////////////////////
                $result = "";
                try{
                    ///convertNumbers
                    $api = new \Kavenegar\KavenegarApi("4A754171703437594F5A4E63616A49715748596B6C4B71774932537532497847");
                    //$sender = "100065995";
                    
                    $Code = convertNumbers($Code)  ;
                    $message = "‏‏کد احراز هویت:$Code ‏‏با این کد می‌توانید به فروشگاه خود‌ وارد شوید.";
                            
                    //$receptor = array("09905567166");
                    $result = $api->Send($sender,$receptor,$message);
                   // print_r($result);
                    // if($result){
                    //     foreach($result as $r){
                    //         echo "messageid = $r->messageid";
                    //         echo "message = $r->message";
                    //         echo "status = $r->status";
                    //         echo "statustext = $r->statustext";
                    //         echo "sender = $r->sender";
                    //         echo "receptor = $r->receptor";
                    //         echo "date = $r->date";
                    //         echo "cost = $r->cost";
                    //     }		
                    // }
                }
                catch(\Kavenegar\Exceptions\ApiException $e){
                    // در صورتی که خروجی وب سرویس 200 نباشد این خطا رخ می دهد
                    $result = $e->errorMessage();
                    //echo $e->errorMessage();
                }
                catch(\Kavenegar\Exceptions\HttpException $e){
                    // در زمانی که مشکلی در برقرای ارتباط با وب سرویس وجود داشته باشد این خطا رخ می دهد
                    $result = $e->errorMessage();
                    //echo $e->errorMessage();
                }

               return $result; 
                // try{
                    
                //     $api = new KavenegarApi("4A754171703437594F5A4E63616A49715748596B6C4B7177493253753249784");
                //     $receptor = "09194527844";
                //     $token = "110";
                //     $token2 = "";
                //     $token3 = "";
                //     $template = "VerifyTest";
                //     $type = "call";//sms | call
                //     $result = $api->VerifyLookup($receptor,$token,$token2,$token3,$template,$type);
                //     if($result){
                //         var_dump($result);
                //     }
                // }
                // catch(ApiException $e){
                //     echo $e->errorMessage();
                // }
                // catch(HttpException $e){
                //     echo $e->errorMessage();
                // }
                // //////////////////////////////////////
                // print_r(json_encode($result));
            }
// end send sms 
//end function
///
//////////////start Add Allow-Origin
$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});
////////////////// end Allow-Origin
require __DIR__. '/Routes/RouteQoust.php';
 require __DIR__. '/Routes/RouteUser.php';
 require __DIR__. '/Routes/RouteAdmin.php';
 require __DIR__. '/Routes/RouteSuperAdmin.php';


// Catch-all route to serve a 404 Not Found page if none of the routes match
// NOTE: make sure this route is defined last
$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
    $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
    return $handler($req, $res);
});
