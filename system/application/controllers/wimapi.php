<?php
include "base.php";

class Wimapi extends Base {

	function Wimapi()
	{
		parent::Base();
		$this->load->model("gpsmodel");
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
		$this->load->model("dashboardmodel");
		$this->load->helper('common_helper');
		$this->load->helper('kopindosat');
		$this->load->model('m_wimapi');
	}

  function updateunit(){
		// header('Access-Control-Allow-Origin: *');
		// header("Access-Control-Allow-Methods: GET, OPTIONS, POST");
		header("Content-Type: application/json");

		$token      = "BIBaW5kNGhraTX0OnAwNXRkNHQ0a2k0dA16";
		$postdata   = json_decode(file_get_contents("php://input"));
		$allvehicle = 0;
		$now        = date("Ymd");

		$headers = null;
		if (isset($_SERVER['Authorization'])) {
      $headers = trim($_SERVER["Authorization"]);
    }else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
      $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    }else if (function_exists('apache_request_headers')) {
      $requestHeaders = apache_request_headers();
      // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
      $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
      if (isset($requestHeaders['Authorization']))
      {
        $headers = trim($requestHeaders['Authorization']);
      }
    }

		// echo json_encode($postdata);
		// exit;


	$feature = array();


    $data       = array(
      "integrationwim_transactionID"          => $postdata->TransactionID,
      "integrationwim_penimbanganStartUTC"    => $postdata->PenimbanganStartUTC,
      "integrationwim_penimbanganStartLocal"  => $postdata->PenimbanganStartLocal,
      "integrationwim_penimbanganFinishUTC"   => $postdata->PenimbanganFinishUTC,
      "integrationwim_penimbanganFinishLocal" => $postdata->PenimbanganFinishLocal,
      "integrationwim_beratTiapGandar"        => $postdata->BeratTiapGandar,
			"integrationwim_totalGandar" 	          => $postdata->TotalGandar,
      "integrationwim_gross"                  => $postdata->Gross,
      "integrationwim_tare"                   => $postdata->Tare,
      "integrationwim_netto"                  => $postdata->Netto,
      "integrationwim_averageSpeed"           => $postdata->AverageSpeed,
      "integrationwim_weightBalance"          => $postdata->WeightBalance,
      "integrationwim_rfid"                   => $postdata->RFID,
      "integrationwim_noMesin"                => $postdata->NoMesin,
			"integrationwim_noRangka"               => $postdata->NoRangka,
      "integrationwim_truckType"              => $postdata->TruckType,
      "integrationwim_providerId"             => $postdata->ProviderId,
      "integrationwim_truckID"                => $postdata->TruckID,
      "integrationwim_haulingContractor"      => $postdata->HaulingContractor,
      "integrationwim_status"                 => $postdata->Status,
      "integrationwim_truckImage"             => $postdata->TruckImage,
			"integrationwim_truckImage2"            => $postdata->TruckImage2,
			"integrationwim_created_date"           => date("Y-m-d H:i:s")
    );

    $payload       = array(
      "TransactionID"          => $postdata->TransactionID,
      "PenimbanganStartUTC"    => $postdata->PenimbanganStartUTC,
      "PenimbanganStartLocal"  => $postdata->PenimbanganStartLocal,
      "PenimbanganFinishUTC"   => $postdata->PenimbanganFinishUTC,
      "PenimbanganFinishLocal" => $postdata->PenimbanganFinishLocal,
      "BeratTiapGandar"        => $postdata->BeratTiapGandar,
			"TotalGandar" 		       => $postdata->TotalGandar,
      "Gross"                  => $postdata->Gross,
      "Tare"                   => $postdata->Tare,
      "Netto"                  => $postdata->Netto,
      "AverageSpeed"           => $postdata->AverageSpeed,
      "WeightBalance"          => $postdata->WeightBalance,
      "RFID"                   => $postdata->RFID,
      "NoMesin"                => $postdata->NoMesin,
			"NoRangka"               => $postdata->NoRangka,
      "TruckType"              => $postdata->TruckType,
      "ProviderId"             => $postdata->ProviderId,
      "TruckID"                => $postdata->TruckID,
      "HaulingContractor"      => $postdata->HaulingContractor,
      "Status"                 => $postdata->Status,
      "TruckImage"             => $postdata->TruckImage,
			"TruckImage2"            => $postdata->TruckImage2,
    );

		$m1     = date("F", strtotime($postdata->PenimbanganStartLocal));
		$year   = date("Y", strtotime($postdata->PenimbanganStartLocal));
		$report = "historikal_integrationwim_unit_";

		switch ($m1)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			break;
			case "April":
            $dbtable = $report."april_".$year;
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			break;
			case "September":
            $dbtable = $report."september_".$year;
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			break;
			case "November":
            $dbtable = $report."november_".$year;
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			break;
		}

		// echo "<pre>";
		// var_dump($dbtable);die();
		// echo "<pre>";

		//hardcode ke 1 table
		$dbtable = "historikal_integrationwim_unit";



		//condition checking
		$feature = array();
		if($headers != $token){
			$feature["code"] = 400;
			$feature["msg"]    = "INVALID TOKEN";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}


		if(!isset($postdata->ProviderId) || $postdata->ProviderId == ""){
			$feature["code"] = 400;
			$feature["msg"]    = "INVALID USER ID";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}else{
			//hanya user yg terdaftar yg bisa akes API
			$this->db->where("api_user",$postdata->ProviderId);
			$this->db->where("api_token",$headers);
			$this->db->where("api_status",1);
			$this->db->where("api_flag",0);
			$q = $this->db->get("api_user");
			if($q->num_rows == 0)
			{
				$feature["code"] = 400;
				$feature["msg"]    = "USER & TOKEN NOT AVAILABLE";
				$feature["payload"]    = $payload;
				echo json_encode($feature);
				exit;
			}
		}
		//mandatory
		if(!isset($postdata->TransactionID) || $postdata->TransactionID == ""){
			$feature["code"] = 400;
			$feature["msg"]    = "NO DATA TRANSACTION ID";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}

		if(!isset($postdata->NoMesin) || $postdata->NoMesin == ""){
			$feature["code"] = 400;
			$feature["msg"]    = "NO DATA NOMOR MESIN";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}

		if(!isset($postdata->NoRangka) || $postdata->NoRangka == ""){
			$feature["code"] = 400;
			$feature["msg"]    = "NO DATA NOMOR RANGKA";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}

    $cekTransactionID = $this->m_wimapi->checkintable($dbtable, $postdata->TransactionID);
      if (sizeof($cekTransactionID) > 0) {
        $updateData = $this->m_wimapi->updateData($dbtable, "integrationwim_TransactionID", $postdata->TransactionID, $data);
          if ($updateData) {
            echo json_encode(array("code" => 200, "msg" => "ok", "payload" => $payload));
          }else {
            echo json_encode(array("code" => 400, "msg" => "Failed Update Data", "payload" => $payload));
          }
      }else {
        $insert = $this->m_wimapi->insertData($dbtable, $data);
          if ($insert) {
            echo json_encode(array("code" => 200, "msg" => "ok", "payload" => $payload));
          }else {
            echo json_encode(array("code" => 400, "msg" => "Failed Insert Data", "payload" => $payload));
          }
      }

    // echo "<pre>";
    // var_dump($cekTransactionID);die();
    // echo "<pre>";
    // printf("DONE \r\n");
  }

	function updatetare(){
		header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Methods: GET, OPTIONS, POST");
		header("Content-Type: application/json");

		$token      = "BIBaW5kNGhraTX0OnAwNXRkNHQ0a2k0dA16";
		$postdata   = json_decode(file_get_contents("php://input"));
		$allvehicle = 0;
		$now        = date("Ymd");

		$headers = null;
		if (isset($_SERVER['Authorization'])) {
			$headers = trim($_SERVER["Authorization"]);
		}else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
			$headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
		}else if (function_exists('apache_request_headers')) {
			$requestHeaders = apache_request_headers();
			// Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
			$requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
			if (isset($requestHeaders['Authorization']))
			{
				$headers = trim($requestHeaders['Authorization']);
			}
		}

		// echo "<pre>";
    // var_dump($postdata);die();
    // echo "<pre>";



		$data       = array(
			"integrationwim_transactionID"          => $postdata->TransactionID,
			"integrationwim_penimbanganStartUTC"    => $postdata->PenimbanganStartUTC,
			"integrationwim_penimbanganStartLocal"  => $postdata->PenimbanganStartLocal,
			"integrationwim_penimbanganFinishUTC"   => $postdata->PenimbanganFinishUTC,
			"integrationwim_penimbanganFinishLocal" => $postdata->PenimbanganFinishLocal,
			"integrationwim_beratTiapGandar"        => $postdata->BeratTiapGandar,
			"integrationwim_totalGandar" 	          => $postdata->TotalGandar,
			"integrationwim_gross"                  => $postdata->Gross,
			"integrationwim_tare"                   => $postdata->Tare,
			"integrationwim_netto"                  => $postdata->Netto,
			"integrationwim_averageSpeed"           => $postdata->AverageSpeed,
			"integrationwim_weightBalance"          => $postdata->WeightBalance,
			"integrationwim_rfid"                   => $postdata->RFID,
			"integrationwim_noMesin"                => $postdata->NoMesin,
			"integrationwim_noRangka"               => $postdata->NoRangka,
			"integrationwim_truckType"              => $postdata->TruckType,
			"integrationwim_providerId"             => $postdata->ProviderId,
			"integrationwim_truckID"                => $postdata->TruckID,
			"integrationwim_haulingContractor"      => $postdata->HaulingContractor,
			"integrationwim_status"                 => $postdata->Status,
			"integrationwim_truckImage"             => $postdata->TruckImage,
			"integrationwim_truckImage2"            => $postdata->TruckImage2,
			"integrationwim_created_date"           => date("Y-m-d H:i:s")
		);

		$payload       = array(
			"TransactionID"          => $postdata->TransactionID,
			"PenimbanganStartUTC"    => $postdata->PenimbanganStartUTC,
			"PenimbanganStartLocal"  => $postdata->PenimbanganStartLocal,
			"PenimbanganFinishUTC"   => $postdata->PenimbanganFinishUTC,
			"PenimbanganFinishLocal" => $postdata->PenimbanganFinishLocal,
			"BeratTiapGandar"        => $postdata->BeratTiapGandar,
			"TotalGandar" 	         => $postdata->TotalGandar,
			"Gross"                  => $postdata->Gross,
			"Tare"                   => $postdata->Tare,
			"Netto"                  => $postdata->Netto,
			"AverageSpeed"           => $postdata->AverageSpeed,
			"WeightBalance"          => $postdata->WeightBalance,
			"RFID"                   => $postdata->RFID,
			"NoMesin"                => $postdata->NoMesin,
			"NoRangka"               => $postdata->NoRangka,
			"TruckType"              => $postdata->TruckType,
			"ProviderId"             => $postdata->ProviderId,
			"TruckID"                => $postdata->TruckID,
			"HaulingContractor"      => $postdata->HaulingContractor,
			"Status"                 => $postdata->Status,
			"TruckImage"             => $postdata->TruckImage,
			"TruckImage2"            => $postdata->TruckImage2,
		);

		//condition checking
		$feature = array();
		if($headers != $token){
			$feature["code"] = 400;
			$feature["msg"]    = "INVALID TOKEN";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}


		if(!isset($postdata->ProviderId) || $postdata->ProviderId == ""){
			$feature["code"] = 400;
			$feature["msg"]    = "INVALID USER ID";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}else{
			//hanya user yg terdaftar yg bisa akes API
			$this->db->where("api_user",$postdata->ProviderId);
			$this->db->where("api_token",$headers);
			$this->db->where("api_status",1);
			$this->db->where("api_flag",0);
			$q = $this->db->get("api_user");
			if($q->num_rows == 0)
			{
				$feature["code"] = 400;
				$feature["msg"]    = "USER & TOKEN NOT AVAILABLE";
				$feature["payload"]    = $payload;
				echo json_encode($feature);
				exit;
			}
		}
		//mandatory
		if(!isset($postdata->TransactionID) || $postdata->TransactionID == ""){
			$feature["code"] = 400;
			$feature["msg"]    = "NO DATA TRANSACTION ID";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}

		if(!isset($postdata->NoMesin) || $postdata->NoMesin == ""){
			$feature["code"] = 400;
			$feature["msg"]    = "NO DATA NOMOR MESIN";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}

		if(!isset($postdata->NoRangka) || $postdata->NoRangka == ""){
			$feature["code"] = 400;
			$feature["msg"]    = "NO DATA NOMOR RANGKA";
			$feature["payload"]    = $payload;
			echo json_encode($feature);
			exit;
		}



		$cekTransactionID = $this->m_wimapi->checkintable("historikal_integrationwim_tare", $postdata->TransactionID);

		// echo "<pre>";
		// var_dump($data);die();
		// echo "<pre>";

			if (sizeof($cekTransactionID) > 0) {
				$updateDataPertama = $this->m_wimapi->updateData("historikal_integrationwim_tare", "integrationwim_transactionID", $postdata->TransactionID, $data);

				// echo "<pre>";
				// var_dump($updateDataPertama);die();
				// echo "<pre>";

				if ($updateDataPertama) {
					// SINKRONISASI DENGAN MASTER PORTAL DI WEBTRACKINGMASTER_PORTAL ULTRON
					/*Key-ID nya adalah no_lambung dan no_rangka.
					Jika ditemukan, maka data update.
					Jika kombinasi no_lambung dan no_rangka tidak ditemukan data existing-nya, maka prioritas pengecekan adalah no_rangka;
					Jika ditemukan, maka data update
					Jika tidak ditemukan, maka create new SEMENTARA INI TIDAK DIPAKAI*/
					// $updateDatatoMasterPortal = $this->m_wimapi->checkInMasterPortal("master_portal", $postdata->NoRangka, $postdata->TruckID);
					// 	if ($updateDatatoMasterPortal) {
					// 		$dataforupdate = array(
					// 			"master_portal_gps_tare"         => $postdata->Tare,
					// 			"master_portal_gps_tare_updated" => date("Y-m-d H:i:s")
					// 		);
					// 		$updateData2 = $this->m_wimapi->updateData2("master_portal", "master_portal_norangka", $postdata->NoRangka, $dataforupdate);
					// 			if ($updateData2) {
					// 				echo json_encode(array("code" => 200, "msg" => "ok", "payload" => $payload));
					// 			}else {
					// 				echo json_encode(array("code" => 400, "msg" => "Failed Update Data In Master Portal", "payload" => $payload));
					// 			}
					// 	}else {
					// 		echo json_encode(array("code" => 400, "msg" => "Data Not Found in Master Portal", "payload" => $payload));
					// 	}
					echo json_encode(array("code" => 200, "msg" => "ok", "payload" => $payload));
				}else {
					// echo json_encode(array("code" => 200, "msg" => "Failed Update Data", "payload" => $payload));
				}
			}else {
				$insert = $this->m_wimapi->insertData("historikal_integrationwim_tare", $data);
					if ($insert) {
						$updateDatatoMasterPortal = $this->m_wimapi->checkInMasterPortal("master_portal", $postdata->NoRangka, $postdata->TruckID);
							if ($updateDatatoMasterPortal) {
								$dataforupdate = array(
									"master_portal_gps_tare"         => $postdata->Tare,
									"master_portal_gps_tare_updated" => date("Y-m-d H:i:s")
								);
								$updateData2 = $this->m_wimapi->updateData2("master_portal", "master_portal_norangka", $postdata->NoRangka, $dataforupdate);
									if ($updateData2) {
										echo json_encode(array("code" => 200, "msg" => "ok", "payload" => $payload));
									}else {
										echo json_encode(array("code" => 400, "msg" => "Failed Update Data", "payload" => $payload));
									}
							}else {
								echo json_encode(array("code" => 400, "msg" => "Failed Update Data", "payload" => $payload));
							}
					}else {
						echo json_encode(array("code" => 400, "msg" => "Failed Insert Data", "payload" => $payload));
					}
			}

		// echo "<pre>";
		// var_dump($cekTransactionID);die();
		// echo "<pre>";
		// printf("DONE \r\n");
	}

// 	{
//     "TransactionID" : "332132313313",
//     "PenimbanganStartUTC" : "2021-10-21 08:21:00",
//     "PenimbanganStartLocal" : "2021-10-21 14:21:00",
//     "PenimbanganFinishUTC" : "2021-10-21 08:26:00",
//     "PenimbanganFinishLocal" : "2021-10-21 14:26:00",
//     "BeratTiapGandar" : "10,15,30,45",
//     "TotalGandar" : 4,
//     "Gross" : 46320,
//     "Tare" : 15280,
//     "Netto" : 31040,
//     "AverageSpeed" : 10.5,
//     "WeightBalance" : 5,
//     "RFID" : "ABC123456",
//     "NoMesin" : "ABC123456",
//     "NoRangka" : "RK554234",
//     "TruckType" : "DDT",
//     "ProviderId" : 4203,
//     "TruckID" : "EST 002",
//     "HaulingContractor" : "RBT",
//     "Status" : "ACTUAL",
//     "TruckImage" : "https://teman-indobara.sinarmasmining.com/assets/bib/images/transactionid00001.jpg"
// }

















}
