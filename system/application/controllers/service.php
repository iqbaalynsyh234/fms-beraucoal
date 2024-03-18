<?php
class Service extends Controller {

	function Service()
	{
		parent::Controller();	
		
		$this->load->helper("common");
		$this->load->model("vehiclemodel");
		$this->load->database();
	}

	function getSession($type="return")
	{
        	$session =  isset($_POST['session']) ? $_POST['session'] : "";
                if (strlen($session) == 0) return FALSE;
                
		$this->db->where("session_id", $session);
                $q = $this->db->get("session");
                
		if ($q->num_rows() == 0) return FALSE;
		$row = $q->row();
		
		$this->db->where("user_id", $row->session_user);
		$q = $this->db->get("user");

		if ($q->num_rows() == 0) return FALSE;

		if ($type == "json")
		{
			$callback['user'] = $q->result();
			echo json_encode($callback);
			return;
		}

		return $q->row();
	}

	function vehicle()
	{
		$row = $this->getSession();
		if ($row == FALSE)
		{
			return;
		}
		
		if ($row->user_type == 3)
		{						
			$this->db->where("user_agent", $row->user_agent);				
		}
		else
		if ($row->user_type == 2)
		{
			$this->db->where("user_id", $row->user_id);
		}
		
		$this->db->select("vehicle.*, user_name");
		$this->db->where("vehicle_status", 1);
		$this->db->join("user", "user_id = vehicle_user_id");
		$q = $this->db->get("vehicle");
		
		$rows = $q->result();
		for($i=0; $i < count($rows); $i++)
		{
			$t1 = dbintmaketime($rows[$i]->vehicle_active_date1, 0);
			$t2 = dbintmaketime($rows[$i]->vehicle_active_date2, 0);

			$rows[$i]->vehicle_date_expired = sprintf("%s - %s", date("d/m/Y", $t1), date("d/m/Y", $t2));
		}
		
		$callback['vehicles'] = $rows;
		echo json_encode($callback);
	}

	function placemarkers()
	{
		$this->load->helper("url");
		$this->load->model("gpsmodel");

		if (! isset($_POST['ids'])) return;
		if (! is_array($_POST['ids'])) return;
	
		$this->db->where("vehicle_status", 1);
		$this->db->where_in("vehicle_id", $_POST['ids']);
		$this->db->join("user", "user_id = vehicle_user_id");
		$q = $this->db->get("vehicle");	

		if ($q->num_rows() == 0) return;

		$rows = $q->result();
		for($i=0; $i < count($rows); $i++)
		{
                	$t = dbintmaketime($rows[$i]->vehicle_active_date1, 0);
                	$rows[$i]->vehicle_active_date1_fmt = date("M, jS Y", $t);

                	$t = dbintmaketime($rows[$i]->vehicle_active_date2, 0);
                	$rows[$i]->vehicle_active_date2_fmt = date("M, jS Y", $t);

			$devices = explode("@", $rows[$i]->vehicle_device);
			$rows[$i]->gps = $this->gpsmodel->GetLastInfo($devices[0], $devices[1], true, false, 0, $rows[$i]->vehicle_type);
		}

		$this->params["vehicles"] = $rows;

		$this->db->distinct();
		$this->db->select("vehicle_image");
		$q = $this->db->get("vehicle");

		$imagedirs = $q->result();
		
		$this->params['imagedirs'] = $imagedirs;
		$this->load->view("map/googleearth1", $this->params);
	}
	
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
