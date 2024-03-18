<?php
include "base.php";

class Production extends Base {

	function Production()
	{
		parent::Base();
		$this->load->helper('common_helper');
		$this->load->model("dashboardmodel");
		$this->load->helper('common');
		if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}
	}
	
	function board()
	{
		
		ini_set('display_errors', 1);

		$this->params['code_view_menu'] = "report";
		
		$companydata = $this->getcompany_bycreator(4408);
		$portdata = $this->getport_bycreator(4408);
		
		$this->params["rcompany"]    = $companydata;
		$this->params["rport"]    = $portdata;
		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
		$this->params["content"]        = $this->load->view('newdashboard/production/v_dashboard_prod_mn', $this->params, true);
		$this->load->view("newdashboard/partial/template_dashboard_chart_new", $this->params);
	}
	
	function search()
	{
		ini_set('display_errors', 1);

		$this->params['code_view_menu'] = "report";
		
		$userid = 4408;
		$startdate  = $this->input->post("startdate");
		$enddate    = $this->input->post("enddate");
		$periode    = $this->input->post("periode");
		
		//$periode = "last7";
		//$company = "all";
		$port = $this->input->post("port");
		$company = $this->input->post("contractor");
		$error = "";
		
		
		/* if($company != "all"){
			
			$callback['error'] = true;
			$callback['message'] = "Data Per Contractor belum tersedia!";

			echo json_encode($callback);
			return;
		} */
		
		//$companydata = $this->getcompany_bycreator($userid);
		/* $portdata = $this->getport_bycreator($userid);
		print_r($portdata);exit(); */
		
		$shour = "00:00:00";
		$ehour = "23:59:59";
		
		
		$nowdate = date("Y-m-d");
		$nowday = date("d");
		$nowmonth = date("m");
		$nowyear = date("Y");
		$lastday = date("t");
		
		$report = "ritase_board";
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
		
		
		$sdate_show = date("d-m-Y", strtotime($sdate));
		$edate_show = date("d-m-Y", strtotime($edate));
		$periode_show = "PERIODE: ".$sdate_show." to ".$edate_show;
		$periode_show_percompany = $periode_show;
		
		//print_r($sdate." ".$edate);exit();
		$m1 = date("F", strtotime($sdate));
		$m2 = date("F", strtotime($edate));
		$year = date("Y", strtotime($sdate));
		$year2 = date("Y", strtotime($edate));
		
		
		$ritaseboard_by_port = $this->getRitaseBoard_byPort($userid,$port,$periode,$startdate,$enddate);
		$content_all_hauling = $this->getContentAllHauling($ritaseboard_by_port);
		$content_all_hauling_level2 = $this->getContentAllHauling_level2($ritaseboard_by_port);
		
		$ritaseboard_by_company = $this->getRitaseBoard_byCompany($userid,$company,$periode,$startdate,$enddate);
		$content_company = $this->getContentCompany($ritaseboard_by_company);
		$content_ton = $this->getContentTon($ritaseboard_by_company);
		$content_rit = $this->getContentRit($ritaseboard_by_company);
		
		$ritaseboard_by_hour = $this->getRitaseBoard_byHour($userid,$company,$periode,$startdate,$enddate);
		$content_hour_name = $this->getContentCompany($ritaseboard_by_hour);
		$content_ton_hour = $this->getContentTon($ritaseboard_by_hour);
		$content_rit_hour = $this->getContentRit($ritaseboard_by_hour);
	
		
		$this->params["content_all_hauling"] = $content_all_hauling;
		$this->params["content_all_hauling_level2"] = $content_all_hauling_level2;
		$this->params["content_all_hauling_hour"] = $ritaseboard_by_hour;
		
		//$this->params["content_ritase_allport"] = $ritaseboard_by_port;
		$this->params["content_company"] = $content_company;
		$this->params["content_ton"] = $content_ton;
		$this->params["content_rit"] = $content_rit;
		
		$this->params["content_hour_name"] = $content_hour_name;
		$this->params["content_ton_hour"] = $content_ton_hour;
		$this->params["content_rit_hour"] = $content_rit_hour;
		
		$this->params["periode_show"]  = $periode_show;
		$this->params["port_type"]  = $port;
		$this->params["company_type"]  = $company;
	
		
		$html                    = $this->load->view('newdashboard/production/v_dashboard_prod_result', $this->params, true);
		$callback["html"]        = $html;
	
		echo json_encode($callback);	
	}
	
	function getRitaseBoard_byPort($userid,$port,$periode,$startdate,$enddate)
	{
		$model = "port";
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

		$error = "";
		$rows_summary = "";

		$feature_level1 = array();
		$feature_level2 = array();
		
		$rows_master = $this->getport_bycreator(4408);
		//print_r($rows_master);exit();
		for($x=0; $x < count($rows_master); $x++)
			{
		
							$this->dbtrip = $this->load->database("tensor_report",true);
							$this->dbtrip->select("ritase_board_total,ritase_board_port");
							$this->dbtrip->order_by("ritase_board_id","asc");
							$this->dbtrip->where("ritase_board_date >=",$sdate);
							$this->dbtrip->where("ritase_board_date <=",$edate);
							$this->dbtrip->where("ritase_board_type",0); //default
							$this->dbtrip->where("ritase_board_model",$model);
							$this->dbtrip->where("ritase_board_port",$rows_master[$x]->port_name);
							$qdata = $this->dbtrip->get($dbtable);
							$totaldata = 0;
							$totaltonnage = 0;
							if ($qdata->num_rows>0)
							{
								$rows_data = $qdata->result();
								
								for($i=0; $i < count($rows_data); $i++)
								{
									$totaldata = $totaldata + $rows_data[$i]->ritase_board_total;
									
								}
								
								$totaltonnage = $totaldata*30;
							}
					
					$feature_level1[$x]['name'] = $rows_master[$x]->port_name;
					$feature_level1[$x]['y'] = (int)$totaltonnage;
					$feature_level1[$x]['drilldown'] = 'level2_content';
					//$feature_level1[$x]['drilldown'] = null;
							
			}
					
			//$content_level1 = json_encode($feature_level1);
			$content_level1 = $feature_level1;
			//print_r($content_level1);exit();
			//print_r(json_encode($content_level1));//exit();
							
			return $content_level1;
	}
	
	function getContentAllHauling($data){
		
		
		$total_data = count($data);
		$feature_level2 = array();
		
		$total_ton = 0;
		for($y=0; $y < $total_data; $y++)
		{
			$total_ton = $total_ton +$data[$y]['y'];
		}
		
		$feature_level2['name'] = 'ALL HAULING';
		$feature_level2['y'] = (int)$total_ton;
		$feature_level2['drilldown'] = 'content_level2';
		
		$content = $feature_level2;
		//print_r(json_encode($content));exit();
		return $content;
	
	}
	
	function getContentAllHauling_level2($data){
		
		
		$total_data = count($data);
		$feature_level2 = array();
		for($y=0; $y < $total_data; $y++)
		{
			
			$name = $data[$y]['name'];
			$total_value = (int)$data[$y]['y'];
			
			$feature_level2[$y][] = $name;
			$feature_level2[$y][] = $total_value;
		}
		
		$content = $feature_level2;
		//print_r(json_encode($content));exit();
		return $content;
	
	}
	
	
	function getRitaseBoard_byCompany($userid,$company,$periode,$startdate,$enddate)
	{
		$model = "company";
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

		$error = "";
		$rows_summary = "";

		$feature_level1 = array();
		$feature_level2 = array();
		
		$rows_master = $this->getcompany_bycreator(4408);
		//print_r($rows_master);exit();
		for($x=0; $x < count($rows_master); $x++)
			{
		
							$this->dbtrip = $this->load->database("tensor_report",true);
							$this->dbtrip->select("ritase_board_total,ritase_board_port");
							$this->dbtrip->order_by("ritase_board_id","asc");
							$this->dbtrip->where("ritase_board_date >=",$sdate);
							$this->dbtrip->where("ritase_board_date <=",$edate);
							$this->dbtrip->where("ritase_board_type",0); //default
							$this->dbtrip->where("ritase_board_model",$model);
							$this->dbtrip->where("ritase_board_vehicle_company",$rows_master[$x]->company_id);
							$qdata = $this->dbtrip->get($dbtable);
							$totaldata = 0;
							$totaltonnage = 0;
							if ($qdata->num_rows>0)
							{
								$rows_data = $qdata->result();
								
								for($i=0; $i < count($rows_data); $i++)
								{
									$totaldata = $totaldata + $rows_data[$i]->ritase_board_total;
									
								}
								
								$totaltonnage = $totaldata*30;
							}
					
					$feature_level1[$x]['name'] = $rows_master[$x]->company_name;
					$feature_level1[$x]['y_rit'] = (int)$totaldata;
					$feature_level1[$x]['y_ton'] = (int)$totaltonnage;
					//$feature_level1['drilldown'] = 'contractor';
					$feature_level1[$x]['drilldown'] = null;
							
			}
					
			//$content_level1 = json_encode($feature_level1);
			$content_level1 = $feature_level1;
			//print_r($content_level1);exit();
			//print_r(json_encode($content_level1));exit();
							
			return $content_level1;
	}
	
	function getRitaseBoard_byHour($userid,$company,$periode,$startdate,$enddate)
	{
		$model = "hour";
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

		$error = "";
		$rows_summary = "";

		$feature_level1 = array();
		$feature_level2 = array();
		
		$rows_master = $this->gethour_bycreator(4408);
		//print_r($rows_master);exit();
		for($x=0; $x < count($rows_master); $x++)
			{
		
							$this->dbtrip = $this->load->database("tensor_report",true);
							$this->dbtrip->select("ritase_board_total,ritase_board_port");
							$this->dbtrip->order_by("ritase_board_id","asc");
							$this->dbtrip->where("ritase_board_date >=",$sdate);
							$this->dbtrip->where("ritase_board_date <=",$edate);
							$this->dbtrip->where("ritase_board_type",0); //default
							$this->dbtrip->where("ritase_board_model",$model);
							$this->dbtrip->where("ritase_board_hour_name",$rows_master[$x]->hour_name);
							$qdata = $this->dbtrip->get($dbtable);
							$totaldata = 0;
							$totaltonnage = 0;
							if ($qdata->num_rows>0)
							{
								$rows_data = $qdata->result();
								
								for($i=0; $i < count($rows_data); $i++)
								{
									$totaldata = $totaldata + $rows_data[$i]->ritase_board_total;
									
								}
								
								$totaltonnage = $totaldata*30;
							}
					
					$feature_level1[$x]['name'] = $rows_master[$x]->hour_name;
					$feature_level1[$x]['y_rit'] = (int)$totaldata;
					$feature_level1[$x]['y_ton'] = (int)$totaltonnage;
					//$feature_level1['drilldown'] = 'contractor';
					$feature_level1[$x]['drilldown'] = null;
							
			}
					
			//$content_level1 = json_encode($feature_level1);
			$content_level1 = $feature_level1;
			//print_r($content_level1);exit();
			//print_r(json_encode($content_level1));exit();
							
			return $content_level1;
	}
	
	function getContentCompany($data){
		
		
		$total_data = count($data);
		$feature_level2 = array();
		for($y=0; $y < $total_data; $y++)
		{
			$feature_level2[$y] = $data[$y]['name'];
			//$feature_level2[$y]['y'] = $data[$y]['y_pa'];
			//$feature_level2[$y]['drilldown'] = null;
		}
		
		$content = $feature_level2;
		//print_r(json_encode($content));exit();
		return $content;
	
	}
	
	function getContentTon($data){
		
		
		$total_data = count($data);
		$feature_level2 = array();
		for($y=0; $y < $total_data; $y++)
		{
			$feature_level2[$y] = $data[$y]['y_ton'];
			//$feature_level2[$y]['y'] = $data[$y]['y_pa'];
			//$feature_level2[$y]['drilldown'] = null;
		}
		
		$content = $feature_level2;
		//print_r(json_encode($content));exit();
		return $content;
	
	}
	
	function getContentRit($data){
		
		
		$total_data = count($data);
		$feature_level2 = array();
		for($y=0; $y < $total_data; $y++)
		{
			$feature_level2[$y] = $data[$y]['y_rit'];
			//$feature_level2[$y]['y'] = $data[$y]['y_pa'];
			//$feature_level2[$y]['drilldown'] = null;
		}
		
		$content = $feature_level2;
		//print_r(json_encode($content));exit();
		return $content;
	
	}
	
	function getport_bycreator($userid){
		
		$this->dbts = $this->load->database("webtracking_ts",true);
		$this->dbts->select("*");
		$this->dbts->order_by("port_name", "asc");
		$this->dbts->where("port_type ", 0);
		$this->dbts->where("port_flag ", 0);
		$this->dbts->where("port_user", $userid);
		$q = $this->dbts->get("ts_port");
		$rows = $q->result();
		
		return $rows;
	}
	
	function getcompany_bycreator($userid){
		
		$this->db->select("company_id,company_name");	
		$this->db->order_by("company_name", "asc");
		$this->db->where("company_flag ", 0);
		$this->db->where("company_created_by", $userid);
		$q = $this->db->get("company");
		$rows = $q->result();
		//$total_rows = count($rows);
		
		return $rows;
	}
	
	function gethour_bycreator($userid){
		
		$this->dbts = $this->load->database("webtracking_ts",true);
		$this->dbts->select("*");
		$this->dbts->order_by("hour_name", "asc");
		$this->dbts->where("hour_type ", 1);
		$this->dbts->where("hour_flag ", 0);
		$this->dbts->where("hour_user", $userid);
		$q = $this->dbts->get("ts_hour");
		$rows = $q->result();
		
		return $rows;
	}
	
	
	

	




















}
