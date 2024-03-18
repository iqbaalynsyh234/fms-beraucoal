<?php
include "base.php";

class Report_ota extends Base {

	function __construct()
	{
		parent::Base();	
		$this->load->helper('common_helper');
	}
	
	function index(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		
		$bulanini = date('m');
		$tahunini = date('Y');
		//Get company
		//balrich dan sariroti pusat
		if($this->sess->user_id == "1032" || $this->sess->user_id == "2974"){
			$row_company = $this->get_company_bycustom();
			$row_plant = $this->get_plant_byadmin();
		}else{
			//cikarang
			if($this->sess->user_company == "65" || $this->sess->user_company == "612"){
				$row_company = $this->get_company_byarea(65);
				$row_plant = $this->get_plant_bycompany(65);
			}
			//cikande
			if($this->sess->user_company == "397" || $this->sess->user_company == "613"){
				$row_company = $this->get_company_byarea(397);
				$row_plant = $this->get_plant_bycompany(397);
			}
			//palembang
			if($this->sess->user_company == "258" || $this->sess->user_company == "614"){
				$row_company = $this->get_company_byarea(258);
				$row_plant = $this->get_plant_bycompany(258);
			}
			//lampung
			if($this->sess->user_company == "66" || $this->sess->user_company == "615"){
				$row_company = $this->get_company_byarea(66);
				$row_plant = $this->get_plant_bycompany(66);
			}
			//pasuruan
			if($this->sess->user_company == "98" || $this->sess->user_company == "616"){
				$row_company = $this->get_company_byarea(98);
				$row_plant = $this->get_plant_bycompany(98);
			}
			//medan
			if($this->sess->user_company == "556" || $this->sess->user_company == "620"){
				$row_company = $this->get_company_byarea(556);
				$row_plant = $this->get_plant_bycompany(556);
			}
			//makassar
			if($this->sess->user_company == "398" || $this->sess->user_company == "621"){
				$row_company = $this->get_company_byarea(398);
				$row_plant = $this->get_plant_bycompany(398);
			}
			//cileungsi
			if($this->sess->user_company == "68" || $this->sess->user_company == "622"){
				$row_company = $this->get_company_byarea(68);
				$row_plant = $this->get_plant_bycompany(68);
			}
			//parung
			if($this->sess->user_company == "432" || $this->sess->user_company == "623"){
				$row_company = $this->get_company_byarea(432);
				$row_plant = $this->get_plant_bycompany(432);
			}
			//keradenan
			if($this->sess->user_company == "433" || $this->sess->user_company == "624"){
				$row_company = $this->get_company_byarea(433);
				$row_plant = $this->get_plant_bycompany(433);
			}
			//semarang
			if($this->sess->user_company == "470" || $this->sess->user_company == "625"){
				$row_company = $this->get_company_byarea(470);
				$row_plant = $this->get_plant_bycompany(470);
			}
			//purwakarta
			if($this->sess->user_company == "630" || $this->sess->user_company == "638"){
				$row_company = $this->get_company_byarea(630);
				$row_plant = $this->get_plant_bycompany(630);
			}
			//balaraja
			if($this->sess->user_company == "656" || $this->sess->user_company == "657"){
				$row_company = $this->get_company_byarea(656);
				$row_plant = $this->get_plant_bycompany(656);
			}
			//cianjur
			if($this->sess->user_company == "680" || $this->sess->user_company == "682"){
				$row_company = $this->get_company_byarea(680);
				$row_plant = $this->get_plant_bycompany(680);
			}
			
			//cirebon
			if($this->sess->user_company == "681" || $this->sess->user_company == "638"){
				$row_company = $this->get_company_byarea(681);
				$row_plant = $this->get_plant_bycompany(681);
			}
			
			//cikarang (plant) - sariroti
			if($this->sess->user_company == "1439"){
				$row_company = $this->get_supercompany_byarea(1439);
				$row_plant = $this->get_plant_bysupercompany(1439);
			}
			//cibitung (plant) - sariroti
			if($this->sess->user_company == "1441"){
				$row_company = $this->get_supercompany_byarea(1441);
				$row_plant = $this->get_plant_bysupercompany(1441);
			}
			//cikande (plant) - sariroti
			if($this->sess->user_company == "1442"){
				$row_company = $this->get_supercompany_byarea(1442);
				$row_plant = $this->get_plant_bysupercompany(1442);
			}
			//purwakarta (plant) - sariroti
			if($this->sess->user_company == "1443"){
				$row_company = $this->get_supercompany_byarea(1443);
				$row_plant = $this->get_plant_bysupercompany(1443);
			}
			//semarang (plant) - sariroti
			if($this->sess->user_company == "1445"){
				$row_company = $this->get_supercompany_byarea(1445);
				$row_plant = $this->get_plant_bysupercompany(1445);
			}
			//pasuruan (plant) - sariroti
			if($this->sess->user_company == "1448"){
				$row_company = $this->get_supercompany_byarea(1448);
				$row_plant = $this->get_plant_bysupercompany(1448);
			}
			//palembang (plant) - sariroti
			if($this->sess->user_company == "1444"){
				$row_company = $this->get_supercompany_byarea(1444);
				$row_plant = $this->get_plant_bysupercompany(1444);
			}
			//medan (plant) - sariroti
			if($this->sess->user_company == "1447"){
				$row_company = $this->get_supercompany_byarea(1447);
				$row_plant = $this->get_plant_bysupercompany(1447);
			}
			//makassar (plant) - sariroti
			if($this->sess->user_company == "1446"){
				$row_company = $this->get_supercompany_byarea(1446);
				$row_plant = $this->get_plant_bysupercompany(1446);
			}
			
		}
		//$this->params['rcompany'] = $row_company;
		$this->params['rplant'] = $row_plant;
		
		//Get date
		$row_month = $this->get_month();
		$this->params["month"] = $row_month;
		
		//Get year
		$row_year = $this->get_year();
		$this->params["year"] = $row_year;
		
		//Get this month
		$row_this_month = $this->get_this_month($bulanini);
		$this->params["thismonth"] = $row_this_month;
		
		//get this year
		$row_this_year = $this->get_this_year($tahunini);
		$this->params["thisyear"] = $row_this_year;
		
		$this->params["content"] = $this->load->view('transporter/report/mn_ota', $this->params, true);		
		$this->load->view("templatesess", $this->params);
	}
	
	function search(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
        if (!isset($this->sess->user_id)){redirect(base_url());}
		$plant = isset($_POST["plant"]) ? $_POST["plant"] : "";
		$company = isset($_POST["company"]) ? $_POST["company"] : "";
		$parent = isset($_POST["parent"]) ? $_POST["parent"] : "";
		$distrep = isset($_POST["distrep"]) ? $_POST["distrep"] : "";
		
		$sdate = isset($_POST["sdate"]) ? $_POST["sdate"] : "";
		$edate = isset($_POST["edate"]) ? $_POST["edate"] : "";
		$startdate = isset($_POST["startdate"]) ? $_POST["startdate"] : "";
		$enddate = isset($_POST["enddate"]) ? $_POST["enddate"] : "";
		$bank = isset($_POST["bank"]) ? $_POST["bank"] : "";
		$month = isset($_POST["month"]) ? $_POST["month"] : "";
		$thismonth = isset($_POST["thismonth"]) ? $_POST["thismonth"] : "";
		$year = isset($_POST["year"]) ? $_POST["year"] : "";
		$thisyear = isset($_POST["thisyear"]) ? $_POST["thisyear"] : "";
		$date_option = isset($_POST["date_option"]) ? $_POST["date_option"] : "";
		$checkdetail = isset($_POST["checkdetail"]) ? $_POST["checkdetail"] : 0;
		$type_id = 0;
		$type_name = "";
		
		$month = 0;
		$year = 0;
		
		
		$dbtable = "_";
		$report = "inout_geofence_";
		$m1 = date("F", strtotime($startdate)); 
		$month = date("m", strtotime($startdate)); 
		$year = date("Y", strtotime($startdate));
		
		$month_startdate = date("m", strtotime($startdate));
		$month_enddate = date("m", strtotime($enddate));
		
		$year_startdate = date("Y", strtotime($startdate));
		$year_enddate = date("Y", strtotime($enddate));
		
		switch ($m1)
		{
			case "January":
            $dbtable = $report."januari_".$year;
			$month_name = "JANUARI";
			break;
			case "February":
            $dbtable = $report."februari_".$year;
			$month_name = "FEBRUARI";
			break;
			case "March":
            $dbtable = $report."maret_".$year;
			$month_name = "MARET";
			break;
			case "April":
            $dbtable = $report."april_".$year;
			$month_name = "APRIL";
			break;
			case "May":
            $dbtable = $report."mei_".$year;
			$month_name = "MEI";
			break;
			case "June":
            $dbtable = $report."juni_".$year;
			$month_name = "JUNI";
			break;
			case "July":
            $dbtable = $report."juli_".$year;
			$month_name = "JULI";
			break;
			case "August":
            $dbtable = $report."agustus_".$year;
			$month_name = "AGUSTUS";
			break;
			case "September":
            $dbtable = $report."september_".$year;
			$month_name = "SEPTEMBER";
			break;
			case "October":
            $dbtable = $report."oktober_".$year;
			$month_name = "OKTOBER";
			break;
			case "November":
            $dbtable = $report."november_".$year;
			$month_name = "NOVEMBER";
			break;
			case "December":
            $dbtable = $report."desember_".$year;
			$month_name = "DESEMBER";
			break;
		}
		$this->params["dbtable"] = $dbtable;
		
		$sdate = date("Y-m-d", strtotime($startdate));
		$edate = date("Y-m-d", strtotime($enddate));
		$this->params["sdate"] = $sdate;
		$this->params["edate"] = $edate;
		$this->params["month"] = $month;
		$this->params["year"] = $year;
		
		if($plant == "")
		{
			$callback['error'] = true;
            $callback['message'] = "Please Select Plant !";
            echo json_encode($callback);
            return;
		}
		
		if($company == "")
		{
			$callback['error'] = true;
            $callback['message'] = "Please Select Pool !";
            echo json_encode($callback);
            return;
		}
		
		if($parent == "")
		{
			$callback['error'] = true;
            $callback['message'] = "Please Select Group !";
            echo json_encode($callback);
            return;
		}
		
		if($distrep == "")
		{
			$callback['error'] = true;
            $callback['message'] = "Please Select Distrep !";
            echo json_encode($callback);
            return;
		}
		
		if($sdate == "")
		{
			$callback['error'] = true;
            $callback['message'] = "Please Select Periode !";
            echo json_encode($callback);
            return;
		}
		
		if($enddate < $startdate)
		{
			$callback['error'] = true;
            $callback['message'] = "Invalid End Date !";
            echo json_encode($callback);
            return;
		}
		
		if($year_startdate != $year_enddate)
		{
			$callback['error'] = true;
            $callback['message'] = " Periode hanya dapat dipilih dalam bulan dan tahun yang sama !";
            echo json_encode($callback);
            return;
		}
		
		if($month_startdate != $month_enddate)
		{
			$callback['error'] = true;
            $callback['message'] = " Periode hanya dapat dipilih dalam bulan dan tahun yang sama !";
            echo json_encode($callback);
            return;
		}
		
		//get company name
		$row_company_name = $this->get_company_name($company);
		$this->params["company_name"] = $row_company_name;
		
		//get parent name 
		$row_parent_name = $this->get_parent_name($parent);
		$this->params["parent_name"] = $row_parent_name;
		
		//get distrep name 
		$row_distrep_name = $this->get_distrep_name($distrep);
		$this->params["distrep_name"] = $row_distrep_name;
		
		if(isset($row_distrep_name)){
			$type_id = $row_distrep_name->distrep_type;
			if($type_id == 1){
				$type_name = "COMBINE";
			}else{
				$type_name = "REGULAR";
			}
			
		}
		$this->params["type_id"] = $type_id;
		$this->params["type_name"] = $type_name;
		
		//get droppoint
		$row_droppoint = $this->get_droppoint_bydistrep($distrep,$month,$year);
		$this->params["droppoint"] = $row_droppoint;
		
		// get data monthly report
		$row_monthly_report = $this->get_monthly_report($sdate,$edate);
		$this->params["data"] = $row_monthly_report;
		
		//get plant
		$row_plant = $this->get_plant_byid($plant);
		$this->params["plant"] = $row_plant;
		
		$this->params["checkdetail"] = $checkdetail;
		$this->params["month_name"] = $month_name;
		
		$html = $this->load->view('transporter/report/list_result_ota', $this->params, true);
        
        $callback['html'] = $html;
       
		echo json_encode($callback);
	}
	
	function get_company_by_plant($plant){
		
		$byadmin = 0;
		$idcompany = 0;
		$super = 0;
		
		//balrich dan sariroti pusat
		if($this->sess->user_id == "1032" || $this->sess->user_id == "2974"){
			$byadmin = 1;
		}else{
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
	
	function get_parent_by_company($company){
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("parent_name","asc");
		$this->dbtransporter->where("parent_flag", 0);
		$this->dbtransporter->where("parent_company", $company);
		$qd = $this->dbtransporter->get("droppoint_parent");
		$rd = $qd->result();
			
		if($qd->num_rows() > 0){
			$options = "<option value='' selected='selected'>--Select Group--</option>";
			foreach($rd as $obj){
				$options .= "<option value='". $obj->parent_id . "'>". $obj->parent_name ."</option>";
			}
			
			echo $options;
			return;
		}
	}
	
	function get_distrep_by_parent($parent){
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("distrep_name","asc");
		$this->dbtransporter->where("distrep_flag", 0);
		$this->dbtransporter->where("distrep_parent", $parent);
		$qd = $this->dbtransporter->get("droppoint_distrep");
		$rd = $qd->result();
			
		if($qd->num_rows() > 0){
			$options = "<option value='' selected='selected'>--Select Distrep--</option>";
			foreach($rd as $obj){
				$options .= "<option value='". $obj->distrep_id . "'>". $obj->distrep_name ."</option>";
			}
			
			echo $options;
			return;
		}
	}
	
	function get_month(){
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("month_value","asc");
		$this->dbtransporter->where("month_status", 1);
		$qd = $this->dbtransporter->get("config_month");
		$rd = $qd->result();
		return $rd;
	}
	
	function get_year(){
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("year_value","asc");
		$this->dbtransporter->where("year_status", 1);
		$qd = $this->dbtransporter->get("config_year");
		$rd = $qd->result();
		return $rd;
	}
	
	function get_this_year($tahunini){
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("year_value","asc");
		$this->dbtransporter->where("year_status", 1);
		$this->dbtransporter->where("year_value", $tahunini);
		$qd = $this->dbtransporter->get("config_year");
		$rd = $qd->result();
		return $rd;
	}
	
	function get_this_month($bulanini){
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("month_value","asc");
		$this->dbtransporter->where("month_status", 1);
		$this->dbtransporter->where("month_value", $bulanini);
		$qd = $this->dbtransporter->get("config_month");
		$rd = $qd->result();
		return $rd;
	}
	
	function get_company_name($company){
		
		$this->db->order_by("company_name","asc");
		$this->db->select("company_id,company_name");
		$this->db->where("company_id", $company);
		$qd = $this->db->get("company");
		$rd = $qd->row();
		
		return $rd;
	}
	
	function get_parent_name($parent){
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->select("parent_id,parent_name,parent_code");
		$this->dbtransporter->where("parent_id", $parent);
		$qd = $this->dbtransporter->get("droppoint_parent");
		$rd = $qd->row();
		
		return $rd;
	}
	
	function get_distrep_name($distrep){
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->select("distrep_id,distrep_name,distrep_code,distrep_type,distrep_report_status,distrep_vehicle_no");
		$this->dbtransporter->where("distrep_id", $distrep);
		$qd = $this->dbtransporter->get("droppoint_distrep");
		$rd = $qd->row();
		
		return $rd;
	}
	
	function get_monthly_report($sdate,$edate){
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("monthly_date","asc");
		$this->dbtransporter->where("monthly_date >=", $sdate);
		$this->dbtransporter->where("monthly_date <=", $edate);
		$qd = $this->dbtransporter->get("config_monthly_report");
		$rd = $qd->result();
		
		return $rd;
	}
	
	function get_parent_bycreator(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("parent_name","asc");
		$this->dbtransporter->where("parent_flag", 0);
		$this->dbtransporter->where("parent_creator", $this->sess->user_id);
		$qd = $this->dbtransporter->get("droppoint_parent");
		$rd = $qd->result();
		return $rd;
	}
	
	function get_distrep_bycreator(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("distrep_name","asc");
		$this->dbtransporter->where("distrep_flag", 0);
		$qd = $this->dbtransporter->get("droppoint_distrep");
		$rd = $qd->result();
		return $rd;
	}
	
	function get_geofence_bylogin(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		
		$this->db->select("geofence_id,geofence_user,geofence_name,geofence_type");
		$this->db->order_by("geofence_name","asc");
		$this->db->group_by("geofence_name");
		$this->db->where("geofence_status", 1);
		$this->db->where("geofence_user", $this->sess->user_id);
		$qd = $this->db->get("geofence");
		$rd = $qd->result();
		return $rd;
	}
	
	function get_company_bylogin(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$this->db->order_by("company_name","asc");
		$this->db->where("company_flag", 0);
		$this->db->where("company_created_by", 1032); //hanya balrich
		$qd = $this->db->get("company");
		$rd = $qd->result();
		
		return $rd;
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
	
	function get_droppoint_bydistrep($distrep,$month,$year){
		
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		
		$this->dbtransporter->select("droppoint_id,droppoint_geofence,droppoint_name,target_time,droppoint_code");
		$this->dbtransporter->order_by("target_time","asc");
		$this->dbtransporter->group_by("droppoint_code");
		$this->dbtransporter->where("droppoint_flag",0);
		$this->dbtransporter->where("droppoint_distrep", $distrep);
		$this->dbtransporter->where("target_month", $month);
		$this->dbtransporter->where("target_year", $year);
		$this->dbtransporter->join("droppoint_target", "target_droppoint = droppoint_id", "left");
		$qd = $this->dbtransporter->get("droppoint");
		$rd = $qd->result();
		
		return $rd;
	}
	
	function get_target($sdate,$edate){
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("target_startdate","asc");
		$this->dbtransporter->where("target_startdate >=", $sdate);
		$this->dbtransporter->where("target_startdate <=", $edate);
		$qd = $this->dbtransporter->get("droppoint_target");
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
	
	function get_plant_byid($id){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$this->dbtransporter = $this->load->database("transporter", TRUE);
		$this->dbtransporter->order_by("plant_name","asc");
		$this->dbtransporter->select("plant_id,plant_name,plant_code");
		$this->dbtransporter->where("plant_id", $id);
		$this->dbtransporter->where("plant_flag", 0);
		$qd = $this->dbtransporter->get("droppoint_plant");
		$rd = $qd->row();
		
		return $rd;
	}
}
	
	