<?php 
	header("Content-type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=BuildSch_Tempalte".Date("Ymd_His").".xls");
	use App\V2\BuildSch\BuildSchAPI;
	$date = date('Y-m-d', strtotime($_GET["ExportDate"]));
	$shift = $_GET["ExportShift"];

	if ($shift==1) {
		$shiftname = "กลางวัน";
	}else{
		$shiftname = "กลางคืน";
	}
	$data = (new BuildSchAPI)->getGreentireList($date);
	// echo json_encode($data);
	// print_r($data);

	echo "<table border=1>";
		echo "<tr bgcolor='#ffcd38'>";
		echo "<th rowspan=2>";
		echo "ItemId";
		echo "</th>";
		echo "<th rowspan=2>";
		echo "Size";
		echo "</th>";
		echo "<th rowspan=2>";
		echo "Color";
		echo "</th>";
		echo "<th rowspan=2>";
		echo "ออเดอร์สัปดาห์";
		echo "</th>";
		echo "<th rowspan=2>";
		echo "ตัวเลข BL";
		echo "</th>";
		echo "<th rowspan=2>";
		echo "BL";
		echo "</th>";
		echo "<th colspan=4>";
		echo "แผนผลิตแผนกสร้างโครงประจำวัน";
		echo "</th>";
		echo "<th rowspan=2>";
		echo "หมายเหตุ";
		echo "</th>";
		echo "<th rowspan=2>";
		echo "สร้างยางเกิน/ขาด";
		echo "</th>";
		echo "</tr>";

		echo "<tr bgcolor='#ffe69b'>";
		echo "<td>";
		echo "แผนผลิตกะ".$shiftname."(เส้น)";
		echo "</td>";
		echo "<td>";
		echo "ปรับแผนผลิต";
		echo "</td>";
		echo "<td>";
		echo "แผนผลิตกะ".$shiftname."(เส้น)";
		echo "</td>";
		echo "<td>";
		echo "ผลิตได้กะ".$shiftname."(เส้น)";
		echo "</td>";
		echo "</tr>";
	foreach ($data as $key => $value) {
		echo "<tr>";
		echo "<td>";
		echo $value['ItemId'];
		echo "</td>";
		echo "<td>";
		echo $value['ItemGTName'];
		echo "</td>";
		echo "<td>";
		$color = '';
		if ($value['Color']!=NULL) {
			$color1 = $value['Color'];
			$color .= $color1;
		}
		if ($value['Color2']!=NULL) {
			$color2 = $value['Color2'];
			$color .= "/".$color2;
		}
		if ($value['Color3']!=NULL) {
			$color3 = $value['Color3'];
			$color .= "/".$color3;
		}
		if ($value['Color4']!=NULL) {
			$color4 = $value['Color4'];
			$color .= "/".$color4;
		}
		if ($value['Color5']!=NULL) {
			$color5 = $value['Color5'];
			$color .= "/".$color5;
		}
		echo $color;
		echo "</td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "</tr>";
	}
	echo "</table>";
?>
