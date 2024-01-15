<?php ob_start(); ?>
<!DOCTYPE html>
<html>
<style type="text/css">
    table {
	    border-collapse: collapse;
	    width: 9000px;
    }

    td, tr, th {
        border: 1px solid #848689;
        text-align: center;
        padding: 5px;
        font-family:"Cordia New";
        font-size: 16px;
    }
    .td {
    	text-align: left;
    }
</style>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Production Report</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>

	<table>
		<tr>
			<td colspan="2">
				<?php if ($_SESSION['user_company']=='DSI'){ ?>
					<img  src="/assets/images/DSI.png" style="padding-left:10px;height:45px; width:auto;" />
				<?php } ?>
				<?php if ($_SESSION['user_company']=='SVO'){ ?>
					<img  src="/assets/images/SVO.png" style="padding-left:10px;height:40px; width:120px;" />
				<?php } ?>
				<?php if ($_SESSION['user_company']=='DRB'){ ?>
					<img  src="./assets/images/DRB.png" style="padding-left:10px;height:40px; width:auto;" />
				<?php } ?>
				<?php if ($_SESSION['user_company']=='DSL'){ ?>
					<img  src="/assets/images/DSL.png" style="padding-left:10px;height:35px; width:auto;" />
				<?php } ?>
				<?php if ($_SESSION['user_company']=='STR'){ ?>
					<img  src="/assets/images/STR.png" style="padding-left:10px;height:40px; width:auto;" />
				<?php } ?>
				
			</td>
			<td colspan="140" class="td">
				<h2>
					สรุป การอบยางแผนกอบยางนอกรถยนต์ประจำ  เดือน  <?php echo $monththai." พ.ศ.".$yearthai; ?>
				</h2>
			</td>
		</tr>

		<tr style="background-color: #35ABAF; color: white;"> 
			<th rowspan="2" width="40px;">ลำดับ</th> 
			<th rowspan="2" width="100px;">ITEM</th> 
			<th rowspan="2" width="160px;">SIZE</th> 

			<?php 
				for ($i=1; $i <= $nummonth; $i++) { 
					$arr_sum = [
						"AC" => [],
						"AD" => [],
						"NW" => [],
						"W"  => []
					];
			?>
			<th colspan="2" width="200px;"><?php echo $i; ?></th> 
			<?php } ?>

			<th colspan="3" width="250px;">รวม</th> 

		</tr> 
		<tr style="background-color: #13A19C; color: white;" valign="top"> 
			<?php  
				for ($i=1; $i <= $nummonth; $i++) { 
			?>
			<th width="50px;">C</th> 
			<th width="50px;">D</th> 
			
			<?php } ?>

			<th width="50px;" style="background-color: #F7E565; color: black;">C</th> 
			<th width="50px;" style="background-color: #F7E565; color: black;">D</th> 
			<th width="50px;" style="background-color: #F7E565; color: black;">C+D</th> 
		</tr> 

		<?php  
			foreach ($data as $k => $v) {
		?>

		<tr style="background-color: #F7FFD8;">
			<td><?php echo $k+1; ?></td>
			<td><?php echo $v['ItemID']; ?></td>
			<td class="td"><?php echo $v['ItemName']; ?></td>

			<?php
				$sumactualc = 0; 
				$sumactuald = 0; 
				$sumnetweight = 0;
				$sumweight = 0;
				$dx = '';

				foreach ($v['DaysLists'] as $d => $l) {

					if ($d==0) {
						$ac_0 += $l['ActualC'];
						$ad_0 += $l['ActualD'];
						$w_0 += $l['Weight'];
						$nw_0 += $l['NetWeight'];

						$arr_sum['AC'][$d]=$ac_0;
						$arr_sum['AD'][$d]=$ad_0;
						$arr_sum['W'][$d]=$w_0;
						$arr_sum['NW'][$d]=$nw_0;
					}

					if ($d==1) {
						$ac_1 += $l['ActualC'];
						$ad_1 += $l['ActualD'];
						$w_1 += $l['Weight'];
						$nw_1 += $l['NetWeight'];

						$arr_sum['AC'][$d]=$ac_1;
						$arr_sum['AD'][$d]=$ad_1;
						$arr_sum['W'][$d]=$w_1;
						$arr_sum['NW'][$d]=$nw_1;
					}

					if ($d==2) {
						$ac_2 += $l['ActualC'];
						$ad_2 += $l['ActualD'];
						$w_2 += $l['Weight'];
						$nw_2 += $l['NetWeight'];

						$arr_sum['AC'][$d]=$ac_2;
						$arr_sum['AD'][$d]=$ad_2;
						$arr_sum['W'][$d]=$w_2;
						$arr_sum['NW'][$d]=$nw_2;
					}

					if ($d==3) {
						$ac_3 += $l['ActualC'];
						$ad_3 += $l['ActualD'];
						$w_3 += $l['Weight'];
						$nw_3 += $l['NetWeight'];

						$arr_sum['AC'][$d]=$ac_3;
						$arr_sum['AD'][$d]=$ad_3;
						$arr_sum['W'][$d]=$w_3;
						$arr_sum['NW'][$d]=$nw_3;
					}

					if ($d==4) {
						$ac_4 += $l['ActualC'];
						$ad_4 += $l['ActualD'];
						$w_4 += $l['Weight'];
						$nw_4 += $l['NetWeight'];

						$arr_sum['AC'][$d]=$ac_4;
						$arr_sum['AD'][$d]=$ad_4;
						$arr_sum['W'][$d]=$w_4;
						$arr_sum['NW'][$d]=$nw_4;
					}

					if ($d==5) {
						$ac_5 += $l['ActualC'];
						$ad_5 += $l['ActualD'];
						$w_5 += $l['Weight'];
						$nw_5 += $l['NetWeight'];

						$arr_sum['AC'][$d]=$ac_5;
						$arr_sum['AD'][$d]=$ad_5;
						$arr_sum['W'][$d]=$w_5;
						$arr_sum['NW'][$d]=$nw_5;
					}

					if ($d==6) {
						$ac_6 += $l['ActualC'];
						$ad_6 += $l['ActualD'];
						$w_6 += $l['Weight'];
						$nw_6 += $l['NetWeight'];

						$arr_sum['AC'][$d]=$ac_6;
						$arr_sum['AD'][$d]=$ad_6;
						$arr_sum['W'][$d]=$w_6;
						$arr_sum['NW'][$d]=$nw_6;
					}

					if ($d==7) {
						$ac_7 += $l['ActualC'];
						$ad_7 += $l['ActualD'];
						$w_7 += $l['Weight'];
						$nw_7 += $l['NetWeight'];

						$arr_sum['AC'][$d]=$ac_7;
						$arr_sum['AD'][$d]=$ad_7;
						$arr_sum['W'][$d]=$w_7;
						$arr_sum['NW'][$d]=$nw_7;
					}

					if ($d==8) {
						$ac_8 += $l['ActualC'];
						$ad_8 += $l['ActualD'];
						$w_8 += $l['Weight'];
						$nw_8 += $l['NetWeight'];

						$arr_sum['AC'][$d]=$ac_8;
						$arr_sum['AD'][$d]=$ad_8;
						$arr_sum['W'][$d]=$w_8;
						$arr_sum['NW'][$d]=$nw_8;
					}

					if ($d==9) {
						$ac_9 += $l['ActualC'];
						$ad_9 += $l['ActualD'];
						$w_9 += $l['Weight'];
						$nw_9 += $l['NetWeight'];

						$arr_sum['AC'][$d]=$ac_9;
						$arr_sum['AD'][$d]=$ad_9;
						$arr_sum['W'][$d]=$w_9;
						$arr_sum['NW'][$d]=$nw_9;
					}

					if ($d==10) {
						$ac_10 += $l['ActualC'];
						$ad_10 += $l['ActualD'];
						$w_10 += $l['Weight'];
						$nw_10 += $l['NetWeight'];

						$arr_sum['AC'][$d]=$ac_10;
						$arr_sum['AD'][$d]=$ad_10;
						$arr_sum['W'][$d]=$w_10;
						$arr_sum['NW'][$d]=$nw_10;
					}

					if ($d==11) {
						$ac_11 += $l['ActualC'];
						$ad_11 += $l['ActualD'];
						$w_11 += $l['Weight'];
						$nw_11 += $l['NetWeight'];

						$arr_sum['AC'][$d]=$ac_11;
						$arr_sum['AD'][$d]=$ad_11;
						$arr_sum['W'][$d]=$w_11;
						$arr_sum['NW'][$d]=$nw_11;
					}

					if ($d==12) {
						$ac_12 += $l['ActualC'];
						$ad_12 += $l['ActualD'];
						$w_12 += $l['Weight'];
						$nw_12 += $l['NetWeight'];

						$arr_sum['AC'][$d]=$ac_12;
						$arr_sum['AD'][$d]=$ad_12;
						$arr_sum['W'][$d]=$w_12;
						$arr_sum['NW'][$d]=$nw_12;
					}

					if ($d==13) {
						$ac_13 += $l['ActualC'];
						$ad_13 += $l['ActualD'];
						$w_13 += $l['Weight'];
						$nw_13 += $l['NetWeight'];

						$arr_sum['AC'][$d]=$ac_13;
						$arr_sum['AD'][$d]=$ad_13;
						$arr_sum['W'][$d]=$w_13;
						$arr_sum['NW'][$d]=$nw_13;
					}

					if ($d==14) {
						$ac_14 += $l['ActualC'];
						$ad_14 += $l['ActualD'];
						$w_14 += $l['Weight'];
						$nw_14 += $l['NetWeight'];

						$arr_sum['AC'][$d]=$ac_14;
						$arr_sum['AD'][$d]=$ad_14;
						$arr_sum['W'][$d]=$w_14;
						$arr_sum['NW'][$d]=$nw_14;
					}

					if ($d==15) {
						$ac_15 += $l['ActualC'];
						$ad_15 += $l['ActualD'];
						$w_15 += $l['Weight'];
						$nw_15 += $l['NetWeight'];

						$arr_sum['AC'][$d]=$ac_15;
						$arr_sum['AD'][$d]=$ad_15;
						$arr_sum['W'][$d]=$w_15;
						$arr_sum['NW'][$d]=$nw_15;
					}

					if ($d==16) {
						$ac_16 += $l['ActualC'];
						$ad_16 += $l['ActualD'];
						$w_16 += $l['Weight'];
						$nw_16 += $l['NetWeight'];

						$arr_sum['AC'][$d]=$ac_16;
						$arr_sum['AD'][$d]=$ad_16;
						$arr_sum['W'][$d]=$w_16;
						$arr_sum['NW'][$d]=$nw_16;
					}

					if ($d==17) {
						$ac_17 += $l['ActualC'];
						$ad_17 += $l['ActualD'];
						$w_17 += $l['Weight'];
						$nw_17 += $l['NetWeight'];

						$arr_sum['AC'][$d]=$ac_17;
						$arr_sum['AD'][$d]=$ad_17;
						$arr_sum['W'][$d]=$w_17;
						$arr_sum['NW'][$d]=$nw_17;
					}

					if ($d==18) {
						$ac_18 += $l['ActualC'];
						$ad_18 += $l['ActualD'];
						$w_18 += $l['Weight'];
						$nw_18 += $l['NetWeight'];

						$arr_sum['AC'][$d]=$ac_18;
						$arr_sum['AD'][$d]=$ad_18;
						$arr_sum['W'][$d]=$w_18;
						$arr_sum['NW'][$d]=$nw_18;
					}

					if ($d==19) {
						$ac_19 += $l['ActualC'];
						$ad_19 += $l['ActualD'];
						$w_19 += $l['Weight'];
						$nw_19 += $l['NetWeight'];

						$arr_sum['AC'][$d]=$ac_19;
						$arr_sum['AD'][$d]=$ad_19;
						$arr_sum['W'][$d]=$w_19;
						$arr_sum['NW'][$d]=$nw_19;
					}

					if ($d==20) {
						$ac_20 += $l['ActualC'];
						$ad_20 += $l['ActualD'];
						$w_20 += $l['Weight'];
						$nw_20 += $l['NetWeight'];

						$arr_sum['AC'][$d]=$ac_20;
						$arr_sum['AD'][$d]=$ad_20;
						$arr_sum['W'][$d]=$w_20;
						$arr_sum['NW'][$d]=$nw_20;
					}

					if ($d==21) {
						$ac_21 += $l['ActualC'];
						$ad_21 += $l['ActualD'];
						$w_21 += $l['Weight'];
						$nw_21 += $l['NetWeight'];

						$arr_sum['AC'][$d]=$ac_21;
						$arr_sum['AD'][$d]=$ad_21;
						$arr_sum['W'][$d]=$w_21;
						$arr_sum['NW'][$d]=$nw_21;
					}

					if ($d==22) {
						$ac_22 += $l['ActualC'];
						$ad_22 += $l['ActualD'];
						$w_22 += $l['Weight'];
						$nw_22 += $l['NetWeight'];

						$arr_sum['AC'][$d]=$ac_22;
						$arr_sum['AD'][$d]=$ad_22;
						$arr_sum['W'][$d]=$w_22;
						$arr_sum['NW'][$d]=$nw_22;
					}

					if ($d==23) {
						$ac_23 += $l['ActualC'];
						$ad_23 += $l['ActualD'];
						$w_23 += $l['Weight'];
						$nw_23 += $l['NetWeight'];

						$arr_sum['AC'][$d]=$ac_23;
						$arr_sum['AD'][$d]=$ad_23;
						$arr_sum['W'][$d]=$w_23;
						$arr_sum['NW'][$d]=$nw_23;
					}

					if ($d==24) {
						$ac_24 += $l['ActualC'];
						$ad_24 += $l['ActualD'];
						$w_24 += $l['Weight'];
						$nw_24 += $l['NetWeight'];

						$arr_sum['AC'][$d]=$ac_24;
						$arr_sum['AD'][$d]=$ad_24;
						$arr_sum['W'][$d]=$w_24;
						$arr_sum['NW'][$d]=$nw_24;
					}

					if ($d==25) {
						$ac_25 += $l['ActualC'];
						$ad_25 += $l['ActualD'];
						$w_25 += $l['Weight'];
						$nw_25 += $l['NetWeight'];

						$arr_sum['AC'][$d]=$ac_25;
						$arr_sum['AD'][$d]=$ad_25;
						$arr_sum['W'][$d]=$w_25;
						$arr_sum['NW'][$d]=$nw_25;
					}

					if ($d==26) {
						$ac_26 += $l['ActualC'];
						$ad_26 += $l['ActualD'];
						$w_26 += $l['Weight'];
						$nw_26 += $l['NetWeight'];

						$arr_sum['AC'][$d]=$ac_26;
						$arr_sum['AD'][$d]=$ad_26;
						$arr_sum['W'][$d]=$w_26;
						$arr_sum['NW'][$d]=$nw_26;
					}

					if ($d==27) {
						$ac_27 += $l['ActualC'];
						$ad_27 += $l['ActualD'];
						$w_27 += $l['Weight'];
						$nw_27 += $l['NetWeight'];

						$arr_sum['AC'][$d]=$ac_27;
						$arr_sum['AD'][$d]=$ad_27;
						$arr_sum['W'][$d]=$w_27;
						$arr_sum['NW'][$d]=$nw_27;
					}

					if ($d==28) {
						$ac_28 += $l['ActualC'];
						$ad_28 += $l['ActualD'];
						$w_28 += $l['Weight'];
						$nw_28 += $l['NetWeight'];

						$arr_sum['AC'][$d]=$ac_28;
						$arr_sum['AD'][$d]=$ad_28;
						$arr_sum['W'][$d]=$w_28;
						$arr_sum['NW'][$d]=$nw_28;
					}

					if ($d==29) {
						$ac_29 += $l['ActualC'];
						$ad_29 += $l['ActualD'];
						$w_29 += $l['Weight'];
						$nw_29 += $l['NetWeight'];

						$arr_sum['AC'][$d]=$ac_29;
						$arr_sum['AD'][$d]=$ad_29;
						$arr_sum['W'][$d]=$w_29;
						$arr_sum['NW'][$d]=$nw_29;
					}

					if ($d==30) {
						$ac_30 += $l['ActualC'];
						$ad_30 += $l['ActualD'];
						$w_30 += $l['Weight'];
						$nw_30 += $l['NetWeight'];

						$arr_sum['AC'][$d]=$ac_30;
						$arr_sum['AD'][$d]=$ad_30;
						$arr_sum['W'][$d]=$w_30;
						$arr_sum['NW'][$d]=$nw_30;
					}

					$summary_allmonth_c += $l['ActualC'];
					$summary_allmonth_d += $l['ActualD'];
					$summary_allmonth_netweight += $l['NetWeight'];
					$summary_allmonth_weight += $l['Weight'];
	
			?>
				<td><?php echo $l['ActualC']; ?></td>
				<td><?php echo $l['ActualD']; ?></td>
			<?php 

				$sumactualc += $l['ActualC'];
				$sumactuald += $l['ActualD'];
				$sumnetweight += $l['NetWeight'];
				$sumweight += $l['Weight'];

				} 
			?>
			
			<td><?php echo $sumactualc; $sumactualc_all += $sumactualc; ?></td>
			<td><?php echo $sumactuald; $sumactuald_all += $sumactuald; ?></td>
			<td><?php $sumactualcd = $sumactualc+$sumactuald; echo $sumactualcd; $sumactualcd_all += $sumactualcd; ?></td>
		</tr>

		<?php } ?>

		<tr style="background-color: #35ABAF; color: white;">
			<th colspan="3">
				Total
			</th>
			<?php 
				for ($i=0; $i < $nummonth; $i++) { 
					echo "<th>".$arr_sum['AC'][$i]."</th>";
					echo "<th>".$arr_sum['AD'][$i]."</th>";
				}
			?>
			<th style="background-color: #F7E565; color: black;"><?php echo $sumactualc_all; ?></th>
			<th style="background-color: #F7E565; color: black;"><?php echo $sumactuald_all; ?></th>
			<th style="background-color: #F7E565; color: black;"><?php echo $sumactualcd_all; ?></th>
		</tr>

	</table>

</body>
</html>

<?php

// $html = ob_get_contents();
// ob_end_clean();
// $pdf = new mPDF('th','A1-L', 0, '', 3, 3, 3, 3);  
// $pdf->SetDisplayMode('fullpage');
// $pdf->WriteHTML($html);
// $pdf->Output();