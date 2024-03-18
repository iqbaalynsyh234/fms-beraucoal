<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class InvoiceModel extends Model {	
	function InvoiceModel () 
	{				
		parent::Model();		
	}	

	function getInvoiceNo($db)
	{
		$i = 1;
		$total = $db->count_all_results("invoice");
		while(1)
		{			
			$no = $total+$i;
			
			$db->where("invoice_no", $no);
			$total = $db->count_all_results("invoice");
			if ($total == 0) return sprintf("%06d", $no);
			
			$i++;
		}
		
	}

}

