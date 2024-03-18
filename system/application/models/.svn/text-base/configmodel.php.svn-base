<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class ConfigModel extends Model {
	var $earthRadius = 6371;
	
	function ConfigModel () 
	{				
		parent::Model();		
	}	

	function get()
	{
		$q = $this->db->get("config");
		if ($q->num_rows() == 0) return;		

		$rows = $q->result();
		for($i=0; $i < count($rows); $i++)
		{
			$configs[$rows[$i]->config_name] = $rows[$i]->config_value;
		}
		
		return $configs;
	}	
	
	function getMaxHistory()
	{
		$this->db->where("config_name", "maxhist");
		$q = $this->db->get("config");
		
		if ($q->num_rows() == 0) return 3;
		
		$row = $q->row();
		return $row->config_value;
	}
}

