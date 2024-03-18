	<?php
include "base.php";

class Operational_report extends Base {
	var $otherdb;
	
	function Operational_report()
	{
		parent::Base();	
		$this->load->model("gpsmodel");
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
		$this->load->helper('common_helper');
		
	}
	
	function index()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		
		$this->db->select("vehicle.*, user_name");		
		$this->db->order_by("vehicle_no", "asc");		
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_type <>", "TJAM");
		
		if ($this->sess->user_type == 2)
		{			
			$this->db->where("vehicle_user_id", $this->sess->user_id);
			$this->db->or_where("vehicle_company", $this->sess->user_company);
			$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		}
		else
		if ($this->sess->user_type == 3)
		{
			$this->db->where("user_agent", $this->sess->user_agent);			
		}
		//tambahan, user group yg open playback report
		if ($this->sess->user_group <> 0)
		{
			$this->db->where("vehicle_group", $this->sess->user_group);			
		}
	
		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q = $this->db->get("vehicle");
		
		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}
		
		$rows = $q->result();
		$this->params["vehicles"] = $rows;
		
		$this->params["content"] = $this->load->view('transporter/report/mn_operational', $this->params, true);		
		$this->load->view("templatesess", $this->params);
	}
	
	function dataoperational()
	{
		ini_set('display_errors', 1); 
//		ini_set('memory_limit', '2G');
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$vehicle = $this->input->post("vehicle");
		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("enddate");
		$engine = $this->input->post("engine");
		$view_map = $this->input->post("view_map");
		$report = "operasional_";
		$report_sum = "summary_";
		
		$sdate = date("Y-m-d H:i:s", strtotime($startdate." "."00:00:00"));
		$edate = date("Y-m-d H:i:s", strtotime($enddate." "."23:59:59"));
		
		$m1 = date("F", strtotime($startdate)); 
		$m2 = date("F", strtotime($enddate)); 
		$year = date("Y", strtotime($startdate));
		$year2 = date("Y", strtotime($enddate));
		$rows = array();
		$rows2 = array();
		$total_q = 0;
		$total_q2 = 0;
		
		$error = "";
		$rows_summary = "";
		
		
		if ($vehicle == "" || $vehicle == 0)
		{
			$error .= "- Invalid Vehicle. Silahkan Pilih salah satu kendaraan! \n";	
		}
		if ($m1 != $m2)
		{
			$error .= "- Invalid Date. Tanggal Report yang dipilih harus dalam bulan yang sama! \n";	
		}
		
		if ($year != $year2)
		{
			$error .= "- Invalid Year. Tanggal Report yang dipilih harus dalam tahun yang sama! \n";	
		}
		
		if ($error != "")
		{
			$callback['error'] = true;	
			$callback['message'] = $error;	
			
			echo json_encode($callback);			
			return;
		}
		
		switch ($m1)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			$dbtable_sum = $report_sum."januari_".$year;
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			$dbtable_sum = $report_sum."februari_".$year;
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			$dbtable_sum = $report_sum."maret_".$year;
			break;
			case "April":
            $dbtable = $report."april_".$year;
			$dbtable_sum = $report_sum."april_".$year;
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			$dbtable_sum = $report_sum."mei_".$year;
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			$dbtable_sum = $report_sum."juni_".$year;
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			$dbtable_sum = $report_sum."juli_".$year;
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			$dbtable_sum = $report_sum."agustus_".$year;
			break;
			case "September":
            $dbtable = $report."september_".$year;
			$dbtable_sum = $report_sum."september_".$year;
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			$dbtable_sum = $report_sum."oktober_".$year;
			break;
			case "November":
            $dbtable = $report."november_".$year;
			$dbtable_sum = $report_sum."november_".$year;
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			$dbtable_sum = $report_sum."desember_".$year;
			break;
		}
		
		switch ($m2)
		{
			case "January":
            $dbtable2 = $report."januari_".$year;
			$dbtable2_sum = $report_sum."januari_".$year;
			break;
			case "February":
            $dbtable2 = $report."februari_".$year;
			$dbtable2_sum = $report_sum."februari_".$year;
			break;
			case "March":
            $dbtable2 = $report."maret_".$year;
			$dbtable2_sum = $report_sum."maret_".$year;
			break;
			case "April":
            $dbtable2 = $report."april_".$year;
			$dbtable2_sum = $report_sum."april_".$year;
			break;
			case "May":
            $dbtable2 = $report."mei_".$year;
			$dbtable2_sum = $report_sum."mei_".$year;
			break;
			case "June":
            $dbtable2 = $report."juni_".$year;
			$dbtable2_sum = $report_sum."juni_".$year;
			break;
			case "July":
            $dbtable2 = $report."juli_".$year;
			$dbtable2_sum = $report_sum."juli_".$year;
			break;
			case "August":
            $dbtable2 = $report."agustus_".$year;
			$dbtable2_sum = $report_sum."agustus_".$year;
			break;
			case "September":
            $dbtable2 = $report."september_".$year;
			$dbtable2_sum = $report_sum."september_".$year;
			break;
			case "October":
            $dbtable2 = $report."oktober_".$year;
			$dbtable2_sum = $report_sum."oktober_".$year;
			break;
			case "November":
            $dbtable2 = $report."november_".$year;
			$dbtable2_sum = $report_sum."november_".$year;
			break;
			case "December":
            $dbtable2 = $report."desember_".$year;
			$dbtable2_sum = $report_sum."desember_".$year;
			break;
		}
		
		//get vehicle
		$this->db->select("vehicle_device");
		$this->db->from("vehicle");
		if ($vehicle != 0)
		{
			$this->db->where("vehicle_device",$vehicle);
		}
		else
		{
			$this->db->where("vehicle_user_id",$this->sess->user_id);
			$this->db->or_where("vehicle_company", $this->sess->user_company);
		}
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		$qv = $this->db->get();
		
		if ($qv->num_rows == 0)
		{
			redirect(base_url());
		}
		$rv = $qv->result();
		//end get vehicle
	
		//get data operasional
		for($i=0;$i<count($rv);$i++)
		{
			$this->dbtrip = $this->load->database("operational_report",true);
			$this->dbtrip->order_by("trip_mileage_vehicle_id","asc");
			$this->dbtrip->order_by("trip_mileage_start_time","asc");
			$this->dbtrip->where("trip_mileage_vehicle_id", $rv[$i]->vehicle_device);
			$this->dbtrip->where("trip_mileage_start_time >=",$sdate);
			$this->dbtrip->where("trip_mileage_end_time <=", $edate);
			if($engine != ""){
				$this->dbtrip->where("trip_mileage_engine", $engine);
			}
			$q = $this->dbtrip->get($dbtable);
		
			if ($q->num_rows>0)
			{
				//$rows = array_merge($rows, $q->result());
				$rows = $q->result();
			}
			
			//detail to //summary
			if($view_map > 0){
				$this->dbtrip->order_by("summary_gps_time","asc");
				$this->dbtrip->where("summary_vehicle_device", $rv[$i]->vehicle_device);
				$this->dbtrip->where("summary_gps_time >=",$sdate);
				$this->dbtrip->where("summary_gps_time <=",$edate);
				$q_detail = $this->dbtrip->get($dbtable_sum);
			
				if ($q_detail->num_rows>0)
				{
					//$rows = array_merge($rows, $q->result());
					$rows_detail = $q_detail->result();
					$params['rows_detail'] = $rows_detail;
					
					
					
				}
			}
			
		}

		//totaldur
		//total cumm km 
		$totalcumm = 0;
		$totalcumm_on = 0;
		$totalcumm_off = 0;
		$totaldur = 0;
		if($m1 != $m2)
		{
			for($i=0; $i < count($rowsall); $i++)
			{	
				if($rowsall[$i]->trip_mileage_engine == 1 ){
					$totalcumm_on += $rowsall[$i]->trip_mileage_trip_mileage;
				}
				if($rowsall[$i]->trip_mileage_engine == 0 ){
					$totalcumm_off += $rowsall[$i]->trip_mileage_trip_mileage;
				}
				
				$totalcumm += $rowsall[$i]->trip_mileage_trip_mileage;
				$totaldur += $rowsall[$i]->trip_mileage_duration_sec;
				
			}
		}
		else
		{
			for($i=0; $i < count($rows); $i++)
			{	
				if($rows[$i]->trip_mileage_engine == 1 ){
					$totalcumm_on += $rows[$i]->trip_mileage_trip_mileage;
					$totaldur += $rows[$i]->trip_mileage_duration_sec;	
				}
				if($rows[$i]->trip_mileage_engine == 0 ){
					$totalcumm_off += $rows[$i]->trip_mileage_trip_mileage;
				}
				
				$totalcumm += $rows[$i]->trip_mileage_trip_mileage;
						
			}
		
		}
		
		$totalcummulative = $totalcumm;
		$totalcummulative_on = $totalcumm_on;
		$totalcummulative_off = $totalcumm_off;
		$totalduration = $totaldur;
	
		$params['view_map'] = $view_map;
		
		if($m1 != $m2)
		{
			$params['data'] = $rowsall;
			
		}
		else
		{
			$params['data'] = $rows;
			
		}
		
		//print_r($rows_summary);exit();
		
		$params['vehicle'] = $rv;
		$params['dbtable'] = $dbtable;
		$params['dbtable_sum'] = $dbtable_sum;
		
		$params['totalduration'] = $totalduration;
		$params['totalcummulative'] = $totalcummulative;
		$params['totalcummulative_on'] = $totalcummulative_on;
		$params['totalcummulative_off'] = $totalcummulative_off;
		
		/*$params['uniqid'] = isset($uniqid) ? $uniqid : "";
		$params["initmap"] = $this->load->view('initmap', $params, true);
		*/
		
		if($view_map > 0){
			$html = $this->load->view("transporter/report/result_summary", $params, true);
		}else{
			$html = $this->load->view("transporter/report/result_operational", $params, true);
		}
		
		$callback['error'] = false;
		$callback['html'] = $html;
		echo json_encode($callback);
		//return;
		
	}
	
	function gotoanimation()
	{
		$rows = 0;
		exit (json_encode(array("data"=>$rows)));	
		return;
	}
	
	
	function map()
	{
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$id = $this->uri->segment(3);
		//$dbtable = $this->uri->segment(4);
		
			$this->dbtrip = $this->load->database("operational_report",true);
			$this->dbtrip->order_by("trip_mileage_id","asc");
			$this->dbtrip->where("trip_mileage_id", $id);
			$q = $this->dbtrip->get("operasional_mei_2019");
			$row = $q->row();
			
			if(count($row)>0){
				$data = json_decode($row->trip_mileage_coordinate_list);
				$gps = $data;
				$total_data  = count($gps);
				
				/*foreach ($data as $v=>$key)
				{
					$gps[$i]->gps_latitude_real = $key["Lat"];
					$gps[$i]->gps_longitude_real = $key["Lng"];
					$map_params[] = array($gps[$i]->gps_longitude_real, $gps[$i]->gps_latitude_real);
					$i++;
				}*/
				
				//get data operasional
				for($i=0;$i<$total_data;$i++)
				{
					//$gps[$i]->gps_latitude_real = $key["Lat"];
					//$gps[$i]->gps_longitude_real = $key["Lng"];
					$map_params[] = array($gps[$i]->gps_longitude_real, $gps[$i]->gps_latitude_real);
					//$i++;
				}
				
				$uniqid = md5( uniqid() );
				
				$uniqid = md5( uniqid() );
				$this->db = $this->load->database("default", TRUE);
				unset($insert);
							
				$insert['log_created'] = date("Y-m-d H:i:s");
				$insert['log_creator'] = $this->sess->user_id;
				$insert['log_type'] = 'mapparams'.$uniqid;
				$insert['log_ip'] = "";			
				$insert['log_data'] = json_encode($map_params);
				$insert['log_version'] = "desktop";
				$insert['log_target'] = "";
				$this->db->insert("log", $insert);
				
				$this->params["initmap"] = $this->load->view('initmap', $this->params, true);
				$this->params['uniqid'] = isset($uniqid) ? $uniqid : "";
				$this->params['data'] = $gps;
				$this->params['row'] = $row;
				$this->params['content'] = $this->load->view("transporter/report/result_operational_map", $this->params, true);
				$this->load->view("templatesess", $this->params);
			}
			
	}
	
	function getDistanceBetween($latitude1, $longitude1, $latitude2, $longitude2, $unit = 'Mi') 
	{ 
		$theta = $longitude1 - $longitude2; 
		$distance = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2)))  + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta))); 
		$distance = acos($distance); 
		$distance = rad2deg($distance); 
		$distance = $distance * 60 * 1.1515; 
		switch($unit) 
		{ 
			case 'Mi': break; 
			case 'Km' : $distance = $distance * 1.609344; 
		} 
		return (round($distance,2)); 
	}
	
	function get_company_bycustom(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("company_name","asc");
		$this->dbtransporter->where("company_flag", 0);
		$qd = $this->dbtransporter->get("droppoint_company_custom");
		$rd = $qd->result();
		
		return $rd;
	}
	
	function get_company_byarea($id){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("company_name","asc");
		$this->dbtransporter->where("company_id", $id);
		$this->dbtransporter->where("company_flag", 0);
		$qd = $this->dbtransporter->get("droppoint_company_custom");
		$rd = $qd->result();
		
		return $rd;
	}
	function get_supercompany_byarea($id){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("company_name","asc");
		$this->dbtransporter->where("company_super_company", $id);
		$this->dbtransporter->where("company_flag", 0);
		$qd = $this->dbtransporter->get("droppoint_company_custom");
		$rd = $qd->result();
		
		return $rd;
	}
	
	function get_plant_byadmin(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("plant_name","asc");
		$this->dbtransporter->select("plant_id,plant_name,plant_code");
		$this->dbtransporter->where("plant_flag", 0);
		$this->dbtransporter->where("plant_company", 24); //khusus company balrich
		$qd = $this->dbtransporter->get("droppoint_plant");
		$rd = $qd->result();
		
		return $rd;
	}
	
	function get_plant_bycompany($id){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("plant_name","asc");
		$this->dbtransporter->select("plant_id,plant_name,plant_code");
		$this->dbtransporter->where("company_id", $id);
		$this->dbtransporter->join("droppoint_plant", "company_plant = plant_id", "left");
		$qd = $this->dbtransporter->get("droppoint_company_custom");
		$rd = $qd->result();
		return $rd;
	}
	function get_plant_bysupercompany($id){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("plant_name","asc");
		$this->dbtransporter->select("plant_id,plant_name,plant_code");
		$this->dbtransporter->where("plant_super_company", $id);
		$qd = $this->dbtransporter->get("droppoint_plant");
		$rd = $qd->result();
		return $rd;
	}
	function get_vehicle_by_company($id){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		
		$this->db->order_by("vehicle_no","asc");
		$this->db->select("vehicle_id,vehicle_device,vehicle_name,vehicle_no,company_name");
		$this->db->where("vehicle_company", $id);
		if($this->sess->user_group > 0){
			$this->db->where("vehicle_group", $this->sess->user_group);
		}
		$this->db->where("vehicle_status <>",3);
		$this->db->join("company", "vehicle_company = company_id", "left");
		$qd = $this->db->get("vehicle");
		$rd = $qd->result();
		
		if($qd->num_rows() > 0){
			$options = "<option value='0' selected='selected'>--Select Vehicle--</option>";
			foreach($rd as $obj){
				$options .= "<option value='". $obj->vehicle_device . "'>". $obj->vehicle_no ." - ".$obj->vehicle_name." "."(".$obj->company_name.")"."</option>";
			}
			
			echo $options;
			return;
		}
	}
	
	function get_company_by_plant($plant){
		
		$byadmin = 0;
		$idcompany = 0;
		$super = 0;
		//balrich dan sariroti pusat
		if($this->sess->user_id == "1032" || $this->sess->user_id == "2974"){
			$byadmin = 1;
		}else{
			//rembang
			if($this->sess->user_company == "1508" || $this->sess->user_company == "1507"){
				$idcompany = 1508;
			}
			//cikarang
			if($this->sess->user_company == "65" || $this->sess->user_company == "612"){
				$idcompany = 65;
			}
			//cikande
			if($this->sess->user_company == "397" || $this->sess->user_company == "613"){
				$idcompany = 397;
			}
			//palembang
			if($this->sess->user_company == "258" || $this->sess->user_company == "614"){
				$idcompany = 258;
			}
			//lampung
			if($this->sess->user_company == "66" || $this->sess->user_company == "615"){
				$idcompany = 66;
			}
			//pasuruan
			if($this->sess->user_company == "98" || $this->sess->user_company == "616"){
				$idcompany = 98;
			}
			//medan
			if($this->sess->user_company == "556" || $this->sess->user_company == "620"){
				$idcompany = 556;
			}
			//makassar
			if($this->sess->user_company == "398" || $this->sess->user_company == "621"){
				$idcompany = 398;
			}
			//cileungsi
			if($this->sess->user_company == "68" || $this->sess->user_company == "622"){
				$idcompany = 68;
			}
			//parung
			if($this->sess->user_company == "432" || $this->sess->user_company == "623"){
				$idcompany = 432;
			}
			//keradenan
			if($this->sess->user_company == "433" || $this->sess->user_company == "624"){
				$idcompany = 433;
			}
			//semarang
			if($this->sess->user_company == "470" || $this->sess->user_company == "625"){
				$idcompany = 470;
			}
			//purwakarta
			if($this->sess->user_company == "630" || $this->sess->user_company == "638"){
				$idcompany = 630;
			}
			//balaraja
			if($this->sess->user_company == "656" || $this->sess->user_company == "657"){
				$idcompany = 656;
			}
			//cianjur
			if($this->sess->user_company == "680" || $this->sess->user_company == "682"){
				$idcompany = 680;
			}
			//cirebon
			if($this->sess->user_company == "681" || $this->sess->user_company == "638"){
				$idcompany = 681;
			}
			
			//cikarang (plant) - sariroti
			if($this->sess->user_company == "1439"){
				$idcompany = 1439;
				$super = 1;
			}
			//cibitung (plant) - sariroti
			if($this->sess->user_company == "1441"){
				$idcompany = 1441;
				$super = 1;
			}
			//cikande (plant) - sariroti
			if($this->sess->user_company == "1442"){
				$idcompany = 1442;
				$super = 1;
			}
			//purwakarta (plant) - sariroti
			if($this->sess->user_company == "1443"){
				$idcompany = 1443;
				$super = 1;
			}
			//semarang (plant) - sariroti
			if($this->sess->user_company == "1445"){
				$idcompany = 1445;
				$super = 1;
			}
			//pasuruan (plant) - sariroti
			if($this->sess->user_company == "1448"){
				$idcompany = 1448;
				$super = 1;
			}
			//palembang (plant) - sariroti
			if($this->sess->user_company == "1444"){
				$idcompany = 1444;
				$super = 1;
			}
			//medan (plant) - sariroti
			if($this->sess->user_company == "1447"){
				$idcompany = 1447;
				$super = 1;
			}
			//makassar (plant) - sariroti
			if($this->sess->user_company == "1446"){
				$idcompany = 1446;
				$super = 1;
			}
			//kalimantan + plant
			if($this->sess->user_company == "1644" || $this->sess->user_company == "1647"){
				$idcompany = 1644; //khusus kendaran kalimantan hanya ada di pool kalimantan
				$super = 1;
			}
		}
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("company_name","asc");
		$this->dbtransporter->where("company_flag", 0);
		if($byadmin == "1"){
			$this->dbtransporter->where("company_plant", $plant);
		}else{
			if($super == "1"){
				$this->dbtransporter->where("company_super_company", $idcompany);
			}else{
				$this->dbtransporter->where("company_id", $idcompany);
			}
		}
		$qd = $this->dbtransporter->get("droppoint_company_custom");
		$rd = $qd->result();
			
		if($qd->num_rows() > 0){
			$options = "<option value='' selected='selected'>--Select Pool--</option>";
			foreach($rd as $obj){
				$options .= "<option value='". $obj->company_id . "'>". $obj->company_name ."</option>";
			}
			
			echo $options;
			return;
		}
	}
}