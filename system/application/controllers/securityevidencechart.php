<?php
include "base.php";

class Securityevidencechart extends Base {
	var $otherdb;

	function Securityevidencechart()
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
		$this->params["content"] = $this->load->view('dashboard/report/vsecurityevidence_chart_report', $this->params, true);
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
		//$type = $this->input->post("type");
		$type = 0;
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
		
		$report = "alarm_board_";
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
			$this->db->where("vehicle_mv03 !=", "0000");
			$this->db->where("vehicle_device <> ","69969039633231@TK510");
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
							$this->dbtrip->select("alarm_board_total");
							$this->dbtrip->order_by("alarm_board_id","asc");
							$this->dbtrip->where("alarm_board_date >=",$sdate);
							$this->dbtrip->where("alarm_board_date <=",$edate);
							$this->dbtrip->where("alarm_board_type",$type);
							$this->dbtrip->where("alarm_board_model",$model);
							$this->dbtrip->where("alarm_board_vehicle_id",$vehicle[$z]->vehicle_id);
							$qdata = $this->dbtrip->get($dbtable);
							$totalalarm = 0;
							$maxdata = 0;
							if ($qdata->num_rows>0)
							{
								$rows_data = $qdata->result();
								for($i=0; $i < count($rows_data); $i++)
								{
									$totalalarm += $rows_data[$i]->alarm_board_total;
									if($rows_data[$i]->alarm_board_total > $maxdata){
										$maxdata = $rows_data[$i]->alarm_board_total;
									}
									
								}
								
							}
									
							$feature[$z]["value"] = $totalalarm;
							$feature[$z]["name"] = $vehicle[$z]->vehicle_no." (".$totalalarm.")";
							$feature_name[$z] = $vehicle[$z]->vehicle_no." (".$totalalarm.")";
									
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
				
				$view = "vsecurityevidence_vehicle_chart_result";
					
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
			$this->dblive->order_by("geofence_id", "asc");
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
							$this->dbtrip->select("alarm_board_total");
							$this->dbtrip->order_by("alarm_board_id","asc");
							$this->dbtrip->where("alarm_board_date >=",$sdate);
							$this->dbtrip->where("alarm_board_date <=",$edate);
							$this->dbtrip->where("alarm_board_type",$type);
							$this->dbtrip->where("alarm_board_model",$model);
							//$this->dbtrip->where("alarm_board_jalur","kosongan");
							$this->dbtrip->where("alarm_board_geofence",$geofence[$z]->geofence_name);
							$qdata = $this->dbtrip->get($dbtable);
							$totalalarm = 0;
							
							if ($qdata->num_rows>0)
							{
								$rows_data = $qdata->result();
								for($i=0; $i < count($rows_data); $i++)
								{
									$totalalarm += $rows_data[$i]->alarm_board_total;
									
								}
								
							}
									
							$feature[$z]["value"] = $totalalarm;
							$feature[$z]["name"] = $geofence[$z]->geofence_name." (".$totalalarm.")";
							$feature_name[$z] = $geofence[$z]->geofence_name." (".$totalalarm.")";
									
						}
						
					
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
					
					$view = "vsecurityevidence_geofence_chart_result";
								
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
							$this->dbtrip->select("alarm_board_total");
							$this->dbtrip->order_by("alarm_board_id","asc");
							$this->dbtrip->where("alarm_board_date >=",$sdate);
							$this->dbtrip->where("alarm_board_date <=",$edate);
							$this->dbtrip->where("alarm_board_type",$type);
							$this->dbtrip->where("alarm_board_model",$model);
							//$this->dbtrip->where("alarm_board_jalur","kosongan");
							$this->dbtrip->where("alarm_board_street",$street_name);
							$qdata = $this->dbtrip->get($dbtable);
							$totalalarm = 0;
							
							if ($qdata->num_rows>0)
							{
								$rows_data = $qdata->result();
								for($i=0; $i < count($rows_data); $i++)
								{
									$totalalarm += $rows_data[$i]->alarm_board_total;
									
								}
								
							}
									
							$feature[$z]["value"] = $totalalarm;
							$feature[$z]["name"] = $street_name." (".$totalalarm.")";
							$feature_name[$z] = $street_name." (".$totalalarm.")";
									
						}
						
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
					
					$view = "vsecurityevidence_street_chart_result";
								
				}
					
		}
		else if($model == "code")
		{
			$limitdata = 5;
			if($sublevel == 1){
				//get data street
				$user_id = $this->sess->user_id;
				$user_level      = $this->sess->user_level;
				$user_company    = $this->sess->user_company;
				$user_subcompany = $this->sess->user_subcompany;
				$user_group      = $this->sess->user_group;
				$user_subgroup   = $this->sess->user_subgroup;
				$user_dblive 	  = $this->sess->user_dblive;
				$user_id_fix     = $user_id;
				$one = "one";
				$two = "two";
				
				//level one
				$alarm_one = $this->getalarmreport_bylevel($one); 
				$total_alarm_one = count($alarm_one);
				if ($total_alarm_one == 0)
				{
					$error .= "- No Data Alarm Level One ! \n";
				}else{
						$feature_one = array();
						$feature_name_one = array();
						$feature_value_one = array();
						
						if(count($alarm_one) > 0)
						{
							for($z=0;$z<count($alarm_one);$z++)
							{
								$this->dbtrip = $this->load->database("tensor_report",true);
								$this->dbtrip->select("alarm_board_total");
								$this->dbtrip->order_by("alarm_board_id","asc");
								$this->dbtrip->where("alarm_board_date >=",$sdate);
								$this->dbtrip->where("alarm_board_date <=",$edate);
								$this->dbtrip->where("alarm_board_type",$type);
								$this->dbtrip->where("alarm_board_model",$model);
								$this->dbtrip->where("alarm_board_alarmtype",$alarm_one[$z]->alarm_type);
								$qdata = $this->dbtrip->get($dbtable);
								$totalalarm_one = 0;
								
								if ($qdata->num_rows>0)
								{
									$rows_data_one = $qdata->result();
									for($i=0; $i < count($rows_data_one); $i++)
									{
										$totalalarm_one += $rows_data_one[$i]->alarm_board_total;
										
									}
									
								}
										
								$feature_one[$z]["value"] = $totalalarm_one;
								$feature_one[$z]["name"] = $alarm_one[$z]->alarm_name." (".$totalalarm_one.")";
								//$feature_name[$z] = $alarm_one[$z]->alarm_name." (".$totalalarm.")";
										
							}
							
							$content_ex_one = $this->dashboardmodel->array_sort($feature_one, 'value', SORT_DESC);
							$content_one = array_slice($content_ex_one, 0, $limitdata);
							$content_name_one = array();
							for($u=0;$u<count($content_one);$u++)
							{
								$content_name_one[] = $content_one[$u]["name"];
							}
							$params['content_one'] = $content_one;
							$params['content_name_one'] = $content_name_one;
							
							$params['limitdata'] = $limitdata;
							
						}
									
					}
				
				//level two
				$alarm_two = $this->getalarmreport_bylevel($two); 
				$total_alarm_two = count($alarm_two);
				if ($total_alarm_two == 0)
				{
					$error .= "- No Data Alarm Level two ! \n";
				}else{
						$feature_two = array();
						$feature_name_two = array();
						$feature_value_two = array();
						
						if(count($alarm_two) > 0)
						{
							for($z=0;$z<count($alarm_two);$z++)
							{
								$this->dbtrip = $this->load->database("tensor_report",true);
								$this->dbtrip->select("alarm_board_total");
								$this->dbtrip->order_by("alarm_board_id","asc");
								$this->dbtrip->where("alarm_board_date >=",$sdate);
								$this->dbtrip->where("alarm_board_date <=",$edate);
								$this->dbtrip->where("alarm_board_type",$type);
								$this->dbtrip->where("alarm_board_model",$model);
								$this->dbtrip->where("alarm_board_alarmtype",$alarm_two[$z]->alarm_type);
								$qdata = $this->dbtrip->get($dbtable);
								$totalalarm_two = 0;
								
								if ($qdata->num_rows>0)
								{
									$rows_data_two = $qdata->result();
									for($i=0; $i < count($rows_data_two); $i++)
									{
										$totalalarm_two += $rows_data_two[$i]->alarm_board_total;
										
									}
									
								}
										
								$feature_two[$z]["value"] = $totalalarm_two;
								$feature_two[$z]["name"] = $alarm_two[$z]->alarm_name." (".$totalalarm_two.")";
								//$feature_name[$z] = $alarm_two[$z]->alarm_name." (".$totalalarm.")";
										
							}
							
							$content_ex_two = $this->dashboardmodel->array_sort($feature_two, 'value', SORT_DESC);
							$content_two = array_slice($content_ex_two, 0, $limitdata);
							$content_name_two = array();
							for($u=0;$u<count($content_two);$u++)
							{
								$content_name_two[] = $content_two[$u]["name"];
							}
							$params['content_two'] = $content_two;
							$params['content_name_two'] = $content_name_two;
							
							$params['limitdata'] = $limitdata;
							
						}
									
					}
					$view = "vsecurityevidence_level_chart_result";
				
				
			}
			//no data level
			else
			{
				//get data street
				$user_id = $this->sess->user_id;
				$user_level      = $this->sess->user_level;
				$user_company    = $this->sess->user_company;
				$user_subcompany = $this->sess->user_subcompany;
				$user_group      = $this->sess->user_group;
				$user_subgroup   = $this->sess->user_subgroup;
				$user_dblive 	  = $this->sess->user_dblive;
				$user_id_fix     = $user_id;
				
				$alarm = $this->getalarmreport(); 
				$total_alarm = count($alarm);
				if ($total_alarm == 0)
				{
					$error .= "- No Data Alarm ! \n";
				}else{
						$feature = array();
						$feature_name = array();
						$feature_value = array();
						
						if(count($alarm) > 0)
						{
							for($z=0;$z<count($alarm);$z++)
							{
								$this->dbtrip = $this->load->database("tensor_report",true);
								$this->dbtrip->select("alarm_board_total");
								$this->dbtrip->order_by("alarm_board_id","asc");
								$this->dbtrip->where("alarm_board_date >=",$sdate);
								$this->dbtrip->where("alarm_board_date <=",$edate);
								$this->dbtrip->where("alarm_board_type",$type);
								$this->dbtrip->where("alarm_board_model",$model);
								$this->dbtrip->where("alarm_board_alarmtype",$alarm[$z]->alarm_type);
								$qdata = $this->dbtrip->get($dbtable);
								$totalalarm = 0;
								
								if ($qdata->num_rows>0)
								{
									$rows_data = $qdata->result();
									for($i=0; $i < count($rows_data); $i++)
									{
										$totalalarm += $rows_data[$i]->alarm_board_total;
										
									}
									
								}
										
								$feature[$z]["value"] = $totalalarm;
								$feature[$z]["name"] = $alarm[$z]->alarm_name." (".$totalalarm.")";
								//$feature_name[$z] = $alarm[$z]->alarm_name." (".$totalalarm.")";
										
							}
							
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
						
						$view = "vsecurityevidence_code_chart_result";
									
					}
				
			}
			
					
		}
		else if($model == "adas")
		{
				$limitdata = 5;
			
				//get data street
				$user_id = $this->sess->user_id;
				$user_level      = $this->sess->user_level;
				$user_company    = $this->sess->user_company;
				$user_subcompany = $this->sess->user_subcompany;
				$user_group      = $this->sess->user_group;
				$user_subgroup   = $this->sess->user_subgroup;
				$user_dblive 	  = $this->sess->user_dblive;
				$user_id_fix     = $user_id;
				$adasgroup = 2;
				$modelnew = "adas_vehicle";
				
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
				$this->db->where("vehicle_mv03 !=", "0000");
				$this->db->where("vehicle_device <> ","69969039633231@TK510");
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
								$this->dbtrip->select("alarm_board_total");
								$this->dbtrip->order_by("alarm_board_id","asc");
								$this->dbtrip->where("alarm_board_date >=",$sdate);
								$this->dbtrip->where("alarm_board_date <=",$edate);
								$this->dbtrip->where("alarm_board_type",$type);
								$this->dbtrip->where("alarm_board_model",$modelnew);
								$this->dbtrip->where("alarm_board_vehicle_id",$vehicle[$z]->vehicle_id);
								$qdata = $this->dbtrip->get($dbtable);
								$totalalarm = 0;
								$maxdata = 0;
								if ($qdata->num_rows>0)
								{
									$rows_data = $qdata->result();
									for($i=0; $i < count($rows_data); $i++)
									{
										$totalalarm += $rows_data[$i]->alarm_board_total;
										if($rows_data[$i]->alarm_board_total > $maxdata){
											$maxdata = $rows_data[$i]->alarm_board_total;
										}
										
									}
									
								}
										
								$feature[$z]["value"] = $totalalarm;
								$feature[$z]["name"] = $vehicle[$z]->vehicle_no." (".$totalalarm.")";
								$feature_name[$z] = $vehicle[$z]->vehicle_no." (".$totalalarm.")";
										
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
			
				//by street
				$limitdata = 5;
				//get data street
				$user_id = $this->sess->user_id;
				$user_level      = $this->sess->user_level;
				$user_company    = $this->sess->user_company;
				$user_subcompany = $this->sess->user_subcompany;
				$user_group      = $this->sess->user_group;
				$user_subgroup   = $this->sess->user_subgroup;
				$user_dblive 	  = $this->sess->user_dblive;
				$user_id_fix     = $user_id;
				$modelnew = "adas_street";
				
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
								$this->dbtrip->select("alarm_board_total");
								$this->dbtrip->order_by("alarm_board_id","asc");
								$this->dbtrip->where("alarm_board_date >=",$sdate);
								$this->dbtrip->where("alarm_board_date <=",$edate);
								$this->dbtrip->where("alarm_board_type",$type);
								$this->dbtrip->where("alarm_board_model",$modelnew);
								$this->dbtrip->where("alarm_board_street",$street_name);
								$qdata = $this->dbtrip->get($dbtable);
								$totalalarm = 0;
								
								if ($qdata->num_rows>0)
								{
									$rows_data = $qdata->result();
									for($i=0; $i < count($rows_data); $i++)
									{
										$totalalarm += $rows_data[$i]->alarm_board_total;
										
									}
									
								}
										
								$feature[$z]["value"] = $totalalarm;
								$feature[$z]["name"] = $street_name." (".$totalalarm.")";
								$feature_name[$z] = $street_name." (".$totalalarm.")";
										
							}
							
							$content_ex = $this->dashboardmodel->array_sort($feature, 'value', SORT_DESC);
							$content = array_slice($content_ex, 0, $limitdata);
							$content_name = array();
							for($u=0;$u<count($content);$u++)
							{
								$content_name[] = $content[$u]["name"];
							}
							$params['content_street'] = $content;
							$params['content_name_street'] = $content_name;
							
							$params['limitdata'] = $limitdata;
							
						}
						
						
									
					}
			
				
					$view = "vsecurityevidence_adas_chart_result";
				
				
			
			
					
		}
		else if($model == "distracted_vehicle")
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
			$this->db->where("vehicle_mv03 !=", "0000");
			$this->db->where("vehicle_device <> ","69969039633231@TK510");
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
							$this->dbtrip->select("alarm_board_total");
							$this->dbtrip->order_by("alarm_board_id","asc");
							$this->dbtrip->where("alarm_board_date >=",$sdate);
							$this->dbtrip->where("alarm_board_date <=",$edate);
							$this->dbtrip->where("alarm_board_type",$type);
							$this->dbtrip->where("alarm_board_model",$model);
							$this->dbtrip->where("alarm_board_vehicle_id",$vehicle[$z]->vehicle_id);
							$qdata = $this->dbtrip->get($dbtable);
							$totalalarm = 0;
							$maxdata = 0;
							if ($qdata->num_rows>0)
							{
								$rows_data = $qdata->result();
								for($i=0; $i < count($rows_data); $i++)
								{
									$totalalarm += $rows_data[$i]->alarm_board_total;
									if($rows_data[$i]->alarm_board_total > $maxdata){
										$maxdata = $rows_data[$i]->alarm_board_total;
									}
									
								}
								
							}
									
							$feature[$z]["value"] = $totalalarm;
							$feature[$z]["name"] = $vehicle[$z]->vehicle_no." (".$totalalarm.")";
							$feature_name[$z] = $vehicle[$z]->vehicle_no." (".$totalalarm.")";
									
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
				
				$view = "vsecurityevidence_vehicle_distracted_chart_result";		
		}
		else if($model == "distracted_vehicle_shift_1")
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
			$this->db->where("vehicle_mv03 !=", "0000");
			$this->db->where("vehicle_device <> ","69969039633231@TK510");
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
							$this->dbtrip->select("alarm_board_total");
							$this->dbtrip->order_by("alarm_board_id","asc");
							$this->dbtrip->where("alarm_board_date >=",$sdate);
							$this->dbtrip->where("alarm_board_date <=",$edate);
							$this->dbtrip->where("alarm_board_type",$type);
							$this->dbtrip->where("alarm_board_model",$model);
							$this->dbtrip->where("alarm_board_vehicle_id",$vehicle[$z]->vehicle_id);
							$qdata = $this->dbtrip->get($dbtable);
							$totalalarm = 0;
							$maxdata = 0;
							if ($qdata->num_rows>0)
							{
								$rows_data = $qdata->result();
								for($i=0; $i < count($rows_data); $i++)
								{
									$totalalarm += $rows_data[$i]->alarm_board_total;
									if($rows_data[$i]->alarm_board_total > $maxdata){
										$maxdata = $rows_data[$i]->alarm_board_total;
									}
									
								}
								
							}
									
							$feature[$z]["value"] = $totalalarm;
							$feature[$z]["name"] = $vehicle[$z]->vehicle_no." (".$totalalarm.")";
							$feature_name[$z] = $vehicle[$z]->vehicle_no." (".$totalalarm.")";
									
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
				
				$view = "vsecurityevidence_vehicle_distracted_chart_result";		
		}
		else if($model == "distracted_vehicle_shift_2")
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
			$this->db->where("vehicle_mv03 !=", "0000");
			$this->db->where("vehicle_device <> ","69969039633231@TK510");
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
							$this->dbtrip->select("alarm_board_total");
							$this->dbtrip->order_by("alarm_board_id","asc");
							$this->dbtrip->where("alarm_board_date >=",$sdate);
							$this->dbtrip->where("alarm_board_date <=",$edate);
							$this->dbtrip->where("alarm_board_type",$type);
							$this->dbtrip->where("alarm_board_model",$model);
							$this->dbtrip->where("alarm_board_vehicle_id",$vehicle[$z]->vehicle_id);
							$qdata = $this->dbtrip->get($dbtable);
							$totalalarm = 0;
							$maxdata = 0;
							if ($qdata->num_rows>0)
							{
								$rows_data = $qdata->result();
								for($i=0; $i < count($rows_data); $i++)
								{
									$totalalarm += $rows_data[$i]->alarm_board_total;
									if($rows_data[$i]->alarm_board_total > $maxdata){
										$maxdata = $rows_data[$i]->alarm_board_total;
									}
									
								}
								
							}
									
							$feature[$z]["value"] = $totalalarm;
							$feature[$z]["name"] = $vehicle[$z]->vehicle_no." (".$totalalarm.")";
							$feature_name[$z] = $vehicle[$z]->vehicle_no." (".$totalalarm.")";
									
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
				
				$view = "vsecurityevidence_vehicle_distracted_chart_result";		
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
		$this->db->select("alarm_type,alarm_name");	
		$this->db->order_by("alarm_type", "asc");
		$this->db->where("alarm_status", 2);
		$q = $this->db->get("webtracking_ts_alarm");
		$rows = $q->result();
		
		if(count($rows)>0){
			$alarm_list = $rows;
		}else{
			$alarm_list = false;
		}
	
		return $alarm_list;
		
	}
	
	function getalarmreport_bylevel($level){
		ini_set('memory_limit', '2G');
		$this->db = $this->load->database("tensor_report", TRUE);
		$this->db->select("alarm_type,alarm_name,alarm_level");	
		$this->db->order_by("alarm_type", "asc");
		$this->db->where("alarm_status", 2);
		$this->db->where("alarm_level", $level);
		$q = $this->db->get("webtracking_ts_alarm");
		$rows = $q->result();
		
		if(count($rows)>0){
			$alarm_list = $rows;
		}else{
			$alarm_list = false;
		}
	
		return $alarm_list;
		
	}
	
	function getalarmreport_bygroup($group){
		ini_set('memory_limit', '2G');
		$this->db = $this->load->database("tensor_report", TRUE);
		$this->db->select("alarm_type,alarm_name,alarm_group");	
		$this->db->order_by("alarm_type", "asc");
		$this->db->where("alarm_status", 2);
		$this->db->where("alarm_group", $group);
		$q = $this->db->get("webtracking_ts_alarm");
		$rows = $q->result();
		
		if(count($rows)>0){
			$alarm_list = $rows;
		}else{
			$alarm_list = false;
		}
	
		return $alarm_list;
		
	}

}
