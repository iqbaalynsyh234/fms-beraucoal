<?php
include "base.php";

class Georeport extends Base
{
    function index()
    {
        $this->load->model("dashboardmodel");

        if (!isset($this->sess->user_type)) {
            redirect(base_url());
        }

        $privilegecode = $this->sess->user_id_role;

        $this->db->order_by("company_name", "asc");
        $this->db->where("company_created_by", 4408);
        $this->db->where("company_flag", 0);
        $qd = $this->db->get("company");
        $rows_company = $qd->result();

        $this->db->select("vehicle_no,vehicle_name,vehicle_id,vehicle_imei,vehicle_device,vehicle_company");
        $this->db->order_by("vehicle_no", "asc");
        $this->db->where("vehicle_user_id", 4408);
        $this->db->where("vehicle_status", 1);
        $q = $this->db->get("vehicle");
        $vehicledata = $q->result();


        $this->params["rows_company"]       = $rows_company;
        $this->params["vehicles"]    = $vehicledata;


        $this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
        $this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
        $this->params["onload"]         = 1;
        if ($privilegecode == 1) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/geofence/v_georeport', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
        } elseif ($privilegecode == 2) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/geofence/v_georeport', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
        } elseif ($privilegecode == 3) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/geofence/v_georeport', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
        } elseif ($privilegecode == 4) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/geofence/v_georeport', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
        } elseif ($privilegecode == 5) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/geofence/v_georeport', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
        } elseif ($privilegecode == 6) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/geofence/v_georeport', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
        } else {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/geofence/v_georeport', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
        }
    }

    function search_georeport() //source:tensor report > ritase_bln_thn
    {
        $company = $this->input->post("company");
        $vehicle = $this->input->post("vehicle");
        $periode = $this->input->post("periode");
        $model = $this->input->post("model");
        // $nowdate = date('Y-m-d');
        $year = date('Y');
        $mont = date('m');
        $nowday = date('d');
        // $lastdate = date('t');
        $err = false;
        if ($periode == "today") {
            $sdate = date("Y-m-d 00:00:00");
            $edate = date("Y-m-d 23:59:59");
            $datein = date("d-m-Y", strtotime($sdate));
        } else if ($periode == "yesterday") {
            $sdate = date("Y-m-d 00:00:00", strtotime("yesterday"));
            $edate = date("Y-m-d 23:59:59", strtotime("yesterday"));
            $datein = date("d-m-Y", strtotime("yesterday"));
        } else if ($periode == "last7") {
            $nowday = $nowday - 1;
            $firstday = $nowday - 7;
            if ($nowday <= 7) {
                $firstday = 1;
            }
            $sdate = date("Y-m-d 00:00:00", strtotime($year . "-" . $mont . "-" . $firstday));
            $edate = date("Y-m-d 23:59:59", strtotime($year . "-" . $mont . "-" . $nowday));
            $datein = date("d-m-Y", strtotime($sdate)) . " s.d. " . date("d-m-Y", strtotime($edate));
        } else if ($periode == "last30") {

            // $sdate = date("Y-m-d H:i:s ", strtotime(" -30 day", strtotime($nowdate)));
            // $edate = date("Y-m-d 23:59:59", strtotime($year . "-" . $mont . "-" . $nowday));

            $sdate = date("Y-m-d 00:00:00", strtotime($year . "-" . $mont . "-1"));
            $edate = date("Y-m-d 23:59:59", strtotime($year . "-" . $mont . "-" . $nowday));

            $datein = date("d-m-Y", strtotime($sdate)) . " s.d. " . date("d-m-Y", strtotime($edate));
        } else if ($periode == "custom") {
            $sdate = $this->input->post("sdate");
            $edate = $this->input->post("edate");
            $sdate = date("Y-m-d 00:00:00", strtotime($sdate));
            $edate = date("Y-m-d 23:59:59", strtotime($edate));
            $datein = date("d-m-Y", strtotime($sdate)) . " s.d. " . date("d-m-Y", strtotime($edate));
            $diff = strtotime($edate) - strtotime($sdate);
            if ($diff < 0) {
                $err = true;
                $msg = "Date is not correct!";
            }
            $diff = strtotime(date("Y-m-d")) - strtotime($sdate);
            if ($diff < 0) {
                $err = true;
                $msg = "Date is not correct!";
            }
            if ($company == "all") {
                $diff = strtotime($edate) - strtotime($sdate);
                if ($diff > 604800) {
                    $err = true;
                    $msg = "Maximum date range for all contractors is 7 days!";
                }
            }
            $diff1 = date("m", strtotime($sdate));
            $diff2 = date("m", strtotime($edate));
            if ($diff1 != $diff2) {
                $err = true;
                $msg = "Date must be in the same month!";
            }
            $diff1 = date("Y", strtotime($sdate));
            $diff2 = date("Y", strtotime($edate));
            if ($diff1 != $diff2) {
                $err = true;
                $msg = "Date must be in the same year!";
            }
            $year = date("Y", strtotime($sdate));
        }

        if ($err == true) {
            $callback['error'] = true;
            $callback['message'] = $msg;
            echo json_encode($callback);
            return;
        }
        $m1           = date("F", strtotime($sdate));
        $report         = "georeport_";
        switch ($m1) {
            case "January":
                $dbtable   = $report . "januari_" . $year;
                break;
            case "February":
                $dbtable   = $report . "februari_" . $year;
                break;
            case "March":
                $dbtable   = $report . "maret_" . $year;
                break;
            case "April":
                $dbtable   = $report . "april_" . $year;
                break;
            case "May":
                $dbtable   = $report . "mei_" . $year;
                break;
            case "June":
                $dbtable   = $report . "juni_" . $year;
                break;
            case "July":
                $dbtable   = $report . "juli_" . $year;
                break;
            case "August":
                $dbtable   = $report . "agustus_" . $year;
                break;
            case "September":
                $dbtable   = $report . "september_" . $year;
                break;
            case "October":
                $dbtable   = $report . "oktober_" . $year;
                break;
            case "November":
                $dbtable   = $report . "november_" . $year;
                break;
            case "December":
                $dbtable   = $report . "desember_" . $year;
                break;
        }
        $privilegecode   = $this->sess->user_id_role;
        $user_id         = $this->sess->user_id;
        $user_parent     = $this->sess->user_parent;
        $user_company    = $this->sess->user_company;
        $this->dbtrip = $this->load->database("tensor_report", true);
        // $this->dbtrip->order_by("georeport_starttime", "asc");
        $this->dbtrip->order_by("georeport_vehicle_no", "asc");
        $this->dbtrip->where("georeport_vehicle_user_id", $user_id);
        if ($company != 'all') {
            $this->dbtrip->where("georeport_company_id", $company);
        }
        if ($model != 'all') {
            $this->dbtrip->where("georeport_model", $model);
        }
        if ($vehicle != '0') {
            $this->dbtrip->where("georeport_vehicle_device", $vehicle);
        }
        $this->dbtrip->where("georeport_vehicle_user_id <>", 72150933); //jika pilih all bukan mobil trial
        // } else {
        // 	$this->dbtrip->where("ritase_report_vehicle_device", $vehicle);
        // }

        $this->dbtrip->where("georeport_starttime >=", $sdate);
        $this->dbtrip->where("georeport_starttime <=", $edate);
        // $this->dbtrip->where("ritase_report_type", $reporttype); //data fix (default) = 0
        $q = $this->dbtrip->get($dbtable);
        $nr = $q->num_rows();
        if ($nr > 0) {
            $rows = $q->result_array();


            $this->params['data'] = $rows;
            $this->params['periode'] = $datein;
            // $callback['data'] = $check;
            // $this->params['total_data'] = $nr;
            $html                    = $this->load->view('newdashboard/geofence/v_georeport_result', $this->params, true);
            $callback['html'] = $html;
        } else {
            $callback['error'] = true;
            $callback['message'] = "Data empty.";
            echo json_encode($callback);
            return;
        }
        $callback['input'] = array(
            'company' => $company,
            'date_s' => $sdate,
            'date_e' => $edate
        );
        echo json_encode($callback);
    }
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
