<?php
include "base.php";

class Exca extends Base
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
	
	function index()
    {
        $this->hour();
    }

    // USER FUNCTION START
    function hour()
    {
        if (!isset($this->sess->user_type)) {
            redirect(base_url());
        }

        $privilegecode = $this->sess->user_id_role;

        $rows_company                   = $this->get_company();
        $this->params["rcompany"]       = $rows_company;

        $this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
        $this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
        $this->params["onload"]         = 1;
        if ($privilegecode == 1) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/excahour/v_exca_hour', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
        } elseif ($privilegecode == 2) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/excahour/v_exca_hour', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
        } elseif ($privilegecode == 3) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/excahour/v_exca_hour', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
        } elseif ($privilegecode == 4) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/excahour/v_exca_hour', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
        } elseif ($privilegecode == 5) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/excahour/v_exca_hour', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
        } elseif ($privilegecode == 7) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminitws', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/excahour/v_exca_hour', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_adminitws", $this->params);
        }else {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/excahour/v_exca_hour', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
        }
    }

    function search()
    {
        $company = $this->input->post('company');
        $datein = $this->input->post('date');
        $shift = $this->input->post('shift');
		$shift_hour = $this->input->post('shift_hour'); //print_r($shift_hour);exit();
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

		$exca_list = array("AMM E514","AMM E515","AMM E516");
		//$port_list = array("PORT BIB","PORT BIR","PORT TIA");

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
            "shift" => $shift,
			"shift_hour" => $shift_hour
        );

        $this->dbts = $this->load->database("webtracking_ts", true);
        if ($shift == 1) {
            //$this->dbts->select("radius_report_host_vehicle_no,radius_report_host_vehicle_company_name,radius_report_date,radius_report_hour,radius_report_start_coordinate,radius_report_guest_vehicle_no,radius_report_duration,radius_report_distance,radius_report_start_time");
            $shift = array("06:00:00", "07:00:00", "08:00:00", "09:00:00", "10:00:00", "11:00:00", "12:00:00", "13:00:00", "14:00:00", "15:00:00", "16:00:00", "17:00:00");
            $this->dbts->where("radius_report_date", $date);
			//$this->dbts->where("radius_report_duration_sec >", 0);
			$this->dbts->where_in("radius_report_host_vehicle_no", $exca_list);

            if ($company != 0) {
                $this->dbts->where("radius_report_host_vehicle_company", $company);
            }
			
			if ($shift_hour != "all") {
                $this->dbts->where("radius_report_hour", $shift_hour);
            }else{
				$this->dbts->where_in("radius_report_hour", $shift);
			}
          
            $this->dbts->order_by("radius_report_hour", "asc");
            $this->dbts->order_by("radius_report_host_vehicle_company_name", "asc");
            $result = $this->dbts->get("ts_radius_hour");
            $data = $result->result_array();
            $nr = $result->num_rows();
        } else if ($shift == 2) {
            //$this->dbts->select("radius_report_host_vehicle_no,radius_report_host_vehicle_company_name,radius_report_date,radius_report_hour,radius_report_start_coordinate,radius_report_guest_vehicle_no,radius_report_duration,radius_report_distance,radius_report_start_time");
			$shift1 = array("18:00:00", "19:00:00", "20:00:00", "21:00:00", "22:00:00", "23:00:00");
            $shift2 = array("00:00:00", "01:00:00", "02:00:00", "03:00:00", "04:00:00", "05:00:00");
            $this->dbts->where("radius_report_date", $date);
			//$this->dbts->where("radius_report_duration_sec >", 0);
			$this->dbts->where_in("radius_report_host_vehicle_no", $exca_list);
            if ($company != 0) {
                $this->dbts->where("radius_report_host_vehicle_company", $company);
            }
           // $this->dbts->where_in("radius_report_hour", $shift1);
			if ($shift_hour != "all") {
                $this->dbts->where("radius_report_hour", $shift_hour);
            }else{
				$this->dbts->where_in("radius_report_hour", $shift1);
			}
            $this->dbts->order_by("radius_report_hour", "asc");
            $this->dbts->order_by("radius_report_host_vehicle_company_name", "asc");
            $result = $this->dbts->get("ts_radius_hour");
            $data1 = $result->result_array();
            $nr1 = $result->num_rows();
            $this->dbts->distinct();
            //$this->dbts->select("radius_report_host_vehicle_no,radius_report_host_vehicle_company_name,radius_report_date,radius_report_hour,radius_report_start_coordinate,radius_report_guest_vehicle_no,radius_report_duration,radius_report_distance,radius_report_start_time");
            $this->dbts->where("radius_report_date", $next);
            if ($company != 0) {
                $this->dbts->where("radius_report_host_vehicle_company", $company);
            }
			
			if ($shift_hour != "all") {
                $this->dbts->where("radius_report_hour", $shift_hour);
            }else{
				  $this->dbts->where_in("radius_report_hour", $shift2);
			}
			
            $this->dbts->order_by("radius_report_hour", "asc");
            $this->dbts->order_by("radius_report_host_vehicle_company_name", "asc");
            $result = $this->dbts->get("ts_radius_hour");
            $data2 = $result->result_array();
            $nr2 = $result->num_rows();
            $data = array_merge($data1, $data2);
            $nr = $nr1 +  $nr2;
        } else {
            //$this->dbts->select("radius_report_host_vehicle_no,radius_report_host_vehicle_company_name,radius_report_date,radius_report_hour,radius_report_start_coordinate,radius_report_guest_vehicle_no,radius_report_duration,radius_report_distance,radius_report_start_time");
            $shift1 = array("06:00:00", "07:00:00", "08:00:00", "09:00:00", "10:00:00", "11:00:00", "12:00:00", "13:00:00", "14:00:00", "15:00:00", "16:00:00", "17:00:00", "18:00:00", "19:00:00", "20:00:00", "21:00:00", "22:00:00", "23:00:00");
            $shift2 = array("00:00:00", "01:00:00", "02:00:00", "03:00:00", "04:00:00", "05:00:00");
            $this->dbts->where("radius_report_date", $date);
			//$this->dbts->where("radius_report_duration_sec >", 0);
			$this->dbts->where_in("radius_report_host_vehicle_no", $exca_list);
            if ($company != 0) {
                $this->dbts->where("radius_report_host_vehicle_company", $company);
            }
			
			if ($shift_hour != "all") {
                $this->dbts->where("radius_report_hour", $shift_hour);
            }else{
				 $this->dbts->where_in("radius_report_hour", $shift1);
			}
			
          
			
            $this->dbts->order_by("radius_report_hour", "asc");
            $this->dbts->order_by("radius_report_host_vehicle_company_name", "asc");
            $result = $this->dbts->get("ts_radius_hour");
            $data1 = $result->result_array();
            $nr1 = $result->num_rows();
            $this->dbts->distinct();
            //$this->dbts->select("radius_report_host_vehicle_no,radius_report_host_vehicle_company_name,radius_report_date,radius_report_hour,radius_report_start_coordinate,radius_report_guest_vehicle_no,radius_report_duration,radius_report_distance,radius_report_start_time");
            $this->dbts->where("radius_report_date", $next);
            if ($company != 0) {
                $this->dbts->where("radius_report_host_vehicle_company", $company);
            }
            
			if ($shift_hour != "all") {
                $this->dbts->where("radius_report_hour", $shift_hour);
            }else{
				$this->dbts->where_in("radius_report_hour", $shift2);
			}
			
            $this->dbts->order_by("radius_report_hour", "asc");
            $this->dbts->order_by("radius_report_host_vehicle_company_name", "asc");
            $result = $this->dbts->get("ts_radius_hour");
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

	function board()
    {
        if (!isset($this->sess->user_type)) {
            redirect(base_url());
        }

        $privilegecode = $this->sess->user_id_role;

        $rows_company                   = $this->get_company();

        $this->params["rcompany"]       = $rows_company;
        $this->params["runit"]      = $this->get_allExca();
        $this->params['code_view_menu'] = "dashboard";


        $this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
        $this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);
        $this->params["onload"]         = 1;
        if ($privilegecode == 1) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/excahour/v_exca_board_hour', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
        } elseif ($privilegecode == 2) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/excahour/v_exca_board_hour', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
        } elseif ($privilegecode == 3) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/excahour/v_exca_board_hour', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
        } elseif ($privilegecode == 4) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/excahour/v_exca_board_hour', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
        } elseif ($privilegecode == 5) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/excahour/v_exca_board_hour', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
        } elseif ($privilegecode == 6) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/excahour/v_exca_board_hour', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
        }elseif ($privilegecode == 7) {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminitws', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/excahour/v_exca_board_hour', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_adminitws", $this->params);
        } else {
            $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
            $this->params["content"]        = $this->load->view('newdashboard/excahour/v_exca_board_hour', $this->params, true);
            $this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
        }
    }

    function search_board()
    {
        $company = $this->input->post('company'); //port
        $location = $this->input->post('location');
		$exca = $this->input->post('exca');
        $datein = $this->input->post('date');
        $shift = $this->input->post('shift');
        $date = date("Y-m-d", strtotime($datein));
        if (date("Y-m-d") < $date) {
            echo json_encode(array("code" => 200, "error" => true, "msg" => "Data Empty", "total" => 0, "data" => array()));
            exit();
        }
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

        $shift1 = array("06", "07", "08", "09", "10", "11", "12", "13", "14", "15", "16", "17");
        $shift21 = array("18", "19", "20", "21", "22", "23");
        $shift22 = array("00", "01", "02", "03", "04", "05");
        $allshift1 = array("06", "07", "08", "09", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23");
        $allshift2 = array("00", "01", "02", "03", "04", "05");



        //get vehicle info
        $this->db->select("vehicle_name,vehicle_no,company_name");
        $this->db->order_by("company_name", "asc");
        $this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_typeunit", 1);
        if ($company != 0) {
            $this->db->where("vehicle_company", $company);
        }
		$this->db->where("vehicle_user_id", 4408);
        $this->db->join("company", "vehicle_company = company_id", "left");
        $qd = $this->db->get("vehicle");
        $total_unit = $qd->num_rows();
        $rd = $qd->result();
        $total_unit_percontractor = array();
        if ($company == 0) {
            for ($x = 0; $x < $total_unit; $x++) {
                if ($rd[$x]->company_name != null) {
                    if (!isset($total_unit_percontractor[$rd[$x]->company_name])) {
                        $total_unit_percontractor[$rd[$x]->company_name] = 1;
                    } else {
                        $jml = (int)$total_unit_percontractor[$rd[$x]->company_name] + 1;
                        $total_unit_percontractor[$rd[$x]->company_name] = $jml;
                    }
                }
            }
        }
        //end get vehicle info
		
        //exca selected 
        $this->dbts = $this->load->database("webtracking_ts", true);
		$this->dbts->order_by("radius_report_start_time", "asc");
		$this->dbts->where("radius_report_host_vehicle_company_name <>", "");
		//temp not available
		if ($location == "0") {
           $this->dbts->where_in("radius_report_start_geofence", array("ROM A1","ROM A2"));
        } else {
            $exp = explode(" ", $location);
            if ($exp[0] == "ROM") {
                //lokasi ROM
                $this->dbts->where("radius_report_start_geofence", $location);
            } else if ($exp[0] == "PORT") {
                //lokasi PORT
                $this->dbts->where("radius_report_start_geofence", $location);
                //$this->dbts->where("radius_report_start_geofence LIKE ", "%" . $exp[1] . "%");
            }
        }
		
		//select by exca
		if( $exca != "0"){
           
            $this->dbts->where("radius_report_host_vehicle_no", $exca);
        }

        if ($shift == 1) {
            for ($s = 0; $s < count($shift1); $s++) {
                $shift1[$s] .= ":00:00";
            }
            $this->dbts->select("radius_report_host_vehicle_no,radius_report_host_vehicle_company_name,radius_report_date,radius_report_hour,radius_report_start_coordinate,radius_report_guest_vehicle_no,radius_report_duration,radius_report_distance,radius_report_start_geofence,radius_report_start_time");
            $this->dbts->where("radius_report_date", $date);
            if ($company != 0) {
                $this->dbts->where("radius_report_host_vehicle_company", $company);
            }
            $this->dbts->where_in("radius_report_hour", $shift1);
            $this->dbts->order_by("radius_report_hour", "asc");
            $this->dbts->order_by("radius_report_host_vehicle_company_name", "asc");
            $result = $this->dbts->get("ts_radius_hour");
            $data = $result->result_array();
            $nr = $result->num_rows();
        } else if ($shift == 2) {
            for ($s = 0; $s < count($shift21); $s++) {
                $shift21[$s] .= ":00:00";
            }
            for ($s = 0; $s < count($shift22); $s++) {
                $shift22[$s] .= ":00:00";
            }
            $this->dbts->select("radius_report_host_vehicle_no,radius_report_host_vehicle_company_name,radius_report_date,radius_report_hour,radius_report_start_coordinate,radius_report_guest_vehicle_no,radius_report_duration,radius_report_distance,radius_report_start_geofence,radius_report_start_time");
			$this->dbts->where("radius_report_host_vehicle_company_name <>", "");           
		    $this->dbts->where("radius_report_date", $date);
            if ($company != 0) {
                $this->dbts->where("ritase_report_vehicle_company", $company);
            }
            $this->dbts->where_in("radius_report_host_vehicle_company", $shift21);
            $this->dbts->order_by("radius_report_hour", "asc");
            $this->dbts->order_by("radius_report_host_vehicle_company_name", "asc");
            $result = $this->dbts->get("ts_radius_hour");
            $data1 = $result->result_array();
            $nr1 = $result->num_rows();

            $this->dbts->distinct();
			$this->dbts->order_by("radius_report_start_geofence", "asc");
			$this->dbts->where("radius_report_host_vehicle_company_name <>", ""); 
			 if ($location == "0") {
			$this->dbts->where_in("radius_report_start_geofence", array("ROM A1", "ROM A2"));
			} else {
				$exp = explode(" ", $location);
				if ($exp[0] == "ROM") {
					//lokasi ROM
					$this->dbts->where("radius_report_start_geofence", $location);
				} else if ($exp[0] == "PORT") {
					//lokasi PORT
					$this->dbts->where("radius_report_start_geofence", $location);
					//$this->dbts->where("radius_report_start_geofence LIKE ", "%" . $exp[1] . "%");
				}
			}
			
			if($exca != "0"){
				
				$this->dbts->where("radius_report_host_vehicle_no", $exca);
			}
            /*if ($location == "STREET.0") {
                $this->dbts->where("ritase_report_group", "STREET");
                $this->dbts->where("ritase_report_jalur", "kosongan");
            } else if ($location == "STREET.1") {
                $this->dbts->where("ritase_report_group", "STREET");
                $this->dbts->where("ritase_report_jalur", "muatan");
            } else if ($location == "0") {
                $this->dbts->where_in("ritase_report_group", array("STREET", "ROM", "PORT"));
            } else {
                $exp = explode(" ", $location);
                if ($exp[0] == "ROM") {
                    //lokasi ROM
                    $this->dbts->where("ritase_report_location", $location);
                } else if ($exp[0] == "PORT") {
                    //lokasi PORT
                    $this->dbts->where("ritase_report_group", "PORT");
                    $this->dbts->where("ritase_report_location LIKE ", "%" . $exp[1] . "%");
                }
            } */


            $this->dbts->select("radius_report_host_vehicle_no,radius_report_host_vehicle_company_name,radius_report_date,radius_report_hour,radius_report_start_coordinate,radius_report_guest_vehicle_no,radius_report_duration,radius_report_distance,radius_report_start_geofence,radius_report_start_time");
            $this->dbts->where("radius_report_host_vehicle_company_name <>", ""); 
			$this->dbts->where("radius_report_date", $next);
            if ($company != 0) {
                $this->dbts->where("radius_report_host_vehicle_company", $company);
            }
            $this->dbts->where_in("radius_report_hour", $shift22);
            $this->dbts->order_by("radius_report_hour", "asc");
            $this->dbts->order_by("radius_report_host_vehicle_company_name", "asc");
            $result = $this->dbts->get("ts_radius_hour");
            $data2 = $result->result_array();
            $nr2 = $result->num_rows();
            $data = array_merge($data1, $data2);
            $nr = $nr1 +  $nr2;
        } else {

            for ($s = 0; $s < count($allshift1); $s++) {
                $allshift1[$s] .= ":00:00";
            }
            for ($s = 0; $s < count($allshift2); $s++) {
                $allshift2[$s] .= ":00:00";
            }
            $this->dbts->select("radius_report_host_vehicle_no,radius_report_host_vehicle_company_name,radius_report_date,radius_report_hour,radius_report_start_coordinate,radius_report_guest_vehicle_no,radius_report_duration,radius_report_distance,radius_report_start_geofence,radius_report_start_time");
			$this->dbts->where("radius_report_host_vehicle_company_name <>", ""); 
			$this->dbts->where("radius_report_date", $date);
            if ($company != 0) {
                $this->dbts->where("radius_report_host_vehicle_company", $company);
            }
            $this->dbts->where_in("radius_report_hour", $allshift1);
            $this->dbts->order_by("radius_report_hour", "asc");
            $this->dbts->order_by("radius_report_host_vehicle_company_name", "asc");
            $result = $this->dbts->get("ts_radius_hour");
            $data1 = $result->result_array();
            $nr1 = $result->num_rows();
            $this->dbts->distinct();
            $this->dbts->order_by("radius_report_start_geofence", "asc");
			
			if($exca != "0"){
				
				$this->dbts->where("radius_report_host_vehicle_no", $exca);
			}
			
			
			if ($location == "0") {
			$this->dbts->where_in("radius_report_start_geofence", array("ROM A1", "ROM A2"));
			} else {
				$exp = explode(" ", $location);
				if ($exp[0] == "ROM") {
					//lokasi ROM
					$this->dbts->where("radius_report_start_geofence", $location);
				} else if ($exp[0] == "PORT") {
					//lokasi PORT
					$this->dbts->where("radius_report_start_geofence", $location);
					//$this->dbts->where("radius_report_start_geofence LIKE ", "%" . $exp[1] . "%");
				}
			}

            /* if ($location == "STREET.0") {
                $this->dbts->where("ritase_report_group", "STREET");
                $this->dbts->where("ritase_report_jalur", "kosongan");
            } else if ($location == "STREET.1") {
                $this->dbts->where("ritase_report_group", "STREET");
                $this->dbts->where("ritase_report_jalur", "muatan");
            } else if ($location == "0") {
                $this->dbts->where_in("ritase_report_group", array("STREET", "ROM", "PORT"));
            } else {
                $exp = explode(" ", $location);
                if ($exp[0] == "ROM") {
                    //lokasi ROM
                    $this->dbts->where("ritase_report_location", $location);
                } else if ($exp[0] == "PORT") {
                    //lokasi PORT
                    $this->dbts->where("ritase_report_group", "PORT");
                    $this->dbts->where("ritase_report_location LIKE ", "%" . $exp[1] . "%");
                }
            } */


			$this->dbts->select("radius_report_host_vehicle_no,radius_report_host_vehicle_company_name,radius_report_date,radius_report_hour,radius_report_start_coordinate,radius_report_guest_vehicle_no,radius_report_duration,radius_report_distance,radius_report_start_geofence,radius_report_start_time");
			$this->dbts->where("radius_report_host_vehicle_company_name <>", ""); 
			$this->dbts->where("radius_report_date", $next);
            if ($company != 0) {
                $this->dbts->where("radius_report_host_vehicle_company", $company);
            }
            $this->dbts->where_in("radius_report_hour", $allshift2);
            $this->dbts->order_by("radius_report_hour", "asc");
            $this->dbts->order_by("radius_report_host_vehicle_company_name", "asc");
            $result = $this->dbts->get("ts_radius_hour");
            $data2 = $result->result_array();
            $nr2 = $result->num_rows();
            $data = array_merge($data1, $data2);
            $nr = $nr1 +  $nr2;
        }

        if ($nr > 0) {
            $data_fix = array();
            $dhour = array();
            $dcompany = array();
            $dlocation = array();
            $c = array();
            $l = array();
            for ($i = 0; $i < $nr; $i++) {
                $exp = explode(":", $data[$i]['radius_report_hour']);

                if (!isset($hour[$exp[0]])) {
                    $hour[$exp[0]] = 1;
                    $dhour[] = $exp[0];
                }
                if (!isset($c[$data[$i]['radius_report_host_vehicle_company_name']])) {

                    $c[$data[$i]['radius_report_host_vehicle_company_name']] = $data[$i]['radius_report_host_vehicle_company_name'];
                    $dcompany[] = $c[$data[$i]['radius_report_host_vehicle_company_name']];
                }
				if (!isset($l[$data[$i]['radius_report_start_geofence']])) {

                    $l[$data[$i]['radius_report_start_geofence']] = $data[$i]['radius_report_start_geofence'];
                    $dlocation[] = $l[$data[$i]['radius_report_start_geofence']];
                }
                // $data_fix[$exp[0]][$data[$i]['radius_report_host_vehicle_company_name']][$data[$i]['radius_report_host_vehicle_no']] = array("vehicle" => $data[$i]['radius_report_host_vehicle_no']);


				if (!isset($data_fix[$exp[0]][$data[$i]['radius_report_host_vehicle_no']])) {
                    $data_fix[$exp[0]][$data[$i]['radius_report_host_vehicle_no']] = array();
                    $d = array(
                        "company" => $data[$i]['radius_report_host_vehicle_no'],
						"vehicle" => $data[$i]['radius_report_guest_vehicle_no'],
                        "vehicle_guest" => $data[$i]['radius_report_guest_vehicle_no'],
                        "distance" => $data[$i]['radius_report_distance'],
						"gpstime" => $data[$i]['radius_report_start_time'],
                        "hour" => $exp[0]
                    );
                    array_push($data_fix[$exp[0]][$data[$i]['radius_report_host_vehicle_no']], $d);
                } else {
                    $d = array(
                        "company" => $data[$i]['radius_report_host_vehicle_no'],
						"vehicle" => $data[$i]['radius_report_guest_vehicle_no'],
                        "vehicle_guest" => $data[$i]['radius_report_guest_vehicle_no'],
                        "distance" => $data[$i]['radius_report_distance'],
						"gpstime" => $data[$i]['radius_report_start_time'],
                        "hour" => $exp[0]
                    );
                    array_push($data_fix[$exp[0]][$data[$i]['radius_report_host_vehicle_no']], $d);
                }
                if (!isset($unit_location[$data[$i]['radius_report_host_vehicle_no']])) {
                    $unit_location[$data[$i]['radius_report_host_vehicle_no']] = $data[$i]['radius_report_host_vehicle_no'];
                }
                $data[$i]['hour'] = $exp[0];
            }

			//print_r($data_fix);exit();

            echo json_encode(array(
                "code" => 200,
                "error" => false,
                "msg" => "success",
                "data" => $data, //data mentah dari source data untuk compare data fix
                "total_unit_location" => count($unit_location), //total unit dilokasi terpilih
                "total_unit" => $total_unit, //total unit semua kontraktor atau perkontraktor dipilih
                "total_unit_per_contractor" => $total_unit_percontractor, //total unit tiap kontraktor
                "data_hour" => $dhour, //data jam
                "data_company" => $dcompany, //data kontraktor
                "data_location" => $dlocation, //data lokasi
                "data_fix" => $data_fix, //data fix
				"length_data" => count($data), //data all
                "length_company" => count($dcompany), //jumlah kontraktor
                "length_location" => count($dlocation), //jumlah lokasi
                "length_hour" => count($dhour) //jumlah jam
            ));
        } else {
            echo json_encode(array("code" => 200, "error" => true, "msg" => "Data Empty", "data" => $data, "total" => $nr));
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
		$this->db->where("company_exca", 1);
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
		$this->db->where("company_exca", 0);
        $qd = $this->db->get("company");
        $rd = $qd->result();

        return $rd;
    }

	function get_location()
    {
        $this->db->select("street_alias");
        $this->db->order_by("street_alias", "asc");
        $this->db->where_in("street_type", array(4));
		$this->db->where("street_alias <>", "PORT BBC,");
        $q = $this->db->get("street");

        return $q->result();
    }
	
	function get_allExca()
    {
        $this->db->select("vehicle_no");
        $this->db->order_by("vehicle_no", "asc");
        $this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_typeunit", 1);
        $q = $this->db->get("vehicle");

        return $q->result();
    }
}
