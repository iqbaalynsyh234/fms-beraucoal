<?php
include "base.php";

class Telegram extends Base {

	function __construct()
	{
		parent::__construct();
	}

	function index($field='all', $keyword='all', $offset=0)
	{
		switch($field)
		{
			case "telegroup_name":
				$this->db->where("telegroup_name LIKE '%".$keyword."%'", null);
			break;
		}

		$this->db->order_by("telegroup_name", "asc");
		$q = $this->db->get("telegroup", $this->config->item("limit_records"), $offset);
		$rows = $q->result();


		switch($field)
		{
			case "telegroup_name":
				$this->db->where("telegroup_name LIKE '%".$keyword."%'", null);
			break;
		}
		$total = $this->db->count_all_results("telegroup");

		$config['uri_segment'] = 5;
		$config['base_url'] = base_url()."telegram/index/".$field."/".$keyword;
		$config['total_rows'] = $total;
		$config['per_page'] = $this->config->item("limit_records");

		$this->pagination->initialize($config);

		$this->params['title'] = "Telegram Group List";
		$this->params["field"] = $field;
		$this->params["keyword"] = $keyword;
		$this->params["paging"] = $this->pagination->create_links();
		$this->params["offset"] = $offset;
		$this->params["total"] = $total;
		$this->params["data"] = $rows;
		$this->params["content"] = $this->load->view('telegram/grouplist', $this->params, true);
		$this->load->view("templatesess", $this->params);
	}

	function add($id=0)
	{
		if ($id)
		{
			$this->db->where("telegroup_id", $id);
			$q = $this->db->get("telegroup");
			if ($q->num_rows() == 0) { redirect(base_url()); return; }
			$row = $q->row();

			$this->params['row'] = $row;
			$this->params['title'] = "Update Telegram Group";
		}
		else
		{
			$this->params['title'] = "Add Telegram Group";
		}

		$this->params["content"] = $this->load->view('telegram/addform', $this->params, true);
		$this->load->view("templatesess", $this->params);
	}

	function save()
	{
		$id = isset($_POST['id']) ? trim($_POST['id']) : "";
		$telegroup_name = isset($_POST['telegroup_name']) ? trim($_POST['telegroup_name']) : "";
		$telegroup_chat_id = isset($_POST['telegroup_chat_id']) ? trim($_POST['telegroup_chat_id']) : "";

		if (strlen($telegroup_name) == 0) { $callback['error'] = true; $callback['message'] = "Masukkan Group Name Terlebih Dahulu !"; echo json_encode($callback); return; }
		if (strlen($telegroup_chat_id) == 0) { $callback['error'] = true; $callback['message'] = "Masukkan Group CHAT ID Terlebih Dahulu !"; echo json_encode($callback); return; }

		unset($data);
		$data['telegroup_name'] = $telegroup_name;
		$data['telegroup_chat_id'] = $telegroup_chat_id;
		$mydb = $this->load->database("master", TRUE);

		if ($id)
		{
			$mydb->where("telegroup_id", $id);
			$mydb->update("telegroup", $data);
			$this->db->cache_delete_all();
			$callback['error'] = false;
			$callback['message'] = "Update Group Telegram Sukses :) ";
			//$callback['redirect'] = base_url()."telegram/add/".$id."/".uniqid();
			$callback['redirect'] = base_url()."telegram";
			echo json_encode($callback);
			return;
		}

		$mydb->insert("telegroup", $data);
		$this->db->cache_delete_all();

		$lastid = $mydb->insert_id();

		$callback['error'] = false;
		$callback['message'] = "Add Telegram Group Success :) ";
		//$callback['redirect'] = base_url()."telegram/add/".$lastid."/".uniqid();
		$callback['redirect'] = base_url()."telegram";

		echo json_encode($callback);
	}

	function remove($id)
	{
		$this->db->where("telegroup_id", $id);
		$q = $this->db->get("telegroup");

		if ($q->num_rows() == 0) { redirect(base_url()."telegram/"); return; }
		$row = $q->row();
		$mydb = $this->load->database("master", TRUE);
		$mydb->where("telegroup_id", $id);
		$mydb->delete("telegroup");

		$this->db->cache_delete_all();

		redirect(base_url()."telegram");
	}

	function telegrampost()
	{
		//https://api.telegram.org/bot779316654:AAGaE8DFeJNx6HKkPidc7fBu3JCAQuXmnyM/sendMessage?chat_id=@lacakmobil&text=testing
		//error_reporting(E_ALL);
		//ini_set('display_errors', 1);

		//https://api.telegram.org/bot779316654:AAGaE8DFeJNx6HKkPidc7fBu3JCAQuXmnyM/getUpdates

		//Delete Webhook
		//https://api.telegram.org/bot779316654:AAGaE8DFeJNx6HKkPidc7fBu3JCAQuXmnyM/deleteWebhook

		//Send to group
		//https://api.telegram.org/bot779316654:AAGaE8DFeJNx6HKkPidc7fBu3JCAQuXmnyM/sendMessage?chat_id=-353793046&text=testingcurlgroup

		//Goto (https://web.telegram.org)
		//Goto your Gorup and Find your link of Gorup(https://web.telegram.org/#/im?p=g154513121)
		//Copy That number after g and put a (-) Before That -154513121
		//Send Your Message to Gorup bot.sendMessage(-154513121, "Hi")

		header("Content-Type: application/json");

		$data = json_decode(file_get_contents('php://input'), true);

		$id = $data["id"];
		$message = $data["message"];

		if($id == "") { echo json_encode(array("error"=>true, "message"=>"User ID Tidak Di Ketahui !")); return; }
		if($message == "") { echo json_encode(array("error"=>true, "message"=>"Pesan KOSONG !")); return; }

		$this->db->select("user_telegroup");
		$this->db->where("user_id",$id);
		$q = $this->db->get("user");

		if($q->num_rows == 0)
		{
			echo json_encode(array("error"=>true, "message"=>"User Tidak Diketahui !")); return;
		}

		$data = $q->row();

		$TOKEN  = $this->config->item("TELEGRAM_BOT_API");
		$channelid = "@lacakmobil";

		if($data->user_telegroup == 0)
		{
			echo json_encode(array("error"=>true, "message"=>"User Tidak Terdaftar Dalam Group/Channel !")); return;
		}

		$this->db->where("telegroup_id",$data->user_telegroup);
		$q = $this->db->get("telegroup");
		$tele = $q->row();

		if($tele->telegroup_chat_id != "")
		{
			$channelid = $tele->telegroup_chat_id;
		}
		else
		{
			$channelid = "@".$tele->telegroup_name;
		}

		print_r("abcnya : ". $id . $channelid);exit();


		$method	= "sendMessage";
		$url    = "https://api.telegram.org/bot" . $TOKEN . "/". $method."?chat_id=".$channelid."&text=".$message;
		file_get_contents($url);

	}

	function tespost()
	{
		error_reporting(E_ALL);
		ini_set('display_errors', 1);

		//$url = "http://lacak-mobil.com/telegram/telegrampost";
		$url = "http://admintib.buddiyanto.my.id/telegram/telegrampost";

		$data = array("id" => "631", "message" => "testing from telegram curl");
		$data_string = json_encode($data);

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data_string)));
		$result = curl_exec($ch);

		if ($result === FALSE) {
			die("Curl failed: " . curL_error($ch));
		}
		echo $result;
		echo curl_getinfo($ch, CURLINFO_HTTP_CODE);

	}

	function telegram_directpost()
	{
		header("Content-Type: application/json");

		$data = json_decode(file_get_contents('php://input'), true);

		$groupid = $data["id"];
		$message = $data["message"];

		if($groupid == "") { echo json_encode(array("error"=>true, "message"=>"Group Telegram KOSONG !")); return; }
		if($message == "") { echo json_encode(array("error"=>true, "message"=>"Pesan KOSONG !")); return; }

		$TOKEN  = $this->config->item("TELEGRAM_BOT_API");
		//$channelid = "@lacakmobil";
		$channelid = "@fmsnotif";

		if($groupid == 0)
		{
			echo json_encode(array("error"=>true, "message"=>"Group/Channel Tidak Terdaftar !")); return;
		}

		$channelid = $groupid;

		$method	= "sendMessage";
		$url    = "https://api.telegram.org/bot" . $TOKEN . "/". $method."?chat_id=".$channelid."&text=".$message;
		//$url    = "https://core.telegram.org/bot" . $TOKEN . "/". $method."?chat_id=".$channelid."&text=".$message;
		file_get_contents($url);

	}

	function telegram_directpost_withlink()
	{
		header("Content-Type: application/json");

		$data = json_decode(file_get_contents('php://input'), true);

		$groupid = $data["id"];
		$message = $data["message"];

		if($groupid == "") { echo json_encode(array("error"=>true, "message"=>"Group Telegram KOSONG !")); return; }
		if($message == "") { echo json_encode(array("error"=>true, "message"=>"Pesan KOSONG !")); return; }

		$TOKEN  = $this->config->item("TELEGRAM_BOT_API");
		$channelid = "@lacakmobil";

		if($groupid == 0)
		{
			echo json_encode(array("error"=>true, "message"=>"Group/Channel Tidak Terdaftar !")); return;
		}

		$channelid = $groupid;

		$method	= "sendMessage";
		$url    = "https://api.telegram.org/bot" . $TOKEN . "/". $method."?chat_id=".$channelid."&text=".$message."&parse_mode=HTML";
		file_get_contents($url);

	}

	function telegram_directpost_andy()
	{
		header("Content-Type: application/json");

		$data = json_decode(file_get_contents('php://input'), true);

		$groupid = $data["id"];
		$message = $data["message"];

		if($groupid == "") { echo json_encode(array("error"=>true, "message"=>"Group Telegram KOSONG !")); return; }
		if($message == "") { echo json_encode(array("error"=>true, "message"=>"Pesan KOSONG !")); return; }

		$TOKEN  = "990434545:AAHQdGAW5C1u_QyizRu_p8ngDwLxTPSfELw"; //username = andy_tes_bot
		$channelid = "@andybot";

		if($groupid == 0)
		{
			echo json_encode(array("error"=>true, "message"=>"Group/Channel Tidak Terdaftar !")); return;
		}

		$channelid = $groupid;

		$method	= "sendMessage";
		$url    = "https://api.telegram.org/bot" . $TOKEN . "/". $method."?chat_id=".$channelid."&text=".$message;
		file_get_contents($url);

	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
