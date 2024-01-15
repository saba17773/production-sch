<?php ob_start(); ?>
<!DOCTYPE html>
<html>
<style type="text/css">
    table {
	    border-collapse: collapse;
	    width: 100%;
	    font-size:10px;
    }

    td, tr, th {
        border: 1px solid #000000;
        text-align: center;
        padding: 5px;
        font-family:"Angsana New";
    }

    .table {
	    border-collapse: collapse;
	    width: 40%;
	    font-size: 11px;
    }

    .td, .tr, .th {
        border: 0px solid #000000;
        text-align: left;
        padding: 5px;
        font-family:"Angsana New";
    }
</style>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Production Report</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<style>
		body {
			font-size: 10px;
		}
	</style>
</head>
<body>

	<table>
    <thead>
  		<tr>
  			<td colspan="1" rowspan="2" >
  				<?php if ($_SESSION['user_company']=='DSI'){ ?>
  					<a class="navbar-brand"><img  src="./assets/images/DSI.png" style="padding-left:10px;height:45px; width:auto;" /></a>
  				<?php } ?>
  				<?php if ($_SESSION['user_company']=='SVO'){ ?>
  					<a class="navbar-brand"><img  src="./assets/images/SVO.png" style="padding-left:10px;height:40px; width:120px;" /></a>
  				<?php } ?>
  				<?php if ($_SESSION['user_company']=='DRB'){ ?>
  					<a class="navbar-brand"><img  src="./assets/images/DRB.png" style="padding-left:10px;height:40px; width:auto;" /></a>
  				<?php } ?>
  				<?php if ($_SESSION['user_company']=='DSL'){ ?>
  					<a class="navbar-brand"><img  src="./assets/images/DSL.png" style="padding-left:10px;height:35px; width:auto;" /></a>
  				<?php } ?>
  				<?php if ($_SESSION['user_company']=='STR'){ ?>
  					<a class="navbar-brand"><img  src="./assets/images/STR.png" style="padding-left:10px;height:40px; width:auto;" /></a>
  				<?php } ?>

  			</td>
  			<td colspan="3" rowspan="2">
  				<h2>
  					DSL   อบยาง BOM แยกตามชนิดการใช้งาน
  				</h2>
  			</td>
  		</tr>
  		<tr>
  			<td colspan="3" align="left">
  				<?php echo $date; ?>
  				<font color="red"><?php echo date('d/m/Y H:i'); ?></font>
  				<br><br>
  				กะ <?php echo $shift; ?>
  			</td>
  		</tr>
      <tr>
			<th rowspan="1" class="f10" width="120px">ชนิดยาง</th>
			<th rowspan="1" class="f10" width="160px">ชนิดยาง</th>
			<th rowspan="1" class="f10" width="80px">รวมพิมพ์เปิดอบ</th>
			<th rowspan="1" class="f10" width="80px">อบได้/วัน</th>
			<th rowspan="1" class="f10" width="100px">Size / ขอบ</th>
			<th rowspan="1" class="f10" width="100px">พิมพ์เปิดอบ</th>
			<th rowspan="1" class="f10" width="100px">อบได้/วัน</th>
    </tr>
    </thead>
    <tr>
    </tr>

<?php

$type ="";
foreach ($data as $key => $value)
{
			    // echo $key;
                // echo "<br>";
                echo "<tr>";
                    if ($type != $value['Type'])
                    {
                        echo "<td class='f12'  rowspan=".$value['rowspan'].">".$value['Type']."</td>";
                    }
                    echo "<td class='f12' >".$value['Type2']."</td>";
                    if ($type != $value['Type'])
                    {
                        echo "<td class='f12'  rowspan=".$value['rowspan'].">".$value['TT']."</td>";
                        echo "<td class='f12'  rowspan=".$value['rowspan'].">".$value['TCD']."</td>";
                    }
                    echo "<td class='f12' >".$value['Size']."</td>";
                    echo "<td class='f12' >".$value['CountPrint']."</td>";
                    echo "<td class='f12' >".$value['GreentireDay']."</td>";

               echo "</tr>";
               $type = $value['Type'];
              // $type = $key;

}


 ?>

 <tr>
    <th colspan="2">
      Total
    </th>
    <th>	<?php
      $sumtarget = 0;
          foreach ($data as $k => $v) {
            $sumtarget += $v['Countprintcure'];
          }
          echo $sumtarget;
      ?></th>
    <th>	<?php
      $sumtarget1 = 0;
          foreach ($data as $k => $v) {
            $sumtarget1 += $v['CureDay'];
          }
          echo $sumtarget1;?></th>
    <th></th>
    <th><?php
      $sumtarget2 = 0;
          foreach ($data as $k => $v) {
            $sumtarget2 += $v['CountPrint'];
          }
          echo $sumtarget2;?></th>
    <th><?php
      $sumtarget3 = 0;
          foreach ($data as $k => $v) {
            $sumtarget3 += $v['GreentireDay'];
          }
          echo $sumtarget3;?></th>


  </tr>

















	</table>

</body>
</html>

<?php

$html = ob_get_contents();
ob_end_clean();
$pdf = new mPDF('th','A4', 0, '', 3, 3, 3, 3);
$pdf->SetDisplayMode('fullpage');
$pdf->WriteHTML($html);
$pdf->Output();
