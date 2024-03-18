<?php
include "base.php";

class Googleapi extends Base {
	function __construct()
	{
		parent::__construct();	
		
		$this->load->model("gpsmodel");
		$this->load->model("smsmodel");
		$this->load->model("configmodel");
		$this->load->library('email');
		$this->load->helper('email');
		$this->load->helper('common');
		
	}
	
	function getaccess_token($userid=""){
		
		$end_point = 'https://accounts.google.com/o/oauth2/v2/auth';
		$client_id = '689214124692-9kf4elr8j209gihfres10onsr9onb4pv.apps.googleusercontent.com';
		$client_secret = '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';
		//$redirect_uri = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]';   //  http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"] or urn:ietf:wg:oauth:2.0:oob
		$redirect_uri = 'urn:ietf:wg:oauth:2.0:oob';
		$scope = 'https://www.googleapis.com/auth/drive.file https://www.googleapis.com/auth/drive.metadata https://www.googleapis.com/auth/drive https://www.googleapis.com/auth/drive.photos.readonly https://www.googleapis.com/auth/drive.metadata.readonly';
			$access_token = 'ya29.a0ARrdaM_6l8GUAH4Bu4zcwwrKXq-kK-qCYZ6gSzeKKH08eY4P_AZV9FOAz157I6VnN5RawONybhW9a0dhVXvqiNKafKhvyxz7Lgf2ysYYHDH9eIGleLKKDth9EpshcGklgutW4h8By8YCk97TZxH7ve9U79ds';
			$refresh_token = '1//0dG5jzyAvoSifCgYIARAAGA0SNwF-L9Ir_tbyoAC5yDaHelsRyOaTqQsVbanWEf5woYduzKWcBPYNr_xjEJfyymJV-GzUOP_Dr34';

			// Check if the access token already expired
			$ch = curl_init(); 
			curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/oauth2/v1/tokeninfo?access_token='.$access_token); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$error_response = curl_exec($ch);
			$array = json_decode($error_response);
			//print_r($array);
			if(isset($array->error)){
				//print_r('disini');exit();
				
				$query_data = array('client_id'=>$client_id,'client_secret'=>$client_secret,'refresh_token'=>$refresh_token,'grant_type'=>'refresh_token');
				$numeric_prefix= '';
				$arg_separator = '&';
				$http_query = http_build_query ($query_data, $numeric_prefix, $arg_separator);
				
				// Generate new Access Token using old Refresh Token
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL,"https://accounts.google.com/o/oauth2/token");
				curl_setopt($ch, CURLOPT_POST, TRUE);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $http_query);
				
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$response = curl_exec($ch);
				curl_close ($ch);
				//var_dump($response);
				$response = json_decode($response);
				
				if(isset($response->access_token)){
					$token_access = $response->access_token;
					$token_type = $response->token_type;
					$token_refresh = $refresh_token;
					$token_user = $userid;
					$token_status = 1;
					
					//printf("PREPARE INSERT TO TBL AUTOREPORT \r\n");
					$this->dbts = $this->load->database("webtracking_ts", TRUE);
					$data_insert["token_access"] = $token_access;
					$data_insert["token_refresh"] = $token_refresh;
					$data_insert["token_user"] = $token_user;
					$data_insert["token_type"] = $token_type;
					$data_insert["token_status"] = $token_status;
					$data_insert["token_created"] = date("Y-m-d H:i:s");
					$this->dbts->insert("ts_access_token",$data_insert);
					$this->dbts->close();
					printf("INSERT TOKEN SUCCESS : %s \r \n",$token_access);
				}else{
					
					printf("CANNOT GET ACCESS TOKEN \r \n");
				}
				
				
			}else{
				printf("TOKEN MASIH BERLAKU : %s \r \n",$access_token);
				
			} 
			

		
	}
	
	
	/* function testapi2(){
		
		$end_point = 'https://accounts.google.com/o/oauth2/v2/auth';
		$client_id = '244399799775-rt1gj3vk8q2ncgfeejulp60m73fmuhql.apps.googleusercontent.com';
		$client_secret = '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';
		//$redirect_uri = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]';   //  http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"] or urn:ietf:wg:oauth:2.0:oob
		$redirect_uri = 'urn:ietf:wg:oauth:2.0:oob';
		//$scope = 'https://www.googleapis.com/auth/drive.metadata.readonly';
		$scope = 'https://www.googleapis.com/auth/drive';
		


		$authUrl = $end_point.'?'.http_build_query([
			'client_id'              => $client_id,
			'redirect_uri'           => $redirect_uri,              
			'scope'                  => $scope,
			'access_type'            => 'offline',
			'include_granted_scopes' => 'true',
			'state'                  => 'state_parameter_passthrough_value',
			'response_type'          => 'code'
		]);

		echo '<a href = "'.$authUrl.'">Authorize</a></br>';


		// Generate new Access Token and Refresh Token if token.json doesn't exist
		if ( !file_exists('token.json') ){
			
			if ( isset($_GET['code'])){
				$code = $_GET['code'];         // Visit $authUrl and get the authentication code
			}else{
				return;
			} 

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,"https://accounts.google.com/o/oauth2/token");
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_HTTPHEADER, [ 'Content-Type: application/x-www-form-urlencoded']);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
				'code'          => $code,
				'client_id'     => $client_id,
				'client_secret' => $client_secret,
				'redirect_uri'  => $redirect_uri,
				'grant_type'    => 'authorization_code',
			]));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($ch);
			curl_close ($ch);
			
			file_put_contents('token.json', $response);
		}
		else{
			$response = file_get_contents('token.json');
			$array = json_decode($response);
			$access_token = $array->access_token;
			$refresh_token = $array->refresh_token;

			// Check if the access token already expired
			$ch = curl_init(); 
			curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/oauth2/v1/tokeninfo?access_token='.$access_token); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$error_response = curl_exec($ch);
			$array = json_decode($error_response);
			
			if( isset($array->error)){
				
				// Generate new Access Token using old Refresh Token
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL,"https://accounts.google.com/o/oauth2/token");
				curl_setopt($ch, CURLOPT_POST, TRUE);
				curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
					'client_id'     => $client_id,
					'client_secret' => $client_secret,
					'refresh_token'  => $refresh_token,
					'grant_type'    => 'refresh_token',
				]));
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$response = curl_exec($ch);
				curl_close ($ch);
			}  
		}

		var_dump($response);
	}
	 */
	function testapi(){
		
		$client_id = '244399799775-rt1gj3vk8q2ncgfeejulp60m73fmuhql.apps.googleusercontent.com';
		$redirect_uri = 'https://developers.google.com/oauthplayground';
		$client_secret = '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';

		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, "https://accounts.google.com/o/oauth2/token");
		
		curl_setopt($ch, CURLOPT_POST, TRUE);
		
		$code = '4/0AX4XfWgUhvdrqxBQ_w2mTGWLtrtH7RR5unNQPdus2JNsYXcSoZe9kZIcyeoA2TfIWqxtmA';
		//$code = $_REQUEST['code'];

		// This option is set to TRUE so that the response
		// doesnot get printed and is stored directly in
		// the variable
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
		curl_setopt($ch, CURLOPT_POSTFIELDS, array(
		'code' => $code,
		'client_id' => $client_id,
		'client_secret' => $client_secret,
		'redirect_uri' => $redirect_uri,
		'grant_type' => 'authorization_code'
		));

		$data = curl_exec($ch);
		
		var_dump($data);
	}
	
	function testgeocode()
	{
		//printf("PROSES POST SAMPLE -> GET >> GEOCODE \r\n");
		
		$token = "BkaW5kNGhraTR0OnAwNXRkNHQ0a2k0dA16B";
		$authorization = "Authorization:".$token;
		$url = "http://balrich.lacak-mobil.com/customapi/geocode";
		$feature = array();
		
		$feature["lat"] = "0.4530";
		$feature["long"] = "101.4168";
		$feature["apikey"] = "AIzaSyB6IF8SgmcExwBNjJ1nZ0jIeyagUr8i-zo";
		
		//printf("POSTING PROSES \r\n");
		//$content = json_encode($feature, JSON_NUMERIC_CHECK);
		$content = json_encode($feature);
		$total_content = count($content);
		
		//printf("Data JSON : %s \r \n",$content);
		                    
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
		//printf("-------------------------- \r\n");
		
		exit;
	}
	
	
	
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
