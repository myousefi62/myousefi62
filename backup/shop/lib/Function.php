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

function HashPass($UserName,$UserPassword){
	return sha1(md5($UserName).md5($UserPassword));
}
function ValidTest($Text){
	if (!preg_match("/^[a-zA-Z0-9]*$/",$Text)) 
		return False; 
	else 
		return True;
	}
function ValidNumber($Text){
	if (!preg_match("/^[0-9]*$/",$Text)) 
		return False; 
	else 
		return True;
	}
function ValidEmail($email){
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
		return False; 
	else 
		return True;
	}
function VerifyUser(){
	$conn = Flight::DbConn();
	$token	=	getallheaders()['token'] ;
	$UserIP	=	GetClientIP();
	$stmt = $conn->prepare("SELECT count(*) count ,UserID FROM `userlogin` WHERE UserIP = '$UserIP' and token = '$token' "); 
	$stmt->execute(); 
	$row = $stmt->fetch();
	$count = $row ["count"];
	$result = array(); 
	if ($count >= 1){
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
?>