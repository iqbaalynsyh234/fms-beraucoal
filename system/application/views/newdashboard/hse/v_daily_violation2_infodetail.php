<thead>
    <tr>
        <th colspan="12" class="titletable"> Daily Violation <?= $company . "(" . $sdate . " s.d. " . $edate . ")";
                                                            //var_dump($master_video); 
                                                            ?> </th>
    </tr>
    <tr>
        <th>Date</th>
        <th>Time</th>
		<th>Shift</th>
        <th>Violation</th>
		<th>Company</th>
        <th>Vehicle</th>
        <th>Location</th>
		<th>Alarm Level</th>
		<th>Speed (kph)</th>
		<th>Jalur</th>
       <!--<th>Info</th>-->
        <th>Coordinate</th>
        <th colspan="1">Attachment</th>
    </tr>
</thead>
<tbody>
    <?php
    asort($data);
    if ($total_data > 0) {
        for ($i = 0; $i < $total_data; $i++) {
            $daten = date("Y-m-d", strtotime($data[$i]['date'])); ?>
            <tr>
                <td><?= $daten; ?></td>
                <td><?= $data[$i]['time']; ?></td>
				<td><?= $data[$i]['shift']; ?></td>
                <td><?= $data[$i]['violation']; ?></td>
				<td><?= $data[$i]['company']; ?></td>
                <td><?= $data[$i]['vehicle']; ?></td>
                <td><?= $data[$i]['location']; ?></td>
                <td><?= $data[$i]['level']; ?></td>
				<td><?= $data[$i]['speed']; ?></td>
				<td><?= $data[$i]['jalur']; ?></td>
                <!--<td><?= $data[$i]['info']; ?></td>-->
				<td><?= $data[$i]['coordinate']; ?></td>
				
                <!--<td><a href="https://www.google.com/maps/?q=<?= $data[$i]['coordinate']; ?>" target="_blank"><?= $data[$i]['coordinate']; ?></a></td>-->
				
               <!-- <td>
                    <?php if ($data[$i]['violation'] != "Overspeed") { ?>
                        <a href="<?= $data[$i]['file_url']; ?>" target="_blank" class="attachment">
                            <img src="<?= $data[$i]['file_url']; ?>" alt="link" style="width:100px;height:100px;">
                        </a>
                    <?php } ?>
				</td> -->
				
                <td>
                    <?php if ($data[$i]['violation'] != "Overspeed") { ?>
                        <!--<a href="<?php echo base_url() . 'hse/view_video/' . $db_table . '/' . $data[$i]['vehicle'] . '/' . $daten . '/' . $data[$i]['time']; ?>" class="btn btn-primary btn-sm" title="watch video" target="_blank" class="attachment">
                            <span class="fa fa-file-archive-o"></span>
                        </a>-->
						<a href="<?= $data[$i]['attachment_url']; ?>" class="btn btn-primary btn-sm" title="watch video" target="_blank" class="attachment">
                            <span class="fa fa-file-archive-o"></span>
                        </a>
						
                    <?php } ?>
				</td>
				
            </tr>
    <?php }
    } ?>
</tbody>