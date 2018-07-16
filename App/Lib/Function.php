<?php
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
function convertNumbers($srting,$toPersian=true){
      $en_num = array('0','1','2','3','4','5','6','7','8','9');
      $fa_num = array('۰','۱','۲','۳','۴','۵','۶','۷','۸','۹');
      if( $toPersian ) return str_replace($en_num, $fa_num, $srting);
          else return str_replace($fa_num, $en_num, $srting);
  }

function getToken($length){
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
    
    //echo "SELECT  tu.UserID , tu.Mobile FROM tblogin tl inner join tbuser tu on tu.UserID = tl.UserID  where Token = '$token' and Type = '$type' and DateExpire > '$DateNow'";
	$stmt = $dbhandler->prepare("SELECT  tu.UserID , tu.Mobile FROM tblogin tl inner join tbuser tu on tu.UserID = tl.UserID  where Token = '$token' and Type = '$type' and DateExpire > '$DateNow' and `status` = 'active'"); 
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
// در دسترس بودن حساب کاربری و حساب فروشگاه
// نمی توان یک حساب کاربری و فروشگاه همنام در سامانه وجود داشته باشد
function AvailableAccount($AccountName,$UserID,$dbhandler){
    $result = false;
    if(ValidText($UserName)){
        $Query = $dbhandler->prepare("SELECT count(*) count FROM `tbuser`where UserName = '$AccountName'  and UserID != $UserID"); 
        $Query->execute(); 
        $rowUser = $Query->fetch();
        $Query = $dbhandler->prepare("SELECT count(*) count FROM `tbmarket`where MarketName = '$AccountName'  and UserID != $UserID"); 
        $Query->execute(); 
        $rowmarket = $Query->fetch();
        if ($rowUser["count"] == 0 && $rowmarket["count"] == 0){
            //این نام کاربری قبل از این استفاده شده است
            $result =  true;
        }
    }
    return $result;
}
    
function AvailableUserName($UserName,$UserID,$dbhandler){
    if(ValidText($UserName)){
        $Query = $dbhandler->prepare("SELECT count(*) count FROM `tbuser`where UserName = '$UserName'  and UserID != $UserID"); 
        $Query->execute(); 
        $row = $Query->fetch();
        if ($row["count"] > 0){
            //این نام کاربری قبل از این استفاده شده است
            return false;
        }else{
            //این نام کاربری در دسترس می باشد
            return true;
        }
    }else{
        //نام کاربری از کاراکتر های غیر استاندارد استفاده شده است
        return false;
    }
	
}
function AvailableEmail($Email,$UserID,$dbhandler){
    if(ValidEmail($Email)){
        $Query = $dbhandler->prepare("SELECT count(*) count FROM `tbuser`where Email = '$Email' and UserID != $UserID"); 
        $Query->execute(); 
        $row = $Query->fetch();
        if ($row["count"] > 0){
            //این پست الکترونیکی قبل از این استفاده شده است
            return false;
        }else{
            //این پست الکترونیکی در دسترس می باشد
            return true;
        }
    }else{
        //این پست الکترونیکی از نظر ساختاری درست نمی باشد
        return false;
    }
    
}
function AvailableReagentID($ReagentID,$userID,$dbhandler){
    
    $Query = $dbhandler->prepare("SELECT ReagentID FROM `tbuser`where UserID = '$userID' "); 
    $Query->execute(); 
    $row = $Query->fetch();
    //echo $row["ReagentID"];
    if ($row["ReagentID"] == NULL){
        $Query = $dbhandler->prepare("SELECT count(*) count FROM `tbuser`where RefrencsID = '$ReagentID' "); 
        $Query->execute(); 
        $row = $Query->fetch();
        if ($row["count"] > 0){
            //این شناسه ارجاع در دسترس می باشد
            return $ReagentID;
        }else{
            //این شناسه ارجاع قبل از این استفاده شده است
            return NULL;
        }
    }else{
        return $row["ReagentID"];

    }
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
//-- انتقال فایل به پوشه مورد نظر
// function moveUploadedFile($directory, UploadedFile $uploadedFile){
    function moveUploadedFile($directory, $uploadedFile){
print_r($uploadedFile);

    $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
    $basename = bin2hex(random_bytes(8));
    $filename = sprintf('%s.%0.8s', $basename, $extension);
    $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);
   
   return $filename;
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
//پارامتر های ورودی را خالص سازی میکند
function sanitizeString($var)
  {
    $var = strip_tags($var);
    $var = htmlentities($var, ENT_COMPAT, 'UTF-8');   
    $var = stripslashes($var);
    $var = htmlspecialchars($var);
    $var=str_replace("/"," ",$var);
    $var=str_replace("\\"," ",$var);
    $var=str_replace("^"," ",$var);
    $var=str_replace("~"," ",$var);
    $var=str_replace("etc"," ",$var);
    $var=str_replace("passwd"," ",$var);
    return $var; 
  }

  /*
to take encoded files as a parameter,decoded,save as a file,and return json message
*/
function upload_file($encoded_string , $target_dir, $File_Name){
    //$target_dir = ''; // add the specific path to save the file
    $decoded_file = base64_decode($encoded_string); // decode the file
    $mime_type = finfo_buffer(finfo_open(), $decoded_file, FILEINFO_MIME_TYPE); // extract mime type
    $extension = mime2ext($mime_type); // extract extension from mime type
    $file = uniqid() .'.'. $extension; // rename file as a unique name
    $file_dir = $target_dir . uniqid() .'.'. $extension;
    try {
        file_put_contents($file_dir, $decoded_file); // save
        database_saving($file);
        header('Content-Type: application/json');
        echo json_encode("File Uploaded Successfully");
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode($e->getMessage());
    }

}
/*
to take mime type as a parameter and return the equivalent extension
*/
function mime2ext($mime){
    $all_mimes = '{
        "png":["image\/png","image\/x-png"],
        "bmp":["image\/bmp","image\/x-bmp","image\/x-bitmap","image\/x-xbitmap","image\/x-win-bitmap","image\/x-windows-bmp","image\/ms-bmp","image\/x-ms-bmp","application\/bmp","application\/x-bmp","application\/x-win-bitmap"],
        "gif":["image\/gif"],
        "jpeg":["image\/jpeg","image\/pjpeg"],
        "xspf":["application\/xspf+xml"],
        "vlc":["application\/videolan"],
        "wmv":["video\/x-ms-wmv","video\/x-ms-asf"],
        "au":["audio\/x-au"],
        "ac3":["audio\/ac3"],
        "flac":["audio\/x-flac"],
        "ogg":["audio\/ogg","video\/ogg","application\/ogg"],
        "kmz":["application\/vnd.google-earth.kmz"],
        "kml":["application\/vnd.google-earth.kml+xml"],
        "rtx":["text\/richtext"],
        "rtf":["text\/rtf"],
        "jar":["application\/java-archive","application\/x-java-application","application\/x-jar"],
        "zip":["application\/x-zip","application\/zip","application\/x-zip-compressed","application\/s-compressed","multipart\/x-zip"],
        "7zip":["application\/x-compressed"],
        "xml":["application\/xml","text\/xml"],
        "svg":["image\/svg+xml"],
        "3g2":["video\/3gpp2"],
        "3gp":["video\/3gp","video\/3gpp"],
        "mp4":["video\/mp4"],
        "m4a":["audio\/x-m4a"],
        "f4v":["video\/x-f4v"],
        "flv":["video\/x-flv"],
        "webm":["video\/webm"],
        "aac":["audio\/x-acc"],
        "m4u":["application\/vnd.mpegurl"],
        "pdf":["application\/pdf","application\/octet-stream"],
        "pptx":["application\/vnd.openxmlformats-officedocument.presentationml.presentation"],
        "ppt":["application\/powerpoint","application\/vnd.ms-powerpoint","application\/vnd.ms-office",
        "application\/msword"],
        "docx":["application\/vnd.openxmlformats-officedocument.wordprocessingml.document"],
        "xlsx":["application\/vnd.openxmlformats-officedocument.spreadsheetml.sheet","application\/vnd.ms-excel"],
        "xl":["application\/excel"],
        "xls":["application\/msexcel","application\/x-msexcel","application\/x-ms-excel","application\/x-excel","application\/x-dos_ms_excel","application\/xls","application\/x-xls"],
        "xsl":["text\/xsl"],"mpeg":["video\/mpeg"],
        "mov":["video\/quicktime"],
        "avi":["video\/x-msvideo","video\/msvideo","video\/avi","application\/x-troff-msvideo"],
        "movie":["video\/x-sgi-movie"],
        "log":["text\/x-log"],
        "txt":["text\/plain"],
        "css":["text\/css"],
        "html":["text\/html"],
        "wav":["audio\/x-wav","audio\/wave","audio\/wav"],
        "xhtml":["application\/xhtml+xml"],
        "tar":["application\/x-tar"],
        "tgz":["application\/x-gzip-compressed"],
        "psd":["application\/x-photoshop",
        "image\/vnd.adobe.photoshop"],
        "exe":["application\/x-msdownload"],
        "js":["application\/x-javascript"],
        "mp3":["audio\/mpeg","audio\/mpg","audio\/mpeg3","audio\/mp3"],
        "rar":["application\/x-rar","application\/rar","application\/x-rar-compressed"],
        "gzip":["application\/x-gzip"],
        "hqx":["application\/mac-binhex40","application\/mac-binhex","application\/x-binhex40","application\/x-mac-binhex40"],
        "cpt":["application\/mac-compactpro"],
        "bin":["application\/macbinary","application\/mac-binary","application\/x-binary","application\/x-macbinary"],
        "oda":["application\/oda"],
        "ai":["application\/postscript"],
        "smil":["application\/smil"],
        "mif":["application\/vnd.mif"],
        "wbxml":["application\/wbxml"],
        "wmlc":["application\/wmlc"],
        "dcr":["application\/x-director"],
        "dvi":["application\/x-dvi"],
        "gtar":["application\/x-gtar"],
        "php":["application\/x-httpd-php","application\/php","application\/x-php","text\/php","text\/x-php","application\/x-httpd-php-source"],
        "swf":["application\/x-shockwave-flash"],
        "sit":["application\/x-stuffit"],
        "z":["application\/x-compress"],
        "mid":["audio\/midi"],
        "aif":["audio\/x-aiff","audio\/aiff"],
        "ram":["audio\/x-pn-realaudio"],
        "rpm":["audio\/x-pn-realaudio-plugin"],
        "ra":["audio\/x-realaudio"],
        "rv":["video\/vnd.rn-realvideo"],
        "jp2":["image\/jp2","video\/mj2","image\/jpx","image\/jpm"],
        "tiff":["image\/tiff"],
        "eml":["message\/rfc822"],
        "pem":["application\/x-x509-user-cert","application\/x-pem-file"],
        "p10":["application\/x-pkcs10","application\/pkcs10"],
        "p12":["application\/x-pkcs12"],
        "p7a":["application\/x-pkcs7-signature"],
        "p7c":["application\/pkcs7-mime","application\/x-pkcs7-mime"],
        "p7r":["application\/x-pkcs7-certreqresp"],
        "p7s":["application\/pkcs7-signature"],
        "crt":["application\/x-x509-ca-cert","application\/pkix-cert"],
        "crl":["application\/pkix-crl","application\/pkcs-crl"],
        "pgp":["application\/pgp"],
        "gpg":["application\/gpg-keys"],
        "rsa":["application\/x-pkcs7"],
        "ics":["text\/calendar"],
        "zsh":["text\/x-scriptzsh"],
        "cdr":["application\/cdr","application\/coreldraw","application\/x-cdr","application\/x-coreldraw","image\/cdr","image\/x-cdr","zz-application\/zz-winassoc-cdr"],
        "wma":["audio\/x-ms-wma"],
        "vcf":["text\/x-vcard"],
        "srt":["text\/srt"],
        "vtt":["text\/vtt"],
        "ico":["image\/x-icon","image\/x-ico","image\/vnd.microsoft.icon"],
        "csv":["text\/x-comma-separated-values","text\/comma-separated-values","application\/vnd.msexcel"],
        "json":["application\/json","text\/json"]
    }';
    $all_mimes = json_decode($all_mimes,true);
    foreach ($all_mimes as $key => $value) {
        if(array_search($mime,$value) !== false) return $key;
    }
    return false;
}
/*
to save the file name and extension into database
*/
function database_saving($file){
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "demo";

// Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $sql = "INSERT INTO uploaded_files (file_name)VALUES ('$file')";
    $conn->query($sql);
    $conn->close();

}

// // invoke upload_file function and pass your input as a parameter
// $encoded_string = !empty($_POST['base64_file']) ? $_POST['base64_file'] : 'V2ViZWFzeXN0ZXAgOik=';
// upload_file($encoded_string);
//end function
///
// function crypto_rand_secure($min, $max)
// {
//     $range = $max - $min;
//     if ($range < 1) return $min; // not so random...
//     $log = ceil(log($range, 2));
//     $bytes = (int) ($log / 8) + 1; // length in bytes
//     $bits = (int) $log + 1; // length in bits
//     $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
//     do {
//         $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
//         $rnd = $rnd & $filter; // discard irrelevant bits
//     } while ($rnd > $range);
//     return $min + $rnd;
// }

// function getToken($length)
// {
//     $token = "";
//     $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
//     $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
//     $codeAlphabet.= "0123456789";
//     $max = strlen($codeAlphabet); // edited

//     for ($i=0; $i < $length; $i++) {
//         $token .= $codeAlphabet[crypto_rand_secure(0, $max-1)];
//     }

//     return $token;
// }

// //GetIp Client 

// function GetClientIP($validate = False){
//   $ipkeys = array(
//   'REMOTE_ADDR', 
//   'HTTP_CLIENT_IP', 
//   'HTTP_X_FORWARDED_FOR', 
//   'HTTP_X_FORWARDED', 
//   'HTTP_FORWARDED_FOR', 
//   'HTTP_FORWARDED', 
//   'HTTP_X_CLUSTER_CLIENT_IP'
//   );

//   /*
//   now we check each key against $_SERVER if contain such value
//   */
//   $ip = array();
//   foreach($ipkeys as $keyword){
//     if( isset($_SERVER[$keyword]) ){
//       if($validate){
//         if( ValidatePublicIP($_SERVER[$keyword]) ){
//           $ip[] = $_SERVER[$keyword];
//         }
//       }else{
//         $ip[] = $_SERVER[$keyword];
//       }
//     }
//   }

//   $ip = ( empty($ip) ? 'Unknown' : implode(", ", $ip) );
//   return $ip;

// }
// function ValidatePublicIP($ip){
// //echo $ip ;
//   if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
//     return true;
//   }
//   else {
//     return false;
//   }
// } 

// function HashPass($UserName,$UserPassword){
// 	return sha1(md5($UserName).md5($UserPassword));
// }
// function ValidTest($Text){
// 	if (!preg_match("/^[a-zA-Z0-9]*$/",$Text)) 
// 		return False; 
// 	else 
// 		return True;
// 	}
// function ValidNumber($Text){
// 	if (!preg_match("/^[0-9]*$/",$Text)) 
// 		return False; 
// 	else 
// 		return True;
// 	}
// function ValidEmail($email){
// 	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
// 		return False; 
// 	else 
// 		return True;
// 	}
// function VerifyUser(){
// 	$conn = Flight::DbConn();
// 	$token	=	getallheaders()['token'] ;
// 	$UserIP	=	GetClientIP();
// 	$stmt = $conn->prepare("SELECT count(*) count ,UserID FROM `userlogin` WHERE UserIP = '$UserIP' and token = '$token' "); 
// 	$stmt->execute(); 
// 	$row = $stmt->fetch();
// 	$count = $row ["count"];
// 	$result = array(); 
// 	if ($count >= 1){
// 		$result['Verify']= true;
// 		$UserID = $row ["UserID"];
// 		$result['UserID']=$UserID  ; 
// 		$stmt = $conn->prepare("SELECT UserType  FROM `users`  where UserID = $UserID "); 
// 		$stmt->execute(); 
// 		$row = $stmt->fetch();
// 		$result['UserType'] = $row ["UserType"];
// 		}
// 	else 
// 		$result['Verify']= false;
// 	return $result;
	
// }
?>