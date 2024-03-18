<?php
include "base.php";

class Mod_dist_tupperware extends Base {

	function __construct()
	{
		parent::Base();	
		$this->load->model("gpsmodel");
	}
	
	function index()
	{
		if(! isset($this->sess->dist_code)){redirect(base_url());}
		$sortby = isset($_POST['sortby']) ? $_POST['sortby'] : "user_name";
		$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : "asc";
		$this->dbtrans = $this->load->database("transporter",true);
		$this->dbtrans->join("tupper_dr","transporter_dr_booking_id = booking_id","left_outer");
		$this->dbtrans->where("transporter_dr_status",1);
		$this->dbtrans->where("transporter_db_code",$this->sess->dist_code);
		$this->dbtrans->where("booking_status",1);
		$this->dbtrans->where("booking_delivery_status",1);
		$q = $this->dbtrans->get("id_booking");
		$rows = $q->result();
		$total = count($rows);
		$vehicle="";
		if(isset($rows))
		{
			for($i=0;$i<$total;$i++)
			{
				$vehicle[]=$rows[$i]->booking_vehicle;
			}
		}
		$this->db->order_by("vehicle_no", "asc");
		$this->db->where_in("vehicle_device", $vehicle);
		$q = $this->db->get("vehicle");
		$rows = $q->result();
		for($i=0; $i < count($rows); $i++)
		{
			if (isset($vehicles[$rows[$i]->vehicle_device])) 
			{
				if ($rows[$i]->vehicle_id < $vehicles[$rows[$i]->vehicle_device])
				{
					$vehicles[$rows[$i]->vehicle_device] = $rows[$i]->vehicle_id;
					continue;
				}
			}
			$vehicles[$rows[$i]->vehicle_device] = $rows[$i]->vehicle_id;
		}
		for($i=0; $i < count($rows); $i++)
		{
			$arr = explode("@", $rows[$i]->vehicle_device);
			$rows[$i]->vehicle_id = $vehicles[$rows[$i]->vehicle_device];
			$rows[$i]->vehicle_device_name = (count($arr) > 0) ? $arr[0] : "";
			$rows[$i]->vehicle_device_host = (count($arr) > 1) ? $arr[1] : "";
		}
		$this->params['sortby'] = $sortby;
		$this->params['orderby'] = $orderby;		
		$this->params["data"] = $rows;
		$this->params["initmap"] = $this->load->view('initmap', $this->params, true);
		$this->params["updateinfo"] =  $this->load->view('updateinfo', $this->params, true);
		$this->params["content"] = $this->load->view('trackers/list', $this->params, true);		
		$this->load->view("templatesess", $this->params);	
	} 
	
}