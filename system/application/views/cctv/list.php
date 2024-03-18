<html>
	<head>
		<title>Daftar CCTV</title>
	<head>
	<body bgcolor="#000000">		
		<table width="100%" cellpadding="10" cellspacing="0">
			<tr>
				<td colspan="2" align="center"><font color="#ffffff" size="+1">Daftar CCTV (<?php echo count($rows); ?>)</font></td>
			</tr>
			<tr>
				<td width="5%" style="border-top: 1px #ffffff solid; color: #000000; background: #ffffff;">&nbsp;No</td>
				<td style="border-top: 1px #ffffff solid; color: #000000; background: #ffffff;">&nbsp;Lokasi</td>
			</tr>
			<?php
		for($i=0; $i < count($rows); $i++)
		{
		?>
			<tr>
				<td style="border-top: 1px #333333 solid; border-left: 1px #ffffff solid; color: #cccccc;" align="right" valign="top"><?php echo $i+1; ?>&nbsp;</td>
				<td style="border-top: 1px #333333 solid; border-left: 1px #ffffff solid; color: #cccccc;" valign="top">&nbsp;<?php echo nl2br($rows[$i]->cctv_desc); ?></td>
			</tr>
		
		<?php
		}
			?>
			<tr>
				<td colspan="2" style="border-top: 1px #ffffff solid; color: #333333;">&nbsp;</td>
			</tr>
		</table>
	</body>
</html>