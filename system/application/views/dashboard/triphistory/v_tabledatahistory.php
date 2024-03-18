<link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/dashboard/assets/css/jquery.dataTables.min.css"/>
<!-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs/dt-1.10.22/datatables.min.css"/> -->
<link href="<?php echo base_url();?>assets/dashboard/assets/css/style.css" rel="stylesheet" type="text/css" />


<table class="table" id="historykalimantan" style="width: 100%; font-size:12px;">
  <thead>
    <tr>
     <th width="2%"  style="text-align: center;">No.</td>
     <th width="30%" style="text-align: center;"><?=$this->lang->line("ldatetime"); ?></th>
     <th style="text-align: center;"><?=$this->lang->line("lposition"); ?></th>
     <th width="20%" style="text-align: center;"><?=$this->lang->line("lcoordinate"); ?></th>
     <th width="12%"  style="text-align: center;"><?=$this->lang->line("lstatus"); ?></th>
     <th width="12%"  style="text-align: center;"><?=$this->lang->line("lspeed"); ?></th>
     <th width="8%"  style="text-align: center;"><?php echo $this->lang->line('lengine_1'); ?></th>
     <th width="8%"  style="text-align: center;"><?=$this->lang->line("lodometer"); ?> (km)</th>
    </tr>
  </thead>
  <tbody id="dvresult">
    <?php for ($i=0; $i < sizeof($data); $i++) {?>
      <tr>
        <td style="text-align: center;"><?php echo $i+1 ?></td>
        <td style="text-align: center;"><?php echo $data[$i]->gps_date_fmt.' '.$data[$i]->gps_time_fmt ?></td>
        <td style="text-align: center;"><?php echo $data[$i]->georeverse->display_name ?></td>
        <td style="text-align: center;"><?php echo $data[$i]->gps_latitude_real_fmt.','.$data[$i]->gps_longitude_real_fmt ?></td>
        <td style="text-align: center;"><?php echo $data[$i]->gpstatus ?></td>
        <td style="text-align: center;"><?php echo $data[$i]->gps_speed_fmt ?></td>
        <td style="text-align: center;"><?php echo $data[$i]->status1 ?></td>
        <td style="text-align: center;"><?php echo $data[$i]->odometer ?></td>
      </tr>
    <?php } ?>
  </tbody>
</table>

<!-- data tables -->
<script type="text/javascript" src="<?php echo base_url() ?>assets/dashboard/assets/js/datatable.min.js"></script>
<script type="text/javascript">
  $(document).ready(function() {
    $('#historykalimantan').DataTable();
  });
</script>
