<div style="position: absolute; margin: 0; padding: 0; z-index: 1000; width: 100%;"> 
	<?=$navigation;?>
	<div id="main" style="margin: 20px;">
		<br />&nbsp;
		<h1>Contact Information</h1>
		<table width="100%" cellpadding="3" class="tablelist">
			<?php
				for ($i=0; $i<count($data); $i++)
				{
			?>
					<tr>
						<td width="10%">
							Nama
						</td>
						<td>:</td>
						<td>
							<?=$data[$i]->user_name;?>
						</td>
					</tr>
					
					<tr>
						<td width="10%">
							Telp
						</td>
						<td>:</td>
						<td>
							<?=$data[$i]->user_phone;?>
						</td>
					</tr>
					
					<tr>
						<td width="10%">
							Hand Phone
						</td>
						<td>:</td>
						<td>
							<?=$data[$i]->user_mobile;?>
						</td>
					</tr>
					
					<tr>
						<td width="10%">
							Email
						</td>
						<td>:</td>
						<td>
							<?=$data[$i]->user_mail;?>
						</td>
					</tr>
					
					<tr>
						<td width="10%">
							Address
						</td>
						<td>:</td>
						<td>
							<?=$data[$i]->user_address;?>
						</td>
					</tr>
					
					<tr>
						<td width="10%">
							City
						</td>
						<td>:</td>
						<td>
							<?=$data[$i]->user_city;?>
						</td>
					</tr>
					
					<tr>
						<td width="10%">
							Province
						</td>
						<td>:</td>
						<td>
							<?=$data[$i]->user_province;?>
						</td>
					</tr>
			<?php
				}
			?>
		</table>
	</div>