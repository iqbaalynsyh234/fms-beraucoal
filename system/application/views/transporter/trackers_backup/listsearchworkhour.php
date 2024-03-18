        <div class="block-border">
            <h3><?=$this->lang->line("llongtimetotal"); ?>: <?=$longtime;?></h3>
            <table class="table sortable no-margin" cellspacing="0" width="100%">
                <thead>
                    <tr>
					   <th width="2%" style="text-align: center;">No.</td>
					   <th colspan="2" style="text-align: center;">Periode</th>
					   <th width="30%" style="text-align: center;"><?=$this->lang->line("llongtime"); ?>&nbsp;&nbsp;</th>
				    </tr>
				</thead>
				<tbody>
                    <?php for($i=0; $i < count($data); $i++) { ?>
                        <tr <?=($i%2) ? "class='odd'" : "";?>>
                            <td style="text-align: center;"><?=$i+1+$offset?></td>
                            <td style="text-align: center;"><?=date('M, jS Y H:i:s ', $data[$i][1]);?></td>
                            <td style="text-align: center;"><?=date('M, jS Y H:i:s ', $data[$i][0]);?></td>
                            <td style="text-align: right;"><?=$data[$i][3];?>&nbsp;&nbsp;</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?=$paging;?>
        </div>
<!-- End content -->
<!-- end new table -->				
