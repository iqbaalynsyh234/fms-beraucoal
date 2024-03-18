<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Dashboardmanagemodel extends Model {
	
	function Dashboardmanagemodel(){
		parent::Model();
	}
	
	function getvehicle_byowner()
	{
		$this->db->order_by("vehicle_no","asc");
		if($this->sess->user_login == "demo_transporter"){
			$this->db->where("vehicle_user_id",1933);
		}else{
			$this->db->where("vehicle_user_id",$this->sess->user_id);
		}
		$this->db->where("vehicle_status <>", 3);
		$q = $this->db->get("vehicle");
		$rows = $q->result();
		return $rows;
    }
	
	function getcompany_byowner($id)
	{
		$this->db->select("company_id,company_name");
		$this->db->order_by("company_name","asc");
		$this->db->where("company_created_by",$id);
		$this->db->where("company_flag", 0);
		$q = $this->db->get("company");
		$rows = $q->result();
		return $rows;
    }
	
	function getsj_byadmin($id)
	{
		$this->db->limit(700);
		$this->db->order_by("sj_id","desc");
		if($this->sess->user_login == "demo_transporter"){
			$this->db->where("sj_user_id",1147);
		}else{
			$this->db->where("sj_user_id",$id);
		}
		$this->db->where("sj_flag",0);
		$q = $this->db->get("sj");
		$rows = $q->result();
		return $rows;
    }
	
	function getsj_byid($id)
	{
		$this->db->order_by("sj_id","desc");
		$this->db->where("sj_id",$id);
		$this->db->where("sj_flag",0);
		$q = $this->db->get("sj");
		$rows = $q->row();
		return $rows;
    }
	
	function getsubcompany_byparent($id)
	{
		$this->db->order_by("subcompany_name","asc");
		$this->db->where("subcompany_parent",$id);
		$this->db->where("subcompany_status",1);
		$this->db->where("subcompany_flag",0);
		$q = $this->db->get("subcompany");
		$rows = $q->result();
		return $rows;
    }
	
	function getgroup_byparent($id)
	{
		$this->db->order_by("group_name","asc");
		$this->db->where("group_subcompany",$id);
		$this->db->where("group_status",1);
		$this->db->where("group_flag",0);
		$q = $this->db->get("group");
		$rows = $q->result();
		return $rows;
    }
	
	function getsubgroup_byparent($id)
	{
		$this->db->order_by("subgroup_name","asc");
		$this->db->where("subgroup_subcompany",$id);
		$this->db->where("subgroup_status",1);
		$this->db->where("subgroup_flag",0);
		$q = $this->db->get("subgroup");
		$rows = $q->result();
		return $rows;
    }
	
	function getcompanyall($id)
	{	
		$this->db->select("company_id,company_name");
		$this->db->order_by("company_name","asc");
		$this->db->where("company_created_by",$id);
		$this->db->where("company_status",1);
		$this->db->where("company_flag",0);
		$q = $this->db->get("company");
		$rows = $q->result();
		return $rows;
    }
	
	function getsubcompanyall($id)
	{
		$this->db->select("subcompany_id,subcompany_name");
		$this->db->order_by("subcompany_name","asc");
		$this->db->where("subcompany_creator",$id);
		$this->db->where("subcompany_status",1);
		$this->db->where("subcompany_flag",0);
		$q = $this->db->get("subcompany");
		$rows = $q->result();
		return $rows;
    }
	
	function getgroupall($id)
	{
		$this->db->select("group_id,group_name");
		$this->db->order_by("group_name","asc");
		$this->db->where("group_creator",$id);
		$this->db->where("group_status",1);
		$this->db->where("group_flag",0);
		$q = $this->db->get("group");
		$rows = $q->result();
		return $rows;
    }
	
	function getsubgroupall($id)
	{
		$this->db->select("subgroup_id,subgroup_name");
		$this->db->order_by("subgroup_name","asc");
		$this->db->where("subgroup_creator",$id);
		$this->db->where("subgroup_status",1);
		$this->db->where("subgroup_flag",0);
		$q = $this->db->get("subgroup");
		$rows = $q->result();
		return $rows;
    }
	
	
	
}
