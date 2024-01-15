<?php

namespace App\V2\Report;

use App\V2\Report\ReportAPI;
use App\V2\ProductionSCH\ProductionSCHAPI;
use App\V2\Helper\Helper;
use App\Components\Utils;
use App\Components\Security;
use App\Components\Authentication;
use App\V2\TargetGreentire\TargetGreentireAPI;

class ReportController
{
	public function __construct()
	{
		$this->targetGreentireApi = new TargetGreentireAPI();
	}
	public function all()
	{
		renderView('report/report_all');
	}

	public function reportSch()
	{
		renderView('production_sch/report_sch');
	}

	public function reportSchCuring()
	{
		renderView('production_sch/report_sch_curing');
	}

	public function reportSchCuringPress()
	{
		renderView('production_sch/report_sch_curingpress');
	}

	public function reportSchSummary()
	{
		renderView('production_sch/report_sch_summary');
	}

	public function reportSchbillbuy()
	{
		renderView('production_sch/report_sch_billbuy');
	}

	public function reportSchOrder()
	{
		renderView('production_sch/report_sch_Order');
	}

	public function reportWeight()
	{
		renderView('production_sch/report_sch_weight');
	}

	public function reportSchGreentire()
	{
		renderView('production_sch/report_sch_greentire');
	}

	public function reportSchGreentireWithdraw()
	{
		renderView('production_sch/report_sch_greentire_withdraw');
	}
	public function reportSchrecive()
	{
		renderView('production_sch2/target_greentire_recivereport');
	}
	public function reportSplittire()
	{
		renderView('production_sch2/target_greentire_Splittire');
	}

	public function reportdisbursementtire()
	{
		renderView('production_sch2/target_greentire_disbursementtire');
	}

	public function reportFacetire()
	{
		renderView('production_sch2/target_greentire_Facetire');
	}

	public function reportsummaryorder()
	{
		renderView('production_sch/report_sch_summaryorder');
	}

	public function reportSchPdf($date, $shift, $type)
	{

		$date = date('Y-m-d', strtotime($date));
		$numyear = intval(substr($date, 0, 4));
		$nummonth  = intval(substr($date, 5, 2));
		$thaimonth = self::thaimonth($nummonth);
		$fulldate = "วันที่ " . intval(substr($date, 8, 2)) . " เดือน " . $thaimonth . " พ.ศ." . ($numyear + 543);

		$sch = new ReportAPI;
		$pro = new ProductionSCHAPI;

		$boilerall = $pro->load_cure();
		$report = $sch->reportSchPdf($date, $shift);
		$getboiler = $sch->getBoilerbyDate($date, $shift);;
		$sorted = [];

		foreach ($getboiler as $key => $value) {
			// echo $value["Boiler"]."**".$sch->countItemExist($date, $shift, $value['Boiler']);
			// echo "<br>";
			$check_rows = $sch->countItemExist($date, $shift, $value['Boiler']);
			$check_mold = $sch->countMoldExist($date, $shift, $value['Boiler']);
			// $check_mold = $query_rows[0]['MoldID'];

			// echo $value["Boiler"]."**".$check_rows;
			// echo "<br>";

			if ($check_rows === 1) {
				foreach ($report as $r) {
					if ($r['Boiler'] === $value['Boiler']) {
						if ($check_mold == "B") {
							$sorted[] = [
								'Boiler' => $r['Boiler'],
								'rowspan' => 2,
								'BoilerName' => $r['CureSize'],
								'ItemID' => "",
								'ItemName' => "",
								'Time' => "",
								'Target' => "",
								'Actual1' => "",
								'Actual2' => "",
								'Actual' => "",
								'Weight' => "",
								'Employee' => $sch->loademployee($r['Boiler'], $date, $shift),
								'Remark' => ""
							];

							$sorted[] = [
								'Boiler' => $r['Boiler'],
								'rowspan' => 2,
								'BoilerName' => $r['CureSize'],
								'ItemID' => $r['ItemID'],
								'ItemName' => $r['ItemName'],
								'Time' => $r['Time'],
								'Target' => $r['Target'],
								'Actual1' => $r['Actual1'],
								'Actual2' => $r['Actual2'],
								'Actual' => $r['Actual'],
								'Weight' => $r['Weight'],
								'Employee' => $sch->loademployee($r['Boiler'], $date, $shift),
								'Remark' => $sch->loadremark($r['ID'])
							];
						}
						if ($check_mold == "A") {
							$sorted[] = [
								'Boiler' => $r['Boiler'],
								'rowspan' => 2,
								'BoilerName' => $r['CureSize'],
								'ItemID' => $r['ItemID'],
								'ItemName' => $r['ItemName'],
								'Time' => $r['Time'],
								'Target' => $r['Target'],
								'Actual1' => $r['Actual1'],
								'Actual2' => $r['Actual2'],
								'Actual' => $r['Actual'],
								'Weight' => $r['Weight'],
								'Employee' => $sch->loademployee($r['Boiler'], $date, $shift),
								'Remark' => $sch->loadremark($r['ID'])
							];

							$sorted[] = [
								'Boiler' => $r['Boiler'],
								'rowspan' => 2,
								'BoilerName' => $r['CureSize'],
								'ItemID' => "",
								'ItemName' => "",
								'Time' => "",
								'Target' => "",
								'Actual1' => "",
								'Actual2' => "",
								'Actual' => "",
								'Weight' => "",
								'Employee' => $sch->loademployee($r['Boiler'], $date, $shift),
								'Remark' => ""
							];
						}
					}
				}
			}
			if ($check_rows === 2) {
				foreach ($report as $r) {
					if ($r['Boiler'] === $value['Boiler']) {
						$sorted[] = [
							'Boiler' => $r['Boiler'],
							'rowspan' => 2,
							'BoilerName' => $r['CureSize'],
							'ItemID' => $r['ItemID'],
							'ItemName' => $r['ItemName'],
							'Time' => $r['Time'],
							'Target' => $r['Target'],
							'Actual1' => $r['Actual1'],
							'Actual2' => $r['Actual2'],
							'Actual' => $r['Actual'],
							'Weight' => $r['Weight'],
							'Employee' => $sch->loademployee($r['Boiler'], $date, $shift),
							'Remark' => $sch->loadremark($r['ID'])
						];
					}
				}
			}
			if ($check_rows > 2) {
				foreach ($report as $r) {
					if ($r['Boiler'] === $value['Boiler']) {
						$sorted[] = [
							'Boiler' => $r['Boiler'],
							'rowspan' => $check_rows,
							'BoilerName' => $r['CureSize'],
							'ItemID' => $r['ItemID'],
							'ItemName' => $r['ItemName'],
							'Time' => $r['Time'],
							'Target' => $r['Target'],
							'Actual1' => $r['Actual1'],
							'Actual2' => $r['Actual2'],
							'Actual' => $r['Actual'],
							'Weight' => $r['Weight'],
							'Employee' => $sch->loademployee($r['Boiler'], $date, $shift),
							'Remark' => $sch->loadremark($r['ID'])
						];
					}
				}
			}
			if ($check_rows === 0) {
				for ($i = 0; $i < 2; $i++) {
					$sorted[] = [
						'Boiler' => $value['Boiler'],
						'rowspan' => 2,
						'BoilerName' => $value['CureSize'],
						'ItemID' => "",
						'ItemName' => "",
						'Time' => "",
						'Target' => "",
						'Actual1' => "",
						'Actual2' => "",
						'Actual' => "",
						'Weight' => "",
						'Employee' => "",
						'Remark' => ""
					];
				}
			}
		}

		// echo "<pre>".print_r($sorted,true)."</pre>";
		// exit();

		$countBoiler = $sch->countBoiler($date, $shift);
		$countMold   = $sch->countMold($date, $shift);
		$masterreportsch = $pro->getmasterreportsch($date, $shift);

		$sf = (int) $report[0]['ShiftFor'] === 1 ? 'C' : 'D';
		$st = (int) $shift === 1 ? ' (08.00 - 20.00 น.)' : ' (20.00 - 08.00 น.)';
		if ($type == 'pdf') {

			renderView("production_sch/report_sch_pdf", [
				"data" => $sorted,
				"date" => $fulldate,
				"shift" => $sf . $st,
				"countBoiler" => $countBoiler,
				"countMold"   => $countMold,
				"masterreportsch" => json_decode($masterreportsch)
			]);
		} else {

			renderView("production_sch/report_sch_excel", [
				"data" => $sorted,
				"date" => $fulldate,
				"shift" => $sf . $st,
				"countBoiler" => $countBoiler,
				"countMold"   => $countMold,
				"masterreportsch" => json_decode($masterreportsch)
			]);
		}
	}

	public function reportSchCuringPdf($date, $shift, $type)
	{
		$sch = new ReportAPI;
		$report = $sch->reportSchCuring($date, $shift);

		$countboiler = $sch->countBoiler();

		$sorted = [];
		foreach ($report as $value) {
			$sorted[] = [
				'CurID' => $value['CurID'],
				'CureSize' => $value['CureSize'],
				'Time' => $value['Time'],
				'Target' => $value['Target'],
				'Actual' => $value['Actual'],
				'Scrap' => $value['Scrap'],
				'Weight' => $value['Weight'],
				'Remark' => $sch->loadremark_byCure($value['CurID'], $date, $shift)
			];
		}

		$monthnum  = substr($date, 5);
		$monthname = date("F", strtotime("2001" . $monthnum . "01"));
		// echo $monthnum;
		// echo "<pre>".print_r($sorted,true)."</pre>";
		// exit();

		if ($report !== null) {

			if ($type == 1) {
				renderView('production_sch/report_sch_curing_pdf', [
					'data' => $sorted,
					'monthnum' => $monthnum,
					'monthname' => $monthname,
					'shift' => (int) $shift === 1 ? 'กลางวัน (08.00 - 20.00 น.)' : 'กลางคืน (20.00 - 08.00 น.)',
					'type' => strtoupper($type),
					'countboiler' => $countboiler
				]);
			} else {
				renderView('production_sch/report_sch_curing_excel', [
					'data' => $sorted,
					'monthnum' => $monthnum,
					'monthname' => $monthname,
					'shift' => (int) $shift === 1 ? 'กลางวัน (08.00 - 20.00 น.)' : 'กลางคืน (20.00 - 08.00 น.)',
					'type' => strtoupper($type),
					'countboiler' => $countboiler
				]);
			}
		} else {
			echo 'data not found!';
		}
	}

	public function reportSchSummaryPdf($date, $type)
	{

		$date = date('Y-m-d', strtotime($date));
		$numyear = intval(substr($date, 0, 4));
		$nummonth  = intval(substr($date, 5, 2));
		$thaimonth = self::thaimonth($nummonth);
		$fulldate = intval(substr($date, 8, 2)) . " เดือน " . $thaimonth . " พ.ศ." . ($numyear + 543);

		$sch = new ReportAPI;
		$data = $sch->reportSchSummary($date);
		$data1 = $this->targetGreentireApi->shiftwork($date);
		$shiftcheck = $data1[0]["ShiftFor"];
		$sf = (int) $shiftcheck === 1 ? 'C' : 'D';
		$st =  $st1 = ' (08.00 - 20.00 น.)';
		$sf1 = (int) $shiftcheck === 1 ? 'D' : 'C';
		$st1 =  $st1 = ' (20.00 - 08.00 น.)';

		$itemid = '';
		foreach ($data as $value) {
			if ($itemid != $value['ItemID']) {
				$sorted[] = [
					'ItemID' => $value['ItemID'],
					'ItemName' => $value['ItemName'],
					'Shift1'  => [],
					'Weight1' => [],
					'Target1' => [],
					'Actual1' => [],
					'Scrap1' => [],
					'Remark1' => [],
					'Shift2'  => [],
					'Weight2' => [],
					'Target2' => [],
					'Actual2' => [],
					'Scrap2' => [],
					'Remark2' => [],
					'WeightDefault' => $value['WeightDefault']
				];
			}
			$itemid = $value['ItemID'];

			$remark[] = [
				'ItemID' => $value['ItemID'],
				'Shift' => $value['Shift'],
				// 'Remark' => $sch->loadremark(19406)
				'Remark' => $sch->loadremark_byItem($date, $value['Shift'], $value['ItemID'])
			];
		}
		// echo "<pre>".print_r($remark,true)."</pre>";
		// exit();
		$line_a = [];
		$line_b = [];

		foreach ($data as $key => $value) {
			if ($value['Shift'] == 1) {
				$line_a[] = [
					'ItemID' => $value['ItemID'],
					'ItemName' => $value['ItemName'],
					'Shift1' => $value['Shift'],
					'Weight1' => $value['Weight'],
					'Target1' => $value['Target'],
					'Actual1' => $value['Actual'],
					'Scrap1' => $value['Scrap']
				];
			}
			if ($value['Shift'] == 2) {
				$line_b[] = [
					'ItemID' => $value['ItemID'],
					'ItemName' => $value['ItemName'],
					'Shift2' => $value['Shift'],
					'Weight2' => $value['Weight'],
					'Target2' => $value['Target'],
					'Actual2' => $value['Actual'],
					'Scrap2' => $value['Scrap']
				];
			}
		}

		foreach ($sorted as $k => $v) {
			if (count($line_a) > 0) {
				foreach ($line_a as $a) {
					if ($v['ItemID'] == $a['ItemID']) {
						array_push($sorted[$k]['Shift1'], $a['Shift1']);
						array_push($sorted[$k]['Weight1'], $a['Weight1']);
						array_push($sorted[$k]['Target1'], $a['Target1']);
						array_push($sorted[$k]['Actual1'], $a['Actual1']);
						array_push($sorted[$k]['Scrap1'], $a['Scrap1']);
					}
				}
			}

			if (count($line_b) > 0) {
				foreach ($line_b as $b) {
					if ($v['ItemID'] == $b['ItemID']) {
						array_push($sorted[$k]['Shift2'], $b['Shift2']);
						array_push($sorted[$k]['Weight2'], $b['Weight2']);
						array_push($sorted[$k]['Target2'], $b['Target2']);
						array_push($sorted[$k]['Actual2'], $b['Actual2']);
						array_push($sorted[$k]['Scrap2'], $a['Scrap2']);
					}
				}
			}

			$itemid = '';
			$r1 = '';
			foreach ($remark as $r) {
				if ($v['ItemID'] == $r['ItemID'] && $r['Shift'] == 1 && $r['Remark'] != $r1) {
					array_push($sorted[$k]['Remark1'], $r['Remark']);
				}
				if ($v['ItemID'] == $r['ItemID'] && $r['Shift'] == 2) {
					array_push($sorted[$k]['Remark2'], $r['Remark']);
				}
				// $itemid = $v['ItemID'];
				$r1 = $r['Remark'];
			}
		}
		// echo "<pre>".print_r($remark,true)."<pre>";
		// echo "<pre>".print_r($sorted,true)."<pre>";
		// exit();
		if ($type == "pdf") {
			renderView('production_sch/report_sch_summary_pdf', [
				'data' => $sorted,
				"date" => $fulldate,
				"shift1" => $sf . $st,
				"shift2" => $sf1 . $st1
			]);
		} else {
			renderView('production_sch/report_sch_summary_excel', [
				'data' => $sorted,
				"date" => $fulldate,
				"shift1" => $sf . $st,
				"shift2" => $sf1 . $st1
			]);
		}
	}

	public function reportSchWeightPdf($date, $type)
	{
		$sch = new ReportAPI;
		$report = $sch->reportSchWeight($date);

		$arr_month = [
			1 => 31, //Jan
			2 => 28, //fab
			3 => 31, //Mar
			4 => 30, //Apr
			5 => 31, //May
			6 => 30, //Jun
			7 => 31, //Jul
			8 => 31, //Aug
			9 => 30, //Sep
			10 => 31, //Oct
			11 => 30, //Nov
			12 => 31 //Dec
		];

		$numyear = intval(substr($date, 0, 4));
		$date_in = intval(substr($date, 5, 2));

		if (array_key_exists($date_in, $arr_month)) {
			$nummonth = $arr_month[$date_in];
		}

		// for ($i=1; $i <= $nummonth; $i++) {

		// 	if (strlen($i)=="1") {
		// 		$x = "0".$i;
		// 	}else{
		// 		$x = $i;
		// 	}

		// 	$date_check = $date."-".$x;

		// 	$found_key = array_search($date_check, array_column($report, 'SchDate'));

		// 	$nummonth_arr[] = [
		// 		$date_check => $found_key
		// 	];
		// }

		$item_temp = '';
		foreach ($report as $r) {
			if ($item_temp != $r['ItemID']) {

				$DataLists = self::LoopDate($date, $nummonth, $r['ItemID'], $report);

				$item_arr[] = [
					'ItemID' => $r['ItemID'],
					'ItemName' => $r['ItemName'],
					'Pattern' => $r['Pattern'],
					'TT' 	 => $r['TT'],
					'Color'  => $r['Color1'] . $r['Color2'] . $r['Color3'] . $r['Color4'] . $r['Color5'],
					'DaysLists'   => $DataLists
				];
			}
			$item_temp = $r['ItemID'];
		}

		if ($type == 1) {
			renderView('production_sch/report_sch_weight_pdf', [
				'data' => $item_arr,
				'nummonth' => $nummonth,
				'monththai' => self::thaimonth($date_in),
				'yearthai' => ($numyear + 543)
			]);
		} else {
			renderView('production_sch/report_sch_weight_excel', [
				'data' => $item_arr,
				'nummonth' => $nummonth,
				'monththai' => self::thaimonth($date_in),
				'yearthai' => ($numyear + 543)
			]);
		}
	}

	public function reportSchCuringpressPdf($date, $type)
	{
		$sch = new ReportAPI;
		$report = $sch->reportSchWeight($date);

		$arr_month = [
			1 => 31, //Jan
			2 => 28, //fab
			3 => 31, //Mar
			4 => 30, //Apr
			5 => 31, //May
			6 => 30, //Jun
			7 => 31, //Jul
			8 => 31, //Aug
			9 => 30, //Sep
			10 => 31, //Oct
			11 => 30, //Nov
			12 => 31 //Dec
		];

		$numyear = intval(substr($date, 0, 4));
		$date_in = intval(substr($date, 5, 2));

		if (array_key_exists($date_in, $arr_month)) {
			$nummonth = $arr_month[$date_in];
		}

		$item_temp = '';
		foreach ($report as $r) {
			if ($item_temp != $r['ItemID']) {

				$DataLists = self::LoopDate($date, $nummonth, $r['ItemID'], $report);

				$item_arr[] = [
					'ItemID' => $r['ItemID'],
					'ItemName' => $r['ItemName'],
					'DaysLists'   => $DataLists
				];
			}
			$item_temp = $r['ItemID'];
		}
		// echo "<pre>".print_r($item_arr,true)."</pre>";
		// exit();
		if ($type == 1) {
			renderView('production_sch/report_sch_curingpress_pdf', [
				'data' => $item_arr,
				'nummonth' => $nummonth,
				'monththai' => self::thaimonth($date_in),
				'yearthai' => ($numyear + 543)
			]);
		} else {
			renderView('production_sch/report_sch_curingpress_excel', [
				'data' => $item_arr,
				'nummonth' => $nummonth,
				'monththai' => self::thaimonth($date_in),
				'yearthai' => ($numyear + 543)
			]);
		}
	}

	public function reportSchReceiveGreentirePdf($date, $shift)
	{
		$sch = new ReportAPI;
		$date = date('Y-m-d', strtotime($date));

		$numyear = intval(substr($date, 0, 4));
		$nummonth  = intval(substr($date, 5, 2));
		$thaimonth = self::thaimonth($nummonth);
		$fulldate = "วันที่ " . intval(substr($date, 8, 2)) . " " . $thaimonth . " พ.ศ." . ($numyear + 543);

		$report = $sch->reportReceiveGreentirePdf($date, $shift);

		// echo "<pre>".print_r($report,true)."</pre>";
		renderView("production_sch/report_sch_greentire_receive_pdf", [
			"data" => $report,
			"date" => $fulldate,
			"shift" => (int) $shift === 1 ? 'กลางวัน (08.00 - 20.00 น.)' : 'กลางคืน (20.00 - 08.00 น.)',
			"shift_head" => (int) $shift === 1 ? 'C กลางวัน' : 'D กลางคืน'
		]);
	}

	public function reportSchGreentireWithdrawPdf()
	{
		$date = $_POST['date_sch'];
		$shift = $_POST['shift'];
		$date = date('Y-m-d', strtotime($date));

		$date_pay = $_POST['date_sch_pay'];
		$shift_pay = $_POST['shift_pay'];
		$date_pay = date('Y-m-d', strtotime($date_pay));

		$numyear = intval(substr($date, 0, 4));
		$nummonth  = intval(substr($date, 5, 2));
		$thaimonth = self::thaimonth($nummonth);
		$fulldate = "วันที่ " . intval(substr($date, 8, 2)) . " เดือน " . $thaimonth . " พ.ศ." . ($numyear + 543);

		$numyear_pay = intval(substr($date_pay, 0, 4));
		$nummonth_pay  = intval(substr($date_pay, 5, 2));
		$thaimonth_pay = self::thaimonth($nummonth_pay);
		$fulldate_pay = "เบิกยางให้ วันที่ " . intval(substr($date_pay, 8, 2)) . " เดือน " . $thaimonth_pay . " พ.ศ." . ($numyear_pay + 543);

		$sch = new ReportAPI;
		$report = $sch->reportSchPdf($date, $shift);

		// $sorted = [];
		foreach ($report as $value) {
			$sorted[] = [
				'Boiler' => $value['Boiler'],
				'BoilerName' => $value['CureSize'],
				'ItemID' => $value['ItemID'],
				'ItemName' => $value['ItemName'],
				'Time' => $value['Time'],
				'Target' => $value['Target'],
				'Actual' => $value['Actual'],
				'Scrap' => $value['Scrap'],
				'Weight' => $value['Weight'],
				'rowspan' => $value['rowspan'],
				'Remark' => $sch->loadremark($value['ID'])
			];
		}
		// echo "<pre>".print_r($sorted,true)."</pre>";
		// exit();
		renderView("production_sch/report_sch_greentire_withdraw_pdf", [
			"data" => $sorted,
			"date" => $fulldate,
			"shift" => (int) $shift === 1 ? 'กลางวัน (08.00 - 20.00 น.)' : 'กลางคืน (20.00 - 08.00 น.)',
			"date_pay" => $fulldate_pay,
			"shift_pay" => (int) $shift_pay === 1 ? 'กลางวัน (08.00 - 20.00 น.)' : 'กลางคืน (20.00 - 08.00 น.)'
		]);
	}

	public function LoopDate($reciveDate, $dataDays, $item, $report)
	{

		for ($i = 1; $i <= $dataDays; $i++) {

			if (strlen($i) == "1") {
				$x = "0" . $i;
			} else {
				$x = $i;
			}

			$day = $reciveDate . "-" . $x;

			$arr_lists[] = [
				"Date" => $day,
				"ActualC" => self::filterValueReport('actual', $day, 1, $item, $report),
				"ActualD" => self::filterValueReport('actual', $day, 2, $item, $report),
				"Weight"  => self::filterValueReport('weight', $day, 'NULL', $item, $report),
				"NetWeight"  => self::filterValueReport('netweight', $day, 'NULL', $item, $report)
			];
		}

		return $arr_lists;
	}

	public function filterValueReport($type, $day, $shift, $item, $dataTemp)
	{

		if ($type === 'actual') {
			$sumactual = 0;
			foreach ($dataTemp as $key => $value) {
				if ($value['ItemID'] === $item && $value['SchDate'] === $day && $value['Shift'] === $shift) {
					$sumactual += $value['Actual'];
				}
			}
			return $sumactual;
		}

		if ($type === 'weight') {
			$sumweight = 0;
			foreach ($dataTemp as $key => $value) {
				if ($value['ItemID'] === $item && $value['SchDate'] === $day) {
					$sumweight += $value['Weight'];
				}
			}
			return $sumweight;
		}

		if ($type === 'netweight') {
			$sumnetweight = 0;
			foreach ($dataTemp as $key => $value) {
				if ($value['ItemID'] === $item && $value['SchDate'] === $day) {
					$sumnetweight += $value['WeightTarget'];
				}
			}
			return $sumnetweight;
		}

		return 0;
	}

	public function thaiMonth($nummonth)
	{
		$thaimonth = array("มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม");
		return $thaimonth[$nummonth - 1];
	}

	public function reportgatgetReceiveGreentirePdf($date, $shift, $Type)
	{
		$sch = new ReportAPI;
		$date = date('Y-m-d', strtotime($date));

		$numyear = intval(substr($date, 0, 4));
		$nummonth  = intval(substr($date, 5, 2));
		$thaimonth = self::thaimonth($nummonth);
		$fulldate = "วันที่ " . intval(substr($date, 8, 2)) . " " . $thaimonth . " พ.ศ." . ($numyear + 543);

		$report = $sch->reportgatgetReceiveGreentirePdf($date, $shift);




		// echo "<pre>".print_r($report,true)."</pre>";
		if ($Type == 1) {
			renderView("production_sch/report_sch_greentire_receivereport_pdf", [
				"data" => $report,
				"date" => $fulldate,
				"shift" => (int) $shift === 1 ? 'กลางวัน (08.00 - 20.00 น.)' : 'กลางคืน (20.00 - 08.00 น.)',
				"shift_head" => (int) $shift === 1 ? 'C กลางวัน' : 'D กลางคืน',
				"shiftcheck" => (int) $shift === 1 ? 'แผนสร้าง กะกลางวัน (เส้น)' : 'แผนสร้าง กะกลางคืน (เส้น)',
				"shiftcheck2" => (int) $shift === 1 ? 'ผลิตได้ กะกลางวัน (เส้น)' : 'ผลิตได้ กะกลางคืน (เส้น)'
			]);
		} else {
			renderView("production_sch/report_sch_greentire_receivereport_excel", [
				"data" => $report,
				"date" => $fulldate,
				"shift" => (int) $shift === 1 ? 'กลางวัน (08.00 - 20.00 น.)' : 'กลางคืน (20.00 - 08.00 น.)',
				"shift_head" => (int) $shift === 1 ? 'C กลางวัน' : 'D กลางคืน',
				"shiftcheck" => (int) $shift
			]);
		}
	}

	public function reportSchreciveprint()
	{
		renderView('production_sch2/target_greentire_printreport');
	}

	public function schreportplan()
	{
		renderView('production_sch2/target_greentire_plantirereport');
	}

	public function reportgreentireprintPdf($date, $shift, $Type)
	{
		$sch = new ReportAPI;
		$date = date('Y-m-d', strtotime($date));

		$numyear = intval(substr($date, 0, 4));
		$nummonth  = intval(substr($date, 5, 2));
		$thaimonth = self::thaimonth($nummonth);
		$fulldate = "วันที่ " . intval(substr($date, 8, 2)) . " " . $thaimonth . " พ.ศ." . ($numyear + 543);

		$report = $sch->reportgatgetprintGreentirePdf($date, $shift);




		//echo "<pre>".print_r($report,true)."</pre>"; exit();
		if ($Type == 1) {
			renderView("production_sch/report_sch_printreport_pdf", [
				"data" => $report,
				"date" => $fulldate,
				"shift" => (int) $shift === 1 ? 'กลางวัน (08.00 - 20.00 น.)' : 'กลางคืน (20.00 - 08.00 น.)',
				"shift_head" => (int) $shift === 1 ? 'C กลางวัน' : 'D กลางคืน',
				"shiftcheck" => (int) $shift,
				"plan1" => (int) $shift === 1 ? 'แผนสร้าง<BR>กะกลางวัน (เส้น)' : 'แผนสร้าง<BR>กะกลางคืน (เส้น)',
				"plan2" => (int) $shift === 1 ? 'แผนผลิต<BR>กะกลางคืน' : 'แผนผลิต<BR>กะกลางวัน'
			]);
		} else {
			renderView("production_sch/report_sch_printreport_excel", [
				"data" => $report,
				"date" => $fulldate,
				"shift" => (int) $shift === 1 ? 'กลางวัน (08.00 - 20.00 น.)' : 'กลางคืน (20.00 - 08.00 น.)',
				"shift_head" => (int) $shift === 1 ? 'C กลางวัน' : 'D กลางคืน',
				"shiftcheck" => (int) $shift,
				"plan1" => (int) $shift === 1 ? 'แผนสร้าง<BR>กะกลางวัน (เส้น)' : 'แผนสร้าง<BR>กะกลางคืน (เส้น)',
				"plan2" => (int) $shift === 1 ? 'แผนผลิต<BR>กะกลางคืน' : 'แผนผลิต<BR>กะกลางวัน'
			]);
		}
	}
	public function reportgreentiresplittirePdf($date, $shift, $Type)
	{
		$sch = new ReportAPI;
		$date = date('Y-m-d', strtotime($date));

		$numyear = intval(substr($date, 0, 4));
		$nummonth  = intval(substr($date, 5, 2));
		$thaimonth = self::thaimonth($nummonth);
		$fulldate = "วันที่ " . intval(substr($date, 8, 2)) . " " . $thaimonth . " พ.ศ." . ($numyear + 543);
		$sorted = [];

		$report = $sch->reportgatgetsplitGreentirePdf($date, $shift);
		$typeall = $sch->TypeAll();

		foreach ($typeall as $key => $type) {
			$check_rows = $sch->countrowsplit($type['Type']); {
				if ($check_rows === 0) {
				} else {
					foreach ($report as $r) {
						if ($r['Type'] === $type['Type']) {
							$sorted[] = [
								'Type' => $r['Type'],
								'rowspan' => $check_rows,
								'Type2' => $r['Type2'],
								'Size' => $r['Size'],
								'Countprintcure' => $r['Countprintcure'],
								'CureDay' => $r['CureDay'],
								'CountPrint' => $r['CountPrint'],
								'GreentireDay' => $r['GreentireDay'],
								'TT' => $r['TT'],
								'TCD' => $r['TCD']

							];
						}
					}
				}
			}
		}

		// foreach ($report as $key => $value)
		// {
		//
		//
		// 					$sorted[] = [
		// 						'Type' => $value['Type'],
		// 						'rowspan' => $check_rows,
		// 						'Type2' => $value['Type2'],
		// 						'Size' => $value['Size'],
		// 						'Countprintcure' => $value['Countprintcure'],
		// 						'CureDay' => $value['CureDay'],
		// 						'CountPrint' => $value['CountPrint'],
		// 						'GreentireDay' => $value['GreentireDay'],
		//  					];
		// }

		//echo "<pre>".print_r($sorted,true)."</pre>";  exit();

		if ($Type == 1) {
			renderView("production_sch/report_sch_splittirePdf", [
				"data" => $sorted,
				"date" => $fulldate,
				"shift" => (int) $shift === 1 ? 'กลางวัน (08.00 - 20.00 น.)' : 'กลางคืน (20.00 - 08.00 น.)',
				"shift_head" => (int) $shift === 1 ? 'C กลางวัน' : 'D กลางคืน',
				"shiftcheck" => (int) $shift
			]);
		} else {
			renderView("production_sch/report_sch_splittireExecl", [
				"data" => $sorted,
				"date" => $fulldate,
				"shift" => (int) $shift === 1 ? 'กลางวัน (08.00 - 20.00 น.)' : 'กลางคืน (20.00 - 08.00 น.)',
				"shift_head" => (int) $shift === 1 ? 'C กลางวัน' : 'D กลางคืน',
				"shiftcheck" => (int) $shift
			]);
		}
	}

	public function reportdisbursementtirePdf($date, $shift, $Type)
	{
		$sch = new ReportAPI;
		$date = date('Y-m-d', strtotime($date));
		$numyear = intval(substr($date, 0, 4));
		$nummonth  = intval(substr($date, 5, 2));
		$thaimonth = self::thaimonth($nummonth);
		$fulldate = "วันที่ " . intval(substr($date, 8, 2)) . " " . $thaimonth . " พ.ศ." . ($numyear + 543);
		$table = 'ProductionGreentireFaceOfireTable';
		$getdate = $sch->getdatebyshift($date, $shift, $table);
		// echo "<pre>";
		// print_r($getdate);
		// "</pre>";
		// exit();
		//	$sorted = [];
		$report = $sch->reportdisbursementtirePdf($date, $shift, $getdate["dateold"], $getdate["shiftold"], $getdate["datenext"], $getdate["shifnext"]);
		//echo "<pre>"; print_r($report) ; "</pre>"; exit();




		if ($Type == 1) {
			renderView("production_sch/reportdisbursementtirePdf", [
				"data" => $report,
				"date" => $fulldate,
				"shift" => (int) $shift === 1 ? 'กลางวัน (08.00 - 20.00 น.)' : 'กลางคืน (20.00 - 08.00 น.)',
				"shift_head" => (int) $shift === 1 ? 'C กลางวัน' : 'D กลางคืน',
				"shiftcheck" => (int) $shift
			]);
		} else {
			renderView("production_sch/reportdisbursementtire_excel", [
				"data" => $report,
				"date" => $fulldate,
				"shift" => (int) $shift === 1 ? 'กลางวัน (08.00 - 20.00 น.)' : 'กลางคืน (20.00 - 08.00 น.)',
				"shift_head" => (int) $shift === 1 ? 'C กลางวัน' : 'D กลางคืน',
				"shiftcheck" => (int) $shift,
				"topic1" => (int) $shift === 1 ? 'แผนผลิตกะกลางวัน(เส้น)' : 'แผนผลิตกะกลางคืน(เส้น)',
				"topic2" => (int) $shift === 1 ? 'แผนผลิต กะ กลางคืน' : 'แผนผลิต กะ กลางวัน',
				"topic3" => (int) $shift === 1 ? 'ผลิตได้ กะ กลางวัน ' : 'ผลิตได้ กะ กลางคืน '
			]);
		}
	}
	public function reportgreentirefacetirePdf($date, $shift, $Type)
	{
		$sch = new ReportAPI;
		$date = date('Y-m-d', strtotime($date));
		$numyear = intval(substr($date, 0, 4));
		$nummonth  = intval(substr($date, 5, 2));
		$thaimonth = self::thaimonth($nummonth);
		$fulldate = "วันที่ " . intval(substr($date, 8, 2)) . " " . $thaimonth . " พ.ศ." . ($numyear + 543);
		//	$sorted = [];
		$table = 'ProductionGreentireFaceOfireTable';
		$getdate = $sch->getdatebyshift($date, $shift, $table);
		// echo  $getdate["dateold"];
		// exit();

		$report = $sch->reportgreentirefacetirePdf($date, $shift, $getdate["dateold"], $getdate["shiftold"], $getdate["datenext"], $getdate["shifnext"]);
		//echo "<pre>"; print_r($report) ; "</pre>"; exit();




		if ($Type == 1) {
			renderView("production_sch/reporfacetirePdf", [
				"data" => $report,
				"date" => $fulldate,
				"shift" => (int) $shift === 1 ? 'กลางวัน (08.00 - 20.00 น.)' : 'กลางคืน (20.00 - 08.00 น.)',
				"shift_head" => (int) $shift === 1 ? 'C กลางวัน' : 'D กลางคืน',
				"shiftcheck" => (int) $shift
			]);
		} else {
			renderView("production_sch/reporfacetirExcel", [
				"data" => $report,
				"date" => $fulldate,
				"shift" => (int) $shift === 1 ? 'กลางวัน (08.00 - 20.00 น.)' : 'กลางคืน (20.00 - 08.00 น.)',
				"shift_head" => (int) $shift === 1 ? 'C กลางวัน' : 'D กลางคืน',
				"shiftcheck" => (int) $shift
			]);
		}
	}
	public function reportgreentireplantirePdf($date, $shift, $Type)
	{
		$sch = new ReportAPI;
		$date = date('Y-m-d', strtotime($date));

		$numyear = intval(substr($date, 0, 4));
		$nummonth  = intval(substr($date, 5, 2));
		$thaimonth = self::thaimonth($nummonth);
		$fulldate = "วันที่ " . intval(substr($date, 8, 2)) . " " . $thaimonth . " พ.ศ." . ($numyear + 543);

		$report = $sch->reportgatgetplantirePdf($date, $shift);
		$report2 = $sch->reportgatgetplantirePdfGroup2($date, $shift);
		$reportall = $sch->reportgatgetplantirePdfGroupall($date, $shift);
		$getdatetire = $sch->getdateplantire($date);

		$date1 =  date_format(date_create($date), "d/m/Y");
		$date2 =  date_format(date_create($getdatetire[0]["DateBuild"]), "d/m/Y");
		$date3 =  date_format(date_create($getdatetire[1]["DateBuild"]), "d/m/Y");
		// echo "<pre>";
		// print_r($report);
		// echo "</pre>";
		// exit();
		if ($shift == 1 || $shift == 2) {
			if ($Type == 1) {
				renderView("production_sch/report_sch_plantirereport_pdf", [
					"data" => $report,
					"data2" => $report2,
					"date" => $fulldate,
					"shift" => (int) $shift === 1 ? 'กลางวัน (08.00 - 20.00 น.)' : 'กลางคืน (20.00 - 08.00 น.)',
					"shift_head" => (int) $shift === 1 ? 'C กลางวัน' : 'D กลางคืน',
					"shiftcheck" => (int) $shift,
					"date1" => $date1,
					"date2" => $date2,
					"date3" => $date3
				]);
			} else {

				renderView("production_sch/report_sch_plantirereport_excel", [
					"data" => $report,
					"data2" => $report2,
					"date" => $fulldate,
					"shift" => (int) $shift === 1 ? 'กลางวัน (08.00 - 20.00 น.)' : 'กลางคืน (20.00 - 08.00 น.)',
					"shift_head" => (int) $shift === 1 ? 'C กลางวัน' : 'D กลางคืน',
					"shiftcheck" => (int) $shift,
					"date1" => $date1,
					"date2" => $date2,
					"date3" => $date3
				]);
			}
		} else {
			if ($shift == 3) {
				$shift = 1;
			} else {
				$shift = 2;
			}
			if ($Type == 1) {
				renderView("production_sch/report_sch_plantirereportall_pdf", [
					"data" => $reportall,
					"date" => $fulldate,
					"shift" => (int) $shift === 1 ? 'กลางวัน (08.00 - 20.00 น.)' : 'กลางคืน (20.00 - 08.00 น.)',
					"shift_head" => (int) $shift === 1 ? 'C กลางวัน' : 'D กลางคืน',
					"shiftcheck" => (int) $shift,
					"date1" => $date1,
					"date2" => $date2,
					"date3" => $date3
				]);
			} else {
				renderView("production_sch/report_sch_plantirereportAll_excel", [
					"data" => $reportall,
					"date" => $fulldate,
					"shift" => (int) $shift === 1 ? 'กลางวัน (08.00 - 20.00 น.)' : 'กลางคืน (20.00 - 08.00 น.)',
					"shift_head" => (int) $shift === 1 ? 'C กลางวัน' : 'D กลางคืน',
					"shiftcheck" => (int) $shift,
					"date1" => $date1,
					"date2" => $date2,
					"date3" => $date3
				]);
			}
		}
	}
	public function reportSchBillbuyPdf($date, $type)
	{

		$date = date('Y-m-d', strtotime($date));
		$numyear = intval(substr($date, 0, 4));
		$nummonth  = intval(substr($date, 5, 2));
		$thaimonth = self::thaimonth($nummonth);
		$fulldate = intval(substr($date, 8, 2)) . " เดือน " . $thaimonth . " พ.ศ." . ($numyear + 543);

		$sch = new ReportAPI;
		$data = $sch->reportSchbillbuy($date);
		$data1 = $this->targetGreentireApi->shiftwork($date);
		$shiftcheck = $data1[0]["ShiftFor"];
		$sf = (int) $shiftcheck === 1 ? 'C' : 'D';
		$st =  $st1 = ' (08.00 - 20.00 น.)';
		$sf1 = (int) $shiftcheck === 1 ? 'D' : 'C';
		$st1 =  $st1 = ' (20.00 - 08.00 น.)';

		$itemid = '';


		foreach ($data as $value) {
			if ($itemid != $value['ItemID']) {
				$sorted[] = [
					'ItemID' => $value['ItemID'],
					'ItemName' => $value['ItemName'],
					'Shift1'  => [],
					'BillUse1' => [],
					'BillGive1' => [],
					'faceBoiler1' => [],
					'Shift2'  => [],
					'BillUse2' => [],
					'BillGive2' => [],
					'faceBoiler2' => []

				];
			}
			$itemid = $value['ItemID'];
		}
		// echo "<pre>" . print_r($remark, true) . "</pre>";
		// exit();
		$line_a = [];
		$line_b = [];

		foreach ($data as $key => $value) {
			if ($value['Shift'] == 1) {
				$line_a[] = [
					'ItemID' => $value['ItemID'],
					'ItemName' => $value['ItemName'],
					'Shift1' => $value['Shift'],
					'BillUse1' => $value['BillUse'],
					'BillGive1' => $value['BillGive'],
					'faceBoiler1' => $value['faceBoiler']

				];
			}
			if ($value['Shift'] == 2) {
				$line_b[] = [
					'ItemID' => $value['ItemID'],
					'ItemName' => $value['ItemName'],
					'Shift2' => $value['Shift'],
					'BillUse2' => $value['BillUse'],
					'BillGive2' => $value['BillGive'],
					'faceBoiler2' => $value['faceBoiler']
				];
			}
		}
		// echo "<pre>" . print_r($line_b, true) . "<pre>";
		// exit();

		foreach ($sorted as $k => $v) {
			if (count($line_a) > 0) {
				foreach ($line_a as $a) {
					if ($v['ItemID'] == $a['ItemID']) {
						array_push($sorted[$k]['Shift1'], $a['Shift1']);
						array_push($sorted[$k]['BillUse1'], $a['BillUse1']);
						array_push($sorted[$k]['BillGive1'], $a['BillGive1']);
						array_push($sorted[$k]['faceBoiler1'], $a['faceBoiler1']);
					}
				}
			}

			if (count($line_b) > 0) {
				foreach ($line_b as $b) {
					if ($v['ItemID'] == $b['ItemID']) {
						array_push($sorted[$k]['Shift2'], $b['Shift2']);
						array_push($sorted[$k]['BillUse2'], $b['BillUse2']);
						array_push($sorted[$k]['BillGive2'], $b['BillGive2']);
						array_push($sorted[$k]['faceBoiler2'], $b['faceBoiler2']);
					}
				}
			}

			$itemid = '';
			$r1 = '';
			// foreach ($remark as $r) {

		}
		// echo "<pre>".print_r($remark,true)."<pre>";
		// echo "<pre>" . print_r($sorted, true) . "<pre>";
		// exit();
		if ($type == "pdf") {
			renderView('production_sch/report_sch_billbuy_pdf', [
				'data' => $sorted,
				"date" => $fulldate,
				"shift1" => $sf . $st,
				"shift2" => $sf1 . $st1
			]);
		} else {
			renderView('production_sch/report_sch_billbuy_excel', [
				'data' => $sorted,
				"date" => $fulldate,
				"shift1" => $sf . $st,
				"shift2" => $sf1 . $st1
			]);
		}
	}

	public function reportSchOrderPdf($date, $shift, $Type)
	{
		$sch = new ReportAPI;
		$report = $sch->reportSchOrder($date, $shift);
		$date = date('Y-m-d', strtotime($date));
		$numyear = intval(substr($date, 0, 4));
		$nummonth  = intval(substr($date, 5, 2));
		$thaimonth = self::thaimonth($nummonth);
		$fulldate = intval(substr($date, 8, 2)) . " เดือน " . $thaimonth . " พ.ศ." . ($numyear + 543);

		//$countboiler = $sch->countBoiler();

		// echo "<pre>" . print_r($report, true) . "</pre>";
		// exit();
		$sorted = [];
		foreach ($report as $value) {
			$sorted[] = [
				'ItemId' => $value['ItemId'],
				'ColorAll' => $value['ColorAll'],
				'ItemGTName' => $value['ItemGTName'],
				'Actual' => $value['Actual'],
				'BomCheck' => $value['BomCheck'],
				'SpareOfcure' => $value['SpareOfcure'],
				'StockInplan' => $value['StockInplan'],
				'CountIn' => $value['CountIn'],
				'CountOut' => $value['CountOut'],
				'CountCure' => $value['CountCure'],
				'SpareOfcure2' => $value['SpareOfcure2'],
				'CountInOrder' => $value['CountInOrder'],
				'GreentireInDept' => $value['GreentireInDept'],
				'SummaryInDept' => $value['SummaryInDept'],
				'CalCure' => $value['CalCure'],
				'SummaryCure' => $value['SummaryCure'],
				'CompareCreateRecve' => $value['CompareCreateRecve'],
				'CompareBillBuy' => $value['CompareBillBuy'],
				'CompareFaceTire' => $value['CompareFaceTire'],
				'Id' => $value['Id'],
				'CompareReal' => $value['CompareReal']
				//'Remark' => $sch->loadremark_byCure($value['CurID'], $date, $shift)
			];
		}

		$monthnum  = substr($date, 5);
		$monthname = date("F", strtotime("2001" . $monthnum . "01"));
		// echo $monthnum;
		// echo "<pre>" . print_r($sorted, true) . "</pre>";
		// exit();

		if ($report !== null) {

			if ($Type == 1) {
				renderView('production_sch/report_sch_orderfeport_pdf', [
					'data' => $sorted,
					'monthnum' => $monthnum,
					'monthname' => $monthname,
					'shift' => (int) $shift === 1 ? 'กลางวัน (08.00 - 20.00 น.)' : 'กลางคืน (20.00 - 08.00 น.)',
					'type' => strtoupper($Type),
					'date' => $fulldate
				]);
			} else {
				renderView('production_sch/report_sch_orderfeport_excel', [
					'data' => $sorted,
					'monthnum' => $monthnum,
					'monthname' => $monthname,
					'shift' => (int) $shift === 1 ? 'กลางวัน (08.00 - 20.00 น.)' : 'กลางคืน (20.00 - 08.00 น.)',
					'type' => strtoupper($Type),
					'date' => $fulldate
				]);
			}
		} else {
			echo 'data not found!';
		}
	}

	public function reportSchSummaryMonthtPdf($date, $type)
	{
		$sch = new ReportAPI;

		$checkdate = explode('-', $date);
		$checkYear = $checkdate[0];
		$checkmonth = $checkdate[1];
		$firstday = $checkYear . "-" . $checkmonth . "-" . "01";
		$report = $sch->reportSchSummaryMonth($checkYear, $checkmonth, $firstday);
		//$date = date('Y-m-d', strtotime($date));
		$numyear = intval(substr($date, 0, 4));
		$nummonth  = intval(substr($date, 5, 2));
		$thaimonth = self::thaimonth($nummonth);
		$fulldate = " เดือน " . $thaimonth . " พ.ศ." . ($numyear + 543);
		// print_r($report);
		// exit();
		//$countboiler = $sch->countBoiler();

		$sorted = [];
		foreach ($report as $value) {
			$sorted[] = [
				'ItemId' => $value['ItemId'],
				'ColorAll' => $value['ColorAll'],
				'ItemGTName' => $value['ItemGTName'],
				'Actual' => $value['Actual'],
				'Bom' => $value['Bom'],
				'SpareOfcure' => $value['SpareOfcure'],
				'StockInplan' => $value['StockInplan'],
				'CountIn' => $value['CountIn'],
				'CountOut' => $value['CountOut'],
				'CountCure' => $value['CountCure'],
				'TotalGreentire' => $value['TotalGreentire'],
				'AmountInplan' => $value['AmountInplan'],
				'CalCure' => $value['CalCure'],
				'SummaryCure' => $value['SummaryCure']

			];
		}

		$monthnum  = substr($date, 5);
		$monthname = date("F", strtotime("2001" . $monthnum . "01"));
		// echo $monthnum;
		// echo "<pre>".print_r($sorted,true)."</pre>";
		// exit();

		if ($report !== null) {

			if ($type == 1) {
				renderView('production_sch/report_sch_SummaryMontReport_pdf', [
					'data' => $sorted,
					'monthnum' => $monthnum,
					'monthname' => $monthname,
					//'shift' => (int) $shift === 1 ? 'กลางวัน (08.00 - 20.00 น.)' : 'กลางคืน (20.00 - 08.00 น.)',
					'type' => strtoupper($type),
					'date' => $fulldate
				]);
			} else {
				renderView('production_sch/report_sch_SummaryMontReport_excel', [
					'data' => $sorted,
					'monthnum' => $monthnum,
					'monthname' => $monthname,
					//	'shift' => (int) $shift === 1 ? 'กลางวัน (08.00 - 20.00 น.)' : 'กลางคืน (20.00 - 08.00 น.)',
					'type' => strtoupper($type),
					'date' => $fulldate
				]);
			}
		} else {
			echo 'data not found!';
		}
	}

	public function reportSchDraw()
	{
		renderView('production_sch/report_schDraw');
	}

	public function reportSchDrawPdf($date, $shift, $type)
	{

		$date = date('Y-m-d', strtotime($date));
		$numyear = intval(substr($date, 0, 4));
		$nummonth  = intval(substr($date, 5, 2));
		$thaimonth = self::thaimonth($nummonth);
		$fulldate = "วันที่ " . intval(substr($date, 8, 2)) . " เดือน " . $thaimonth . " พ.ศ." . ($numyear + 543);

		$sch = new ReportAPI;
		$pro = new ProductionSCHAPI;

		$boilerall = $pro->load_cure();
		$report = $sch->reportSchDrawPdf($date, $shift);
		// echo "<pre>";
		// print_r($report);
		// echo "/<pre>";
		// exit();
		$getboiler = $sch->getBoilerbyDateDraw($date, $shift);
		$sorted = [];

		foreach ($getboiler as $key => $value) {
			echo $value["Boiler"] . "**" . $sch->countItemExist($date, $shift, $value['Boiler']);
			echo "<br>";
			$check_rows = $sch->countItemExist($date, $shift, $value['Boiler']);
			$check_mold = $sch->countMoldExist($date, $shift, $value['Boiler']);
			// $check_mold = $query_rows[0]['MoldID'];

			// echo $value["Boiler"] . "**" . $check_rows;
			// echo "<br>";

			if ($check_rows === 1) {
				foreach ($report as $r) {
					if ($r['Boiler'] === $value['Boiler']) {
						if ($check_mold == "B") {
							// $sorted[] = [
							// 	'Boiler' => $r['Boiler'],
							// 	'rowspan' => 1,
							// 	'BoilerName' => $value['CureSize'],
							// 	'ItemID' => "",
							// 	'ItemName' => "",
							// 	'Time' => "",
							// 	'Target' => "",
							// 	'Actual1' => "",
							// 	'Actual2' => "",
							// 	'Actual' => "",
							// 	'Weight' => "",
							// 	'Employee' => $sch->loademployee($r['Boiler'], $date, $shift),
							// 	'Remark' => "",
							// 	'BillUse' => $r['BillUse'],
							// 	'BillGive' => $r['BillGive'],
							// 	'faceBoiler' => $r['faceBoiler']

							// ];

							$sorted[] = [
								'Boiler' => $r['Boiler'],
								'rowspan' => 1,
								'BoilerName' => $value['CureSize'],
								'ItemID' => $r['ItemID'],
								'ItemName' => $r['ItemName'],
								'Time' => $r['Time'],
								'Target' => $r['Target'],
								'Actual1' => $r['Actual1'],
								'Actual2' => $r['Actual2'],
								'Actual' => $r['Actual'],
								'Weight' => $r['Weight'],
								'Employee' => $sch->loademployee($r['Boiler'], $date, $shift),
								'Remark' => $sch->loadremark($r['ID']),
								'BillUse' => $r['BillUse'],
								'BillGive' => $r['BillGive'],
								'faceBoiler' => $r['faceBoiler']
							];
						}
						if ($check_mold == "A") {
							$sorted[] = [
								'Boiler' => $r['Boiler'],
								'rowspan' => 1,
								'BoilerName' => $value['CureSize'],
								'ItemID' => $r['ItemID'],
								'ItemName' => $r['ItemName'],
								'Time' => $r['Time'],
								'Target' => $r['Target'],
								'Actual1' => $r['Actual1'],
								'Actual2' => $r['Actual2'],
								'Actual' => $r['Actual'],
								'Weight' => $r['Weight'],
								'Employee' => $sch->loademployee($r['Boiler'], $date, $shift),
								'Remark' => $sch->loadremark($r['ID']),
								'BillUse' => $r['BillUse'],
								'BillGive' => $r['BillGive'],
								'faceBoiler' => $r['faceBoiler']
							];

							// $sorted[] = [
							// 	'Boiler' => $r['Boiler'],
							// 	'rowspan' => 1,
							// 	'BoilerName' => $value['CureSize'],
							// 	'ItemID' => "",
							// 	'ItemName' => "",
							// 	'Time' => "",
							// 	'Target' => "",
							// 	'Actual1' => "",
							// 	'Actual2' => "",
							// 	'Actual' => "",
							// 	'Weight' => "",
							// 	'Employee' => $sch->loademployee($r['Boiler'], $date, $shift),
							// 	'Remark' => "",
							// 	'BillUse' => $r['BillUse'],
							// 	'BillGive' => $r['BillGive'],
							// 	'faceBoiler' => $r['faceBoiler']
							// ];
						}
					}
				}
			}
			if ($check_rows === 2) {
				foreach ($report as $r) {
					if ($r['Boiler'] === $value['Boiler']) {
						$sorted[] = [
							'Boiler' => $r['Boiler'],
							'rowspan' => 2,
							'BoilerName' => $value['CureSize'],
							'ItemID' => $r['ItemID'],
							'ItemName' => $r['ItemName'],
							'Time' => $r['Time'],
							'Target' => $r['Target'],
							'Actual1' => $r['Actual1'],
							'Actual2' => $r['Actual2'],
							'Actual' => $r['Actual'],
							'Weight' => $r['Weight'],
							'Employee' => $sch->loademployee($r['Boiler'], $date, $shift),
							'Remark' => $sch->loadremark($r['ID']),
							'BillUse' => $r['BillUse'],
							'BillGive' => $r['BillGive'],
							'faceBoiler' => $r['faceBoiler']
						];
					}
				}
			}
			if ($check_rows > 2) {
				foreach ($report as $r) {
					if ($r['Boiler'] === $value['Boiler']) {
						$sorted[] = [
							'Boiler' => $r['Boiler'],
							'rowspan' => $check_rows,
							'BoilerName' => $value['CureSize'],
							'ItemID' => $r['ItemID'],
							'ItemName' => $r['ItemName'],
							'Time' => $r['Time'],
							'Target' => $r['Target'],
							'Actual1' => $r['Actual1'],
							'Actual2' => $r['Actual2'],
							'Actual' => $r['Actual'],
							'Weight' => $r['Weight'],
							'Employee' => $sch->loademployee($r['Boiler'], $date, $shift),
							'Remark' => $sch->loadremark($r['ID']),
							'BillUse' => $r['BillUse'],
							'BillGive' => $r['BillGive'],
							'faceBoiler' => $r['faceBoiler']
						];
					}
				}
			}
			if ($check_rows === 0) {
				for ($i = 0; $i < 2; $i++) {
					$sorted[] = [
						'Boiler' => $value['Boiler'],
						'rowspan' => 2,
						'BoilerName' => $value['CureSize'],
						'ItemID' => "",
						'ItemName' => "",
						'Time' => "",
						'Target' => "",
						'Actual1' => "",
						'Actual2' => "",
						'Actual' => "",
						'Weight' => "",
						'Employee' => "",
						'Remark' => "",
						'BillUse' => "",
						'BillGive' => "",
						'faceBoiler' => ""
					];
				}
			}
		}

		// echo "<pre>" . print_r($sorted, true) . "</pre>";
		//exit();

		$countBoiler = $sch->countBoiler($date, $shift);
		$countMold   = $sch->countMold($date, $shift);
		$masterreportsch = $pro->getmasterreportsch($date, $shift);

		$sf = (int) $report[0]['ShiftFor'] === 1 ? 'C' : 'D';
		$st = (int) $shift === 1 ? ' (08.00 - 20.00 น.)' : ' (20.00 - 08.00 น.)';
		if ($type == 'pdf') {

			renderView("production_sch/report_schDraw_pdf", [
				"data" => $sorted,
				"date" => $fulldate,
				"shift" => $sf . $st,
				"countBoiler" => $countBoiler,
				"countMold"   => $countMold,
				"masterreportsch" => json_decode($masterreportsch)
			]);
		} else {

			renderView("production_sch/report_schDraw_excel", [
				"data" => $sorted,
				"date" => $fulldate,
				"shift" => $sf . $st,
				"countBoiler" => $countBoiler,
				"countMold"   => $countMold,
				"masterreportsch" => json_decode($masterreportsch)
			]);
		}
	}

	public function reportSchall()
	{
		renderView('production_sch/report_schall');
	}

	public function reportSchPdfall($date, $type)
	{

		$date = date('Y-m-d', strtotime($date));
		$numyear = intval(substr($date, 0, 4));
		$nummonth  = intval(substr($date, 5, 2));
		$thaimonth = self::thaimonth($nummonth);
		$fulldate = "วันที่ " . intval(substr($date, 8, 2)) . " เดือน " . $thaimonth . " พ.ศ." . ($numyear + 543);

		$sch = new ReportAPI;
		$pro = new ProductionSCHAPI;

		$boilerall = $pro->load_cure();
		$report = $sch->reportSchallPdf($date);
		$getboiler = $sch->getBoilerbyDateall($date);;
		$sorted = [];
		// echo "<pre>";
		// print_r($report);
		// echo "</pre>";
		// exit();


		foreach ($getboiler as $key => $value) {
			// echo $value["Boiler"] . "**" . $sch->countItemExistall($date, $value['Boiler']);
			// echo "<br>";

			// $check_rows = $sch->countItemExistall($date, $value['Boiler']);
			// $check_mold = $sch->countMoldExistall($date, $value['Boiler']);

			// $check_mold = $query_rows[0]['MoldID'];

			// echo $value["Boiler"]."**".$check_rows;
			// echo "<br>";

			foreach ($report as $r) {
				if ($r['checkrows'] === 1) {

					if ($r['Boiler'] === $value['Boiler']) {
						if ($r['checkMoldID'] == "B") {
							$sorted[] = [
								'Boiler' => $r['Boiler'],
								'rowspan' => 2,
								'BoilerName' => $r['CureSize'],
								'ItemID' => "",
								'ItemName' => "",
								'Time' => "",
								'Target' => "",
								'Actual1' => "",
								'Actual2' => "",
								'Actual' => "",
								'Weight' => "",
								'Remark' => "",
								'Shift' => "",
								'ItemID_D' => "",
								'ItemName_D' => "",
								'Time_D' => "",
								'Target_D' => "",
								'Actual1_D' => "",
								'Actual2_D' => "",
								'Actual_D' => "",
								'Weight_D' => "",
								'Remark_D' => "",
								'rowspantop' => $r['rowtop'],
								'TotalBoiler' => $r['TotalBoiler']

							];

							$sorted[] = [
								'Boiler' => $r['Boiler'],
								'rowspan' => 2,
								'BoilerName' => $r['CureSize'],
								'ItemID' => $r['ITEMID_SHIFT1'],
								'ItemName' => $r['ITEMNAME_SHIFT1'],
								'Time' => $r['TIME_SHIFT1'],
								'Target' => $r['TARGET_SHIFT1'],
								'Actual1' => $r['ACTUAL1_SHIFT1'],
								'Actual2' => $r['ACTUAL2_SHIFT1'],
								'Actual' => $r['ACTUAL_SHIFT1'],
								'Weight' => $r['WEIGHT_SHIFT1'],
								//'Employee' => $sch->loademployee($r['Boiler'], $date, $shift),
								'Remark' => $sch->loadremark($r['ID_SHIFT1']),
								'ItemID_D' => $r['ITEMID_SHIFT2'],
								'ItemName_D' => $r['ITEMNAME_SHIFT2'],
								'Time_D' => $r['TIME_SHIFT2'],
								'Target_D' => $r['TARGET_SHIFT2'],
								'Actual1_D' => $r['ACTUAL1_SHIFT2'],
								'Actual2_D' => $r['ACTUAL2_SHIFT2'],
								'Actual_D' => $r['ACTUAL_SHIFT2'],
								'Weight_D' => $r['WEIGHT_SHIFT2'],
								'Remark_D' => $sch->loadremark($r['ID_SHIFT2']),
								'rowspantop' => $r['rowtop'],
								'TotalBoiler' => $r['TotalBoiler']


							];
						}
						if ($r['checkMoldID'] == "A") {
							$sorted[] = [
								'Boiler' => $r['Boiler'],
								'rowspan' => 2,
								'BoilerName' => $r['CureSize'],
								'ItemID' => $r['ITEMID_SHIFT1'],
								'ItemName' => $r['ITEMNAME_SHIFT1'],
								'Time' => $r['TIME_SHIFT1'],
								'Target' => $r['TARGET_SHIFT1'],
								'Actual1' => $r['ACTUAL1_SHIFT1'],
								'Actual2' => $r['ACTUAL2_SHIFT1'],
								'Actual' => $r['ACTUAL_SHIFT1'],
								'Weight' => $r['WEIGHT_SHIFT1'],
								//'Employee' => $sch->loademployee($r['Boiler'], $date, $shift),
								'Remark' => $sch->loadremark($r['ID_SHIFT1']),
								'ItemID_D' => $r['ITEMID_SHIFT2'],
								'ItemName_D' => $r['ITEMNAME_SHIFT2'],
								'Time_D' => $r['TIME_SHIFT2'],
								'Target_D' => $r['TARGET_SHIFT2'],
								'Actual1_D' => $r['ACTUAL1_SHIFT2'],
								'Actual2_D' => $r['ACTUAL2_SHIFT2'],
								'Actual_D' => $r['ACTUAL_SHIFT2'],
								'Weight_D' => $r['WEIGHT_SHIFT2'],
								'Remark_D' => $sch->loadremark($r['ID_SHIFT2']),
								'rowspantop' => $r['rowtop'],
								'TotalBoiler' => $r['TotalBoiler']

							];

							$sorted[] = [
								'Boiler' => $r['Boiler'],
								'rowspan' => 2,
								'BoilerName' => $r['CureSize'],
								'ItemID' => "",
								'ItemName' => "",
								'Time' => "",
								'Target' => "",
								'Actual1' => "",
								'Actual2' => "",
								'Actual' => "",
								'Weight' => "",
								//'Employee' => $sch->loademployee($r['Boiler'], $date, $shift),
								'Remark' => "",
								'ItemID_D' => "",
								'ItemName_D' => "",
								'Time_D' => "",
								'Target_D' => "",
								'Actual1_D' => "",
								'Actual2_D' => "",
								'Actual_D' => "",
								'Weight_D' => "",
								'Remark_D' => "",
								'rowspantop' => $r['rowtop'],
								'TotalBoiler' => $r['TotalBoiler']

							];
						}
					}
				}

				if ($r['checkrows'] === 2) {

					if ($r['Boiler'] === $value['Boiler']) {
						$sorted[] = [
							'Boiler' => $r['Boiler'],
							'rowspan' => 2,
							'BoilerName' => $r['CureSize'],
							'ItemID' => $r['ITEMID_SHIFT1'],
							'ItemName' => $r['ITEMNAME_SHIFT1'],
							'Time' => $r['TIME_SHIFT1'],
							'Target' => $r['TARGET_SHIFT1'],
							'Actual1' => $r['ACTUAL1_SHIFT1'],
							'Actual2' => $r['ACTUAL2_SHIFT1'],
							'Actual' => $r['ACTUAL_SHIFT1'],
							'Weight' => $r['WEIGHT_SHIFT1'],
							//'Employee' => $sch->loademployee($r['Boiler'], $date, $shift),
							'Remark' => $sch->loadremark($r['ID_SHIFT1']),
							'ItemID_D' => $r['ITEMID_SHIFT2'],
							'ItemName_D' => $r['ITEMNAME_SHIFT2'],
							'Time_D' => $r['TIME_SHIFT2'],
							'Target_D' => $r['TARGET_SHIFT2'],
							'Actual1_D' => $r['ACTUAL1_SHIFT2'],
							'Actual2_D' => $r['ACTUAL2_SHIFT2'],
							'Actual_D' => $r['ACTUAL_SHIFT2'],
							'Weight_D' => $r['WEIGHT_SHIFT2'],
							'Remark_D' => $sch->loadremark($r['ID_SHIFT2']),
							'rowspantop' => $r['rowtop'],
							'TotalBoiler' => $r['TotalBoiler']

						];
					}
				}

				if ($r['checkrows'] > 2) {

					if ($r['Boiler'] === $value['Boiler']) {
						$sorted[] = [
							'Boiler' => $r['Boiler'],
							'rowspan' => $r['checkrows'],
							'BoilerName' => $r['CureSize'],
							'ItemID' => $r['ITEMID_SHIFT1'],
							'ItemName' => $r['ITEMNAME_SHIFT1'],
							'Time' => $r['TIME_SHIFT1'],
							'Target' => $r['TARGET_SHIFT1'],
							'Actual1' => $r['ACTUAL1_SHIFT1'],
							'Actual2' => $r['ACTUAL2_SHIFT1'],
							'Actual' => $r['ACTUAL_SHIFT1'],
							'Weight' => $r['WEIGHT_SHIFT1'],
							//'Employee' => $sch->loademployee($r['Boiler'], $date, $shift),
							'Remark' => $sch->loadremark($r['ID_SHIFT1']),
							'ItemID_D' => $r['ITEMID_SHIFT2'],
							'ItemName_D' => $r['ITEMNAME_SHIFT2'],
							'Time_D' => $r['TIME_SHIFT2'],
							'Target_D' => $r['TARGET_SHIFT2'],
							'Actual1_D' => $r['ACTUAL1_SHIFT2'],
							'Actual2_D' => $r['ACTUAL2_SHIFT2'],
							'Actual_D' => $r['ACTUAL_SHIFT2'],
							'Weight_D' => $r['WEIGHT_SHIFT2'],
							'Remark_D' => $sch->loadremark($r['ID_SHIFT2']),
							'rowspantop' => $r['rowtop'],
							'TotalBoiler' => $r['TotalBoiler']

						];
					}
				}
				if ($r['checkrows'] === 0 || $r['checkrows'] === "") {
					for ($i = 0; $i < 2; $i++) {
						$sorted[] = [
							'Boiler' => $r['Boiler'],
							'rowspan' => 2,
							'BoilerName' => $r['CureSize'],
							'ItemID' => "",
							'ItemName' => "",
							'Time' => "",
							'Target' => "",
							'Actual1' => "",
							'Actual2' => "",
							'Actual' => "",
							'Weight' => "",
							//'Employee' => "",
							'Remark' => "",
							'Shift' => "",
							'ItemID_D' => "",
							'ItemName_D' => "",
							'Time_D' => "",
							'Target_D' => "",
							'Actual1_D' => "",
							'Actual2_D' => "",
							'Actual_D' => "",
							'Weight_D' => "",
							'Remark_D' => "",
							'rowspantop' => $r['rowtop'],
							'TotalBoiler' => $r['TotalBoiler']

						];
					}
				}
			}
		}

		// echo "<pre>" . print_r($sorted, true) . "</pre>";
		// exit();

		$countBoiler = $sch->countBoilerall($date);
		$countMold   = $sch->countMoldall($date);
		$countBoilerAll =  $sch->countBoileralldata($date);
		$countMoldAll   = $sch->countMoldalldata($date);
		$masterreportsch = $pro->getmasterreportschall($date);

		$sf = (int) $report[0]['ShiftFor'] === 1 ? 'C' : 'D';
		$st = (int) $shift === 1 ? ' (08.00 - 20.00 น.)' : ' (20.00 - 08.00 น.)';
		if ($type == 'pdf') {

			renderView("production_sch/report_schall_pdf", [
				"data" => $sorted,
				"date" => $fulldate,
				"shift" => $sf . $st,
				"countBoiler" => $countBoiler,
				"countMold"   => $countMold,
				"countMoldAll" => $countMoldAll,
				"countBoilerAll"   => $countBoilerAll,
				"masterreportsch" => json_decode($masterreportsch)
			]);
		} else if ($type == 'excel') {

			renderView("production_sch/report_schall_excel", [
				"data" => $sorted,
				"date" => $fulldate,
				"shift" => $sf . $st,
				"countBoiler" => $countBoiler,
				"countMold"   => $countMold,
				"countMoldAll" => $countMoldAll,
				"countBoilerAll"   => $countBoilerAll,
				"masterreportsch" => json_decode($masterreportsch)
			]);
		} else {
			renderView("production_sch/report_schall_excel_rowdata", [
				"data" => $sorted,
				"date" => $fulldate,
				"shift" => $sf . $st,
				"countBoiler" => $countBoiler,
				"countMold"   => $countMold,
				"countMoldAll" => $countMoldAll,
				"countBoilerAll"   => $countBoilerAll,
				"masterreportsch" => json_decode($masterreportsch)
			]);
		}
	}
}
