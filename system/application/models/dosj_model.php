<?php
class Dosj_model extends Model {

    function Dosj_model()
    {
		parent::Model();
    }

    function tambahdosj($dataarray)
    {
		$this->dbtransporter = $this->load->database('transporter', true);
		$my_company = $this->sess->user_company;
		
        for($i=1;$i<count($dataarray);$i++){
            $data = array(
                'dosj_no'=>$dataarray[$i]['dosj_no'],
				'dosj_company'=>$my_company,
                'dosj_customer_tmp'=>$dataarray[$i]['dosj_customer_tmp'],
				'dosj_item_desc'=>$dataarray[$i]['dosj_item_desc'],
				'dosj_item_panjang'=>$dataarray[$i]['dosj_item_panjang'],
				'dosj_item_lebar'=>$dataarray[$i]['dosj_item_lebar'],
				'dosj_item_tinggi'=>$dataarray[$i]['dosj_item_tinggi'],
				'dosj_item_quantity'=>$dataarray[$i]['dosj_item_quantity'],
				'dosj_item_unit'=>$dataarray[$i]['dosj_item_unit'],
				'dosj_ship_date'=>$dataarray[$i]['dosj_ship_date'],
				'dosj_mortar_no'=>$dataarray[$i]['dosj_mortar_no'],
				'dosj_note'=>$dataarray[$i]['dosj_note']
				
            );
            $this->dbtransporter->insert('dosj', $data);
        }
		$this->dbtransporter->close();
    }
	
}
?>