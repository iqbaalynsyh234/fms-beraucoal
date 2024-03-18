<?php
include "base.php";

class Driver_cronjob extends Base {

	function Driver_cronjob()
	{
		parent::Base();
		$this->load->helper('common_helper');
		$this->load->helper('email');
		$this->load->library('email');
		$this->load->model("dashboardmodel");
		$this->load->helper('common');
		$this->load->model("driver_model");
		$this->load->model("gpsmodel");
		

	}

	function upload_gdrive(){
		date_default_timezone_set("Asia/Jakarta");
		$nowdate = date('Y-m-d H:i:s');
		$this->dbts = $this->load->database("webtracking_ts",true);
		
		printf("===Starting cron . . . at %s \r\n", $nowdate);
		printf("======================================\r\n");
		
			//$userlist = array('4201');
			$this->dbts->order_by("absensi_id","asc");
			$this->dbts->where("absensi_flag", 0);
			$this->dbts->where("absensi_upload", 0);//belum di upload
			$this->dbts->where("absensi_id", 17);
			$q = $this->dbts->get("ts_driver_absensi");
			$rows = $q->result();
			$total = count($rows);
			//print_r($rows);
			//upload gdrive
			$last_oauth = $this->getlast_OAUTH(4408); //temanindobara
			//print_r($last_oauth);exit();
			if($last_oauth == ""){
				printf("===NO DATA ACCESS TOKEN!! \r\n");
			}else{
				
				$j = 1;
				for ($i=0;$i<count($rows);$i++)
				{
					printf("===Process Upload Selfie Driver For %s %s (%d/%d) \r\n", $rows[$i]->absensi_driver_name, $rows[$i]->absensi_clock_in, $j, $total);
					$data_to_upload = $this->upload_image($rows[$i]->absensi_driver_idcard, $rows[$i]->absensi_driver_name, $rows[$i]->absensi_clock_in, $rows[$i]->absensi_shift_type, $rows[$i]->absensi_photo_txt);
					$j++;
				}
			}
			
		$this->db->close();
		$this->db->cache_delete_all();
		
		$enddate = date('Y-m-d H:i:s');
		printf("===FINISH Cron start %s to %s \r\n", $nowdate, $enddate);
		printf("============================== \r\n");
		//printf("===SLEEP.... %s s \r\n", $interval);
		//sleep($interval);
		//$this->lastposition($userid);
		
	}
	
	function getlast_OAUTH($userid){
		$key = 0;
		$this->dbts       = $this->load->database('webtracking_ts', true);
		$this->dbts->select("token_access,");
		$this->dbts->order_by("token_created", "desc");
		$this->dbts->where("token_status", 1);
		$this->dbts->where("token_user", $userid);
		$this->dbts->limit(1);
		$q             = $this->dbts->get("ts_access_token");
		$row    = $q->row();
		if(count($row)>0){
			$key = $row->token_access;
		}
		
		return $key;
	}
	//$data_to_upload = $this->upload_image($rows[$i]->absensi_driver_idcard, $rows[$i]->absensi_driver_name, $rows[$i]->absensi_clock_in, $rows[$i]->absensi_shift_type, $rows[$i]->absensi_photo_txt);
	function upload_image($idcard,$drivername,$clockin,$shift,$imagebase64) {
		
		//<SIMPER>_<NAMA_DRIVER>_<TGL>_<SHIFTTYPE>.jpg
		
		$drivername = str_replace(' ', '_', $drivername);
		$clockin_new = date("dmY_his");
		$file_name = $idcard."_".$drivername."_".$clockin_new."_".$shift.".jpg";
		
		/* $img = str_replace('data:image/jpeg;base64,', '', $imagebase64);
		$img = str_replace(' ', '+', $img);
		$data = base64_decode($img);
		$file = "../assets/media/absensi/".$file_name; 
		$success = file_put_contents($file, $data);
		
		print_r($success);exit(); */
		$img = str_replace('data:image/jpeg;base64,', '', $imagebase64);
		$image = base64_decode($img);
		
		$config['upload_path']   = './assets/media/absensi/';
		$config['allowed_types'] = 'gif|jpeg|jpg|png';
		$config['max_size']      = '200';
		$config['max_width']     = '320';
		$config['max_height']    = '300';
		$config['file_name']     = $image;
		$config['overwrite']     = false;
		
		$this->load->library('upload', $config);
		
		if (!$this->upload->do_upload()) {
			$result = $this->upload->display_errors();
			printf("===ERORR UPLOAD %s \r\n", $result);
			
		}else {
			$data = $this->upload->data(); print_r($data);exit();
			$result = "OK";
			printf("===ERORR SUCCESS %s \r\n", $result);
		}
		
		return $result;
	}
	

}
