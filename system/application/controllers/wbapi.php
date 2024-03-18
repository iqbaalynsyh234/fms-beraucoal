<?php
include "base.php";

class Wbapi extends Base {

	function Wbapi()
	{
		parent::Base();
		$this->load->model("gpsmodel");
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
		$this->load->model("dashboardmodel");
		$this->load->helper('common_helper');
		$this->load->helper('kopindosat');
		$this->load->model('m_ugemsmodel');
	}

	function gettoken2($userid=""){
		$url = "https://interactive.jatismobile.com/wa/users/login";
		$data = array("username" => "temanindobara", "password" => "SmmIndobaratmn68%");

				$content = json_encode($data);

				$curl = curl_init($url);
				curl_setopt($curl, CURLOPT_HEADER, false);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
				curl_setopt($curl, CURLOPT_HTTPHEADER,
				        array("Content-type: application/json"));
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

				$json_response = curl_exec($curl);
				$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
				$result = json_decode($json_response);
				
				print_r($result);exit();
				
				$data =  $result->data;
				$token = $data->token;
				
				unset($data);
				$data["sess_value"] = $token;
				$data["sess_type"] = "TOKEN";
				$data["sess_lastmodified"] = date("Y-m-d H:i:s");
				$data["sess_status"] = 1;
				$data["sess_user"] = $userid;
				
				$this->dbts = $this->load->database("webtracking_ts", true);
				$this->dbts->insert("ts_ugems_token",$data);
				
				$nowtime = date("Y-m-d H:i:s");
				printf("===GET TOKEN WA SUCESS !! at %s \r\n", $nowtime);
				$this->dbts->close();
				$this->dbts->cache_delete_all();

	}
	
	function gettoken($userid=""){
		$urlgettoken = "https://interactive.jatismobile.com/wa/users/login";
		
		$username = "temanindobara";
		$password  = "SmmIndobaratmn68%";

				/* $curlHandler = curl_init();

				curl_setopt_array($curlHandler, array(
					CURLOPT_URL => $urlgettoken,
					CURLOPT_RETURNTRANSFER => true,

					CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
					CURLOPT_USERPWD => $username . ':' . $password,
				));

				$response = curl_exec($curlHandler);
				curl_close($curlHandler);
				
				print_r($response);exit(); */
				
				
				/*  $curlSecondHandler = curl_init();

				curl_setopt_array($curlSecondHandler, array(
					CURLOPT_URL => $urlgettoken,
					CURLOPT_RETURNTRANSFER => true,

					CURLOPT_HTTPHEADER => array(
						'Authorization: Basic ' . base64_decode($username . ':' . $password)
					),
				));

				$response = curl_exec($curlSecondHandler);
				curl_close($curlSecondHandler);
				
				print_r($response);exit(); */
				
				
				/* $ch = curl_init();

				curl_setopt($ch, CURLOPT_URL, 'https://interactive.jatismobile.com/wa/users/login'); 
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, "{}");

				$headers = array();
				$headers[] = 'Authorization: Basic base64(temanindobara:SmmIndobaratmn68%)';
				$headers[] = 'Content-Type: application/json';
				
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

				$result = curl_exec($ch);
				if (curl_errno($ch)) {
					echo 'Error:' . curl_error($ch);
				}
				curl_close($ch); */
				
				
				// Generated @ codebeautify.org
				$ch = curl_init();

				curl_setopt($ch, CURLOPT_URL, 'https://interactive.jatismobile.com/wa/users/login');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, "{}");

				$headers = array();
				$headers[] = 'Authorization: Basic base64(temanindobara:SmmIndobaratmn68%)';
				$headers[] = 'Content-Type: application/json';
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

				$result = curl_exec($ch);
				if (curl_errno($ch)) {
					echo 'Error:' . curl_error($ch);
				}
				curl_close($ch);

				
				print_r($result);exit();
				
				$data =  $result->data;
				$token = $data->token;
				
				unset($data);
				$data["sess_value"] = $token;
				$data["sess_type"] = "TOKEN";
				$data["sess_lastmodified"] = date("Y-m-d H:i:s");
				$data["sess_status"] = 1;
				$data["sess_user"] = $userid;
				
				$this->dbts = $this->load->database("webtracking_ts", true);
				$this->dbts->insert("ts_wa_token",$data);
				
				$nowtime = date("Y-m-d H:i:s");
				printf("===GET TOKEN WA SUCESS !! at %s \r\n", $nowtime);
				$this->dbts->close();
				$this->dbts->cache_delete_all();

	}
	
	function post($userid="")
	{
		ini_set('display_errors', 1);
		date_default_timezone_set("Asia/Jakarta");
		$mytoken = "b0d54308-53a5-4433-8cda-3e9d580e0646";
			
		$token = $mytoken;
		$authorization = "token: Bearer ".$token;
		$url = "https://interactive.jatismobile.com/v1/messages";
		
			$arrayVar = array();

				/* $arrayVar = [
					"to" => "6281617467868",
					"type" => "template",
					"template" => [
						"namespace" => "56bfcbc9_e293_4702_8497_b768270ba792",
						"language" => ["policy" => "deterministic", "code" => "en"],
						"name" => "smm_smartservicesmm_information_01",
						"components" => [
							[
								"type" => "body",
								"parameters" => [
									["type" => "text", "text" => "Budi Indobara"],
									["type" => "text", "text" => "(10101010)"],
									["type" => "text", "text" => "2022-11-17"],
								],
							],
						],
					],
				]; */

		

				$content = json_encode($arrayVar);
				print_r($content);exit();
				
			
								$curl = curl_init($url);
								curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
								curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
								curl_setopt($curl, CURLOPT_HEADER, false);
								curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
								curl_setopt($curl, CURLOPT_POST, true);
								curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
								curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
								curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
								
								$result = curl_exec($curl);
								
								echo $result;
								echo curl_getinfo($curl, CURLINFO_HTTP_CODE);
								
								// Get the POST request header status
								//$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

								// If header status is not Created or not OK, return error message
								/* if ( $status !== 201 || $status !== 200 ) {
								   die("Error: call to URL $url failed with status $status, response $result, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
								} */
								printf("-------------------------- \r\n");
			
			
			/* $this->db->close();
			$this->db->cache_delete_all(); */

	}
	
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
