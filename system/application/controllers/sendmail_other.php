<?php
include "base.php";

class Sendmail_other extends Base {
    function Sendmail_other()
    {  
		parent::Base();	
		$this->load->helper('email');
		$this->load->library('email');
	}
   
	function index()
	{
		date_default_timezone_set('Asia/Jakarta');
		$this->db = $this->load->database("DB_JUGGERNAUT",true);
		$this->db->where("email_sent",0);
		$this->db->where("email_flag",0);
		$q = $this->db->get("upload_email");
		if ($q->num_rows() == 0)
		{
			echo "Tidak email yg harus dikirim !\r\n";
			return;
		}
		$rows = $q->result();
		$i = 0;
		foreach($rows as $row)
		{
			printf("\r\n\r\n[%s WIB] %02d %s %s\r\n", date("Y-m-d H:i:s"), $i, $row->email_to, $row->email_subject);
			
			//send email to uploader
			$subjectmail = $row->email_subject;
			$contentmail = $row->email_content;
			$destemail = $row->email_to;
			$ccemail = $row->email_cc;
			$bccemail = $row->email_bcc;
			$sendermail = $row->email_from;
			
			$sendmail = $this->sendemail($destemail,$subjectmail,$contentmail,$ccemail,$bccemail,$sendermail);
			
			$update_job['email_sent'] = 1;
			$update_job['email_sent_datetime'] = date("Y-m-d H:i:s");
			
			$this->db->limit(1);
			$this->db->where("email_id", $row->email_id);
			$this->db->update("upload_email", $update_job);
			printf("----- UPDATE STATUS OK : %s \r\n",$row->email_id);
				
			$i++;
		}
		
		printf("----- Selesai \r\n");
		
	}
	
	function sendemail($to,$subject,$msg,$cc,$bcc,$sender)
    {
        $headers = "";
        $headers .= 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";        
        $headers .= 'Bcc: '.$bcc. "\r\n";
		$headers .=  'From: '.$sender. "\r\n" . 'X-Mailer: PHP/' . phpversion();
		
        mail($to, $subject, $msg, $headers);
        return true;
    }
	
	
}

