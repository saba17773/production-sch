<?php

namespace App\V2\ProductionSCH;

use App\V2\ProductionSCH\ProductionSCHAPI;
use App\V2\Helper\Helper;
use App\Components\Utils;
use App\Components\Security;
use App\Components\Authentication;

class ProductionSCHController
{
	public function __construct()
	{
		$this->auth = new Authentication;
		$this->secure = new Security;
	}

	public function sch()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView('production_sch/sch');
	}

	public function approve()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView('production_sch/sch_approve');
	}

	public function mapEmployee()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView('production_sch/map_employee');
	}

	public function masterItem()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView('production_sch/master_item');
	}

	public function masterItemitemGT()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView('production_sch/master_itemGT');
	}
	public function masterItemitemEXT()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView('production_sch/master_itemEXT');
	}
	public function masterItemitemCP()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView('production_sch/master_itemCP');
	}

	public function masterRemark()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView('production_sch/master_remark');
	}

	public function masterTime()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView('production_sch/master_time');
	}

	public function masterScheduler()
	{
		if ($this->secure->isAccess() === false) {
			exit(renderView('page/404'));
		}
		renderView('production_sch/master_scheduler');
	}
	public function insertbuybill()
	{

		renderView('production_sch/insertbuybill');
	}

	public function load()
	{
		$date_sch = filter_input(INPUT_GET, "date_sch");
		$shift 	  = filter_input(INPUT_GET, "shift");

		if (isset($_GET["id"])) {
			$_id = $_GET["id"];
		} else {
			$_id = null;
		}

		$sch = new ProductionSCHAPI;

		$date_sch = date('Y-m-d', strtotime($date_sch));

		$dataTrans = $sch->load($date_sch, $shift, $_id);

		$employee = $sch->loademployeeSch($date_sch, $shift);
		$remark = $sch->loadremarkSch($date_sch, $shift);

		function generateEmployee($id, $employee)
		{
			$e = [];
			foreach ($employee as $value) {
				if ($id == $value['TransID']) {
					array_push($e, $value['FullName']);
				}
			}

			$istext = implode(",", $e);
			return $istext;
		}

		function generateRemark($id, $remark)
		{
			$e = [];
			foreach ($remark as $value) {
				if ($id == $value['TransID']) {
					array_push($e, $value['Remark']);
				}
			}

			$istext = implode(",", $e);
			return $istext;
		}

		foreach ($dataTrans as $value) {
			$sorted[] = (object) [
				'ID' => $value['ID'],
				'FullName' => generateEmployee($value['ID'], $employee),
				'Remark' => generateRemark($value['ID'], $remark),
				'Boiler' => $value['Boiler'],
				'BoilerName' => $value['BoilerName'],
				'Employee' => '',
				'ItemID' => $value['ItemID'],
				'ItemName' => $value['ItemName'],
				'NameTH' => $value['NameTH'],
				'Time' => $value['Time'],
				'Target' => $value['Target'],
				'Actual1' => $value['Actual1'],
				'Actual2' => $value['Actual2'],
				'Actual' => $value['Actual'],
				'Scrap' => $value['Scrap'],
				'Weight' => $value['Weight'],
				'WeightDefault' => $value['WeightDefault'],
				'MoldID' => $value['MoldID'],
				'CurID' => $value['CurID'],
				'Status' => $value['Status'],
				'SchDate' => $value['SchDate'],
				'Shift' => $value['Shift'],
				'BillUse' => $value['BillUse'],
				'BillGive' => $value['BillGive'],
				'faceBoiler' => $value['faceBoiler']
			];
		}

		$sorted = json_encode($sorted);

		return $sorted;
		// echo "<pre>".print_r($sorted,true)."</pre>";
	}

	public function load2()
	{
		$date_sch = filter_input(INPUT_GET, "date_sch");
		$shift 	  = filter_input(INPUT_GET, "shift");
		$sch = new ProductionSCHAPI;

		$date_sch = date('Y-m-d', strtotime($date_sch));

		$dataTrans = $sch->load($date_sch, $shift);

		$employee = $sch->loademployeeSch($date_sch, $shift);
		$remark = $sch->loadremarkSch($date_sch, $shift);

		function generateEmployee($id, $employee)
		{
			$e = [];
			foreach ($employee as $value) {
				if ($id == $value['TransID']) {
					array_push($e, $value['FullName']);
				}
			}

			$istext = implode(",", $e);
			return $istext;
		}

		function generateRemark($id, $remark)
		{
			$e = [];
			foreach ($remark as $value) {
				if ($id == $value['TransID']) {
					array_push($e, $value['Remark']);
				}
			}

			$istext = implode(",", $e);
			return $istext;
		}

		foreach ($dataTrans as $value) {
			$sorted[] = (object) [
				'ID' => $value['ID'],
				'FullName' => generateEmployee($value['ID'], $employee),
				'Remark' => generateRemark($value['ID'], $remark),
				'Boiler' => $value['Boiler'],
				'BoilerName' => $value['BoilerName'],
				'Employee' => '',
				'ItemID' => $value['ItemID'],
				'ItemName' => $value['ItemName'],
				'NameTH' => $value['NameTH'],
				'Time' => $value['Time'],
				'Target' => $value['Target'],
				'Actual1' => $value['Actual1'],
				'Actual2' => $value['Actual2'],
				'Actual' => $value['Actual'],
				'Scrap' => $value['Scrap'],
				'Weight' => $value['Weight'],
				'WeightDefault' => $value['WeightDefault'],
				'MoldID' => $value['MoldID'],
				'CurID' => $value['CurID'],
				'Status' => $value['Status'],
				'SchDate' => $value['SchDate'],
				'Shift' => $value['Shift']
			];
		}

		$sorted = json_encode([
			"draw" => 1,
			"recordsFiltered" => count($sorted),
			"recordsTotal" => count($sorted),
			"data" => $sorted,
		]);

		echo $sorted;
		// echo "<pre>".print_r($sorted,true)."</pre>";
	}

	public function loadisExist()
	{
		$date_sch = filter_input(INPUT_GET, "date_sch");
		$shift 	  = filter_input(INPUT_GET, "shift");
		$sch = new ProductionSCHAPI;

		$date_sch = date('Y-m-d', strtotime($date_sch));
		$dataTrans = $sch->loadisExist($date_sch, $shift);

		$employee = $sch->loademployeeSch($date_sch, $shift);
		$remark = $sch->loadremarkSch($date_sch, $shift);

		function generateEmployee($id, $employee)
		{
			$e = [];
			foreach ($employee as $value) {
				if ($id == $value['TransID']) {
					array_push($e, $value['FullName']);
				}
			}

			$istext = implode(",", $e);
			return $istext;
		}

		function generateRemark($id, $remark)
		{
			$e = [];
			foreach ($remark as $value) {
				if ($id == $value['TransID']) {
					array_push($e, $value['Remark']);
				}
			}

			$istext = implode(",", $e);
			return $istext;
		}

		foreach ($dataTrans as $value) {
			$sorted[] = (object) [
				'ID' => $value['ID'],
				'FullName' => generateEmployee($value['ID'], $employee),
				'Remark' => generateRemark($value['ID'], $remark),
				'Boiler' => $value['Boiler'],
				'BoilerName' => $value['BoilerName'],
				'Employee' => '',
				'ItemID' => $value['ItemID'],
				'NameTH' => $value['NameTH'],
				'Time' => $value['Time'],
				'Target' => $value['Target'],
				'Actual' => $value['Actual'],
				'Scrap' => $value['Scrap'],
				'Weight' => $value['Weight'],
				'MoldID' => $value['MoldID'],
				'CurID' => $value['CurID'],
				'Status' => $value['Status'],
				'SchDate' => $value['SchDate'],
				'Shift' => $value['Shift']
			];
		}

		$sorted = json_encode($sorted);
		echo $sorted;
	}

	public function load_cure()
	{
		$sch = new ProductionSCHAPI;

		echo $sch->load_cure();
	}

	public function loademployee()
	{
		$sch = new ProductionSCHAPI;

		echo $sch->loademployee();
	}

	public function loaditem()
	{
		$boiler = filter_input(INPUT_GET, "boiler");
		$sch = new ProductionSCHAPI;

		echo $sch->loaditem($boiler);
	}
	public function loaditemGT()
	{
		$boiler = filter_input(INPUT_GET, "boiler");
		$sch = new ProductionSCHAPI;

		echo $sch->loaditemGT($boiler);
	}
	public function loaditemEXT()
	{
		$boiler = filter_input(INPUT_GET, "boiler");
		$sch = new ProductionSCHAPI;

		echo $sch->loaditemEXT($boiler);
	}
	public function loaditemCP()
	{
		$boiler = filter_input(INPUT_GET, "boiler");
		$sch = new ProductionSCHAPI;

		echo $sch->loaditemCP($boiler);
	}


	public function loadarms()
	{
		$sch = new ProductionSCHAPI;

		echo $sch->loadarms();
	}

	public function loadremark()
	{
		$sch = new ProductionSCHAPI;

		echo $sch->loadremark();
	}

	public function loadtime()
	{
		$sch = new ProductionSCHAPI;

		echo $sch->loadtime();
	}

	public function getremark()
	{
		$transid 	 = filter_input(INPUT_GET, "transid");

		$sch = new ProductionSCHAPI;

		echo $sch->getremark($transid);
	}

	public function getemployee()
	{
		$transid 	 = filter_input(INPUT_GET, "transid");

		$sch = new ProductionSCHAPI;

		echo $sch->getemployee($transid);
	}

	public function gen()
	{

		$gen_emp   = null;
		$date_emp  = null;
		$shift_emp = null;
		$shift_for = null;

		$date_gen = $_POST['date_gen'];
		$shift_gen 	  =	$_POST['gen_shift'];
		$date_sch = $_POST['date_sch'];
		$shift 	  =	$_POST['shift'];
		$copy 	  = $_POST['copy'];
		$gen_emp   = $_POST['gen_emp'];
		$date_emp  = $_POST['date_emp'];
		$shift_emp = $_POST['shift_emp'];
		$shift_for = $_POST['shift_for'];
		$sch = new ProductionSCHAPI;

		// $date_sch = '2019-06-22';
		// $shift = '1';
		// $copy = '1';
		// $insert = $sch->insertSchTable($date_sch,$shift,$copy);
		// echo $gen_emp;
		// exit();
		// check
		if ($date_gen == $date_sch && $shift_gen == $shift) {
			echo json_encode([
				"result" => 404,
				"message" => "Date is Exist!"
			]);
			exit();
		}

		$checkdata = $sch->checkSchTable($date_sch, $shift);
		// 	if ($checkdata==false) {
		// 		echo json_encode([
		// 	"result" => 400,
		// 	"message"=> "Error : Clear Scheduler!."
		// ]);
		// 	}else{
		// create
		$insert = $sch->insertSchTable($date_sch, $shift, $copy, $date_gen, $shift_gen, $gen_emp, $date_emp, $shift_emp, $shift_for);
		if ($insert == true) {
			echo json_encode([
				"result" => 200,
				"message" => "Generate Successful"
			]);
		} else {
			echo json_encode([
				"result" => 404,
				"message" => "Generate Failed!"
			]);
		}
		// }

	}

	public function addrow()
	{
		$boiler 	= $_POST['boiler'];
		$date_sch 	= date('Y-m-d', strtotime($_POST['date_sch']));
		$shift 		= $_POST['shift'];
		$type 		= $_POST['type'];
		$sch = new ProductionSCHAPI;

		// create
		$insert = $sch->CopySchTable($boiler, $date_sch, $shift, $type);
		if ($insert == true) {
			echo json_encode([
				"result" => 200,
				"message" => "Copy Successful"
			]);
		} else {
			echo json_encode([
				"result" => 404,
				"message" => "Copy Failed!"
			]);
		}
	}

	public function deleterow()
	{
		$id = $_POST['id'];

		$sch = new ProductionSCHAPI;

		// check count rows
		$query = $sch->CountSchTable($id);
		if (count($query) == 1) {
			echo json_encode([
				"result" => 200,
				"message" => "Cannot Delete Boiler Last Record"
			]);
			exit();
		}

		// create
		$delete = $sch->DeleteSchTable($id);
		if ($delete == true) {

			$deleteEmployee = $sch->DeleteEmployee($id);
			echo json_encode([
				"result" => 200,
				"message" => "Delete Successful"
			]);
		} else {
			echo json_encode([
				"result" => 404,
				"message" => "Delete Failed!"
			]);
		}
	}

	public function deleteemployee()
	{
		$id = $_POST['id_trans'];

		$sch = new ProductionSCHAPI;
		$delete = $sch->DeleteEmployee($id);

		if ($delete) {
			echo json_encode([
				"result" => 200,
				"message" => "Delete Successful"
			]);
		} else {
			echo json_encode([
				"result" => 404,
				"message" => "Delete Failed!"
			]);
		}
	}

	public function addemployee()
	{
		$idtrans   = $_POST['id_trans'];
		$boiler 	= $_POST['boiler'];
		$date_sch 	= date('Y-m-d', strtotime($_POST['date_sch']));
		$shift 		= $_POST['shift'];
		$mold 		= $_POST['mold'];
		$code 		= $_POST['code'];
		$sch = new ProductionSCHAPI;

		$mold = substr($mold, 0, 1);
		// echo $mold;
		// exit();
		// create
		$insert = $sch->InsertEmployeeSchTable($idtrans, $boiler, $date_sch, $shift, $mold, $code);
		if ($insert == true) {
			echo json_encode([
				"result" => 200,
				"message" => "InsertEmployee Successful"
			]);
		} else {
			echo json_encode([
				"result" => 404,
				"message" => "InsertEmployee Failed!"
			]);
		}
	}

	public function addremark()
	{
		$transid    = $_POST['transid'];
		$boiler 	= $_POST['boiler'];
		$date_sch 	= date('Y-m-d', strtotime($_POST['date_sch']));
		$shift 		= $_POST['shift'];
		$mold 		= $_POST['mold'];
		$code 		= $_POST['code'];
		$sch = new ProductionSCHAPI;

		// create
		$insert = $sch->InsertRemarkSchTable($transid, $boiler, $date_sch, $shift, $mold, $code);
		if ($insert == true) {
			echo json_encode([
				"result" => 200,
				"message" => "InsertRemark Successful"
			]);
		} else {
			echo json_encode([
				"result" => 404,
				"message" => "InsertRemark Failed!"
			]);
		}
	}

	public function additem()
	{
		$itemid 	= $_POST['itemid'];
		$ratecure 	= $_POST['ratecure'];
		$netweight  = $_POST['netweight'];
		$id 		= $_POST['id'];
		$actual 	= 0;
		$scrap 		= 0;

		$sch = new ProductionSCHAPI;

		// create
		$insert = $sch->InsertItemSchTable($itemid, $ratecure, $netweight, $actual, $scrap, $id);
		if ($insert == true) {
			echo json_encode([
				"result" => 200,
				"message" => "InsertItem Successful"
			]);
		} else {
			echo json_encode([
				"result" => 404,
				"message" => "InsertItem Failed!"
			]);
		}
	}

	public function updatesch()
	{
		$time 	= $_POST['time'];
		$target = $_POST['target'];
		$actual1 = $_POST['actual1'];
		$actual2 = $_POST['actual2'];
		// $actual = $_POST['actual'];
		$scrap 	= $_POST['scrap'];
		// $weight = $_POST['weight'];
		$item = $_POST['item'];
		$arms 	= $_POST['arms'];
		$id 	= $_POST['id'];

		$actual = ($actual1 + $actual2);

		$sch = new ProductionSCHAPI;

		if (empty($scrap)) {
			$scrap = 0;
		}

		if ($scrap > $actual) {
			echo json_encode([
				"result" => 404,
				"message" => "UpdateSchTable Scrap > Actual !"
			]);
			exit();
		}

		// if ($target < ($actual+$scrap)) {
		// 	echo json_encode([
		// 		"result" => 404,
		// 		"message"=> "UpdateSchTable Target < Actual+Scrap !"
		// 	]);
		// 	exit();
		// }

		if ($sch->check_item($item) == false) {
			echo json_encode([
				"result" => 404,
				"message" => "Item is not  found !"
			]);
			exit();
		}

		$data_item = $sch->loaditem_by($item);
		$weight = $data_item[0]['NetWeight'];

		// create
		$insert = $sch->UpdateSchTable($time, $target, $actual1, $actual2, $actual, $scrap, $weight, $arms, $item, $id);
		if ($insert == true) {
			echo json_encode([
				"result" => 200,
				"message" => "UpdateSchTable Successful"
			]);
		} else {
			echo json_encode([
				"result" => 404,
				"message" => "UpdateSchTable Failed!"
			]);
		}
	}

	public function updatesch2()
	{
		$BillUse = $_POST['BillUse'];
		$BillGive = $_POST['BillGive'];
		$faceBoiler = $_POST['faceBoiler'];
		$id 	= $_POST['id'];
		$shift = $_POST['shift'];
		$date_sch = $_POST['date_sch'];

		$sch = new ProductionSCHAPI;
		$date_sch = date('Y-m-d', strtotime($date_sch));





		//create
		//$shift = $_POST['shift'];
		$insert = $sch->UpdateSchTable2($BillUse, $BillGive, $faceBoiler, $date_sch, $shift, $id);
		if ($insert == true) {
			echo json_encode([
				"result" => 200,
				"message" => "UpdateSchTable Successful"
			]);
		} else {
			echo json_encode([
				"result" => 404,
				"message" => "UpdateSchTable Failed!"
			]);
		}
	}

	public function checkdata()
	{
		$date_sch = filter_input(INPUT_GET, "date_sch");
		$shift 	  = filter_input(INPUT_GET, "shift");
		$status   = 1;
		$shiftname = (int) $shift === 1 ? 'กลางวัน (08.00 - 20.00 น.)' : 'กลางคืน (20.00 - 08.00 น.)';

		$sch = new ProductionSCHAPI;
		$checkdata = $sch->checkcompleteSchTable($date_sch, $shift, $status);
		$checkcomplete = $sch->checkcompleteSchTable($date_sch, $shift, 3);

		if ($checkdata == true) {
			echo json_encode([
				"result" => true,
				"status" => 1,
				"message" => "Data is Hasrows"
			]);
		} else {
			if ($checkcomplete == true) {
				echo json_encode([
					"result" => false,
					"status" => 3,
					"message" => "<font color='green'>" . $date_sch . " " . $shiftname . "<br>Scheduler is complete!" . "</font>"
				]);
			} else {
				echo json_encode([
					"result" => false,
					"status" => 1,
					"message" => $date_sch . " " . $shiftname . "<br>Scheduler not Found !"
				]);
			}
		}
	}

	public function checkcomplete()
	{
		$date_sch = filter_input(INPUT_GET, "date_sch");
		$shift 	  = filter_input(INPUT_GET, "shift");
		$status   = 3;

		$sch = new ProductionSCHAPI;
		$checkcomplete = $sch->checkcompleteSchTable($date_sch, $shift, $status);

		if ($checkcomplete == true) {
			echo json_encode([
				"result" => 200,
				"message" => "Status is Completed"
			]);
		} else {
			echo json_encode([
				"result" => 404,
				"message" => "Status is Open"
			]);
		}
	}

	public function complete()
	{
		$date_sch 	= date('Y-m-d', strtotime($_POST['date_sch']));
		$shift 		= $_POST['shift'];
		$sch = new ProductionSCHAPI;

		// create
		$complete = $sch->CompleteSchTable($date_sch, $shift);
		if ($complete == true) {
			echo json_encode([
				"result" => 200,
				"message" => "Completed Successful"
			]);
		} else {
			echo json_encode([
				"result" => 404,
				"message" => "Completed Failed!"
			]);
		}
	}

	public function loaddate()
	{
		$sch = new ProductionSCHAPI;

		echo $sch->loaddate();
	}

	public function listboiler()
	{
		$datesch  = date('Y-m-d', strtotime(filter_input(INPUT_GET, "datesch")));
		$shift 	  = filter_input(INPUT_GET, "shift");

		$sch = new ProductionSCHAPI;
		echo $sch->listboiler($datesch, $shift);
	}

	public function updatelist()
	{
		$id 	= $_POST['id'];
		$sch = new ProductionSCHAPI;

		$update = $sch->UpdateSchTableList($id);
		if ($update == true) {
			echo json_encode([
				"result" => 200,
				"message" => "UpdateSchTable Successful"
			]);
		} else {
			echo json_encode([
				"result" => 404,
				"message" => "UpdateSchTable Failed!"
			]);
		}
	}

	public function updatetime()
	{
		$id 	= $_POST['id'];
		$hours 	= $_POST['hours'];
		$active = $_POST['active'];
		$sch = new ProductionSCHAPI;

		$update = $sch->UpdateTime($id, $hours, $active);
		if ($update == true) {
			echo json_encode([
				"result" => 200,
				"message" => "UpdateTime Successful"
			]);
		} else {
			echo json_encode([
				"result" => 404,
				"message" => "UpdateTime Failed!"
			]);
		}
	}

	public function updateitem()
	{
		$id 	= $_POST['ID'];
		$color1 = $_POST['Color1'];
		$color2 = $_POST['Color2'];
		$color3 = $_POST['Color3'];
		$color4 = $_POST['Color4'];
		$color5 = $_POST['Color5'];
		$sch = new ProductionSCHAPI;

		$update = $sch->UpdateItem($id, $color1, $color2, $color3, $color4, $color5);
		if ($update == true) {
			echo json_encode([
				"result" => 200,
				"message" => "UpdateColor Successful"
			]);
		} else {
			echo json_encode([
				"result" => 404,
				"message" => "UpdateColor Failed!"
			]);
		}
	}

	public function updateitemGT()
	{
		$id 	= $_POST['ID'];
		$color = $_POST['Color'];
		// $color2 = $_POST['Color2'];
		// $color3 = $_POST['Color3'];
		// $color4 = $_POST['Color4'];
		// $color5 = $_POST['Color5'];
		$sch = new ProductionSCHAPI;

		$update = $sch->UpdateItemGT($id, $color);
		if ($update == true) {
			echo json_encode([
				"result" => 200,
				"message" => "UpdateColor Successful"
			]);
		} else {
			echo json_encode([
				"result" => 404,
				"message" => "UpdateColor Failed!"
			]);
		}
	}

	public function updateremark()
	{
		$id 	= $_POST['id'];
		$name 	= $_POST['name'];
		$sch = new ProductionSCHAPI;

		$update = $sch->UpdateRemark($id, $name);
		if ($update == true) {
			echo json_encode([
				"result" => 200,
				"message" => "UpdateProblem Successful"
			]);
		} else {
			echo json_encode([
				"result" => 404,
				"message" => "UpdateProblem Failed!"
			]);
		}
	}

	public function createremark()
	{
		$name 	= $_POST['txt_remark'];
		$sch = new ProductionSCHAPI;

		$create = $sch->CreateRemark($name);
		if ($create == true) {
			echo json_encode([
				"result" => 200,
				"message" => "CreateProblem Successful"
			]);
		} else {
			echo json_encode([
				"result" => 404,
				"message" => "CreateProblem Failed!"
			]);
		}
	}

	public function deleteremark()
	{
		$id 		= $_POST['id'];
		$problemid 	= $_POST['problemid'];
		$sch = new ProductionSCHAPI;

		return $sch->DeleteRemark($id, $problemid);
	}

	public function getmasterreportsch()
	{
		$date 	 = filter_input(INPUT_GET, "date");
		$shift 	 = filter_input(INPUT_GET, "shift");
		$sch = new ProductionSCHAPI;

		echo $sch->getmasterreportsch($date, $shift);
	}

	public function createmastersch()
	{
		$Senior 		= $_POST['Senior'];
		$SectionHead 	= $_POST['SectionHead'];
		$EmpBladder 	= $_POST['EmpBladder'];
		$EmpCuringBack 	= $_POST['EmpCuringBack'];
		$Auditor 		= $_POST['Auditor'];
		$EmpMantain 	= $_POST['EmpMantain'];
		$EmpCuring 		= $_POST['EmpCuring'];
		$EmpCutting 	= $_POST['EmpCutting'];
		$EmpWarehoure 	= $_POST['EmpWarehoure'];
		$EmpWorking 	= $_POST['EmpWorking'];
		$EmpSummer 		= $_POST['EmpSummer'];
		$EmpSeak 		= $_POST['EmpSeak'];
		$EmpLeave 		= $_POST['EmpLeave'];
		$EmpNoInfo 		= $_POST['EmpNoInfo'];
		$SCHDate 		= $_POST['schdate_'];
		$Shift 			= $_POST['shift_'];
		$Remark 		= $_POST['Remark'];

		$sch = new ProductionSCHAPI;

		return $sch->createmastersch(
			$Senior,
			$SectionHead,
			$EmpBladder,
			$EmpCuringBack,
			$Auditor,
			$EmpMantain,
			$EmpCuring,
			$EmpCutting,
			$EmpWarehoure,
			$EmpWorking,
			$EmpSummer,
			$EmpSeak,
			$EmpLeave,
			$EmpNoInfo,
			$SCHDate,
			$Shift,
			$Remark
		);
	}

	public function SyncEmployee()
	{
		$company = "DSL";
		$sch = new ProductionSCHAPI;
		return $sch->SyncEmployee($company);
	}

	public function sendmail()
	{
		$date_sch 	= date('d/m/Y', strtotime($_POST['date_sch']));
		$shift 		= $_POST['shift'];
		if ($shift == 1) {
			$shiftname = "กะกลางวัน";
		} else {
			$shiftname = "กะกลางคืน";
		}
		$sch = new ProductionSCHAPI;

		$mailTo = [
			'wiriya_y@deestone.com',
			'harit_j@deestone.com'
		];

		$mailCC = [];

		$BCC = [];

		$sender = "ea_webmaster@deestone.com";

		$subject = "[TEST] ใบรายงานการผลิต";
		// $link = "<a href='http://192.168.90.27:3212/production_sch/sch'>Link</a>";
		$txt  = "";
		$txt .= "<b>ใบรายงานการผลิต</b><br>";
		$txt .= "วันที่ &nbsp;: " . $date_sch . "&nbsp;&nbsp;&nbsp;กะการทำงาน : " . $shiftname . "<br>";
		$txt .= "อนุมัติ : " . $link;
		$body = $txt;
		if ($sch->SendMail($mailTo, $mailCC, $BCC, $subject, $body, $sender) === true) {
			echo json_encode([
				"result" => 200,
				"message" => "Sendmail Successful"
			]);
		} else {
			echo json_encode([
				"result" => 404,
				"message" => "Sendmail Failed!"
			]);
		}
	}

	public function clearList()
	{
		$id 	= $_POST['id'];
		$sch = new ProductionSCHAPI;

		$update = $sch->ClearList($id);
		if ($update == true) {
			echo json_encode([
				"result" => 200,
				"message" => "ClearData Successful"
			]);
		} else {
			echo json_encode([
				"result" => 404,
				"message" => "ClearData Failed!"
			]);
		}
	}

	public function deleteremarkById()
	{
		$id 		= $_POST['transid'];
		$sch = new ProductionSCHAPI;

		return $sch->DeleteRemarkId($id);
	}

	public function deleteemployeeById()
	{
		$id 		= $_POST['transid'];
		$sch = new ProductionSCHAPI;

		return $sch->DeleteEmployeeId($id);
	}

	public function confirmSch()
	{
		$date = $_POST['date'];
		$shift = $_POST['shift'];
		$status = $_POST['status'];

		$sch = new ProductionSCHAPI;

		return $sch->confirmSch($date, $shift, $status);
	}

	public function checkconfirmSch()
	{
		$date = $_GET['date'];
		$shift = $_GET['shift'];

		$sch = new ProductionSCHAPI;

		return $sch->checkconfirmSch($date, $shift);
	}

	public function edititem()
	{


		$itemGT = $_POST['itemGT'];
		$Timcure     =  $_POST['Timcure'];
		$GroupItem = $_POST['GroupItem'];
		$counprint     =  $_POST['counprint'];
		$FGItem = $_POST['FGItem'];
		$sch = new ProductionSCHAPI;


		$insert = $sch->insertedititem($itemGT, $Timcure, $GroupItem, $counprint, $FGItem);
		if ($insert == true) {
			echo json_encode([
				"result" => 200,
				"message" => "Generate Successful"
			]);
		} else {
			echo json_encode([
				"result" => 404,
				"message" => "Generate Failed!"
			]);
		}
	}
}
