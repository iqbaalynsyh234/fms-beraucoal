<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class SMSModel extends Model {
	var $earthRadius = 6371;

	function SMSModel ()
	{
		parent::Model();
	}

	function getMessage($code)
	{
		switch($code)
		{
			case "ue":
				return $this->lang->line("lerror_empty_username");
			break;
			case "pe":
				return $this->lang->line("lerror_empty_userpass");
			break;
			case "il":
				return $this->lang->line("lerror_invalid_login");
			break;

		}
	}

	function sendsms($xml, $direct=0)
	{
		$smsserver = $this->getSMSServer();
		switch($smsserver)
		{
			case "mondial":
				return $this->sendsmsmondial($xml);
			break;
			default:
				return $this->sendsmslocalhost($xml, $direct);
		}
	}


	function sendsmsmondial($xml)
	{
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $this->config->item("SMS_API_URL"));
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$c = curl_exec($ch);
		$err = curl_errno($ch);
		curl_close($ch);

		if ($err) return false;

		return strlen($c);
	}

	function sendsmslocalhost($xml, $direct=0)
	{
		$xmls = explode("\1", $xml);
print_r($xmls);
		$smsdb = $this->load->database("smscolo", TRUE);

		foreach(explode("|", $xmls[0]) as $hp)
		{
			unset($insert);

			//$insert["direct"] = $direct;
			$insert["SenderNumber"] = $hp;
			$insert["ReceivingDateTime"] = date("Y-m-d H:i:s");
			$insert["TextDecoded"] = $xmls[1];

			if (count($xmls) > 2)
			{
				$insert["RecipientID"] = $xmls[2];
			}

			$smsdb->insert("inbox", $insert);
		}

		$this->load->database("default", TRUE);

		return true;
	}

	function getSaldoUser($userid)
	{
		$this->db->order_by("smsbalance_created", "desc");
		$this->db->where("smsbalance_user", $userid);
		$this->db->limit(1, 0);
		$q = $this->db->get("smsbalance");

		if ($q->num_rows() == 0)
		{
			return 0;
		}

		$row = $q->row();
		return $row->smsbalance_saldo;
	}

	function getSaldoAgent($agentid)
	{
		$this->db->order_by("smsbalance_created", "desc");
		$this->db->where("smsbalance_agent", $agentid);
		$this->db->limit(1, 0);
		$q = $this->db->get("smsbalance");

		if ($q->num_rows() == 0)
		{
			return 0;
		}

		$row = $q->row();
		return $row->smsbalance_saldo;
	}

	function operator($nogsm)
	{
		$nogsm = str_replace(' ', '', $nogsm);
		$nogsm = str_replace('+', '', $nogsm);

		$nogsm = ltrim($nogsm, "0");
		$nogsm = ltrim($nogsm, "62");

		$op = substr($nogsm, 0, 3);

		return $this->config->item("SMS_".$op);
	}

	function welcome($user)
	{

		if (in_array($user->user_id, $this->config->item("SKIP_WELCOME_USER")))
		{
			return;
		}

		$hp = valid_mobiles($user->user_mobile);
		if ($hp === FALSE) return;

		$this->db->where("smsannouncement_user", $user->user_id);
		$this->db->where("DATEDIFF(smsannouncement_send, '".date("Y-m-d H:i:s")."') = 0", null);
		$total = $this->db->count_all_results("smsannouncement");
		if ($total > 0)
		{
			return;
		}

		if ($user->user_type == 2)
		{
			$this->db->limit(1, 0);
			$this->db->where("vehicle_user_id", $user->user_parent);
			$q = $this->db->get("vehicle");

			if ($q->num_rows() == 0) return;
		}
		else
		if ($user->user_type == 3)
		{
			$this->db->limit(1, 0);
			$this->db->where("user_agent", $user->user_agent);
			$this->db->join("user", "user_id = vehicle_user_id");
			$q = $this->db->get("vehicle");

			if ($q->num_rows() == 0) return;
		}
		else
		{
			$this->db->limit(1, 0);
			$this->db->join("user", "user_id = vehicle_user_id");
			$q = $this->db->get("vehicle");

			if ($q->num_rows() == 0) return;
		}

		$rowvehicle = $q->row();

		if ($user->user_agent == 3)
		{
			$license = "GPS Andalas";
		}
		else
		{
			$license = "lacak-mobil.com";
		}

		$content[] = sprintf("slmt dtg %s di %s. u/ meminimalkan penyalahgunaan kend, lakukan pengaturan batas area kend, setiap kali kend dipinjam. Pilih menu setting > geofence", $user->user_login, $license);
		$content[] = sprintf("slmt dtg %s di %s. u/ monitor posisi via sms ketik PSS %s <no kendaraan> dan kirim ke no ini. Misal PSS %s %s. Free charge.", $user->user_login, $license, $user->user_login, $user->user_login, nomobil($rowvehicle->vehicle_no));
		$content[] = sprintf("slmt dtg %s di %s. set batas kecepatan, ketik KM <kec km/jam> <no kend> & kirim ke no ini. Mis KM 100 B1234CD. Free charge", $user->user_login, $license);
		$content[] = sprintf("slmt dtg %s di %s. set batas lama parkir, ketik PARK <menit> <no kend> & kirim ke no ini. Mis PARK 60 B1234CD. Free charge", $user->user_login, $license);
		$content[] = sprintf("slmt dtg %s di %s. Anda sekarang bisa mematikan dan menghidupkan mesin dari web. Buka realtime tracking dan klik Cut Off Engine/Resume Engine", $user->user_login, $license);
		$content[] = sprintf("slmt dtg %s di %s. Utk mempermudah monitoring posisi semua kendaraan Anda, ketik sms PSSSEMUA %s & kirim ke no ini. Free charge. Data akan dikirim per 2-3 menit.", $user->user_login, $license, $user->user_login);
		$content[] = sprintf("slmt dtg %s di %s. Utk sms pertanyaan dan pengaduan, silahkan kirim ke 082111240876 atau 0815810207.", $user->user_login, $license);
		$idx = rand(0, count($content)-1);
		$idx = count($content)-1;

		$this->params['dest'] = $hp;
		$this->params['content'] = isset($content[$idx]) ? $content[$idx] : $content[0];

		$xml = $this->load->view("sms/send", $this->params, true);
		if (! $this->sendsms($xml)) return;

		unset($insert);

		unset($content);
		$content['text'] = $this->params['content'];
		$content['receive'] = implode(",", $hp);

		$insert['smsannouncement_user'] = $user->user_id;
		$insert['smsannouncement_send'] = date("Y-m-d H:i:s");
		$insert['smsannouncement_content'] = json_encode($content);

		$this->db->insert("smsannouncement", $insert);
		$this->db->cache_delete_all();
	}

	function getSMSServer()
	{
		$this->db->where("config_name", "smsserver");
		$q = $this->db->get("config");

		if ($q->num_rows() == 0) return "mondial";

		$row = $q->row();

		return $row->config_value;
	}

	function sendsms1($dest, $message)
	{
		$smsserver = $this->getSMSServer();
		switch($smsserver)
		{
			case "mondial":
				return $this->sendsms1mondial($dest, $message);
			default:
				return $this->sendsms1localhost($dest, $message);
		}
	}

	function sendsms1mondial($dest, $message)
	{
		$this->params['dest'] = $dest;
		$this->params['content'] = $message;

		$xml = $this->load->view("sms/send", $this->params, true);
		return $this->sendsms($xml);
	}

	function sendsms1localhost($dest, $message)
	{
		$smsdb = $this->load->database("smscolo", TRUE);
		foreach($dest as $hp)
		{
			unset($insert);

			$insert["ReceivingDateTime"] = date("Y-m-d H:i:s");
			$insert["TextDecoded"] = $message;
			$insert["SenderNumber"] = $hp;

			$smsdb->insert("inbox", $insert);
		}

		$this->load->database("default", TRUE);

		return true;
	}

	function cutoffengine($type)
	{
		if (in_array(strtoupper($type), $this->config->item("vehicle_t5")))
		{
			return "stopelec114477";
		}

		if (in_array(strtoupper($type), $this->config->item("vehicle_gtp")))
		{
			return "W000000,020,1,1";
		}

		if (in_array(strtoupper($type), $this->config->item("vehicle_t1")))
		{
			return "*stop#";
		}

		if (in_array(strtoupper($type), $this->config->item("vehicle_t3")))
		{
			return "9000000";
		}

		return;
	}

	function resumeengine($type)
	{
		if (in_array(strtoupper($type), $this->config->item("vehicle_t5")))
		{
			return "supplyelec114477";
		}

		if (in_array(strtoupper($type), $this->config->item("vehicle_gtp")))
		{
			return "W000000,020,1,0";
		}

		if (in_array(strtoupper($type), $this->config->item("vehicle_t1")))
		{
			return "*re#";
		}

		if (in_array(strtoupper($type), $this->config->item("vehicle_t3")))
		{
			return "9100000";
		}

		return;
	}

	function checkpulse($operator)
	{
		$op = $this->getOperator($operator);

		if (strcasecmp($op, "INDOSAT") == 0) return "*QP**555##";
		if (strcasecmp($op, "TELKOMSEL") == 0) return "*QP**888##";

		return "";
	}

	function restart($vtype, $operator)
	{
		$op = $this->getOperator($operator);
		switch(strtoupper($vtype))
		{
			case "T1":
			case "T1_1":
			case "T1_U1":
			case "PLN":
				if (strlen($op) == 0) return "NOT SUPPORT";

				if (strcasecmp($op, "INDOSAT") == 0) return "*APN:INDOSATGPRS#";
				if (strcasecmp($op, "TELKOMSEL") == 0) return "*APN:TELKOMSEL#";
			break;
			case "T3":
				return "*RESTART#0000##";
			break;
			case "T5":
			case "T5 PULSE":
			case "T5 Fuel":
				return "Protocol114477 UDP";
				//if (strlen($op) == 0) return "NOT SUPPORT";

				//if (strcasecmp($op, "INDOSAT") == 0) return "APN114477 INDOSATGPRS";
				//if (strcasecmp($op, "TELKOMSEL") == 0) return "APN114477 TELKOMSEL";
			break;
			case "INDOGPS":
				return "*RESTART#0000##";
			break;

		}

		return "";
	}

	function getOperator($type)
	{
		$indosats = array("INDOSAT", "MATRIX", "IM3", "MATRIK", "MENTARI");
		foreach($indosats as $indosat)
		{
			$pos = strpos(strtoupper($type), $indosat);
			if ($pos !== FALSE) return "INDOSAT";
		}

		$telkomsels = array("TELKOMSEL", "SIMPATI", "AS");
		foreach($telkomsels as $telkomsel)
		{
			$pos = strpos(strtoupper($type), $telkomsel);
			if ($pos !== FALSE) return "TELKOMSEL";
		}

		return "";
	}

	function abbreviation($url)
	{
		$db = $this->load->database("smscolo", TRUE);

		$len = 2;
		while(1)
		{
			for($i=0; $i < 3; $i++)
			{
				$s = $this->_abbreviation($len);

				$db->where("abbreviation", $s);
				$total = $db->count_all_results("link");

				if ($total == 0)
				{
					unset($insert);

					$insert['abbreviation'] = $s;
					$insert['url'] = $url;
					$insert['created'] = date("Y-m-d H:i:s");

					$db->insert("link", $insert);

					$this->load->database("default", TRUE);
					return $s;
				}
			}

			$len++;
		}

		$this->load->database("default", TRUE);
	}

	function _abbreviation($len)
	{
		$s = "";
		for($i=0; $i < $len; $i++)
		{
			if (($i % 2) == 0)
			{
				$s .= chr(rand(ord('a'), ord('z')));
				continue;
			}

			$s .= chr(rand(ord('A'), ord('Z')));
		}

		return $s;
	}

	function masaaktif($nominal)
	{
		switch($nominal)
		{
			case 10000:
				return 15;
			case 20000:
				return 30;
			case 25000:
				return 35;
			case 50000:
			case 75000:
				return 45;
			case 100000:
				return 60;
			case 150000:
				return 90;
		}

		return 0;
	}

}
