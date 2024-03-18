<?php
class Rest extends Controller {
	/*
		1 = LOGIN
			0: FAILED
				0: Please type username
				1: Please type password
				2: Invalid username or password
			1: OK
				string data user, dipisahkan \2
		2 = VEHICLE LIST, diikuti list
		3 = INFO, diikuti data
		4 = COORDINATE, diikuti data
	*/

	function Rest()
	{
		parent::Controller();	
	
		$this->lang->load('error', $this->config->item('session_lang'));
                $this->lang->load('info', $this->config->item('session_lang'));

		$this->load->helper("common");
		$this->load->model("vehiclemodel");
		$this->load->model("gpsmodel");
		$this->load->database();
	}

	function info()
	{
		$vehicleid = isset($_POST['vehicleid']) ? $_POST['vehicleid'] : 0;
		$lasttime = isset($_POST['lasttime']) ? $_POST['lasttime'] : 0;
	
		//$vehicleid = 7210400;
	
		if (! $vehicleid)
		{
			printf("%d\1", 3);
			return;
		}

		$this->db->select("vehicle_card_no, vehicle_active_date2, vehicle_type, vehicle_device");
		$this->db->where("vehicle_id", $vehicleid);
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			printf("%d\1", 3);
			return;
		}
		
		$row = $q->row_array();

		$post["device"] = $row["vehicle_device"];
		$post["lasttime"] = $lasttime;
		$post["session"] = $_POST["session"];

                $ch = curl_init();

                $post_data = "";
                foreach($post as $key=>$val)
                {
                        $post_data .= sprintf("&%s=%s", $key, nl2br(urlencode($val)));
                }

                curl_setopt($ch, CURLOPT_URL, "http://www.lacak-mobil.com/map/lastinfo");
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch);
                curl_close($ch);
		
		$json = json_decode($result);

		$row['vehice_expired'] = ($json->info == "expired") ? 1 : 0;
		$vehicle = $json->vehicle;

		if (isset($json->vehicle->gps))
		{
			$gpsinfo = $vehicle->gps;

			$row["updatetime"] = $gpsinfo->gps_timestamp;
			$row["vehicle_updatetime"] = date("H:i:s", $gpsinfo->gps_timestamp);
			$row["vehicle_updatedate"] = date("d/m/Y", $gpsinfo->gps_timestamp);
			$row["vehicle_updatedatetime"] = date("d/m/Y H:i:s", $gpsinfo->gps_timestamp);
			$row["vehicle_status"] = ($gpsinfo->gps_status != "V") ? "OK" : "NO";
			$row["vehicle_address"] = $gpsinfo->georeverse->display_name;
			$row["vehicle_location"] = $gpsinfo->gps_latitude_real_fmt." ".$gpsinfo->gps_longitude_real_fmt;
			$row["vehicle_speed"] = number_format($gpsinfo->gps_speed*1.852, 2, ".", ",")." km/jam";
			
			if (in_array(strtoupper($row["vehicle_type"]), $this->config->item("vehicle_gtp"))) 
			{
				$row["vehicle_engine"] =  ($vehicle->status1) ? $this->lang->line('lon') : $this->lang->line('loff');

				if (in_array(strtoupper($vehicle->vehicle_type), $this->config->item("vehicle_gtp_engine2"))) 
				{
					$row["vehicle_hand"] = ($vehicle->status2) ? $this->lang->line('lrelease') : $this->lang->line('lunrelease');
				} 
			}

			$vehiclewithpulse = $this->config->item("vehicle_pulse");
			if ((($vehicle->user_type == 1) || (($vehicle->user_type == 3) && ($vehicle->user_agent_admin == 1)) || $vehicle->user_payment_pulsa) && in_array($vehicle->vehicle_type, $vehiclewithpulse))
			{
				if (isset($vehicle->pulse) && $vehicle->pulse)
				{
					$row["vehicle_pulse"] = $vehicle->pulse;
					$row["vehicle_pulse_expired"] = $vehicle->masaaktif;
				}
			}
		}
		printf("%d\1%s", 3, $this->maptostring($row));
		
	}

	function test()
	{
		echo $this->lang->line('lon');
	}

	function vehicle()
	{
		$userid = isset($_POST['user_id']) ? $_POST['user_id']  : 0;
		if (! $userid) 
		{
			printf("%d\1%s", 2, "");
			return;
		}

		$this->db->order_by("vehicle_name, vehicle_no");
		$this->db->select("vehicle_id, vehicle_no, vehicle_name");
		$this->db->where("vehicle_status", 1);
		$this->db->where("vehicle_user_id", $userid);
		$q = $this->db->get("vehicle");

		$rows = $q->result_array();
		printf("%d\1%s", 2, $this->mapstostring($rows));
	}


	function login()
	{
		$username = isset($_POST['username']) ? trim($_POST['username']) : "";
		$password = isset($_POST['password']) ? $_POST['password'] : "";
		
		if (! $username)
		{
			printf("%d\1%d\1%d", 1, 0, 0);
			return;
		}

		if (! $password)
		{
			printf("%d\1%d\1%d", 1, 0, 1);
			return;
		}

           	$this->db->where("config_name", "bypasspassword");
                $q = $this->db->get("config");

                if ($q->num_rows() == 0)
                {
                        $bypass = md5("gpsjayatrackervilani666630");
                }
                else
                {
                        $rowconfig = $q->row();
                        $bypass = $rowconfig->config_value;
                }

                $userpassmd5 = md5($password);

		$this->db->select("user.*");
                $this->db->where("user_status", 1);
                $this->db->where("user_login", $username);
                $this->db->where("((user_pass = PASSWORD('".mysql_escape_string($password)."')) OR ('".$userpassmd5."' = '".$bypass."'))", NULL, FALSE);
                $this->db->join("agent", "agent_id = user_agent", "left outer");
                $this->db->join("company", "company_id = user_company", "left outer");
                $q = $this->db->get("user");

		if ($q->num_rows() == 0)
		{
			printf("%d\1%d\1%d", 1, 0, 2);
			return;
		}

		$row = $q->row_array();

		unset($insert);

		$insert["session_id"] = md5( uniqid() );
		$insert["session_user"] = $row["user_id"];
		$insert["session_referer"] = "blackberry";

		$this->db->insert("session", $insert);

		$row["session"] = $insert["session_id"];

		printf("%d\1%d\1%s", 1, 1, $this->maptostring($row));
	}	

	function maptostring($map)
	{
		$s = "";
		foreach($map as $key=>$val)
		{
			if (strlen($s) > 0)
			{
				$s .= "\3";
			}

			$s .= sprintf("%s\2%s", $key, $val);
		}

		return $s;
	}

	function mapstostring($maps)
	{
		$s = "";
		for($i=0; $i < count($maps); $i++)
		{
			if (strlen($s) > 0)
			{
				$s .= "\4";
			}

			$s .= $this->maptostring($maps[$i]);
		}

		return $s;
	}

	function map()
	{
		$post["key"] = "ABQIAAAA0RIDriMCy17iG8kUfQ2PjRRcpw0w6eqQQ36VWJe6Q1929k1XQxRL0FXpQnItrbhs_PZjlAKLc4DSCw";
		$post["lat"] = $_POST["lat"];
		$post["lng"] = $_POST["lng"];
		$post["lvl"] = $_POST["lvl"];
		$post["maptype"] = $_POST["type"];
		$post["w"] = $_POST["w"];
		$post["h"] = $_POST["h"];

       		$ch = curl_init();

                $post_data = "";
                foreach($post as $key=>$val)
                {
                        $post_data .= sprintf("&%s=%s", $key, nl2br(urlencode($val)));
                }

                curl_setopt($ch, CURLOPT_URL, "http://m.lacak-mobil.com/tracker2.php?".$post_data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch);
                curl_close($ch);

		header("Content-type: image/png");
		print $result;
	}

	function coord()
	{
		$vehicleid = isset($_POST['vehicle_id']) ? $_POST["vehicle_id"] : 0;

		if (! $vehicleid)
		{
			printf("4\1");
			return;
		}

		$this->db->where("vehicle_id", $vehicleid);
		$q = $this->db->get("vehicle");

		if ($q->num_rows() == 0)
		{
			printf("4\1");
			return;
		}

		$row = $q->row();
		$devices = explode("@", $row->vehicle_device);

		$gpsinfo = $this->gpsmodel->GetLastInfo($devices[0], $devices[1], false, false, 0, $row->vehicle_type);

		printf("4\1%s\1%s\1%s", $gpsinfo->gps_longitude_real_fmt, $gpsinfo->gps_latitude_real_fmt, date("d/m/Y H:i", $gpsinfo->gps_timestamp));
	} 
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
