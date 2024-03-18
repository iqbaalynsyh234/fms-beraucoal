<?php
include "base.php";

class Uploadarchive extends Base {
	
		var $period1;
		var $period2;
		var $tblhist;
		var $tblinfohist;
		var $otherdb;
	
	function Uploadarchive()
	{
		parent::Base();
		
		$this->load->model("gpsmodel");
		$this->load->model("vehiclemodel");
		$this->load->model("configmodel");
		$this->load->model("historymodel");		
		$this->load->helper('url');
                $this->load->helper('form');
                $this->load->helper('file');
		$this->load->helper('download');
		
		if (! isset($this->sess->user_company)){
		redirect(base_url());
		}		
	}
	
	function mn_upload()
	{
		$this->params['content'] = $this->load->view("archive/upload_archive", $this->params, true);
		$this->load->view("templatesess", $this->params);
	}
	
	function upload_data()
	{
		$config['upload_path'] = './temp_upload/';
		$config['allowed_types'] = 'csv';
		$config['max_size']	= '100';
		$config['max_width']  = '1024';
		$config['max_height']  = '768';

		$gps = array();
		
		$this->load->library('upload', $config);

		if ( ! $this->upload->do_upload())
		{
			$this->params['error'] = $this->upload->display_errors();
			$this->params['content'] = $this->load->view("mod_tupperware/upload_error", $this->params, true);
			$this->load->view("templatesess", $this->params);
		}
		else
		{
			$data = $this->upload->data();
			$file_name = $data["file_name"];
			$i=0;
			
			$config['upload_path'] = './temp_upload/';
			$config['allowed_types'] = 'csv';
			
			$this->load->library('csvreader');
			$filePath = './temp_upload/'.$file_name;
			
			$datacsv = $this->csvreader->parse_file($filePath);	
			$total_data  = count($datacsv);
			
			foreach ($datacsv as $v=>$key)
			{
				$gps[$i]->gps_latitude_real_fmt = $key["Lat"];
				$gps[$i]->gps_longitude_real_fmt = $key["Lng"];
				$map_params[] = array($gps[$i]->gps_longitude_real_fmt, $gps[$i]->gps_latitude_real_fmt);
				$i++;
			}
			
			
			
			$uniqid = md5( uniqid() );
			
			$uniqid = md5( uniqid() );
			$this->db = $this->load->database("default", TRUE);
			unset($insert);
						
			$insert['log_created'] = date("Y-m-d H:i:s");
			$insert['log_creator'] = $this->sess->user_id;
			$insert['log_type'] = 'mapparams'.$uniqid;
			$insert['log_ip'] = "";			
			$insert['log_data'] = json_encode($map_params);
			$insert['log_version'] = "desktop";
			$insert['log_target'] = "";
			$this->db->insert("log", $insert);			
			
			$this->params["initmap"] = $this->load->view('initmap', $this->params, true);
			$this->params['uniqid'] = isset($uniqid) ? $uniqid : "";
			$this->params['data'] = $gps;
			$this->params['content'] = $this->load->view("archive/mapresult", $this->params, true);
			$this->load->view("templatesess", $this->params);
		}
		
		
		
	}
}

/* End of file driver.php */
/* Location: ./system/application/controllers/transporter/driver.php */
