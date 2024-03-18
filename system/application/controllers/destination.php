<?php
include "base.php";

class Destination extends Base {

	function Destination()
	{
		parent::Base();
		
	}
	
	
	function index($field ='all', $keyword='all', $offset=0)
	{
		
		$this->dbkim = $this->load->database('kim', true);
		
		switch($field){
			case "destination_name":
				$this->dbkim->where("destination_name1 LIKE '%".$keyword."%'", null);
			break;
			case "destination_vehicle":
				$this->dbkim->where("destination_vehicle LIKE '%".$keyword."%'", null);
			break;
		}
		
		$this->dbkim->select("*");
		$this->dbkim->from("destination");
		$this->dbkim->orderby("destination_name1","asc");
		$q = $this->dbkim->get("", $this->config->item("limit_record"), $offset);
		$rows = $q->result();
		
		switch($field){
			case "destination_name1":
				$this->dbkim->where("destination_name1 LIKE '%".$keyword."%'", null);
			break;
		}
		
		$total = count($rows);
		$config['uri_segment'] = 4;
		$config['base_url'] = base_url()."destination/index/".$field."/".$keyword;
		$config['total_rows'] = $total;
		$config['per_page'] = $this->config->item("limit_records");
		
		$this->pagination->initialize($config);
		
		$rows_vehicle = $this->get_vehicle();
		
		$this->params["rows_vehicle"] = $rows_vehicle;
		$this->params["title"] = "Destination";
		$this->params["field"] = $field;
		$this->params["keyword"] = $keyword;
		$this->params["paging"] = $this->pagination->create_links();
		$this->params["offset"] = $offset;
		$this->params["total"] = $total;
		$this->params["data"] = $rows;
		$this->params["content"] = $this->load->view("kim/destination/destination_list.php", $this->params, true);
		$this->load->view("templatesess", $this->params);
		
		$this->dbkim->close();
		
	}
	
	function add()
	{
		$this->params["title"] = "Manage Driver - ADD";		
		$this->params['content'] = $this->load->view("kim/destination/destination_add", $this->params, true);
		$this->load->view("templatesess", $this->params);
	}
	
	function save()
	{
		$this->dbkim = $this->load->database("kim", true);
		
		$destination_name1 = isset($_POST['destination_name1']) ? $_POST['destination_name1'] : "";
		
		$error = "";
		unset($data);
		
		$data['destination_name1'] = $destination_name1;
		
		$this->dbkim->insert("destination", $data);
		$callback["error"] = false;
		$callback["message"] = "Add Destination Success";
		$callback["redirect"] = base_url()."destination";
		
		echo json_encode($callback);
		$this->dbkim->close();
		return;
		
	}
	
	function edit()
	{
		$destination_id = $this->uri->segment(3);
		if ($destination_id) {
		$this->dbkim = $this->load->database("kim", true);
		$this->dbkim->select("*");
		$this->dbkim->from("destination");
		$this->dbkim->where("destination_id", $destination_id);
		$q = $this->dbkim->get();
		
		if ($q->num_rows == 0) { return; }
			$row = $q->row();
			$this->params['row'] = $row;
			$this->params['title'] = "Edit Destination";
			$this->params['content'] = $this->load->view("kim/destination/destination_edit", $this->params, true);
			$this->load->view("templatesess", $this->params);
			$this->dbkim->close();
		} 
		else 
		{
			$this->dbkim->close();
			redirect(base_url()."destination");
		}
	}
	
	function edit_destination()
	{
		$destination_id = $this->uri->segment(3);
		if ($destination_id) {
		$this->dbkim = $this->load->database("kim", true);
		$this->dbkim->select("*");
		$this->dbkim->from("destination");
		$this->dbkim->where("destination_id", $destination_id);
		$q = $this->dbkim->get();
		
		if ($q->num_rows == 0) { return; }
			$row = $q->row();
			$this->params['row'] = $row;
			$this->params['title'] = "Edit Destination";
			$this->params['content'] = $this->load->view("kim/destination/destination_edit", $this->params, true);
			$this->load->view("templatesess", $this->params);
			$this->dbkim->close();
		} 
		else 
		{
			$this->dbkim->close();
			redirect(base_url()."destination");
		}
	}
	
	
	
	
	function update()
	{
		$this->dbkim = $this->load->database('kim', true);
		
		$destination_id = $this->input->post('destination_id');
		$destination_name1 = $this->input->post('destination_name1');
		$destination_vehicle = $this->input->post('destination_vehicle');

		
		$data = array('destination_name1' => $destination_name1,
					  'destination_vehicle' => $destination_vehicle
					  );
		
		$this->dbkim->where('destination_id', $destination_id);
		$this->dbkim->update('destination', $data);
		$this->dbkim->close();
		
		redirect (base_url()."destination", 'refresh');
		
	}
	
	function update_dest()
	{
		$this->dbtrans = $this->load->database('transporter', true);
		
		$destination_id = $this->input->post('destination_id');
		
		$destination_name1 = $this->input->post('destination_name1');

		
		$data = array('destination_name1' => $destination_name1
					  );
		
		$this->dbtrans->where('destination_id', $destination_id);
		$this->dbtrans->update('destination_reksa', $data);
		$this->dbtrans->close();
		
		//redirect (base_url()."trackers#atop", 'refresh');
		$callback["error"] = false;
		$callback["message"] = "SUKSES";
		
		echo json_encode($callback);
		return;
	}
	
	function delete()
	{
		$this->dbkim = $this->load->database('kim', true);
		
		$destination_id = $this->uri->segment(3);
		
		$this->dbkim->where('destination_id', $destination_id);
		$this->dbkim->delete('destination');
		$this->dbkim->close();
		
		redirect (base_url()."destination", 'refresh');
		
	}
	
	function get_vehicle(){
	
		$this->db->select("*");
		$this->db->from("vehicle");
		
		if ($this->sess->user_type != 1)
		{
			$this->db->where("vehicle_user_id", $this->sess->user_id);
			//$this->db->or_where("vehicle_company", 38);
			
			//$this->db->where("vehicle_user_id", 1784);
			
			//$this->db->where("vehicle_active_date2 >=", date("Ymd"));
		}
		
		$q= $this->db->get();
		$row_vehicle = $q->result();
		return $row_vehicle;
	}
	
	function take_vehicle()
	{
		$destination_id = $this->input->post("id");
		$this->dbkim = $this->load->database("kim", true);
		$this->dbkim->select("*");
		$this->dbkim->from("destination");
		$this->dbkim->where("destination_id", $destination_id);
		$q = $this->dbkim->get();
		$row = $q->row();
		
		$rows_vehicle = $this->get_vehicle();
		$this->dbkim->close();
		
		$params['rows_vehicle'] = $rows_vehicle;
		$params['row'] = $row;
		$params["title"] = "Destination Vehicle";		
		$params["destination_id"] = $destination_id;
		$html = $this->load->view("kim/destination/destination_form_vehicle", $params, true);
		$callback['error'] = false;
		$callback['html'] = $html;		
		echo json_encode($callback);
	}
	
	function save_take_vehicle()
	{
		$destination_id = $this->input->post('destination_id');
		$destination_vehicle = $this->input->post('destination_vehicle');
		$data = array("destination_vehicle"=>$destination_vehicle);
		
		$this->dbkim = $this->load->database("kim", true);
		$this->dbkim->where('destination_id', $destination_id);
		$this->dbkim->update('destination', $data);
		$this->dbkim->close();
		
		redirect (base_url()."destination", 'refresh');
		
	}
	
	function driver_info_detail()
	{
		$driver_id = $this->input->post("id");
		$this->dbkim = $this->load->database("kim", true);
		$this->dbkim->select("*");
		$this->dbkim->from("driver");
		$this->dbkim->where("driver_id", $driver_id);
		$q = $this->dbkim->get();
		$row = $q->row();
		
		$rows_vehicle = $this->get_vehicle();
		$this->dbkim->close();
		
		$params['rows_vehicle'] = $rows_vehicle;
		$params['row'] = $row;
		$params["title"] = "Drive Vehicle";		
		$params["driver_id"] = $driver_id;
		$html = $this->load->view("kim/driver/driver_info_detail", $params, true);
		$callback['error'] = false;
		$callback['html'] = $html;		
		echo json_encode($callback);
	}
	
	function changephoto()
	{
		$id = $this->input->post("id");
		$this->dbkim = $this->load->database("kim", true);		
		$this->dbkim->where("driver_id", $id);
		$q = $this->dbkim->get("driver");
		
		if ($q->num_rows() == 0)
		{
			$callback['error'] = true;	
			echo json_encode($callback);
			return;
		}
		
		$row = $q->row();
		
		$params['row'] = $row;
		$html = $this->load->view("kim/driver/driver_changephoto", $params, true);		
		
		$callback['html'] = $html;
		$callback['error'] = false;	
		
		echo json_encode($callback);
	}
	
	function savephoto($id){
		$error = "";
		$msg = "";
		$fileElementName = 'fileToUpload';
		if(!empty($_FILES[$fileElementName]['error']))
		{
			switch($_FILES[$fileElementName]['error'])
			{
	
				case '1':
					$error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
					break;
				case '2':
					$error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
					break;
				case '3':
					$error = 'The uploaded file was only partially uploaded';
					break;
				case '4':
					$error = 'No file was uploaded.';
					break;
	
				case '6':
					$error = 'Missing a temporary folder';
					break;
				case '7':
					$error = 'Failed to write file to disk';
					break;
				case '8':
					$error = 'File upload stopped by extension';
					break;
				case '999':
				default:
					$error = 'No error code avaiable';
			}
		}elseif(empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] == 'none')
		{
			$error = 'No file was uploaded..';
		}else 
		{
			$allowedExtensions = array("jpg","jpeg","gif","png");
			
			if (!in_array(end(explode(".",strtolower($_FILES[$fileElementName]['name']))),$allowedExtensions)) {
			    $error = 'Invalid extension file..';
			}else{
				@mkdir($this->config->item("driver_photo_path"));
				$filename = $this->config->item("driver_photo_path") . str_replace(" ", "_", $_FILES[$fileElementName]['name']);
				if(move_uploaded_file($_FILES[$fileElementName]['tmp_name'], $filename)){
					
					$data["driver_pict"] = str_replace(" ", "_", $_FILES[$fileElementName]['name']);
					$this->dbkim = $this->load->database("kim", true);	
					$this->dbkim->where("driver_id", $id);
					$this->dbkim->update("driver", $data);
					
					$msg .= "Photo has been updated";
					//for security reason, we force to remove all uploaded file
					@unlink($_FILES[$fileElementName]);		
				}else{
					$error = 'Failed upload photo..';
				}
			}
		}		
		echo "{";
		echo				"error: '" . $error . "',\n";
		echo				"msg: '" . $msg . "'\n";
		echo "}";
	}
	
	
	//destination detail
	function destination_info_detail()
	{
		$destination_id = $this->input->post("id");
		$this->dbtrans = $this->load->database('transporter', TRUE);
		$this->dbtrans->select("*");
		$this->dbtrans->from("destination_reksa");
		$this->dbtrans->where("destination_id", $destination_id);
		$q = $this->dbtrans->get();
		$row = $q->row();
		
		
		$rows_vehicle = $this->get_vehicle();
		$this->dbtrans->close();
		
		$params['rows_vehicle'] = $rows_vehicle;
		$params['row'] = $row;
		$params["title"] = "Destination Vehicle";		
		$params["destination_id"] = $destination_id;
		$html = $this->load->view("reksaprabawa/destination/destination_info_detail", $params, true);
		$callback['error'] = false;
		$callback['html'] = $html;		
		echo json_encode($callback);
	}
	//end
	
}

/* End of file driver.php */
/* Location: ./system/application/controllers/driver.php */