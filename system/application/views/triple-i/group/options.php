<select id="group" name="group">
<?php if ($isshowadmin) { ?>
<option value="0" <?php if ($selected == 0) { echo "selected"; } ?>><?php echo $this->lang->line("ladministrator"); ?></option>
<?php } else { ?>
<option value="0" <?php if ($selected == 0) { echo "selected"; } ?>><?php echo $this->lang->line("lall_group"); ?></option>
<?php } ?>
<?php foreach($rows as $row) { ?>
<option value="<?php echo $row->group_id; ?>" <?php if ($selected == $row->group_id) { echo "selected"; } ?>><?php echo $row->group_name; ?></option>
<?php } ?>
</select>

