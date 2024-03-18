<?php
include "base.php";

class Summary extends Base
{

	function Summary()
	{
		parent::Base();
		$this->load->helper('common_helper');
		$this->load->model("dashboardmodel");
		$this->load->helper('common');
		if (!isset($this->sess->user_type)) {
			redirect(base_url());
		}
	}

	function board()
	{

		ini_set('display_errors', 1);

		$this->params['code_view_menu'] = "report";

		$companydata = $this->get_company();

		$this->params["rcompany"]    = $companydata;
		$this->params['code_view_menu'] = "dashboard";
		$privilegecode = $this->sess->user_id_role;

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
		$this->params["onload"]         = 1;
		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/summary/v_dashboard_mn', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		} elseif ($privilegecode == 2) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/summary/v_dashboard_mn', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
		} elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/summary/v_dashboard_mn', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		} elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/summary/v_dashboard_mn', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		} elseif ($privilegecode == 5) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/summary/v_dashboard_mn', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
		} elseif ($privilegecode == 6) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/summary/v_dashboard_mn', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
		} else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/summary/v_dashboard_mn', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
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
		$company = $this->input->post("contractor");
		$type = $this->input->post("type");
		$error = "";

		$companydata = $this->getcompany_bycreator($userid);

		$shour = "00:00:00";
		$ehour = "23:59:59";

		$nowdate  = date("Y-m-d");
		$nowday   = date("d");
		$nowmonth = date("m");
		$nowyear  = date("Y");
		$lastday  = date("t");

		$report     = "alarm_evidence_";
		$report_sum = "summary_";

		// print_r($periode);exit();

		if($periode == "custom"){
			// $sdate = date("Y-m-d H:i:s", strtotime("-1 Hour", strtotime($startdate." ".$shour)));
			$sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
			$edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));
		}elseif ($periode == "today") {
			$sdate = date("Y-m-d 23:00:00", strtotime("yesterday"));
			$edate = date("Y-m-d H:i:s");
			$datein = date("d-m-Y", strtotime($sdate));
		}else if($periode == "yesterday"){

			$sdate1 = date("Y-m-d 00:00:00", strtotime("yesterday"));
			$edate = date("Y-m-d 23:59:59", strtotime("yesterday"));
			// $sdate = date("Y-m-d H:i:s", strtotime("-1 Hour", strtotime($sdate1)));
			$sdate = date("Y-m-d H:i:s", strtotime($sdate1));
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

		// print_r($sdate." ".$edate);exit();

		$m1 = date("F", strtotime($sdate));
		$m2 = date("F", strtotime($edate));
		$year = date("Y", strtotime($sdate));
		$year2 = date("Y", strtotime($edate));
		$rows = array();
		$total_q = 0;

		$error = "";
		$rows_summary = "";

		if ($vehicle == "")
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

		if ($alarmtype == "")
		{
			$error .= "- Please Select Alarm Type! \n";
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


		if ($company == 'all') {
			$esdm_board_by_company = $this->getEsdmBoard_byCompany($userid, 'all', $periode, $startdate, $enddate);
		} else {
			$esdm_board_by_company = $this->getEsdmBoard_byCompany($userid, $company, $periode, $startdate, $enddate);


			$rows_company = $this->getDetailCompany_byID($company);
			$esdm_board_by_percompany = $this->getEsdmBoard_byPerCompany($userid, $company, $periode, $startdate, $enddate);
			$content_pa_percompany = $this->getContentPA($esdm_board_by_percompany);
			$content_ma_percompany = $this->getContentMA($esdm_board_by_percompany);
			$content_ua_percompany = $this->getContentUA($esdm_board_by_percompany);
			$content_eu_percompany = $this->getContentEU($esdm_board_by_percompany);


			$periode_show_percompany = $rows_company->company_name . " " . $periode_show;


			$this->params["periode_show_percompany"]  = $periode_show_percompany;
			$this->params["content_pa_percompany"]    = $content_pa_percompany;
			$this->params["content_ma_percompany"]    = $content_ma_percompany;
			$this->params["content_ua_percompany"]    = $content_ua_percompany;
			$this->params["content_eu_percompany"]    = $content_eu_percompany;
		}
		$content_pa = $this->getContentPA($esdm_board_by_company);
		$content_ma = $this->getContentMA($esdm_board_by_company);
		$content_ua = $this->getContentUA($esdm_board_by_company);
		$content_eu = $this->getContentEU($esdm_board_by_company);


		$this->params["content_pa"]    = $content_pa;
		$this->params["content_ma"]    = $content_ma;
		$this->params["content_ua"]    = $content_ua;
		$this->params["content_eu"]    = $content_eu;

		$this->params["periode_show"]  = $periode_show;
		$this->params["company_type"]  = $company;

		//print_r($esdm_board_by_company);exit();

		$html                    = $this->load->view('newdashboard/esdm/v_dashboard_esdm_result', $this->params, true);
		$callback["html"]        = $html;

		echo json_encode($callback);
	}
	
	//prod
	function search_dev()
	{
		ini_set('display_errors', 1);

		$this->params['code_view_menu'] = "report";

		$userid = 4408;
		$startdate  = $this->input->post("startdate");
		$enddate    = $this->input->post("enddate");
		$periode    = $this->input->post("periode");
		//$periode = "last7";
		//$company = "all";
		$company = $this->input->post("contractor");
		$type = $this->input->post("type");
		$error = "";

		$companydata = $this->getcompany_bycreator($userid);

		$shour = "00:00:00";
		$ehour = "23:59:59";

		$nowdate  = date("Y-m-d");
		$nowday   = date("d");
		$nowmonth = date("m");
		$nowyear  = date("Y");
		$lastday  = date("t");

		$report     = "alarm_evidence_";
		$report_sum = "summary_";

		// print_r($periode);exit();

		if($periode == "custom"){
			// $sdate = date("Y-m-d H:i:s", strtotime("-1 Hour", strtotime($startdate." ".$shour)));
			$sdate = date("Y-m-d H:i:s", strtotime($startdate." ".$shour));
			$edate = date("Y-m-d H:i:s", strtotime($enddate." ".$ehour));
		}elseif ($periode == "today") {
			$sdate = date("Y-m-d 23:00:00", strtotime("yesterday"));
			$edate = date("Y-m-d H:i:s");
			$datein = date("d-m-Y", strtotime($sdate));
		}else if($periode == "yesterday"){

			$sdate1 = date("Y-m-d 00:00:00", strtotime("yesterday"));
			$edate = date("Y-m-d 23:59:59", strtotime("yesterday"));
			// $sdate = date("Y-m-d H:i:s", strtotime("-1 Hour", strtotime($sdate1)));
			$sdate = date("Y-m-d H:i:s", strtotime($sdate1));
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

		// print_r($sdate." ".$edate);exit();

		$m1 = date("F", strtotime($sdate));
		$m2 = date("F", strtotime($edate));
		$year = date("Y", strtotime($sdate));
		$year2 = date("Y", strtotime($edate));
		$rows = array();
		$total_q = 0;

		$error = "";
		$rows_summary = "";

		if ($vehicle == "")
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

		if ($alarmtype == "")
		{
			$error .= "- Please Select Alarm Type! \n";
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

		// $content_pa_percompany = "";
		// $content_ma_percompany = "";
		// $content_ua_percompany = "";
		// $content_eu_percompany = "";


		if ($company == 'all') {
			$esdm_board_by_company = $this->getAlarmEvidence_byCompany($userid, 'all', $periode, $startdate, $enddate, $dbtable);

			$content_pa = $this->getContentPA_dev($esdm_board_by_company);
			$content_ma = $this->getContentMA_dev($esdm_board_by_company);
			$content_ua = $this->getContentUA_dev($esdm_board_by_company);
			$content_eu = $this->getContentEU_dev($esdm_board_by_company);
			// printf($content_pa[0]);
			// printf($content_pa[1]);
			// return;
			$this->params["data_company"] = $content_pa[0];
			$this->params["content_pa"]    = $content_pa[1];
			$this->params["content_ma"]    = $content_ma[1];
			$this->params["content_ua"]    = $content_ua[1];
			$this->params["content_eu"]    = $content_eu[1];
		} else {
			
			$esdm_board_by_percompany = $this->getEsdmBoard_byPerCompany($userid, $company, $periode, $startdate, $enddate);
			$content_pa_percompany = $this->getContentPA_dev_company($esdm_board_by_percompany);
			$content_ma_percompany = $this->getContentMA_dev_company($esdm_board_by_percompany);
			$content_ua_percompany = $this->getContentUA_dev_company($esdm_board_by_percompany);
			$content_eu_percompany = $this->getContentEU_dev_company($esdm_board_by_percompany);

			$this->params["content_pa"]    = $content_pa_percompany;
			$this->params["content_ma"]    = $content_ma_percompany;
			$this->params["content_ua"]    = $content_ua_percompany;
			$this->params["content_eu"]    = $content_eu_percompany;
		}

		$this->params["periode_show"]  = $periode_show;
		$this->params["company_type"]  = $company;

		

		$html                    = $this->load->view('newdashboard/esdm/v_dashboard_result', $this->params, true);
		$callback["html"]        = $html;

		echo json_encode($callback);
	}

	function getEsdmBoard_byCompany($userid, $company, $periode, $startdate, $enddate)
	{

		$model = "daily_perunit";
		$shour = "00:00:00";
		$ehour = "23:59:59";


		$nowdate = date("Y-m-d");
		$nowday = date("d");
		$nowmonth = date("m");
		$nowyear = date("Y");
		$lastday = date("t");

		$report = "ts_kepmen_source";
		$report_sum = "summary_";

		if ($periode == "custom") {
			$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour));
			$edate = date("Y-m-d H:i:s", strtotime($enddate . " " . $ehour));
		} else if ($periode == "yesterday") {

			$sdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
			$edate = date("Y-m-d 23:59:59", strtotime("yesterday"));
		} else if ($periode == "last7") {
			$nowday = $nowday - 1;
			$firstday = $nowday - 7;
			if ($nowday <= 7) {
				$firstday = 1;
			}

			/*if($firstday > $nowday){
				$firstday = 1;
			}*/

			$sdate = date("Y-m-d H:i:s ", strtotime($nowyear . "-" . $nowmonth . "-" . $firstday . " " . "00:00:00"));
			$edate = date("Y-m-d H:i:s", strtotime($nowyear . "-" . $nowmonth . "-" . $nowday . " " . "23:59:59"));
		} else if ($periode == "last30") {
			$firstday = "1";
			$sdate = date("Y-m-d H:i:s ", strtotime($nowyear . "-" . $nowmonth . "-" . $firstday . " " . "00:00:00"));
			$edate = date("Y-m-d H:i:s", strtotime($nowyear . "-" . $nowmonth . "-" . $lastday . " " . "23:59:59"));
		} else {
			$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour));
			$edate = date("Y-m-d H:i:s", strtotime($enddate . " " . $ehour));
		}

		//print_r($sdate." ".$edate);exit();

		$m1 = date("F", strtotime($sdate));
		$m2 = date("F", strtotime($edate));
		$year = date("Y", strtotime($sdate));
		$year2 = date("Y", strtotime($edate));

		/*

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

*/

		$error = "";
		$rows_summary = "";

		$feature_level1 = array();
		$feature_level2 = array();

		$rows_master = $this->getkepmen_bycreator(4408);
		//print_r($rows_master);exit();
		if ($company == 'all') {
			$rows_master_level2 = $this->getcompany_bycreator(4408);
		} else {
			$rows_master_level2 = $this->getcompany_bycreator(null, $company);
		}
		//print_r($rows_master);exit();
		$totaldata = 0;


		//master report
		/*for($x=0; $x < count($rows_master); $x++)
			{

				*/

		//master by company
		for ($y = 0; $y < count($rows_master_level2); $y++) {
			//get total hours by company
			$daily_sec = 24 * 3600;
			$total_unit_bycompany = $this->getTotalUnit_byCompany($rows_master_level2[$y]->company_id);
			//print_r($total_unit_bycompany."||");
			$total_hour_bycompany = $total_unit_bycompany * $daily_sec;

			//get detail
			$this->dbts = $this->load->database("webtracking_ts", true);
			$this->dbts->select("kepmen_working_time,kepmen_idle_time,kepmen_breakdown_time,kepmen_total_time,kepmen_standby_time");
			$this->dbts->order_by("kepmen_id", "asc");
			$this->dbts->where("kepmen_date >=", $sdate);
			$this->dbts->where("kepmen_date <=", $edate);
			$this->dbts->where("kepmen_company_id", $rows_master_level2[$y]->company_id);
			$this->dbts->where("kepmen_flag", 0);
			$this->dbts->where("kepmen_model", $model);
			$qdata = $this->dbts->get($report);

			$total_working_bycompany = 0;
			$total_idle_bycompany = 0;
			$total_breakdown_bycompany = 0;
			$total_standby_bycompany = 0;


			if ($qdata->num_rows > 0) {
				$rows_data = $qdata->result();
				$totaldata = 0;
				for ($i = 0; $i < count($rows_data); $i++) {
					$total_working_bycompany = $total_working_bycompany + $rows_data[$i]->kepmen_working_time;
					$total_idle_bycompany = $total_idle_bycompany + $rows_data[$i]->kepmen_idle_time;
					$total_breakdown_bycompany = $total_breakdown_bycompany + $rows_data[$i]->kepmen_breakdown_time;
					$total_standby_bycompany = $total_standby_bycompany + $rows_data[$i]->kepmen_standby_time;
				}
			}

			$total_w = $total_working_bycompany + $total_idle_bycompany;

			$PA_value = 0;
			$UA_value = 0;
			$EU_value = 0;
			$MA_value = 0;

			$T = round(($total_hour_bycompany / 3600), 0, PHP_ROUND_HALF_UP);
			$W = round(($total_w / 3600), 0, PHP_ROUND_HALF_UP);
			$R = round(($total_breakdown_bycompany / 3600), 0, PHP_ROUND_HALF_UP);
			$S = round(($total_standby_bycompany / 3600), 0, PHP_ROUND_HALF_UP);
			/*
							$WS = $W + $S;
							$WR = $W + $R;

							$PA_value =  (($WS) / ($W+$S+$R) * 100);
							$UA_value = ($W/$WS) * 100;
							$EU_value = ($W/($W+$R+$S)) * 100;
							$MA_value =  ($W/($WR)) * 100;

							*/
			$WS = $W + $S;
			$WR = $W + $R;

			$WSR = $W + $S + $R;
			//print_r($WSR);exit();

			if ($WS > 0) {

				$PA_value =  (($WS) / ($W + $S + $R) * 100);
				$UA_value = ($W / $WS) * 100;
			}


			if ($WSR > 0) {

				$EU_value = ($W / ($W + $R + $S)) * 100;
			}

			if ($WR > 0) {

				$MA_value =  ($W / ($WR)) * 100;
			}

			/*
								$MA_value =  ( $W / ($W+$R) ) / 100;
							*/

			$feature_level2[$y]['name'] = $rows_master_level2[$y]->company_name;
			//$feature_level2[$y]['y'] = round($PA_value,0,PHP_ROUND_HALF_UP);
			$feature_level2[$y]['unit'] = $total_unit_bycompany;
			$feature_level2[$y]['w'] = $W;
			$feature_level2[$y]['r'] = $R;
			$feature_level2[$y]['s'] = $S;
			$feature_level2[$y]['t'] = $T;

			$feature_level2[$y]['y_pa'] = round($PA_value, 0, PHP_ROUND_HALF_UP);
			$feature_level2[$y]['y_ua'] = round($UA_value, 0, PHP_ROUND_HALF_UP);
			$feature_level2[$y]['y_eu'] = round($EU_value, 0, PHP_ROUND_HALF_UP);
			$feature_level2[$y]['y_ma'] =  round($MA_value, 0, PHP_ROUND_HALF_UP);

			/* $feature_level2[$y]['y_pa'] = round($PA_value,0,PHP_ROUND_HALF_UP);
							$feature_level2[$y]['y_ma'] = round($UA_value,0,PHP_ROUND_HALF_UP);
							$feature_level2[$y]['y_eu'] = round($PA_value,0,PHP_ROUND_HALF_UP);
							$feature_level2[$y]['y_ma'] = round($UA_value,0,PHP_ROUND_HALF_UP); */

			$feature_level2[$y]['drilldown'] = true;

			//print_r($W."+".$R."+".$UA_value."+".$EU_value."||");
			//print_r($PA_value."+".$MA_value."+".$UA_value."+".$EU_value."||");
		}
		//exit();

		//$content_level2 = json_encode($feature_level2);
		$content_level2 = $feature_level2;

		return $content_level2;
	}

	function getEsdmBoard_byPerCompany($userid, $company, $periode, $startdate, $enddate)
	{

		$model = "daily_perunit";
		$shour = "00:00:00";
		$ehour = "23:59:59";


		$nowdate = date("Y-m-d");
		$nowday = date("d");
		$nowmonth = date("m");
		$nowyear = date("Y");
		$lastday = date("t");

		$report = "ts_kepmen_source";
		$report_sum = "summary_";

		if ($periode == "custom") {
			$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour));
			$edate = date("Y-m-d H:i:s", strtotime($enddate . " " . $ehour));
		} else if ($periode == "yesterday") {

			$sdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
			$edate = date("Y-m-d 23:59:59", strtotime("yesterday"));
		} else if ($periode == "last7") {
			$nowday = $nowday - 1;
			$firstday = $nowday - 7;
			if ($nowday <= 7) {
				$firstday = 1;
			}

			/*if($firstday > $nowday){
				$firstday = 1;
			}*/

			$sdate = date("Y-m-d H:i:s ", strtotime($nowyear . "-" . $nowmonth . "-" . $firstday . " " . "00:00:00"));
			$edate = date("Y-m-d H:i:s", strtotime($nowyear . "-" . $nowmonth . "-" . $nowday . " " . "23:59:59"));
		} else if ($periode == "last30") {
			$firstday = "1";
			$sdate = date("Y-m-d H:i:s ", strtotime($nowyear . "-" . $nowmonth . "-" . $firstday . " " . "00:00:00"));
			$edate = date("Y-m-d H:i:s", strtotime($nowyear . "-" . $nowmonth . "-" . $lastday . " " . "23:59:59"));
		} else {
			$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour));
			$edate = date("Y-m-d H:i:s", strtotime($enddate . " " . $ehour));
		}

		//print_r($sdate." ".$edate);exit();

		$m1 = date("F", strtotime($sdate));
		$m2 = date("F", strtotime($edate));
		$year = date("Y", strtotime($sdate));
		$year2 = date("Y", strtotime($edate));

		/*

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

*/

		$error = "";
		$rows_summary = "";

		$feature_level1 = array();
		$feature_level2 = array();

		$rows_company = $this->getDetailCompany_byID($company);

		$rows_master_level2 = $this->getVehicle_byCompany($company);
		//print_r($rows_master_level2);exit();

		$totaldata = 0;
		$y = 0;


		//master by company
		for ($y = 0; $y < count($rows_master_level2); $y++) {

			//get total hours by company
			$daily_sec = 24 * 3600;
			$total_unit_bycompany = $this->getTotalUnit_byCompany($company);
			//print_r($total_unit_bycompany."||");
			$total_hour_bycompany = $total_unit_bycompany * $daily_sec;

			//get detail
			$this->dbts = $this->load->database("webtracking_ts", true);
			$this->dbts->select("kepmen_working_time,kepmen_idle_time,kepmen_breakdown_time,kepmen_total_time,kepmen_standby_time");
			$this->dbts->order_by("kepmen_id", "asc");
			$this->dbts->where("kepmen_date >=", $sdate);
			$this->dbts->where("kepmen_date <=", $edate);
			$this->dbts->where("kepmen_vehicle_id", $rows_master_level2[$y]->vehicle_id);
			$this->dbts->where("kepmen_flag", 0);
			$this->dbts->where("kepmen_model", $model);
			$qdata = $this->dbts->get($report);

			$total_working_bycompany = 0;
			$total_idle_bycompany = 0;
			$total_breakdown_bycompany = 0;
			$total_standby_bycompany = 0;


			if ($qdata->num_rows > 0) {
				$rows_data = $qdata->result();
				$totaldata = 0;
				for ($i = 0; $i < count($rows_data); $i++) {
					$total_working_bycompany = $total_working_bycompany + $rows_data[$i]->kepmen_working_time;
					$total_idle_bycompany = $total_idle_bycompany + $rows_data[$i]->kepmen_idle_time;
					$total_breakdown_bycompany = $total_breakdown_bycompany + $rows_data[$i]->kepmen_breakdown_time;
					$total_standby_bycompany = $total_standby_bycompany + $rows_data[$i]->kepmen_standby_time;
				}
			}

			$total_w = $total_working_bycompany + $total_idle_bycompany;

			$PA_value = 0;
			$UA_value = 0;
			$EU_value = 0;
			$MA_value = 0;

			$T = round(($total_hour_bycompany / 3600), 0, PHP_ROUND_HALF_UP);
			$W = round(($total_w / 3600), 0, PHP_ROUND_HALF_UP);
			$R = round(($total_breakdown_bycompany / 3600), 0, PHP_ROUND_HALF_UP);
			$S = round(($total_standby_bycompany / 3600), 0, PHP_ROUND_HALF_UP);

			$WS = $W + $S;
			$WR = $W + $R;

			$WSR = $W + $S + $R;
			//print_r($WSR);exit();

			if ($WS > 0) {

				$PA_value =  (($WS) / ($W + $S + $R) * 100);
				$UA_value = ($W / $WS) * 100;
			}

			if ($WSR > 0) {

				$EU_value = ($W / ($W + $R + $S)) * 100;
			}

			if ($WR > 0) {

				$MA_value =  ($W / ($WR)) * 100;
			}


			/*
								$MA_value =  ( $W / ($W+$R) ) / 100;
							*/

			$feature_level2[$y]['name'] = $rows_master_level2[$y]->vehicle_no;
			//$feature_level2[$y]['y'] = round($PA_value,0,PHP_ROUND_HALF_UP);
			$feature_level2[$y]['unit'] = $total_unit_bycompany;
			$feature_level2[$y]['w'] = $W;
			$feature_level2[$y]['r'] = $R;
			$feature_level2[$y]['s'] = $S;
			$feature_level2[$y]['t'] = $T;

			$feature_level2[$y]['y_pa'] = round($PA_value, 0, PHP_ROUND_HALF_UP);
			$feature_level2[$y]['y_ua'] = round($UA_value, 0, PHP_ROUND_HALF_UP);
			$feature_level2[$y]['y_eu'] = round($EU_value, 0, PHP_ROUND_HALF_UP);
			$feature_level2[$y]['y_ma'] =  round($MA_value, 0, PHP_ROUND_HALF_UP);

			$feature_level2[$y]['drilldown'] = true;

			//print_r($W."+".$R."+".$UA_value."+".$EU_value."||");
			//print_r($PA_value."+".$MA_value."+".$UA_value."+".$EU_value."||");
		}
		//exit();
		//$content_level2 = json_encode($feature_level2);
		$content_level2 = $feature_level2;
		//print_r($content_level2);exit();
		return $content_level2;
	}
	
	function getAlarmEvidence_byCompany($userid, $company, $periode, $startdate, $enddate, $dbtable)
	{

		$model = "daily_perunit";
		$shour = "00:00:00";
		$ehour = "23:59:59";


		$nowdate = date("Y-m-d");
		$nowday = date("d");
		$nowmonth = date("m");
		$nowyear = date("Y");
		$lastday = date("t");

		$report = "ts_kepmen_source";
		$report_sum = "summary_";

		if ($periode == "custom") {
			$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour));
			$edate = date("Y-m-d H:i:s", strtotime($enddate . " " . $ehour));
		} else if ($periode == "yesterday") {

			$sdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
			$edate = date("Y-m-d 23:59:59", strtotime("yesterday"));
		} else if ($periode == "last7") {
			$nowday = $nowday - 1;
			$firstday = $nowday - 7;
			if ($nowday <= 7) {
				$firstday = 1;
			}

			/*if($firstday > $nowday){
				$firstday = 1;
			}*/

			$sdate = date("Y-m-d H:i:s ", strtotime($nowyear . "-" . $nowmonth . "-" . $firstday . " " . "00:00:00"));
			$edate = date("Y-m-d H:i:s", strtotime($nowyear . "-" . $nowmonth . "-" . $nowday . " " . "23:59:59"));
		} else if ($periode == "last30") {
			$firstday = "1";
			$sdate = date("Y-m-d H:i:s ", strtotime($nowyear . "-" . $nowmonth . "-" . $firstday . " " . "00:00:00"));
			$edate = date("Y-m-d H:i:s", strtotime($nowyear . "-" . $nowmonth . "-" . $lastday . " " . "23:59:59"));
		} else {
			$sdate = date("Y-m-d H:i:s", strtotime($startdate . " " . $shour));
			$edate = date("Y-m-d H:i:s", strtotime($enddate . " " . $ehour));
		}

		//print_r($sdate." ".$edate);exit();

		$m1 = date("F", strtotime($sdate));
		$m2 = date("F", strtotime($edate));
		$year = date("Y", strtotime($sdate));
		$year2 = date("Y", strtotime($edate));

		

		$error = "";
		$rows_summary = "";

		$feature_level1 = array();
		$feature_level2 = array();

		$rows_master = $this->getalerttype_bycreator(4408);
		//print_r($rows_master);exit();
		if ($company == 'all') {
			$rows_master_level2 = $this->getcompany_bycreator(4408);
		} else {
			$rows_master_level2 = $this->getcompany_bycreator(null, $company);
		}
		//print_r($rows_master);exit();
		$totaldata = 0;

		//master by company
		for ($y = 0; $y < count($rows_master_level2); $y++) {
			//get total hours by company
			$daily_sec = 24 * 3600;
			$total_unit_bycompany = $this->getTotalUnit_byCompany($rows_master_level2[$y]->company_id);
			//print_r($total_unit_bycompany."||");
			$total_hour_bycompany = $total_unit_bycompany * $daily_sec;

			//get detail
			$this->dbts = $this->load->database("webtracking_ts", true);
			$this->dbts->select("kepmen_working_time,kepmen_idle_time,kepmen_breakdown_time,kepmen_total_time,kepmen_standby_time");
			$this->dbts->order_by("kepmen_id", "asc");
			$this->dbts->where("kepmen_date >=", $sdate);
			$this->dbts->where("kepmen_date <=", $edate);
			$this->dbts->where("kepmen_company_id", $rows_master_level2[$y]->company_id);
			$this->dbts->where("kepmen_flag", 0);
			$this->dbts->where("kepmen_model", $model);
			$qdata = $this->dbts->get($report);

			$total_working_bycompany = 0;
			$total_idle_bycompany = 0;
			$total_breakdown_bycompany = 0;
			$total_standby_bycompany = 0;


			if ($qdata->num_rows > 0) {
				$rows_data = $qdata->result();
				$totaldata = 0;
				for ($i = 0; $i < count($rows_data); $i++) {
					$total_working_bycompany = $total_working_bycompany + $rows_data[$i]->kepmen_working_time;
					$total_idle_bycompany = $total_idle_bycompany + $rows_data[$i]->kepmen_idle_time;
					$total_breakdown_bycompany = $total_breakdown_bycompany + $rows_data[$i]->kepmen_breakdown_time;
					$total_standby_bycompany = $total_standby_bycompany + $rows_data[$i]->kepmen_standby_time;
				}
			}

			$total_w = $total_working_bycompany + $total_idle_bycompany;

			$PA_value = 0;
			$UA_value = 0;
			$EU_value = 0;
			$MA_value = 0;

			$T = round(($total_hour_bycompany / 3600), 0, PHP_ROUND_HALF_UP);
			$W = round(($total_w / 3600), 0, PHP_ROUND_HALF_UP);
			$R = round(($total_breakdown_bycompany / 3600), 0, PHP_ROUND_HALF_UP);
			$S = round(($total_standby_bycompany / 3600), 0, PHP_ROUND_HALF_UP);
			/*
							$WS = $W + $S;
							$WR = $W + $R;

							$PA_value =  (($WS) / ($W+$S+$R) * 100);
							$UA_value = ($W/$WS) * 100;
							$EU_value = ($W/($W+$R+$S)) * 100;
							$MA_value =  ($W/($WR)) * 100;

							*/
			$WS = $W + $S;
			$WR = $W + $R;

			$WSR = $W + $S + $R;
			//print_r($WSR);exit();

			if ($WS > 0) {

				$PA_value =  (($WS) / ($W + $S + $R) * 100);
				$UA_value = ($W / $WS) * 100;
			}


			if ($WSR > 0) {

				$EU_value = ($W / ($W + $R + $S)) * 100;
			}

			if ($WR > 0) {

				$MA_value =  ($W / ($WR)) * 100;
			}

			/*
								$MA_value =  ( $W / ($W+$R) ) / 100;
							*/

			$feature_level2[$y]['name'] = $rows_master_level2[$y]->company_name;
			//$feature_level2[$y]['y'] = round($PA_value,0,PHP_ROUND_HALF_UP);
			$feature_level2[$y]['unit'] = $total_unit_bycompany;
			$feature_level2[$y]['w'] = $W;
			$feature_level2[$y]['r'] = $R;
			$feature_level2[$y]['s'] = $S;
			$feature_level2[$y]['t'] = $T;

			$feature_level2[$y]['y_pa'] = round($PA_value, 0, PHP_ROUND_HALF_UP);
			$feature_level2[$y]['y_ua'] = round($UA_value, 0, PHP_ROUND_HALF_UP);
			$feature_level2[$y]['y_eu'] = round($EU_value, 0, PHP_ROUND_HALF_UP);
			$feature_level2[$y]['y_ma'] =  round($MA_value, 0, PHP_ROUND_HALF_UP);

			/* $feature_level2[$y]['y_pa'] = round($PA_value,0,PHP_ROUND_HALF_UP);
							$feature_level2[$y]['y_ma'] = round($UA_value,0,PHP_ROUND_HALF_UP);
							$feature_level2[$y]['y_eu'] = round($PA_value,0,PHP_ROUND_HALF_UP);
							$feature_level2[$y]['y_ma'] = round($UA_value,0,PHP_ROUND_HALF_UP); */

			$feature_level2[$y]['drilldown'] = true;

			//print_r($W."+".$R."+".$UA_value."+".$EU_value."||");
			//print_r($PA_value."+".$MA_value."+".$UA_value."+".$EU_value."||");
		}
		//exit();

		//$content_level2 = json_encode($feature_level2);
		$content_level2 = $feature_level2;

		return $content_level2;
	}

	function getContentPA($data)
	{


		$total_data = count($data);
		$feature_level2 = array();
		for ($y = 0; $y < $total_data; $y++) {
			$feature_level2[$y]['name'] = $data[$y]['name'];
			$feature_level2[$y]['y'] = $data[$y]['y_pa'];
			$feature_level2[$y]['drilldown'] = null;
		}

		$content = $feature_level2;
		//print_r(json_encode($content));exit();
		return $content;
	}
	function getContentPA_dev($data)
	{
		$total_data = count($data);
		$data_company = array();
		$datan = array();
		for ($y = 0; $y < $total_data; $y++) {
			$data_company[$y] = $data[$y]['name'];
			$datan[$y] = $data[$y]['y_pa'];
		}
		$content = array(
			'name' => 'PA',
			'color' => '#D32E36',
			'data' => $datan
		);
		return array($data_company, $content);
	}
	function getContentPA_dev_company($data)
	{
		$total_data = count($data);
		$datan = array();
		for ($y = 0; $y < $total_data; $y++) {
			$datan[$y] = array($data[$y]['name'], $data[$y]['y_pa']);
		}
		$content = array(
			'name' => 'PA',
			'color' => '#D32E36',
			'data' => $datan
		);
		return $content;
	}


	function getContentMA_dev($data)
	{
		$total_data = count($data);
		$data_company = array();
		$datan = array();
		for ($y = 0; $y < $total_data; $y++) {
			$data_company[$y] = $data[$y]['name'];
			$datan[$y] = $data[$y]['y_ma'];
		}
		$content = array(
			'name' => 'MA',
			'color' => '#434348',
			'data' => $datan
		);
		return array($data_company, $content);
	}

	function getContentMA_dev_company($data)
	{
		$total_data = count($data);
		$datan = array();
		for ($y = 0; $y < $total_data; $y++) {
			$datan[$y] = array($data[$y]['name'], $data[$y]['y_ma']);
		}
		$content = array(
			'name' => 'MA',
			'color' => '#434348',
			'data' => $datan
		);
		return $content;
	}

	function getContentUA_dev($data)
	{
		$total_data = count($data);
		$data_company = array();
		$datan = array();
		for ($y = 0; $y < $total_data; $y++) {
			$data_company[$y] = $data[$y]['name'];
			$datan[$y] = $data[$y]['y_ua'];
		}
		$content = array(
			'name' => 'UA',
			'color' => '#035405',
			'data' => $datan
		);
		return array($data_company, $content);
	}

	function getContentUA_dev_company($data)
	{
		$total_data = count($data);
		$datan = array();
		for ($y = 0; $y < $total_data; $y++) {
			$datan[$y] = array($data[$y]['name'], $data[$y]['y_ua']);
		}
		$content = array(
			'name' => 'UA',
			'color' => '#035405',
			'data' => $datan
		);
		return $content;
	}

	function getContentEU_dev($data)
	{
		$total_data = count($data);
		$data_company = array();
		$datan = array();
		for ($y = 0; $y < $total_data; $y++) {
			$data_company[$y] = $data[$y]['name'];
			$datan[$y] = $data[$y]['y_eu'];
		}
		$content = array(
			'name' => 'EU',
			'color' => '#0c0fad',
			'data' => $datan
		);
		return array($data_company, $content);
	}

	function getContentEU_dev_company($data)
	{
		$total_data = count($data);
		$datan = array();
		for ($y = 0; $y < $total_data; $y++) {
			$datan[$y] = array($data[$y]['name'], $data[$y]['y_eu']);
		}
		$content = array(
			'name' => 'EU',
			'color' => '#0c0fad',
			'data' => $datan
		);
		return $content;
	}

	function getContentPA_percompany($data)
	{


		$total_data = count($data);
		$feature_level2 = array();
		for ($y = 0; $y < $total_data; $y++) {
			$feature_level2[$y]['name'] = $data[$y]['name'];
			$feature_level2[$y]['y'] = $data[$y]['y_pa'];
			$feature_level2[$y]['drilldown'] = null;
		}

		$content = $feature_level2;
		// print_r(json_encode($content));
		// exit();
		return $content;
	}

	function getContentMA($data)
	{


		$total_data = count($data);
		$feature_level2 = array();
		for ($y = 0; $y < $total_data; $y++) {
			$feature_level2[$y]['name'] = $data[$y]['name'];
			$feature_level2[$y]['y'] = $data[$y]['y_ma'];
			$feature_level2[$y]['drilldown'] = null;
		}

		$content = $feature_level2;
		//print_r($content);exit();
		return $content;
	}

	function getContentUA($data)
	{


		$total_data = count($data);
		$feature_level2 = array();
		for ($y = 0; $y < $total_data; $y++) {
			$feature_level2[$y]['name'] = $data[$y]['name'];
			$feature_level2[$y]['y'] = $data[$y]['y_ua'];
			$feature_level2[$y]['drilldown'] = null;
		}

		$content = $feature_level2;
		//print_r($content);exit();
		return $content;
	}

	function getContentEU($data)
	{


		$total_data = count($data);
		$feature_level2 = array();
		for ($y = 0; $y < $total_data; $y++) {
			$feature_level2[$y]['name'] = $data[$y]['name'];
			$feature_level2[$y]['y'] = $data[$y]['y_eu'];
			$feature_level2[$y]['drilldown'] = null;
		}

		$content = $feature_level2;
		//print_r($content);exit();
		return $content;
	}

	function getcompany_bycreator($userid = null, $company_id = null)
	{

		$this->db->select("company_id,company_name");
		$this->db->order_by("company_name", "asc");
		$this->db->where("company_flag ", 0);
		if ($userid != null) {
			$this->db->where("company_created_by", $userid);
		}
		if ($company_id != null) {
			$this->db->where("company_id", $company_id);
		}
		$q = $this->db->get("company");
		$rows = $q->result();
		//$total_rows = count($rows);

		return $rows;
	}

	function getTotalUnit_byCompany($id)
	{

		$this->db->select("vehicle_id");
		$this->db->order_by("vehicle_id", "asc");
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_company", $id);
		$q = $this->db->get("vehicle");
		$rows = $q->result();
		$total_rows = count($rows);

		return $total_rows;
	}

	function getVehicle_byCompany($id)
	{

		$this->db->select("vehicle_id,vehicle_no");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_company", $id);
		$this->db->where("vehicle_user_id", 4408);
		$q = $this->db->get("vehicle");
		$rows = $q->result();
		//$total_rows = count($rows);

		return $rows;
	}

	function getDetailCompany_byID($id)
	{

		$this->db->select("company_id,company_name");
		$this->db->order_by("company_name", "asc");
		$this->db->where("company_flag ", 0);
		$this->db->where("company_id", $id);
		$q = $this->db->get("company");
		$rows = $q->row();

		return $rows;
	}



	function getkepmen_bycreator($userid)
	{

		$this->dbts = $this->load->database("webtracking_ts", true);
		$this->dbts->select("*");
		$this->dbts->order_by("name_id", "asc");
		$this->dbts->where("name_flag", 0);
		$this->dbts->where("name_user", $userid);
		$q = $this->dbts->get("ts_kepmen_type");
		$rows = $q->result();

		return $rows;
	}
	
	function getalerttype_bycreator($userid)
	{

		$this->dbts = $this->load->database("webtracking_ts", true);
		$this->dbts->select("*");
		$this->dbts->order_by("name_id", "asc");
		$this->dbts->where("name_flag", 0);
		$this->dbts->where("name_user", $userid);
		$q = $this->dbts->get("ts_board_alert_type");
		$rows = $q->result();

		return $rows;
	}


	function getstreetalias_bycreator($userid)
	{

		$this->dbts = $this->load->database("webtracking_ts", true);
		$this->dbts->select("*");
		$this->dbts->order_by("km_alias_value", "asc");
		$this->dbts->where("km_alias_flag ", 0);
		$this->dbts->where("km_alias_user", $userid);
		$q = $this->dbts->get("ts_km_alias");
		$rows = $q->result();

		return $rows;
	}

	function getsign_bycreator($userid)
	{

		$this->dbts = $this->load->database("webtracking_ts", true);
		$this->dbts->select("*");
		$this->dbts->order_by("sign_value", "asc");
		$this->dbts->where("sign_type ", 1);
		$this->dbts->where("sign_flag ", 0);
		$this->dbts->where("sign_user", $userid);
		$q = $this->dbts->get("ts_sign");
		$rows = $q->result();

		return $rows;
	}

	function getspeed_level_bycreator($userid)
	{

		$this->dbts = $this->load->database("webtracking_ts", true);
		$this->dbts->select("*");
		$this->dbts->order_by("level_value", "asc");
		$this->dbts->where("level_type ", 1);
		$this->dbts->where("level_flag ", 0);
		$this->dbts->where("level_user", $userid);
		$q = $this->dbts->get("ts_speed_level");
		$rows = $q->result();

		return $rows;
	}

	function gethour_bycreator($userid)
	{

		$this->dbts = $this->load->database("webtracking_ts", true);
		$this->dbts->select("*");
		$this->dbts->order_by("hour_name", "asc");
		$this->dbts->where("hour_type ", 1);
		$this->dbts->where("hour_flag ", 0);
		$this->dbts->where("hour_user", $userid);
		$q = $this->dbts->get("ts_hour");
		$rows = $q->result();

		return $rows;
	}
	function get_company()
	{
		if (!isset($this->sess->user_type)) {
			redirect(base_url());
		}

		$privilegecode = $this->sess->user_id_role;


		$this->db->order_by("company_name", "asc");
		if ($privilegecode == 0) {
			$this->db->where("company_created_by", $this->sess->user_id);
		} elseif ($privilegecode == 1) {
			$this->db->where("company_created_by", $this->sess->user_parent);
		} elseif ($privilegecode == 2) {
			$this->db->where("company_created_by", $this->sess->user_parent);
		} elseif ($privilegecode == 3) {
			$this->db->where("company_created_by", $this->sess->user_parent);
		} elseif ($privilegecode == 4) {
			$this->db->where("company_created_by", $this->sess->user_parent);
		} elseif ($privilegecode == 5) {
			$this->db->where("company_id", $this->sess->user_company);
		} elseif ($privilegecode == 6) {
			$this->db->where("company_id", $this->sess->user_company);
		}

		$this->db->where("company_flag", 0);
		$qd = $this->db->get("company");
		$rd = $qd->result();

		return $rd;
	}
}
