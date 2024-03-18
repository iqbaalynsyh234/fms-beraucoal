<?php
include "base.php";

class Comment extends Base {

	function __construct()
	{
		parent::Base();
		$this->load->library('email');
		if (! isset($this->sess->user_company)){
		redirect(base_url());
		}
	}

	function index(){

		if (!$this->sess->user_company){
			redirect(base_url());
		}

		$vid = isset($_POST['id']) ? $_POST['id'] : "";

		$this->db->select("vehicle_id, vehicle_user_id,vehicle_device, vehicle_name, vehicle_no");
		$this->db->where("vehicle_id", $vid);
		$qv             = $this->db->get("vehicle");
		$rowv           = $qv->row();
		$params['rowv'] = $rowv;

		if ($qv->num_rows > 0){
			$rowv = $qv->row();
			$userid = $rowv->vehicle_user_id;

			if ($userid == "1933"){ //khusus ssi
				$comment_table = "ssi_vehicle_comment";
			}else{
				$comment_table = "vehicle_comment";
			}
		}
		$params['comment_table'] = $comment_table;

		$this->dbtransporter=$this->load->database("transporter", TRUE);
		$this->dbtransporter->select("comment_id,comment_title,comment_creator_name,comment_datetime");
		$this->dbtransporter->order_by("comment_datetime", "desc");
		$this->dbtransporter->where("comment_flag", 0);
		$this->dbtransporter->where("comment_status", 0);
		$this->dbtransporter->where("comment_vehicle_id", $vid);
		$q = $this->dbtransporter->get($comment_table);
		$row = $q->row();
		$params['row'] = $row;

		$html = $this->load->view("transporter/comment/info_comment", $params, true);

		$callback['html'] = $html;
		$callback['error'] = false;

		$this->db->cache_delete_all();
		echo json_encode($callback);

	}

	function commentdashboard(){

		if (!$this->sess->user_company){
			redirect(base_url());
		}

		$vid = isset($_POST['id']) ? $_POST['id'] : "";

		$this->db->select("vehicle_id, vehicle_user_id,vehicle_device, vehicle_name, vehicle_no");
		$this->db->where("vehicle_id", $vid);
		$qv             = $this->db->get("vehicle");
		$rowv           = $qv->row();
		$params['rowv'] = $rowv;

		if ($qv->num_rows > 0){
			$rowv = $qv->row();
			$userid = $rowv->vehicle_user_id;

			if ($userid == "1933"){ //khusus ssi
				$comment_table = "ssi_vehicle_comment";
			}else{
				$comment_table = "vehicle_comment";
			}
		}
		$params['comment_table'] = $comment_table;

		$this->dbtransporter=$this->load->database("transporter", TRUE);
		$this->dbtransporter->select("comment_id,comment_title,comment_creator_name,comment_datetime");
		$this->dbtransporter->order_by("comment_datetime", "desc");
		$this->dbtransporter->where("comment_flag", 0);
		$this->dbtransporter->where("comment_status", 0);
		$this->dbtransporter->where("comment_vehicle_id", $vid);
		$q                 = $this->dbtransporter->get($comment_table);
		$row               = $q->row();
		$params['row']     = $row;

		$html              = $this->load->view("dashboard/comment", $params, true);

		$callback['html']  = $html;
		$callback['error'] = false;

		$this->db->cache_delete_all();
		echo json_encode($callback);
	}

	function save_comment()
	{
		if (! isset($this->sess->user_company)){
			redirect(base_url());
		}
		$this->dbtransporter=$this->load->database("transporter", TRUE);

		$vid = isset($_POST['vid']) ? trim($_POST['vid']) : "";
		$vname = isset($_POST['vname']) ? trim($_POST['vname']) : "";
		$vno = isset($_POST['vno']) ? trim($_POST['vno']) : "";
		$vdevice = isset($_POST['vdevice']) ? trim($_POST['vdevice']) : "";

		$title = isset($_POST['title']) ? trim($_POST['title']) : "";
		$status = isset($_POST['status']) ? trim($_POST['status']) : 0;
		$comment_table = isset($_POST['comment_table']) ? trim($_POST['comment_table']) : 0;
		$datetime = date("Y-m-d H:i:s");

		$error = "";

		if ($title == "")
		{
			$error .= "- Please fill Comment \n";
		}
		if ($vid == "")
		{
			$error .= "- No data Vehicle ID ! \n";
		}
		if ($vname == "")
		{
			$error .= "- No data Vehicle Name ! \n";
		}
		if ($vno == "")
		{
			$error .= "- No data Vehicle No ! \n";
		}
		if ($vdevice == "")
		{
			$error .= "- No data Vehicle Device ! \n";
		}
		if ($error != "")
		{
			$callback['error'] = true;
			$callback['message'] = $error;

			echo json_encode($callback);
			return;
		}

		unset($data);
		$data['comment_vehicle_id'] = $vid;
		$data['comment_vehicle_name'] = $vname;
		$data['comment_vehicle_no'] = $vno;
		$data['comment_vehicle_device'] = $vdevice;
		$data['comment_title'] = $title;
		$data['comment_datetime'] = $datetime;
		$data['comment_creator'] = $this->sess->user_id;
		$data['comment_creator_name'] = $this->sess->user_name;
		$data['comment_status'] = $status;
		$data['comment_vehicle_user_id'] = $this->sess->user_id;

			if($vid){
				$this->dbtransporter->select("comment_id, comment_vehicle_id");
				$this->dbtransporter->order_by("comment_datetime","desc");
				$this->dbtransporter->where("comment_vehicle_id", $vid);
				$this->dbtransporter->where("comment_flag", 0);
				$this->dbtransporter->where("comment_status", 0);
				$this->dbtransporter->limit(1);
				$q = $this->dbtransporter->get($comment_table);
				$row = $q->row();

				if(count($row) > 0){
				/*
						unset($mail);
						$contentmail = $title;
						$this->load->library('email');
						$this->email->set_newline('\r\n');
						$this->email->clear();
						$this->email->from('no-reply@lacak-mobil.com');
						$this->email->to('monitoring@lacak-mobil.com');
						$this->email->cc('budiyanto@lacak-mobil.com');
						$this->email->subject("["." "."Comment Alert: ".$vno." "."]"." ".$this->sess->user_name.": "."Status GPS Merah");
						$this->email->message($contentmail);
						$this->email->send();
			*/
					$this->dbtransporter->select("comment_id, comment_vehicle_id");
					$this->dbtransporter->order_by("comment_datetime","desc");
					$this->dbtransporter->where("comment_vehicle_id", $vid);
					$this->dbtransporter->where("comment_flag", 0);
					$this->dbtransporter->where("comment_status", 0);
					$this->dbtransporter->limit(1);
					$this->dbtransporter->update($comment_table, $data);

					$callback['error'] = false;
					$callback['message'] = "Comment Successfully Updated";
					echo json_encode($callback);

					return;
				}

				else{
				/*
						unset($mail);
						$contentmail = $title;
						$this->load->library('email');
						$this->email->set_newline('\r\n');
						$this->email->clear();
						$this->email->from('no-reply@lacak-mobil.com');
						$this->email->to('monitoring@lacak-mobil.com');
						$this->email->cc('budiyanto@lacak-mobil.com');
						$this->email->subject("["." "."Comment Alert: ".$vno." "."]"." ".$this->sess->user_name.": "."Status GPS Merah");
						$this->email->message($contentmail);
						$this->email->send();
				*/
					$this->dbtransporter->insert($comment_table, $data);

					$callback['error'] = false;
					$callback['message'] = "Comment Successfully Submitted";
					echo json_encode($callback);

					return;
				}


			}
	}

}
