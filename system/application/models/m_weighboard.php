<?php
class M_weighboard extends Model {

    function M_weighboard(){
		parent::Model();
    	$this->fromsocket = false;
    }

    function totaldata(){
      $datefrom  = "2020-11-17 00:00:00";
      $dateuntil = "2020-11-17 23:59:59";

      $query = "SELECT * FROM webtracking_ts_bibtrans
                WHERE DateTimeTrans >= '$datefrom' AND DateTimeTrans <= '$dateuntil'";
      $this->dbtensor = $this->load->database("tensor_report", TRUE);
      return $this->dbtensor->query($query)->result_array();
    }

    function totalgross(){
      $datefrom  = "2020-11-17 00:00:00";
      $dateuntil = "2020-11-17 23:59:59";

      $query = "SELECT sum(Gross) as total_gross FROM webtracking_ts_bibtrans
                WHERE DateTimeTrans >= '$datefrom' AND DateTimeTrans <= '$dateuntil'";
      $this->dbtensor = $this->load->database("tensor_report", TRUE);
      return $this->dbtensor->query($query)->result_array();
    }

    function totalnetto(){
      $datefrom  = "2020-11-17 00:00:00";
      $dateuntil = "2020-11-17 23:59:59";

      $query = "SELECT sum(Netto) as total_netto FROM webtracking_ts_bibtrans
                WHERE DateTimeTrans >= '$datefrom' AND DateTimeTrans <= '$dateuntil'";
      $this->dbtensor = $this->load->database("tensor_report", TRUE);
      return $this->dbtensor->query($query)->result_array();
    }

    function grossbyvehicle($table){
      $datefrom  = "2020-11-17 00:00:00";
      $dateuntil = "2020-11-17 23:59:59";

      $query = "SELECT $table.Trans, $table.Mode, $table.Truck, SUM(Gross) as totalgrosspervehicle
                FROM $table
                WHERE $table.DateTimeTrans >= '$datefrom' AND $table.DateTimeTrans <= '$dateuntil'
                GROUP BY $table.Truck order by totalgrosspervehicle DESC LIMIT 5";
      $this->dbtensor = $this->load->database("tensor_report", TRUE);
      return $this->dbtensor->query($query)->result_array();
    }

    function nettobyvehicle($table){
      $datefrom  = "2020-11-17 00:00:00";
      $dateuntil = "2020-11-17 23:59:59";

      $query = "SELECT $table.Trans, $table.Mode, $table.Truck, SUM(Netto) as totalnettopervehicle
                FROM $table
                WHERE $table.DateTimeTrans >= '$datefrom' AND $table.DateTimeTrans <= '$dateuntil'
                GROUP BY $table.Truck order by totalnettopervehicle DESC LIMIT 5";
      $this->dbtensor = $this->load->database("tensor_report", TRUE);
      return $this->dbtensor->query($query)->result_array();
    }


}
?>
