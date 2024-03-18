<?php
include "base.php";

class Ototrack_contact extends Base 
{
	function ototrack_contact()
	{
		parent::Base();
	}
	
	function index()
	{
		if ($this->sess->user_type==2)
		{
			$this->db->select('*');
			$this->db->from("user");
			$this->db->where("user_agent",$this->sess->user_agent);
			$this->db->where("user_type","3");
			$q = $this->db->get();
			$rows = $q->result();
			
			$this->params["title"] = "Contact Info";
			$this->params["data"] = $rows;
			$this->params["content"] = $this->load->view("oto-track/info_contact.php", $this->params, true);
			$this->load->view("templatesess", $this->params);
		}
	}
	
	function info()
	{
		$this->load->view("oto-track/info");
	}
}