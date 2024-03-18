<?php
include "base.php";

class Ritasechart extends Base {
	var $otherdb;

	function Ritasechart()
	{
		parent::Base();
		$this->load->model("gpsmodel");
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
		$this->load->helper('common_helper');
		$this->load->model("dashboardmodel");
	}

	function index()
	{
		//redirect(base_url());
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		/*$user_id = $this->sess->user_id;
		$user_level      = $this->sess->user_level;
		$user_company    = $this->sess->user_company;
		$user_subcompany = $this->sess->user_subcompany;
		$user_group      = $this->sess->user_group;
		$user_subgroup   = $this->sess->user_subgroup;
		$user_dblive 	  = $this->sess->user_dblive;
		$user_id_fix     = $user_id;
		
		$this->db->select("vehicle.*, user_name");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);
	
		if($user_level == 1){
			$this->db->where("vehicle_user_id", $user_id_fix);
		}else if($user_level == 2){
			$this->db->where("vehicle_company", $user_company);
		}else if($user_level == 3){
			$this->db->where("vehicle_subcompany", $user_subcompany);
		}else if($user_level == 4){
			$this->db->where("vehicle_group", $user_group);
		}else if($user_level == 5){
			$this->db->where("vehicle_subgroup", $user_subgroup);
		}else{
			$this->db->where("vehicle_no",99999);
		}

		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}

		$rows = $q->result();
		$rows_company = $this->get_company_bylevel();
		//$rows_geofence = $this->get_geofence_bydblive($user_dblive);//print_r($rows_geofence);exit();

		$this->params["vehicles"] = $rows;
		$this->params["rcompany"] = $rows_company;
		//$this->params["rgeofence"] = $rows_geofence;*/
		$this->params['code_view_menu'] = "report";


		$this->params["header"] = $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"] = $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["content"] = $this->load->view('dashboard/report/vritase_chart_report', $this->params, true);
		$this->load->view("dashboard/template_dashboard_report", $this->params);
	}

	function search()
	{
		ini_set('display_errors', 1);
		ini_set('memory_limit', '2G');
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$type = $this->input->post("type");
		$startdate = $this->input->post("startdate");
		$enddate = $this->input->post("enddate");
		$model = $this->input->post("model");
		$periode = $this->input->post("periode");
		$sublevel = $this->input->post("sublevel");
		$shour = "00:00:00";
		$ehour = "23:59:59";
		
		$nowdate = date("Y-m-d");
		$nowday = date("d");
		$nowmonth = date("m");
		$nowyear = date("Y");
		$lastday = date("t");
		
		$report = "ritase_board_";
		$report_sum = "summary_";
		
		if($periode == "custom"){
			$sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
			$edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));
		}else if($periode == "yesterday"){
			
			$sdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
			$edate = date("Y-m-d 23:59:59", strtotime("yesterday"));
			
		}else if($periode == "last7"){
			$nowday = $nowday - 1;
			$firstday = $nowday - 7;
			if($nowday <= 7){
				$firstday = 1;
			}
			
			/*if($firstday > $nowday){
				$firstday = 1;
			}*/
		
			$sdate = date("Y-m-d H:i:s ", strtotime($nowyear."-".$nowmonth."-".$firstday." "."00:00:00"));
			$edate = date("Y-m-d H:i:s", strtotime($nowyear."-".$nowmonth."-".$nowday." "."23:59:59"));
			
		}
		else if($periode == "last30"){
			$firstday = "1";
			$sdate = date("Y-m-d H:i:s ", strtotime($nowyear."-".$nowmonth."-".$firstday." "."00:00:00"));
			$edate = date("Y-m-d H:i:s", strtotime($nowyear."-".$nowmonth."-".$lastday." "."23:59:59"));
		}
		else{
			$sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
			$edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));
		}
		
		//print_r($sdate." ".$edate);exit();
		
		$m1 = date("F", strtotime($sdate));
		$m2 = date("F", strtotime($edate));
		$year = date("Y", strtotime($sdate));
		$year2 = date("Y", strtotime($edate));
		$rows = array();
		$total_q = 0;

		$error = "";
		$rows_summary = "";

		if ($model == "")
		{
			$error .= "- Please Select Data! \n";
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
		
		if($model == "vehicle")
		{
			$limitdata = 5;
			//get data vehicle
			$user_id = $this->sess->user_id;
			$user_level      = $this->sess->user_level;
			$user_company    = $this->sess->user_company;
			$user_subcompany = $this->sess->user_subcompany;
			$user_group      = $this->sess->user_group;
			$user_subgroup   = $this->sess->user_subgroup;
			$user_dblive 	  = $this->sess->user_dblive;
			$user_id_fix     = $user_id;
			
			$this->db->select("vehicle_id,vehicle_no");
			$this->db->order_by("vehicle_no", "asc");
			$this->db->where("vehicle_status <>", 3);
		
			if($user_level == 1){
				$this->db->where("vehicle_user_id", $user_id_fix);
			}else if($user_level == 2){
				$this->db->where("vehicle_company", $user_company);
			}else if($user_level == 3){
				$this->db->where("vehicle_subcompany", $user_subcompany);
			}else if($user_level == 4){
				$this->db->where("vehicle_group", $user_group);
			}else if($user_level == 5){
				$this->db->where("vehicle_subgroup", $user_subgroup);
			}else{
				$this->db->where("vehicle_no",99999);
			}
			$this->db->where("vehicle_device <> ","69969039633231@TK510");//trial
			$qv = $this->db->get("vehicle");
			$vehicle = $qv->result();
			if ($qv->num_rows() == 0)
			{
				$error .= "- No Data Vehicle ! \n";
			}else{
					$feature = array();
					$feature_name = array();
					$feature_value = array();
					if(count($vehicle) > 0)
					{
						for($z=0;$z<count($vehicle);$z++)
						{
							$this->dbtrip = $this->load->database("tensor_report",true);
							$this->dbtrip->select("ritase_board_total");
							$this->dbtrip->order_by("ritase_board_id","asc");
							$this->dbtrip->where("ritase_board_date >=",$sdate);
							$this->dbtrip->where("ritase_board_date <=",$edate);
							$this->dbtrip->where("ritase_board_type",$type);
							$this->dbtrip->where("ritase_board_model",$model);
							$this->dbtrip->where("ritase_board_vehicle_id",$vehicle[$z]->vehicle_id);
							$qdata = $this->dbtrip->get($dbtable);
							$totalritase = 0;
							$maxdata = 0;
							if ($qdata->num_rows>0)
							{
								$rows_data = $qdata->result();
								for($i=0; $i < count($rows_data); $i++)
								{
									$totalritase += $rows_data[$i]->ritase_board_total;
									if($rows_data[$i]->ritase_board_total > $maxdata){
										$maxdata = $rows_data[$i]->ritase_board_total;
									}
									
								}
								
							}
									
							$feature[$z]["value"] = $totalritase;
							$feature[$z]["name"] = $vehicle[$z]->vehicle_no." (".$totalritase.")";
							$feature_name[$z] = $vehicle[$z]->vehicle_no." (".$totalritase.")";
									
						}
						/*
						$content = $feature;
						$content_name = $feature_name;
						*/
						
						$content_ex = $this->dashboardmodel->array_sort($feature, 'value', SORT_DESC);
						$content = array_slice($content_ex, 0, $limitdata);
						$content_name = array();
						for($u=0;$u<count($content);$u++)
						{
							$content_name[] = $content[$u]["name"];
						}
						$params['content'] = $content;
						$params['content_name'] = $content_name;
						$params['limitdata'] = $limitdata;
					}
								
				}
				
				$view = "vritase_vehicle_chart_result";
					
		}
		if($model == "shift")
		{
			$limitdata = 5;
			$shift_1 = "vehicle_shift_1";
			//get data vehicle
			$user_id = $this->sess->user_id;
			$user_level      = $this->sess->user_level;
			$user_company    = $this->sess->user_company;
			$user_subcompany = $this->sess->user_subcompany;
			$user_group      = $this->sess->user_group;
			$user_subgroup   = $this->sess->user_subgroup;
			$user_dblive 	  = $this->sess->user_dblive;
			$user_id_fix     = $user_id;
			
			$this->db->select("vehicle_id,vehicle_no");
			$this->db->order_by("vehicle_no", "asc");
			$this->db->where("vehicle_status <>", 3);
		
			if($user_level == 1){
				$this->db->where("vehicle_user_id", $user_id_fix);
			}else if($user_level == 2){
				$this->db->where("vehicle_company", $user_company);
			}else if($user_level == 3){
				$this->db->where("vehicle_subcompany", $user_subcompany);
			}else if($user_level == 4){
				$this->db->where("vehicle_group", $user_group);
			}else if($user_level == 5){
				$this->db->where("vehicle_subgroup", $user_subgroup);
			}else{
				$this->db->where("vehicle_no",99999);
			}
			$this->db->where("vehicle_device <> ","69969039633231@TK510");//trial
			$qv = $this->db->get("vehicle");
			$vehicle = $qv->result();
			if ($qv->num_rows() == 0)
			{
				$error .= "- No Data Vehicle ! \n";
			}else{
					$feature = array();
					$feature_name = array();
					$feature_value = array();
					if(count($vehicle) > 0)
					{
						for($z=0;$z<count($vehicle);$z++)
						{
							$this->dbtrip = $this->load->database("tensor_report",true);
							$this->dbtrip->select("ritase_board_total");
							$this->dbtrip->order_by("ritase_board_id","asc");
							$this->dbtrip->where("ritase_board_date >=",$sdate);
							$this->dbtrip->where("ritase_board_date <=",$edate);
							$this->dbtrip->where("ritase_board_type",$type);
							$this->dbtrip->where("ritase_board_model",$shift_1);
							$this->dbtrip->where("ritase_board_vehicle_id",$vehicle[$z]->vehicle_id);
							$qdata = $this->dbtrip->get($dbtable);
							$totalritase = 0;
							$maxdata = 0;
							if ($qdata->num_rows>0)
							{
								$rows_data = $qdata->result();
								for($i=0; $i < count($rows_data); $i++)
								{
									$totalritase += $rows_data[$i]->ritase_board_total;
									if($rows_data[$i]->ritase_board_total > $maxdata){
										$maxdata = $rows_data[$i]->ritase_board_total;
									}
									
								}
								
							}
									
							$feature[$z]["value"] = $totalritase;
							$feature[$z]["name"] = $vehicle[$z]->vehicle_no." (".$totalritase.")";
							$feature_name[$z] = $vehicle[$z]->vehicle_no." (".$totalritase.")";
									
						}
						/*
						$content = $feature;
						$content_name = $feature_name;
						*/
						
						$content_ex = $this->dashboardmodel->array_sort($feature, 'value', SORT_DESC);
						$content = array_slice($content_ex, 0, $limitdata);
						$content_name = array();
						for($u=0;$u<count($content);$u++)
						{
							$content_name[] = $content[$u]["name"];
						}
						$params['content_one'] = $content;
						$params['content_name_one'] = $content_name;
						$params['limitdata'] = $limitdata;
					}
					
					
					
								
				}
				
			$shift_2 = "vehicle_shift_2";
			//get data vehicle
			$user_id = $this->sess->user_id;
			$user_level      = $this->sess->user_level;
			$user_company    = $this->sess->user_company;
			$user_subcompany = $this->sess->user_subcompany;
			$user_group      = $this->sess->user_group;
			$user_subgroup   = $this->sess->user_subgroup;
			$user_dblive 	  = $this->sess->user_dblive;
			$user_id_fix     = $user_id;
			
			$this->db->select("vehicle_id,vehicle_no");
			$this->db->order_by("vehicle_no", "asc");
			$this->db->where("vehicle_status <>", 3);
		
			if($user_level == 1){
				$this->db->where("vehicle_user_id", $user_id_fix);
			}else if($user_level == 2){
				$this->db->where("vehicle_company", $user_company);
			}else if($user_level == 3){
				$this->db->where("vehicle_subcompany", $user_subcompany);
			}else if($user_level == 4){
				$this->db->where("vehicle_group", $user_group);
			}else if($user_level == 5){
				$this->db->where("vehicle_subgroup", $user_subgroup);
			}else{
				$this->db->where("vehicle_no",99999);
			}
			$this->db->where("vehicle_device <> ","69969039633231@TK510");//trial
			$qv = $this->db->get("vehicle");
			$vehicle = $qv->result();
			if ($qv->num_rows() == 0)
			{
				$error .= "- No Data Vehicle ! \n";
			}else{
					$feature = array();
					$feature_name = array();
					$feature_value = array();
					if(count($vehicle) > 0)
					{
						for($z=0;$z<count($vehicle);$z++)
						{
							$this->dbtrip = $this->load->database("tensor_report",true);
							$this->dbtrip->select("ritase_board_total");
							$this->dbtrip->order_by("ritase_board_id","asc");
							$this->dbtrip->where("ritase_board_date >=",$sdate);
							$this->dbtrip->where("ritase_board_date <=",$edate);
							$this->dbtrip->where("ritase_board_type",$type);
							$this->dbtrip->where("ritase_board_model",$shift_2);
							$this->dbtrip->where("ritase_board_vehicle_id",$vehicle[$z]->vehicle_id);
							$qdata = $this->dbtrip->get($dbtable);
							$totalritase = 0;
							$maxdata = 0;
							if ($qdata->num_rows>0)
							{
								$rows_data = $qdata->result();
								for($i=0; $i < count($rows_data); $i++)
								{
									$totalritase += $rows_data[$i]->ritase_board_total;
									if($rows_data[$i]->ritase_board_total > $maxdata){
										$maxdata = $rows_data[$i]->ritase_board_total;
									}
									
								}
								
							}
									
							$feature[$z]["value"] = $totalritase;
							$feature[$z]["name"] = $vehicle[$z]->vehicle_no." (".$totalritase.")";
							$feature_name[$z] = $vehicle[$z]->vehicle_no." (".$totalritase.")";
									
						}
						/*
						$content = $feature;
						$content_name = $feature_name;
						*/
						
						$content_ex = $this->dashboardmodel->array_sort($feature, 'value', SORT_DESC);
						$content = array_slice($content_ex, 0, $limitdata);
						$content_name = array();
						for($u=0;$u<count($content);$u++)
						{
							$content_name[] = $content[$u]["name"];
						}
						$params['content_two'] = $content;
						$params['content_name_two'] = $content_name;
						$params['limitdata'] = $limitdata;
					}
								
				}
				
				$view = "vritase_shift_chart_result";
					
		}
		
		else
		{
			exit();
		}
		
		if ($error != "")
		{
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}
		
		
		$params['startdate'] = $sdate;
		$params['enddate'] = $edate;
		
		$html = $this->load->view("dashboard/report/".$view."", $params, true);
		
		$callback['error'] = false;
		$callback['html'] = $html;
		echo json_encode($callback);
		//return;

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

	function get_company_all(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$this->db->order_by("company_name","asc");
		$this->db->where("company_flag", 0);
		$qd = $this->db->get("company");
		$rd = $qd->result();

		return $rd;
	}
	function get_company_bylevel(){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		$this->db->order_by("company_name","asc");
		/*if($this->sess->user_level == "1"){
			$this->db->where("company_created_by", $this->sess->user_id);
		}*/
		$this->db->where("company_created_by", $this->sess->user_id);
		$this->db->where("company_flag", 0);
		$qd = $this->db->get("company");
		$rd = $qd->result();

		return $rd;
	}
	
	function get_geofence_bydblive($dblive){
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
		
		$this->dblive = $this->load->database($dblive,true);
		$this->dblive->select("geofence_name");
		$this->dblive->order_by("geofence_name","asc");
		$this->dblive->where("geofence_user", 4203); //khusus bib
		$this->dblive->where("geofence_status", 1);
		$this->dblive->where("geofence_type", "road");
		$qd = $this->dblive->get("geofence");
		$rd = $qd->result();

		return $rd;
	}
	
	function getalarmreport(){
		ini_set('memory_limit', '2G');
		$this->db = $this->load->database("tensor_report", TRUE);
		$this->db->select("ritase_type,ritase_name");	
		$this->db->order_by("ritase_type", "asc");
		$this->db->where("ritase_status", 2);
		$q = $this->db->get("webtracking_ts_alarm");
		$rows = $q->result();
		
		if(count($rows)>0){
			$ritase_list = $rows;
		}else{
			$ritase_list = false;
		}
	
		return $ritase_list;
		
	}
	
	function getalarmreport_bylevel($level){
		ini_set('memory_limit', '2G');
		$this->db = $this->load->database("tensor_report", TRUE);
		$this->db->select("ritase_type,ritase_name,ritase_level");	
		$this->db->order_by("ritase_type", "asc");
		$this->db->where("ritase_status", 2);
		$this->db->where("ritase_level", $level);
		$q = $this->db->get("webtracking_ts_alarm");
		$rows = $q->result();
		
		if(count($rows)>0){
			$ritase_list = $rows;
		}else{
			$ritase_list = false;
		}
	
		return $ritase_list;
		
	}
	
	function getalarmreport_bygroup($group){
		ini_set('memory_limit', '2G');
		$this->db = $this->load->database("tensor_report", TRUE);
		$this->db->select("ritase_type,ritase_name,ritase_group");	
		$this->db->order_by("ritase_type", "asc");
		$this->db->where("ritase_status", 2);
		$this->db->where("ritase_group", $group);
		$q = $this->db->get("webtracking_ts_alarm");
		$rows = $q->result();
		
		if(count($rows)>0){
			$ritase_list = $rows;
		}else{
			$ritase_list = false;
		}
	
		return $ritase_list;
		
	}

}
