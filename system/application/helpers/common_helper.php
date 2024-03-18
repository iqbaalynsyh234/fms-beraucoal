<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('stringtohex'))
{
	function stringtohex($s)
	{
		$hex = "";
		for($i=0; $i < strlen($s); $i++)
		{
			$d = ord($s[$i]);
			$hex .= sprintf("%02X", $d);
		}
		
		return $hex;
	}
}

if ( ! function_exists('valid_emails'))
{
	function valid_emails($email)
	{
		$emails = explode(";", $email); 
		
		foreach($emails as $email)
		{
			if (! valid_email($email)) return false;
		}
		
		return true;
	}
}

if (!function_exists('get_time_difference')) {
    function get_time_difference($date1, $date2) {
        $date1 = strtotime($date1);
        $date2 = strtotime($date2);
        if ($date1 !== - 1 && $date2 !== - 1) {
            if ($date2 > $date1) {
                $diff = $date2 - $date1;
                if ($days = intval((floor($diff / 86400))))
                    $diff = $diff % 86400;
                if ($hours = intval((floor($diff / 3600))))
                    $diff = $diff % 3600;
                if ($minutes = intval((floor($diff / 60))))
                    $diff = $diff % 60;
                $diff = intval($diff);
                
                return array($days, $hours, $minutes, intval($diff));
            }
        }
        
        return false;
    }
}

if ( ! function_exists('get_valid_emails'))
{
	function get_valid_emails($email)
	{
		$emails = explode(";", $email); 
		 
		foreach($emails as $email)
		{
			if (! valid_email($email)) continue;
			
			$validemails[] = $email;
		}
		
		
		if (! isset($validemails)) return FALSE;
		
		return $validemails;
	}
}

//EXPIRED|PARKIR|KECEPATAN|GEOFENCE
if ( ! function_exists('isON'))
{
	function isON($dec, $pos)
	{
		$binaries = decbin($dec);
		return isset($binaries[$pos]) && ($binaries[$pos] == 1);
	}
}

if (! function_exists("paddingleft"))
{
	function paddingleft($s, $c, $len)
	{
		while(strlen($s) < $len)
		{
			$s = $c.$s;
		}
		
		return $s;
	}
}

if ( ! function_exists('hextostring'))
{
	function hextostring($s)
	{
		$dec = "";
		for($i=0; $i < strlen($s); $i+=2)
		{
			$hex = substr($s, $i, 2);
			
			$dec .= chr(hexdec($hex));
		}
		
		return $dec;
	}
}

if ( ! function_exists('nomobil'))
{
	function nomobil($nomobil)
	{
		$nomobil = str_replace('"', '', $nomobil);
		$nomobil = str_replace("'", '', $nomobil);
		$nomobil = str_replace(' ', '', $nomobil);
		$nomobil = str_replace('.', '', $nomobil);
		$nomobil = str_replace('<', '', $nomobil);
		$nomobil = str_replace('>', '', $nomobil);
		$nomobil = str_replace('!', '', $nomobil);
		$nomobil = str_replace(',', '', $nomobil);
		
		return $nomobil;
	}
}

if ( ! function_exists('maillocalhost'))
{
	function maillocalhost($subject, $body, $to, $url="http://services.adilahsoft.com/lacakmobil/sendmail.php", $sender="support@adilahsoft.com", $sendername="AdilahSoft Support", $log=false, $bcc=false)
	{
		$ch = curl_init();
		
		$post_data = "";
		$post_data .= "to=".$to;
		$post_data .= "&subject=".$subject;
		$post_data .= "&body=".nl2br(urlencode($body));		
		$post_data .= "&sender=".$sender;
		$post_data .= "&sendername=".$sendername;
		if ($bcc)
		{
			$post_data .= "&bcc=".$bcc;
		}
		
		if ($log)
		{
			echo "curl: ".$url."\r\n".$post_data."\r\n\r\n";
		}
		
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);	
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);	
		curl_exec($ch);	
		curl_close($ch);	
	}
}

if ( ! function_exists('intl_mobile'))
{
	function intl_mobile($no)
	{
		$no = ltrim($no, '0');
		
		if (substr($no, 0, 2) == "62")
		{
			return $no;
		}

		return "62".$no;
	}
}

if ( ! function_exists('valid_mobile'))
{
	function valid_mobile($no)
	{
		$no = preg_replace("/[a-zA-Z]/", "", $no);
		$no = str_replace(" ", "", $no);
		$no = str_replace("(", "", $no);
		$no = str_replace(")", "", $no);
		$no = str_replace("+", "", $no);
		$no = str_replace("-", "", $no);
		$no = ltrim($no, "0");
		
		if (! $no) return "";
		if (! is_numeric($no)) return "";				

		if (substr($no, 0, 2) == "62")
		{
			return $no;
		}

		return "0".$no;
	}
}

if ( ! function_exists('valid_mobiles'))
{
	function valid_mobiles($no)
	{
		$nos = explode(";", $no);
		foreach($nos as $n)
		{
			$mobile = valid_mobile($n);
			if (! $mobile) continue;
			
			$mobiles[] = $mobile;
		}
		
		if (! isset($mobiles)) return FALSE;
		
		return $mobiles;
	}
}

if ( ! function_exists('same_valid_mobile'))
{
	function same_valid_mobile($no1, $no2)
	{
		$no1 = valid_mobile($no1);
		$no2s = valid_mobiles($no2);
		
		if ($no2s === FALSE) return false;
		
		foreach($no2s as $no2)
		{		
			if (substr($no1, 0, 2) == "62")
			{
				$no1 = "0".substr($no1, 2);
			}

			if (substr($no2, 0, 2) == "62")
			{
				$no2 = "0".substr($no2, 2);
			}

			if ($no1 == $no2) return true;
		}
		
		return false;
	}
}

if ( ! function_exists('getLatitude'))
{
	function getLatitude($angle, $northSouth)
	{
		$degrees = floor($angle/100.0);        
		$minutes = $angle - $degrees*100;
		
		$latitude = $degrees+$minutes/60.0;
		
		if ($northSouth == "S")
		{
			$latitude = -1*$latitude;
		}
	
		return $latitude;
	}
}   

if ( ! function_exists('getLongitude'))
{
	function getLongitude($angle, $westEast)
	{
		$degrees = floor($angle/100.0);        
		$minutes = $angle - $degrees*100;

		$longitude = $degrees+$minutes/60.0;
		if ($westEast == "W")
		{
			$longitude = -1*$longitude;
		}
	
		return $longitude;
	} 	
}

/**
 * yyyymmdd to date (dd/mm/yyyy) 
 *
 * @access	public
 * @return	time
 */	
if ( ! function_exists('inttodate'))
{
	function inttodate($t)
	{
		$year = substr($t,0,4);
		$month= substr($t,4,2);
		$date = substr($t,6,2);
		
		return $date."/".$month."/".$year;
	}
}


/**
 * yyyy-mm-dd hh:ii:ss to time 
 *
 * @access	public
 * @return	time
 */	
if ( ! function_exists('dbmaketime'))
{
	function dbmaketime($t)
	{
		$ts = explode(" ", $t);
		if (count($ts) != 2) return 0;
		
		$ds = explode("-", trim($ts[0]));
		$ts1 = explode(":", trim($ts[1]));

		if (count($ds) != 3) return 0;
		if (count($ts1) != 3) return 0;
		
		return mktime($ts1[0], $ts1[1], $ts1[2], $ds[1], $ds[2], $ds[0]);
	}
}

/**
 * yyyymmdd hhiiss to time 
 *
 * @access	public
 * @return	time
 */	
if ( ! function_exists('dbintmaketime'))
{
	function dbintmaketime($d, $t)
	{
		$y = floor($d/10000);
		$m = floor(($d%10000)/100);
		$d = ($d%10000)%100;

		$j = floor($t/10000);
		$me = floor(($t%10000)/100);
		$de = ($t%10000)%100;
		
		return mktime($j, $me, $de, $m, $d, $y);

	}
}

/**
 * ddmmyy hhiiss to time 
 *
 * @access	public
 * @return	time
 */	
if ( ! function_exists('dbintmaketime1'))
{
	function dbintmaketime1($d, $t)
	{
		//17811
		$dd = floor($d/10000);
		$m = floor(($d%10000)/100);
		$y = ($d%10000)%100;

		$j = floor($t/10000);
		$me = floor(($t%10000)/100);
		$de = ($t%10000)%100;
		
		return mktime($j, $me, $de, $m, $dd, $y);

	}
}


/**
 * ddmmyyyy hhiiss to time 
 *
 * @access	public
 * @return	time
 */	
if ( ! function_exists('dbintmaketime2'))
{
	function dbintmaketime2($d, $t)
	{
		//28122011
		$dd = floor($d/1000000);
		$m = floor(($d%1000000)/10000);
		$y = ($d%10000)%100;

		$j = floor($t/10000);
		$me = floor(($t%10000)/100);
		$de = ($t%10000)%100;
		
		return mktime($j, $me, $de, $m, $dd, $y);

	}
}

/**
 * dd/mm/yyyy hh:ii:ss to time 
 *
 * @access	public
 * @return	time
 */	
if ( ! function_exists('formmaketime'))
{
	function formmaketime($t)
	{
		$ts = explode(" ", $t);
		
		if (count($ts) != 2) return 0;

		$ds = explode("/", trim($ts[0]));						
		$ts1 = explode(":", trim($ts[1]));
				
		if (count($ds) != 3) return 0;
		if (count($ts1) != 3) return 0;
		
		return mktime($ts1[0]*1, $ts1[1]*1, $ts1[2]*1, $ds[1]*1, $ds[0]*1, $ds[2]*1);
	}
}

/**
 * dd/mm/yy hh:ii:ss to time 
 *
 * @access	public
 * @return	time
 */	
if ( ! function_exists('formmaketimeshort'))
{
	function formmaketimeshort($t)
	{
		$ts = explode(" ", $t);
		
		if (count($ts) != 2) return 0;

		$ds = explode("/", trim($ts[0]));						
		$ts1 = explode(":", trim($ts[1]));
				
		if (count($ds) != 3) return 0;
		if (count($ts1) != 3) return 0;
		
		return mktime($ts1[0]*1, $ts1[1]*1, $ts1[2]*1, $ds[1]*1, $ds[0]*1, $ds[2]*1+2000);
	}
}

if ( ! function_exists('sendlocalhost'))
{
	function sendlocalhost($subject, $body)
	{
		$ch = curl_init();

		$post_data = "subject=".$subject;
		$post_data .= "&body=".nl2br(urlencode($body));
			
		curl_setopt($ch, CURLOPT_URL, "http://services.adilahsoft.com/lacakmobil/sendmail.php");
		curl_setopt($ch, CURLOPT_POST, true); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);	
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);	
		curl_exec($ch);	
		curl_close($ch);	
	}
}

if ( ! function_exists('implodeex'))
{
	function implodeex($separator, $arr)
	{
		if (! is_array($arr)) return;
		if (count($arr) == 0) return;
		
		$body = "";
		foreach($arr as $key=>$val)
		{
			if ($body)
			{
				$body .= $separator;
			}
			
			$body .=  $key."=".$val;
		}
		
		return $body;
	}
}

if ( ! function_exists('curl_post_async'))
{
	function curl_post_async($url, $params)
	{
	    foreach ($params as $key => &$val) 
	    {
			if (is_array($val)) $val = implode(',', $val);
	        $post_params[] = $key.'='.urlencode($val);
	    }
	    
	    $post_string = implode('&', $post_params);
	
	    $parts=parse_url($url);
	
	    $fp = fsockopen($parts['host'], isset($parts['port'])?$parts['port']:80, $errno, $errstr, 30);
	
	    $out = "POST ".$parts['path']." HTTP/1.1\r\n";
	    $out.= "Host: ".$parts['host']."\r\n";
	    $out.= "Content-Type: application/x-www-form-urlencoded\r\n";
	    $out.= "Content-Length: ".strlen($post_string)."\r\n";
	    $out.= "Connection: Close\r\n\r\n";
	    if (isset($post_string)) $out.= $post_string;
	
	    fwrite($fp, $out);
	    fclose($fp);
	}
}

if ( ! function_exists('lacakmobilmail'))
{
	function lacakmobilmail($post)
	{
		if (is_array($post['dest']))
		{
			$mails = $post['dest'];
		}
		else
		{
			$emails = array($post['dest']);
		}
		
		if (! isset($emails))
		{
			printf("email tidak terdefinisi\r\n");
			return;
		}
		
		foreach($emails as $email)
		{		
			$post['dest'] = $email;
			
			$ch = curl_init();
			
			$post_data = "";
			foreach($post as $key=>$val)
			{
				$post_data .= sprintf("&%s=%s", $key, nl2br(urlencode($val)));
			}
			
			curl_setopt($ch, CURLOPT_URL, "http://www.lacak-mobil.com/mail.php");
			curl_setopt($ch, CURLOPT_POST, true); 
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);	
			//curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);	
			echo curl_exec($ch);	
			curl_close($ch);	
		}
	}
}

if ( ! function_exists('erpservice'))
{
	function erpservice($path, $post, $return=false)
	{
		$CI =& get_instance();
		
		$ch = curl_init();
		
		$post_data = "";
		foreach($post as $key=>$val)
		{
			$post_data .= sprintf("&%s=%s", $key, nl2br(urlencode($val)));
		}
		
		curl_setopt($ch, CURLOPT_URL, $CI->config->item("erpservice").$path);
		curl_setopt($ch, CURLOPT_POST, true); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);	
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);	
		$result = curl_exec($ch);	
		curl_close($ch);
		
		if ($return) return $result;		
		if (! $result) return;
		
		echo $result;
	}
}

if ( ! function_exists('csvheader'))
{
	function csvheader($header, $csv)
	{
		echo str_replace(";", $csv, $header)."\r\n";
	}
}

if ( ! function_exists('csvcontents'))
{
	function csvcontents($fields, $rows, $csv)
	{
		for($i=0; $i < count($rows); $i++)
		{
			for($j=0; $j < count($fields); $j++)
			{
				if ($j > 0) echo $csv;
				
				echo str_replace($csv, " ", $rows[$i]->{$fields[$j]});
			}
			
			echo "\r\n";
		}
	}
}


//LALIN
	function twitterify($ret) {
        $ret = preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t< ]*)#", "\\1<a href=\"\\2\" target=\"_blank\">\\2</a>", $ret);
        $ret = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r< ]*)#", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $ret);
        $ret = preg_replace("/@(\w+)/", "<a href=\"http://www.twitter.com/\\1\" target=\"_blank\">@\\1</a>", $ret);
        $ret = preg_replace("/#(\w+)/", "<a href=\"http://twitter.com/#!/search?q=%23\\1\" target=\"_blank\">#\\1</a>", $ret);
        return $ret;
    }

/* End of file email_helper.php */
/* Location: ./system/helpers/email_helper.php */
