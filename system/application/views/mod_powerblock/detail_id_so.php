<script>
	jQuery.maxZIndex = jQuery.fn.maxZIndex = function(opt) {
	    var def = { inc: 10, group: "*" };
	    jQuery.extend(def, opt);
	    var zmax = 0;
	    jQuery(def.group).each(function() {
	        var cur = parseInt(jQuery(this).css('z-index'));
	        zmax = cur > zmax ? cur : zmax;
	    });
	    if (!this.jquery)
	        return zmax;
	
	    return this.each(function() {
	        zmax += def.inc;
	        jQuery(this).css("z-index", zmax);
	    });
	}
	
	jQuery(document).ready(
		function()
		{
			showclock();
		}
	);
</script>

<div id="main_data">
		<form id="frmidbooking">		
			<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
			
				<tr>
					<td style="text-align:center" colspan="2">
						<h2>SURAT JALAN SO BLOCK( DETAIL )</h2>
						Ship Date : <?php if (isset($data)) { echo date("d-m-Y", strtotime($data->suratjalan_ship_date)); } ?>
					</td>
				</tr>
				
				
				<tr>
					<td style="text-align:left">VEHICLE NO</td>
					<td><?php if (isset($data)) { echo $data->suratjalan_vehicle_no; } ?></td>
				</tr>
				
				<tr>
					<td style="text-align:left">SURAT JALAN SALES ORDER BLOCK</td>
					<td><?php if (isset($data)) { echo $data->suratjalan_sales_order_block; } ?></td>
				</tr>
				
				<tr>
					<td style="text-align:left">SURAT JALAN SHIP BLOCK</td>
					<td><?php if (isset($data)) { echo $data->suratjalan_ship_block; } ?></td>
				</tr>
				
				<tr>
					<td style="text-align:left">SHIP DATE</td>
					<td><?php if (isset($data)) { echo $data->suratjalan_ship_date; } ?></td>
				</tr>
				
				<tr>
					<td style="text-align:left">CUSTOMER NO</td>
					<td><?php if (isset($data)) { echo $data->suratjalan_cust_no; } ?></td>
				</tr>
				
				<tr>
					<td style="text-align:left">SHIP NAME</td>
					<td><?php if (isset($data)) { echo $data->suratjalan_ship_name; } ?></td>
				</tr>
				
				<tr>
					<td style="text-align:left">SHIP ADDRESS</td>
					<td><?php if (isset($data)) { echo $data->suratjalan_ship_address; } ?></td>
				</tr>
				
				<tr>
					<td style="text-align:left">SHIP ADDRESS 2</td>
					<td><?php if (isset($data)) { echo $data->suratjalan_ship_address2; } ?></td>
				</tr>
				
				<tr>
					<td style="text-align:left">SALES CODE</td>
					<td><?php if (isset($data)) { echo $data->suratjalan_sales_code; } ?></td>
				</tr>
				
				<tr>
					<td style="text-align:left">SHIP AGEN CODE</td>
					<td><?php if (isset($data)) { echo $data->suratjalan_ship_agen_code; } ?></td>
				</tr>
				
				<tr>
					<td style="text-align:left">TRAVEL EXPENCE AMOUNT</td>
					<td><?php if (isset($data)) { echo $data->suratjalan_travel_expense_amount; } ?></td>
				</tr>
				
				<tr>
					<td style="text-align:left">ITEM TYPE</td>
					<td><?php if (isset($data)) { echo $data->suratjalan_item_type; } ?></td>
				</tr>
				
				<tr>
					<td style="text-align:left">ITEM NO</td>
					<td><?php if (isset($data)) { echo $data->suratjalan_item_no; } ?></td>
				</tr>
				
				<tr>
					<td style="text-align:left">DESCRIPTION</td>
					<td><?php if (isset($data)) { echo $data->suratjalan_desc; } ?></td>
				</tr>
				
				<tr>
					<td style="text-align:left">QTY</td>
					<td><?php if (isset($data)) { echo $data->suratjalan_qty; } ?></td>
				</tr>
				
				<tr>
					<td style="text-align:left">SURAT JALAN UOM CODE</td>
					<td><?php if (isset($data)) { echo $data->suratjalan_uom_code; } ?></td>
				</tr>
				
				<tr>
					<td style="text-align:left">SURAT JALAN AMOUNT</td>
					<td><?php if (isset($data)) { echo $data->suratjalan_amount; } ?></td>
				</tr>
				
				
				
				
				
			</table>
		</form>
</div>