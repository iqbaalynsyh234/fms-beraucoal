<?php
include "base.php";

class Fib extends Base {

	function Fib()
	{
		parent::Base();
		$this->load->helper('common_helper');
		$this->load->library('email');
		$this->load->model("dashboardmodel");

	}
	function index()
	{
		if (! isset($this->sess->user_id)){redirect(base_url());}

		$time_slider = 5*1000; //default = 60 detik
		$app_id = $this->sess->user_id;

		$companydata = $this->dashboardmodel->getcompany_id($this->sess->user_id);

		$this->db->order_by("fib_config_id","desc");
		$this->db->limit(1);
		$this->db->where("fib_config_status",1);
		$this->db->where("fib_config_app_id",$app_id);
		$this->db->where("fib_config_flag",0);
		$q = $this->db->get("fib_config");
		$config = $q->row();
		if(count($config)>0){
			$time_slider = $config->fib_config_duration*1000;
		}
		$this->params["time_slider"]    = $time_slider;
		$this->params['companydata']    = $companydata;
		$this->params['code_view_menu'] = "report";

		$this->params["header"]         = $this->load->view('dashboard/header', $this->params, true);	
		$this->params["sidebar"]        = $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('dashboard/chatsidebar', $this->params, true);
		$this->params["content"]        = $this->load->view("fib/fib_view", $this->params, true);
		$this->load->view("dashboard/template_dashboard", $this->params);
	}
	function search()
	{
		if (! isset($this->sess->user_id)){redirect(base_url());}

		if($this->sess->user_login == "demo_transporter"){
			$app_id = 1147;
		}else{
			$app_id = $this->sess->user_id;
		}

		/*$this->db->select("vehicle_no,vehicle_name,vehicle_id,vehicle_device,vehicle_user_id,fib_loaded_status,fib_co_ischeck,fib_sj_status,fib_uj_status,fib_co_status,
						   fib_eta_status,fib_arrival_time_status,fib_customer_status,fib_customer2_status,fib_customer3_status,fib_noso_status,fib_remark_status,fib_remark,
						   fib_id,fib_vehicle,fib_driver,fib_loaded,fib_sj,fib_uj,fib_co,fib_eta,fib_arrival_time,fib_arrival_name,fib_customer,fib_noso,fib_remark,fib_status,
						   fib_customer2,fib_customer3,fib_out_time,fib_out_time_status,fib_out_name,fib_last_geofence,fib_av_plus,fib_customer_unique,fib_customer2_unique,fib_customer3_unique,
						   fib_note
						");*/
		/*$this->db->order_by("fib_customer_status","desc");
		$this->db->order_by("fib_customer","asc"); */
		$this->db->order_by("fib_sj","desc");
		$this->db->order_by("fib_vehicle_no","asc");
		$this->db->where("fib_vehicle_user_id",$app_id);
		$this->db->where("fib_status",1);
		$this->db->where("fib_flag",0);
		//$this->db->join("fib","vehicle_device=fib_vehicle", "left");
		$q = $this->db->get("fib");
		$data = $q->result();
		$this->params["data"] = $data;

		$total = count($data);
		$this->params["total"] = $total;

				$now_date = date("Y-m-d");
				$now_datetime = date("Y-m-d H:i:s");
				$now_datetime_sec = strtotime($now_datetime);
				$style = "";
				$fib_loaded_sec = 0;
				$muat_gantung_limit = 4*3600; //4 jam
				$muat_gantung_color = "#1E90FF"; //DodgerBlue
				$loading_color = "green";
				$gps_trouble_color = "red";
				$customer_warning_color = "yellow";
				$customer_warning_limit = 4*3600; //4jam di customer

				$total_available = $total;
				$total_muatgantung = 0;
				$total_gpstrouble = 0;
				$total_loading = 0;
				$total_warning = 0;
				$total_co = 0;


			for($i=0;$i<count($data); $i++){

				//gps trouble
				if(isset($data[$i]->fib_remark) && ($data[$i]->fib_remark_status == 1) && (($data[$i]->fib_remark == "GPS TROUBLE") || ($data[$i]->fib_remark == "SERVICE AREA"))  ){
					if($data[$i]->fib_remark_status == 1){
						$style = $gps_trouble_color;
						$total_available = $total_available - 1 ;
						$total_gpstrouble = $total_gpstrouble + 1;
						$data[$i]->kondisi_merah = 1;
						$data[$i]->kondisi_putih = 0;
					}
				}
				else
				{
					//CO HIJAU (rev awal juni)
					if(isset($data[$i]->fib_co) && ($data[$i]->fib_co_status == 1)){

						$total_co = $total_co + 1;
						//$total_gpstrouble = $total_gpstrouble - 1;
						$total_available = $total_available - 1;
						$data[$i]->kondisi_hijau = 1;
						//$data[$i]->kondisi_merah = 0;
						$data[$i]->kondisi_putih = 0;


						//di sudah sampai customer + lebih dari 4 jam di customer (out status = 0)
						if(isset($data[$i]->fib_arrival_time) && ($data[$i]->fib_arrival_time_status == 1) && ($data[$i]->fib_out_time_status == 0)){
							$fib_arrival_time_sec = strtotime($data[$i]->fib_arrival_time);
								$delta_customer = $now_datetime_sec - $fib_arrival_time_sec;
								//status >= 4 jam belum keluar dari arrival
								if(($delta_customer >= $customer_warning_limit) && ($data[$i]->fib_arrival_time_status == 1)){
									$style = $customer_warning_color;

									$total_warning = $total_warning + 1;
									$total_co = $total_co - 1;
									$data[$i]->kondisi_kuning = 1;
									$data[$i]->kondisi_hijau = 0;
								}
						}

					}

					//muat gantung
					if(isset($data[$i]->fib_loaded) && ($data[$i]->fib_co_ischeck == 0) && ($data[$i]->fib_sj_status == 0) ){

						if($data[$i]->fib_loaded_status == 1){
							$fib_loaded_sec = strtotime($data[$i]->fib_loaded);
							//status >= 4 jam belum keluar dari KIM
							$delta_loading = $now_datetime_sec - $fib_loaded_sec;
							if(($delta_loading >= $muat_gantung_limit) && ($data[$i]->fib_co_status == 0)){
								$style = $muat_gantung_color;
								$total_muatgantung = $total_muatgantung + 1;
								$total_available = $total_available - 1 ;
								//$total_gpstrouble = $total_gpstrouble - 1;
								$data[$i]->kondisi_biru = 1;
								$data[$i]->kondisi_putih = 0;
								//$data[$i]->kondisi_merah = 0;

							}
						}
					}


				}

			}

			if($total_available <= 0){
				$total_available = 0;
			}

			$limit_perslide = 20;
			$total_slide_real = 0;

			//new
			//get config slider
			$this->db->order_by("fib_config_id","desc");
			$this->db->where("fib_config_app_id",$app_id); //user kim
			$this->db->where("fib_config_company",$this->sess->user_company);
			$this->db->where("fib_config_flag",0);
			$this->db->where("fib_config_status",1);
			$qconfigvehicle = $this->db->get("fib_config_slide");
			$row_configvehicle = $qconfigvehicle->row();

			if(count($row_configvehicle)>0){
				$limit_perslide = $row_configvehicle->fib_config_vehicle;
			}else{
				$limit_perslide = 20;
			}
			$total_vehicle = $total;
			$total_slide = $total_vehicle / $limit_perslide;

			$total_slide_data = explode(".", $total_slide);
			if(count($total_slide_data)>1){
				$total_slide_real = $total_slide_data[0]+$plus1_slide = 1;
			}else{
				$total_slide_real = $total_slide;
			}

			$this->params["per_slice"] = $limit_perslide;
			$this->params["total_slide_real"] = $total_slide_real;

			$callback['total_white'] = $total_available;
			$callback['total_green'] = $total_co;
			$callback['total_blue'] = $total_muatgantung;
			$callback['total_yellow'] = $total_warning;
			$callback['total_red'] = $total_gpstrouble;
			$callback['total_slide_real'] = $total_slide_real;

		$html = $this->load->view('fib/fib_result', $this->params, true);

		$callback['html'] = $html;
		$callback['total'] = $total;

		echo json_encode($callback);

	}
	function sjadd()
	{
		if (! isset($this->sess->user_id)){redirect(base_url());}

		$this->db->order_by("vehicle_no","asc");
		$this->db->where("vehicle_status",1);
		$this->db->where("vehicle_user_id",860);
		$q = $this->db->get("vehicle");
		$vehicle = $q->result();
		$this->params["vehicle"] = $vehicle;

		$this->db->order_by("customer_name","asc");
		$this->db->where("customer_status",1);
		$this->db->where("customer_flag",0);
		$qc = $this->db->get("fib_cust");
		$customer = $qc->result();
		$this->params["customer"] = $customer;

		$this->params["content"] = $this->load->view("fib/sjadd.php", $this->params, true);
		$this->load->view("templatesess", $this->params);
	}
	function savesj()
	{
		if (! isset($this->sess->user_id)){redirect(base_url());}
		$sj_id = isset($_POST['sj_id']) ? $_POST['sj_id'] : "";
		$sj_no = isset($_POST['sj_no']) ? $_POST['sj_no'] : "";
		$sj_vehicle = isset($_POST['sj_vehicle']) ? $_POST['sj_vehicle'] : "";
		//code
		$sj_cust_1_code = isset($_POST['sj_cust_1_code']) ? $_POST['sj_cust_1_code'] : "";
		$sj_cust_2_code = isset($_POST['sj_cust_2_code']) ? $_POST['sj_cust_2_code'] : "";
		$sj_cust_3_code = isset($_POST['sj_cust_3_code']) ? $_POST['sj_cust_3_code'] : "";

		$sj_cust_1_name = isset($_POST['sj_cust_1_name']) ? $_POST['sj_cust_1_name'] : "";
		$sj_cust_2_name = isset($_POST['sj_cust_2_name']) ? $_POST['sj_cust_2_name'] : "";
		$sj_cust_3_name = isset($_POST['sj_cust_3_name']) ? $_POST['sj_cust_3_name'] : "";

		$fib_customer_status = isset($_POST['fib_customer_status']) ? $_POST['fib_customer_status'] : 0;
		$fib_customer2_status = isset($_POST['fib_customer2_status']) ? $_POST['fib_customer2_status'] : 0;
		$fib_customer3_status = isset($_POST['fib_customer3_status']) ? $_POST['fib_customer3_status'] : 0;

		$sj_no_real = "";
		$month = date("m");
		$year = date("Y");

		if($sj_no == "")
		{
			$callback["error"] = true;
			$callback["message"] = "Please input SJ No !";
			echo json_encode($callback);
			return;
		}

		if($sj_vehicle == "")
		{
			$callback["error"] = true;
			$callback["message"] = "Please Select Vehicle !";
			echo json_encode($callback);
			return;
		}

		if($sj_cust_1_code == "")
		{
			$callback["error"] = true;
			$callback["message"] = "Please Select Customer 1 !";
			echo json_encode($callback);
			return;
		}

		if(isset($sj_cust_1_code) && ($sj_cust_1_code != "")){
			$this->db->order_by("customer_id","desc");
			$this->db->where("customer_code",$sj_cust_1_code);
			$q = $this->db->get("fib_cust");
			$row = $q->row();
			if(count($row)>0){
				$sj_cust_1_name = $row->customer_name;
				$fib_customer_status = 1;
			}
		}

		if(isset($sj_cust_2_code) && ($sj_cust_2_code != "")){
			$this->db->order_by("customer_id","desc");
			$this->db->where("customer_code",$sj_cust_2_code);
			$q = $this->db->get("fib_cust");
			$row = $q->row();
			if(count($row)>0){
				$sj_cust_2_name = $row->customer_name;
				$fib_customer2_status = 1;
			}
		}

		if(isset($sj_cust_3_code) && ($sj_cust_3_code != "")){
			$this->db->order_by("customer_id","desc");
			$this->db->where("customer_code",$sj_cust_3_code);
			$q = $this->db->get("fib_cust");
			$row = $q->row();
			if(count($row)>0){
				$sj_cust_3_name = $row->customer_name;
				$fib_customer3_status = 1;
			}
		}


		$sj_no_real = $month."-".$year."-".$sj_no;

		unset($data);
		$data["sj_no"] = $sj_no_real;
		$data["sj_vehicle"] = $sj_vehicle;
		$data["sj_date"] = date("Y-m-d H:i:s");
		$data["sj_cust_1_code"] = $sj_cust_1_code;
		$data["sj_cust_2_code"] = $sj_cust_2_code;
		$data["sj_cust_3_code"] = $sj_cust_3_code;
		$data["sj_cust_1_name"] = $sj_cust_1_name;
		$data["sj_cust_2_name"] = $sj_cust_2_name;
		$data["sj_cust_3_name"] = $sj_cust_3_name;
		$data["sj_isread"] = 1;
		if($sj_id == "")
		{
			$this->db->insert("sj",$data);
			$callback["message"] = "Proses Input SJ Success !";

			//update di master FIB
			if($sj_vehicle != "")
			{
				unset($data_fib);
				$data_fib["fib_noso"] = $sj_no_real;
				$data_fib["fib_noso_status"] = 1;
				$data_fib["fib_remark"] = "";
				$data_fib["fib_remark_status"] = 1;
				$data_fib["fib_customer"] = $sj_cust_1_name;
				$data_fib["fib_customer_code"] = $sj_cust_1_code;
				$data_fib["fib_customer_status"] = $fib_customer_status;
				$data_fib["fib_customer2"] = $sj_cust_2_name;
				$data_fib["fib_customer2_code"] = $sj_cust_2_code;
				$data_fib["fib_customer2_status"] = $fib_customer2_status;
				$data_fib["fib_customer3"] = $sj_cust_3_name;
				$data_fib["fib_customer3_code"] = $sj_cust_3_code;
				$data_fib["fib_customer3_status"] = $fib_customer3_status;
				$data_fib["fib_sj"] = date("Y-m-d H:i:s");
				$data_fib["fib_sj_status"] = 1;
				$data_fib["fib_co_ischeck"] = 0;
				$this->db->where("fib_vehicle",$sj_vehicle);
				$this->db->update("fib",$data_fib);
			}
		}
		else
		{
			$this->db->where("sj_id",$sj_id);
			$this->db->update("sj",$data);
			$callback["message"] = "Proses Update SJ Success !";

			//update di master FIB
			if($sj_vehicle != "")
			{
				unset($data_fib);
				$data_fib["fib_noso"] = $sj_no_real;
				$data_fib["fib_noso_status"] = 1;
				$data_fib["fib_remark"] = "";
				$data_fib["fib_remark_status"] = 1;
				$data_fib["fib_customer"] = $sj_cust_1_name;
				$data_fib["fib_customer_code"] = $sj_cust_1_code;
				$data_fib["fib_customer_status"] = $fib_customer_status;
				$data_fib["fib_customer2"] = $sj_cust_2_name;
				$data_fib["fib_customer2_code"] = $sj_cust_2_code;
				$data_fib["fib_customer2_status"] = $fib_customer2_status;
				$data_fib["fib_customer3"] = $sj_cust_3_name;
				$data_fib["fib_customer3_code"] = $sj_cust_3_code;
				$data_fib["fib_customer3_status"] = $fib_customer3_status;
				$data_fib["fib_sj"] = date("Y-m-d H:i:s");
				$data_fib["fib_sj_status"] = 1;
				$data_fib["fib_co_ischeck"] = 0;
				$this->db->where("fib_vehicle",$sj_vehicle);
				$this->db->update("fib",$data_fib);
			}
		}

		$callback["error"] = true;
		echo json_encode($callback);
		return;
	}
	function ujadd()
	{
		if (! isset($this->sess->user_id)){redirect(base_url());}

		$this->db->order_by("vehicle_no","asc");
		$this->db->where("vehicle_status",1);
		$this->db->where("vehicle_user_id",860);
		$q = $this->db->get("vehicle");
		$vehicle = $q->result();
		$this->params["vehicle"] = $vehicle;

		$this->params["content"] = $this->load->view("fib/ujadd.php", $this->params, true);
		$this->load->view("templatesess", $this->params);
	}
	function saveuj()
	{
		if (! isset($this->sess->user_id)){redirect(base_url());}
		$uj_id = isset($_POST['uj_id']) ? $_POST['uj_id'] : "";
		$uj_no = isset($_POST['uj_no']) ? $_POST['uj_no'] : "";
		$uj_vehicle = isset($_POST['uj_vehicle']) ? $_POST['uj_vehicle'] : "";

		if($uj_vehicle == "")
		{
			$callback["error"] = true;
			$callback["message"] = "Please Select Vehicle !";
			echo json_encode($callback);
			return;
		}

		unset($data);
		$data["uj_vehicle"] = $uj_vehicle;
		$data["uj_date"] = date("Y-m-d H:i:s");
		$data["uj_isread"] = 1;

		if($uj_id == "")
		{
			$this->db->insert("uj",$data);

			//update di master FIB
			if($uj_vehicle != "")
			{
				unset($data_fib);
				$data_fib["fib_co_ischeck"] = 0;
				$data_fib["fib_uj"] = date("Y-m-d H:i:s");
				$data_fib["fib_uj_status"] = 1;
				$this->db->where("fib_vehicle",$uj_vehicle);
				$this->db->update("fib",$data_fib);
			}
			$callback["message"] = "Proses Input UJ Success !";
		}
		else
		{
			$this->db->where("uj_id",$uj_id);
			$this->db->update("uj",$data);

			//update di master FIB
			if($uj_vehicle != "")
			{
				unset($data_fib);
				$data_fib["fib_co_ischeck"] = 0;
				$data_fib["fib_uj"] = date("Y-m-d H:i:s");
				$data_fib["fib_uj_status"] = 1;
				$this->db->where("fib_vehicle",$uj_vehicle);
				$this->db->update("fib",$data_fib);
			}
			$callback["message"] = "Proses Update UJ Success !";
		}

		$callback["error"] = true;
		echo json_encode($callback);
		return;
	}
	function cronfib_sj($user="")
	{
		printf("PROSES GET SJ \r\n");
		$this->db->order_by("sj_id","desc");
		$this->db->where("sj_status",1);
		//$this->db->where("sj_isread",0);
		$this->db->where("sj_user_id",$user);
		$q = $this->db->get("sj");
		$data = $q->result();

		if(isset($data))
		{
		  printf("TOTAL SJ %s\r\n", count($data));
		  for($i=0;$i<count($data);$i++)
		  {
			 unset($datafib);
			 $datafib["fib_sj"] = date("Y-m-d H:i:s",strtotime($data[$i]->sj_sj_date));
			 $datafib["fib_remark"] = $data[$i]->sj_sj_no;
			 $datafib["fib_sj_status"] = $data[$i]->sj_sj_no;
			 $this->db->where("fib_vehicle",$data[$i]->sj_vehicle_device);
			 $this->db->update("fib",$datafib);

			 unset($update);
			 $update["sj_isread"] = 1;
			 $this->db->where("sj_id",$data[$i]->sj_id);
			 $this->db->update("sj",$update);
		  }
		}

		printf("PROSES SJ FINISH \r\n");
	}
	function cronfib_uj()
	{
		printf("PROSES GET UJ \r\n");
		$this->db->order_by("uj_id","desc");
		$this->db->where("uj_status",1);
		$this->db->where("uj_isread",0);
		$q = $this->db->get("uj");
		$data = $q->result();

		if(isset($data))
		{
		  printf("TOTAL UJ %s\r\n", count($data));
		  for($i=0;$i<count($data);$i++)
		  {
			 unset($datafib);
			 $datafib["fib_uj"] = date("Y-m-d H:i:s",strtotime($data[$i]->uj_date));
			 $datafib["fib_co_ischeck"] = 0;
			 $this->db->where("fib_vehicle",$data[$i]->uj_vehicle);
			 $this->db->update("fib",$datafib);

			 unset($update);
			 $update["uj_isread"] = 1;
			 $this->db->where("uj_id",$data[$i]->uj_id);
			 $this->db->update("uj",$update);
		  }
		}
		printf("PROSES SJ FINISH \r\n");
	}
	function cronfib_loading()
	{
		//update webtracking_fib set fib_loaded_status=0 where fib_status = 1;
		printf("PROSES GET LOADING \r\n");

		$geofence_loading = array("office#LOADING DALAM","office#LOADING LUAR");
		$geofence_id = array();
		$geofence_direction = array("1","2");

		$this->db->select("geofence_id");
		$this->db->where("geofence_status",1);
		$this->db->where_in("geofence_name",$geofence_loading);
		$q = $this->db->get("geofence_fib");
		$geofence = $q->result();
		for($i=0;$i<$q->num_rows;$i++)
		{
			$geofence_id[] = $geofence[$i]->geofence_id;
		}

		$this->db->where("fib_loaded_status",0);
		$q = $this->db->get("fib");
		$data = $q->result();
		if(isset($data))
		{
			for($i=0;$i<count($data);$i++)
			{
				$this->db->order_by("geoalert_time","desc");
				$this->db->where_in("geoalert_direction",$geofence_direction);
				$this->db->where_in("geoalert_geofence",$geofence_id);
				$this->db->where("geoalert_vehicle",$data[$i]->fib_vehicle);
				$q = $this->db->get("geofence_alert_fib");
				if($q->num_rows > 0)
				{
					printf("GET ALERT LOADED \r\n");
					$co = $q->row();
					unset($alert);
					$alert["fib_loaded_status"] = 1;
					$alert["fib_loaded"] = date("Y-m-d H:i:s",strtotime($co->geoalert_time));
					$this->db->where("fib_vehicle",$data[$i]->fib_vehicle);
					$this->db->update("fib",$alert);
				}
			}
		}
		printf("PROSES LOADING FINISH \r\n");

	}
	function cronfib_loading_new()
	{
		printf("PROSES GET LOADING \r\n");
		$now = date("Y-m-d");
		$limitdate = date("Y-m-d H:i:s", strtotime($now."00:00:00"));
		//$limitdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
		$this->db->where("fib_loaded_status",0); //diambil 1 kali nilai loading pertama. jika CI lebih besar dari LDG maka CI New berlaku
		$this->db->where("fib_status",1);
		$q = $this->db->get("fib");
		$data = $q->result();
		$geofence_loading = array("office#LOADING DALAM","office#LOADING LUAR");
		$geofence_office = array("office#kim pt","office#PT. KIM","bengkel#BENGKEL PT KIM");

		if(isset($data))
		{
			for($i=0;$i<$q->num_rows;$i++)
			{
				$this->db->order_by("fib_tripmileage_end_time","desc");
				$this->db->limit(1);
				$this->db->select("fib_tripmileage_id,fib_tripmileage_vehicle_id,fib_tripmileage_vehicle_no,fib_tripmileage_start_time,
								   fib_tripmileage_end_time,fib_tripmileage_geofence_start,fib_tripmileage_geofence_end,
								   fib_tripmileage_engine_start,fib_tripmileage_engine_end,fib_tripmileage_duration_sec
								 ");
				$this->db->where_in("fib_tripmileage_geofence_end",$geofence_loading);
				$this->db->where("fib_tripmileage_vehicle_id",$data[$i]->fib_vehicle);
				$this->db->where("fib_tripmileage_start_time >=",date("Y-m-d H:i:s",strtotime($limitdate)));
				$q_r = $this->db->get("fib_tripmileage");

				if($q_r->num_rows > 0)
				{
					printf("GET ALERT LOADING \r\n");
					$lo = $q_r->row();

					if($lo->fib_tripmileage_start_time > $data[$i]->fib_ci){
						unset($alert);
						$alert["fib_loaded_status"] = 1;
						$alert["fib_loaded"] = date("Y-m-d H:i:s",strtotime($lo->fib_tripmileage_start_time));
						$this->db->where("fib_vehicle",$data[$i]->fib_vehicle);
						$this->db->update("fib",$alert);
						printf("PROCESSED : %s , start: %s , end: %s \r\n", $lo->fib_tripmileage_vehicle_no, $lo->fib_tripmileage_geofence_start, $lo->fib_tripmileage_geofence_end);
					}else{
						printf("SKIP LOADING %s \r\n", $lo->fib_tripmileage_vehicle_no);
					}

				}
				else
				{
					//jika kosong kondisi ke 2,
					$this->db->order_by("fib_tripmileage_start_time","desc");
					$this->db->limit(1);
					$this->db->select("fib_tripmileage_id,fib_tripmileage_vehicle_id,fib_tripmileage_vehicle_no,fib_tripmileage_start_time,
									   fib_tripmileage_end_time,fib_tripmileage_geofence_start,fib_tripmileage_geofence_end,
									   fib_tripmileage_engine_start,fib_tripmileage_engine_end,fib_tripmileage_duration_sec
									 ");
					$this->db->where_in("fib_tripmileage_geofence_start",$geofence_loading);
					$this->db->where_in("fib_tripmileage_geofence_end",$geofence_office);
					$this->db->where("fib_tripmileage_vehicle_id",$data[$i]->fib_vehicle);
					$this->db->where("fib_tripmileage_start_time >=",date("Y-m-d H:i:s",strtotime($limitdate)));
					$q_r = $this->db->get("fib_tripmileage");
					if($q_r->num_rows > 0)
					{
						printf("GET ALERT LOADING \r\n");
						$lo = $q_r->row();
						if($lo->fib_tripmileage_start_time > $data[$i]->fib_ci){
							unset($alert);
							$alert["fib_loaded_status"] = 1;
							$alert["fib_loaded"] = date("Y-m-d H:i:s",strtotime($lo->fib_tripmileage_start_time));
							$this->db->where("fib_vehicle",$data[$i]->fib_vehicle);
							$this->db->update("fib",$alert);
							printf("PROCESSED #2 : %s , start: %s , end: %s \r\n", $lo->fib_tripmileage_vehicle_no, $lo->fib_tripmileage_geofence_start, $lo->fib_tripmileage_geofence_end);
						}else{
							printf("SKIP LOADING %s \r\n", $lo->fib_tripmileage_vehicle_no);
						}
					}

				}
			}
		}
		printf("PROSES LOADING FINISH \r\n");
	}
	function cronfib_co()
	{
		printf("PROSES GET CO \r\n");
		$now = date("Y-m-d");
		$limitdate = date("Y-m-d H:i:s", strtotime($now."00:00:00"));
		$this->db->where("fib_co_ischeck",0);
		$q = $this->db->get("fib");
		$data = $q->result();

		if(isset($data))
		{
			for($i=0;$i<$q->num_rows;$i++)
			{
				$this->db->order_by("geoalert_time","desc");
				$this->db->limit(1);
				$this->db->select("geofence_name,geofence_id,geofence_status,
								   geoalert_time,geoalert_geofence
								 ");
				$this->db->where("geoalert_direction",2); //out of
				$this->db->where("geofence_name","office#PT. KIM");
				$this->db->where("geoalert_vehicle",$data[$i]->fib_vehicle);
				$this->db->where("geoalert_time >=",date("Y-m-d H:i:s",strtotime($limitdate)));
				$this->db->join("geofence","geoalert_geofence=geofence_id", "left");
				$q_r = $this->db->get("geofence_alert");

				if($q_r->num_rows > 0)
				{
					printf("GET ALERT CO #1 \r\n");
					$co = $q_r->row();
					unset($alert);
					$alert["fib_co_ischeck"] = 1;
					$alert["fib_co_status"] = 1;
					$alert["fib_co"] = date("Y-m-d H:i:s",strtotime($co->geoalert_time));
					$this->db->where("fib_vehicle",$data[$i]->fib_vehicle);
					$this->db->update("fib",$alert);
					printf("PROCESSED %s \r\n", $data[$i]->fib_vehicle);
				}
				//jika tidak ada di KIM PT
				else
				{
					$this->db->order_by("geoalert_time","desc");
					$this->db->limit(1);
					$this->db->select("geofence_name,geofence_id,geofence_status,
									   geoalert_time,geoalert_geofence
									 ");
					$this->db->where("geoalert_direction",2); //out of
					//$this->db->where_in("geoalert_geofence",$this->config->item("is_geofencekim_office"));
					$this->db->where("geofence_name","office#kim pt");
					$this->db->where("geoalert_vehicle",$data[$i]->fib_vehicle);
					$this->db->where("geoalert_time >=",date("Y-m-d H:i:s",strtotime($limitdate)));
					$this->db->join("geofence","geoalert_geofence=geofence_id", "left");
					$q_r2 = $this->db->get("geofence_alert");

					if($q_r2->num_rows > 0)
					{
						printf("GET ALERT CO #2 \r\n");
						$co2 = $q_r2->row();
						unset($alert);
						$alert["fib_co_ischeck"] = 1;
						$alert["fib_co_status"] = 1;
						$alert["fib_co"] = date("Y-m-d H:i:s",strtotime($co2->geoalert_time));
						$this->db->where("fib_vehicle",$data[$i]->fib_vehicle);
						$this->db->update("fib",$alert);
						printf("PROCESSED %s \r\n", $data[$i]->fib_vehicle);
					}

				}
			}
		}
		printf("PROSES CO FINISH \r\n");
	}
	function cronfib_co_new()
	{
		printf("PROSES GET CO \r\n");
		$now = date("Y-m-d");
		$limitdate = date("Y-m-d H:i:s", strtotime($now."00:00:00"));
		//$limitdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
		//$this->db->where("fib_co_ischeck",0);
		//$this->db->where("fib_co_status",0);
		$this->db->where("fib_status",1);
		$q = $this->db->get("fib");
		$data = $q->result();
		$geofence_office = array("office#kim pt","office#PT. KIM","office#LOADING LUAR","office#LOADING DALAM","bengkel#BENGKEL PT KIM");

		if(isset($data))
		{
			for($i=0;$i<$q->num_rows;$i++)
			{
				$this->db->order_by("fib_tripmileage_start_time","desc");
				$this->db->limit(1);
				$this->db->select("fib_tripmileage_id,fib_tripmileage_vehicle_id,fib_tripmileage_vehicle_no,fib_tripmileage_start_time,
								   fib_tripmileage_end_time,fib_tripmileage_geofence_start,fib_tripmileage_geofence_end,
								   fib_tripmileage_engine_start,fib_tripmileage_engine_end,fib_tripmileage_duration_sec
								 ");
				$this->db->where_in("fib_tripmileage_geofence_start",$geofence_office);
				$this->db->where_not_in("fib_tripmileage_geofence_end",$geofence_office);
				$this->db->where("fib_tripmileage_vehicle_id",$data[$i]->fib_vehicle);
				$this->db->where("fib_tripmileage_start_time >=",date("Y-m-d H:i:s",strtotime($data[$i]->fib_ci)));
				$q_r = $this->db->get("fib_tripmileage");

				if($q_r->num_rows > 0)
				{
					printf("GET ALERT CO #1 \r\n");
					$co = $q_r->row();
						unset($alert);
						$alert["fib_co_ischeck"] = 1;
						$alert["fib_co_status"] = 1;
						$alert["fib_co"] = date("Y-m-d H:i:s",strtotime($co->fib_tripmileage_start_time));

						$this->db->where("fib_vehicle",$data[$i]->fib_vehicle);
						$this->db->update("fib",$alert);
						printf("PROCESSED %s %s \r\n", $data[$i]->fib_vehicle, $co->fib_tripmileage_vehicle_no);
				}

			}
		}
		printf("PROSES CO FINISH \r\n");
	}
	function cronfib_ci()
	{
		printf("PROSES GET CI \r\n");
		$now = date("Y-m-d");
		$limitdate = date("Y-m-d H:i:s", strtotime($now."00:00:00"));
		$this->db->where("fib_ci_status",0);
		$q = $this->db->get("fib");
		$data = $q->result();

		if(isset($data))
		{
			for($i=0;$i<$q->num_rows;$i++)
			{
				$this->db->order_by("geoalert_time","desc");
				$this->db->limit(1);
				$this->db->select("geofence_name,geofence_id,geofence_status,
								   geoalert_time,geoalert_geofence,geoalert_vehicle
								 ");
				$this->db->where("geoalert_direction",1); //in of
				$this->db->where("geofence_name","office#PT. KIM");
				$this->db->where("geoalert_vehicle",$data[$i]->fib_vehicle);
				$this->db->where("geoalert_time >=",date("Y-m-d H:i:s",strtotime($limitdate)));
				$this->db->join("geofence","geoalert_geofence=geofence_id", "left");
				$q_r = $this->db->get("geofence_alert");

				if($q_r->num_rows > 0)
				{
					printf("GET ALERT CI #1 \r\n");
					$ci = $q_r->row();
					unset($alert);
					$alert["fib_loaded_status"] = 0;
					$alert["fib_sj_status"] = 0;
					$alert["fib_uj_status"] = 0;
					$alert["fib_co_status"] = 0;
					$alert["fib_co_ischeck"] = 0;

					$alert["fib_eta_status"] = 0;
					$alert["fib_arrival_time_status"] = 0;
					$alert["fib_customer_status"] = 0;
					$alert["fib_customer2_status"] = 0;
					$alert["fib_customer3_status"] = 0;
					$alert["fib_noso_status"] = 0;
					$alert["fib_remark_status"] = 0;

					$alert["fib_customer"] = "";
					$alert["fib_customer2"] = "";
					$alert["fib_customer3"] = "";
					$alert["fib_noso"] = "";
					$alert["fib_remark"] = "";
					$alert["fib_arrival_time"] = "";
					$alert["fib_eta"] = "";

					$this->db->where("fib_vehicle",$data[$i]->fib_vehicle);
					$this->db->update("fib",$alert);
					printf("PROCESSED %s \r\n", $data[$i]->fib_vehicle);
				}
				//jika tidak ada di KIM PT
				else
				{
					$this->db->order_by("geoalert_time","desc");
					$this->db->limit(1);
					$this->db->select("geofence_name,geofence_id,geofence_status,
									   geoalert_time,geoalert_geofence
									 ");
					$this->db->where("geoalert_direction",1); //in of
					$this->db->where("geofence_name","office#kim pt");
					$this->db->where("geoalert_vehicle",$data[$i]->fib_vehicle);
					$this->db->where("geoalert_time >=",date("Y-m-d H:i:s",strtotime($limitdate)));
					$this->db->join("geofence","geoalert_geofence=geofence_id", "left");
					$q_r2 = $this->db->get("geofence_alert");

					if($q_r2->num_rows > 0)
					{
						printf("GET ALERT CI #2 \r\n");
						$ci = $q_r2->row();
						unset($alert);
						$alert["fib_loaded_status"] = 0;
						$alert["fib_sj_status"] = 0;
						$alert["fib_uj_status"] = 0;
						$alert["fib_co_status"] = 0;
						$alert["fib_co_ischeck"] = 0;

						$alert["fib_eta_status"] = 0;
						$alert["fib_arrival_time_status"] = 0;
						$alert["fib_customer_status"] = 0;
						$alert["fib_customer2_status"] = 0;
						$alert["fib_customer3_status"] = 0;
						$alert["fib_noso_status"] = 0;
						$alert["fib_remark_status"] = 0;

						$alert["fib_customer"] = "";
						$alert["fib_customer2"] = "";
						$alert["fib_customer3"] = "";
						$alert["fib_noso"] = "";
						$alert["fib_remark"] = "";
						$alert["fib_arrival_time"] = "";
						$alert["fib_eta"] = "";

						$this->db->where("fib_vehicle",$data[$i]->fib_vehicle);
						$this->db->update("fib",$alert);
						printf("PROCESSED %s \r\n", $data[$i]->fib_vehicle);
					}

				}
			}
		}
		printf("PROSES CI FINISH \r\n");
	}
	function cronfib_ci_new()
	{
		printf("PROSES GET CI \r\n");
		$now = date("Y-m-d");
		//$limitdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
		$limitdate = date("Y-m-d H:i:s", strtotime($now."00:00:00"));

		$this->db->where("fib_co_status",1); //yg sudah co
		$this->db->where("fib_status",1);
		$q = $this->db->get("fib");
		$data = $q->result();
		$geofence_office = array("office#kim pt","office#PT. KIM","office#LOADING LUAR","office#LOADING DALAM","bengkel#BENGKEL PT KIM");

		if(isset($data))
		{
			for($i=0;$i<$q->num_rows;$i++)
			{
				$this->db->order_by("fib_tripmileage_end_time","desc");
				$this->db->limit(1);
				$this->db->select("fib_tripmileage_id,fib_tripmileage_vehicle_id,fib_tripmileage_vehicle_no,fib_tripmileage_start_time,
								   fib_tripmileage_end_time,fib_tripmileage_geofence_start,fib_tripmileage_geofence_end,
								   fib_tripmileage_engine_start,fib_tripmileage_engine_end,fib_tripmileage_duration_sec
								 ");
				$this->db->where_in("fib_tripmileage_geofence_end",$geofence_office);
				$this->db->where_not_in("fib_tripmileage_geofence_start",$geofence_office);
				$this->db->where("fib_tripmileage_vehicle_id",$data[$i]->fib_vehicle);
				$this->db->where("fib_tripmileage_end_time >=",date("Y-m-d H:i:s",strtotime($limitdate)));
				$q_r = $this->db->get("fib_tripmileage");

				if($q_r->num_rows > 0)
				{
					printf("GET ALERT CI \r\n");
					$ci = $q_r->row();
					$update = 0;
					//jika data ci lebih besar dari data terakhir co maka indikasi mobil balik ke pool
					unset($alert);
						//cek uj hari ini jika ada avplus 1
						$this->db->order_by("uj_date","desc");
						$this->db->where("uj_date >=",date("Y-m-d H:i:s",strtotime($limitdate)));
						$this->db->where("uj_vehicle",$data[$i]->fib_vehicle);
						$q_uj = $this->db->get("uj");
						$row_uj = $q_uj->row();

						if($ci->fib_tripmileage_end_time > $data[$i]->fib_loaded){
							$alert["fib_loaded_status"] = 0;
							$alert["fib_ci"] = $ci->fib_tripmileage_end_time;
							$update = 1;
						}
						if($ci->fib_tripmileage_end_time > $data[$i]->fib_co){
							$update = 1;
							$alert["fib_co_status"] = 0;
							$alert["fib_sj_status"] = 0;

							$alert["fib_customer"] = "";
							$alert["fib_customer_code"] = "";
							$alert["fib_customer_arrive"] = 0;
							$alert["fib_customer_status"] = 0;
							$alert["fib_customer_unique"] = "";

							$alert["fib_customer2"] = "";
							$alert["fib_customer2_code"] = "";
							$alert["fib_customer2_arrive"] = 0;
							$alert["fib_customer2_status"] = 0;
							$alert["fib_customer2_unique"] = "";

							$alert["fib_customer3"] = "";
							$alert["fib_customer3_code"] = "";
							$alert["fib_customer3_arrive"] = 0;
							$alert["fib_customer3_status"] = 0;
							$alert["fib_customer3_unique"] = "";

							$alert["fib_noso_status"] = 0;
							$alert["fib_eta_status"] = 0;
							$alert["fib_remark_status"] = 0;
							$alert["fib_out_time_status"] = 0;
							$alert["fib_out_name"] = "";
							$alert["fib_arrival_time_status"] = 0;
							$alert["fib_arrival_name"] = "";
							$alert["fib_av_plus"] = 0;
							$alert["fib_ci"] = $ci->fib_tripmileage_end_time;


							if(isset($row_uj) && (count($row_uj)>0)){
								$alert["fib_av_plus"] = 1;
								if($ci->fib_tripmileage_end_time > $row_uj->uj_date){
									$alert["fib_uj_status"] = 0;
								}
							}
						}

						if(isset($update) && ($update == 1)){
							$this->db->where("fib_vehicle",$data[$i]->fib_vehicle);
							$this->db->update("fib",$alert);

							printf("PROCESSED #1: %s , CO: %s , CI: %s \r\n", $ci->fib_tripmileage_vehicle_no, $data[$i]->fib_co, $ci->fib_tripmileage_end_time);
						}
						else
						{
							printf("NO DATA CI: %s , CO: %s , CI: %s \r\n", $ci->fib_tripmileage_vehicle_no, $data[$i]->fib_co, $ci->fib_tripmileage_end_time);
						}


				}

			}
		}
		printf("PROSES CI FINISH \r\n");
	}
	function cronfib_in_out()
	{
		printf("PROSES GET ARRIVAL \r\n");
		$now = date("Y-m-d");
		$limitdate = date("Y-m-d H:i:s", strtotime($now."00:00:00"));
		$this->db->where("fib_uj_status",1); //yg sudah ada uj
		$this->db->where("fib_uj >=",$limitdate); //hanya input hari ini
		$q = $this->db->get("fib");
		$data = $q->result();

		if(isset($data))
		{
			//in
			for($i=0;$i<$q->num_rows;$i++)
			{
				$arrival_name = "";

				$this->db->order_by("geoalert_time","desc");
				$this->db->limit(1);
				$this->db->select("geofence_name,geofence_id,geofence_status,
								   geoalert_time,geoalert_geofence,geoalert_vehicle
								 ");
				$this->db->where("geoalert_direction",1); //in
				$this->db->like("geofence_name","customer#");
				$this->db->where("geoalert_vehicle",$data[$i]->fib_vehicle);
				$this->db->where("geoalert_time >=",date("Y-m-d H:i:s",strtotime($limitdate)));
				$this->db->join("geofence","geoalert_geofence=geofence_id", "left");

				$q_r = $this->db->get("geofence_alert");

				if($q_r->num_rows > 0)
				{
					printf("GET ALERT IN \r\n");
					$arrival = $q_r->row();

					$arrival_data = explode("#", $arrival->geofence_name);
					if(count($arrival_data)>1){
						$arrival_name = $arrival_data[1];
					}else{
						$arrival_name =  $arrival->geofence_name;
					}

					unset($alert);
					$alert["fib_arrival_time_status"] = 1;
					$alert["fib_arrival_name"] = $arrival_name;
					$alert["fib_arrival_time"] = date("Y-m-d H:i:s",strtotime($arrival->geoalert_time));
					$this->db->where("fib_vehicle",$data[$i]->fib_vehicle);
					$this->db->update("fib",$alert);
					printf("PROCESSED %s \r\n", $data[$i]->fib_vehicle);
				}
			}

			//out
			for($i=0;$i<$q->num_rows;$i++)
			{
				$depart_name = "";

				$this->db->order_by("geoalert_time","desc");
				$this->db->limit(1);
				$this->db->select("geofence_name,geofence_id,geofence_status,
								   geoalert_time,geoalert_geofence,geoalert_vehicle
								 ");
				$this->db->where("geoalert_direction",2); //out
				$this->db->like("geofence_name","customer#");
				$this->db->where("geoalert_vehicle",$data[$i]->fib_vehicle);
				$this->db->where("geoalert_time >=",date("Y-m-d H:i:s",strtotime($limitdate)));
				$this->db->join("geofence","geoalert_geofence=geofence_id", "left");
				$q_o = $this->db->get("geofence_alert");

				if($q_o->num_rows > 0)
				{
					printf("GET ALERT OUT \r\n");
					$depart = $q_o->row();

					$depart_data = explode("#", $depart->geofence_name);
					if(count($depart_data)>1){
						$depart_name = $depart_data[1];
					}else{
						$depart_name =  $depart->geofence_name;
					}

					unset($alert);
					$alert["fib_out_time_status"] = 1;
					$alert["fib_out_name"] = $depart_name;
					$alert["fib_out_time"] = date("Y-m-d H:i:s",strtotime($depart->geoalert_time));
					$this->db->where("fib_vehicle",$data[$i]->fib_vehicle);
					$this->db->update("fib",$alert);
					printf("PROCESSED %s \r\n", $data[$i]->fib_vehicle);
				}
			}
		}
		printf("PROSES IN OUT FINISH \r\n");
	}
	function cronfib_in_out_new()
	{
		printf("PROSES GET ARRIVAL \r\n");
		$now = date("Y-m-d");
		$limitdate = date("Y-m-d H:i:s", strtotime($now."00:00:00"));
		//$limitdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
		$this->db->where("fib_sj_status",1); //yg sudah ada sj
		//$this->db->where("fib_sj >=",$limitdate); //hanya sj hari ini
		$this->db->where("fib_status",1); //yg aktif
		$q = $this->db->get("fib");
		$data = $q->result();

		if(isset($data))
		{

			for($i=0;$i<$q->num_rows;$i++)
			{
				$customer_coordinate = 0;
				$customer_geofence = "-";
				$customer2_coordinate = 0;
				$customer2_geofence = "-";
				$customer3_coordinate = 0;
				$customer3_geofence = "-";

				if($data[$i]->fib_customer != ""){
					//cek master customer
					$this->db->order_by("customer_id","desc");
					$this->db->where("customer_flag",0);
					$this->db->where("customer_status",1);
					$this->db->where("customer_code",$data[$i]->fib_customer_code);
					$this->db->where("customer_unique",$data[$i]->fib_customer_unique);
					$q_c = $this->db->get("fib_cust");
					$row_c = $q_c->row();

					if(count($row_c)>0){
						$customer_coordinate = $row_c->customer_koord;
						$customer_geofence = $row_c->customer_geofence_name;
					}

					//in
					$arrival_name = "";

					$this->db->order_by("fib_tripmileage_end_time","asc");
					$this->db->limit(1);
					$this->db->select("fib_tripmileage_id,fib_tripmileage_vehicle_id,fib_tripmileage_vehicle_no,fib_tripmileage_start_time,
									   fib_tripmileage_end_time,fib_tripmileage_geofence_start,fib_tripmileage_geofence_end,
									   fib_tripmileage_engine_start,fib_tripmileage_engine_end,fib_tripmileage_duration_sec
									 ");
					$this->db->where("fib_tripmileage_geofence_end",$customer_geofence);
					$this->db->where("fib_tripmileage_vehicle_id",$data[$i]->fib_vehicle);
					$this->db->where("fib_tripmileage_end_time >=",date("Y-m-d H:i:s",strtotime($limitdate)));
					$q_r = $this->db->get("fib_tripmileage");

					if($q_r->num_rows > 0)
					{
						printf("GET ALERT IN \r\n");
						$arrival = $q_r->row();

						$arrival_data = explode("#", $arrival->fib_tripmileage_geofence_end);

						if(count($arrival_data)>1){

							$arrival_name = $arrival_data[1];
							unset($alert);
							$alert["fib_arrival_time_status"] = 1;
							$alert["fib_arrival_name"] = $arrival_name;
							$alert["fib_arrival_time"] = date("Y-m-d H:i:s",strtotime($arrival->fib_tripmileage_end_time));
							$alert["fib_customer_arrive"] = 1;
							$alert["fib_eta_status"] = 0;
							$this->db->where("fib_vehicle",$data[$i]->fib_vehicle);
							$this->db->update("fib",$alert);
							printf("PROCESSED %s , %s \r\n", $data[$i]->fib_vehicle, $arrival->fib_tripmileage_vehicle_no);

							//out
							$depart_name = "";

							$this->db->order_by("fib_tripmileage_start_time","desc");
							$this->db->limit(1);
							$this->db->select("fib_tripmileage_id,fib_tripmileage_vehicle_id,fib_tripmileage_vehicle_no,fib_tripmileage_start_time,
											   fib_tripmileage_end_time,fib_tripmileage_geofence_start,fib_tripmileage_geofence_end,
											   fib_tripmileage_engine_start,fib_tripmileage_engine_end,fib_tripmileage_duration_sec
											 ");
							$this->db->where("fib_tripmileage_geofence_start",$arrival->fib_tripmileage_geofence_end);
							$this->db->where("fib_tripmileage_geofence_end != ",$arrival->fib_tripmileage_geofence_end);
							$this->db->where("fib_tripmileage_vehicle_id",$data[$i]->fib_vehicle);
							$this->db->where("fib_tripmileage_end_time >=",date("Y-m-d H:i:s",strtotime($limitdate)));
							$q_o = $this->db->get("fib_tripmileage");

							if($q_o->num_rows > 0)
							{
								printf("GET ALERT OUT \r\n");
								$depart = $q_o->row();

								$depart_data = explode("#", $depart->fib_tripmileage_geofence_start);
								if(count($depart_data)>1){
									$depart_name = $depart_data[1];
								}else{
									$depart_name =  $depart->fib_tripmileage_geofence_start;
								}

								unset($alert);
								$alert["fib_out_time_status"] = 1;
								$alert["fib_out_name"] = $depart_name;
								$alert["fib_out_time"] = date("Y-m-d H:i:s",strtotime($depart->fib_tripmileage_start_time));
								$this->db->where("fib_vehicle",$data[$i]->fib_vehicle);
								$this->db->update("fib",$alert);
								printf("PROCESSED %s , %s \r\n", $data[$i]->fib_vehicle, $depart->fib_tripmileage_vehicle_no);
							}

						}else{
							printf("MASIH DI AREA GEOFENCE: %s ,  %s \r\n", $data[$i]->fib_vehicle,$arrival->fib_tripmileage_geofence_start);
						}

					}
					else{
						printf("NO DATA IN GEOFENCE :  %s \r\n", $data[$i]->fib_vehicle);
					}

				}

				if($data[$i]->fib_customer2 != ""){
					//cek master customer
					$this->db->order_by("customer_id","desc");
					$this->db->where("customer_flag",0);
					$this->db->where("customer_status",1);
					$this->db->where("customer_code",$data[$i]->fib_customer2_code);
					$this->db->where("customer_unique",$data[$i]->fib_customer2_unique);
					$q_c = $this->db->get("fib_cust");
					$row_c = $q_c->row();

					if(count($row_c)>0){
						$customer_coordinate = $row_c->customer_koord;
						$customer_geofence = $row_c->customer_geofence_name;
					}

					//in
					$arrival_name = "";

					$this->db->order_by("fib_tripmileage_end_time","asc");
					$this->db->limit(1);
					$this->db->select("fib_tripmileage_id,fib_tripmileage_vehicle_id,fib_tripmileage_vehicle_no,fib_tripmileage_start_time,
									   fib_tripmileage_end_time,fib_tripmileage_geofence_start,fib_tripmileage_geofence_end,
									   fib_tripmileage_engine_start,fib_tripmileage_engine_end,fib_tripmileage_duration_sec
									 ");
					$this->db->where("fib_tripmileage_geofence_end",$customer2_geofence);
					$this->db->where("fib_tripmileage_vehicle_id",$data[$i]->fib_vehicle);
					$this->db->where("fib_tripmileage_end_time >=",date("Y-m-d H:i:s",strtotime($limitdate)));
					$q_r = $this->db->get("fib_tripmileage");

					if($q_r->num_rows > 0)
					{
						printf("GET ALERT IN 2 \r\n");
						$arrival = $q_r->row();

						$arrival_data = explode("#", $arrival->fib_tripmileage_geofence_end);

						if(count($arrival_data)>1){

							$arrival_name = $arrival_data[1];
							unset($alert);
							$alert["fib_arrival_time_status"] = 1;
							$alert["fib_arrival_name"] = $arrival_name;
							$alert["fib_arrival_time"] = date("Y-m-d H:i:s",strtotime($arrival->fib_tripmileage_end_time));
							$alert["fib_customer2_arrive"] = 1;
							$alert["fib_eta_status"] = 0;
							$this->db->where("fib_vehicle",$data[$i]->fib_vehicle);
							$this->db->update("fib",$alert);
							printf("PROCESSED 2 %s , %s \r\n", $data[$i]->fib_vehicle, $arrival->fib_tripmileage_vehicle_no);

							//out
							$depart_name = "";

							$this->db->order_by("fib_tripmileage_start_time","desc");
							$this->db->limit(1);
							$this->db->select("fib_tripmileage_id,fib_tripmileage_vehicle_id,fib_tripmileage_vehicle_no,fib_tripmileage_start_time,
											   fib_tripmileage_end_time,fib_tripmileage_geofence_start,fib_tripmileage_geofence_end,
											   fib_tripmileage_engine_start,fib_tripmileage_engine_end,fib_tripmileage_duration_sec
											 ");
							$this->db->where("fib_tripmileage_geofence_start",$arrival->fib_tripmileage_geofence_end);
							$this->db->where("fib_tripmileage_geofence_end != ",$arrival->fib_tripmileage_geofence_end);
							$this->db->where("fib_tripmileage_vehicle_id",$data[$i]->fib_vehicle);
							$this->db->where("fib_tripmileage_end_time >=",date("Y-m-d H:i:s",strtotime($limitdate)));
							$q_o = $this->db->get("fib_tripmileage");

							if($q_o->num_rows > 0)
							{
								printf("GET ALERT OUT 2 \r\n");
								$depart = $q_o->row();

								$depart_data = explode("#", $depart->fib_tripmileage_geofence_start);
								if(count($depart_data)>1){
									$depart_name = $depart_data[1];
								}else{
									$depart_name =  $depart->fib_tripmileage_geofence_start;
								}

								unset($alert);
								$alert["fib_out_time_status"] = 1;
								$alert["fib_out_name"] = $depart_name;
								$alert["fib_out_time"] = date("Y-m-d H:i:s",strtotime($depart->fib_tripmileage_start_time));
								$this->db->where("fib_vehicle",$data[$i]->fib_vehicle);
								$this->db->update("fib",$alert);
								printf("PROCESSED 2 %s , %s \r\n", $data[$i]->fib_vehicle, $depart->fib_tripmileage_vehicle_no);
							}

						}else{
							printf("MASIH DI AREA GEOFENCE 2: %s ,  %s \r\n", $data[$i]->fib_vehicle,$arrival->fib_tripmileage_geofence_start);
						}

					}
					else{
						printf("NO DATA IN GEOFENCE 2 :  %s \r\n", $data[$i]->fib_vehicle);
					}

				}

				if($data[$i]->fib_customer3 != ""){
					//cek master customer
					$this->db->order_by("customer_id","desc");
					$this->db->where("customer_flag",0);
					$this->db->where("customer_status",1);
					$this->db->where("customer_code",$data[$i]->fib_customer3_code);
					$this->db->where("customer_unique",$data[$i]->fib_customer3_unique);
					$q_c = $this->db->get("fib_cust");
					$row_c = $q_c->row();

					if(count($row_c)>0){
						$customer_coordinate = $row_c->customer_koord;
						$customer_geofence = $row_c->customer_geofence_name;
					}

					//in
					$arrival_name = "";

					$this->db->order_by("fib_tripmileage_end_time","asc");
					$this->db->limit(1);
					$this->db->select("fib_tripmileage_id,fib_tripmileage_vehicle_id,fib_tripmileage_vehicle_no,fib_tripmileage_start_time,
									   fib_tripmileage_end_time,fib_tripmileage_geofence_start,fib_tripmileage_geofence_end,
									   fib_tripmileage_engine_start,fib_tripmileage_engine_end,fib_tripmileage_duration_sec
									 ");
					$this->db->where("fib_tripmileage_geofence_end",$customer3_geofence);
					$this->db->where("fib_tripmileage_vehicle_id",$data[$i]->fib_vehicle);
					$this->db->where("fib_tripmileage_end_time >=",date("Y-m-d H:i:s",strtotime($limitdate)));
					$q_r = $this->db->get("fib_tripmileage");

					if($q_r->num_rows > 0)
					{
						printf("GET ALERT IN 3 \r\n");
						$arrival = $q_r->row();

						$arrival_data = explode("#", $arrival->fib_tripmileage_geofence_end);

						if(count($arrival_data)>1){

							$arrival_name = $arrival_data[1];
							unset($alert);
							$alert["fib_arrival_time_status"] = 1;
							$alert["fib_arrival_name"] = $arrival_name;
							$alert["fib_arrival_time"] = date("Y-m-d H:i:s",strtotime($arrival->fib_tripmileage_end_time));
							$alert["fib_customer3_arrive"] = 1;
							$alert["fib_eta_status"] = 0;
							$this->db->where("fib_vehicle",$data[$i]->fib_vehicle);
							$this->db->update("fib",$alert);
							printf("PROCESSED 3 %s , %s \r\n", $data[$i]->fib_vehicle, $arrival->fib_tripmileage_vehicle_no);

							//out
							$depart_name = "";

							$this->db->order_by("fib_tripmileage_start_time","desc");
							$this->db->limit(1);
							$this->db->select("fib_tripmileage_id,fib_tripmileage_vehicle_id,fib_tripmileage_vehicle_no,fib_tripmileage_start_time,
											   fib_tripmileage_end_time,fib_tripmileage_geofence_start,fib_tripmileage_geofence_end,
											   fib_tripmileage_engine_start,fib_tripmileage_engine_end,fib_tripmileage_duration_sec
											 ");
							$this->db->where("fib_tripmileage_geofence_start",$arrival->fib_tripmileage_geofence_end);
							$this->db->where("fib_tripmileage_geofence_end != ",$arrival->fib_tripmileage_geofence_end);
							$this->db->where("fib_tripmileage_vehicle_id",$data[$i]->fib_vehicle);
							$this->db->where("fib_tripmileage_end_time >=",date("Y-m-d H:i:s",strtotime($limitdate)));
							$q_o = $this->db->get("fib_tripmileage");

							if($q_o->num_rows > 0)
							{
								printf("GET ALERT OUT 3 \r\n");
								$depart = $q_o->row();

								$depart_data = explode("#", $depart->fib_tripmileage_geofence_start);
								if(count($depart_data)>1){
									$depart_name = $depart_data[1];
								}else{
									$depart_name =  $depart->fib_tripmileage_geofence_start;
								}

								unset($alert);
								$alert["fib_out_time_status"] = 1;
								$alert["fib_out_name"] = $depart_name;
								$alert["fib_out_time"] = date("Y-m-d H:i:s",strtotime($depart->fib_tripmileage_start_time));
								$this->db->where("fib_vehicle",$data[$i]->fib_vehicle);
								$this->db->update("fib",$alert);
								printf("PROCESSED 3 %s , %s \r\n", $data[$i]->fib_vehicle, $depart->fib_tripmileage_vehicle_no);
							}

						}else{
							printf("MASIH DI AREA GEOFENCE 3: %s ,  %s \r\n", $data[$i]->fib_vehicle,$arrival->fib_tripmileage_geofence_start);
						}

					}
					else{
						printf("NO DATA IN GEOFENCE 3 :  %s \r\n", $data[$i]->fib_vehicle);
					}

				}

			}

		}
		printf("PROSES IN OUT FINISH \r\n");
	}

	function cronfib_eta()
	{
		printf("PROSES GET ETA \r\n");
		$now = date("Y-m-d");
		$limitdate = date("Y-m-d H:i:s", strtotime($now."00:00:00"));
		$this->db->where("fib_co_status",1); //yg sudah CO
		$this->db->where("fib_sj_status",1); //yg sudah ada sj
		$this->db->where("fib_sj >=",$limitdate); //hanya sj hari ini
		$this->db->where("fib_status",1); //yg aktif
		$q = $this->db->get("fib");
		$data = $q->result();

		if(isset($data))
		{

			for($i=0;$i<$q->num_rows;$i++)
			{
				$customer_coordinate = 0;
				$customer_geofence = "-";
				$customer2_coordinate = 0;
				$customer2_geofence = "-";
				$customer3_coordinate = 0;
				$customer3_geofence = "-";

				//hitung eta
				//cari berdasarkan customer status == 1

				//jika customer 1 aktif cust 2 kosong  arrive 0 (belum sampe)
				if($data[$i]->fib_customer != "" && $data[$i]->fib_customer_status == 1 && $data[$i]->fib_customer_arrive == 0){

					//cek master customer
					$this->db->order_by("customer_id","desc");
					$this->db->where("customer_flag",0);
					$this->db->where("customer_status",1);
					$this->db->where("customer_code",$data[$i]->fib_customer_code);
					$this->db->where("customer_unique",$data[$i]->fib_customer_unique);
					$this->db->where('customer_koord <>','');
					$this->db->where('customer_koord is NOT NULL', NULL, FALSE);

					$q_c = $this->db->get("fib_cust");
					$row_c = $q_c->row();

					if(count($row_c)>0){
						$customer_coordinate = $row_c->customer_koord;
						$customer_geofence = $row_c->customer_geofence_name;

						printf("CEK ETA CUST 1: %s \r\n", $data[$i]->fib_vehicle);

						if($customer_coordinate == 0 || $customer_coordinate == ""){
							printf("NO DATA KOORD 1: %s \r\n", $customer_geofence);
						}else{
							//hitung jarak
							$customer_coordinate_data = explode(",", $customer_coordinate);
							$latitude1 = $customer_coordinate_data[0];
							$longitude1 = $customer_coordinate_data[1];

							//cek lasposition fib
							$latitude2 = $data[$i]->fib_last_lat;
							$longitude2 = $data[$i]->fib_last_long;

							//Apigoogle
							//$apikey = $this->config->item('GOOGLE_MAP_API_KEY');
							$apikey = "AIzaSyDjkxkZrIVJbT6Bv2nmJlK9OvNYTBcA2z0";
							//print_r($latitude1." ".$longitude1." ".$latitude2." ".$longitude2);exit();
							$eta_data = $this->getETA($latitude1, $longitude1, $latitude2, $longitude2, $apikey, $data[$i]->fib_last_gpstime);
							printf("ETA KOORD 1: %s \r\n", $eta_data);

							if($eta_data != ""){
								unset($alert_eta);
								$alert_eta["fib_eta_status"] = 1;
								$alert_eta["fib_eta"] = date("Y-m-d H:i:s",strtotime($eta_data));
								$this->db->where("fib_vehicle",$data[$i]->fib_vehicle);
								$this->db->update("fib",$alert_eta);
								printf("UPDATE ETA %s , %s \r\n", $data[$i]->fib_vehicle, $eta_data);
							}

						}

					}else{
						printf("NO DATA KOORD #: \r\n");
					}
				}

				//jika customer 2 aktif cust 2 kosong  arrive 0 (belum sampe)
				if($data[$i]->fib_customer2 != "" && $data[$i]->fib_customer2_status == 1 && $data[$i]->fib_customer2_arrive == 0){

					//cek master customer
					$this->db->order_by("customer_id","desc");
					$this->db->where("customer_flag",0);
					$this->db->where("customer_status",1);
					$this->db->where("customer_code",$data[$i]->fib_customer2_code);
					$this->db->where("customer_unique",$data[$i]->fib_customer2_unique);
					$this->db->where('customer_koord <>','');
					$this->db->where('customer_koord is NOT NULL', NULL, FALSE);
					$q_c = $this->db->get("fib_cust");
					$row_c = $q_c->row();

					if(count($row_c)>0){
						$customer_coordinate = $row_c->customer_koord;
						$customer_geofence = $row_c->customer_geofence_name;

						printf("CEK ETA CUST 2: %s \r\n", $data[$i]->fib_vehicle);

						if($customer_coordinate == 0 || $customer_coordinate == ""){
							printf("NO DATA KOORD 2: %s \r\n", $customer_geofence);
						}else{
							//hitung jarak
							$customer_coordinate_data = explode(",", $customer_coordinate);
							$latitude1 = $customer_coordinate_data[0];
							$longitude1 = $customer_coordinate_data[1];

							//cek lasposition fib
							$latitude2 = $data[$i]->fib_last_lat;
							$longitude2 = $data[$i]->fib_last_long;

							//Apigoogle
							$apikey = "AIzaSyDjkxkZrIVJbT6Bv2nmJlK9OvNYTBcA2z0";
							//$apikey = $this->config->item('GOOGLE_MAP_API_KEY');
							$eta_data = $this->getETA($latitude1, $longitude1, $latitude2, $longitude2, $apikey, $data[$i]->fib_last_gpstime);
							printf("ETA KOORD 2: %s \r\n", $eta_data);

							if($eta_data != ""){
								unset($alert_eta);
								$alert_eta["fib_eta_status"] = 1;
								$alert_eta["fib_eta"] = date("Y-m-d H:i:s",strtotime($eta_data));
								$this->db->where("fib_vehicle",$data[$i]->fib_vehicle);
								$this->db->update("fib",$alert_eta);
								printf("UPDATE ETA 2 %s , %s \r\n", $data[$i]->fib_vehicle, $eta_data);
							}


						}

					}else{
						printf("NO DATA KOORD 2#: \r\n");
					}
				}

				//jika customer 3 aktif cust 2 kosong  arrive 0 (belum sampe)
				if($data[$i]->fib_customer3 != "" && $data[$i]->fib_customer3_status == 1 && $data[$i]->fib_customer3_arrive == 0){

					//cek master customer
					$this->db->order_by("customer_id","desc");
					$this->db->where("customer_flag",0);
					$this->db->where("customer_status",1);
					$this->db->where("customer_code",$data[$i]->fib_customer3_code);
					$this->db->where("customer_unique",$data[$i]->fib_customer3_unique);
					$this->db->where('customer_koord <>','');
					$this->db->where('customer_koord is NOT NULL', NULL, FALSE);
					$q_c = $this->db->get("fib_cust");
					$row_c = $q_c->row();

					if(count($row_c)>0){
						$customer_coordinate = $row_c->customer_koord;
						$customer_geofence = $row_c->customer_geofence_name;

						printf("CEK ETA CUST 3: %s \r\n", $data[$i]->fib_vehicle);

						if($customer_coordinate == 0 || $customer_coordinate == ""){
							printf("NO DATA KOORD 3: %s \r\n", $customer_geofence);
						}else{
							//hitung jarak
							$customer_coordinate_data = explode(",", $customer_coordinate);
							$latitude1 = $customer_coordinate_data[0];
							$longitude1 = $customer_coordinate_data[1];

							//cek lasposition fib
							$latitude2 = $data[$i]->fib_last_lat;
							$longitude2 = $data[$i]->fib_last_long;

							//Apigoogle
							$apikey = "AIzaSyDjkxkZrIVJbT6Bv2nmJlK9OvNYTBcA2z0";
							//$apikey = $this->config->item('GOOGLE_MAP_API_KEY');
							$eta_data = $this->getETA($latitude1, $longitude1, $latitude2, $longitude2, $apikey, $data[$i]->fib_last_gpstime);
							printf("ETA KOORD 3: %s \r\n", $eta_data);

							if($eta_data != ""){
								unset($alert_eta);
								$alert_eta["fib_eta_status"] = 1;
								$alert_eta["fib_eta"] = date("Y-m-d H:i:s",strtotime($eta_data));
								$this->db->where("fib_vehicle",$data[$i]->fib_vehicle);
								$this->db->update("fib",$alert_eta);
								printf("UPDATE ETA 3 %s , %s \r\n", $data[$i]->fib_vehicle, $eta_data);
							}


						}

					}else{
						printf("NO DATA KOORD 3#: \r\n");
					}
				}


			}

		}
		printf("PROSES ETA FINISH \r\n");
	}

	function cronfib_eta_new()
	{
		printf("PROSES GET ETA NEW \r\n");
		$now = date("Y-m-d");
		$limitdate = date("Y-m-d H:i:s", strtotime($now."00:00:00"));
		$this->db->where("fib_co_status",1); //yg sudah CO
		$this->db->where("fib_sj_status",1); //yg sudah ada sj
		$this->db->where("fib_sj >=",$limitdate); //hanya sj hari ini
		$this->db->where("fib_status",1); //yg aktif
		$q = $this->db->get("fib");
		$data = $q->result();

		if(isset($data))
		{

			for($i=0;$i<$q->num_rows;$i++)
			{
				$customer_coordinate = 0;
				$customer_geofence = "-";
				$customer2_coordinate = 0;
				$customer2_geofence = "-";
				$customer3_coordinate = 0;
				$customer3_geofence = "-";
				$customer_eta_duration_value = 0;

				//hitung eta
				//cari berdasarkan customer status == 1


				//khusus cust pertama ambil dari data master ETA
				if($data[$i]->fib_customer != "" && $data[$i]->fib_customer_status == 1 && $data[$i]->fib_customer_arrive == 0){

					//cek master customer
					$this->db->order_by("customer_id","desc");
					$this->db->where("customer_flag",0);
					$this->db->where("customer_status",1);
					$this->db->where("customer_code",$data[$i]->fib_customer_code);
					$this->db->where("customer_unique",$data[$i]->fib_customer_unique);
					$this->db->where('customer_koord <>','');
					$this->db->where('customer_koord is NOT NULL', NULL, FALSE);
					$this->db->where('customer_eta_duration is NOT NULL', NULL, FALSE);

					$q_c = $this->db->get("fib_cust");
					$row_c = $q_c->row();

					if(count($row_c)>0){
						$customer_coordinate = $row_c->customer_koord;
						$customer_geofence = $row_c->customer_geofence_name;
						$customer_eta_duration_value = $row_c->customer_eta_duration;

						printf("CEK ETA CUST 1: %s \r\n", $data[$i]->fib_vehicle);

						if($customer_coordinate == 0 || $customer_coordinate == ""){
							printf("NO DATA KOORD 1: %s \r\n", $customer_geofence);
						}else{
							//cari berdasarkan master ETA
							printf("ADA MASTER ETA 1: %s \r\n", $customer_geofence);
							if($customer_eta_duration_value != 0){

								$dateinterval = new DateTime($data[$i]->fib_co);
								$dateinterval->add(new DateInterval('PT'.$customer_eta_duration_value.'S'));
								$eta_data = $dateinterval->format('Y-m-d H:i:s');

								if($eta_data != ""){
									unset($alert_eta);
									$alert_eta["fib_eta_status"] = 1;
									$alert_eta["fib_eta"] = date("Y-m-d H:i:s",strtotime($eta_data));
									$this->db->limit(1);
									$this->db->where("fib_vehicle",$data[$i]->fib_vehicle);
									$this->db->update("fib",$alert_eta);
									printf("UPDATE ETA %s , %s \r\n", $data[$i]->fib_vehicle, $eta_data);
								}
							}
						}
					}
					else
					{
						printf("NO DATA KOORD #: \r\n");
					}
				}

				//jika customer 2 aktif cust 2 kosong arrive 0 (belum sampe)
				if($data[$i]->fib_customer2 != "" && $data[$i]->fib_customer2_status == 1 && $data[$i]->fib_customer2_arrive == 0){

					//cek master customer
					$this->db->order_by("customer_id","desc");
					$this->db->where("customer_flag",0);
					$this->db->where("customer_status",1);
					$this->db->where("customer_code",$data[$i]->fib_customer2_code);
					$this->db->where("customer_unique",$data[$i]->fib_customer2_unique);
					$this->db->where('customer_koord <>','');
					$this->db->where('customer_koord is NOT NULL', NULL, FALSE);
					$q_c = $this->db->get("fib_cust");
					$row_c = $q_c->row();

					if(count($row_c)>0){
						$customer_coordinate = $row_c->customer_koord;
						$customer_geofence = $row_c->customer_geofence_name;

						printf("CEK ETA CUST 2: %s \r\n", $data[$i]->fib_vehicle);

						if($customer_coordinate == 0 || $customer_coordinate == ""){
							printf("NO DATA KOORD 2: %s \r\n", $customer_geofence);
						}else{

							//cek lastposition fib
							$latitude1 = trim($data[$i]->fib_last_lat);
							$longitude1 = trim($data[$i]->fib_last_long);

							//koord cust
							$customer_coordinate_data = explode(",", $customer_coordinate);
							$latitude2 = trim($customer_coordinate_data[0]);
							$longitude2 = trim($customer_coordinate_data[1]);

							//Apigoogle
							$apikey = "AIzaSyDjkxkZrIVJbT6Bv2nmJlK9OvNYTBcA2z0";
							//$apikey = $this->config->item('GOOGLE_MAP_API_KEY');
							$eta_data = $this->getETA($latitude1, $longitude1, $latitude2, $longitude2, $apikey, $data[$i]->fib_last_gpstime);
							printf("ETA KOORD 2: %s \r\n", $eta_data);

							if($eta_data != ""){
								unset($alert_eta);
								$alert_eta["fib_eta_status"] = 1;
								$alert_eta["fib_eta"] = date("Y-m-d H:i:s",strtotime($eta_data));
								$this->db->limit(1);
								$this->db->where("fib_vehicle",$data[$i]->fib_vehicle);
								$this->db->update("fib",$alert_eta);
								printf("UPDATE ETA 2 %s , %s \r\n", $data[$i]->fib_vehicle, $eta_data);
							}


						}

					}else{
						printf("NO DATA KOORD 2#: \r\n");
					}
				}

				//jika customer 3 aktif cust 2 kosong arrive 0 (belum sampe)
				if($data[$i]->fib_customer3 != "" && $data[$i]->fib_customer3_status == 1 && $data[$i]->fib_customer3_arrive == 0){

					//cek master customer
					$this->db->order_by("customer_id","desc");
					$this->db->where("customer_flag",0);
					$this->db->where("customer_status",1);
					$this->db->where("customer_code",$data[$i]->fib_customer3_code);
					$this->db->where("customer_unique",$data[$i]->fib_customer3_unique);
					$this->db->where('customer_koord <>','');
					$this->db->where('customer_koord is NOT NULL', NULL, FALSE);
					$q_c = $this->db->get("fib_cust");
					$row_c = $q_c->row();

					if(count($row_c)>0){
						$customer_coordinate = $row_c->customer_koord;
						$customer_geofence = $row_c->customer_geofence_name;

						printf("CEK ETA CUST 3: %s \r\n", $data[$i]->fib_vehicle);

						if($customer_coordinate == 0 || $customer_coordinate == ""){
							printf("NO DATA KOORD 3: %s \r\n", $customer_geofence);
						}else{
							//cek lastposition fib
							$latitude1 = trim($data[$i]->fib_last_lat);
							$longitude1 = trim($data[$i]->fib_last_long);

							//koord cust
							$customer_coordinate_data = explode(",", $customer_coordinate);
							$latitude2 = trim($customer_coordinate_data[0]);
							$longitude2 = trim($customer_coordinate_data[1]);


							//Apigoogle
							$apikey = "AIzaSyDjkxkZrIVJbT6Bv2nmJlK9OvNYTBcA2z0";
							//$apikey = $this->config->item('GOOGLE_MAP_API_KEY');
							$eta_data = $this->getETA($latitude1, $longitude1, $latitude2, $longitude2, $apikey, $data[$i]->fib_last_gpstime);
							printf("ETA KOORD 3: %s \r\n", $eta_data);

							if($eta_data != ""){
								unset($alert_eta);
								$alert_eta["fib_eta_status"] = 1;
								$alert_eta["fib_eta"] = date("Y-m-d H:i:s",strtotime($eta_data));
								$this->db->limit(1);
								$this->db->where("fib_vehicle",$data[$i]->fib_vehicle);
								$this->db->update("fib",$alert_eta);
								printf("UPDATE ETA 3 %s , %s \r\n", $data[$i]->fib_vehicle, $eta_data);
							}


						}

					}else{
						printf("NO DATA KOORD 3#: \r\n");
					}
				}

			}

		}
		printf("ETA NEW FINISH \r\n");
	}

	function cronfib_eta_master()
	{
		printf("PROSES GET ETA MASTER \r\n");
		$now = date("Y-m-d");
		$limitdate = date("Y-m-d H:i:s", strtotime($now."00:00:00"));
		$this->db->order_by("customer_id","asc");
		$this->db->where("customer_flag",0);
		$this->db->where("customer_status",1);
		$this->db->where("customer_koord is NOT NULL", NULL, FALSE);
		$this->db->where("customer_koord <>","");
		$this->db->where("customer_eta_check",0);
		$q = $this->db->get("fib_cust");
		$data = $q->result();

		if(isset($data))
		{
			for($i=0;$i<$q->num_rows;$i++)
			{
				printf("CEK ETA MASTER CUST: %s \r\n", $data[$i]->customer_name);
					$base_koord = "";
					$duration_text = "";
					$duration_value = 0;
					$distance_text = "";
					$distance_value = 0;

					//Cek Base
					$this->db->where("fib_base_id",$data[$i]->customer_base);
					$this->db->where("fib_base_status",1);
					$this->db->where("fib_base_flag",0);
					$qb = $this->db->get("fib_base");
					$rbase = $qb->row();
					$total_base = count($rbase);
					if($total_base > 0){
						$base_koord = $rbase->fib_base_koord;
					}

					if($base_koord != ""){
						//origin (base)
						$customer_coordinate_origin = explode(",", $base_koord);
						$latitude1 = trim($customer_coordinate_origin[0]);
						$longitude1 = trim($customer_coordinate_origin[1]);

						//destination
						$customer_coordinate_dest = explode(",", $data[$i]->customer_koord);
						$latitude2 = trim($customer_coordinate_dest[0]);
						$longitude2 = trim($customer_coordinate_dest[1]);

						//Apigoogle
						//$apikey = $this->config->item('GOOGLE_MAP_API_KEY');
						$apikey = "AIzaSyDjkxkZrIVJbT6Bv2nmJlK9OvNYTBcA2z0";

						$eta_data = $this->getETA_master($latitude1, $longitude1, $latitude2, $longitude2, $apikey);
						printf("ETA MASTER KOORD: %s \r\n", $eta_data);

						if(isset($eta_data)){
							$duration_text = $eta_data['rows'][0]['elements'][0]['duration']['text'];
							$duration_value = $eta_data['rows'][0]['elements'][0]['duration']['value'];
							$distance_text = $eta_data['rows'][0]['elements'][0]['distance']['text'];
							$distance_value = $eta_data['rows'][0]['elements'][0]['distance']['value'];

							unset($master_eta);
							$master_eta["customer_eta_distance"] = $distance_value;
							$master_eta["customer_eta_distance_text"] = $distance_text;
							$master_eta["customer_eta_duration"] = $duration_value;
							$master_eta["customer_eta_duration_text"] = $duration_text;

							if(($distance_value > 0) && ($distance_value > 0)){
								$master_eta["customer_eta_check"] = 1; //sudah di cek
							}

							$this->db->limit(1);
							$this->db->where("customer_id",$data[$i]->customer_id);
							$this->db->update("fib_cust",$master_eta);
							printf("UPDATE ETA MASTER %s , %s \r\n", $data[$i]->customer_id, $data[$i]->customer_name);

						}
						//exit();
					}
					else
					{
						printf("NO DATA BASE KOORD \r\n");
					}
			}

		}

		printf("PROSES ETA MASTER FINISH \r\n");
	}

	function cronfib_lastposition($usercode="", $order="asc")
	{
		$nowdate = date('Y-m-d H:i:s');
		$now = date("Y-m-d");
		$limitdate = date("Y-m-d H:i:s", strtotime($now."00:00:00"));
		$offset=0;

		printf("Search USER CODE at %s \r\n", $nowdate);
		printf("======================================\r\n");

		//select list user code
		$this->db = $this->load->database("default", TRUE);
		if (isset($usercode) && ($usercode != ""))
		{
			$this->db->where("fib_user_code", $usercode);
		}
		$this->db->where("fib_user_status",1);
		$this->db->where("fib_user_flag",0);
		$quser = $this->db->get("fib_user");
		if ($quser->num_rows() == 0) return;

		$rowsuser = $quser->result();
		$totaluser = count($rowsuser);
		$m = 0;

		foreach($rowsuser as $rowuser)
		{
				if (($m+1) < $offset)
				{
					$m++;
					continue;
				}

			printf("Prepare Check Last POSITION USER : %s (%d/%d)\n", $rowuser->fib_user_name, ++$m, $totaluser);
			$user = $rowuser->fib_user_code;

			/*$this->db->order_by("","asc");
			$this->db->where("vehicle_status <>", 3);
			$this->db->where("vehicle_type", "T5");
			$this->db->where("vehicle_name <>", "Hino Trailer-TRL");
			$this->db->where("vehicle_user_id", $user);
			*/

			$this->db->order_by("vehicle_no","asc");
			$this->db->where("vehicle_status",1);
			$this->db->where("vehicle_user_id",$user);
			$this->db->where("fib_status",1); //only yg terdaftar di tbl fib
			$this->db->join("fib","vehicle_device=fib_vehicle", "left");

			$q = $this->db->get("vehicle");

			if ($q->num_rows() == 0)
			{
				printf("No Vehicles \r\n");
				//return;
			}

			$rows = $q->result();
			$totalvehicle = count($rows);
			printf("Total Vehicle:  %s \r\n", $totalvehicle);

			$j = 1;

			for ($i=0;$i<count($rows);$i++)
			{
				//select from db master


				printf("Process Check Last POSITION For %s %s (%d/%d) USER : %s \n", $rows[$i]->vehicle_no, $rows[$i]->vehicle_device, $j, $totalvehicle, $rowuser->fib_user_name);
				printf("execute %s\r\n", $rows[$i]->vehicle_no);

				$fib_last_position = "";
				$fib_last_geofence = "";
				$fib_last_gpstime = "";
				$fib_last_gpsstatus = "";
				$fib_last_lat = "";
				$fib_last_long = "";
				$fib_last_engine = "";
				$fib_last_speed = "";
				$fib_remark = "";
				$geofence_data = "";
				$geofence_name = "";

								// last position
								$vehicledevice = $rows[$i]->vehicle_device;

								$this->db->where("vehicle_status", 1);
								$this->db->where("vehicle_device", $vehicledevice);
								$qv = $this->db->get("vehicle");

								if ($qv->num_rows() == 0)
								{
									printf("No Data \r\n");
								}

								$rowvehicle = $qv->row();
								$rowvehicles = $qv->result();

								$t = $rowvehicle->vehicle_active_date2;
								$now = date("Ymd");

								if ($t < $now)
								{
									printf("Mobil Expired \r\n");
								}

								list($name, $host) = explode("@", $rowvehicle->vehicle_device);

								$gps = $this->gpsmodel->GetLastInfo($name, $host, true, false, 0, $rowvehicle->vehicle_type);

								/*if ($this->gpsmodel->fromsocket)
								{
									$datainfo = $this->gpsmodel->datainfo;
									$fromsocket = $this->gpsmodel->fromsocket;
								}*/

								if (! $gps)
								{
									printf("Gps Belum Aktif \r\n");
									/*$this->db = $this->load->database("default", TRUE);
									unset($board);

									$board["fib_last_engine"] = $fib_last_engine;
									$board["fib_last_position"] = $fib_last_position;
									$board["fib_last_lat"] = $fib_last_lat;
									$board["fib_last_long"] = $fib_last_long;
									$board["fib_last_geofence"] = $fib_last_geofence;
									$board["fib_last_gpstime"] = $fib_last_gpstime;
									$board["fib_last_gpsstatus"] = $fib_last_gpsstatus;
									$board["fib_last_speed"] = $fib_last_speed;
									$board["fib_remark"] = "GPS TROUBLE";
									$board["fib_remark_status"] = 1;

										//select fib master
										$this->db->select("fib_loaded,fib_loaded_status,fib_uj,fib_uj_status,fib_sj,fib_sj_status,fib_co,fib_co_status,
														   fib_out_time,fib_arrival_time
														  ");
										$this->db->where("fib_vehicle",$rowvehicle->vehicle_device);
										$q = $this->db->get("fib");
										if($q->num_rows > 0)
										{
											$row_m = $q->row();

											if($limitdate > $row_m->fib_loaded){
												$board["fib_loaded_status"] = 0;
											}
											if($limitdate > $row_m->fib_uj){
												$board["fib_uj_status"] = 0;
											}
											if($limitdate > $row_m->fib_sj){
												$board["fib_sj_status"] = 0;
												$board["fib_customer_status"] = 0;
												$board["fib_customer2_status"] = 0;
												$board["fib_customer3_status"] = 0;
												$board["fib_noso_status"] = 0;

											}
											if($limitdate > $row_m->fib_co){
												$board["fib_co_status"] = 0;
											}
											if($limitdate > $row_m->fib_arrival_time){
												$board["fib_arrival_time_status"] = 0;
											}
											if($limitdate > $row_m->fib_out_time){
												$board["fib_out_time_status"] = 0;
											}

										}

									$this->db->where("fib_vehicle",$rowvehicle->vehicle_device);
									$this->db->update("webtracking_fib",$board);
									printf("PROCESSED %s \r\n", $rowvehicle->vehicle_device); */

								}

								$gtps = $this->config->item("vehicle_gtp");

								//$dir = $gps->direction-1;
								$dirs = $this->config->item("direction");

								//io status
								if (in_array(strtoupper($rowvehicle->vehicle_type), $gtps))
								{
									if (! isset($datainfo))
									{
										if (isset($gps) && $gps && date("Ymd", $gps->gps_timestamp) >= date("Ymd"))
										{
											$tables = $this->gpsmodel->getTable($rowvehicle);
											$this->db = $this->load->database($tables["dbname"], TRUE);

										}
										else
										{
											$devices = explode("@", $rowvehicle->vehicle_device);
											$tables['info'] = sprintf("%s@%s_info", strtolower($devices[0]), strtolower($devices[1]));
											$this->db = $this->load->database("gpshistory", TRUE);
										}

										// ambil informasi di gps_info

										$this->db->order_by("gps_info_time", "DESC");
										$this->db->where("gps_info_device", $rowvehicle->vehicle_device);
										$q = $this->db->get($tables['info'], 1, 0);
									}

									if ((! isset($datainfo)) && ($q->num_rows() == 0))
									{
										$engine = "OFF";
									}
									else
									{
										$rowinfo = isset($datainfo) ? $datainfo : $q->row();
										$ioport = $rowinfo->gps_info_io_port;

										$status3 = ((strlen($ioport) > 1) && ($ioport[1] == 1)); // opened/closed
										$status2 = ((strlen($ioport) > 3) && ($ioport[3] == 1)); // release/hold
										$status1 = ((strlen($ioport) > 4) && ($ioport[4] == 1)); // on/off

										$engine = $status1 ? "ON" : "OFF";

									}

								}

								$this->db = $this->load->database("default", TRUE);
								$skip = 0;

								if(isset($gps->gps_timestamp)){

									$delta = ((mktime() - $gps->gps_timestamp)); // tidak dikurangi 3600 detik

									//cek delay kurang dari 10 menit
									if ($delta >= 600 && $delta <= 43200) //lebih 10 menit kurang dari 12 jam //yellow condition
									{
										printf("Vehicle No %s GPS DELAY \r\n", $rowvehicle->vehicle_no);
										$fib_last_gpsstatus = "GPS DELAY";
										$fib_remark = "";

									}
									else if($delta >= 43201) //lebih dari 1 hari //red condition
									{
										printf("===GPS RED=== \r\n");
										$fib_last_gpsstatus = "GPS RED";
										$fib_remark = "GPS TROUBLE";
									}
									else
									{
										if($gps->gps_status == "V"){
											printf("Vehicle No %s NOT OK \r\n", $rowvehicle->vehicle_no);
											$fib_last_gpsstatus = "GPS NOT OK";

										}else{
											printf("===GPS UPDATE=== \r\n");
											$fib_last_gpsstatus = "GPS UPDATE";

										}

										$fib_remark = "";
									}
								}else{
									printf("===NO DATA=== \r\n");
								}

								if(isset($gps)){
									$nowdate_gps = $gps->gps_time;
									$dateinterval = new DateTime($nowdate_gps);
									$dateinterval->add(new DateInterval('PT7H'));
									$nowdate_gps = $dateinterval->format('Y-m-d H:i:s');

									$fib_last_engine = $engine;
									$fib_last_gpstime = $nowdate_gps;
									$fib_last_lat = $gps->gps_latitude_real;
									$fib_last_long = $gps->gps_longitude_real;
									$fib_last_position = $this->getPosition($gps->gps_longitude, $gps->gps_ew, $gps->gps_latitude, $gps->gps_ns);
									$fib_last_geofence = $this->getGeofence($gps->gps_longitude, $gps->gps_ew, $gps->gps_latitude, $gps->gps_ns, $rowvehicle->vehicle_user_id);
									$fib_last_speed = $gps->gps_speed;

										//gps bengkel
										if(isset($fib_last_geofence)){
											$geofence_data = explode("#", $fib_last_geofence);
											if(count($geofence_data)>1){
												$geofence_name = $geofence_data[0];

												//jika geofence bengkel
												if($geofence_name == "bengkel"){
													$fib_remark = "SERVICE AREA";
												}
											}
										}

									//$this->dbmaster = $this->load->database("master", TRUE);
									unset($board);

									$board["fib_last_engine"] = $fib_last_engine;
									$board["fib_last_position"] = $fib_last_position->display_name;
									$board["fib_last_lat"] = $fib_last_lat;
									$board["fib_last_long"] = $fib_last_long;
									$board["fib_last_geofence"] = $fib_last_geofence;
									$board["fib_last_gpstime"] = $fib_last_gpstime;
									$board["fib_last_gpsstatus"] = $fib_last_gpsstatus;
									$board["fib_last_speed"] = $fib_last_speed;
									$board["fib_remark"] = $fib_remark;
									$board["fib_remark_status"] = 1;


										//select fib master
									/*	$this->db->select("fib_loaded,fib_loaded_status,fib_uj,fib_uj_status,fib_sj,fib_sj_status,fib_co,fib_co_status,
														   fib_arrival_time,fib_out_time
														  ");
										$this->db->where("fib_vehicle",$rowvehicle->vehicle_device);
										$q = $this->db->get("fib");
										if($q->num_rows > 0)
										{
											$row_m = $q->row();
											if($limitdate > $row_m->fib_loaded){
												$board["fib_loaded_status"] = 0;
											}
											if($limitdate > $row_m->fib_uj){
												$board["fib_uj_status"] = 0;
											}
											if($limitdate > $row_m->fib_sj){
												$board["fib_sj_status"] = 0;
												$board["fib_customer_status"] = 0;
												$board["fib_customer2_status"] = 0;
												$board["fib_customer3_status"] = 0;
												$board["fib_noso_status"] = 0;
											}
											if($limitdate > $row_m->fib_co){
												$board["fib_co_status"] = 0;
											}
											if($limitdate > $row_m->fib_arrival_time){
												$board["fib_arrival_time_status"] = 0;
											}
											if($limitdate > $row_m->fib_out_time){
												$board["fib_out_time_status"] = 0;
											}

										}
									*/

									$this->db->where("fib_vehicle",$rowvehicle->vehicle_device);
									$this->db->update("webtracking_fib",$board);
									printf("PROCESSED %s \r\n", $rowvehicle->vehicle_device);
									//exit();

								}else{
									printf("===NO DATA GPS=== \r\n");
								}



				$j++;
			}


		}
		$this->db->close();
		$this->db->cache_delete_all();
		$enddate = date('Y-m-d H:i:s');
		printf("FINISH Check Last POSITION from %s to %s \r\n", $nowdate, $enddate);
		printf("============================== \r\n");

	}

	function cronfib_tripmileage_realtime($vdevice="", $vtype="")
	{
		//ini_set('memory_limit', '-1');
		printf("PROSES TRIPMILEAGE REALTIME KHUSUS PORT >> START \r\n");
        $startproses = date("Y-m-d H:i:s");

		$start_time = date("Y-m-d H:i:s");
		$report_type = "tripmileage_realtime";
		$report = "fib_tripmileage";
		$user = 860;
        $z =0;

		$this->db->order_by("vehicle_no","desc");
		$this->db->where("vehicle_status",1);
		$this->db->where("vehicle_user_id",$user);
		if($vdevice != "" && $vtype != ""){
			$this->db->where("vehicle_device",$vdevice."@".$vtype);
		}
		$this->db->where("fib_status",1); //only yg terdaftar di tbl fib
		$this->db->join("fib","vehicle_device=fib_vehicle", "left");

		$q = $this->db->get("vehicle");
		$rowvehicle = $q->result();

		$total_process = count($rowvehicle);
		printf("STARTING TIME	 : %s \r\n",$start_time);
        printf("TOTAL PROSES VEHICLE : %s \r\n",$total_process);
        printf("============================================ \r\n");

		for ($x=0;$x<count($rowvehicle);$x++)
		{

			printf("PROSES VEHICLE : %s (%d/%d) \r\n",$rowvehicle[$x]->vehicle_device, ++$z, $total_process);
			//PORT Only
			if (isset($rowvehicle[$x]->vehicle_info))
			{
				$json = json_decode($rowvehicle[$x]->vehicle_info);
                if (isset($json->vehicle_ip) && isset($json->vehicle_port))
                {

					$databases = $this->config->item('databases');
					if (isset($databases[$json->vehicle_ip][$json->vehicle_port]))
					{
						$database = $databases[$json->vehicle_ip][$json->vehicle_port];
						$table = $this->config->item("external_gpstable");
						$tableinfo = $this->config->item("external_gpsinfotable");
						$this->dbhist = $this->load->database($database, TRUE);
						//$this->dbhist2 = $this->load->database("gpshistory",true);
					}
					else
					{
						$table = $this->gpsmodel->getGPSTable($rowvehicle[$x]->vehicle_type);
						$tableinfo = $this->gpsmodel->getGPSInfoTable($rowvehicle[$x]->vehicle_type);
						$this->dbhist = $this->load->database("default", TRUE);
						//$this->dbhist2 = $this->load->database("gpshistory",true);
					}

					$vehicle_device = explode("@", $rowvehicle[$x]->vehicle_device);
                    $vehicle_no = $rowvehicle[$x]->vehicle_no;
					$vehicle_dev = $rowvehicle[$x]->vehicle_device;
					$vehicle_name = $rowvehicle[$x]->vehicle_name;
					$lastcheck_gps = "";

					//select master fib
					$this->db->order_by("fib_id","desc");
					$this->db->select("fib_vehicle,fib_last_check");
					$this->db->where("fib_vehicle", $rowvehicle[$x]->vehicle_device);
					$this->db->where("fib_status", 1);
					$qfib = $this->db->get('fib');
					$rowfib = $qfib->row();

					//jika ada di master fib
					if(count($rowfib)>0){

						$sdate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($rowfib->fib_last_check))); //from last check
						$edate = date("Y-m-d H:i:s", strtotime("-7 hour", strtotime($start_time))); //until now

						if ($rowvehicle[$x]->vehicle_type == "T5" || $rowvehicle[$x]->vehicle_type == "T5 PULSE")
						{
							$tablehist = $vehicle_device[0]."@t5_gps";
							$tablehistinfo = $vehicle_device[0]."@t5_info";
						}
						else
						{
							$tablehist = strtolower($vehicle_device[0])."@".strtolower($vehicle_device[1])."_gps";
							$tablehistinfo = strtolower($vehicle_device[0])."@".strtolower($vehicle_device[1])."_info";
						}

							$this->dbhist->join($tableinfo, "gps_info_time = gps_time AND gps_info_device = CONCAT(gps_name,'@',gps_host)");
							$this->dbhist->where("gps_info_device", $rowvehicle[$x]->vehicle_device);
							$this->dbhist->where("gps_time >=", $sdate);
							$this->dbhist->where("gps_time <=", $edate);
							$this->dbhist->order_by("gps_time","asc");
							$this->dbhist->from($table);
							$q = $this->dbhist->get();
							$rows1 = $q->result();

							$rows = $rows1;

							//write data ON
							$data = array();
							$nopol = "";
							$on = false;
							$trows = count($rows);

							printf("TOTAL DATA : %s \r\n",$trows);

							for($i=0;$i<$trows;$i++)
							{
								$lastcheck_gps = $rows[$i]->gps_time;
								//if($rows[$i]->gps_speed == 0) continue;
								if($nopol != $rowvehicle[$x]->vehicle_no)
								{ //new vehicle
									if($on && $i!=0)
									{
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowvehicle[$x]->vehicle_user_id);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i-1]->gps_longitude, $rows[$i-1]->gps_ew, $rows[$i-1]->gps_latitude, $rows[$i-1]->gps_ns);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i-1]->gps_info_distance;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i-1]->gps_latitude_real.", ".$rows[$i-1]->gps_longitude_real;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_engine'] = substr($rows[$i-1]->gps_info_io_port, 4, 1);
									}

									if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1)
									{
										$trip_no = 1;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowvehicle[$x]->vehicle_user_id);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_coordinate'] = $rows[$i]->gps_latitude_real.", ".$rows[$i]->gps_longitude_real;
										$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_engine'] = substr($rows[$i]->gps_info_io_port, 4, 1);

										$on = true;

										if($i==$trows-1)
										{
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowvehicle[$x]->vehicle_user_id);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude_real.", ".$rows[$i]->gps_longitude_real;
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_engine'] = substr($rows[$i]->gps_info_io_port, 4, 1);
										}
									}
									else
									{
										$trip_no = 1;
										$on = false;
									}
								}
								else
								{ //same vehicle
									if(substr($rows[$i]->gps_info_io_port, 4, 1) == 1)
									{
										if(!$on)
										{
											$data[$rowvehicle[$x]->vehicle_no][$trip_no++]['start_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_geofence_location'] = $this->getGeofence($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowvehicle[$x]->vehicle_user_id);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_mileage'] = $rows[$i]->gps_info_distance;
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['vehicle_name'] = $rowvehicle[$x]->vehicle_name;
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_coordinate'] = $rows[$i]->gps_latitude_real.", ".$rows[$i]->gps_longitude_real;
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['start_engine'] = substr($rows[$i]->gps_info_io_port, 4, 1);
										}
										$on = true;
										if($i==$trows-1 && $on)
										{
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowvehicle[$x]->vehicle_user_id);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude_real.", ".$rows[$i]->gps_longitude_real;
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_engine'] = substr($rows[$i]->gps_info_io_port, 4, 1);
										}
									}
									else
									{
										if($on)
										{
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_time'] = date("d-m-Y H:i:s", strtotime("+7 hour", strtotime($rows[$i]->gps_time)));
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_position'] = $this->getPosition($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_geofence_location'] = $this->getGeofence($rows[$i]->gps_longitude, $rows[$i]->gps_ew, $rows[$i]->gps_latitude, $rows[$i]->gps_ns, $rowvehicle[$x]->vehicle_user_id);
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_mileage'] = $rows[$i]->gps_info_distance;
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_coordinate'] = $rows[$i]->gps_latitude_real.", ".$rows[$i]->gps_longitude_real;
											$data[$rowvehicle[$x]->vehicle_no][$trip_no-1]['end_engine'] = substr($rows[$i]->gps_info_io_port, 4, 1);
										}
										$on = false;
									}
								}
								$nopol = $rowvehicle[$x]->vehicle_no;
							}

							if(count($data) > 0)
							{
								$j=1;
								$new = "";
								unset($insert_data);
								foreach($data as $vehicle_no=>$val)
								{
									if($new != $vehicle_no)
									{
										$cumm = 0;
										$trip_no = 1;
									}

									foreach($val as $no=>$report)
									{
										$mileage = $report['end_mileage']- $report['start_mileage'];
										if($mileage != 0)
										{
											//$duration = get_time_difference($report['start_time'], $report['end_time']);
											$start_1 = dbmaketime($report['start_time']);
											$end_1 = dbmaketime($report['end_time']);
											$duration_sec = $end_1 - $start_1;

											$show = "";
											/*if($duration[0]!=0)
											{
												$show .= $duration[0] ." Day ";
											}
											if($duration[1]!=0)
											{
												$show .= $duration[1] ." Hour ";
											}
											if($duration[2]!=0)
											{
												$show .= $duration[2] ." Min ";
											}
											if($show == "")
											{
												$show .= "0 Min";
											}*/
											$tm = $mileage/1000;
											$cumm += $tm;
											$insert_data['fib_tripmileage_vehicle_id'] = $vehicle_dev;
											$insert_data['fib_tripmileage_vehicle_no'] = $vehicle_no;
											$insert_data['fib_tripmileage_vehicle_name'] = $report['vehicle_name'];
											$insert_data['fib_tripmileage_trip_no'] = $trip_no++;
											$insert_data['fib_tripmileage_start_time'] = date("Y-m-d H:i:s", strtotime($report['start_time']));
											$insert_data['fib_tripmileage_end_time'] = date("Y-m-d H:i:s", strtotime($report['end_time']));
											$insert_data['fib_tripmileage_duration'] = $show;
											$insert_data["fib_tripmileage_duration_sec"] = $duration_sec;
											$insert_data['fib_tripmileage_trip_mileage'] = $tm;
											$insert_data['fib_tripmileage_cummulative_mileage'] = $cumm;

											$insert_data['fib_tripmileage_coordinate_start'] = $report['start_coordinate'];
											$insert_data['fib_tripmileage_coordinate_end'] = $report['end_coordinate'];
											$insert_data['fib_tripmileage_geofence_start'] = $report['start_geofence_location'];
											$insert_data['fib_tripmileage_geofence_end'] = $report['end_geofence_location'];

											$insert_data['fib_tripmileage_location_start'] = $report['start_position']->display_name;
											$insert_data['fib_tripmileage_location_end'] = $report['end_position']->display_name;

											$insert_data['fib_tripmileage_engine_start'] = $report['start_engine'];
											$insert_data['fib_tripmileage_engine_end'] = $report['end_engine'];


											$this->db->insert("fib_tripmileage", $insert_data);
											printf("INSERT ON DB HTM REALTIME OK \r\n");

											//update last check to master FIB
											$lastcheck_gps_now = date("Y-m-d H:i:s", strtotime("+7 hour", strtotime($lastcheck_gps)));
											//print_r($lastcheck_gps." ".$lastcheck_gps_now);exit();

											unset($data_fib);
											$data_fib["fib_last_check"] = $lastcheck_gps_now;
											$this->db->limit(1);
											$this->db->where("fib_vehicle",$rowvehicle[$x]->vehicle_device);
											$this->db->update("fib",$data_fib);
											printf("UPDATE FIB MASTER OK \r\n");

											printf("DELETE CACHE HISTORY \r\n");
											$this->dbhist->cache_delete_all();
											unset($data);
											printf("FINISH FOR VEHICLE : %s \r\n",$rowvehicle[$x]->vehicle_device);


										}
									}
								}
							}
							//end write on

							unset($data_fib_lasthtm);
							$data_fib_lasthtm["fib_last_htm"] = date("Y-m-d H:i:s");
							$this->db->limit(1);
							$this->db->where("fib_vehicle",$rowfib->fib_vehicle);
							$this->db->update("fib",$data_fib_lasthtm);
							printf("UPDATE LAST CEK TRIPMILEAGE \r\n");
							printf("============================================ \r\n");

					}
					else
					{
						printf("NO DATA IN FIB MASTER : %s \r\n", $rowvehicle[$x]->vehicle_device);
					}
				}
				else
				{
					printf("SKIP VEHICLE ( NO VEHICLE PORT ) \r\n");
					printf("-------------------------------------- \r\n");
				}

			}
			else
			{
				printf("SKIP VEHICLE ( NO VEHICLE INFO ) \r\n");
				printf("-------------------------------- \r\n");
			}
		}


		$finish_time = date("Y-m-d H:i:s");
		printf("DONE TRIP MILEAGE DB PORT REALTIME: %s \r\n",$finish_time);

		$this->db->close();
		$this->db->cache_delete_all();

		return;
	}

	function cronfib_tripmileage_delete($startdate = "", $enddate = "")
	{
		printf("PROSES DELETE TRIPMILEAGE REALTIME >> START \r\n");

		$start_time = date("Y-m-d H:i:s");
		$now_date = date("Y-m-d");
		$table_report = "fib_tripmileage";

		if($startdate == ""){
			//2 hari sebelum //edited 7
			$date = new DateTime($now_date);
			$interval = new DateInterval('P7D');
			$date->sub($interval);
			$startdate_ex = $date->format('Y-m-d');
			$limit_startdate = date("Y-m-d H:i:s", strtotime($startdate_ex." "."00"."00"."00"));
		}

		if($startdate != ""){
			$limit_startdate = date("Y-m-d H:i:s", strtotime($startdate." "."00"."00"."00"));
		}

		if($enddate != ""){
			$limit_enddate = date("Y-m-d H:i:s", strtotime($enddate." "."23"."59"."59"));
		}

		if($enddate == ""){
			//2 hari sebelum // edited 7
			$date = new DateTime($now_date);
			$interval = new DateInterval('P7D');
			$date->sub($interval);
			$enddate_ex = $date->format('Y-m-d');
			$limit_enddate = date("Y-m-d H:i:s", strtotime($enddate_ex." "."23"."59"."59"));
		}


		$this->db->order_by("fib_tripmileage_id","asc");
		$this->db->where("fib_tripmileage_start_time >=",$limit_startdate);
		$this->db->where("fib_tripmileage_start_time <=",$limit_enddate);
		$this->db->delete($table_report);

		$finish_time = date("Y-m-d H:i:s");
		printf("DONE DELETE TRIP MILEAGE REALTIME: %s - %s \r\n",$start_time, $finish_time);

		$this->db->close();
		$this->db->cache_delete_all();

		return;
	}

	function cronfib_clear()
	{
		printf("PROSES GET FIB \r\n");
		$nowdate = date("Y-m-d H:i:s");
		$this->db->where("fib_status",1);
		$q = $this->db->get("fib");
		$data = $q->result();

		if(isset($data))
		{
			for($i=0;$i<$q->num_rows;$i++)
			{
				printf("GET DATA FIB \r\n");
				$update = 0;
					//jika data lebih dari limit date maka di clear
					unset($alert);

						if($data[$i]->fib_loaded_status == 1){
							$dateinterval = new DateTime($data[$i]->fib_loaded);
							$dateinterval->add(new DateInterval('PT48H')); //khusus loading limit 2 hari (kondisi jika loading sabtu berangkat senin)
							$limitdate = $dateinterval->format('Y-m-d H:i:s');

							if($nowdate >= $limitdate){
								$alert["fib_loaded_status"] = 0;
								$update = 1;
							}
						}

						if($data[$i]->fib_sj_status == 1){
							$dateinterval = new DateTime($data[$i]->fib_sj);
							$dateinterval->add(new DateInterval('PT48H'));
							$limitdate = $dateinterval->format('Y-m-d H:i:s');

							if($nowdate >= $limitdate){
								$alert["fib_sj_status"] = 0;
								$alert["fib_customer"] = "";
								$alert["fib_customer_code"] = "";
								$alert["fib_customer_arrive"] = 0;
								$alert["fib_customer_status"] = 0;
								$alert["fib_customer_unique"] = "";

								$alert["fib_customer2"] = "";
								$alert["fib_customer2_code"] = "";
								$alert["fib_customer2_arrive"] = 0;
								$alert["fib_customer2_status"] = 0;
								$alert["fib_customer2_unique"] = "";

								$alert["fib_customer3"] = "";
								$alert["fib_customer3_code"] = "";
								$alert["fib_customer3_arrive"] = 0;
								$alert["fib_customer3_status"] = 0;
								$alert["fib_customer3_unique"] = "";

								$alert["fib_noso_status"] = 0;
								$update = 1;
							}
						}
						if($data[$i]->fib_uj_status == 1){
							$dateinterval = new DateTime($data[$i]->fib_uj);
							$dateinterval->add(new DateInterval('PT48H'));
							$limitdate = $dateinterval->format('Y-m-d H:i:s');

							if($nowdate >= $limitdate){
								$alert["fib_uj_status"] = 0;
								$update = 1;
							}
						}
						if($data[$i]->fib_co_status == 1){
							$dateinterval = new DateTime($data[$i]->fib_co);
							$dateinterval->add(new DateInterval('PT24H'));
							$limitdate = $dateinterval->format('Y-m-d H:i:s');

							if($nowdate >= $limitdate){
								$alert["fib_co_status"] = 0;
								$alert["fib_co_ischeck"] = 0;
								$update = 1;
							}
						}

						if($data[$i]->fib_eta_status == 1){
							$dateinterval = new DateTime($data[$i]->fib_eta);
							$dateinterval->add(new DateInterval('PT24H'));
							$limitdate = $dateinterval->format('Y-m-d H:i:s');

							if($nowdate >= $limitdate){
								$alert["fib_eta_status"] = 0;
								$update = 1;
							}
						}
						if($data[$i]->fib_arrival_time_status == 1){
							$dateinterval = new DateTime($data[$i]->fib_arrival_time);
							$dateinterval->add(new DateInterval('PT24H'));
							$limitdate = $dateinterval->format('Y-m-d H:i:s');

							if($nowdate >= $limitdate){
								$alert["fib_arrival_time_status"] = 0;
								$alert["fib_arrival_name"] = "";
								$update = 1;
							}
						}
						if($data[$i]->fib_out_time_status == 1){
							$dateinterval = new DateTime($data[$i]->fib_out_time);
							$dateinterval->add(new DateInterval('PT24H'));
							$limitdate = $dateinterval->format('Y-m-d H:i:s');

							if($nowdate >= $limitdate){
								$alert["fib_out_time_status"] = 0;
								$alert["fib_out_name"] = "";
								$update = 1;
							}
						}

						if(isset($update) && ($update == 1)){
							$this->db->where("fib_vehicle",$data[$i]->fib_vehicle);
							$this->db->update("fib",$alert);

							printf("PROCESSED: %s \r\n", $data[$i]->fib_vehicle);
						}
						else
						{
							printf("NO DATA UPDATED : %s \r\n", $data[$i]->fib_vehicle);
						}

			}
		}
		printf("PROSES CLEAR FINISH \r\n");
	}

	function cronfib_clear_avplus()
	{
		printf("PROSES GET AVPLUS \r\n");
		$now = date("Y-m-d");
		$limitdate = date("Y-m-d H:i:s", strtotime($now."00:00:00"));

		$this->db->where("fib_av_plus",1); //yg av+
		$this->db->where("fib_status",1);
		$q = $this->db->get("fib");
		$data = $q->result();
		//print_r(count($data));exit();
		if(isset($data))
		{
			for($i=0;$i<$q->num_rows;$i++)
			{
				unset($alert);
				$alert["fib_av_plus"] = 0;
				$this->db->where("fib_vehicle",$data[$i]->fib_vehicle);
				$this->db->update("fib",$alert);
				printf("PROCESSED: %s \r\n", $data[$i]->fib_vehicle);
			}
		}
		printf("PROSES CLEAR AVPLUS FINISH \r\n");
	}

	//for all
    function getPosition($longitude, $ew, $latitude, $ns){
        $gps_longitude_real = getLongitude($longitude, $ew);
        $gps_latitude_real = getLatitude($latitude, $ns);

        $gps_longitude_real_fmt = number_format($gps_longitude_real, 4, ".", "");
        $gps_latitude_real_fmt = number_format($gps_latitude_real, 4, ".", "");
        $georeverse = $this->gpsmodel->GeoReverse($gps_latitude_real_fmt, $gps_longitude_real_fmt);

        return $georeverse;
    }

	function getGeofence($longitude, $ew, $latitude, $ns, $userid)
    {

        $this->db = $this->load->database("default", true);
        $lng = getLongitude($longitude, $ew);
        $lat = getLatitude($latitude, $ns);

        $sql = sprintf("
                    SELECT     *
                    FROM     %sgeofence
                    WHERE     TRUE
                            AND CONTAINS(geofence_polygon, GEOMFROMTEXT('POINT(%s %s)'))
                            AND (geofence_user = %s )
                            AND (geofence_status = 1)
					ORDER BY geofence_id desc LIMIT 1 OFFSET 0", $this->db->dbprefix, $lng, $lat, $userid);

        $q = $this->db->query($sql);

        if ($q->num_rows() > 0)
        {
            $row = $q->result();
            $total = $q->num_rows();
            for ($i=0;$i<$total;$i++){
            $data = $row[$i]->geofence_name;
            return $data;
            }

        }else
        {
			$data = "";
            return $data;
        }

    }

	function getETA($latitude1, $longitude1, $latitude2, $longitude2, $apikey, $lastgpstime){
        $dataJson = file_get_contents("https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins=".$latitude1.",".$longitude1."&destinations=".$latitude2.",".$longitude2."&key=".$apikey."");
		printf("JSON API %s \r\n", $dataJson);
		$data = json_decode($dataJson,true);
		$api_status = $data['rows'][0]['elements'][0]['status']['value'];
		$eta = "";

		if($api_status == "O"){
			$duration_sec = $data['rows'][0]['elements'][0]['duration']['value'];
			$dateinterval = new DateTime($lastgpstime);
			$dateinterval->add(new DateInterval('PT'.$duration_sec.'S'));
			$eta = $dateinterval->format('Y-m-d H:i:s');
		}
		printf("ETA %s \r\n", $eta);
        return $eta;
    }

	function getETA_master($latitude1, $longitude1, $latitude2, $longitude2, $apikey){
        $dataJson = file_get_contents("https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins=".$latitude1.",".$longitude1."&destinations=".$latitude2.",".$longitude2."&key=".$apikey."");
		printf("JSON API %s \r\n", $dataJson);
		$data = json_decode($dataJson,true);
		/*$api_status = $data['rows'][0]['elements'][0]['status']['value'];
		$eta = "";

		if($api_status == "O"){
			$duration_sec = $data['rows'][0]['elements'][0]['duration']['value'];
			$dateinterval = new DateTime($lastgpstime);
			$dateinterval->add(new DateInterval('PT'.$duration_sec.'S'));
			$eta = $dateinterval->format('Y-m-d H:i:s');
		}
		printf("ETA %s \r\n", $eta);*/
        //return $eta;
		return $data;

    }

	function sync_vehicle($user="")
	{
		$start_time = date("d-m-Y H:i:s");
		printf("PROSES SYNC FIB VEHICLE \r\n");

		$offset = 0; $i = 0;

		$this->db->order_by("vehicle_id","asc");
		$this->db->where("vehicle_user_id",$user);
		$q = $this->db->get("vehicle");
		$rows = $q->result();
		$total = count($rows);
		printf("TOTAL : %s \r\n", $total);
		//exit();
		foreach($rows as $row)
		{
			if (($i+1) < $offset)
			{
				$i++;
				continue;
			}

			printf("PROCESS NUMBER	 : %s \r\n", ++$i." of ".$total);

			$devices = explode("@", $row->vehicle_device);

			unset($data);

			$data["fib_vehicle"] = $row->vehicle_device;
			$data["fib_vehicle_user_id"] = $row->vehicle_user_id;
			$data["fib_vehicle_no"] = $row->vehicle_no;
			$data["fib_vehicle_name"] = $row->vehicle_name;
			$data["fib_vehicle_company"] = $row->vehicle_company;
			$data["fib_vehicle_subcompany"] = $row->vehicle_subcompany;
			$data["fib_vehicle_group"] = $row->vehicle_group;
			$data["fib_vehicle_subgroup"] = $row->vehicle_subgroup;
			if($row->vehicle_status == 3){
				$data["fib_flag"] = 1;
			}else{
				$data["fib_flag"] = 0;
			}

			$this->db->select("fib_vehicle_no");
			$this->db->where("fib_vehicle", $devices[0]."@".$devices[1]);
			$qu = $this->db->get("fib");
			$ru = $qu->row();
			if (count($ru)>0)
			{
				printf("UPDATE FIB MASTER : %s \r\n", $row->vehicle_no);
				$this->db->where("fib_vehicle", $devices[0]."@".$devices[1]);
				$this->db->update("fib",$data);
			}
			else
			{
				printf("INSERT FIB MASTER : %s \r\n", $row->vehicle_no);
				$this->db->insert("fib",$data);
			}
			printf("FINISH SYNC : %s \r\n", $row->vehicle_no);
			printf("=============================================== \r\n");

		}

		$finish_time = date("d-m-Y H:i:s");
		printf("SELESAI"." ".$finish_time);

	}
}
