<?php
include "base.php";

class CCTV extends Base {

	function CCTV()
	{
		parent::Base();	
		$this->load->model("gpsmodel");
	}
	
	function grab($id)
	{
		if (! function_exists("curl_init")) 
		{
			$callback['isempty'] = true;
			return json_encode($callback);
		}
		
		$this->db->where("cctv_id", $id);
		$q = $this->db->get("cctv");
		
		if ($q->num_rows() == 0)
		{
			$callback['isempty'] = true;
			echo json_encode($callback);
			return;
		}
		
		$row = $q->row();		
		if ($row->cctv_src == "lewatmana.com")
		{		
			$url = "http://lewatmana.com/cam/".$row->cctv_tag."/".$row->cctv_alias."/";
			
			$ch = curl_init();
		
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
			$html = curl_exec($ch);
		
			curl_close($ch);
			if (preg_match("/<img\s+src=[\"\'](.*)[\"\']\s+class=[\"\']cam-image[\"\']\s+id=[\"\']cam-image-".$row->cctv_tag."[\"\']/", $html, $matches))
			{
				$callback['isempty'] = false;
				$callback['cctv'] = $row;
				$callback['image'] = base_url()."cctv/live/".stringtohex($matches[1]);

				echo json_encode($callback);
				return;
			}	
			
			if (preg_match("/<img\s+id=[\"\']cam-image-".$row->cctv_tag."[\"\']\s+src=[\"\'](.*)[\"\']\s+class=[\"\']cam-image[\"\']/", $html, $matches))
			{
				$callback['isempty'] = false;
				$callback['cctv'] = $row;
				$callback['image'] = $matches[1];

				echo json_encode($callback);
				return;				
			}
			
			$callback['isempty'] = true;
			echo json_encode($callback);

			return;
		}
		
		$callback['isempty'] = true;
		
		echo json_encode($callback);
		return;
	}
	
	function live($url)
	{
			$url = hextostring($url); 
			$ch = curl_init();
		
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
			$html = curl_exec($ch);
		
			curl_close($ch);
			
			header("content-type: image/jpg");
			echo $html;
		
	}
	
	function index()
	{
		$this->db->where("cctv_status", 1);
		$q = $this->db->get("cctv");
		
		$rows = $q->result();
		
		$this->params['rows'] = $rows;
		$this->load->view("cctv/list", $this->params);
	}
	
	function form()
	{
		$this->load->view("cctv/form");
	}
	
	function save()
	{
		unset($insert);
		
		foreach($_POST as $key=>$val)
		{
			if (! $val) die("error");
			
			$insert[$key] = mysql_escape_string($val);
		}
		
		$insert['cctv_src'] = "lewatmana.com";
		$insert['cctv_name'] = $insert['cctv_alias'];
		$insert['cctv_status'] = 1;
		
		$this->db->insert("cctv", $insert);
		$url = sprintf("location: %scctv/form", base_url());
		
		header($url);
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
