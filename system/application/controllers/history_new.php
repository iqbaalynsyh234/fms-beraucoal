<?php
include "base.php";

class History_new extends Base {
	var $otherdb;

	function History_new()
	{
		parent::Base();
		$this->load->model("gpsmodel");
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
		$this->load->helper('common_helper');
		$this->load->helper('kopindosat');
		$this->load->model("historymodel");
	}

	function index(){

    if (! isset($this->sess->user_type))
		{
			redirect(base_url());
		}

		$this->db->select("vehicle.*, user_name");
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where("vehicle_status <>", 3);

		if ($this->sess->user_type == 2)
		{
			$this->db->where("vehicle_user_id", $this->sess->user_id);
			$this->db->or_where("vehicle_company", $this->sess->user_company);
			$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		}
		else
		if ($this->sess->user_type == 3)
		{
			$this->db->where("user_agent", $this->sess->user_agent);
		}
		//tambahan, user group yg open playback report
		if ($this->sess->user_group <> 0)
		{
			$this->db->where("vehicle_group", $this->sess->user_group);
		}

		$this->db->join("user", "vehicle_user_id = user_id", "left outer");
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			redirect(base_url());
		}

		$rows = $q->result();
		// echo "<pre>";
    // var_dump($rows);die();
    // echo "<pre>";
		$this->params["vehicles"] = $rows;

		$this->params["content"] = $this->load->view('transporter/historynew/v_historynew', $this->params, true);
		$this->load->view("templatesess", $this->params);
	}

  function gethistory()
  {
    header('Access-Control-Allow-Origin:*');
    $devhist = isset($_POST['id']) ? $_POST['id'] : "";
    $sdate = isset($_POST['sdate']) ? $_POST['sdate'] : "";

    $stime = isset($_POST['stime']) ? $_POST['stime'] : "";
    $etime = isset($_POST['etime']) ? $_POST['etime'] : "";
    $limit = isset($_POST['limit']) ? $_POST['limit'] : "100";
		$typehistory = isset($_POST['typehistory']) ? $_POST['typehistory'] : "0";
		// print_r($typehistory);exit();

    $offset = 0;
    $sdatefmt = date("Y-m-d H:i:s",strtotime($sdate." ".$stime.":00"));
    $edatefmt = date("Y-m-d H:i:s",strtotime($sdate." ".$etime.":59"));

    $sdatefmt = new DateTime($sdatefmt);
    $sdatefmt->modify('-7 hour');
    $sdatefmt = $sdatefmt->format('Y-m-d H:i:s');

    $edatefmt = new DateTime($edatefmt);
    $edatefmt->modify('-7 hour');
    $edatefmt = $edatefmt->format('Y-m-d H:i:s');

    $isdate = date("Y-m-d",strtotime($sdatefmt));
    $yesterday = mktime(0, 0, 0, date('n'), date('j', mktime()), date('Y'))-7*3600;

    $this->db->where("vehicle_id", $devhist);
    $q = $this->db->get("vehicle");
    $rowvehicle = $q->row();
    $vehicle_nopol = $rowvehicle->vehicle_no;

    $ex = explode("@",$rowvehicle->vehicle_device);
    $name = $ex[0];
    $host = $ex[1];

    $json = json_decode($rowvehicle->vehicle_info);

    $tables = $this->gpsmodel->getTable($rowvehicle);
    $this->db = $this->load->database($tables["dbname"], TRUE);

    $params['vehicle'] = $rowvehicle;
    $isgtp = in_array(strtoupper($rowvehicle->vehicle_type), $this->config->item("vehicle_gtp"));

    $tablehist = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
    $tablehistinfo = sprintf("%s_info", strtolower($rowvehicle->vehicle_device));

    $totalodometer = 0;
    $totalodometer1 = 0;

    if($rowvehicle->vehicle_type == "GT06" || $rowvehicle->vehicle_type == "A13" || $rowvehicle->vehicle_type == "TK303" || $rowvehicle->vehicle_type == "TK309" || $rowvehicle->vehicle_type == "TK309N" || $rowvehicle->vehicle_type == "TK315N" || $rowvehicle->vehicle_type == "TK315DOOR")
    {
      $sdatefmt = new DateTime($sdatefmt);
      $sdatefmt->modify('+7 hour');
      $sdatefmt = $sdatefmt->format('Y-m-d H:i:s');

      $edatefmt = new DateTime($edatefmt);
      $edatefmt->modify('+7 hour');
      $edatefmt = $edatefmt->format('Y-m-d H:i:s');

    }

    if($isdate == date("Y-m-d"))
    {
      $this->db->order_by("gps_time", "asc");
      $this->db->where("gps_name", $ex[0]);
      $this->db->where("gps_host", $ex[1]);
      $this->db->where("gps_time >=", $sdatefmt);
      $this->db->where("gps_time <=", $edatefmt);
      $q = $this->db->get($tables["gps"]);
      $rows = $q->result();

      $this->db->order_by("gps_info_time", "asc");
      $this->db->select("gps_info_io_port, gps_info_time, gps_info_distance, gps_info_alarm_alert, gps_info_alarm_data, gps_info_ad_input");
      $this->db->where("gps_info_device", $ex[0]."@".$ex[1]);
      $this->db->where("gps_info_time >=", $sdatefmt);
      $this->db->where("gps_info_time <=", $edatefmt);
      $q = $this->db->get($tables["info"]);
      $rowlastinfos = $q->result();
    }
    else
    {
      $this->db->order_by("gps_time", "asc");
      $this->db->where("gps_name", $ex[0]);
      $this->db->where("gps_host", $ex[1]);
      $this->db->where("gps_time >=", $sdatefmt);
      $this->db->where("gps_time <=", $edatefmt);
      $q = $this->db->get($tables["gps"]);
      $rows = $q->result();

      $istbl_history = $this->config->item("dbhistory_default");
      if($this->config->item("is_dbhistory") == 1)
      {
        $istbl_history = $rowvehicle->vehicle_dbhistory_name;
      }
      $this->db = $this->load->database($istbl_history, TRUE);
      $this->db->order_by("gps_time", "asc");
      $this->db->where("gps_name", $ex[0]);
      $this->db->where("gps_host", $ex[1]);
      $this->db->where("gps_time >=", $sdatefmt);
      $this->db->where("gps_time <=", $edatefmt);
      $q = $this->db->get($tablehist);
      $rowshist = $q->result();

      $rows = array_merge($rows, $rowshist);

      $total = count($rows);
    }

    for($i=count($rows)-1; $i >= 0; $i--)
    {
      if (($i+1) >= count($rows))
      {
        $rowsummary[] = $rows[$i];
        continue;
      }
      $latbefore = getLatitude($rows[$i+1]->gps_latitude, $rows[$i+1]->gps_ns);
      $lngbefore = getLongitude($rows[$i+1]->gps_longitude, $rows[$i+1]->gps_ew);
      $latcurrent = getLatitude($rows[$i]->gps_latitude, $rows[$i]->gps_ns);
      $lngcurrent = getLongitude($rows[$i]->gps_longitude, $rows[$i]->gps_ew);
      if (sprintf("%.4f,%.4f", $latbefore, $lngbefore) != sprintf("%.4f,%.4f", $latcurrent, $lngcurrent))
      {
        $rowsummary[] = $rows[$i];
        continue;
      }
      if ($rows[$i+1]->gps_speed != $rows[$i]->gps_speed)
      {
        $rowsummary[] = $rows[$i];
        continue;
      }
    }

    $rows = array();
    $total = 0;
    if (isset($rowsummary))
    {
      $rowsummary = array_reverse($rowsummary);
      $total = count($rowsummary);
      $rows = array_splice($rowsummary, $offset, $limit);
    }

    unset($map_params);
    $ismove = false;
    $lastcoord = false;

    for($i=0; $i < count($rows); $i++)
    {

      if ($i == 0)
      {
        // ambil info

        $tinfo2 = dbmaketime($rows[0]->gps_time);
        $tinfo1 = dbmaketime($rows[count($rows)-1]->gps_time);

        if ($tinfo1 > $yesterday)
        {
          if (isset($json->vehicle_ws))
          {
            if ($tinfo1 > $yesterday)
            {
              $this->db = $this->load->database("gpshistory2", TRUE);
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

            $rowinfos = $this->historymodel->allinfo($tablehistinfo, $name, $host, $tinfo1, $tinfo2,  0, 0, array() ,"asc");
          }
          else
          {
            $this->db = $this->load->database($tables["dbname"], TRUE);
            $rowinfos = $this->historymodel->allinfo($tables["info"], $name, $host, $tinfo1, $tinfo2,  0, 0, array() ,"asc");
          }
        }
        else
        if ($tinfo2 <= $yesterday)
        {

          if (!isset($json->vehicle_ws))
          {
            $istbl_history = $this->config->item("dbhistory_default");
            if($this->config->item("is_dbhistory") == 1)
            {
              $istbl_history = $rowvehicle->vehicle_dbhistory_name;
            }
            $this->db = $this->load->database($istbl_history, TRUE);
            $rowinfos = $this->historymodel->allinfo($tablehistinfo, $name, $host, $tinfo1, $yesterday,  0, 0, array() ,"asc");
          }
          else
          {
            $istbl_history = $this->config->item("dbhistory_default");
            if($this->config->item("is_dbhistory") == 1)
            {
              $istbl_history = $rowvehicle->vehicle_dbhistory_name;
            }
            $this->db = $this->load->database($istbl_history, TRUE);
            $rowinfos1 = $this->historymodel->allinfo($tablehistinfo, $name, $host, $tinfo1, $yesterday,  0, 0, array() ,"asc");

            $this->db = $this->load->database("gpshistory2", TRUE);
            $rowinfos2 = $this->historymodel->allinfo($tablehistinfo, $name, $host, $tinfo1, $yesterday,  0, 0, array() ,"asc");
            $rowinfos = array_merge($rowinfos1, $rowinfos2);
          }
        }
        else
        {

          if ((!isset($json->vehicle_ws)))
          {
            $this->db = $this->load->database($tables["dbname"], TRUE);
            $rowinfos1 = $this->historymodel->allinfo($tables["info"], $name, $host, $yesterday, $tinfo2,  0, 0, array() ,"asc");

            $istbl_history = $this->config->item("dbhistory_default");
            if($this->config->item("is_dbhistory") == 1)
            {
              $istbl_history = $rowvehicle->vehicle_dbhistory_name;
            }
            $this->db = $this->load->database($istbl_history, TRUE);
            $rowinfos2 = $this->historymodel->allinfo($tablehistinfo, $name, $host, $tinfo1, $yesterday,  0, 0, array() ,"asc");
          }
          else
          {
            $istbl_history = $this->config->item("dbhistory_default");
            if($this->config->item("is_dbhistory") == 1)
            {
              $istbl_history = $rowvehicle->vehicle_dbhistory_name;
            }
            $this->db = $this->load->database($istbl_history, TRUE);
            $rowinfos1 = $this->historymodel->allinfo($tablehistinfo, $name, $host, $tinfo1, $tinfo2,  0, 0, array() ,"asc");

            $this->db = $this->load->database("gpshistory2", TRUE);
            $rowinfos2 = $this->historymodel->allinfo($tablehistinfo, $name, $host, $tinfo1, $tinfo2,  0, 0, array() ,"asc");
          }

          $rowinfos = array_merge($rowinfos1, $rowinfos2);
        }

        for($j=0; $j < count($rowinfos); $j++)
        {
          $infos[dbmaketime($rowinfos[$j]->gps_info_time)] = $rowinfos[$j];
        }
      }

      $rows[$i]->gps_timestamp = dbmaketime($rows[$i]->gps_time);

      // T6 Invalid condition
      if ($rowvehicle->vehicle_type == "T6" && $rows[$i]->gps_status == "V")
      {
        $tables = $this->gpsmodel->getTable($rowvehicle);
        $this->db = $this->load->database($tables["dbname"], TRUE);

        $this->db->limit(1);
        $this->db->order_by("gps_time", "desc");
        $this->db->where("gps_time <=", date("Y-m-d H:i:s"));
        $this->db->where("gps_name", $name);
        $this->db->where("gps_host", $host);
        $this->db->where("gps_latitude <>", 0);
        $this->db->where("gps_longitude <>", 0);
        $this->db->where("gps_status", "A");
        $q_lastvalid = $this->db->get($tables['gps']);

        if ($q_lastvalid->num_rows() == 0)
        {
          $tablehist = sprintf("%s_gps", strtolower($rowvehicle->vehicle_device));
          $istbl_history = $this->config->item("dbhistory_default");
          if($this->config->item("is_dbhistory") == 1)
          {
            $istbl_history = $rowvehicle->vehicle_dbhistory_name;
          }
          $this->db = $this->load->database($istbl_history, TRUE);

          $this->db->limit(1);
          $this->db->order_by("gps_time", "desc");
          $this->db->where("gps_name", $name);
          $this->db->where("gps_host", $host);
          $this->db->where("gps_latitude <>", 0);
          $this->db->where("gps_longitude <>", 0);
          $this->db->where("gps_status", "A");
          $q_lastvalid = $this->db->get($tablehist);

          if ($q_lastvalid->num_rows() == 0) return;
        }

        $row_lastvalid = $q_lastvalid->row();
        $rows[$i]->gps_longitude_real = getLongitude($row_lastvalid->gps_longitude, $row_lastvalid->gps_ew);
        $rows[$i]->gps_latitude_real = getLatitude($row_lastvalid->gps_latitude, $row_lastvalid->gps_ns);
      }
      else
      {
        if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "TJAM" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK309N" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "TK315DOOR")
        {
          $rows[$i]->gps_longitude_real = getLongitude($rows[$i]->gps_longitude, $rows[$i]->gps_ew);
          $rows[$i]->gps_latitude_real = getLatitude($rows[$i]->gps_latitude, $rows[$i]->gps_ns);
        }
      }

      $rows[$i]->gps_longitude_real_fmt = number_format($rows[$i]->gps_longitude_real, 4, ".", "");
      $rows[$i]->gps_latitude_real_fmt = number_format($rows[$i]->gps_latitude_real, 4, ".", "");

      if ($i == 0)
      {
        $lastcoord = array($rows[$i]->gps_longitude_real_fmt, $rows[$i]->gps_latitude_real_fmt);
      }
      else
      {
        if (($lastcoord[0] != $rows[$i]->gps_longitude_real_fmt) || ($lastcoord[1] != $rows[$i]->gps_latitude_real_fmt))
        {
          $ismove = true;
        }
      }

      if($rowvehicle->vehicle_type != "GT06" && $rowvehicle->vehicle_type != "A13" && $rowvehicle->vehicle_type != "TK303" && $rowvehicle->vehicle_type != "TK309" && $rowvehicle->vehicle_type != "TK309N" && $rowvehicle->vehicle_type != "TK315N" && $rowvehicle->vehicle_type != "TK315DOOR")
      {
        $rows[$i]->gps_date_fmt = date("d/m/Y", $rows[$i]->gps_timestamp+7*3600);
        $rows[$i]->gps_time_fmt = date("H:i:s", $rows[$i]->gps_timestamp+7*3600);
      }
      else
      {
        $rows[$i]->gps_date_fmt = date("d/m/Y", $rows[$i]->gps_timestamp);
        $rows[$i]->gps_time_fmt = date("H:i:s", $rows[$i]->gps_timestamp);
      }

      $rows[$i]->gps_speed_fmt = number_format($rows[$i]->gps_speed*1.852, 0, "", ".");
      $rows[$i]->gps_status = ($rows[$i]->gps_status == "A") ? "OK" : "NOT OK";


      if (isset($infos[$rows[$i]->gps_timestamp]))
      {
        $ioport = $infos[$rows[$i]->gps_timestamp]->gps_info_io_port;
        $rows[$i]->status1 = ((strlen($ioport) > 4) && ($ioport[4] == 1)) ? $this->lang->line('lon') : $this->lang->line('loff');
        $rows[$i]->odometer = number_format(round(($infos[$rows[$i]->gps_timestamp]->gps_info_distance+$rowvehicle->vehicle_odometer*1000)/1000), 0, "", ",");
      }
      else
      {
        $rows[$i]->status1 = "-";
        $rows[$i]->odometer = "-";
      }

      $rows[$i]->georeverse = $this->gpsmodel->GeoReverse($rows[$i]->gps_latitude_real_fmt, $rows[$i]->gps_longitude_real_fmt);

      $rows[$i]->gpsindex = $i+1;
      $rows[$i]->gpsdate = date("d/m/Y", $rows[$i]->gps_timestamp+7*3600);
      $rows[$i]->gpstime = date("H:i:s", $rows[$i]->gps_timestamp+7*3600);
      $rows[$i]->gpsaddress = $rows[$i]->georeverse->display_name;
      $rows[$i]->gpscoord = "(".$rows[$i]->gps_longitude_real_fmt." ".$rows[$i]->gps_latitude_real_fmt.")";
      $rows[$i]->gpstatus = (($rows[$i]->gps_status == "V") ? "NOT OK" : "OK");

      //Fan || Dooe
      $appfan = $this->config->item("fan_app");
      if ($rowvehicle->vehicle_type == "T5FAN" || $rowvehicle->vehicle_type == "T5DOOR" || $rowvehicle->vehicle_type == "T5PTO")
      {
        $rows[$i]->fan = $this->getFanStatus($rows[$i]->gps_msg_ori);
      }

    }

    exit (json_encode(array("data"=>$rows, "typehistory" => $typehistory)));
    return;

  }

  function gotoanimation()
  {
    $rows = 0;
    exit (json_encode(array("data"=>$rows)));
    return;
  }

  function getFanStatus($val)
  {
    //$val = "(000000001271BP05000000000001271120804A0617.4940S10657.9536E000.004514179.73001100000L00000000";
    $totstring = strlen($val);
    $value = substr($val, 79, 1);
    //print_r($value);
    return($value);
  }

}
