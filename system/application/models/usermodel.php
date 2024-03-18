<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class UserModel extends Model {
	
	function UserModel () 
	{				
		parent::Model();		
	}	
	
	function getIdsByMobile($mobiles)
	{
		$smobiles = implode(";", $mobiles);
		$mobiles = valid_mobiles($smobiles);
		
		$this->db->where_in("user_mobile");
		$q = $this->db->get("user");
		
		if ($q->num_rows() == 0) return FALSE;
		
		$rows = $q->result();
		foreach($rows as $row)
		{
			$hps = valid_mobiles($row->user_mobile);
			if ($hps === FALSE) continue;			
			if (count(array_intersect($mobiles, $hps)) == 0) continue;
			
			$ids[] = $row->user_id;
		}
		
		if (! isset($ids)) return FALSE;
		
		return $ids;
	}
}

