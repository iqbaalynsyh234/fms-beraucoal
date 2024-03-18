<?php
include "base.php";

class Board extends Base {

	function Board()
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
	
	function hse_old()
	{
		ini_set('display_errors', 1);

		$this->params['code_view_menu'] = "monitoring";
		$periode = "yesterday";
		
		$companydata = $this->getcompany_bycreator(4408);
		$overspeed_board_company = $this->getOverspeedBoard_byCompany(4408,"all",$periode);
		$overspeed_board_company_bycompany = $this->getOverspeedBoard_byCompany_contractor(4408,"all",$periode);
		$overspeed_board_company_level2 = $this->getOverspeedBoard_byCompany_level2(4408,"all",$periode);
		$overspeed_board_company_byhourly = $this->getOverspeedBoard_byCompany_hourly(4408,"all",$periode);
		$overspeed_board_company_byhourly2 = $this->getOverspeedBoard_byCompany_hourly2(4408,"all",$periode);
		$overspeed_board_company_bycompany_level = $this->getOverspeedBoard_byCompany_level(4408,"all",$periode);
		
		$shour = "00:00:00";
		$ehour = "23:59:59";
		$startdate = "";
		$enddate = "";
		
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
		$sdate_show = date("d-m-Y", strtotime($sdate));
		$edate_show = date("d-m-Y", strtotime($edate));
		$periode_show = $sdate_show." to ".$edate_show;

		$m1 = date("F", strtotime($sdate));
		$m2 = date("F", strtotime($edate));
		$year = date("Y", strtotime($sdate));
		$year2 = date("Y", strtotime($edate));
		$this->params["periode_show"]    = $periode_show;
		$this->params["content_all_overspeed"]    = $overspeed_board_company;
		$this->params["content_all_overspeed"]    = $overspeed_board_company;
		$this->params["content_all_overspeed_bycompany"]    = $overspeed_board_company_level2;
		$this->params["content_all_overspeed_bycontractor"]    = $overspeed_board_company_bycompany;
		$this->params["content_all_overspeed_byhourly"]    = $overspeed_board_company_byhourly;
		$this->params["content_all_overspeed_byhourly2"]    = $overspeed_board_company_byhourly2;
		$this->params["content_all_overspeed_bycompany_level"]    = $overspeed_board_company_bycompany_level;
		
		$this->params["rcompany"]    = $companydata;
		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
		$this->params["content"]        = $this->load->view('newdashboard/board/v_dashboard_hse', $this->params, true);
		$this->load->view("newdashboard/partial/template_dashboard_hse", $this->params);
	}
	
	function hse_old2()
	{
		ini_set('display_errors', 1);

		$this->params['code_view_menu'] = "monitoring";
		$periode = "yesterday";
		
		$companydata = $this->getcompany_bycreator(4408);
		$overspeed_board_company = $this->getOverspeedBoard_byCompany(4408,"all",$periode);
		$overspeed_board_company_bycompany = $this->getOverspeedBoard_byCompany_contractor(4408,"all",$periode);
		$overspeed_board_company_level2 = $this->getOverspeedBoard_byCompany_level2(4408,"all",$periode);
		$overspeed_board_company_byhourly = $this->getOverspeedBoard_byCompany_hourly(4408,"all",$periode);
		$overspeed_board_company_byhourly2 = $this->getOverspeedBoard_byCompany_hourly2(4408,"all",$periode);
		$overspeed_board_company_bycompany_level = $this->getOverspeedBoard_byCompany_level(4408,"all",$periode);
		$overspeed_board_company_bystreet = $this->getOverspeedBoard_byStreet(4408,"all",$periode);
		$overspeed_board_company_bysign = $this->getOverspeedBoard_bySign(4408,"all",$periode);
		
		$shour = "00:00:00";
		$ehour = "23:59:59";
		$startdate = "";
		$enddate = "";
		
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
		$sdate_show = date("d-m-Y", strtotime($sdate));
		$edate_show = date("d-m-Y", strtotime($edate));
		$periode_show = $sdate_show." to ".$edate_show;

		$m1 = date("F", strtotime($sdate));
		$m2 = date("F", strtotime($edate));
		$year = date("Y", strtotime($sdate));
		$year2 = date("Y", strtotime($edate));
		$this->params["periode_show"]    = $periode_show;
		$this->params["content_all_overspeed"]    = $overspeed_board_company;
		$this->params["content_all_overspeed"]    = $overspeed_board_company;
		$this->params["content_all_overspeed_bycompany"]    = $overspeed_board_company_level2;
		$this->params["content_all_overspeed_bycontractor"]    = $overspeed_board_company_bycompany;
		$this->params["content_all_overspeed_byhourly"]    = $overspeed_board_company_byhourly;
		$this->params["content_all_overspeed_byhourly2"]    = $overspeed_board_company_byhourly2;
		$this->params["content_all_overspeed_bycompany_level"]    = $overspeed_board_company_bycompany_level;
		$this->params["content_all_overspeed_bystreet"]    = $overspeed_board_company_bystreet;
		$this->params["content_all_overspeed_bysign"]    = $overspeed_board_company_bysign;
		
		$this->params["rcompany"]    = $companydata;
		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
		$this->params["content"]        = $this->load->view('newdashboard/board/v_dashboard_hse2', $this->params, true);
		$this->load->view("newdashboard/partial/template_dashboard_hse", $this->params);
	}
	
	function hse()
	{
		
		ini_set('display_errors', 1);

		$this->params['code_view_menu'] = "report";
		
		$companydata = $this->getcompany_bycreator(4408);
		
		
		$this->params["rcompany"]    = $companydata;
		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
		$this->params["content"]        = $this->load->view('newdashboard/board/v_dashboard_hse_mn', $this->params, true);
		$this->load->view("newdashboard/partial/template_dashboard_hse_new", $this->params);
	}
	
	function searchhse()
	{
		ini_set('display_errors', 1);

		$this->params['code_view_menu'] = "report";
		
		$userid = 4408;
		$startdate  = $this->input->post("startdate");
		$enddate    = $this->input->post("enddate");
		$periode    = $this->input->post("periode");
		//$periode = "yesterday";
		//$company = "all";
		$company = $this->input->post("contractor");
		$violation = $this->input->post("violation");
		$error = "";
		
		if($company != "all"){
			
			$callback['error'] = true;
			$callback['message'] = "Data Per Contractor belum tersedia!";

			echo json_encode($callback);
			return;
		}
		
		
		if($violation != "overspeed"){
		
			$callback['error'] = true;
			$callback['message'] = "Data tersedia sementara hanya overspeed!";

			echo json_encode($callback);
			return;
		}
		
		$companydata = $this->getcompany_bycreator($userid);
		
		
		$shour = "00:00:00";
		$ehour = "23:59:59";
		$startdate = "";
		$enddate = "";
		
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
		
		
		$sdate_show = date("d-m-Y", strtotime($sdate));
		$edate_show = date("d-m-Y", strtotime($edate));
		$periode_show = "Periode: ".$sdate_show." to ".$edate_show;
		
		// print_r($periode_show);exit();
		$m1 = date("F", strtotime($sdate));
		$m2 = date("F", strtotime($edate));
		$year = date("Y", strtotime($sdate));
		$year2 = date("Y", strtotime($edate));
		
		//ganti dashboard trend hourly, daily
		if($sdate_show != $edate_show)
		{
			$trend_status = 1;
			
		}
		else
		{
			$trend_status = 0;
			
		}
		
		$overspeed_board_company_byhourly = $this->getOverspeedBoard_byCompany_hourly($userid,$company,$periode);
		$overspeed_board_company_byhourly2 = $this->getOverspeedBoard_byCompany_hourly2($userid,$company,$periode);
		
		$overspeed_board_company = $this->getOverspeedBoard_byCompany($userid,$company,$periode);
		$overspeed_board_company_bycompany = $this->getOverspeedBoard_byCompany_contractor($userid,$company,$periode);
		$overspeed_board_company_level2 = $this->getOverspeedBoard_byCompany_level2($userid,$company,$periode);
		
		$overspeed_board_company_bycompany_level = $this->getOverspeedBoard_byCompany_level($userid,$company,$periode);
		$overspeed_board_company_bystreet = $this->getOverspeedBoard_byStreet($userid,$company,$periode);
		$overspeed_board_company_bysign = $this->getOverspeedBoard_bySign($userid,$company,$periode);
		
		
		$this->params["trend_status"]    = $trend_status;
		$this->params["periode_show"]    = $periode_show;
		$this->params["content_all_overspeed"]    = $overspeed_board_company;
		$this->params["content_all_overspeed"]    = $overspeed_board_company;
		$this->params["content_all_overspeed_bycompany"]    = $overspeed_board_company_level2;
		$this->params["content_all_overspeed_bycontractor"]    = $overspeed_board_company_bycompany;
		$this->params["content_all_overspeed_byhourly"]    = $overspeed_board_company_byhourly;
		$this->params["content_all_overspeed_byhourly2"]    = $overspeed_board_company_byhourly2;
		$this->params["content_all_overspeed_bycompany_level"]    = $overspeed_board_company_bycompany_level;
		$this->params["content_all_overspeed_bystreet"]    = $overspeed_board_company_bystreet;
		$this->params["content_all_overspeed_bysign"]    = $overspeed_board_company_bysign;
		
		$html                    = $this->load->view('newdashboard/board/v_dashboard_hse_result', $this->params, true);
		$callback["html"]        = $html;
	
		echo json_encode($callback);	
	}
	
	function getOverspeedBoard_byCompany($userid,$company,$periode)
	{
		
		$model = "company";
		$shour = "00:00:00";
		$ehour = "23:59:59";
		$startdate = "";
		$enddate = "";
		
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
		
							$this->dbtrip = $this->load->database("tensor_report",true);
							$this->dbtrip->select("overspeed_board_total,overspeed_board_company_name");
							$this->dbtrip->order_by("overspeed_board_id","asc");
							$this->dbtrip->where("overspeed_board_date >=",$sdate);
							$this->dbtrip->where("overspeed_board_date <=",$edate);
							if($company != "all"){
								$this->dbtrip->where("overspeed_board_company",$company); //default
							}
							$this->dbtrip->where("overspeed_board_type",0); //default
							$this->dbtrip->where("overspeed_board_model",$model);
							$qdata = $this->dbtrip->get($dbtable);
							$totaloverspeed = 0;
							
							if ($qdata->num_rows>0)
							{
								$rows_data = $qdata->result();
								
								for($i=0; $i < count($rows_data); $i++)
								{
									$totaloverspeed += $rows_data[$i]->overspeed_board_total;
									//$feature_level2[] = $rows_data[$i]->overspeed_board_company_name.",".$rows_data[$i]->overspeed_board_total;
									
								}
							}
					
							$feature_level1['name'] = 'Total Overspeed';
							$feature_level1['y'] = $totaloverspeed;
							//$feature_level1['drilldown'] = 'contractor';
							$feature_level1['drilldown'] = "level";
							//$content = json_encode($feature);
							$content_level1 = $feature_level1;
							
							//print_r($content_level1);exit();
							//print_r(json_encode($content_level1));//exit();
							//return $content_level1;
							return $content_level1;
									
					/*
					
					
					
					["RAM",0],["STLI",0]

					sample
						data: [{name: "01-Des-21",y: 99000,drilldown: "01"},{name: "01-Des-21",y: 99000,drilldown: "01"}]
					*/
			
	}
	
	function getOverspeedBoard_byCompany_level2($userid,$company,$periode)
	{
		
		$model = "company";
		$shour = "00:00:00";
		$ehour = "23:59:59";
		$startdate = "";
		$enddate = "";
		
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
		
							$this->dbtrip = $this->load->database("tensor_report",true);
							$this->dbtrip->select("overspeed_board_total,overspeed_board_company_name");
							$this->dbtrip->order_by("overspeed_board_id","asc");
							$this->dbtrip->where("overspeed_board_date >=",$sdate);
							$this->dbtrip->where("overspeed_board_date <=",$edate);
							if($company != "all"){
								$this->dbtrip->where("overspeed_board_company",$company); //default
							}
							$this->dbtrip->where("overspeed_board_type",0); //default
							$this->dbtrip->where("overspeed_board_model",$model);
							$qdata = $this->dbtrip->get($dbtable);
							$totaloverspeed = 0;
							
							if ($qdata->num_rows>0)
							{
								$rows_data = $qdata->result();
								//print_r($rows_data);//exit();
								for($i=0; $i < count($rows_data); $i++)
								{
									//$totaloverspeed += $rows_data[$i]->overspeed_board_total;
									$feature_level2[][] = $rows_data[$i]->overspeed_board_company_name.",".$rows_data[$i]->overspeed_board_total; 
									
									//$rows_data[$i]->overspeed_board_total;
								}
							}
					
						
							//$content_level1 = $feature_level2;
							//print_r($rows_data);
							$content_level2 = json_encode($feature_level2);
							//print_r($content_level2);exit();
							
							//return $content_level1;
							return $content_level2;
									
					/*
					
					
					
					["RAM",0],["STLI",0]

					sample
						data: [{name: "01-Des-21",y: 99000,drilldown: "01"},{name: "01-Des-21",y: 99000,drilldown: "01"}]
					*/
			
	}
	
	function getOverspeedBoard_byCompany_contractor($userid,$company,$periode)
	{
		
		$model = "company";
		$shour = "00:00:00";
		$ehour = "23:59:59";
		$startdate = "";
		$enddate = "";
		
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
		$totaldata = 0;
		for($x=0; $x < count($rows_master); $x++)
			{
		
							$this->dbtrip = $this->load->database("tensor_report",true);
							$this->dbtrip->select("overspeed_board_total,overspeed_board_company_name");
							$this->dbtrip->order_by("overspeed_board_company_name","asc");
							$this->dbtrip->where("overspeed_board_date >=",$sdate);
							$this->dbtrip->where("overspeed_board_date <=",$edate);
							$this->dbtrip->where("overspeed_board_vehicle_company",$rows_master[$x]->company_id);
							$this->dbtrip->where("overspeed_board_type",0); //default
							$this->dbtrip->where("overspeed_board_model",$model);
							$qdata = $this->dbtrip->get($dbtable);
							$totaloverspeed = 0;
							
							if ($qdata->num_rows>0)
							{
								$rows_data = $qdata->result();
								$totaldata = 0;
								for($i=0; $i < count($rows_data); $i++)
								{
								
									$totaldata = $totaldata + $rows_data[$i]->overspeed_board_total;
								}
							}
							
							$feature_level1[$x]['name'] = $rows_master[$x]->company_name;
							$feature_level1[$x]['y'] = (int)$totaldata;
							//$feature_level1['drilldown'] = 'contractor';
							$feature_level1[$x]['drilldown'] = null;
							
			}
					
			//$content_level1 = json_encode($feature_level1);
			$content_level1 = $feature_level1;
			//print_r($content_level1);exit();
			//print_r(json_encode($content_level1));exit();
							
			return $content_level1;
	}
	
	function getOverspeedBoard_byCompany_hourly($userid,$company,$periode)
	{
		
		$model = "hour";
		$shour = "00:00:00";
		$ehour = "23:59:59";
		$startdate = "";
		$enddate = "";
		
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

		$feature_hour = array();
		//$feature_level2 = array();
		
		$rows_master = $this->gethour_bycreator(4408);
		//print_r($rows_master);exit();
		
		for($x=0; $x < count($rows_master); $x++)
		{
						/*
							$this->dbtrip = $this->load->database("tensor_report",true);
							$this->dbtrip->select("overspeed_board_total,overspeed_board_hour_name");
							$this->dbtrip->order_by("overspeed_board_hour_name","asc");
							$this->dbtrip->where("overspeed_board_date >=",$sdate);
							$this->dbtrip->where("overspeed_board_date <=",$edate);
							$this->dbtrip->where("overspeed_board_type",0); //default
							$this->dbtrip->where("overspeed_board_model",$model);
							$this->dbtrip->where("overspeed_board_hour_name",$rows_master[$x]->hour_name);
							$qdata = $this->dbtrip->get($dbtable);
							$totaldata = 0;
							
							if ($qdata->num_rows>0)
							{
								$rows_data = $qdata->result();
								//print_r($rows_data);exit();
								for($i=0; $i < count($rows_data); $i++)
								{
									
									$totaldata = $totaldata + $rows_data[$i]->overspeed_board_total;
									//$feature_hour[$i] = $rows_data[$i]->overspeed_board_hour_name;
								}
							}
							
							*/
							
							$feature_hour[$x] = $rows_master[$x]->hour_name;
							$result = $feature_hour;
							
		}
						//print_r($result);exit();	
		return $result;
		
	}
	
	function getOverspeedBoard_byCompany_hourly2($userid,$company,$periode)
	{
		
		$model = "hour";
		$shour = "00:00:00";
		$ehour = "23:59:59";
		$startdate = "";
		$enddate = "";
		
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

		//$feature_level1 = array();
		$feature_total = array();
		
		$rows_master = $this->gethour_bycreator(4408);
		//print_r($rows_master);exit();
		
		for($x=0; $x < count($rows_master); $x++)
		{
							$this->dbtrip = $this->load->database("tensor_report",true);
							$this->dbtrip->select("overspeed_board_total,overspeed_board_hour_name");
							$this->dbtrip->order_by("overspeed_board_hour_name","asc");
							$this->dbtrip->where("overspeed_board_date >=",$sdate);
							$this->dbtrip->where("overspeed_board_date <=",$edate);
							$this->dbtrip->where("overspeed_board_type",0); //default
							$this->dbtrip->where("overspeed_board_model",$model);
							$this->dbtrip->where("overspeed_board_hour_name",$rows_master[$x]->hour_name);
							$qdata = $this->dbtrip->get($dbtable);
							$totaldata = 0;
							
							if ($qdata->num_rows>0)
							{
								$rows_data = $qdata->result();
								//print_r($rows_data);exit();
								for($i=0; $i < count($rows_data); $i++)
								{
									
									$totaldata = $totaldata + $rows_data[$i]->overspeed_board_total;
									//$feature_hour[$i] = $rows_data[$i]->overspeed_board_hour_name;
								}
							}
							
							$feature_total[$x] = (int)$totaldata;
							$result = $feature_total;
		}
		//print_r($result);exit();
		return $result;
	}
	
	function getOverspeedBoard_byCompany_level($userid,$company,$periode)
	{
		
		$model = "level";
		$shour = "00:00:00";
		$ehour = "23:59:59";
		$startdate = "";
		$enddate = "";
		
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

		$feature = array();
		$rows_master = $this->getspeed_level_bycreator(4408);
		$total = 0;
		for($x=0; $x < count($rows_master); $x++)
			{
							$this->dbtrip = $this->load->database("tensor_report",true);
							$this->dbtrip->select("overspeed_board_total,overspeed_board_level_name");
							$this->dbtrip->order_by("overspeed_board_level_name","asc");
							$this->dbtrip->where("overspeed_board_date >=",$sdate);
							$this->dbtrip->where("overspeed_board_date <=",$edate);
							$this->dbtrip->where("overspeed_board_type",0); //default
							$this->dbtrip->where("overspeed_board_level_name",$rows_master[$x]->level_name); //default
							$this->dbtrip->where("overspeed_board_model",$model);
							$qdata = $this->dbtrip->get($dbtable);
							$totaldata = 0;
							
							if ($qdata->num_rows>0)
							{
								$rows_data = $qdata->result();
								//print_r($rows_data);exit();
								for($i=0; $i < count($rows_data); $i++)
								{
									
									$totaldata = $totaldata + $rows_data[$i]->overspeed_board_total;
								}
								
								$total = (int)$totaldata;
								
							}
							
							$name = $rows_master[$x]->level_name;
							$content_name = $name.",".$total;
							$feature[$x][] = $name;
							$feature[$x][] = $total;
							//$feature_hour[$i] = $rows_data[$i]->overspeed_board_hour_name;
			}
			
			//print_r($feature);exit();
			//print_r(json_encode($feature));exit();
			//$result = $feature;
			$result = $feature;
			return $result;
	}
	
	function getOverspeedBoard_byStreet($userid,$company,$periode)
	{
		$model = "street";
		$shour = "00:00:00";
		$ehour = "23:59:59";
		$startdate = "";
		$enddate = "";
		
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
		
		$rows_master = $this->getstreetalias_bycreator(4408);
		//print_r($rows_master);exit();
		for($x=0; $x < count($rows_master); $x++)
			{
		
							$this->dbtrip = $this->load->database("tensor_report",true);
							$this->dbtrip->select("overspeed_board_total,overspeed_board_street_alias");
							$this->dbtrip->order_by("overspeed_board_street_alias","asc");
							$this->dbtrip->where("overspeed_board_date >=",$sdate);
							$this->dbtrip->where("overspeed_board_date <=",$edate);
							$this->dbtrip->where("overspeed_board_type",0); //default
							$this->dbtrip->where("overspeed_board_model",$model);
							$this->dbtrip->where("overspeed_board_street_alias",$rows_master[$x]->km_alias_value);
							$qdata = $this->dbtrip->get($dbtable);
							$totaldata = 0;
							
							if ($qdata->num_rows>0)
							{
								$rows_data = $qdata->result();
								
								for($i=0; $i < count($rows_data); $i++)
								{
									$totaldata = $totaldata + $rows_data[$i]->overspeed_board_total;
									
								}
							}
					
					$feature_level1[$x]['name'] = $rows_master[$x]->km_alias_name;
					$feature_level1[$x]['y'] = (int)$totaldata;
					//$feature_level1['drilldown'] = 'contractor';
					$feature_level1[$x]['drilldown'] = null;
							
			}
					
			//$content_level1 = json_encode($feature_level1);
			$content_level1 = $feature_level1;
			//print_r($content_level1);exit();
			//print_r(json_encode($content_level1));exit();
							
			return $content_level1;
	}
	
	function getOverspeedBoard_bySign($userid,$company,$periode)
	{
		$model = "sign";
		$shour = "00:00:00";
		$ehour = "23:59:59";
		$startdate = "";
		$enddate = "";
		
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
		
		$rows_master = $this->getsign_bycreator(4408);
		//print_r($rows_master);exit();
		for($x=0; $x < count($rows_master); $x++)
			{
		
							$this->dbtrip = $this->load->database("tensor_report",true);
							$this->dbtrip->select("overspeed_board_total,overspeed_board_sign");
							$this->dbtrip->order_by("overspeed_board_id","asc");
							$this->dbtrip->where("overspeed_board_date >=",$sdate);
							$this->dbtrip->where("overspeed_board_date <=",$edate);
							$this->dbtrip->where("overspeed_board_type",0); //default
							$this->dbtrip->where("overspeed_board_model",$model);
							$this->dbtrip->where("overspeed_board_sign",$rows_master[$x]->sign_value);
							$qdata = $this->dbtrip->get($dbtable);
							$totaldata = 0;
							
							if ($qdata->num_rows>0)
							{
								$rows_data = $qdata->result();
								
								for($i=0; $i < count($rows_data); $i++)
								{
									$totaldata = $totaldata + $rows_data[$i]->overspeed_board_total;
									
								}
							}
					
					$feature_level1[$x]['name'] = $rows_master[$x]->sign_name;
					$feature_level1[$x]['y'] = (int)$totaldata;
					//$feature_level1['drilldown'] = 'contractor';
					$feature_level1[$x]['drilldown'] = null;
							
			}
					
			//$content_level1 = json_encode($feature_level1);
			$content_level1 = $feature_level1;
			//print_r($content_level1);exit();
			//print_r(json_encode($content_level1));exit();
							
			return $content_level1;
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
	
	function getstreetalias_bycreator($userid){
		
		$this->dbts = $this->load->database("webtracking_ts",true);
		$this->dbts->select("*");
		$this->dbts->order_by("km_alias_value", "asc");
		$this->dbts->where("km_alias_flag ", 0);
		$this->dbts->where("km_alias_user", $userid);
		$q = $this->dbts->get("ts_km_alias");
		$rows = $q->result();
		
		return $rows;
	}
	
	function getsign_bycreator($userid){
		
		$this->dbts = $this->load->database("webtracking_ts",true);
		$this->dbts->select("*");
		$this->dbts->order_by("sign_value", "asc");
		$this->dbts->where("sign_type ", 1);
		$this->dbts->where("sign_flag ", 0);
		$this->dbts->where("sign_user", $userid);
		$q = $this->dbts->get("ts_sign");
		$rows = $q->result();
		
		return $rows;
	}
	
	function getspeed_level_bycreator($userid){
		
		$this->dbts = $this->load->database("webtracking_ts",true);
		$this->dbts->select("*");
		$this->dbts->order_by("level_value", "asc");
		$this->dbts->where("level_type ", 1);
		$this->dbts->where("level_flag ", 0);
		$this->dbts->where("level_user", $userid);
		$q = $this->dbts->get("ts_speed_level");
		$rows = $q->result();
		
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
