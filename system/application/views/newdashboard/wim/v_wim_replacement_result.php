<form class="form-horizontal" id="frmreplace" onsubmit="javascript:return frm_replace();">
<div class="row">
  <div style="margin-left:auto; margin-right:auto;">
    <button type="submit" id="btnReplacement" class="btn btn-primary btn-md" style="display:none;">Replace</button>
  </div>
</div> <br>

<div class="row">
  <div class="col-md-6">
    <label style="font-weight:bold;">Actual WIM Delay</label>
    <div style="max-height:450px; overflow-x:auto;">
      <table class="table table-striped" style="font-size:11px;">
          <thead>
            <th></th>
            <th>No</th>
            <!-- <th>Status</th> -->
            <th>Trans ID</th>
            <th>Contractor</th>
            <th>Truck ID</th>
            <th>Start Local</th>
            <th>Finish Local</th>
            <th>Created Date</th>
            <th>Difference</th>
          </thead>
          <tbody>
            <?php for ($i=0; $i < sizeof($dataactual); $i++) {?>
                 <tr>
                   <td>
                     <input type="checkbox" name="checkboxactual[]" id="checkboxactual<?php echo $i ?>" value="<?php echo $dataactual[$i]['integrationwim_transactionID'].'-'.$dataactual[$i]['integrationwim_id']; ?>" onclick="actualOnlyThis(this.id)">
                   </td>
                   <td><?php echo $i+1; ?></td>
                   <!-- <td><?php echo $dataactual[$i]['integrationwim_status']; ?></td> -->
                   <td><?php echo $dataactual[$i]['integrationwim_transactionID']; ?></td>
                   <td><?php echo $dataactual[$i]['integrationwim_haulingContractor']; ?></td>
                   <td><?php echo $dataactual[$i]['integrationwim_truckID']; ?></td>
                   <td><?php echo $dataactual[$i]['integrationwim_penimbanganStartLocal']; ?></td>
                   <td><?php echo $dataactual[$i]['integrationwim_penimbanganFinishLocal']; ?></td>
                   <td><?php echo $dataactual[$i]['integrationwim_created_date']; ?></td>
                   <td><?php echo $dataactual[$i]['selisih']; ?></td>
                 </tr>
            <?php } ?>
          </tbody>
        </table>
    </div>
  </div>

  <div class="col-md-6">
    <label style="font-weight:bold;">Average FMS</label>
    <div style="max-height:450px; overflow-x:auto;">
      <table class="table table-striped" style="font-size:11px;">
        <thead>
          <th></th>
          <th>No</th>
          <!-- <th>Status</th> -->
          <th>Trans ID</th>
          <th>Contractor</th>
          <th>Truck ID</th>
          <th>Start Local</th>
          <th>Finish Local</th>
          <th>Created Date</th>
        </thead>
        <tbody>
          <?php for ($i=0; $i < sizeof($dataaverage); $i++) {?>
            <tr>
              <td>
                <input type="checkbox" name="checkboxaverage[]" id="checkboxaverage<?php echo $i ?>" value="<?php echo $dataaverage[$i]['integrationwim_transactionID'].'-'.$dataaverage[$i]['integrationwim_id']; ?>" onclick="averageOnlyThis(this.id)">
              </td>
              <td><?php echo $i+1; ?></td>
              <!-- <td><?php echo $dataaverage[$i]['integrationwim_status']; ?></td> -->
              <td><?php echo $dataaverage[$i]['integrationwim_transactionID']; ?></td>
              <td><?php echo $dataaverage[$i]['integrationwim_haulingContractor']; ?></td>
              <td><?php echo $dataaverage[$i]['integrationwim_truckID']; ?></td>
              <td><?php echo $dataaverage[$i]['integrationwim_penimbanganStartLocal']; ?></td>
              <td><?php echo $dataaverage[$i]['integrationwim_penimbanganFinishLocal']; ?></td>
              <td><?php echo $dataaverage[$i]['integrationwim_created_date']; ?></td>
            </tr>
          <?php } ?>
        </tbody>
    </table>
  </div>

</div>
</form>
