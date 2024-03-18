<?php
include "base.php";

class Tmp_tools extends Base {

	function Tmp_tools()
	{
		parent::Base();	
		$this->load->helper("common");
		$this->load->model("gpsmodel");
		$this->load->model("smsmodel");
		$this->load->model("agenmodel");
	}
	
	//New Cron History Move To GPSHISTORY TMP TO GPSHISTORY
	function tmp_vehicle_id($vid=0, $status="", $maxdata=10000, $offset = 0)
	{
		
		$this->db->distinct();
		
		if ($vid != 0)
		{
			if ($status == "besar")
			{
				$this->db->where("vehicle_id >=", $vid);
			}
			
			if ($status == "kecil")
			{
				$this->db->where("vehicle_id <=", $vid);
			}
			
		}
		
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_user_id <>", "860");
		$this->db->where("vehicle_user_id <>", "1095");
		$this->db->where("vehicle_user_id <>", "1122");
		$this->db->select("vehicle_type, vehicle_device, vehicle_info");
		$q = $this->db->get("vehicle");
		
		if ($q->num_rows() == 0) return;

		$rows = $q->result();
		
		$totalvehicle = count($rows);
		$i = 0;
		foreach($rows as $row)
		{
			if (($i+1) < $offset)
			{
				$i++;
				continue;
			}

			unset($repairs);
			printf("Move Temp To Master %s (%d/%d)\n", $row->vehicle_device, ++$i, $totalvehicle);
			
			$this->db = $this->load->database("gpshistory2", TRUE);
			$table = strtolower($row->vehicle_device)."_gps";
			$tableinfo = strtolower($row->vehicle_device)."_info";
			
			$start = mktime();
			$now = mktime(0, 0, 0, date('n'), date('j', $start), date('Y'))-7*3600;
			$devices = explode("@", $row->vehicle_device);
					
			// gps
			$offset = 0;
			unset($isdelete);
			while(1)
			{
				$this->db->limit($maxdata, $offset);
				$this->db->order_by("gps_time","asc");
				$this->db->where("gps_name", $devices[0]);
				$this->db->where("gps_host", $devices[1]);
				$this->db->where("gps_time <=", date("Y-m-d H:i:s", $now));
				$q = $this->db->get($table);					
				$total = $q->num_rows();
							
				printf("=== jumlah move data tmp gps: %d, lama: %ds \n", $q->num_rows(), mktime()-$start);
				
				if ($q->num_rows() == 0) break;
				
				$historydb = $this->load->database("gpshistory", TRUE);
				
				$gpses = $q->result_array();
				$q->free_result();
				foreach($gpses as $gps)
				{
					unset($gps['gps_id']);
					$historydb->insert(strtolower($row->vehicle_device)."_gps", $gps);
					$isdelete = true;
				}
				
				if ($total < $maxdata) break;
				
				$offset += $maxdata;
			}
			
			if (isset($isdelete))
			{
				printf("=== delete tmp data\r\n");
				$this->dblama = $this->load->database("gpshistory2",TRUE);
				$this->dblama->order_by("gps_time","asc");
				$this->dblama->where("gps_name", $devices[0]);
				$this->dblama->where("gps_host", $devices[1]);
				$this->dblama->where("gps_time <=", date("Y-m-d H:i:s", $now));
				$qlama = $this->dblama->get($table);
				$tot_lama = $qlama->num_rows();
				$lim = $tot_lama - 2;
				
				if ($tot_lama > 2)
				{
					$this->dblama->order_by("gps_time","asc");
					$this->dblama->where("gps_name", $devices[0]);
					$this->dblama->where("gps_host", $devices[1]);
					$this->dblama->limit($lim);
					$this->dblama->where("gps_time <=", date("Y-m-d H:i:s", $now));
					$this->dblama->delete($table);
				}
				
			}

			// gps info
			unset($isdelete);

			$offset = 0;
			while(1)
			{
				$this->db->limit($maxdata, $offset);
				$this->db->order_by("gps_info_time","asc");
				$this->db->where("gps_info_device", $row->vehicle_device);
				$this->db->where("gps_info_time <=", date("Y-m-d H:i:s", $now));
				$q = $this->db->get($tableinfo);					
				$total = $q->num_rows();
							
				printf("=== jumlah move data tmp info: %d, lama: %ds \n", $q->num_rows(), mktime()-$start);
				
				if ($q->num_rows() == 0) break;
				
				$historydb = $this->load->database("gpshistory", TRUE);
				
				$gpses = $q->result_array();
				$q->free_result();
				foreach($gpses as $gps)
				{
					unset($gps['gps_info_id']);
					
					$historydb->insert(strtolower($row->vehicle_device)."_info", $gps);
					$isdelete = true;
				}
				
				if ($total < $maxdata) break;
				$offset += $maxdata;
			}
			
			if (isset($isdelete))
			{
				printf("=== delete tmp data\r\n");
				$this->dblama = $this->load->database("gpshistory2",TRUE);
				$this->dblama->order_by("gps_info_time","asc");
				$this->dblama->where("gps_info_device", $row->vehicle_device);
				$this->dblama->where("gps_info_time <=", date("Y-m-d H:i:s", $now));
				$qlama = $this->dblama->get($tableinfo);
				$tot_lama = $qlama->num_rows();
				$lim = $tot_lama - 2;
				
				if ($tot_lama > 2)
				{
					$this->dblama->order_by("gps_info_time","asc");
					$this->dblama->where("gps_info_device", $row->vehicle_device);
					$this->dblama->where("gps_info_time <=", date("Y-m-d H:i:s", $now));
					$this->dblama->limit($lim);
					$this->dblama->delete($tableinfo);					
				}
			}
			
			printf("=== selesai\n");	
		} //finish foreach
		
		printf("=== FINISH\n");	
	}
	
	function tmp_per_vehicle($vid=0, $maxdata=10000, $offset = 0)
	{
		
		$this->db->distinct();
		
		if ($vid != 0)
		{
			$this->db->where("vehicle_id", $vid);
		}
		
		$this->db->where("vehicle_status <>", 3);
		$this->db->where("vehicle_user_id <>", "860");
		$this->db->where("vehicle_user_id <>", "1095");
		$this->db->where("vehicle_user_id <>", "1122");
		$this->db->select("vehicle_type, vehicle_device, vehicle_info");
		$q = $this->db->get("vehicle");
		
		if ($q->num_rows() == 0) return;

		$rows = $q->result();
		
		$totalvehicle = count($rows);
		$i = 0;
		foreach($rows as $row)
		{
			if (($i+1) < $offset)
			{
				$i++;
				continue;
			}

			unset($repairs);
			printf("Move Temp To Master %s (%d/%d)\n", $row->vehicle_device, ++$i, $totalvehicle);
			
			$this->db = $this->load->database("gpshistory2", TRUE);
			$table = strtolower($row->vehicle_device)."_gps";
			$tableinfo = strtolower($row->vehicle_device)."_info";
			
			$start = mktime();
			$now = mktime(0, 0, 0, date('n'), date('j', $start), date('Y'))-7*3600;
			$devices = explode("@", $row->vehicle_device);
					
			// gps
			$offset = 0;
			unset($isdelete);
			while(1)
			{
				$this->db->limit($maxdata, $offset);
				$this->db->order_by("gps_time","asc");
				$this->db->where("gps_name", $devices[0]);
				$this->db->where("gps_host", $devices[1]);
				$this->db->where("gps_time <=", date("Y-m-d H:i:s", $now));
				$q = $this->db->get($table);					
				$total = $q->num_rows();
							
				printf("=== jumlah move data tmp gps: %d, lama: %ds \n", $q->num_rows(), mktime()-$start);
				
				if ($q->num_rows() == 0) break;
				
				$historydb = $this->load->database("gpshistory", TRUE);
				
				$gpses = $q->result_array();
				$q->free_result();
				foreach($gpses as $gps)
				{
					unset($gps['gps_id']);
					$historydb->insert(strtolower($row->vehicle_device)."_gps", $gps);
					$isdelete = true;
				}
				
				if ($total < $maxdata) break;
				
				$offset += $maxdata;
			}
			
			if (isset($isdelete))
			{
				printf("=== delete tmp data\r\n");
				$this->dblama = $this->load->database("gpshistory2",TRUE);
				$this->dblama->order_by("gps_time","asc");
				$this->dblama->where("gps_name", $devices[0]);
				$this->dblama->where("gps_host", $devices[1]);
				$this->dblama->where("gps_time <=", date("Y-m-d H:i:s", $now));
				$qlama = $this->dblama->get($table);
				$tot_lama = $qlama->num_rows();
				$lim = $tot_lama - 2;
				
				if ($tot_lama > 2)
				{
					$this->dblama->order_by("gps_time","asc");
					$this->dblama->where("gps_name", $devices[0]);
					$this->dblama->where("gps_host", $devices[1]);
					$this->dblama->limit($lim);
					$this->dblama->where("gps_time <=", date("Y-m-d H:i:s", $now));
					$this->dblama->delete($table);
				}
				
			}

			// gps info
			unset($isdelete);

			$offset = 0;
			while(1)
			{
				$this->db->limit($maxdata, $offset);
				$this->db->order_by("gps_info_time","asc");
				$this->db->where("gps_info_device", $row->vehicle_device);
				$this->db->where("gps_info_time <=", date("Y-m-d H:i:s", $now));
				$q = $this->db->get($tableinfo);					
				$total = $q->num_rows();
							
				printf("=== jumlah move data tmp info: %d, lama: %ds \n", $q->num_rows(), mktime()-$start);
				
				if ($q->num_rows() == 0) break;
				
				$historydb = $this->load->database("gpshistory", TRUE);
				
				$gpses = $q->result_array();
				$q->free_result();
				foreach($gpses as $gps)
				{
					unset($gps['gps_info_id']);
					
					$historydb->insert(strtolower($row->vehicle_device)."_info", $gps);
					$isdelete = true;
				}
				
				if ($total < $maxdata) break;
				$offset += $maxdata;
			}
			
			if (isset($isdelete))
			{
				printf("=== delete tmp data\r\n");
				$this->dblama = $this->load->database("gpshistory2",TRUE);
				$this->dblama->order_by("gps_info_time","asc");
				$this->dblama->where("gps_info_device", $row->vehicle_device);
				$this->dblama->where("gps_info_time <=", date("Y-m-d H:i:s", $now));
				$qlama = $this->dblama->get($tableinfo);
				$tot_lama = $qlama->num_rows();
				$lim = $tot_lama - 2;
				
				if ($tot_lama > 2)
				{
					$this->dblama->order_by("gps_info_time","asc");
					$this->dblama->where("gps_info_device", $row->vehicle_device);
					$this->dblama->where("gps_info_time <=", date("Y-m-d H:i:s", $now));
					$this->dblama->limit($lim);
					$this->dblama->delete($tableinfo);					
				}
			}
			
			printf("=== selesai\n");	
		} //finish foreach
		
		printf("=== FINISH\n");	
	}
	
	function history_csa($vid=0, $maxdata=10000, $offset = 0)
	{
		
		$this->db->distinct();
		
		if ($vid != 0)
		{
			$this->db->where("vehicle_id", $vid);
		}
		
		$this->db->select("vehicle_type, vehicle_device, vehicle_info");
		$q = $this->db->get("vehicle");
		
		if ($q->num_rows() == 0) return;

		$rows = $q->result();
		
		$totalvehicle = count($rows);
		$i = 0;
		foreach($rows as $row)
		{
			if (($i+1) < $offset)
			{
				$i++;
				continue;
			}

			unset($repairs);
			printf("Move Temp To Master %s (%d/%d)\n", $row->vehicle_device, ++$i, $totalvehicle);
			
			$this->db = $this->load->database("gpshistory", TRUE);
			$table = strtolower($row->vehicle_device)."_gps";
			$tableinfo = strtolower($row->vehicle_device)."_info";
			
			$start = mktime();
			$now = mktime(0, 0, 0, date('n'), date('j', $start), date('Y'))-7*3600;
			$devices = explode("@", $row->vehicle_device);
					
			// gps
			$offset = 0;
			unset($isdelete);
			while(1)
			{
				$this->db->limit($maxdata, $offset);
				$this->db->order_by("gps_time","asc");
				$this->db->where("gps_name", $devices[0]);
				$this->db->where("gps_host", $devices[1]);
				$this->db->where("gps_time <=", date("Y-m-d H:i:s", $now));
				$q = $this->db->get($table);					
				$total = $q->num_rows();
							
				printf("=== jumlah move data tmp gps: %d, lama: %ds \n", $q->num_rows(), mktime()-$start);
				
				if ($q->num_rows() == 0) break;
				
				$historydb = $this->load->database("gpshistorycsa", TRUE);
				
				$gpses = $q->result_array();
				$q->free_result();
				foreach($gpses as $gps)
				{
					unset($gps['gps_id']);
					$historydb->insert(strtolower($row->vehicle_device)."_gps", $gps);
					$isdelete = true;
				}
				
				if ($total < $maxdata) break;
				
				$offset += $maxdata;
			}
			
			if (isset($isdelete))
			{
				printf("=== delete tmp data\r\n");
				$this->dblama = $this->load->database("gpshistory",TRUE);
				$this->dblama->order_by("gps_time","asc");
				$this->dblama->where("gps_name", $devices[0]);
				$this->dblama->where("gps_host", $devices[1]);
				$this->dblama->where("gps_time <=", date("Y-m-d H:i:s", $now));
				$qlama = $this->dblama->get($table);
				$tot_lama = $qlama->num_rows();
				$lim = $tot_lama - 2;
				
				if ($tot_lama > 2)
				{
					$this->dblama->order_by("gps_time","asc");
					$this->dblama->where("gps_name", $devices[0]);
					$this->dblama->where("gps_host", $devices[1]);
					$this->dblama->limit($lim);
					$this->dblama->where("gps_time <=", date("Y-m-d H:i:s", $now));
					$this->dblama->delete($table);
				}
				
			}

			// gps info
			unset($isdelete);

			$offset = 0;
			while(1)
			{
				$this->db->limit($maxdata, $offset);
				$this->db->order_by("gps_info_time","asc");
				$this->db->where("gps_info_device", $row->vehicle_device);
				$this->db->where("gps_info_time <=", date("Y-m-d H:i:s", $now));
				$q = $this->db->get($tableinfo);					
				$total = $q->num_rows();
							
				printf("=== jumlah move data tmp info: %d, lama: %ds \n", $q->num_rows(), mktime()-$start);
				
				if ($q->num_rows() == 0) break;
				
				$historydb = $this->load->database("gpshistorycsa", TRUE);
				
				$gpses = $q->result_array();
				$q->free_result();
				foreach($gpses as $gps)
				{
					unset($gps['gps_info_id']);
					
					$historydb->insert(strtolower($row->vehicle_device)."_info", $gps);
					$isdelete = true;
				}
				
				if ($total < $maxdata) break;
				$offset += $maxdata;
			}
			
			if (isset($isdelete))
			{
				printf("=== delete tmp data\r\n");
				$this->dblama = $this->load->database("gpshistory",TRUE);
				$this->dblama->order_by("gps_info_time","asc");
				$this->dblama->where("gps_info_device", $row->vehicle_device);
				$this->dblama->where("gps_info_time <=", date("Y-m-d H:i:s", $now));
				$qlama = $this->dblama->get($tableinfo);
				$tot_lama = $qlama->num_rows();
				$lim = $tot_lama - 2;
				
				if ($tot_lama > 2)
				{
					$this->dblama->order_by("gps_info_time","asc");
					$this->dblama->where("gps_info_device", $row->vehicle_device);
					$this->dblama->where("gps_info_time <=", date("Y-m-d H:i:s", $now));
					$this->dblama->limit($lim);
					$this->dblama->delete($tableinfo);					
				}
			}
			
			printf("=== selesai\n");	
		} //finish foreach
		
		printf("=== FINISH\n");	
	}
	
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
