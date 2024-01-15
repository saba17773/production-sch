<?php

namespace App\V2\TargetGreentire;

use App\V2\TargetGreentire\TargetGreentireAPI;
use App\V2\ProductionSCH\ProductionSCHAPI;
use App\Common\Datatables;

class TargetGreentireController
{
  private $targetGreentireApi = null;
  private $ProductionSCHAPI = null;
  private $datatables = null;

  public function __construct()
  {
    $this->targetGreentireApi = new TargetGreentireAPI();
    $this->ProductionSCHAPI = new ProductionSCHAPI();
    $this->datatables = new Datatables();
  }

  public function index()
  {
    renderView("production_sch2/main");
  }

  public function getGreentireLists()
  {
    try {

      $data = $this->targetGreentireApi->getGreentireLists();
      $pack = $this->datatables->get($data, $_POST);
      echo json_encode($pack);
    } catch (\Exception $e) {
      echo \json_encode($e->getMessage());
    }
  }

  public function addShiftTrans()
  {
    try {
      $date = $_POST["Date"];
      $shift = $_POST["Shift"];
      $module = $_POST["Module"];

      $isCreated = $this->targetGreentireApi->isCreated($date, $shift, $module);

      if ($isCreated["result"] === true) {
        throw new \Exception("Transaction is already exists.");
      }

      $result = $this->targetGreentireApi->addShiftTrans($date, $shift, $module);

      return json_encode($result);
    } catch (\Exception $e) {
      return json_encode(response(false, $e->getMessage()));
    }
  }

  public function getShiftTrans()
  {
    try {
      // code

      $filter = [
        "Shift" => "SM.[Description]",
        "CreateBy" => "U.Name",
        "Module" => "M.[Description]"
      ];

      $data = $this->targetGreentireApi->getShiftTrans($this->datatables->filter($_POST, $filter));
      $pack = $this->datatables->get($data, $_POST);
      return json_encode($pack);
    } catch (\Exception $e) {
      // exception
      return json_encode(response(false, $e->getMessage()));
    }
  }

  public function getShiftTransById()
  {
    try {
      // code
      $id = $_POST["id"];
      $data = $this->targetGreentireApi->getShiftTransById($id);
      return json_encode($data);
    } catch (\Exception $e) {
      return json_encode(response(false, $e->getMessage()));
    }
  }

  public function loadModule()
  {
    try {

      $shift = $_POST["_shift"];
      $date = $_POST["_date"];
      $module = $_POST["_module"];
      $transId = $_POST["_transId"];

      if ((int) $module === 1) {
        $this->targetGreentireApi->addTargetGreentire($shift, $date, $transId);
        renderView("production_sch2/target_greentire", [
          "transId" => $transId,
          "date" => $date,
          "shift" => $shift
        ]);
      }
    } catch (\Exception $e) {
      return json_encode(response(false, $e->getMessage()));
    }
  }

  public function loadTargetGreentire($shiftDate)
  {
    try {
      $data = $this->targetGreentireApi->loadTargetGreentire($shiftDate);
      $pack = $this->datatables->get($data, $_POST);
      echo json_encode($pack);
    } catch (\Exception $e) {
      return $e->getMessage();
    }
  }

  public function update()
  {
    try {
      // code
      $result = $this->targetGreentireApi->update(
        $_POST['name'],
        $_POST['pk'],
        $_POST['value']
      );

      return json_encode(response(true, $result));
    } catch (\Exception $e) {
      return $e->getMessage();
    }
  }

  public function cancel()
  {
    try {
      // code
      $result = $this->targetGreentireApi->cancel(
        $_POST['id']
      );

      return json_encode($result);
    } catch (\Exception $e) {
      return response(false, $e->getMessage());
    }
  }

  public function delete()
  {
    try {
      // code
      $result = $this->targetGreentireApi->delete(
        $_POST['id']
      );

      return json_encode($result);
    } catch (\Exception $e) {
      return $e->getMessage();
    }
  }

  public function add()
  {
    try {
      // code
      $result = $this->targetGreentireApi->add(
        $_POST["shift_id"],
        $_POST["greentire_id"],
        $_POST["bomo_c_plan"],
        $_POST["bomo_d_plan"],
        $_POST["weight_plan"],
        $_POST["shift_date"]
      );

      return json_encode($result);
    } catch (\Exception $e) {
      return $e->getMessage();
    }
  }

  public function getGreentireMaster()
  {
    try {

      $data = $this->targetGreentireApi->getGreentireMaster($this->datatables->filter($_POST));
      $pack = $this->datatables->get($data, $_POST);
      echo json_encode($pack);
    } catch (\Exception $e) {
      echo \json_encode($e->getMessage());
    }
  }

  public function report($transId, $shiftDate)
  {
    try {
      $this->targetGreentireApi->addTargetGreentire(null, $shiftDate, $transId);
      $data = $this->targetGreentireApi->loadTargetGreentire($shiftDate);
      return renderView("production_sch2/target_greentire_report", [
        "data" => $data,
        "id" => $transId,
        "date" => $shiftDate
      ]);
    } catch (\Exception $e) {
      return $e->getMessage();
    }
  }

  public function reportExcel($transId, $shiftDate)
  {
    try {
      $this->targetGreentireApi->addTargetGreentire(null, $shiftDate, $transId);
      $data = $this->targetGreentireApi->loadTargetGreentire($shiftDate);
      return renderView("production_sch2/target_greentire_report_excel", [
        "data" => $data,
        "id" => $transId,
        "date" => $shiftDate
      ]);
    } catch (\Exception $e) {
      return $e->getMessage();
    }
  }

  public function receivesch() {

    renderView('production_sch2/receivesch');

  }
  public function greentrieprim() {

    renderView('production_sch2/actprintcount');

  }
  public function load()
  {
		$date_sch = filter_input(INPUT_GET, "date_sch");
		$shift 	  = filter_input(INPUT_GET, "shift");
    $date_sch = date('Y-m-d', strtotime($date_sch));
    $dataTrans = $this->targetGreentireApi->load($date_sch,$shift);
    foreach ($dataTrans as $value) {
			$sorted[] = (object) [
				'ID' => $value['Id'],
		    'ItemID' => $value['ItemId'],
				'ItemName' => $value['ItemGTName'],
        'Color' => $value['Color'],
				'SpareOfcure' => $value['SpareOfcure'],
				'StockInplan' => $value['StockInplan'],
				 'TOTAL' => $value['TOTAL'],
				'CountIn' => $value['CountIn'],
				 'CountOut' => $value['CountOut'],
				 'CountNotSpec' => $value['CountNotSpec'],
				'CountReal' => $value['CountReal'],
				 'CountShift' => $value['CountShift'],
				 'TotalSockGT' => $value['TotalSockGT'],
				 'CountCure' => $value['CountCure'],
				 'CheckCountShift' => $value['CheckCountShift'],
				 'CheckCountOut' => $value['CheckCountOut'],
				 'Chekdata' => $value['Chekdata'],
				 'CountPlan' => $value['CountPlan']
       ];
    }
      $sorted = json_encode($sorted);
		  echo $sorted;
	}

  public function gen() {

    $gen_emp   = null;
    $date_emp  = null;
    $shift_emp = null;
    $date_gen = $_POST['date_gen'];
    $shift_gen 	  =	$_POST['gen_shift'];
    $date_sch = $_POST['date_sch'];
    $shift 	  =	$_POST['shift'];
    $copy 	  = $_POST['copy'];
    $gen_emp   = $_POST['gen_emp'];
    $date_emp  = $_POST['date_emp'];
    $shift_emp = $_POST['shift_emp'];
    $date_sch = date('Y-m-d', strtotime($date_sch));
    $date_gen = date('Y-m-d', strtotime($date_gen));
    $insert = $this->targetGreentireApi->insertSchTable($date_sch,$shift,$copy,$date_gen,$shift_gen,$gen_emp,$date_emp,$shift_emp);
      if ($insert==true) {
        echo json_encode([
          "result" => 200,
          "message"=> "Generate Successful"
        ]);
      }else{
        echo json_encode([
          "result" => 404,
          "message"=> "Generate Failed!"
        ]);
      }
  }

  public function updateschrecive() {
		$CountIn 	= $_POST['CountIn'];
		$CountOut = $_POST['CountOut'];
		$CountNotSpec = $_POST['CountNotSpec'];
		$CountReal = $_POST['CountReal'];
		$id 	= $_POST['id'];
	  $insert = $this->targetGreentireApi->UpdateSchReiveTable($CountIn,$CountOut,$CountNotSpec,$CountReal,$id);
  		if ($insert==true) {
  			echo json_encode([
  				"result" => 200,
  				"message"=> "UpdateSchTable Successful"
  			]);
  		}else{
  			echo json_encode([
  				"result" => 404,
  				"message"=> "no save"
  			]);
  		}
  }
  public function loaditem(){
    $test =  $this->targetGreentireApi->loaditem();
    foreach ($test as $value) {
			$sorted[] = (object) [
      'ItemGT' => $value['ItemGT'],
			'ItemGTName' => $value['ItemGTName']
      ];
		}
    $sorted = json_encode($sorted);
		echo $sorted;
  }

  public function additem() {

    $itemid 	= $_POST['itemid'];
    $id 		= $_POST['id'];
    $updateItemgremtire = $this->targetGreentireApi->InsertItemGreentireTable($itemid,$id);
    if ($updateItemgremtire==true) {
      echo json_encode([
        "result" => 200,
        "message"=> "Update Successful"
      ]);
    }else{
      echo json_encode([
        "result" => 404,
        "message"=> "Update Failed!"
      ]);
    }
  }
  public function deleterow() {
    $id = $_POST['id'];
    $delete =  $this->targetGreentireApi->DeleteSchTable($id);
      if ($delete==true) {
        echo json_encode([
          "result" => 200,
          "message"=> "Delete Successful"
        ]);
      }else{
        echo json_encode([
          "result" => 404,
          "message"=> "Delete Failed!"
        ]);
      }
  }
  public function loadprint()
  {
		$date_sch = filter_input(INPUT_GET, "date_sch");
		$shift 	  = filter_input(INPUT_GET, "shift");
    $date_sch = date('Y-m-d', strtotime($date_sch));
    $dataTrans = $this->targetGreentireApi->loadprint($date_sch,$shift);
      foreach ($dataTrans as $value) {
        $sorted[] = (object) [
          'ID' => $value['Id'],
          'ItemId' => $value['ItemId'],
          'ItemGTName' => $value['ItemGTName'],
          'Color' => $value['Color'],
          'SumPrint' => $value['SumPrint'],
          'TotalPrint' => $value['TotalPrint'],
          'Time' => $value['Time'],
          'Countprintcure' => $value['Countprintcure'],
          'Rateprint' => $value['Rateprint'],
          'TimeCureFG' => $value['TimeCureFG'],
          'CountPrintcurFG' => $value['CountPrintcurFG'],
          'RatePrintFG' => $value['RatePrintFG'],
          'GreentireShift' => $value['GreentireShift'],
          'GreentireDay' => $value['GreentireDay'],
          'CountCure' => $value['CountCure'],
          'SpareOfcure' => $value['SpareOfcure'],
          'StockInplan' => $value['StockInplan'],
          'Total' => $value['Total'],
          'TotalHours' => $value['TotalHours'],
          'PersenGreentire' => $value['PersenGreentire'],
          'LackShift' => $value['LackShift'],
          'TargetTemp' => $value['TargetTemp'],
          'OrderLackshift' => $value['OrderLackshift'],
          'LackShift2' => $value['LackShift2'],
          'CountPrint' => $value['CountPrint'],
          'CureDay' => $value['CureDay'],
          'BL' => $value['BL'],
          'Actual' => $value['Actual'],
          'TireLackShift' => $value['TireLackShift'],
          'TireLackDay' => $value['TireLackDay']

        ];
      }
        $sorted = json_encode($sorted);
        echo $sorted;
      //  var_dump($sorted);
  }

  public function checkdata() {
		$date_sch = filter_input(INPUT_GET, "date_sch");
		$shift 	  = filter_input(INPUT_GET, "shift");
		$status   = 1;
		$shiftname= (int)$shift === 1 ? 'กลางวัน (08.00 - 20.00 น.)': 'กลางคืน (20.00 - 08.00 น.)';


		$checkdata = $this->targetGreentireApi->checkcompleteSchReciveTable($date_sch,$shift);


	  	if ($checkdata==true) {
	  		echo json_encode([
				"result" => true,
				"status" => 1,
				"message"=> "Data is Hasrows"
			]);
	  	}else{
	  		if ($checkcomplete==true) {
	  			echo json_encode([
					"result" => false,
					"status" => 3,
					"message"=> "<font color='green'>".$date_sch." ".$shiftname."<br>Scheduler is complete!"."</font>"
				]);
	  		}else{
		  		echo json_encode([
					"result" => false,
					"status" => 1,
					"message"=> $date_sch." ".$shiftname."<br>Scheduler not Found !"
				]);
			}
	  	}
	}

  public function genprint() {

    $gen_emp   = null;
    $date_emp  = null;
    $shift_emp = null;
    $date_gen = $_POST['date_gen'];
    $shift_gen 	  =	$_POST['gen_shift'];
    $date_sch = $_POST['date_sch'];
    $shift 	  =	$_POST['shift'];
    $copy 	  = $_POST['copy'];
    $gen_emp   = $_POST['gen_emp'];
    $date_emp  = $_POST['date_emp'];
    $shift_emp = $_POST['shift_emp'];
    $date_sch = date('Y-m-d', strtotime($date_sch));
    $date_gen = date('Y-m-d', strtotime($date_gen));
    $insert = $this->targetGreentireApi->insertSchPrintTable($date_sch,$shift,$copy,$date_gen,$shift_gen,$gen_emp,$date_emp,$shift_emp);
      if ($insert==true) {
        echo json_encode([
          "result" => 200,
          "message"=> "Generate Successful"
        ]);
      }else{
        echo json_encode([
          "result" => 404,
          "message"=> "Generate Failed!"
        ]);
      }
  }

  public function additemprint() {

    $itemid 	= $_POST['itemid'];
    $id 		= $_POST['id'];
    $sch_date		= $_POST['sch_date'];
    $shift 		= $_POST['shift'];
    //var_dump($sch_date); exit();
    $updateItemgremtire = $this->targetGreentireApi->InsertItemGreentirePrintTable($itemid,$id,$sch_date,$shift);
    if ($updateItemgremtire==true) {
      echo json_encode([
        "result" => 200,
        "message"=> "Update Successful"
      ]);
    }else{
      echo json_encode([
        "result" => 404,
        "message"=> "Update Failed!"
      ]);
    }
  }

  public function updateschprint() {
    $SumPrint         = $_POST['SumPrint'];
    $Countprintcure   = $_POST['Countprintcure'];
    $Rateprint        = $_POST['Rateprint'];
    $CountPrintcurFG  = $_POST['CountPrintcurFG'];
    $RatePrintFG      = $_POST['RatePrintFG'];
    $CountCure        = $_POST['CountCure'];
    $SpareOfcure      =	$_POST['SpareOfcure'];
    $id               = $_POST['id'];
    $insert = $this->targetGreentireApi->UpdateSchprintable($SumPrint ,$Countprintcure ,$Rateprint,$CountPrintcurFG ,$RatePrintFG ,$CountCure ,$SpareOfcure,$id);
  		if ($insert==true) {
  			echo json_encode([
  				"result" => 200,
  				"message"=> "UpdateSchTable Successful"
  			]);
  		}else{
  			echo json_encode([
  				"result" => 404,
  				"message"=> "no save"
  			]);
  		}
  }

}
