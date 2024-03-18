<?php
include "base.php";

class Crontupperware extends Base {

	function Crontupperware()
	{
		parent::Base();	
		$this->load->model("gpsmodel");
        $this->load->model("vehiclemodel");
        $this->load->model("configmodel");
        $this->load->helper('common_helper');
        $this->load->helper('kopindosat');
        $this->load->model("historymodel");
		$this->load->library('email');
		$this->load->helper('email');
	}
    
	function index(){}
    function read_text_file()
	{
		$this->load->helper("file");
		$extensions = array("txt");
		$file_path = "/home/tupperware/inbox/";
		$copy_file_path = "/home/tupperware/recheck/";
		$filenames = get_filenames($file_path,$extensions);
		$totalfile = count($filenames);
		$this->dbtransporter = $this->load->database("transporter",true);
		
		if (isset($filenames) && $totalfile > 0)
		{
			//$string = read_file($filenames[$i]); >> Bawaan Code Igniter
			$string = $filenames[0];
			$line = file($string);
			$total_line = count($line);

			
			for ($i=0; $i<count($line); $i++)
			{
				$id_booking = substr($line[$i],0,9);
				//print_r($id_booking."\r\n");
				$so = substr($line[$i],20,8);
				//print_r($so."\r\n");
				$sotype = substr($line[$i],28,2);
				//print_r($sotype."\r\n");
				$dr = substr($line[$i],30,8);
				//print_r($dr."\r\n");
				$dbcode = substr($line[$i],38,8);
				
				//Cek And Update ID Booking
				unset($data);
				$data["transporter_dr_booking_id"] = $id_booking;
				$data["transporter_dr_so"] = $so;
				$data["transporter_dr_dr"] = $dr;
				$data["transporter_so_type"] = $sotype;
				$data["transporter_db_code"] = $dbcode;
				
				//Cek ID Booking sudah ada atau belum
				$this->dbtransporter->where("transporter_dr_booking_id",$id_booking);
				$this->dbtransporter->where("transporter_dr_so",$so);
				$this->dbtransporter->where("transporter_dr_dr",$dr);
				$this->dbtransporter->where("transporter_dr_status",1);
				$q = $this->dbtransporter->get("tupper_dr");
				$rows = $q->result();
				$total_rows = count($rows);
				if ($total_rows > 0)
				{
					//$sendmaildb = $this->sendmail_to_db($id_booking, $dbcode, $dr, $so);
				}
				else
				{
					$this->dbtransporter->insert("tupper_dr",$data);
					//$sendmaildb = $this->sendmail_to_db($id_booking, $dbcode, $dr, $so);
				}
			}
		}
		
		//Copy File to directory Recheck
		if (isset($filenames[0]))
		{
			$ex_filename = explode("/",$filenames[0]);
			if (isset($ex_filename[4]))
			{
				$newfile = $copy_file_path.$ex_filename[4];
				if(!copy($filenames[0],$newfile))
				{
					printf("Copy Failed \r\n");	
				}
				else
				{
					printf("Copy File Success \r\n");	
				}
			}
			
		}
		else
		{
			printf("Empty Files ! Can't Copy File \r\n");	
		}
		
		//Delete File
		if (isset($filenames[0]))
		{
			if (unlink($filenames[0]))
			{
				printf("Delete File OK \r\n");	
			}
			else
			{
				printf("Delete File Failed \r\n");	
			}
		}
		else
		{	
			printf("Empty Files \r\n");	
		}
		
		printf("FINISH AT %s\r\n",date("d-m-y H:i:s"));
	}
	function sendmail_to_db($key, $dbcode, $dr, $so)
	{
		$content = "";$myvehicle ="";$myekspedisi = "";$link="";
		$this->dbtransporter->where("booking_id",$key);
		$this->dbtransporter->where("booking_delivery_status",1);
		$this->dbtransporter->where("booking_status",1);
		$this->dbtransporter->limit(1);
		$q = $this->dbtransporter->get("id_booking");
		$row = $q->row();
		if(count($row)>0)
		{
			//print_r($row);exit;
			if($row->booking_vehicle != "" || $row->booking_vehicle != 0)
			{
				$dbmail = $this->getdbmail($dbcode);
				$myvehicle = $this->getvehicle($row->booking_vehicle);
				$myekspedisi = $this->getekspedisi($row->booking_vehicle);
				if($dbmail != "")
				{
					$exv = explode("@",$row->booking_vehicle);
					$link = "http://tupperware.lacak-mobil.com/mod_db_tupperware/dbtupper/".$exv[0].
							"/".$dr."/".$so."/".$dbcode."/".date("YmdHis")."/".$exv[1];
					$emails = get_valid_emails($dbmail);
					$content = "<html>".
							   "<body>".
							   "Kami telah menerima permintaan Order Anda.<br />"." ".
							   "Order tersebut telah kami proses dan dikirimkan pada:"." ".date("d-m-Y").",<br />".
							   "<br />".
							   "<font size='4px'><b>DELIVERY</b>- Barang pesanan sedang di perjalanan.</font><br />".
							   "<br />".
							   "Berikut ini adalah informasi detail transaksi tsb :<br />".
							   "<table width='100%'>".
							   "<tr>".
							   "<td width='30%'>".
							   "OrderNo [date] ".
							   "</td>".
							   "<td width='3%'>".
							   ":".
							   "</td>".
							   "<td>".
							   $so." "."[".date("d-m-Y")."]".
							   "</td>".
							   "</tr>".
							   "<tr>".
							   "<td>".
							   "Purchase No. ".
							   "</td>".
							   "<td>".
							   ":".
							   "</td>".
							   "<td>".
							   "&nbsp;".
							   "</td>".
							   "<tr>".
							   "<td>".
							   "DRNo ".
							   "</td>".
							   "<td>".
							   ":".
							   "</td>".
							   "<td>".$dr."</td>".
							   "</tr>".
							   "<tr>".
							   "<td>".
							   "Expedisi ".
							   "</td>".
							   "<td>".
							   ":".
							   "</td>".
							   "<td>".$myekspedisi."</td>".
							   "</tr>".
							   "<tr>".
							   "<td>".
							   "No /Vehicle/Truk/Kapal ".
							   "</td>".
							   "<td>".
							   ":".
							   "</td>".
							   "<td>".
							   $myvehicle."</td>".
							   "</tr>".
							   "</table>".
							   "<br />".
							   "Posisi order tersebut dapat dilihat melalui GPS pada link dibawah ini :<br />".
							   "Link :"." ".$link."<br />".
							   "<br />".
							   "Terima Kasih"."<br />".
							   "<br />".
							   "Tupperware".
							   "</body>".
							   "</html>";
					if (is_array($emails) && count($emails))
					{
						foreach($emails as $email)
						{
							 $subject = "DELIVERY REPORT TUPPERWARE";
							 $msgbody = $content;
							 $tomail = $email;
							 $tocc = "agung@lacak-mobil.com";
							 $process = $this->smtpmailer($tomail, 'support@lacak-mobil.com', 'Support Tupperware', $subject, $msgbody, $tocc);
							 if($process == true)
							{
								printf("PROCESS SEND MAIL OK \r\n");
							}
							else
							{
								printf("PROCESS SEND MAIL FAIL! \r\n");	
								printf("ERROR! %s \r\n",$process);	
							}
						}
					}
				}
			}
		}
	}
	function getdbmail($key)
	{
		$v = "";
		$this->dbtransporter->where("dist_code",$key);
		$this->dbtransporter->where("dist_status",1);
		$this->dbtransporter->limit(1);
		$q = $this->dbtransporter->get("dist_tupper");
		$row = $q->row();
		if(count($row)>0)
		{
			$v = $row->dist_email;
		}
		return $v;
	}
	function getvehicle($key)
	{
		$ve = "";
		$this->db->select("vehicle_no");
		$this->db->where("vehicle_device",$key);
		$this->db->limit(1);
		$q = $this->db->get("vehicle");
		$row = $q->row();
		if(count($row)>0)
		{
			$ve = $row->vehicle_no;
		}
		return $ve;
	}
	function getekspedisi($key)
	{
		$ve = "";
		$this->db->select("vehicle_user_id");
		$this->db->where("vehicle_device",$key);
		$this->db->limit(1);
		$q = $this->db->get("vehicle");
		$row = $q->row();
		if(count($row)>0)
		{
			$ve = $row->vehicle_user_id;
			$this->dbtransporter->select("slcars_name");
			$this->dbtransporter->where("slcars_lacak_code",$ve);
			$this->dbtransporter->where("slcars_status",1);
			$this->dbtransporter->limit(1);
			$q = $this->dbtransporter->get("tupper_slcars");
			$row = $q->row();
			if(count($row)>0)
			{
				$ve = $row->slcars_name;
			}
		}
		return $ve;
	}
	function smtpmailer($to, $from, $from_name, $subject, $body, $tocc) 
	{ 
		require_once ('class/phpmailer/class.phpmailer.php');
		require_once ('class/phpmailer/class.smtp.php');
		global $error;
		$mail = new PHPMailer();  // create a new object
		$mail->IsSMTP(); // enable SMTP
		$mail->SMTPDebug = 0;  // debugging: 1 = errors and messages, 2 = messages only
		$mail->SMTPAuth = true;  // authentication enabled
		$mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for GMail
		$mail->Host = 'smtp.gmail.com';
		$mail->Port = 465; 
		$mail->IsHTML(true);
		$mail->Username = "agung@lacak-mobil.com";  
		$mail->Password = "ariestiani";           
		$mail->SetFrom($from, $from_name);
		$mail->Subject = $subject;
		$mail->Body = $body;
		$mail->AddAddress($to);
		$mail->AddCC($tocc);
		/* foreach($tocc as $email => $name)
		{
			$mail->AddCC($email, $name);
		} */
		if(!$mail->Send()) 
		{
			$error = 'Mail error: '.$mail->ErrorInfo; 
			return $error;
		} 
		else 
		{
			$error = 'Message sent!';
			return true;
		}
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
