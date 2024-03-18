<script>
  function showmapex() {
    showmap();
  }

  function frmsearch_onsubmit() {
    var field = jQuery("#field").val();
    if (field == "poi_category") {
      location = '<?php echo base_url(); ?>poi/index/' + jQuery("#field").val() + "/" + jQuery("#poicat").val();
    } else {
      location = '<?php echo base_url(); ?>poi/index/' + jQuery("#field").val() + "/" + jQuery("#keyword").val();
    }

    return false;
  }

  function field_onchange() {
    var field = jQuery("#field").val();

    if (field == "poi_category") {
      jQuery("#dvkeyword").hide();
      jQuery("#dvpoicat").show();
      poicat_onchange();
    } else {
      jQuery("#dvkeyword").show();
      jQuery("#dvpoicat").hide();
      hidepoicaticon();
    }
  }

  function hidepoicaticon() {
    <?php for($i=0; $i < count($poicats); $i++) { ?>
    jQuery("#img<?=$poicats[$i]->poi_cat_id;?>").hide();
    <?php } ?>
  }

  function poicat_onchange() {
    hidepoicaticon();
    jQuery("#img" + jQuery("#poicat").val()).show();
  }

  jQuery(document).ready(
    function() {
      var field = '<?php echo $field; ?>';

      if ((field != 'poi_category') && (field != 'poi_name')) {
        jQuery("#field").val('poi_category');
      } else {
        jQuery("#field").val(field);
        if (field == "poi_category") {
          jQuery("#poicat").val('<?php echo $keyword; ?>');
          jQuery("#keyword").val('');
        } else {
          jQuery("#keyword").val('<?php echo $keyword; ?>');
        }
      }

      field_onchange();
    }
  );
</script>
<div class="block-border">
  <form class="block-content form" name="frmsearch" id="frmsearch" onsubmit="javascript:return frmsearch_onsubmit()">
    <h1><?=$this->lang->line("lpoi_list"); ?> (<?=$total;?>)</h1>
    <div id="map" style="width: 100%; height: 400px;"></div>




    <table width="100%" cellpadding="3" class="tablelist">
      <tr>
        <td width="10%">
          <fieldset>
            <legend>
              <?=$this->lang->line("lsearchby");?>
            </legend>
            <select id="field" name="field" onchange="javascript:field_onchange()">
              <option value="poi_category">
                <?=$this->lang->line("lpoi_category");?>
              </option>
              <option value="poi_name">
                <?=$this->lang->line("lpoi_name");?>
              </option>
            </select>
            <span id='dvkeyword'><input type="text" name="keyword" id="keyword" value="" class="formdefault" /></span>
            <span id="dvpoicat">
						<select id="poicat" name="poicat" onchange="javascript:poicat_onchange()">
							<?php for($i=0; $i < count($poicats); $i++) { ?>
							<option value="<?php echo $poicats[$i]->poi_cat_id; ?>"><?php echo $poicats[$i]->poi_cat_name; ?></option>
							<?php } ?>
						</select>
					</span>
            <?php for($i=0; $i < count($poicats); $i++) { ?>
              <span id="img<?=$poicats[$i]->poi_cat_id;?>" style="display: none;">
						<img src="<?=base_url()?>assets/images/poi/<?=$poicats[$i]->poi_cat_icon;?>" border="0" />
					</span>
              <?php } ?>
                <input class="button" type="submit" value="<?=$this->lang->line(" lsearch ");?>" />
          </fieldset>
        </td>
      </tr>
    </table>
    <br />
    <a class="button" href="<?=base_url();?>poi/add"><font color="#0000ff"><?=$this->lang->line('ladd')?></font></a>
    <?php if ($this->sess->user_type != 2) { ?>
      <a class="button" href="<?=base_url();?>poi/import"><font color="#0000ff"><?=$this->lang->line('limport')?></font></a>
      <a class="button" href="<?=base_url();?>poi/suggest"><font color="#0000ff"><?=$this->lang->line('lsuggest_poi')?></font></a>
      <?php } ?>
  </form>

  <table class="table sortable no-margin" width="100%" cellpadding="3" class="tablelist" style="margin: 3px;">
    <thead>
      <tr>
        <th width="2%">No.</td>
          <th width="100px;" style="text-align: center">
            <?=$this->lang->line("licon"); ?>
          </th>
          <th>
            <?=$this->lang->line("lpoi_name"); ?>
          </th>
          <th width="20%">
            <?=$this->lang->line("laddress"); ?>
          </th>
          <th width="12%">
            <?=$this->lang->line("lcoordinate"); ?>
          </th>
          <th width="60px;">&nbsp;</th>
      </tr>
    </thead>
    <tbody>
      <?php
			for($i=0; $i < count($data); $i++)
			{
			?>
        <tr <?=($i%2) ? "class='odd'" : "";?>>
          <td>
            <?=$i+1+$offset?>
          </td>
          <?php if ($data[$i]->poi_cat_icon) { ?>
            <td style="text-align: center"><img src="<?=base_url()?>assets/images/poi/<?=$data[$i]->poi_cat_icon;?>" border="0" /></td>
            <?php } else { ?>
              <td style="text-align: center">&nbsp;</td>
              <?php } ?>
                <td>
                  <?=$data[$i]->poi_name;?>
                </td>
                <td>
                  <?=$data[$i]->location_address;?>
                </td>
                <td style="text-align: right;">
                  <?=$data[$i]->poi_latitude?> ,
                    <?=$data[$i]->poi_longitude?>&nbsp;</td>
                <td>
                  <?php if ($data[$i]->updated) { ?>
                    <a href="<?=base_url();?>poi/add/<?=$data[$i]->poi_id;?>"><img src="<?=base_url();?>assets/images/edit.gif" border="0" alt="<?=$this->lang->line("ledit_data"); ?>" title="<?=$this->lang->line("ledit_data"); ?>"></a>
                    <a href="<?=base_url();?>poi/remove/<?=$data[$i]->poi_id;?>" onclick="javascript: return confirm('<?=$this->lang->line(" lconfirm_delete "); ?>')"><img src="<?=base_url();?>assets/images/trash.gif" border="0" alt="<?=$this->lang->line("lremove_data"); ?>" title="<?=$this->lang->line("lremove_data"); ?>"></a>
                    <?php } else { ?>
                      &nbsp; &nbsp;
                      <?php } ?>
                        <a href="javascript:setcenter('<?=$data[$i]->poi_latitude?>', '<?=$data[$i]->poi_longitude?>')"><img src="<?=base_url();?>assets/images/zoomin.gif" border="0" alt="<?=$this->lang->line("lshow_map"); ?>" title="<?=$this->lang->line("lshow_map"); ?>"></a>
                </td>
        </tr>
        <?php
			}
			?>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="6">
          <?=$paging?>
        </td>
      </tr>
    </tfoot>
  </table>
  <footer>
    <div class="float-right">
      <a href="#top" class="button"><img src="<?php echo base_url();?>assets/newfarrasindo/images/icons/fugue/navigation-090.png" width="16" height="16" /> Page top</a>
    </div>
  </footer>
</div>
