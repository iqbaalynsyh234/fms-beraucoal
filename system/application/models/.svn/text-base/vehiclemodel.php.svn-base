<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class VehicleModel extends Model {
	
	function VehicleModel () 
	{				
		parent::Model();		
	}	
	
	function getAllGroups($parent=0, $groups, $grpprocessed)
	{
		$this->db->where("group_status", 1);
		$this->db->where("group_parent", $parent);
		$q = $this->db->get("group");
		
		$rows = $q->result();
		for($i=0; $i < count($rows); $i++)
		{
			if (in_array($rows[$i]->group_id, $grpprocessed)) continue;
			
			$grpprocessed[] = $rows[$i]->group_id;			
			$groups[$rows[$i]->group_id] = array();
			
			$this->getAllGroups($rows[$i]->group_id, &$groups[$rows[$i]->group_id], &$grpprocessed);
		}
	}
	
	function getChilds($tree, $id, $childs, $found=false)
	{
		if (count($tree) == 0) return;
		
		foreach($tree as $key=>$node)
		{
			if ($found)
			{
				$childs[] = $key;
			}
			
			if ($key == $id)
			{				
				$this->getChilds($node, $id, &$childs, true);
				continue;
			}
			
			$this->getChilds($node, $id, &$childs, $found);
		}		
	}
	
	function getChildIds($parent, $childs)
	{
		$this->db->where("group_status", 1);
		$this->db->where("group_parent", $parent);		
		$q = $this->db->get("group");
		
		if ($q->num_rows() == 0) return;
		
		$rows = $q->result();
		for($i=0; $i < count($rows); $i++)
		{
			if (in_array($rows[$i]->group_id, $childs)) continue;
			
			$childs[] = $rows[$i]->group_id;			
			$this->getChildIds($rows[$i]->group_id, &$childs);
		}
	}
	
	
	function getVehicleIds4Group($grp, $vehicleids, $groups, $companyid)
	{
        $this->db->select("vehicle_id");        
        $this->db->where("vehicle_company", $companyid);
		$this->db->where("vehicle_group", $grp);
            
		$q = $this->db->get("vehicle");

		$rowvehicles = $q->result();

		for($i=0; $i < count($rowvehicles); $i++)
		{
			if (in_array($rowvehicles[$i]->vehicle_id, $vehicleids)) continue;
			
			$vehicleids[] = $rowvehicles[$i]->vehicle_id;
		}
		
		// load childs
		
		$this->db->where_not_in("group_id", $groups);
		$this->db->where("group_parent", $grp);
		$q = $this->db->get("group");		
		
		$rows = $q->result();
		for($i=0; $i < count($rows); $i++)
		{
			$groups[] = $rows[$i]->group_id;
			$this->getVehicleIds4Group($rows[$i]->group_id, &$vehicleids, &$groups, $companyid);
		}
	}
	
	function getVehicleIds4Admin($companyid)
	{
		$this->db->select("vehicle_id");        
		$this->db->where("vehicle_company", $companyid);
		
		$q = $this->db->get("vehicle");
		$vehicleids[] = 0;

		$rowvehicles = $q->result();
		for($i=0; $i < count($rowvehicles); $i++)
		{
			if (in_array($rowvehicles[$i]->vehicle_id, $vehicleids)) continue;
			
			$vehicleids[] = $rowvehicles[$i]->vehicle_id;
		}

		return $vehicleids;
	}

	function getVehicleIds($userid=0, $groupid=0, $companyid=0)
	{
		if ($userid == 0)
		{
			$userid = $this->sess->user_id;
			$groupid = $this->sess->user_group;
			$companyid = $this->sess->user_company;
		}
		
			if ($groupid)
			{
				$vehicleids[] = 0;
				$groups[] = $groupid;
				$this->getVehicleIds4Group($groupid, &$vehicleids, &$groups, $companyid);
			}
			else
			{
				$vehicleids = $this->getVehicleIds4Admin($companyid);
			}
            
			// ambil vehicle sendiri

			$this->db->where("vehicle_user_id", $userid);
			$q = $this->db->get("vehicle");

			$rowvehicles = $q->result();

			for($i=0; $i < count($rowvehicles); $i++)
			{
				if (in_array($rowvehicles[$i]->vehicle_id, $vehicleids)) continue;
				
				$vehicleids[] = $rowvehicles[$i]->vehicle_id;
			}

			return $vehicleids;
	}	
}

