<?php
include "base.php";
setlocale(LC_ALL, 'IND');

class Historyfmsnew extends Base {
	var $period1;
	var $period2;
	var $tblhist;
	var $tblinfohist;
	var $otherdb;

	function __construct()
	{
		parent::Base();
		$this->load->model("gpsmodel");
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
		$this->load->model("historymodel");
		$this->load->model("dashboardmodel");
		$this->load->model("m_common_report");
	}

  function formhistory(){
    // ini_set('max_execution_time', '300');
    // set_time_limit(300);
    if (! isset($this->sess->user_type))
    {
      redirect(base_url());
    }

    $privilegecode                  = $this->sess->user_id_role;

    $rows                           = $this->dashboardmodel->getvehicle_report();
    $rows_company                   = $this->m_common_report->get_company_bylevel();
    $this->params["vehicles"]       = $rows;
    $this->params["rcompany"]       = $rows_company;
    $this->params['code_view_menu'] = "monitoradminonly";

    $this->params["header"]         = $this->load->view('newdashboard/partial/headernew', $this->params, true);
    $this->params["chatsidebar"]    = $this->load->view('newdashboard/partial/chatsidebar', $this->params, true);

    if ($privilegecode == 1) {
      $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_superuser', $this->params, true);
      $this->params["content"]        = $this->load->view('newdashboard/report/history/v_home_history', $this->params, true);
      $this->load->view("newdashboard/partial/template_dashboard_superuser", $this->params);
    } elseif ($privilegecode == 2) {
      $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_managementuser', $this->params, true);
      $this->params["content"]        = $this->load->view('newdashboard/report/history/v_home_history', $this->params, true);
      $this->load->view("newdashboard/partial/template_dashboard_managementuser", $this->params);
    } elseif ($privilegecode == 3) {
      $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_reguleruser', $this->params, true);
      $this->params["content"]        = $this->load->view('newdashboard/report/history/v_home_history', $this->params, true);
      $this->load->view("newdashboard/partial/template_dashboard_reguleruser", $this->params);
    } elseif ($privilegecode == 4) {
      $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_teknikaluser', $this->params, true);
      $this->params["content"]        = $this->load->view('newdashboard/report/history/v_home_history', $this->params, true);
      $this->load->view("newdashboard/partial/template_dashboard_teknikaluser", $this->params);
    } elseif ($privilegecode == 5) {
      $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_adminpjo', $this->params, true);
      $this->params["content"]        = $this->load->view('newdashboard/report/history/v_home_history', $this->params, true);
      $this->load->view("newdashboard/partial/template_dashboard_adminpjo", $this->params);
    } elseif ($privilegecode == 6) {
      $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar_userpjo', $this->params, true);
      $this->params["content"]        = $this->load->view('newdashboard/report/history/v_home_history', $this->params, true);
      $this->load->view("newdashboard/partial/template_dashboard_userpjo", $this->params);
    } else {
      $this->params["sidebar"]        = $this->load->view('newdashboard/partial/sidebar', $this->params, true);
      $this->params["content"]        = $this->load->view('newdashboard/report/history/v_home_history', $this->params, true);
      $this->load->view("newdashboard/partial/template_dashboard_new", $this->params);
    }
  }

	function searchhistory()
	{
		$id             = "history";
		$vehicle_device = explode("@", $this->input->post("vehicle"));

		if ($vehicle_device[0] == "")
		{
			$callback['error']   = true;
			$callback['message'] = "Silahkan pilih kendaraan!";

			echo json_encode($callback);
			return false;
		}
		$name           = $vehicle_device[0];
		$host           = $vehicle_device[1];
		$sdate          = date("Y-m-d", strtotime($this->input->post("startdate")));
		$edate          = date("Y-m-d", strtotime($this->input->post("startdate")));
		$stime 					= $this->input->post("shour").":00";
		$etime 					= $this->input->post("ehour").":59";

		$startdate  	  = $sdate." ".$stime;
		$enddate 	  	  = $edate." ".$etime;



		// echo "<pre>";
		// var_dump();die();
		// echo "<pre>";

		$this->db->where("vehicle_device", $name.'@'.$host);
		$q             = $this->db->get("vehicle");
		$rowvehicle    = $q->row();
		$vehicle_nopol = $rowvehicle->vehicle_no;
		$json          = json_decode($rowvehicle->vehicle_info);

		$tyesterday = mktime();
		if (!in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others")))
		{
			$tyesterday  = mktime(-7, 59, 59, date('n', $tyesterday), date('j', $tyesterday), date('Y', $tyesterday));
			$yesterday1  = mktime(0, 0, 0, date('n'), date('j', mktime()), date('Y'))-7*3600;
			$yesterday2  = date("Y-m-d H:i:s", $yesterday1);
			$yesterday   = strtotime($yesterday2);
			// $t1       = $startdate - 7*3600;
			// $t2       = $enddate - 7*3600;
			$s1 				 = date("Y-m-d H:i:s", strtotime($startdate."-7 hours"));
			$s2 				 = date("Y-m-d H:i:s", strtotime($enddate."-7 hours"));
			$t1          = strtotime($s1);
			$t2          = strtotime($s2);
		}
		else
		{
			$tyesterday = mktime(-0, 59, 59, date('n', $tyesterday), date('j', $tyesterday), date('Y', $tyesterday));
			$yesterday1  = mktime(0, 0, 0, date('n'), date('j', mktime()), date('Y'))-0*3600;
			$yesterday2  = date("Y-m-d H:i:s", yesterday1);
			$yesterday   = strtotime($yesterday2);
			// $t1         = $startdate - 0*3600;
			// $t2         = $enddate - 0*3600;
			$s1 				 = date("Y-m-d H:i:s", strtotime($startdate."-0 hours"));
			$s2 				 = date("Y-m-d H:i:s", strtotime($enddate."-0 hours"));
			$t1          = strtotime($s1);
			$t2          = strtotime($s2);
		}

		$tables            = $this->gpsmodel->getTable($rowvehicle);
		$this->db          = $this->load->database($tables["dbname"], TRUE);

		// echo "<pre>";
		// var_dump($tables);die();
		// echo "<pre>";

		$params['vehicle'] = $rowvehicle;

		$isgtp             = in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_gtp"));

		$tablehist         = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
		$tablehistinfo     = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));

		$totalodometer     = 0;
		$totalodometer1    = 0;

		// KONDISI PERTAMA
		// $this->db                              = $this->load->database("gpshistory22", TRUE);
		// $rows                                  = $this->historymodel->all($tablehist, $name, $host, $t1, $t2, 0);
		//
		// $rowlastinfos                          = $this->historymodel->allinfo($tablehistinfo, $name, $host, $yesterday, $t2, 1);

		if ($t1 > $yesterday && (!isset($json->vehicle_ws)))
		{
			// echo "<pre>";
			// var_dump("sikon 1");die();
			// echo "<pre>";

			// if ($rowvehicle->vehicle_imei == "869926046501587") {
			// 	// echo "<pre>";
			// 	// var_dump("sikon 1");die();
			// 	// echo "<pre>";
			// 	$rows           = $this->historymodel->all($tables["gps"], $name, $host, $t1, $t2, 0);
			// 	$rowsdb638      = $this->historymodel->khusus638("gps", $name, $host, $t1, $t2, 0);
			// 	// $rowshist638 = $this->historymodel->khusus638($tablehist, $name, $host, $t1, $yesterday, 0);
			// 	$rows           = array_merge($rows, $rowsdb638);
			//
			// 	$rowlastinfos1 = $this->historymodel->allinfo638("gps_info", $name, $host, $t1, $t2,  1);
			// 	$rowlastinfos2 = $this->historymodel->allinfo($tables["info"], $name, $host, $t1, $t2,  1);
			// 	$rowlastinfos = array_merge($rowlastinfos1, $rowlastinfos2);
			//
			//
			// 	if (count($rowlastinfos) > 0)
			// 	{
			// 		$totalodometer = $rowlastinfos[0]->gps_info_distance;
			//
			// 		$rowfirstinfos1 = $this->historymodel->allinfo638("gps_info", $name, $host, $t1, $t2,  1, 0, array(), "ASC");
			// 		$rowfirstinfos2 = $this->historymodel->allinfo($tables["info"], $name, $host, $t1, $t2,  1, 0, array(), "ASC");
			// 		$rowfirstinfos = array_merge($rowfirstinfos1, $rowfirstinfos2);
			//
			// 		if (count($rowfirstinfos))
			// 		{
			// 			$totalodometer1 = $totalodometer-$rowfirstinfos[0]->gps_info_distance;
			// 		}
			// 	}
			//
			// 	// echo "<pre>";
			// 	// var_dump($tablehist);die();
			// 	// echo "<pre>";
			// }else {
				// echo "<pre>";
				// var_dump("sikon 2");die();
				// echo "<pre>";
				$rows = $this->historymodel->all($tables["gps"], $name, $host, $t1, $t2, 0);
				$rowlastinfos = $this->historymodel->allinfo($tables["info"], $name, $host, $t1, $t2,  1);

				if (count($rowlastinfos) > 0)
				{
					$totalodometer = $rowlastinfos[0]->gps_info_distance;

					$rowfirstinfos = $this->historymodel->allinfo($tables["info"], $name, $host, $t1, $t2,  1, 0, array(), "ASC");

					if (count($rowfirstinfos))
					{
						$totalodometer1 = $totalodometer-$rowfirstinfos[0]->gps_info_distance;
					}
				}
			// }
		}
		else
		{
			// echo "<pre>";
			// var_dump("sikon 2");die();
			// echo "<pre>";

				//mix
				if ($t2 > $yesterday && (!isset($json->vehicle_ws)))
				{
					// if ($rowvehicle->vehicle_imei == "869926046501587") {
					// 	$rows = $this->historymodel->khusus638("gps", $name, $host, $yesterday+1, $t2, 0);
					// 	$rowlastinfos = $this->historymodel->allinfo638("gps_info", $name, $host, $yesterday, $t2,  1);
					//
					// 	$istbl_history = $this->config->item("dbhistory_default");
					// 	if($this->config->item("is_dbhistory") == 1)
					// 	{
					// 		$istbl_history = $rowvehicle->vehicle_dbhistory_name;
					// 	}
					// 	$this->db = $this->load->database($istbl_history, TRUE);
					// 	$rowshist = $this->historymodel->khusus638("gps", $name, $host, $t1, $yesterday, 0);
					//
					// 	// echo "<pre>";
					// 	// var_dump($rowshist);die();
					// 	// echo "<pre>";
					// }else {
						$rows = $this->historymodel->all($tables["gps"], $name, $host, $yesterday+1, $t2, 0);
						$rowlastinfos = $this->historymodel->allinfo($tables["info"], $name, $host, $yesterday, $t2,  1);

						$istbl_history = $this->config->item("dbhistory_default");
						if($this->config->item("is_dbhistory") == 1)
						{
							$istbl_history = $rowvehicle->vehicle_dbhistory_name;
						}
						$this->db = $this->load->database($istbl_history, TRUE);
						$rowshist = $this->historymodel->all($tablehist, $name, $host, $t1, $yesterday, 0);
					// }
				}
				else if($t2 > $yesterday && (isset($json->vehicle_ws)))
				{
					$this->db = $this->load->database("gpshistory22", TRUE);
					$rows = $this->historymodel->all($tablehist, $name, $host, $t1, $t2, 0);
					$rowlastinfos = $this->historymodel->allinfo($tablehistinfo, $name, $host, $yesterday, $t2,  1);

					$istbl_history = $this->config->item("dbhistory_default");
					if($this->config->item("is_dbhistory") == 1)
					{
						$istbl_history = $rowvehicle->vehicle_dbhistory_name;
					}
					$this->db = $this->load->database($istbl_history, TRUE);
					$rowshist = $this->historymodel->all($tablehist, $name, $host, $t1, $yesterday, 0);
				}
				else
				{
					$this->db = $this->load->database("gpshistory22", TRUE);
					$rows = $this->historymodel->all($tablehist, $name, $host, $t1, $t2, 0);
					$rowlastinfos = $this->historymodel->allinfo($tablehistinfo, $name, $host, $t1, $t2,  1);

					$istbl_history = $this->config->item("dbhistory_default");
					if($this->config->item("is_dbhistory") == 1)
					{
						$istbl_history = $rowvehicle->vehicle_dbhistory_name;
					}
					$this->db = $this->load->database($istbl_history, TRUE);
					$rowshist = $this->historymodel->all($tablehist, $name, $host, $t1, $t2, 0);
				}

				// KONDISI KHUSUS
				$rows = array_merge($rows, $rowshist);

				$total = count($rows);

				if (count($rowlastinfos))
				{
					$totalodometer = $rowlastinfos[0]->gps_info_distance;
					$rowfirstinfos = $this->historymodel->allinfo($tablehistinfo, $name, $host, $t1, $yesterday,  1, 0, array(), "ASC");

					if (count($rowfirstinfos))
					{
						$totalodometer1 = $totalodometer-$rowfirstinfos[0]->gps_info_distance;
					}
				}
		}

		// echo "<pre>";
		// var_dump($rows);die();
		// echo "<pre>";

		$istbl_history                         = $this->config->item("dbhistory_default");
			if($this->config->item("is_dbhistory") == 1)
			{
				$istbl_history                       = $rowvehicle->vehicle_dbhistory_name;
			}
		$this->db                              = $this->load->database($istbl_history, TRUE);
		// $rowshist                              = $this->historymodel->all($tablehist, $name, $host, $t1, $yesterday, 0);

		// $rows                                  = array_merge($rows, $rowshist);
		// $rows                                  = array_merge($rows, $rowshist);

		$total                                 = count($rows);

			if (count($rowlastinfos))
			{
				$totalodometer = $rowlastinfos[0]->gps_info_distance;
				$rowfirstinfos = $this->historymodel->allinfo($tablehistinfo, $name, $host, $t1, $yesterday,  1, 0, array(), "ASC");

				if (count($rowfirstinfos))
				{
					$totalodometer1 = $totalodometer-$rowfirstinfos[0]->gps_info_distance;
				}
			}

			// echo "<pre>";
			// var_dump(sizeof($rows));die();
			// echo "<pre>";

		$datafix = array();
		for($i=0; $i < sizeof($rows); $i++)
		{
			if ($i == 0)
			{
				// ambil info

				// $tinfo2 = dbmaketime($rows[0]->gps_time);
				// $tinfo1 = dbmaketime($rows[count($rows)-1]->gps_time);

				$tinfo2awal      = $rows[0]->gps_time;
				$tinfo2          = strtotime($tinfo2awal);

				$tinfo1awal      = $rows[count($rows)-1]->gps_time;
				$tinfo1          = strtotime($tinfo1awal);

				if ($tinfo1 > $yesterday)
				{
					// echo "<pre>";
					// var_dump("sikon 1");die();
					// echo "<pre>";
					if (isset($json->vehicle_ws))
					{
						// echo "<pre>";
						// var_dump("sikon 1");die();
						// echo "<pre>";
						if ($tinfo1 > $yesterday)
						{
							$this->db = $this->load->database("gpshistory22", TRUE);
						}
						else
						{
							$istbl_history = $this->config->item("dbhistory_default");
							if($this->config->item("is_dbhistory") == 1)
							{
								$istbl_history = $rowvehicle->vehicle_dbhistory_name;
							}
							$this->db = $this->load->database($istbl_history, TRUE);
						}

						$rowinfos = $this->historymodel->allinfo($tablehistinfo, $name, $host, $tinfo1, $tinfo2,  0);
					}
					else
					{
						// echo "<pre>";
						// var_dump("sikon 2");die();
						// echo "<pre>";
						// if ($rowvehicle->vehicle_imei == "869926046501587") {
						// 	$this->db = $this->load->database($tables["dbname"], TRUE);
						// 	$rowinfos1 = $this->historymodel->allinfo($tables["info"], $name, $host, $tinfo1, $tinfo2,  0);
						// 	$rowinfos2= $this->historymodel->allinfo638("gps_info", $name, $host, $tinfo1, $tinfo2,  0);
						// 	$rowinfos = array_merge($rowinfos1, $rowinfos2);
						// }else {
							$this->db = $this->load->database($tables["dbname"], TRUE);
							$rowinfos = $this->historymodel->allinfo($tables["info"], $name, $host, $tinfo1, $tinfo2,  0);
						// }

						// echo "<pre>";
						// var_dump($rowinfos);die();
						// echo "<pre>";
					}
				}
				else
				if ($tinfo2 <= $yesterday)
				{
					// echo "<pre>";
					// var_dump("sikon 2");die();
					// echo "<pre>";
					if (!isset($json->vehicle_ws))
					{
						$istbl_history = $this->config->item("dbhistory_default");
						if($this->config->item("is_dbhistory") == 1)
						{
							$istbl_history = $rowvehicle->vehicle_dbhistory_name;
						}
						$this->db = $this->load->database($istbl_history, TRUE);
						$rowinfos = $this->historymodel->allinfo($tablehistinfo, $name, $host, $tinfo1, $yesterday,  0);
					}
					else
					{
						$istbl_history = $this->config->item("dbhistory_default");
						if($this->config->item("is_dbhistory") == 1)
						{
							$istbl_history = $rowvehicle->vehicle_dbhistory_name;
						}
						$this->db = $this->load->database($istbl_history, TRUE);
						$rowinfos1 = $this->historymodel->allinfo($tablehistinfo, $name, $host, $tinfo1, $yesterday,  0);

						$this->db = $this->load->database("gpshistory22", TRUE);
						$rowinfos2 = $this->historymodel->allinfo($tablehistinfo, $name, $host, $tinfo1, $yesterday,  0);
						$rowinfos = array_merge($rowinfos1, $rowinfos2);
					}
				}
				else
				{
					// echo "<pre>";
					// var_dump("sikon 3");die();
					// echo "<pre>";
					if ((!isset($json->vehicle_ws)))
					{
						$this->db = $this->load->database($tables["dbname"], TRUE);
						$rowinfos1 = $this->historymodel->allinfo($tables["info"], $name, $host, $yesterday, $tinfo2,  0);

						$istbl_history = $this->config->item("dbhistory_default");
						if($this->config->item("is_dbhistory") == 1)
						{
							$istbl_history = $rowvehicle->vehicle_dbhistory_name;
						}
						$this->db = $this->load->database($istbl_history, TRUE);
						$rowinfos2 = $this->historymodel->allinfo($tablehistinfo, $name, $host, $tinfo1, $yesterday,  0);
					}
					else
					{
						$istbl_history = $this->config->item("dbhistory_default");
						if($this->config->item("is_dbhistory") == 1)
						{
							$istbl_history = $rowvehicle->vehicle_dbhistory_name;
						}
						$this->db = $this->load->database($istbl_history, TRUE);
						$rowinfos1 = $this->historymodel->allinfo($tablehistinfo, $name, $host, $tinfo1, $tinfo2,  0);

						$this->db = $this->load->database("gpshistory22", TRUE);
						$rowinfos2 = $this->historymodel->allinfo($tablehistinfo, $name, $host, $tinfo1, $tinfo2,  0);
					}

					$rowinfos = array_merge($rowinfos1, $rowinfos2);
				}

				for($j=0; $j < count($rowinfos); $j++)
				{
					$infos[dbmaketime($rowinfos[$j]->gps_info_time)] = $rowinfos[$j];
				}
			}

			$rows[$i]->gps_timestamp          = dbmaketime($rows[$i]->gps_time);

			$rows[$i]->gps_longitude_real_fmt = number_format($rows[$i]->gps_longitude_real, 4, ".", "");
			$rows[$i]->gps_latitude_real_fmt  = number_format($rows[$i]->gps_latitude_real, 4, ".", "");

			if (!in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others")))
			{
			  $rows[$i]->gps_date_fmt = date("d/m/Y", $rows[$i]->gps_timestamp+7*3600);
			  $rows[$i]->gps_time_fmt = date("H:i:s", $rows[$i]->gps_timestamp+7*3600);
			}
			else
			{
			  $rows[$i]->gps_date_fmt = date("d/m/Y", strtotime($rows[$i]->gps_time));
			  $rows[$i]->gps_time_fmt = date("H:i:s", strtotime($rows[$i]->gps_time));
			}

			$rows[$i]->gps_speed_fmt = number_format($rows[$i]->gps_speed*1.852, 0, "", ".");
			$rows[$i]->gps_status    = ($rows[$i]->gps_status == "A") ? "OK" : "NOT OK";

			if (isset($infos[$rows[$i]->gps_timestamp]))
			{
			  $ioport = $infos[$rows[$i]->gps_timestamp]->gps_info_io_port;
			  if($rowvehicle->vehicle_type == "GT06" || $rowvehicle->vehicle_type == "A13" || $rowvehicle->vehicle_type == "TK309" || $rowvehicle->vehicle_type == "TK315")
			  {
			    if($rows[$i]->gps_speed_fmt > 0)
			    {
			      $rows[$i]->status1 = $this->lang->line('lon');
			    }
			    else
			    {
			      $rows[$i]->status1 = ((strlen($ioport) > 4) && ($ioport[4] == 1)) ? $this->lang->line('lon') : $this->lang->line('loff');
			    }
			  }
			  else
			  {
			    $rows[$i]->status1 = ((strlen($ioport) > 4) && ($ioport[4] == 1)) ? $this->lang->line('lon') : $this->lang->line('loff');
			  }
			  $rows[$i]->odometer = number_format(round(($infos[$rows[$i]->gps_timestamp]->gps_info_distance+$rowvehicle->vehicle_odometer*1000)/1000), 0, "", ",");
			}
			else
			{
			  $rows[$i]->status1 = "-";
			  $rows[$i]->odometer = "-";
			}

			$rows[$i]->georeverse = $this->gpsmodel->GeoReversefromadmintib($rows[$i]->gps_latitude_real_fmt, $rows[$i]->gps_longitude_real_fmt);

			// echo "<pre>";
			// var_dump($rows[$i]->georeverse);die();
			// echo "<pre>";

			if (!in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_others")))
			{
			  $rows[$i]->gpsdate = date("d/m/Y", $rows[$i]->gps_timestamp+7*3600);
			  $rows[$i]->gpstime = date("H:i:s", $rows[$i]->gps_timestamp+7*3600);
			}
			else
			{
			  $rows[$i]->gpsdate = date("d/m/Y", strtotime($rows[$i]->gps_time));
			  $rows[$i]->gpstime = date("H:i:s", strtotime($rows[$i]->gps_time));
			}

			$rows[$i]->gps_mvd     = round($rows[$i]->gps_mvd);

		}

		// echo "<pre>";
		// var_dump($rows);die();
		// echo "<pre>";


		$params['totalodometer']  = round(($totalodometer+$rowvehicle->vehicle_odometer*1000)/1000);
		$params['totalodometer1'] = number_format(round($totalodometer1/1000), 0, ".", ",");
		$params['gps_name']       = $name;
		$params['gps_host']       = $host;
		$params['data']           = $rows;
		$params['title']          = $rowvehicle->vehicle_no." ".$rowvehicle->vehicle_name;

		// echo "<pre>";
		// var_dump($params['data']);die();
		// echo "<pre>";

		$html                     = $this->load->view("newdashboard/report/history/v_history_result", $params, true);

		$callback['title']        = $rowvehicle->vehicle_no." ".$rowvehicle->vehicle_name;
		$callback['error']        = false;
		$callback['html']         = $html;
		$callback['data']         = $rows;

		//kembalikan DB ke semula
		$this->db = $this->load->database("default", TRUE);
		echo json_encode($callback);
	}















}
