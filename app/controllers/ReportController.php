<?php

namespace App\Controllers;

use App\Services\ReportService;
use App\Models\InventTrans;
use App\Components\Security;
use App\Components\Authentication;
use App\Services\WarehouseService;

class ReportController
{
	public function __construct()
	{
		$this->secure = new Security;
		$this->report = new ReportService;
		$this->auth = new Authentication;

		if ($this->auth->isLogin() === false) {
			renderView('page/login');
			exit;
		}
	}

	public function greentireScrap()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}

		renderView("page/report_greentire_scrap");
	}

	public function curetireScrap()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}

		renderView("page/report_curetire_scrap");
	}

	public function greentireScrapPdf($date, $item_group)
	{
		$product_group = 'TBR';
		if ($item_group === 'tbr') {
			$product_group = 'TBR';
		} else {
			$product_group = 'RDT';
		}

		$data = $this->report->greentireScrap($date, $product_group);
		// var_dump($data); exit;
		renderView("page/report_greentire_scrap_pdf", [
			"data" => $data,
			"date" => $date
		]);
	}

	public function curetireScrapPdf($date, $item_group)
	{
		$product_group = 'TBR';
		if ($item_group === 'tbr') {
			$product_group = 'TBR';
		} else {
			$product_group = 'RDT';
		}

		$data = $this->report->curetireScrap($date, $product_group);
		renderView("page/report_curetire_scrap_pdf", [
			"data" => $data,
			"date" => $date
		]);
	}

	public function curingReport()
	{
		renderView("report/curing");
	}

	public function curingReportPdf()
	{
		$date = filter_input(INPUT_POST, "date");
		$shift = filter_input(INPUT_POST, "shift");
		$switch = filter_input(INPUT_POST, "switch");

		$getData = $this->report->curingReportPdf($date);

		$q2_array = [];

		$all_tire = [];
		foreach ($getData as $value) {
			$all_tire[] = $value;
		}

		$data = [];

		for ($i = 0; $i < count($all_tire); $i++) {

			if ($all_tire[$i]["Q1"] !== "" && $all_tire[$i]["Q1"] !== null) {
				$all_tire[$i]["Q1"] = trim($all_tire[$i]["Q1"] . "," . $all_tire[$i - 1]["Q1"], ",");
			} else {
				$all_tire[$i]["Q1"] = "";
			}

			if ($all_tire[$i]["Q2"] !== "" && $all_tire[$i]["Q2"] !== null) {
				$all_tire[$i]["Q2"] = trim($all_tire[$i]["Q2"] . "," . $all_tire[$i - 1]["Q2"], ",");
			} else {
				$all_tire[$i]["Q2"] = "";
			}

			if ($all_tire[$i]["Q3"] !== "" && $all_tire[$i]["Q3"] !== null) {
				$all_tire[$i]["Q3"] = trim($all_tire[$i]["Q3"] . "," . $all_tire[$i - 1]["Q3"], ",");
			} else {
				$all_tire[$i]["Q3"] = "";
			}

			if ($all_tire[$i]["Q4"] !== "" && $all_tire[$i]["Q4"] !== null) {
				$all_tire[$i]["Q4"] = trim($all_tire[$i]["Q4"] . "," . $all_tire[$i - 1]["Q4"], ",");
			} else {
				$all_tire[$i]["Q4"] = "";
			}
		}

		echo "<pre>" . print_r($all_tire, true) . "</pre>";
	}

	public function genbuildingPDF()
	{
		if (!isset($_POST['item_group'])) {
			header('Content-Type: text/html; charset=utf-8');
			echo "กรุณาเลือกชนิดของ Product Group<br/>";
			echo "<a href='/report/building'>กลับไป</a>";
			exit;
		}

		$date_building = filter_input(INPUT_POST, "date_building");
		$shift = filter_input(INPUT_POST, "shift");
		$group = filter_input(INPUT_POST, "group");

		$item_group = $_POST['item_group'];

		$product_group = 'TBR';
		if ($item_group === 'tbr') {
			$product_group = 'TBR';
		} else {
			$product_group = 'RDT';
		}

		$datebuilding = date('Y-m-d', strtotime($date_building));

		$arr = $this->report->BuildingServiceallpdf($datebuilding, $shift, $group, $product_group);
		//$arr = BuildingService::allpdf($datebuilding,$shift,$group);
		$json_decode  = json_decode($arr);

		$number = count(array_filter($json_decode));
		$numberall = (29 - $number);

		$fake_data = [
			[0], //1
			[0], //2
			[0], //3
			[0], //4
			[0], //5
			[0], //6
			[0], //7
			[0], //8
			[0], //9
			[0], //10
			[0], //11
			[0], //12
			[0], //13
			[0], //14
			[0], //15
			[0], //16
			[0], //17
			[0], //18
			[0], //19
			[0], //20
			[0], //21
			[0], //22
			[0], //23
			[0], //24
			[0], //25
			[0], //26
			[0], //27
			[0], //28
			[0], //29
		];

		for ($i = 0; $i < $numberall; $i++) {
			foreach ($fake_data[$i] as $value) {
				$sorted = [];
				$json_decode[] = (object) [
					'BuildingNo' => '',
					'GT_Code' => '',
					'Shift' => '',
					'Description' => '',
					'Q1' => '',
					'Q2' => '',
					'Q3' => '',
					'Q4' => '',
					'Q5' => '',
					'Q6' => '',
				];
				$sorted = $json_decode;
			}
		}

		$datashift = $json_decode[0]->Shift;
		if (isset($json_decode[0]->Description)) {
			$datagroup = $json_decode[0]->Description;
		} else {
			$datagroup = '';
		}
		// $datagroup = $json_decode[0]->Description;
		renderView("pagemaster/pdf_building", [
			"datajson" => $json_decode,
			"date_building" => $date_building,
			"shift" => $shift,
			"group" => $datagroup
		]);
	}

	public function geninternalPDF()
	{
		$date_internal = filter_input(INPUT_POST, "date_internal");
		$dateinter = date('Y-m-d', strtotime($date_internal));

		$arr = $this->report->InternalServiceallpdf($dateinter);
		//$arr = (new InternalService)->allpdf($dateinter);
		$json_decode  = json_decode($arr);

		$number = count(array_filter($json_decode));
		$numberall = (13 - $number);

		$fake_data = [
			[0], //1
			[0], //2
			[0], //3
			[0], //4
			[0], //5
			[0], //6
			[0], //7
			[0], //8
			[0], //9
			[0], //10
			[0], //11
			[0], //12
			[0], //13
		];
		for ($i = 0; $i < $numberall; $i++) {
			foreach ($fake_data[$i] as $value) {
				//$sorted = [];
				$json_decode[] = (object) [
					'Row' => '',
					'ItemID' => '',
					'time_create' => '',
					'TemplateSerialNo' => '',
					'NameTH' => '',
					'Note' => '',
					'Batch' => '',
					'qty' => '',
					'FirstName' => '',
					'Department' => '',
					'Name' => '',
				];
				$sorted = $json_decode;
			}
		}
		// echo "<pre>".print_r($json_decode,true)."</pre>";
		// exit();
		renderView("pagemaster/pdf_internal", [
			"dateinter" => $date_internal,
			"datajson" => $json_decode
		]);
	}

	public function gencuringPDF()
	{
		// exit('This section in maintenance for a moment, sorry for your inconvenience.');
		$date_curing = filter_input(INPUT_POST, "date_curing");
		$press 		 = filter_input(INPUT_POST, "selectMenu");
		$shift 		 = filter_input(INPUT_POST, "shift");
		$datecuring = date('Y-m-d', strtotime($date_curing));

		function convertforin($str)
		{
			$strploblem = "";
			$a = explode(',', $str);
			foreach ($a as $value) {
				if ($strploblem === "") {
					$strploblem .= $value;
				} else {
					$strploblem .= "," . $value;
				}
			}
			return $strploblem;
		}
		$pressno  = convertforin(implode(',', $_POST["selectMenu"]));
		if ($pressno == 'B' || $pressno == 'D' || $pressno == 'F' || $pressno == 'B,D' || $pressno == 'D,F'  || $pressno == 'B,F' || $pressno == 'B,D,F') {

			$pagecuring = "pdf_curing";
			if ($pressno == 'B') {
				$press1 = 'B';
			} else if ($pressno == 'D') {
				$press1 = 'D';
			} else if ($pressno == 'F') {
				$press1 = 'F';
			} else if ($pressno == 'H') {
				$press1 = 'H';
			} else if ($pressno == 'J') {
				$press1 = 'J';
			} else if ($pressno == 'L') {
				$press1 = 'L';
			} else if ($pressno == 'B,D') {
				$press1 = 'B';
				$press2 = 'D';
			} else if ($pressno == 'D,F') {
				$press1 = 'D';
				$press2 = 'F';
			} else if ($pressno == 'B,F') {
				$press1 = 'B';
				$press2 = 'F';
			} else if ($pressno == 'B,D,F') {
				$press1 = 'B';
				$press2 = 'D';
				$press3 = 'F';
			}
		} else {
			$pagecuring = "pdf_curing_a";
			if ($pressno == 'A') {
				$press1 = 'A';
			} else if ($pressno == 'C') {
				$press1 = 'C';
			} else if ($pressno == 'E') {
				$press1 = 'E';
			} else if ($pressno == 'G') {
				$press1 = 'G';
			} else if ($pressno == 'I') {
				$press1 = 'I';
			} else if ($pressno == 'K') {
				$press1 = 'K';
			} else if ($pressno == 'A,C') {
				$press1 = 'A';
				$press2 = 'C';
			} else if ($pressno == 'C,E') {
				$press1 = 'C';
				$press2 = 'E';
			} else if ($pressno == 'A,E') {
				$press1 = 'A';
				$press2 = 'E';
			} else if ($pressno == 'A,C,E') {
				$press1 = 'A';
				$press2 = 'C';
				$press3 = 'E';
			}
		}


		function quick_sort($array)
		{
			$length = count($array);

			if ($length <= 1) {
				return $array;
			} else {

				$pivot = $array[0];

				$left = $right = array();

				for ($i = 1; $i < count($array); $i++) {
					if ($array[$i] < $pivot) {
						$left[] = $array[$i];
					} else {
						$right[] = $array[$i];
					}
				}

				return array_merge(quick_sort($left), array($pivot), quick_sort($right));
			}
		}

		if (isset($press1) && isset($press2) && isset($press3)) {
			$arr1 = $this->report->CuringServiceallpdf1($datecuring, $shift, $press1);
			$arr2 = $this->report->CuringServiceallpdf2($datecuring, $shift, $press2);
			$arr3 = $this->report->CuringServiceallpdf3($datecuring, $shift, $press3);
			$getDataQ1 = $this->report->CuringServiceallpdfQ1($datecuring, $shift, $press1);
			$getDataQ2 = $this->report->CuringServiceallpdfQ2($datecuring, $shift, $press2);
			$getDataQ3 = $this->report->CuringServiceallpdfQ1($datecuring, $shift, $press3);
		} else if (isset($press1) && isset($press2)) {
			$arr1 = $this->report->CuringServiceallpdf1($datecuring, $shift, $press1);
			$arr2 = $this->report->CuringServiceallpdf2($datecuring, $shift, $press2);
			$getDataQ1 = $this->report->CuringServiceallpdfQ1($datecuring, $shift, $press1);
			$getDataQ2 = $this->report->CuringServiceallpdfQ2($datecuring, $shift, $press2);
		} else if (isset($press1)) {
			$arr1 = $this->report->CuringServiceallpdf1($datecuring, $shift, $press1);
			$getDataQ1 = $this->report->CuringServiceallpdfQ1($datecuring, $shift, $press1);
			$press01 = $press1 . "01";
			$press04 = $press1 . "04";
			$press05 = $press1 . "05";
			$press08 = $press1 . "08";
			$press09 = $press1 . "09";
			$press12 = $press1 . "12";
		}
		//1
		$fake_data = [
			[0, 0], //1
			[0, 0], //2
			[0, 0], //3
			[0, 0], //4
			[0, 0],	//5
			[0, 0], //6
			[0, 0], //7
			[0, 0], //8
			[0, 0], //9
			[0, 0], //10
			[0, 0], //11
			[0, 0]  //12
		];

		if (isset($press1)) {
			if (!isset($arr1)) {
				for ($i = 0; $i < 12; $i++) {
					foreach ($fake_data[$i] as $value) {
						$sorted = [];
						$json_decode1[] = (object) [
							'PressNo' => $press1 . str_pad($i + 1, 2, "0", STR_PAD_LEFT),
							'PressSide' => '',
							// 'CreateBy' => '',
							// 'Name' => '',
							//       'CuringCode' => '',
							'Q1' => '',
							'Q2' => '',
							'Q3' => '',
							'Q4' => '',
						];
						$sorted[] = $json_decode1;
					}
				}
			} else {
				$json_decode1  = json_decode($arr1);
			}

			//echo "<pre>".print_r($json_decode1,true)."</pre>";		
			$me = [];

			foreach ($json_decode1 as $value) {
				$me[] = [(int) substr($value->PressNo, 1), $value->PressSide];
			}



			foreach ($me as $value) {
				if ($value[1] === 'L') {
					$fake_data[$value[0] - 1][0] = 1;
				} else if ($value[1] === 'R') {
					$fake_data[$value[0] - 1][1] = 1;
				}
			}

			for ($i = 0; $i < 12; $i++) {
				foreach ($fake_data[$i] as $value) {

					if ($value === 0) {
						$json_decode1[] = (object) [
							'PressNo' => $press1 . str_pad($i + 1, 2, "0", STR_PAD_LEFT),
							'PressSide' => 'R',
							// 'CreateBy' => '',
							// 'Name' => '',
							//'CuringCode' => '',
							'Q1' => '',
							'Q2' => '',
							'Q3' => '',
							'Q4' => '',
						];
					}
				}
			}

			$sorted = quick_sort($json_decode1);
			//echo "<pre>".print_r($sorted,true)."</pre>";
			//exit();
		}
		//2		
		if (isset($press2)) {
			if (!isset($arr2)) {
				for ($i = 0; $i < 12; $i++) {
					foreach ($fake_data2[$i] as $value) {
						$sorted2 = [];
						$json_decode2[] = (object) [
							'PressNo' => $press2 . str_pad($i + 1, 2, "0", STR_PAD_LEFT),
							'PressSide' => '',
							'CreateBy' => '',
							'Name' => '',
							//       'CuringCode' => '',
							'Q1' => '',
							'Q2' => '',
							'Q3' => '',
							'Q4' => '',
						];
						$sorted2[] = $json_decode2;
					}
				}
			} else {
				$json_decode2  = json_decode($arr2);
			}

			//echo "<pre>".print_r($json_decode1,true)."</pre>";		
			$me = [];

			foreach ($json_decode2 as $value) {
				$me[] = [(int) substr($value->PressNo, 1), $value->PressSide];
			}

			$fake_data2 = [
				[0, 0], //1
				[0, 0], //2
				[0, 0], //3
				[0, 0], //4
				[0, 0],	//5
				[0, 0], //6
				[0, 0], //7
				[0, 0], //8
				[0, 0], //9
				[0, 0], //10
				[0, 0], //11
				[0, 0]  //12
			];

			foreach ($me as $value) {
				if ($value[1] === 'L') {
					$fake_data2[$value[0] - 1][0] = 1;
				} else if ($value[1] === 'R') {
					$fake_data2[$value[0] - 1][1] = 1;
				}
			}

			for ($i = 0; $i < 12; $i++) {
				foreach ($fake_data2[$i] as $value) {

					if ($value === 0) {
						$json_decode2[] = (object) [
							'PressNo' => $press2 . str_pad($i + 1, 2, "0", STR_PAD_LEFT),
							'PressSide' => 'R',
							'CreateBy' => '',
							'Name' => '',
							//'CuringCode' => '',
							'Q1' => '',
							'Q2' => '',
							'Q3' => '',
							'Q4' => '',
						];
					}
				}
			}

			$sorted2 = quick_sort($json_decode2);
			// echo "<pre>".print_r($sorted2,true)."</pre>";
			// exit();
		}
		//3	
		if (isset($press3)) {
			if (!isset($arr3)) {
				for ($i = 0; $i < 12; $i++) {
					foreach ($fake_data3[$i] as $value) {
						$sorted3 = [];
						$json_decode3[] = (object) [
							'PressNo' => $press3 . str_pad($i + 1, 2, "0", STR_PAD_LEFT),
							'PressSide' => '',
							'CreateBy' => '',
							'Name' => '',
							//       'CuringCode' => '',
							'Q1' => '',
							'Q2' => '',
							'Q3' => '',
							'Q4' => '',
						];
						$sorted3[] = $json_decode3;
					}
				}
			} else {
				$json_decode3  = json_decode($arr3);
			}

			//echo "<pre>".print_r($json_decode1,true)."</pre>";		
			$me = [];

			foreach ($json_decode3 as $value) {
				$me[] = [(int) substr($value->PressNo, 1), $value->PressSide];
			}

			$fake_data3 = [
				[0, 0], //1
				[0, 0], //2
				[0, 0], //3
				[0, 0], //4
				[0, 0],	//5
				[0, 0], //6
				[0, 0], //7
				[0, 0], //8
				[0, 0], //9
				[0, 0], //10
				[0, 0], //11
				[0, 0]  //12
			];

			foreach ($me as $value) {
				if ($value[1] === 'L') {
					$fake_data3[$value[0] - 1][0] = 1;
				} else if ($value[1] === 'R') {
					$fake_data3[$value[0] - 1][1] = 1;
				}
			}

			for ($i = 0; $i < 12; $i++) {
				foreach ($fake_data3[$i] as $value) {

					if ($value === 0) {
						$json_decode3[] = (object) [
							'PressNo' => $press3 . str_pad($i + 1, 2, "0", STR_PAD_LEFT),
							'PressSide' => 'R',
							'CreateBy' => '',
							'Name' => '',
							//'CuringCode' => '',
							'Q1' => '',
							'Q2' => '',
							'Q3' => '',
							'Q4' => '',
						];
					}
				}
			}

			$sorted3 = quick_sort($json_decode3);
			// echo "<pre>".print_r($sorted3,true)."</pre>";
			// exit();
		}
		//echo "<pre>".print_r($sorted,true)."</pre>";
		//echo "<pre>".print_r($sorted2,true)."</pre>";
		//exit();
		//name1		

		$dataname1 = $this->report->Curingname1_4($datecuring, $shift, $press01, $press04);
		$dataname2 = $this->report->Curingname5_8($datecuring, $shift, $press05, $press08);
		$dataname3 = $this->report->Curingname9_12($datecuring, $shift, $press09, $press12);

		error_reporting(E_ALL ^ E_NOTICE);
		$group = $this->report->CuringServiceallgrouppdf($datecuring, $shift);
		$group_decode  = json_decode($group);
		renderView("pagemaster/" . $pagecuring, [
			"datajsonQ" => $getDataQ1,
			"group_decode" => $group_decode,
			"pressNo" => $pressno,
			"press1" => $press1,
			"press2" => $press2,
			"press3" => $press3,
			"datecuring" => $date_curing,
			"shift" => $shift,
			"dataname1" => $dataname1,
			"dataname2" => $dataname2,
			"dataname3" => $dataname3,
			"datajson" => $sorted,
			"b01" => $sorted[0]->PressNo,
			"b02" => $sorted[2]->PressNo,
			"b03" => $sorted[4]->PressNo,
			"b04" => $sorted[6]->PressNo,
			"b05" => $sorted[8]->PressNo,
			"b06" => $sorted[10]->PressNo,
			"b07" => $sorted[12]->PressNo,
			"b08" => $sorted[14]->PressNo,
			"b09" => $sorted[16]->PressNo,
			"b10" => $sorted[18]->PressNo,
			"b11" => $sorted[20]->PressNo,
			"b12" => $sorted[22]->PressNo,
			"cur1" => $sorted[0]->CuringCode,
			"cur2" => $sorted[1]->CuringCode,
			"cur3" => $sorted[2]->CuringCode,
			"cur4" => $sorted[3]->CuringCode,
			"cur5" => $sorted[4]->CuringCode,
			"cur6" => $sorted[5]->CuringCode,
			"cur7" => $sorted[6]->CuringCode,
			"cur8" => $sorted[7]->CuringCode,
			"cur9" => $sorted[8]->CuringCode,
			"cur10" => $sorted[9]->CuringCode,
			"cur11" => $sorted[10]->CuringCode,
			"cur12" => $sorted[11]->CuringCode,
			"cur13" => $sorted[12]->CuringCode,
			"cur14" => $sorted[13]->CuringCode,
			"cur15" => $sorted[14]->CuringCode,
			"cur16" => $sorted[15]->CuringCode,
			"cur17" => $sorted[16]->CuringCode,
			"cur18" => $sorted[17]->CuringCode,
			"cur19" => $sorted[18]->CuringCode,
			"cur20" => $sorted[19]->CuringCode,
			"cur21" => $sorted[20]->CuringCode,
			"cur22" => $sorted[21]->CuringCode,
			"cur23" => $sorted[22]->CuringCode,
			"cur24" => $sorted[23]->CuringCode,
			"qty11" => $sorted[0]->Q1,
			"qty21" => $sorted[0]->Q2,
			"qty31" => $sorted[0]->Q3,
			"qty41" => $sorted[0]->Q4,
			"qty12" => $sorted[1]->Q1,
			"qty22" => $sorted[1]->Q2,
			"qty32" => $sorted[1]->Q3,
			"qty42" => $sorted[1]->Q4,
			"qty13" => $sorted[2]->Q1,
			"qty23" => $sorted[2]->Q2,
			"qty33" => $sorted[2]->Q3,
			"qty43" => $sorted[2]->Q4,
			"qty14" => $sorted[3]->Q1,
			"qty24" => $sorted[3]->Q2,
			"qty34" => $sorted[3]->Q3,
			"qty44" => $sorted[3]->Q4,
			"qty15" => $sorted[4]->Q1,
			"qty25" => $sorted[4]->Q2,
			"qty35" => $sorted[4]->Q3,
			"qty45" => $sorted[4]->Q4,
			"qty16" => $sorted[5]->Q1,
			"qty26" => $sorted[5]->Q2,
			"qty36" => $sorted[5]->Q3,
			"qty46" => $sorted[5]->Q4,
			"qty17" => $sorted[6]->Q1,
			"qty27" => $sorted[6]->Q2,
			"qty37" => $sorted[6]->Q3,
			"qty47" => $sorted[6]->Q4,
			"qty18" => $sorted[7]->Q1,
			"qty28" => $sorted[7]->Q2,
			"qty38" => $sorted[7]->Q3,
			"qty48" => $sorted[7]->Q4,
			"qty19" => $sorted[8]->Q1,
			"qty29" => $sorted[8]->Q2,
			"qty39" => $sorted[8]->Q3,
			"qty49" => $sorted[8]->Q4,
			"qty110" => $sorted[9]->Q1,
			"qty210" => $sorted[9]->Q2,
			"qty310" => $sorted[9]->Q3,
			"qty410" => $sorted[9]->Q4,
			"qty111" => $sorted[10]->Q1,
			"qty211" => $sorted[10]->Q2,
			"qty311" => $sorted[10]->Q3,
			"qty411" => $sorted[10]->Q4,
			"qty112" => $sorted[11]->Q1,
			"qty212" => $sorted[11]->Q2,
			"qty312" => $sorted[11]->Q3,
			"qty412" => $sorted[11]->Q4,
			"qty113" => $sorted[12]->Q1,
			"qty213" => $sorted[12]->Q2,
			"qty313" => $sorted[12]->Q3,
			"qty413" => $sorted[12]->Q4,
			"qty114" => $sorted[13]->Q1,
			"qty214" => $sorted[13]->Q2,
			"qty314" => $sorted[13]->Q3,
			"qty414" => $sorted[13]->Q4,
			"qty115" => $sorted[14]->Q1,
			"qty215" => $sorted[14]->Q2,
			"qty315" => $sorted[14]->Q3,
			"qty415" => $sorted[14]->Q4,
			"qty116" => $sorted[15]->Q1,
			"qty216" => $sorted[15]->Q2,
			"qty316" => $sorted[15]->Q3,
			"qty416" => $sorted[15]->Q4,
			"qty117" => $sorted[16]->Q1,
			"qty217" => $sorted[16]->Q2,
			"qty317" => $sorted[16]->Q3,
			"qty417" => $sorted[16]->Q4,
			"qty118" => $sorted[17]->Q1,
			"qty218" => $sorted[17]->Q2,
			"qty318" => $sorted[17]->Q3,
			"qty418" => $sorted[17]->Q4,
			"qty119" => $sorted[18]->Q1,
			"qty219" => $sorted[18]->Q2,
			"qty319" => $sorted[18]->Q3,
			"qty419" => $sorted[18]->Q4,
			"qty1110" => $sorted[19]->Q1,
			"qty2110" => $sorted[19]->Q2,
			"qty3110" => $sorted[19]->Q3,
			"qty4110" => $sorted[19]->Q4,
			"qty1111" => $sorted[20]->Q1,
			"qty2111" => $sorted[20]->Q2,
			"qty3111" => $sorted[20]->Q3,
			"qty4111" => $sorted[20]->Q4,
			"qty1112" => $sorted[21]->Q1,
			"qty2112" => $sorted[21]->Q2,
			"qty3112" => $sorted[21]->Q3,
			"qty4112" => $sorted[21]->Q4,
			"qty1113" => $sorted[22]->Q1,
			"qty2113" => $sorted[22]->Q2,
			"qty3113" => $sorted[22]->Q3,
			"qty4113" => $sorted[22]->Q4,
			"qty1114" => $sorted[23]->Q1,
			"qty2114" => $sorted[23]->Q2,
			"qty3114" => $sorted[23]->Q3,
			"qty4114" => $sorted[23]->Q4,
			// "dataname_sec1" => $dataname_sec1,
			// "dataname_sec2" => $dataname_sec2,
			// "dataname_sec3" => $dataname_sec3,
			"datajsonQ2" => $getDataQ2,
			"datajson2" => $sorted2,
			"b01_sec" => $sorted2[0]->PressNo,
			"b02_sec" => $sorted2[2]->PressNo,
			"b03_sec" => $sorted2[4]->PressNo,
			"b04_sec" => $sorted2[6]->PressNo,
			"b05_sec" => $sorted2[8]->PressNo,
			"b06_sec" => $sorted2[10]->PressNo,
			"b07_sec" => $sorted2[12]->PressNo,
			"b08_sec" => $sorted2[14]->PressNo,
			"b09_sec" => $sorted2[16]->PressNo,
			"b10_sec" => $sorted2[18]->PressNo,
			"b11_sec" => $sorted2[20]->PressNo,
			"b12_sec" => $sorted2[22]->PressNo,
			"cur1_sec" => $sorted2[0]->CuringCode,
			"cur2_sec" => $sorted2[1]->CuringCode,
			"cur3_sec" => $sorted2[2]->CuringCode,
			"cur4_sec" => $sorted2[3]->CuringCode,
			"cur5_sec" => $sorted2[4]->CuringCode,
			"cur6_sec" => $sorted2[5]->CuringCode,
			"cur7_sec" => $sorted2[6]->CuringCode,
			"cur8_sec" => $sorted2[7]->CuringCode,
			"cur9_sec" => $sorted2[8]->CuringCode,
			"cur10_sec" => $sorted2[9]->CuringCode,
			"cur11_sec" => $sorted2[10]->CuringCode,
			"cur12_sec" => $sorted2[11]->CuringCode,
			"cur13_sec" => $sorted2[12]->CuringCode,
			"cur14_sec" => $sorted2[13]->CuringCode,
			"cur15_sec" => $sorted2[14]->CuringCode,
			"cur16_sec" => $sorted2[15]->CuringCode,
			"cur17_sec" => $sorted2[16]->CuringCode,
			"cur18_sec" => $sorted2[17]->CuringCode,
			"cur19_sec" => $sorted2[18]->CuringCode,
			"cur20_sec" => $sorted2[19]->CuringCode,
			"cur21_sec" => $sorted2[20]->CuringCode,
			"cur22_sec" => $sorted2[21]->CuringCode,
			"cur23_sec" => $sorted2[22]->CuringCode,
			"cur24_sec" => $sorted2[23]->CuringCode,
			"qty11_sec" => $sorted2[0]->Q1,
			"qty21_sec" => $sorted2[0]->Q2,
			"qty31_sec" => $sorted2[0]->Q3,
			"qty41_sec" => $sorted2[0]->Q4,
			"qty12_sec" => $sorted2[1]->Q1,
			"qty22_sec" => $sorted2[1]->Q2,
			"qty32_sec" => $sorted2[1]->Q3,
			"qty42_sec" => $sorted2[1]->Q4,
			"qty13_sec" => $sorted2[2]->Q1,
			"qty23_sec" => $sorted2[2]->Q2,
			"qty33_sec" => $sorted2[2]->Q3,
			"qty43_sec" => $sorted2[2]->Q4,
			"qty14_sec" => $sorted2[3]->Q1,
			"qty24_sec" => $sorted2[3]->Q2,
			"qty34_sec" => $sorted2[3]->Q3,
			"qty44_sec" => $sorted2[3]->Q4,
			"qty15_sec" => $sorted2[4]->Q1,
			"qty25_sec" => $sorted2[4]->Q2,
			"qty35_sec" => $sorted2[4]->Q3,
			"qty45_sec" => $sorted2[4]->Q4,
			"qty16_sec" => $sorted2[5]->Q1,
			"qty26_sec" => $sorted2[5]->Q2,
			"qty36_sec" => $sorted2[5]->Q3,
			"qty46_sec" => $sorted2[5]->Q4,
			"qty17_sec" => $sorted2[6]->Q1,
			"qty27_sec" => $sorted2[6]->Q2,
			"qty37_sec" => $sorted2[6]->Q3,
			"qty47_sec" => $sorted2[6]->Q4,
			"qty18_sec" => $sorted2[7]->Q1,
			"qty28_sec" => $sorted2[7]->Q2,
			"qty38_sec" => $sorted2[7]->Q3,
			"qty48_sec" => $sorted2[7]->Q4,
			"qty19_sec" => $sorted2[8]->Q1,
			"qty29_sec" => $sorted2[8]->Q2,
			"qty39_sec" => $sorted2[8]->Q3,
			"qty49_sec" => $sorted2[8]->Q4,
			"qty110_sec" => $sorted2[9]->Q1,
			"qty210_sec" => $sorted2[9]->Q2,
			"qty310_sec" => $sorted2[9]->Q3,
			"qty410_sec" => $sorted2[9]->Q4,
			"qty111_sec" => $sorted2[10]->Q1,
			"qty211_sec" => $sorted2[10]->Q2,
			"qty311_sec" => $sorted2[10]->Q3,
			"qty411_sec" => $sorted2[10]->Q4,
			"qty112_sec" => $sorted2[11]->Q1,
			"qty212_sec" => $sorted2[11]->Q2,
			"qty312_sec" => $sorted2[11]->Q3,
			"qty412_sec" => $sorted2[11]->Q4,
			"qty113_sec" => $sorted2[12]->Q1,
			"qty213_sec" => $sorted2[12]->Q2,
			"qty313_sec" => $sorted2[12]->Q3,
			"qty413_sec" => $sorted2[12]->Q4,
			"qty114_sec" => $sorted2[13]->Q1,
			"qty214_sec" => $sorted2[13]->Q2,
			"qty314_sec" => $sorted2[13]->Q3,
			"qty414_sec" => $sorted2[13]->Q4,
			"qty115_sec" => $sorted2[14]->Q1,
			"qty215_sec" => $sorted2[14]->Q2,
			"qty315_sec" => $sorted2[14]->Q3,
			"qty415_sec" => $sorted2[14]->Q4,
			"qty116_sec" => $sorted2[15]->Q1,
			"qty216_sec" => $sorted2[15]->Q2,
			"qty316_sec" => $sorted2[15]->Q3,
			"qty416_sec" => $sorted2[15]->Q4,
			"qty117_sec" => $sorted2[16]->Q1,
			"qty217_sec" => $sorted2[16]->Q2,
			"qty317_sec" => $sorted2[16]->Q3,
			"qty417_sec" => $sorted2[16]->Q4,
			"qty118_sec" => $sorted2[17]->Q1,
			"qty218_sec" => $sorted2[17]->Q2,
			"qty318_sec" => $sorted2[17]->Q3,
			"qty418_sec" => $sorted2[17]->Q4,
			"qty119_sec" => $sorted2[18]->Q1,
			"qty219_sec" => $sorted2[18]->Q2,
			"qty319_sec" => $sorted2[18]->Q3,
			"qty419_sec" => $sorted2[18]->Q4,
			"qty1110_sec" => $sorted2[19]->Q1,
			"qty2110_sec" => $sorted2[19]->Q2,
			"qty3110_sec" => $sorted2[19]->Q3,
			"qty4110_sec" => $sorted2[19]->Q4,
			"qty1111_sec" => $sorted2[20]->Q1,
			"qty2111_sec" => $sorted2[20]->Q2,
			"qty3111_sec" => $sorted2[20]->Q3,
			"qty4111_sec" => $sorted2[20]->Q4,
			"qty1112_sec" => $sorted2[21]->Q1,
			"qty2112_sec" => $sorted2[21]->Q2,
			"qty3112_sec" => $sorted2[21]->Q3,
			"qty4112_sec" => $sorted2[21]->Q4,
			"qty1113_sec" => $sorted2[22]->Q1,
			"qty2113_sec" => $sorted2[22]->Q2,
			"qty3113_sec" => $sorted2[22]->Q3,
			"qty4113_sec" => $sorted2[22]->Q4,
			"qty1114_sec" => $sorted2[23]->Q1,
			"qty2114_sec" => $sorted2[23]->Q2,
			"qty3114_sec" => $sorted2[23]->Q3,
			"qty4114_sec" => $sorted2[23]->Q4,
			// "dataname_third1" => $dataname_third1,
			// "dataname_third2" => $dataname_third2,
			// "dataname_third3" => $dataname_third3,
			"datajsonQ3" => $getDataQ3,
			"datajson3" => $sorted3,
			"b01_third" => $sorted3[0]->PressNo,
			"b02_third" => $sorted3[2]->PressNo,
			"b03_third" => $sorted3[4]->PressNo,
			"b04_third" => $sorted3[6]->PressNo,
			"b05_third" => $sorted3[8]->PressNo,
			"b06_third" => $sorted3[10]->PressNo,
			"b07_third" => $sorted3[12]->PressNo,
			"b08_third" => $sorted3[14]->PressNo,
			"b09_third" => $sorted3[16]->PressNo,
			"b10_third" => $sorted3[18]->PressNo,
			"b11_third" => $sorted3[20]->PressNo,
			"b12_third" => $sorted3[22]->PressNo,
			"cur1_third" => $sorted3[0]->CuringCode,
			"cur2_third" => $sorted3[1]->CuringCode,
			"cur3_third" => $sorted3[2]->CuringCode,
			"cur4_third" => $sorted3[3]->CuringCode,
			"cur5_third" => $sorted3[4]->CuringCode,
			"cur6_third" => $sorted3[5]->CuringCode,
			"cur7_third" => $sorted3[6]->CuringCode,
			"cur8_third" => $sorted3[7]->CuringCode,
			"cur9_third" => $sorted3[8]->CuringCode,
			"cur10_third" => $sorted3[9]->CuringCode,
			"cur11_third" => $sorted3[10]->CuringCode,
			"cur12_third" => $sorted3[11]->CuringCode,
			"cur13_third" => $sorted3[12]->CuringCode,
			"cur14_third" => $sorted3[13]->CuringCode,
			"cur15_third" => $sorted3[14]->CuringCode,
			"cur16_third" => $sorted3[15]->CuringCode,
			"cur17_third" => $sorted3[16]->CuringCode,
			"cur18_third" => $sorted3[17]->CuringCode,
			"cur19_third" => $sorted3[18]->CuringCode,
			"cur20_third" => $sorted3[19]->CuringCode,
			"cur21_third" => $sorted3[20]->CuringCode,
			"cur22_third" => $sorted3[21]->CuringCode,
			"cur23_third" => $sorted3[22]->CuringCode,
			"cur24_third" => $sorted3[23]->CuringCode,
			"qty11_third" => $sorted3[0]->Q1,
			"qty21_third" => $sorted3[0]->Q2,
			"qty31_third" => $sorted3[0]->Q3,
			"qty41_third" => $sorted3[0]->Q4,
			"qty12_third" => $sorted3[1]->Q1,
			"qty22_third" => $sorted3[1]->Q2,
			"qty32_third" => $sorted3[1]->Q3,
			"qty42_third" => $sorted3[1]->Q4,
			"qty13_third" => $sorted3[2]->Q1,
			"qty23_third" => $sorted3[2]->Q2,
			"qty33_third" => $sorted3[2]->Q3,
			"qty43_third" => $sorted3[2]->Q4,
			"qty14_third" => $sorted3[3]->Q1,
			"qty24_third" => $sorted3[3]->Q2,
			"qty34_third" => $sorted3[3]->Q3,
			"qty44_third" => $sorted3[3]->Q4,
			"qty15_third" => $sorted3[4]->Q1,
			"qty25_third" => $sorted3[4]->Q2,
			"qty35_third" => $sorted3[4]->Q3,
			"qty45_third" => $sorted3[4]->Q4,
			"qty16_third" => $sorted3[5]->Q1,
			"qty26_third" => $sorted3[5]->Q2,
			"qty36_third" => $sorted3[5]->Q3,
			"qty46_third" => $sorted3[5]->Q4,
			"qty17_third" => $sorted3[6]->Q1,
			"qty27_third" => $sorted3[6]->Q2,
			"qty37_third" => $sorted3[6]->Q3,
			"qty47_third" => $sorted3[6]->Q4,
			"qty18_third" => $sorted3[7]->Q1,
			"qty28_third" => $sorted3[7]->Q2,
			"qty38_third" => $sorted3[7]->Q3,
			"qty48_third" => $sorted3[7]->Q4,
			"qty19_third" => $sorted3[8]->Q1,
			"qty29_third" => $sorted3[8]->Q2,
			"qty39_third" => $sorted3[8]->Q3,
			"qty49_third" => $sorted3[8]->Q4,
			"qty110_third" => $sorted3[9]->Q1,
			"qty210_third" => $sorted3[9]->Q2,
			"qty310_third" => $sorted3[9]->Q3,
			"qty410_third" => $sorted3[9]->Q4,
			"qty111_third" => $sorted3[10]->Q1,
			"qty211_third" => $sorted3[10]->Q2,
			"qty311_third" => $sorted3[10]->Q3,
			"qty411_third" => $sorted3[10]->Q4,
			"qty112_third" => $sorted3[11]->Q1,
			"qty212_third" => $sorted3[11]->Q2,
			"qty312_third" => $sorted3[11]->Q3,
			"qty412_third" => $sorted3[11]->Q4,
			"qty113_third" => $sorted3[12]->Q1,
			"qty213_third" => $sorted3[12]->Q2,
			"qty313_third" => $sorted3[12]->Q3,
			"qty413_third" => $sorted3[12]->Q4,
			"qty114_third" => $sorted3[13]->Q1,
			"qty214_third" => $sorted3[13]->Q2,
			"qty314_third" => $sorted3[13]->Q3,
			"qty414_third" => $sorted3[13]->Q4,
			"qty115_third" => $sorted3[14]->Q1,
			"qty215_third" => $sorted3[14]->Q2,
			"qty315_third" => $sorted3[14]->Q3,
			"qty415_third" => $sorted3[14]->Q4,
			"qty116_third" => $sorted3[15]->Q1,
			"qty216_third" => $sorted3[15]->Q2,
			"qty316_third" => $sorted3[15]->Q3,
			"qty416_third" => $sorted3[15]->Q4,
			"qty117_third" => $sorted3[16]->Q1,
			"qty217_third" => $sorted3[16]->Q2,
			"qty317_third" => $sorted3[16]->Q3,
			"qty417_third" => $sorted3[16]->Q4,
			"qty118_third" => $sorted3[17]->Q1,
			"qty218_third" => $sorted3[17]->Q2,
			"qty318_third" => $sorted3[17]->Q3,
			"qty418_third" => $sorted3[17]->Q4,
			"qty119_third" => $sorted3[18]->Q1,
			"qty219_third" => $sorted3[18]->Q2,
			"qty319_third" => $sorted3[18]->Q3,
			"qty419_third" => $sorted3[18]->Q4,
			"qty1110_third" => $sorted3[19]->Q1,
			"qty2110_third" => $sorted3[19]->Q2,
			"qty3110_third" => $sorted3[19]->Q3,
			"qty4110_third" => $sorted3[19]->Q4,
			"qty1111_third" => $sorted3[20]->Q1,
			"qty2111_third" => $sorted3[20]->Q2,
			"qty3111_third" => $sorted3[20]->Q3,
			"qty4111_third" => $sorted3[20]->Q4,
			"qty1112_third" => $sorted3[21]->Q1,
			"qty2112_third" => $sorted3[21]->Q2,
			"qty3112_third" => $sorted3[21]->Q3,
			"qty4112_third" => $sorted3[21]->Q4,
			"qty1113_third" => $sorted3[22]->Q1,
			"qty2113_third" => $sorted3[22]->Q2,
			"qty3113_third" => $sorted3[22]->Q3,
			"qty4113_third" => $sorted3[22]->Q4,
			"qty1114_third" => $sorted3[23]->Q1,
			"qty2114_third" => $sorted3[23]->Q2,
			"qty3114_third" => $sorted3[23]->Q3,
			"qty4114_third" => $sorted3[23]->Q4

		]);
	}

	public function geninventoryPDF()
	{
		if (!isset($_POST['item_group'])) {
			header('Content-Type: text/html; charset=utf-8');
			echo "กรุณาเลือกชนิดของ Product Group<br/>";
			echo "<a href='/report/greentire/inventory'>กลับไป</a>";
			exit;
		}

		$item_group = $_POST['item_group'];

		$product_group = 'TBR';
		if ($item_group === 'tbr') {
			$product_group = 'TBR';
		} else {
			$product_group = 'RDT';
		}

		// $arr = $this->report->GreentireInventoryServiceallpdf();
		$arr = $this->report->greentireInventoryV2($product_group);
		$json_decode  = json_decode($arr);

		date_default_timezone_set("Asia/Bangkok");
		$date = date("d-m-Y");
		$time = date("H:i:s");

		renderView("pagemaster/pdf_inventory", [
			"datajson" => $json_decode,
			"date" => $date,
			"time" => $time
		]);
	}

	public function genwarehousePDF() // update 24/02/2017
	{
		$shift = filter_input(INPUT_POST, "shift");
		$date = filter_input(INPUT_POST, "datewarehouse");
		$datewarehouse = date('Y-m-d', strtotime($date));
		$datewarehouse_nextday = date('Y-m-d', strtotime($date . ' +1 days'));
		$time_selected = $_POST["selecttime"];
		$timeset = [];

		foreach ($time_selected as $k => $time_id) {
			if ($time_id === '1') {
				array_push($timeset, '\'' . $datewarehouse . ' 08:00:00\' AND ' . '\'' . $datewarehouse . ' 10:00:00\'');
			} else if ($time_id === '2') {
				array_push($timeset, '\'' . $datewarehouse . ' 10:00:00\' AND ' . '\'' . $datewarehouse . ' 12:00:00\'');
			} else if ($time_id === '3') {
				array_push($timeset, '\'' . $datewarehouse . ' 12:00:00\' AND ' . '\'' . $datewarehouse . ' 14:00:00\'');
			} else if ($time_id === '4') {
				array_push($timeset, '\'' . $datewarehouse . ' 14:00:00\' AND ' . '\'' . $datewarehouse . ' 16:00:00\'');
			} else if ($time_id === '5') {
				array_push($timeset, '\'' . $datewarehouse . ' 16:00:00\' AND ' . '\'' . $datewarehouse . ' 18:00:00\'');
			} else if ($time_id === '6') {
				array_push($timeset, '\'' . $datewarehouse . ' 18:00:00\' AND ' . '\'' . $datewarehouse . ' 20:00:00\'');
			} else if ($time_id === '7') {
				array_push($timeset, '\'' . $datewarehouse . ' 20:00:00\' AND ' . '\'' . $datewarehouse . ' 22:00:00\'');
			} else if ($time_id === '8') {
				array_push($timeset, '\'' . $datewarehouse . ' 22:00:00\' AND ' . '\'' . $datewarehouse_nextday . ' 00:00:00\'');
			} else if ($time_id === '9') {
				array_push($timeset, '\'' . $datewarehouse_nextday . ' 00:00:00\' AND ' . '\'' . $datewarehouse_nextday . ' 02:00:00\'');
			} else if ($time_id === '10') {
				array_push($timeset, '\'' . $datewarehouse_nextday . ' 02:00:00\' AND ' . '\'' . $datewarehouse_nextday . ' 04:00:00\'');
			} else if ($time_id === '11') {
				array_push($timeset, '\'' . $datewarehouse_nextday . ' 04:00:00\' AND ' . '\'' . $datewarehouse_nextday . ' 06:00:00\'');
			} else if ($time_id === '12') {
				array_push($timeset, '\'' . $datewarehouse_nextday . ' 06:00:00\' AND ' . '\'' . $datewarehouse_nextday . ' 08:00:00\'');
			}
		}

		$warehouse = filter_input(INPUT_POST, "warehouse");
		if ($warehouse == "sent") {

			$rows = (new WarehouseService)->getReportSentToWarehouse($timeset);

			renderView('pagemaster/pdf_warehousesent', [
				'rows' => $rows,
				'timeset' => $timeset,
				'shift' => $shift,
				'date' => $datewarehouse
			]);
		} else if ($warehouse == "recive") {
			// $time  = convertforin(implode(',',$_POST["selecttime"]));
			// $counttime = count($_POST["selecttime"]);
			if (isset($_POST['selectbrand'])) {
				$brand_select = $_POST['selectbrand'];
				$brand  = '';
				foreach ($brand_select as $v) {
					$brand .= $v . ', ';
				}
				$brand = trim($brand, ', ');
				//convertforin(implode(',',$_POST["selectbrand"]));
			}

			$rows = (new WarehouseService)->getReportReceiveToWarehouse($shift, $timeset, $datewarehouse, $brand);
			// echo "<pre>" . print_r($rows, true) . '</pre>'; exit;
			// $arr = $this->report->GreentireInventoryServiceallpdfwarehouserecive($shift,$timeset,$datewarehouse,$brand);
			// var_dump($shift,$timeset,$datewarehouse,$brand);
			renderView('pagemaster/pdf_warehouserecive', [
				'rows' => $rows,
				'timeset' => $timeset,
				'shift' => $shift,
				'date' => $datewarehouse
			]);
		}

		return;
		function convertforin($str)
		{
			$strploblem = "";
			$a = explode(',', $str);
			foreach ($a as $value) {
				if ($strploblem === "") {
					$strploblem .= $value;
				} else {
					$strploblem .= "," . $value;
				}
			}
			return $strploblem;
		}


		if ($shift == 'day') {
			if ($counttime == 1) {
				if ($time == 1) {
					$timeto = "08:00";
					$timefrom = "11:00";
				} elseif ($time == 2) {
					$timeto = "11:00";
					$timefrom = "14:00";
				} elseif ($time == 3) {
					$timeto = "14:00";
					$timefrom = "17:00";
				} elseif ($time == 4) {
					$timeto = "17:00";
					$timefrom = "20:00";
				}
				$timeshow = $timeto . "-" . $timefrom;
			} elseif ($counttime == 2) {
				if ($time == '1,2') {
					$timeto1 = "08:00";
					$timefrom1 = "11:00";
					$timeto2 = "11:00";
					$timefrom2 = "14:00";
				} elseif ($time == '1,3') {
					$timeto1 = "08:00";
					$timefrom1 = "11:00";
					$timeto2 = "14:00";
					$timefrom2 = "17:00";
				} elseif ($time == '1,4') {
					$timeto1 = "08:00";
					$timefrom1 = "11:00";
					$timeto2 = "17:00";
					$timefrom2 = "20:00";
				} elseif ($time == '2,3') {
					$timeto1 = "11:00";
					$timefrom1 = "14:00";
					$timeto2 = "14:00";
					$timefrom2 = "17:00";
				} elseif ($time == '2,4') {
					$timeto1 = "11:00";
					$timefrom1 = "14:00";
					$timeto2 = "17:00";
					$timefrom2 = "20:00";
				} elseif ($time == '3,4') {
					$timeto1 = "14:00";
					$timefrom1 = "17:00";
					$timeto2 = "17:00";
					$timefrom2 = "20:00";
				}
				$timeshow = $timeto1 . "-" . $timefrom1 . "," . $timeto2 . "-" . $timefrom2;
			} elseif ($counttime == 3) {
				if ($time == '1,2,3') {
					$timeto1 = "08:00";
					$timefrom1 = "11:00";
					$timeto2 = "11:00";
					$timefrom2 = "14:00";
					$timeto3 = "14:00";
					$timefrom3 = "17:00";
				} elseif ($time == '1,2,4') {
					$timeto1 = "08:00";
					$timefrom1 = "11:00";
					$timeto2 = "11:00";
					$timefrom2 = "14:00";
					$timeto3 = "17:00";
					$timefrom3 = "20:00";
				} elseif ($time == '1,3,4') {
					$timeto1 = "08:00";
					$timefrom1 = "11:00";
					$timeto2 = "14:00";
					$timefrom2 = "17:00";
					$timeto3 = "17:00";
					$timefrom3 = "20:00";
				} elseif ($time == '2,3,4') {
					$timeto1 = "11:00";
					$timefrom1 = "14:00";
					$timeto2 = "14:00";
					$timefrom2 = "17:00";
					$timeto3 = "17:00";
					$timefrom3 = "20:00";
				}
				$timeshow = $timeto1 . "-" . $timefrom1 . "," . $timeto2 . "-" . $timefrom2 . "," . $timeto3 . "-" . $timefrom3;
			} elseif ($counttime == 4) {
				if ($time == '1,2,3,4') {
					$timeto1 = "08:00";
					$timefrom1 = "11:00";
					$timeto2 = "11:00";
					$timefrom2 = "14:00";
					$timeto3 = "14:00";
					$timefrom3 = "17:00";
					$timeto4 = "17:00";
					$timefrom4 = "20:00";
				}
				$timeshow = $timeto1 . "-" . $timefrom1 . "," . $timeto2 . "-" . $timefrom2 . "," . $timeto3 . "-" . $timefrom3 . "," . $timeto4 . "-" . $timefrom4;
			}
		} elseif ($shift == 'night') {
			$timeshow = "20:00-08:00";
		}

		$warehouse = filter_input(INPUT_POST, "warehouse");
		if ($warehouse == "sent") {
			$pagewarehouse = "pdf_warehousesent";
			$arr = $this->report->GreentireInventoryServiceallpdfwarehousesent($shift, $time, $counttime, $datewarehouse);
			$json_decode  = json_decode($arr);
		} elseif ($warehouse == "recive") {
			$pagewarehouse = "pdf_warehouserecive";
			$brand  = convertforin(implode(',', $_POST["selectbrand"]));
			$arr = $this->report->GreentireInventoryServiceallpdfwarehouserecive($shift, $time, $counttime, $datewarehouse, $brand);
			$json_decode  = json_decode($arr);

			$number = count(array_filter($json_decode));
			$numberall = (13 - $number);
			$fake_data = [
				[0], //1
				[0], //2
				[0], //3
				[0], //4
				[0], //5
				[0], //6
				[0], //7
				[0], //8
				[0], //9
				[0], //10
				[0], //11
				[0], //12
				[0], //13
			];
			for ($i = 0; $i < $numberall; $i++) {
				foreach ($fake_data[$i] as $value) {
					//$sorted = [];
					$json_decode[] = (object) [
						'Pages' => 1,
						'ItemID' => '',
						'NameTH' => '',
						'QTY' => '',
						'Batch' => '',
					];
					$sorted = $json_decode;
				}
			}
		}
		if ($shift == 'day') {
			$shift = "กลางวัน";
		} else {
			$shift = "กลางคืน";
		}
		// echo "<pre>".print_r($json_decode,true)."</pre>";
		// exit();
		renderView("pagemaster/" . $pagewarehouse, [
			"datajson" => $json_decode,
			"date" => $date,
			"shift" => $shift,
			"timeshow" => $timeshow,
			"number" => ($number / 14)
		]);
	}

	public function curingPress()
	{
		$date_curing = filter_input(INPUT_POST, "date_curing");
		$press_no		 = filter_input(INPUT_POST, "press_no");
		$shift 		 = filter_input(INPUT_POST, "shift");
		$date_curing = date('Y-m-d', strtotime($date_curing));

		$result = (new ReportService)->curingPress($date_curing, $press_no, $shift);
		// echo "<pre>" . print_r($result, true) . '</pre>';
		renderView('report/curing_press', [
			'L' => json_decode($result['L']),
			'R' => json_decode($result['R']),
			'shift' => $result['shift'],
			'date_curing' => $result['date_curing'],
			'weekly' => $result['weekly'],
			'press_no' => $press_no
		]);
	}

	public function gencuringpressPDF()
	{
		$date_curing = filter_input(INPUT_POST, "date_curing");
		$press 		 = filter_input(INPUT_POST, "press_no");
		$shift 		 = filter_input(INPUT_POST, "shift");
		$datecuring = date('Y-m-d', strtotime($date_curing));
		// echo $shift; exit();
		$arr = $this->report->CuringServiceallpresspdf($datecuring, $press, $shift);
		$json_decode  = json_decode($arr);
		// echo '<pre>' . print_r($json_decode) . '</pre>';  exit;
		$arrGTL = $this->report->CuringServiceallpresspdfGTL($datecuring, $press, $shift);
		$json_decodeGTL  = json_decode($arrGTL);
		$arrGTR = $this->report->CuringServiceallpresspdfGTR($datecuring, $press, $shift);
		$json_decodeGTR  = json_decode($arrGTR);
		$arrW = $this->report->CuringServiceallpresspdfweekly($datecuring, $press, $shift);
		$json_decodeW  = json_decode($arrW);
		$arrCL = $this->report->CuringServiceallpresspdfCurcodeL($datecuring, $press, $shift);
		$json_decodeCL  = json_decode($arrCL);
		$arrCR = $this->report->CuringServiceallpresspdfCurcodeR($datecuring, $press, $shift);
		$json_decodeCR  = json_decode($arrCR);
		//echo "<pre>".print_r($json_decodeGTL,true)."</pre>";
		//echo $press;
		//exit();
		// $chk = $json_decode[0]->
		renderView("pagemaster/pdf_curingpress", [
			"datecuring" => $date_curing,
			"shift" => $shift,
			"datajson" => $json_decode,
			"datajsonGTL" => $json_decodeGTL,
			"datajsonGTR" => $json_decodeGTR,
			"datajsonW" => $json_decodeW,
			"datajsonCL" => $json_decodeCL,
			"datajsonCR" => $json_decodeCR,
			"press" => $press,
			"chk" => $chk
		]);
	}

	public function buildingAx()
	{
		renderView("report/building_ax");
	}

	public function buildingAxPdf()
	{
		if (!isset($_POST['item_group'])) {
			header('Content-Type: text/html; charset=utf-8');
			echo "กรุณาเลือกชนิดของ Product Group<br/>";
			echo "<a href='/report/building_ax'>กลับไป</a>";
			exit;
		}

		$date_building = filter_input(INPUT_POST, "date_building");
		$shift = filter_input(INPUT_POST, "shift");
		$group = filter_input(INPUT_POST, "group");

		$item_group = $_POST['item_group'];

		$product_group = 'TBR';
		if ($item_group === 'tbr') {
			$product_group = 'TBR';
		} else {
			$product_group = 'RDT';
		}

		$datebuilding = date('Y-m-d', strtotime($date_building));

		$arr = $this->report->BuildingServiceallpdf($datebuilding, $shift, $group, $product_group);
		//$arr = BuildingService::allpdf($datebuilding,$shift,$group);
		$json_decode  = json_decode($arr);

		$number = count(array_filter($json_decode));
		$numberall = (29 - $number);

		$fake_data = [
			[0], //1
			[0], //2
			[0], //3
			[0], //4
			[0], //5
			[0], //6
			[0], //7
			[0], //8
			[0], //9
			[0], //10
			[0], //11
			[0], //12
			[0], //13
			[0], //14
			[0], //15
			[0], //16
			[0], //17
			[0], //18
			[0], //19
			[0], //20
			[0], //21
			[0], //22
			[0], //23
			[0], //24
			[0], //25
			[0], //26
			[0], //27
			[0], //28
			[0], //29
		];

		for ($i = 0; $i < $numberall; $i++) {
			foreach ($fake_data[$i] as $value) {
				$sorted = [];
				$json_decode[] = (object) [
					'BuildingNo' => '',
					'GT_Code' => '',
					'Shift' => '',
					'Description' => '',
					'Q1' => '',
					'Q2' => '',
					'Q3' => '',
					'Q4' => '',
					'Q5' => '',
					'Q6' => '',
				];
				$sorted = $json_decode;
			}
		}

		$datashift = $json_decode[0]->Shift;
		$datagroup = $json_decode[0]->Description;
		renderView("report/pdf_building_ax", [
			"datajson" => $json_decode,
			"date_building" => $date_building,
			"shift" => $shift,
			"group" => $datagroup
		]);
	}

	public function curingAx()
	{
		renderView("report/curing_ax");
	}

	public function curingAxPdf()
	{

		if (!isset($_POST['item_group'])) {
			header('Content-Type: text/html; charset=utf-8');
			echo "กรุณาเลือกชนิดของ Product Group<br/>";
			echo "<a href='/report/curing_ax'>กลับไป</a>";
			exit;
		}

		$dateCuring = filter_input(INPUT_POST, "date_curing");
		$date_curing = Date('Y-m-d', strtotime($dateCuring));
		$shift = filter_input(INPUT_POST, "shift");

		$item_group = $_POST['item_group'];

		$product_group = 'TBR';
		if ($item_group === 'tbr') {
			$product_group = 'TBR';
		} else {
			$product_group = 'RDT';
		}


		$data = (new ReportService)->curingAx($date_curing, $shift, $product_group);
		// echo var_dump($data); exit;
		renderView("report/pdf_curing_ax", [
			"date_curing" => $date_curing,
			"shift" => $shift,
			"data" => json_decode($data)
		]);
	}

	public function gencureinventoryPDF()
	{

		if (!isset($_POST['item_group'])) {
			header('Content-Type: text/html; charset=utf-8');
			echo "กรุณาเลือกชนิดของ Product Group<br/>";
			echo "<a href='/report/cure/inventory'>กลับไป</a>";
			exit;
		}

		date_default_timezone_set("Asia/Bangkok");
		$date = date("d-m-Y");
		$time = date("H:i:s");

		$item_group = $_POST['item_group'];

		$product_group = 'TBR';
		if ($item_group === 'tbr') {
			$product_group = 'TBR';
		} else {
			$product_group = 'RDT';
		}

		$arr = $this->report->CureInventoryServiceallpdf($product_group);
		$json_decode  = json_decode($arr);
		//echo "<pre>".print_r($json_decode,true)."</pre>";
		//exit();
		renderView("pagemaster/pdf_cureinventory", [
			"datajson" => $json_decode,
			"date" => $date,
			"time" => $time
		]);
	}

	public function genwipfinalfgPDF()
	{
		if (!isset($_POST['item_group'])) {
			header('Content-Type: text/html; charset=utf-8');
			echo "กรุณาเลือกชนิดของ Product Group<br/>";
			echo "<a href='/report/wipfinalfg'>กลับไป</a>";
			exit;
		}

		// date_default_timezone_set("Asia/Bangkok");
		$date = date("d-m-Y");
		$time = date("H:i:s");

		$item_group = $_POST['item_group'];

		$product_group = 'TBR';
		if ($item_group === 'tbr') {
			$product_group = 'TBR';
		} else {
			$product_group = 'RDT';
		}

		$arr = $this->report->WIPServiceallpdf($product_group);
		$json_decode  = json_decode($arr);

		renderView("pagemaster/pdf_wipfinalfg", [
			"datajson" => $json_decode,
			"date" => $date,
			"time" => $time
		]);
	}

	public function curetireMaster()
	{
		renderView("report/curetire_master");
	}

	public function curetireMasterPdf()
	{
		$all = $this->report->cureCodeMasterReport();
		renderView("report/curetire_master_pdf", [
			"data" => $all
		]);
	}

	public function curetireMasterExcel()
	{
		$all = $this->report->cureCodeMasterReport();
		renderView("report/curetire_master_excel", [
			"data" => $all
		]);
	}

	public function renderGreentireHoldUnholdAndRepair()
	{
		renderView('report/greentire_hold_unhold_repair_report');
	}

	public function renderFinalHoldUnholdAndRepair()
	{
		renderView('report/final_hold_unhold_repair_report');
	}

	public function greentireHoldUnholdAndRepair()
	{
		if (!isset($_POST['item_group'])) {
			header('Content-Type: text/html; charset=utf-8');
			echo "กรุณาเลือกชนิดของ Product Group<br/>";
			echo "<a href='/report/greentire/hold_unhold_repair'>กลับไป</a>";
			exit;
		}

		$item_group = $_POST['item_group'];

		$product_group = 'TBR';
		if ($item_group === 'tbr') {
			$product_group = 'TBR';
		} else {
			$product_group = 'RDT';
		}

		$date = filter_input(INPUT_POST, "_date");
		$invent_trans = new InventTrans;
		$result = $invent_trans->greentireHoldUnholdAndRepair($date, $product_group);
		renderView('report/pdf_greentire_hold_unhold_repair_report', [
			"result" => json_decode($result),
			"date" => $date
		]);
	}

	public function finalHoldUnholdAndRepair()
	{
		if (!isset($_POST['item_group'])) {
			header('Content-Type: text/html; charset=utf-8');
			echo "กรุณาเลือกชนิดของ Product Group<br/>";
			echo "<a href='/report/final/hold_unhold_repair'>กลับไป</a>";
			exit;
		}

		$item_group = $_POST['item_group'];

		$product_group = 'TBR';
		if ($item_group === 'tbr') {
			$product_group = 'TBR';
		} else {
			$product_group = 'RDT';
		}
		$date = filter_input(INPUT_POST, "_date");
		$invent_trans = new InventTrans;
		$result = $invent_trans->finalHoldUnholdAndRepair($date, $product_group);
		renderView('report/pdf_final_hold_unhold_repair_report', [
			"result" => json_decode($result),
			"date" => $date
		]);
	}

	public function pdfCuringPressNew()
	{
		echo "PDF Curing Press New !";
	}

	public function buildingMachine()
	{
		renderView("report/building_machine");
	}

	public function buildingMachinePdf()
	{
		if (isset($_POST['machine'])) {
			$machine_select = $_POST['machine'];
			$machine  = '';
			foreach ($machine_select as $v) {
				$machine .= '\'' . $v . '\',';
			}
			$machine = trim($machine, ', ');
		}
		$date 	= filter_input(INPUT_POST, "date_building");
		$shift 	= filter_input(INPUT_POST, "shift");

		$data = (new ReportService)->buildingMachine($date, $shift, $machine);


		// $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
		// 			echo '<img width="100%" height="120" src="data:image/png;base64,' . base64_encode($generator->getBarcode($value->Barcode, $generator::TYPE_CODE_128)) . '"><br />';

		//echo "<pre>". print_r($data, true) . "</pre>";
		// $data = json_decode($data);
		// foreach ($data as $value) {

		// 	$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
		// 			echo '<img width="100" height="15" src="data:image/png;base64,' . base64_encode($generator->getBarcode($value->Barcode, $generator::TYPE_CODE_128)) . '">
		// 			<br><br>
		// 			';
		// }
		// exit();
		if ($shift == "day") {
			$shift = "กลางวัน";
		} else {
			$shift = "กลางคืน";
		}
		renderView('report/building_machine_pdf', [
			"data" => json_decode($data),
			"shift" => $shift,
			"date" => $date
		]);
	}

	public function LoadingPDF($pickingListId, $orderId, $createDate, $custName)
	{
		$custName = urldecode($custName);
		$data = (new ReportService)->Loading($pickingListId, $orderId, $createDate);
		$dataloading = json_decode($data);

		renderView('report/loading_pdf', [
			"pickingListId" => $pickingListId,
			"orderId" 		=> $orderId,
			"createDate" 	=> $createDate,
			"custName" 		=> $custName,
			"dataloading"	=> $dataloading
		]);
	}
}
