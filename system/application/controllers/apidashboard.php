<?php
include "base.php";

class Apidashboard extends Base {

	function Apidashboard()
	{
		parent::Base();	
		$this->load->model("dashboardmodel");
	}
	
	function request()
	{
		printf("PROSES POST SAMPLE -> REQUEST >> LAST POSITION \r\n");
		
		$token = "CVaW5kNGhraTX0OnEVaNoxRkNHksdAWh7k0dA16";
		$authorization = "Authorization:".$token;
		$url = "http://api.lacak-mobil.com/api/lastpositionderek";
		$feature = array();
		
		$feature["UserId"] = 3893; //derek purbaleunyi
		$feature["VehiclePlateNo"] = "all"; 
		
		printf("POSTING PROSES \r\n");
		$content = json_encode($feature);
		$total_content = count($content);
		
		printf("Data JSON : %s \r \n",$content);
		               
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

		$json_response = curl_exec($curl);
		echo $json_response;
		echo curl_getinfo($curl, CURLINFO_HTTP_CODE);
		printf("-------------------------- \r\n");
		
		exit;
	}
	
	function getPosition($longitude, $ew, $latitude, $ns){
		$gps_longitude_real = getLongitude($longitude, $ew);
		$gps_latitude_real = getLatitude($latitude, $ns);
					
		$gps_longitude_real_fmt = number_format($gps_longitude_real, 4, ".", "");
		$gps_latitude_real_fmt = number_format($gps_latitude_real, 4, ".", "");	
					
		$georeverse = $this->gpsmodel->GeoReverse($gps_latitude_real_fmt, $gps_longitude_real_fmt);
		
		return $georeverse;
	}
	
	function getPosition_other($longitude, $latitude)
	{
		$georeverse = $this->gpsmodel->GeoReverse($latitude, $longitude);	
		return $georeverse;
	}
	
	function lastoverspeed()
	{
		header("Content-Type: application/json");
		
		$token = "LCKaW5kNGhraTX0OnAwNXRkNHQ0a2k0dA16";
		$postdata = json_decode(file_get_contents("php://input"));
		$allvehicle = 0;
		$now = date("Ymd");
		
		$headers = null;
		if (isset($_SERVER['Authorization'])) 
		{
            $headers = trim($_SERVER["Authorization"]);
        }
		else if (isset($_SERVER['HTTP_AUTHORIZATION'])) 
		{ //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        }
        else if (function_exists('apache_request_headers')) 
        {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            if (isset($requestHeaders['Authorization'])) 
            {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        print_r("DISINI");exit();
        if($headers != $token)
        {
			$feature["StatusCode"] = "FAILED";
			$feature["Message"] = "INVALID TOKEN";
			echo json_encode($feature);
			exit;
		}
		
		$feature = array();
		
		if(!isset($postdata->OverSpeed) || $postdata->OverSpeed == "")
		{
			$feature["StatusCode"] = "FAILED";
			$feature["Message"] = "INVALID OVERSPEED CONFIG";
			echo json_encode($feature);
			exit;
		}
		
		if(!isset($postdata->UserLevel) || $postdata->UserLevel == "")
		{
			$feature["StatusCode"] = "FAILED";
			$feature["Message"] = "INVALID USER LEVEL CONFIG";
			echo json_encode($feature);
			exit;
		}
		
		if(!isset($postdata->OverSpeed) || $postdata->OverSpeed == "")
		{
			$feature["StatusCode"] = "FAILED";
			$feature["Message"] = "INVALID OVERSPEED CONFIG";
			echo json_encode($feature);
			exit;
		}
		
		if(!isset($postdata->UserId) || $postdata->UserId == "")
		{
			$feature["StatusCode"] = "FAILED";
			$feature["Message"] = "INVALID USER ID";
			echo json_encode($feature);
			exit;
		}else{
			
			//jika ada cek dari database nopol (untuk dapat device id)
			$this->db->order_by("vehicle_id","desc");
			
			$this->db->where("vehicle_status",1);
			$q = $this->db->get("vehicle_autocheck");
			
			$this->db->order_by("auto_vehicle_no","asc");
			if($postdata->UserLevel == 1){
				$this->db->where("auto_user_id",$this->sess->user_id);
			}else if($postdata->UserLevel == 2){
				$this->db->where("auto_vehicle_company",$this->sess->user_company);
			}else{
				$this->db->where("auto_user_id",0);
			}
			
			$this->db->where("auto_last_speed >=",$postdata->OverSpeed);
			$this->db->where("auto_flag",0);
			$q = $this->db->get("vehicle_autocheck");
			
			if($q->num_rows == 0)
			{
				$feature["StatusCode"] = "FAILED";
				$feature["Message"] = "VEHICLE NOT FOUND";
				echo json_encode($feature);
				exit;
			}else{
				$vehicle = $q->result();
				
			}
		}
		
		
		//echo json_encode($feature);
		exit;
	}

}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
