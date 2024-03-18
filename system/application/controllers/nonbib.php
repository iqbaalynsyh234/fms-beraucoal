<?php
include "base.php";

class Nonbib extends Base
{
	var $otherdb;

	function Nonbib()
	{
		parent::Base();
		$this->load->model("gpsmodel");
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
		$this->load->helper('common_helper');
		$this->load->model("dashboardmodel");
    $this->load->model("m_nonbib");
	}

	function index()
	{
		if (!isset($this->sess->user_type)) {
			redirect(base_url());
		}
    $privilegecode   = $this->sess->user_id_role;

		$mastervehicle = $this->m_nonbib->getmastervehicle();
		$rows_company  = $this->get_company_bylevel();

    // echo "<pre>";
    // var_dump($mastervehicle);die();
    // echo "<pre>";

		//$rows_geofence = $this->get_geofence_bydblive($user_dblive);

		$this->params["vehicles"] = $mastervehicle;
		$this->params["rcompany"] = $rows_company;
		//$this->params["rgeofence"] = $rows_geofence;
		$this->params['code_view_menu'] = "report";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/nonbibactivity/v_home_nonbibact', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		} elseif ($privilegecode == 2) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/nonbibactivity/v_home_nonbibact', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
		} elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/nonbibactivity/v_home_nonbibact', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		} elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/nonbibactivity/v_home_nonbibact', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		} elseif ($privilegecode == 5) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/nonbibactivity/v_home_nonbibact', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
		} elseif ($privilegecode == 6) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/nonbibactivity/v_home_nonbibact', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
		} else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/nonbibactivity/v_home_nonbibact', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}

  function search(){
    ini_set('display_errors', 1);
    //ini_set('memory_limit', '2G');
    if (!isset($this->sess->user_type)) {
      redirect(base_url());
    }
    $company        = $this->input->post('company');
    $vehicle        = $this->input->post("vehicle");
    $startdate      = $this->input->post("startdate");
    $enddate        = $this->input->post("enddate");
    $shour          = $this->input->post("shour");
    $ehour          = $this->input->post("ehour");
    $periode        = $this->input->post("periode");

    $nowdate        = date("Y-m-d");
    $nowday         = date("d");
    $nowmonth       = date("m");
    $nowyear        = date("Y");
    $lastday        = date("t");

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

    $m1           = date("F", strtotime($sdate));
    $m2           = date("F", strtotime($edate));
    $year         = date("Y", strtotime($sdate));
    $year2        = date("Y", strtotime($edate));
    $rows         = array();
    $total_q      = 0;

    $error        = "";
    $rows_summary = "";

    // if ($vehicle == "") {
    // 	$error .= "- Invalid Vehicle. Silahkan Pilih salah satu kendaraan! \n";
    // }
    if ($m1 != $m2) {
      $error .= "- Invalid Date. Tanggal Report yang dipilih harus dalam bulan yang sama! \n";
    }

    if ($year != $year2) {
      $error .= "- Invalid Year. Tanggal Report yang dipilih harus dalam tahun yang sama! \n";
    }

    if ($error != "") {
      $callback['error'] = true;
      $callback['message'] = $error;
      echo json_encode($callback);
      return;
    }

    // $company.'-'.$vehicle.'-'.$periode.'-'.$sdate.'-'.$edate

    $datanonbibact       = $this->m_nonbib->getdatanonbibact($company, $vehicle, $sdate, $edate);

    // echo "<pre>";
    // var_dump($datanonbibact);die();
    // echo "<pre>";

    $params['data']      = $datanonbibact;
		$params['startdate'] = $sdate;
		$params['enddate']   = $edate;

    $html                = $this->load->view("newdashboard/nonbibactivity/v_nonbibact_result", $params, true);

		$callback['error']   = false;
		$callback['html']    = $html;
		echo json_encode($callback);
  }

	function dumping()
	{
		if (!isset($this->sess->user_type)) {
			redirect(base_url());
		}
    $privilegecode = $this->sess->user_id_role;

		$mastervehicle = $this->m_nonbib->getmastervehicle();
		$rows_company  = $this->get_company_bylevel();

    // echo "<pre>";
    // var_dump($mastervehicle);die();
    // echo "<pre>";

		$this->params["vehicles"] = $mastervehicle;
		$this->params["rcompany"] = $rows_company;
		$this->params['code_view_menu'] = "report";

		$this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
		$this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

		if ($privilegecode == 1) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/nonbibactivity/v_home_nonbibactdumping', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
		} elseif ($privilegecode == 2) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/nonbibactivity/v_home_nonbibactdumping', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
		} elseif ($privilegecode == 3) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/nonbibactivity/v_home_nonbibactdumping', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
		} elseif ($privilegecode == 4) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/nonbibactivity/v_home_nonbibactdumping', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
		} elseif ($privilegecode == 5) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/nonbibactivity/v_home_nonbibactdumping', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
		} elseif ($privilegecode == 6) {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/nonbibactivity/v_home_nonbibactdumping', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
		} else {
			$this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
			$this->params["content"]        = $this->load->view('newdashboard/nonbibactivity/v_home_nonbibactdumping', $this->params, true);
			$this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
		}
	}

	function search_dumping(){
    ini_set('display_errors', 1);
    //ini_set('memory_limit', '2G');
    if (!isset($this->sess->user_type)) {
      redirect(base_url());
    }
    $company        = $this->input->post('company');
    $vehicle        = $this->input->post("vehicle");
    $startdate      = $this->input->post("startdate");
    $enddate        = $this->input->post("enddate");
    $shour          = $this->input->post("shour");
    $ehour          = $this->input->post("ehour");
    $periode        = $this->input->post("periode");

    $nowdate        = date("Y-m-d");
    $nowday         = date("d");
    $nowmonth       = date("m");
    $nowyear        = date("Y");
    $lastday        = date("t");

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

    $m1           = date("F", strtotime($sdate));
    $m2           = date("F", strtotime($edate));
    $year         = date("Y", strtotime($sdate));
    $year2        = date("Y", strtotime($edate));
    $rows         = array();
    $total_q      = 0;

    $error        = "";
    $rows_summary = "";

    // if ($vehicle == "") {
    // 	$error .= "- Invalid Vehicle. Silahkan Pilih salah satu kendaraan! \n";
    // }
    if ($m1 != $m2) {
      $error .= "- Invalid Date. Tanggal Report yang dipilih harus dalam bulan yang sama! \n";
    }

    if ($year != $year2) {
      $error .= "- Invalid Year. Tanggal Report yang dipilih harus dalam tahun yang sama! \n";
    }

    if ($error != "") {
      $callback['error'] = true;
      $callback['message'] = $error;
      echo json_encode($callback);
      return;
    }

    // $company.'-'.$vehicle.'-'.$periode.'-'.$sdate.'-'.$edate

    $datanonbibact       = $this->m_nonbib->nonbibact_dumping($company, $vehicle, $sdate, $edate);

    // echo "<pre>";
    // var_dump($datanonbibact);die();
    // echo "<pre>";

    $params['data']      = $datanonbibact;
		$params['startdate'] = $sdate;
		$params['enddate']   = $edate;

    $html                = $this->load->view("newdashboard/nonbibactivity/v_nonbibactdumping_result", $params, true);

		$callback['error']   = false;
		$callback['html']    = $html;
		echo json_encode($callback);
  }

  function get_company_bylevel()
	{
		if (!isset($this->sess->user_type)) {
			redirect(base_url());
		}
		$privilegecode 						= $this->sess->user_id_role;

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
			$this->db->where("company_created_by", $this->sess->user_company);
		}elseif ($privilegecode == 6) {
			$this->db->where("company_created_by", $this->sess->user_company);
		}

		$this->db->where("company_flag", 0);
		$qd = $this->db->get("company");
		$rd = $qd->result();

		return $rd;
	}

  function get_vehicle_by_company_with_numberorder($id)
	{
		if (!isset($this->sess->user_type)) {
			redirect(base_url());
		}

		$this->db->order_by("vehicle_no", "asc");
		$this->db->select("vehicle_id,vehicle_device,vehicle_name,vehicle_no,company_name");
		$this->db->where("vehicle_company", $id);
		if ($this->sess->user_group > 0) {
			$this->db->where("vehicle_group", $this->sess->user_group);
		}
		$this->db->where("vehicle_status <>", 3);
		$this->db->join("company", "vehicle_company = company_id", "left");
		$qd = $this->db->get("vehicle");
		$rd = $qd->result();

		if ($qd->num_rows() > 0) {
			$options = "<option value='0' selected='selected' >--All Vehicle--</option>";
			$i = 1;
			foreach ($rd as $obj) {
				$options .= "<option value='" . $obj->vehicle_device . "'>" . $i . ". " . $obj->vehicle_no . " - " . $obj->vehicle_name . " " . "(" . $obj->company_name . ")" . "</option>";
				$i++;
			}

			echo $options;
			return;
		}
	}

}
