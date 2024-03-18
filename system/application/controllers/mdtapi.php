<?php
include "base.php";
class Mdtapi extends Base {

	function Mdtapi()
	{
		parent::Base();	
		$this->load->model("gpsmodel");
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
		$this->load->model("dashboardmodel");
		$this->load->helper('common_helper');
		$this->load->helper('kopindosat');
	}		
	function getneworder()
	{
		
		header('Access-Control-Allow-Origin:*');
		//$id = "7c9bb4e8cedb0bb0";
		$id = $this->input->post('id');
		$this->dbmdt = $this->load->database("webtracking_ts",true);
		$this->dbmdt->order_by("order_datetime","asc");
		$this->dbmdt->where("order_imei_mdt",$id);
		$this->dbmdt->where("order_status",0); //unserved;
		$this->dbmdt->where("order_flag",0);
		$q = $this->dbmdt->get("ts_order");
		$rows = $q->row(); 
		$this->dbmdt->close();
		
		//$this->params["data"] = $rows;
		$callback["data"] = $rows;
		$callback["total_data"] = count($rows);
		
		
		echo json_encode($callback);
	
	}
	
	function getnewdriver()
	{
		ini_set('display_errors', 1);
		header('Access-Control-Allow-Origin:*');
		//$id = "7c9bb4e8cedb0bb0";
		$id = $this->input->post('id');
		$this->dbmdt = $this->load->database("default",true);
		$this->dbmdt->select("vehicle_mv03");
		$this->dbmdt->order_by("vehicle_id","desc");
		$this->dbmdt->where("vehicle_mdt",$id);
		$this->dbmdt->where("vehicle_status <>",3);
		$q = $this->dbmdt->get("vehicle");
		$row = $q->row(); 
		$this->dbmdt->close();
		$total_data = count($row);
		if($total_data > 0){
			//get driver change berdasarkan imei MV03
			$id_mv03 = $row->vehicle_mv03; 
			$this->dbts = $this->load->database("webtracking_ts",true);
			$this->dbts->select("change_driver_id");
			$this->dbts->order_by("change_driver_time","desc");
			$this->dbts->where("change_driver_flag",0);
			$this->dbts->where("change_imei",$id_mv03);
			$this->dbts->limit(1);
			$qdriver = $this->dbts->get("ts_driver_change");
			$rowdriver = $qdriver->row(); 
			$this->dbts->close();
			if(count($rowdriver)>0){
				
				//GET DRIVER berdasarkan ID SIMPER
				$id_simper = $rowdriver->change_driver_id;
				$this->dbtrans = $this->load->database("transporter",true);
				$this->dbtrans->select("driver_id,driver_name,driver_idcard");
				$this->dbtrans->order_by("driver_id","desc");
				$this->dbtrans->where("driver_idcard",$id_simper);
				$this->dbtrans->where("driver_status",1);
				$this->dbtrans->limit(1);
				$qdriver_master = $this->dbtrans->get("driver");
				$rowdriver_master = $qdriver_master->row(); 
				
				if(count($rowdriver_master)>0){
					$data = $rowdriver_master;
					$drivername = strtoupper($data->driver_name);
					$driverid = $data->driver_id;
					
					//get driver image
					$this->dbtrans->select("driver_image_full_path");
					$this->dbtrans->order_by("driver_image_id","desc");
					$this->dbtrans->where("driver_image_driver_id",$driverid);
					$qdriverimage = $this->dbtrans->get("driver_image");
					$rowdriver_image = $qdriverimage->row();
					if(count($rowdriver_image)>0){
						
						$ex_driver_image = explode("/",$rowdriver_image->driver_image_full_path);
						$data_image = "http://".$ex_driver_image[4]."/".$ex_driver_image[6]."/".$ex_driver_image[7]."/".$ex_driver_image[8]."/".$ex_driver_image[9]."/".$ex_driver_image[10];
						$callback["data_image"] = $data_image;
					}else{
						
						$callback["data_image"] = "";
					}
					
					$this->dbtrans->close();
					$voice = 'SELAMAT DATANG BAPAK '.$drivername.'.'.' PERKENALKAN, SAYA ADALAH APLIKASI ASISTEN PENGEMUDI. JIKA NAMA TIDAK SESUAI. SILAHKAN PINDAI KEMBALI WAJAH ANDA.';
					$callback["data"] = $data;
					$callback["total_data"] = count($data);
					$callback["voice"] = $voice;
					
				}
				//jika driver tidak ada di master data
				else
				{
					$callback["data"] = 'Driver belum terdftar';
					$callback["total_data"] = 0;
					$callback["data_image"] = "";
					$callback["voice"] = 'DRIVER BELUM TERDAFTAR. SILAHKAN REGISTRASI WAJAH ANDA.';
				}
					
			}
			//jika tidak ada data di driver change / id simper kosong
			else
			{
				$callback["data"] = 'Belum Ada Data Driver';
				$callback["total_data"] = 0;
				$callback["data_image"] = "";
				$callback["voice"] = 'BELUM ADA DATA DRIVER. SILAHKAN PINDAI WAJAH ANDA KEMBALI.';
			}
		}
		//jika tidak ada data master data mobil
		else
		{
			$callback["data"] = 'Belum Terintegrasi';
			$callback["total_data"] = 0;
			$callback["data_image"] = "";
			$callback["voice"] = 'PERANGKAT BELUM TERINTEGRASI DENGAN UNIT KENDARAAN.';
		}
		
		echo json_encode($callback);
	
	}
	
	function terimastatus()
	{
		header('Access-Control-Allow-Origin:*');
		$order_id = isset($_POST['order_id']) ? trim($_POST['order_id']) : "";
		$order_driver_name = isset($_POST['order_driver_name']) ? trim($_POST['order_driver_name']) : "";
		$order_driver_idcard = isset($_POST['order_driver_idcard']) ? trim($_POST['order_driver_idcard']) : "";
		$order_status = isset($_POST['order_status']) ? trim($_POST['order_status']) : "";
		$order_imei = isset($_POST['order_imei']) ? trim($_POST['order_imei']) : "";
		
		$updatetime_ex = date("Y-m-d H:i:s");
		$updatetime = date('Y-m-d H:i:s', strtotime("+1 hours", strtotime($updatetime_ex)));
		
		if($order_status == ""){
			
			exit(json_encode(array("m"=>"GAGAL UPDATE JOB ORDER!","e"=>true)));
			return;
		}else{
			
			$status_value = 2;
		}
		
		if($order_id == ""){
			exit(json_encode(array("m"=>"ID JOB ORDER KOSONG!","e"=>true)));
			return;
		}
		if($order_driver_name == "" || $order_driver_idcard == ""){
			exit(json_encode(array("m"=>"TIDAK ADA DATA DRIVER. SILAHKAN VERIFIKASI WAJAH KEMBALI!","e"=>true)));
			return;
		}
		if($order_imei == ""){
			exit(json_encode(array("m"=>"IMEI DEVICE KOSONG!","e"=>true)));
			return;
		}
		
		$this->db = $this->load->database("webtracking_ts",true);
		unset($data);
		$data['order_driver_idcard'] = $order_driver_idcard;
		$data['order_driver_name'] = $order_driver_name;
		$data['order_mdt_processdatetime'] = $updatetime;
		$data['order_status'] = $status_value;
		
		$this->db->where("order_id",$order_id);
		$this->db->where("order_imei_mdt",$order_imei);
		$this->db->limit(1);
		$this->db->update("ts_order",$data);
		$this->db->close();
		exit(json_encode(array("m"=>"TUGAS BERHASIL DITERIMA. SELALU UTAMAKAN KESELAMATAN KERJA.")));
		
		return;
	}
	
	function selesaistatus()
	{
		header('Access-Control-Allow-Origin:*');
		$order_id = isset($_POST['order_id']) ? trim($_POST['order_id']) : "";
		$order_driver_name = isset($_POST['order_driver_name']) ? trim($_POST['order_driver_name']) : "";
		$order_driver_idcard = isset($_POST['order_driver_idcard']) ? trim($_POST['order_driver_idcard']) : "";
		$order_status = isset($_POST['order_status']) ? trim($_POST['order_status']) : "";
		$order_imei = isset($_POST['order_imei']) ? trim($_POST['order_imei']) : "";
		
		$updatetime_ex = date("Y-m-d H:i:s");
		$updatetime = date('Y-m-d H:i:s', strtotime("+1 hours", strtotime($updatetime_ex)));
		
		if($order_status == ""){
			
			exit(json_encode(array("m"=>"GAGAL UPDATE JOB ORDER!","e"=>true)));
			return;
		}else{
			
			$status_value = 3; //selesai 
		}
		
		if($order_id == ""){
			exit(json_encode(array("m"=>"ID JOB ORDER KOSONG!","e"=>true)));
			return;
		}
		if($order_driver_name == "" || $order_driver_idcard == ""){
			exit(json_encode(array("m"=>"TIDAK ADA DATA DRIVER. SILAHKAN VERIFIKASI WAJAH KEMBALI!","e"=>true)));
			return;
		}
		if($order_imei == ""){
			exit(json_encode(array("m"=>"IMEI DEVICE KOSONG!","e"=>true)));
			return;
		}
		
		$this->db = $this->load->database("webtracking_ts",true);
		unset($data);
		$data['order_driver_idcard'] = $order_driver_idcard;
		$data['order_driver_name'] = $order_driver_name;
		$data['order_mdt_completedatetime'] = $updatetime;
		$data['order_status'] = $status_value;
		
		$this->db->where("order_id",$order_id);
		$this->db->where("order_imei_mdt",$order_imei);
		$this->db->limit(1);
		$this->db->update("ts_order",$data);
		$this->db->close();
		exit(json_encode(array("m"=>"TUGAS SELESAI. TERIMA KASIH")));
		
		return;
	}

	function getcommandcenter()
	{
		header('Access-Control-Allow-Origin:*');
		$imei = isset($_POST['uid']) ? trim($_POST['uid']) : "";
		$this->db = $this->load->database("webtracking_ts",true);
		$this->db->order_by("voice_datetime", "asc");
		$this->db->where("voice_status", 0);
		$this->db->where("voice_flag", 0);
		$this->db->limit(1);
		$q = $this->db->get("ts_voice");
		$data = $q->row();
		
		exit(json_encode(array("data"=>$data)));
		return;
	}
	
	function voiceupdate()
	{
		header('Access-Control-Allow-Origin:*');
		$id = isset($_POST['id']) ? trim($_POST['id']) : "";
		
		unset($data);
		$data['voice_status'] = 1;
		$this->db = $this->load->database("webtracking_ts",true);
		$this->db->where("voice_id",$id);
		$this->db->update("ts_voice",$data);
		exit(json_encode(array("m"=>"Update Voice Success")));
		return;
	}
	
	function sendlocation()
	{
		header('Access-Control-Allow-Origin:*');
		$device_imei = isset($_POST['device_imei']) ? trim($_POST['device_imei']) : "";
		$coord = isset($_POST['coord']) ? trim($_POST['coord']) : "";
		$lat = isset($_POST['lat']) ? trim($_POST['lat']) : "";
		$lng = isset($_POST['lng']) ? trim($_POST['lng']) : "";
		$speed_kph = isset($_POST['speed_kph']) ? trim($_POST['speed_kph']) : "";
		$direction = isset($_POST['direction']) ? trim($_POST['direction']) : "";
		$gpstime = isset($_POST['gpstime']) ? trim($_POST['gpstime']) : "";
		$gpstime_new = date('Y-m-d H:i:s', strtotime($gpstime));
		
		
		$this->db = $this->load->database("webtracking_ts",true);
		unset($data);
		$data['pos_imei'] = $device_imei;
		$data['pos_coord'] = $coord;
		$data['pos_latitude'] = $lat;
		$data['pos_longitude'] = $lng;
		$data['pos_time'] = $gpstime_new;
		$data['pos_speed'] = $speed_kph;
		$data['pos_direction'] = $direction;
		
		$this->db->insert("ts_pos",$data);
		$this->db->close();
		exit(json_encode(array("m"=>"LOCATION OK")));
		
		return;
	}
}