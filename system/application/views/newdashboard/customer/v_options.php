<!-- BRANCH OFFICE  -->
  <select class="form-control" name="branchoffice" id="branchoffice" onchange="getsubcompanybyid();">
    <option value="">--Choose Branch Office--</option>
    <?php for ($i=0; $i < sizeof($branchoffice); $i++) {?>
      <option value="<?php echo $branchoffice[$i]->company_id;?>"><?php echo $branchoffice[$i]->company_name;?></option>
    <?php } ?>
  </select>
