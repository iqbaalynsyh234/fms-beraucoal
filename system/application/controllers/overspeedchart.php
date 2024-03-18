<?php
include "base.php";

class Overspeedchart extends Base {
	var $otherdb;

	function Overspeedchart()
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
		$user_id = $this->sess->user_id;
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
		$rows_geofence = $this->get_geofence_bydblive($user_dblive);//print_r($rows_geofence);exit();

		$this->params["vehicles"] = $rows;
		$this->params["rcompany"] = $rows_company;
		$this->params["rgeofence"] = $rows_geofence;
		$this->params['code_view_menu'] = "report";


		$this->params["header"] = $this->load->view('dashboard/header', $this->params, true);
		$this->params["sidebar"] = $this->load->view('dashboard/sidebar', $this->params, true);
		$this->params["content"] = $this->load->view('dashboard/report/vspeed_chart_report', $this->params, true);
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
		$shour = "00:00:00";
		$ehour = "23:59:59";
		
		$nowdate = date("Y-m-d");
		$nowday = date("d");
		$nowmonth = date("m");
		$nowyear = date("Y");
		$lastday = date("t");
		
		$report = "overspeed_board_";
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
							$this->dbtrip->select("overspeed_board_total");
							$this->dbtrip->order_by("overspeed_board_id","asc");
							$this->dbtrip->where("overspeed_board_date >=",$sdate);
							$this->dbtrip->where("overspeed_board_date <=",$edate);
							$this->dbtrip->where("overspeed_board_type",$type);
							$this->dbtrip->where("overspeed_board_model",$model);
							$this->dbtrip->where("overspeed_board_vehicle_id",$vehicle[$z]->vehicle_id);
							$qdata = $this->dbtrip->get($dbtable);
							$totaloverspeed = 0;
							$maxdata = 0;
							if ($qdata->num_rows>0)
							{
								$rows_data = $qdata->result();
								for($i=0; $i < count($rows_data); $i++)
								{
									$totaloverspeed += $rows_data[$i]->overspeed_board_total;
									if($rows_data[$i]->overspeed_board_total > $maxdata){
										$maxdata = $rows_data[$i]->overspeed_board_total;
									}
									
								}
								
							}
									
							$feature[$z]["value"] = $totaloverspeed;
							$feature[$z]["name"] = $vehicle[$z]->vehicle_no." (".$totaloverspeed.")";
							$feature_name[$z] = $vehicle[$z]->vehicle_no." (".$totaloverspeed.")";
									
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
				
				$view = "vspeed_chart_result";
					
		}
		else if($model == "geofence")
		{
			$limitdata = 10;
			//get data vehicle
			$user_id = $this->sess->user_id;
			$user_level      = $this->sess->user_level;
			$user_company    = $this->sess->user_company;
			$user_subcompany = $this->sess->user_subcompany;
			$user_group      = $this->sess->user_group;
			$user_subgroup   = $this->sess->user_subgroup;
			$user_dblive 	  = $this->sess->user_dblive;
			$user_id_fix     = $user_id;
			
			$this->dblive = $this->load->database($this->sess->user_dblive,true); 
			$this->dblive->select("geofence_name");
			$this->dblive->order_by("geofence_id", "desc");
			$this->dblive->where("geofence_group", $type);
			$this->dblive->where("geofence_user", $user_id_fix);
			//$this->dblive->where("geofence_status", 1);
			$this->dblive->where("geofence_type", "road");
			$qv = $this->dblive->get("geofence");
			$geofence = $qv->result();
			
			if ($qv->num_rows() == 0)
			{
				$error .= "- No Data Geofence ! \n";
			}else{
					$feature = array();
					$feature_name = array();
					$feature_value = array();
					
					$feature_muatan = array();
					$feature_name_muatan = array();
					$feature_value_muatan = array();
					
					if(count($geofence) > 0)
					{
						for($z=0;$z<count($geofence);$z++)
						{
							$this->dbtrip = $this->load->database("tensor_report",true);
							$this->dbtrip->select("overspeed_board_total");
							$this->dbtrip->order_by("overspeed_board_id","asc");
							$this->dbtrip->where("overspeed_board_date >=",$sdate);
							$this->dbtrip->where("overspeed_board_date <=",$edate);
							$this->dbtrip->where("overspeed_board_type",$type);
							$this->dbtrip->where("overspeed_board_model",$model);
							$this->dbtrip->where("overspeed_board_jalur","kosongan");
							$this->dbtrip->where("overspeed_board_geofence",$geofence[$z]->geofence_name);
							$qdata = $this->dbtrip->get($dbtable);
							$totaloverspeed = 0;
							
							if ($qdata->num_rows>0)
							{
								$rows_data = $qdata->result();
								for($i=0; $i < count($rows_data); $i++)
								{
									$totaloverspeed += $rows_data[$i]->overspeed_board_total;
									
								}
								
							}
									
							$feature[$z]["value"] = $totaloverspeed;
							$feature[$z]["name"] = $geofence[$z]->geofence_name." (".$totaloverspeed.")";
							$feature_name[$z] = $geofence[$z]->geofence_name." (".$totaloverspeed.")";
									
						}
						
						for($z=0;$z<count($geofence);$z++)
						{
							$this->dbtrip = $this->load->database("tensor_report",true);
							$this->dbtrip->select("overspeed_board_total");
							$this->dbtrip->order_by("overspeed_board_id","asc");
							$this->dbtrip->where("overspeed_board_date >=",$sdate);
							$this->dbtrip->where("overspeed_board_date <=",$edate);
							$this->dbtrip->where("overspeed_board_type",$type);
							$this->dbtrip->where("overspeed_board_model",$model);
							$this->dbtrip->where("overspeed_board_jalur","muatan");
							$this->dbtrip->where("overspeed_board_geofence",$geofence[$z]->geofence_name);
							$qdata = $this->dbtrip->get($dbtable);
							$totaloverspeed_muatan = 0;
							
							if ($qdata->num_rows>0)
							{
								$rows_data = $qdata->result();
								for($i=0; $i < count($rows_data); $i++)
								{
									$totaloverspeed_muatan += $rows_data[$i]->overspeed_board_total;
									
								}
								
							}
									
							$feature_muatan[$z]["value"] = $totaloverspeed_muatan;
							$feature_muatan[$z]["name"] = $geofence[$z]->geofence_name." (".$totaloverspeed_muatan.")";
							$feature_name_muatan[$z] = $geofence[$z]->geofence_name." (".$totaloverspeed_muatan.")";
									
						}
						
						//kosongan
						$content_ex = $this->dashboardmodel->array_sort($feature, 'value', SORT_DESC);
						$content = array_slice($content_ex, 0, $limitdata);
						$content_name = array();
						for($u=0;$u<count($content);$u++)
						{
							$content_name[] = $content[$u]["name"];
						}
						$params['content'] = $content;
						$params['content_name'] = $content_name;
						
						
						//muatan
						$content_ex_muatan = $this->dashboardmodel->array_sort($feature_muatan, 'value', SORT_DESC);
						$content_muatan = array_slice($content_ex_muatan, 0, $limitdata);
						$content_name_muatan = array();
						for($v=0;$v<count($content_muatan);$v++)
						{
							$content_name_muatan[] = $content_muatan[$v]["name"];
						}
						
						$params['content_muatan'] = $content_muatan;
						$params['content_name_muatan'] = $content_name_muatan;
						
						$params['limitdata'] = $limitdata;
					}
					
					$view = "vgeofence_chart_result";
								
				}
					
		}
		else if($model == "street")
		{
			$limitdata = 10;
			//get data street
			$user_id = $this->sess->user_id;
			$user_level      = $this->sess->user_level;
			$user_company    = $this->sess->user_company;
			$user_subcompany = $this->sess->user_subcompany;
			$user_group      = $this->sess->user_group;
			$user_subgroup   = $this->sess->user_subgroup;
			$user_dblive 	  = $this->sess->user_dblive;
			$user_id_fix     = $user_id;
			
			$this->db = $this->load->database("default",true); 
			$this->db->select("street_id,street_name");
			$this->db->order_by("street_name", "asc");
			$this->db->where("street_creator", $user_id_fix);
			$this->db->like("street_name", "KM");
			$qv = $this->db->get("street");
			$street = $qv->result();
			
			if ($qv->num_rows() == 0)
			{
				$error .= "- No Data Street ! \n";
			}else{
					$feature = array();
					$feature_name = array();
					$feature_value = array();
					
					$feature_muatan = array();
					$feature_name_muatan = array();
					$feature_value_muatan = array();
					
					if(count($street) > 0)
					{
						for($z=0;$z<count($street);$z++)
						{
							$street_name = str_replace(",", "", $street[$z]->street_name);
							$this->dbtrip = $this->load->database("tensor_report",true);
							$this->dbtrip->select("overspeed_board_total");
							$this->dbtrip->order_by("overspeed_board_id","asc");
							$this->dbtrip->where("overspeed_board_date >=",$sdate);
							$this->dbtrip->where("overspeed_board_date <=",$edate);
							$this->dbtrip->where("overspeed_board_type",$type);
							$this->dbtrip->where("overspeed_board_model",$model);
							$this->dbtrip->where("overspeed_board_jalur","kosongan");
							$this->dbtrip->where("overspeed_board_street",$street_name);
							$qdata = $this->dbtrip->get($dbtable);
							$totaloverspeed = 0;
							
							if ($qdata->num_rows>0)
							{
								$rows_data = $qdata->result();
								for($i=0; $i < count($rows_data); $i++)
								{
									$totaloverspeed += $rows_data[$i]->overspeed_board_total;
									
								}
								
							}
									
							$feature[$z]["value"] = $totaloverspeed;
							$feature[$z]["name"] = $street_name." (".$totaloverspeed.")";
							$feature_name[$z] = $street_name." (".$totaloverspeed.")";
									
						}
						
						for($z=0;$z<count($street);$z++)
						{
							$street_name = str_replace(",", "", $street[$z]->street_name);
							$this->dbtrip = $this->load->database("tensor_report",true);
							$this->dbtrip->select("overspeed_board_total");
							$this->dbtrip->order_by("overspeed_board_id","asc");
							$this->dbtrip->where("overspeed_board_date >=",$sdate);
							$this->dbtrip->where("overspeed_board_date <=",$edate);
							$this->dbtrip->where("overspeed_board_type",$type);
							$this->dbtrip->where("overspeed_board_model",$model);
							$this->dbtrip->where("overspeed_board_jalur","muatan");
							$this->dbtrip->where("overspeed_board_street",$street_name);
							$qdata = $this->dbtrip->get($dbtable);
							$totaloverspeed_muatan = 0;
							
							if ($qdata->num_rows>0)
							{
								$rows_data = $qdata->result();
								for($i=0; $i < count($rows_data); $i++)
								{
									$totaloverspeed_muatan += $rows_data[$i]->overspeed_board_total;
									
								}
								
							}
									
							$feature_muatan[$z]["value"] = $totaloverspeed_muatan;
							$feature_muatan[$z]["name"] = $street_name." (".$totaloverspeed_muatan.")";
							$feature_name_muatan[$z] = $street_name." (".$totaloverspeed_muatan.")";
									
						}
						
						//kosongan
						$content_ex = $this->dashboardmodel->array_sort($feature, 'value', SORT_DESC);
						$content = array_slice($content_ex, 0, $limitdata);
						$content_name = array();
						for($u=0;$u<count($content);$u++)
						{
							$content_name[] = $content[$u]["name"];
						}
						$params['content'] = $content;
						$params['content_name'] = $content_name;
						
						
						//muatan
						$content_ex_muatan = $this->dashboardmodel->array_sort($feature_muatan, 'value', SORT_DESC);
						$content_muatan = array_slice($content_ex_muatan, 0, $limitdata);
						$content_name_muatan = array();
						for($v=0;$v<count($content_muatan);$v++)
						{
							$content_name_muatan[] = $content_muatan[$v]["name"];
						}
						
						$params['content_muatan'] = $content_muatan;
						$params['content_name_muatan'] = $content_name_muatan;
						
						$params['limitdata'] = $limitdata;
					}
					
					$view = "vstreet_chart_result";
								
				}
					
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

}
