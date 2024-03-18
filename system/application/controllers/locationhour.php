<?php
include "base.php";

class Locationhour extends Base
{

    function __construct()
    {
        parent::Base();
        // $this->load->helper('common_helper');
        // $this->load->helper('email');
        // $this->load->library('email');
        $this->load->model("dashboardmodel");
        // $this->load->model("m_production");
        // $this->load->model("log_model");
        // $this->load->helper('common');
    }

    // USER FUNCTION START
    function index()
    {
        if (!isset($this->sess->user_type)) {
            redirect(base_url());
        }

        $privilegecode = $this->sess->user_id_role;

        $rows_company                   = $this->get_company();
        $this->params["rcompany"]       = $rows_company;
        $this->params['code_view_menu'] = "report";

        $this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
        $this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
        $this->params["onload"]         = 1;
        if ($privilegecode == 1) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/report/v_location_hour', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
        } elseif ($privilegecode == 2) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/report/v_location_hour', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
        } elseif ($privilegecode == 3) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/report/v_location_hour', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
        } elseif ($privilegecode == 4) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/report/v_location_hour', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
        } elseif ($privilegecode == 5) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/report/v_location_hour', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
        } else {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/report/v_location_hour', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
        }
    }
	
	//add filter group + hour + report per month
	function search()
    {
        $company = $this->input->post('company');
        $datein = $this->input->post('date');
        $shift = $this->input->post('shift');
		$group = $this->input->post('group');
		$hour = $this->input->post('hour');
        $date = date("Y-m-d", strtotime($datein));
        $lastdate = date("Y-m-t", strtotime($datein));
        $year = date("Y", strtotime($datein));
        $month = date("m", strtotime($datein));
        $day = date('d', strtotime($datein));
        $day++;
        $jmlday = strlen($day);
        if ($jmlday == 1) {
            $day = "0" . $day;
        }
        $next = $year . "-" . $month . "-" . $day;

        if ($next > $lastdate) {
            if ($month == 12) {
                $y = $year + 1;
                $next = $y . "-01-01";
            } else {
                $m = $month + 1;
                $jmlmonth = strlen($m);
                if ($jmlmonth == 1) {
                    $m = "0" . $m;
                }
                $next = $year . "-" . $m . "-01";
            }
        }
        $arraydate = array("date" => $date, "next date" => $next, "last date" => $lastdate);
        $input = array(
            "company" => $company,
			"group" => $group,
            "date" => $arraydate,
            "shift" => $shift
        );
		
		if ($hour != "all") {
			$shourfix = date("H:i:s", strtotime($hour));
		}
		
		$days_report = date("d", strtotime($datein));
		$month_report = date("F", strtotime($datein));
		$year_report = date("Y", strtotime($datein));
		$before_status = 0;
		$year_before = date('Y',strtotime('-1 year',strtotime($year_report)));
		
		$report = "location_hour_";
		$report_ritase = "location_hour_";
		
		switch ($month_report)
		{
			case "January":
            $dbtable = $report."januari_".$year_report;
			$dbtable_ritase = $report_ritase."januari_".$year_report;
			$dbtable_before = $report."desember_".$year_before;
			break;
			case "February":
            $dbtable = $report."februari_".$year_report;
			$dbtable_ritase = $report_ritase."februari_".$year_report;
			$dbtable_before = $report."januari_".$year_report;
			break;
			case "March":
            $dbtable = $report."maret_".$year_report;
			$dbtable_ritase = $report_ritase."maret_".$year_report;
			$dbtable_before = $report."februari_".$year_report;
			break;
			case "April":
            $dbtable = $report."april_".$year_report;
			$dbtable_ritase = $report_ritase."april_".$year_report;
			$dbtable_before = $report."maret_".$year_report;
			break;
			case "May":
            $dbtable = $report."mei_".$year_report;
			$dbtable_ritase = $report_ritase."mei_".$year_report;
			$dbtable_before = $report."april_".$year_report;
			break;
			case "June":
            $dbtable = $report."juni_".$year_report;
			$dbtable_ritase = $report_ritase."juni_".$year_report;
			$dbtable_before = $report."mei_".$year_report;
			break;
			case "July":
            $dbtable = $report."juli_".$year_report;
			$dbtable_ritase = $report_ritase."juli_".$year_report;
			$dbtable_before = $report."juni_".$year_report;
			break;
			case "August":
            $dbtable = $report."agustus_".$year_report;
			$dbtable_ritase = $report_ritase."agustus_".$year_report;
			$dbtable_before = $report."juli_".$year_report;
			break;
			case "September":
            $dbtable = $report."september_".$year_report;
			$dbtable_ritase = $report_ritase."september_".$year_report;
			$dbtable_before = $report."agustus_".$year_report;
			break;
			case "October":
            $dbtable = $report."oktober_".$year_report;
			$dbtable_ritase = $report_ritase."oktober_".$year_report;
			$dbtable_before = $report."september_".$year_report;
			break;
			case "November":
            $dbtable = $report."november_".$year_report;
			$dbtable_ritase = $report_ritase."november_".$year_report;
			$dbtable_before = $report."oktober_".$year_report;
			break;
			case "December":
            $dbtable = $report."desember_".$year_report;
			$dbtable_ritase = $report_ritase."desember_".$year_report;
			$dbtable_before = $report."november_".$year_report;
			break;
		}

        //$this->dbts = $this->load->database("webtracking_ts", true);
		$this->dbts = $this->load->database("tensor_report", true);
        if ($shift == 1) 
		{
            $this->dbts->select("location_report_vehicle_no,location_report_company_name,location_report_gps_date,location_report_gps_hour,location_report_location,location_report_group,location_report_coordinate,location_report_latitude, location_report_longitude");
            $shift = array("06:00:00", "07:00:00", "08:00:00", "09:00:00", "10:00:00", "11:00:00", "12:00:00", "13:00:00", "14:00:00", "15:00:00", "16:00:00", "17:00:00");
            $this->dbts->where("location_report_gps_date", $date);
            if ($company != 0) {
                $this->dbts->where("location_report_vehicle_company", $company);
            }
			if ($group != "all") {
                $this->dbts->where("location_report_group", $group);
            }
          
			if ($hour != "all") {
				$this->dbts->where("location_report_gps_hour", $shourfix);
			}else {
				$this->dbts->where_in("location_report_gps_hour", $shift);
			}
			
            $this->dbts->order_by("location_report_gps_hour", "asc");
            $this->dbts->order_by("location_report_company_name", "asc");
            $result = $this->dbts->get($dbtable);
            $data = $result->result_array();
            $nr = $result->num_rows();
        } else if ($shift == 2) {
            $this->dbts->select("location_report_vehicle_no,location_report_company_name,location_report_gps_date,location_report_gps_hour,location_report_location,location_report_group,location_report_coordinate,location_report_latitude, location_report_longitude");
            $shift1 = array("18:00:00", "19:00:00", "20:00:00", "21:00:00", "22:00:00", "23:00:00");
            $shift2 = array("00:00:00", "01:00:00", "02:00:00", "03:00:00", "04:00:00", "05:00:00");
            $this->dbts->where("location_report_gps_date", $date);
            if ($company != 0) {
                $this->dbts->where("location_report_vehicle_company", $company);
            }
			if ($group != "all") {
                $this->dbts->where("location_report_group", $group);
            }
			
			if ($hour != "all") {
				$this->dbts->where("location_report_gps_hour", $shourfix);
			}else {
				 $this->dbts->where_in("location_report_gps_hour", $shift1);
			}
			
            $this->dbts->order_by("location_report_gps_hour", "asc");
            $this->dbts->order_by("location_report_company_name", "asc");
            $result = $this->dbts->get($dbtable);
            $data1 = $result->result_array();
            $nr1 = $result->num_rows();
            $this->dbts->distinct();
            $this->dbts->select("location_report_vehicle_no,location_report_company_name,location_report_gps_date,location_report_gps_hour,location_report_location,location_report_group,location_report_coordinate,location_report_latitude, location_report_longitude");
            $this->dbts->where("location_report_gps_date", $next);
            if ($company != 0) {
                $this->dbts->where("location_report_vehicle_company", $company);
            }
			if ($group != "all") {
                $this->dbts->where("location_report_group", $group);
            }
            
			if ($hour != "all") {
				$this->dbts->where("location_report_gps_hour", $shourfix);
			}else {
				$this->dbts->where_in("location_report_gps_hour", $shift2);
			}
			
            $this->dbts->order_by("location_report_gps_hour", "asc");
            $this->dbts->order_by("location_report_company_name", "asc");
            $result = $this->dbts->get($dbtable);
            $data2 = $result->result_array();
            $nr2 = $result->num_rows();
            $data = array_merge($data1, $data2);
            $nr = $nr1 +  $nr2;
        } else {
            $this->dbts->select("location_report_vehicle_no,location_report_company_name,location_report_gps_date,location_report_gps_hour,location_report_location,location_report_group,location_report_coordinate,location_report_latitude, location_report_longitude");
            $shift1 = array("06:00:00", "07:00:00", "08:00:00", "09:00:00", "10:00:00", "11:00:00", "12:00:00", "13:00:00", "14:00:00", "15:00:00", "16:00:00", "17:00:00", "18:00:00", "19:00:00", "20:00:00", "21:00:00", "22:00:00", "23:00:00");
            $shift2 = array("00:00:00", "01:00:00", "02:00:00", "03:00:00", "04:00:00", "05:00:00");
            $this->dbts->where("location_report_gps_date", $date);
            if ($company != 0) {
                $this->dbts->where("location_report_vehicle_company", $company);
            }
			if ($group != "all") {
                $this->dbts->where("location_report_group", $group);
            }
            
			if ($hour != "all") {
				$this->dbts->where("location_report_gps_hour", $shourfix);
			}else {
				$this->dbts->where_in("location_report_gps_hour", $shift1);
			}
			
            $this->dbts->order_by("location_report_gps_hour", "asc");
            $this->dbts->order_by("location_report_company_name", "asc");
            $result = $this->dbts->get($dbtable);
            $data1 = $result->result_array();
            $nr1 = $result->num_rows();
            $this->dbts->distinct();
            $this->dbts->select("location_report_vehicle_no,location_report_company_name,location_report_gps_date,location_report_gps_hour,location_report_location,location_report_group,location_report_coordinate,location_report_latitude, location_report_longitude");
            $this->dbts->where("location_report_gps_date", $next);
            if ($company != 0) {
                $this->dbts->where("location_report_vehicle_company", $company);
            }
			if ($group != "all") {
                $this->dbts->where("location_report_group", $group);
            }
           
			if ($hour != "all") {
				$this->dbts->where("location_report_gps_hour", $shourfix);
			}else {
				$this->dbts->where_in("location_report_gps_hour", $shift2);
			}
			
            $this->dbts->order_by("location_report_gps_hour", "asc");
            $this->dbts->order_by("location_report_company_name", "asc");
            $result = $this->dbts->get($dbtable);
            $data2 = $result->result_array();
            $nr2 = $result->num_rows();
            $data = array_merge($data1, $data2);
            $nr = $nr1 +  $nr2;
        }


        if ($nr > 0) {
            echo json_encode(array("code" => 200, "error" => false, "msg" => "success", "input" => $input, "data" => $data, "total" => $nr));
        } else {
            echo json_encode(array("code" => 200, "error" => true, "msg" => "Data Empty"));
        }
    }

    function search_bk()
    {
        $company = $this->input->post('company');
        $datein = $this->input->post('date');
        $shift = $this->input->post('shift');
        $date = date("Y-m-d", strtotime($datein));
        $lastdate = date("Y-m-t", strtotime($datein));
        $year = date("Y", strtotime($datein));
        $month = date("m", strtotime($datein));
        $day = date('d', strtotime($datein));
        $day++;
        $jmlday = strlen($day);
        if ($jmlday == 1) {
            $day = "0" . $day;
        }
        $next = $year . "-" . $month . "-" . $day;

        if ($next > $lastdate) {
            if ($month == 12) {
                $y = $year + 1;
                $next = $y . "-01-01";
            } else {
                $m = $month + 1;
                $jmlmonth = strlen($m);
                if ($jmlmonth == 1) {
                    $m = "0" . $m;
                }
                $next = $year . "-" . $m . "-01";
            }
        }
        $arraydate = array("date" => $date, "next date" => $next, "last date" => $lastdate);
        $input = array(
            "company" => $company,
            "date" => $arraydate,
            "shift" => $shift
        );

        $this->dbts = $this->load->database("webtracking_ts", true);
        if ($shift == 1) {
            $this->dbts->select("location_report_vehicle_no,location_report_company_name,location_report_gps_date,location_report_gps_hour,location_report_location,location_report_group,location_report_coordinate,location_report_latitude, location_report_longitude");
            $shift = array("06:00:00", "07:00:00", "08:00:00", "09:00:00", "10:00:00", "11:00:00", "12:00:00", "13:00:00", "14:00:00", "15:00:00", "16:00:00", "17:00:00");
            $this->dbts->where("location_report_gps_date", $date);
            if ($company != 0) {
                $this->dbts->where("location_report_vehicle_company", $company);
            }
            $this->dbts->where_in("location_report_gps_hour", $shift);
            $this->dbts->order_by("location_report_gps_hour", "asc");
            $this->dbts->order_by("location_report_company_name", "asc");
            $result = $this->dbts->get("ts_location_hour");
            $data = $result->result_array();
            $nr = $result->num_rows();
        } else if ($shift == 2) {
            $this->dbts->select("location_report_vehicle_no,location_report_company_name,location_report_gps_date,location_report_gps_hour,location_report_location,location_report_group,location_report_coordinate,location_report_latitude, location_report_longitude");
            $shift1 = array("18:00:00", "19:00:00", "20:00:00", "21:00:00", "22:00:00", "23:00:00");
            $shift2 = array("00:00:00", "01:00:00", "02:00:00", "03:00:00", "04:00:00", "05:00:00");
            $this->dbts->where("location_report_gps_date", $date);
            if ($company != 0) {
                $this->dbts->where("location_report_vehicle_company", $company);
            }
            $this->dbts->where_in("location_report_gps_hour", $shift1);
            $this->dbts->order_by("location_report_gps_hour", "asc");
            $this->dbts->order_by("location_report_company_name", "asc");
            $result = $this->dbts->get("ts_location_hour");
            $data1 = $result->result_array();
            $nr1 = $result->num_rows();
            $this->dbts->distinct();
            $this->dbts->select("location_report_vehicle_no,location_report_company_name,location_report_gps_date,location_report_gps_hour,location_report_location,location_report_group,location_report_coordinate,location_report_latitude, location_report_longitude");
            $this->dbts->where("location_report_gps_date", $next);
            if ($company != 0) {
                $this->dbts->where("location_report_vehicle_company", $company);
            }
            $this->dbts->where_in("location_report_gps_hour", $shift2);
            $this->dbts->order_by("location_report_gps_hour", "asc");
            $this->dbts->order_by("location_report_company_name", "asc");
            $result = $this->dbts->get("ts_location_hour");
            $data2 = $result->result_array();
            $nr2 = $result->num_rows();
            $data = array_merge($data1, $data2);
            $nr = $nr1 +  $nr2;
        } else {
            $this->dbts->select("location_report_vehicle_no,location_report_company_name,location_report_gps_date,location_report_gps_hour,location_report_location,location_report_group,location_report_coordinate,location_report_latitude, location_report_longitude");
            $shift1 = array("06:00:00", "07:00:00", "08:00:00", "09:00:00", "10:00:00", "11:00:00", "12:00:00", "13:00:00", "14:00:00", "15:00:00", "16:00:00", "17:00:00", "18:00:00", "19:00:00", "20:00:00", "21:00:00", "22:00:00", "23:00:00");
            $shift2 = array("00:00:00", "01:00:00", "02:00:00", "03:00:00", "04:00:00", "05:00:00");
            $this->dbts->where("location_report_gps_date", $date);
            if ($company != 0) {
                $this->dbts->where("location_report_vehicle_company", $company);
            }
            $this->dbts->where_in("location_report_gps_hour", $shift1);
            $this->dbts->order_by("location_report_gps_hour", "asc");
            $this->dbts->order_by("location_report_company_name", "asc");
            $result = $this->dbts->get("ts_location_hour");
            $data1 = $result->result_array();
            $nr1 = $result->num_rows();
            $this->dbts->distinct();
            $this->dbts->select("location_report_vehicle_no,location_report_company_name,location_report_gps_date,location_report_gps_hour,location_report_location,location_report_group,location_report_coordinate,location_report_latitude, location_report_longitude");
            $this->dbts->where("location_report_gps_date", $next);
            if ($company != 0) {
                $this->dbts->where("location_report_vehicle_company", $company);
            }
            $this->dbts->where_in("location_report_gps_hour", $shift2);
            $this->dbts->order_by("location_report_gps_hour", "asc");
            $this->dbts->order_by("location_report_company_name", "asc");
            $result = $this->dbts->get("ts_location_hour");
            $data2 = $result->result_array();
            $nr2 = $result->num_rows();
            $data = array_merge($data1, $data2);
            $nr = $nr1 +  $nr2;
        }


        if ($nr > 0) {
            echo json_encode(array("code" => 200, "error" => false, "msg" => "success", "input" => $input, "data" => $data, "total" => $nr));
        } else {
            echo json_encode(array("code" => 200, "error" => true, "msg" => "Data Empty"));
        }
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
        } elseif ($privilegecode == 4) {
            $this->db->where("company_created_by", 4408);
        } elseif ($privilegecode == 5) {
            $this->db->where("company_created_by", 4408);
        }

        $this->db->where("company_flag", 0);
        $qd = $this->db->get("company");
        $rd = $qd->result();

        return $rd;
    }

    function get_company_pjo()
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
