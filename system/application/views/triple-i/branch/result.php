<style>
    
    a:link {color:green;}
	a:visited {color: #660066;}
	a:hover {text-decoration: none; color: #ff9900;
	font-weight:bold;}
	a:active {color: red;text-decoration: none}

</style>
<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;">
	<?=$navigation;?>
	<div id="main" style="margin: 20px;">
        <br /><br />
		<h2>Branch Office List (<?php echo $total;?>)</h2>
        <br />
        [<a href="<?php echo base_url();?>transporter/branchoffice/add">Add Branch Office</a>]
        <br /><br />
        <table width="100%" cellpadding="3" class="tablelist">
			<thead>
				<tr>
					<th width="1%">&nbsp;</td>
					<th width="2%"><?=$this->lang->line("lno"); ?></td>
					<th width="10%"><?php echo "Name" ?></td>
					<th width="10%"><?php echo "Address" ?></td>
					<th width="10%"><?php echo "City" ?></td>
					<th width="10%"><?php echo "Phone" ?></td>
					<th width="10%"><?php echo "Fax" ?></td>
					<th width="10%"><?php echo "Control" ?></td>
				</tr>
			</thead>
			<tbody>
                <?php for($i=0;$i<count($data);$i++) { ?>
				<tr <?=($i%2) ? "class='odd'" : "";?>>
					<th width="1%">&nbsp;</td>
					<td width="2%"><?=$i+1+$offset?></td>
                    <td><?php echo $data[$i]->company_name;?></td>
                    <td>
                    <?php 
                    foreach($branch as $databranch)
                    {
                        
                        if ($data[$i]->company_id == $databranch->branch_company_id)
                        {
                            
                            echo $databranch->branch_address;
                        }
                        
                    } 
                    ?>
                    </td>
                    <td>
                    <?php 
                    foreach($branch as $databranch)
                    {
                        
                        if ($data[$i]->company_id == $databranch->branch_company_id)
                        {
                            
                            echo $databranch->branch_city;
                        }
                        
                    } 
                    ?>
                    </td>
                    <td>
                    <?php 
                    foreach($branch as $databranch)
                    {
                        
                        if ($data[$i]->company_id == $databranch->branch_company_id)
                        {
                            
                            echo $databranch->branch_telp;
                        }
                        
                    } 
                    ?>
                    </td>
                    <td>
                    <?php 
                    foreach($branch as $databranch)
                    {
                        
                        if ($data[$i]->company_id == $databranch->branch_company_id)
                        {
                            
                            echo $databranch->branch_fax;
                        }
                        
                    } 
                    ?>
                    </td>
                    <td>
                    <a href="<?php echo base_url();?>transporter/branchoffice/edit/<?php echo $data[$i]->company_id;?>">
                    <img src="<?php echo base_url();?>assets/images/edit.gif" />
                    </a>
                    </td>
                </tr>
                <? } ?>
			</tbody>
			
			<tfoot>
					<tr>
						<td colspan="12"><?=$paging?></td>
					</tr>
			</tfoot>
			
		</table>
	</div>
</div>