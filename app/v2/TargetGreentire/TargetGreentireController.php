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
        $data = $this->targetGreentireApi->shiftwork($shift);
        $shiftcheck = $data[0]["ShiftFor"];
        $sf = (int) $shiftcheck === 1 ? 'C' : 'D';
        $st =  ' (08.00 - 20.00 น.)';
        $sf1 = (int) $shiftcheck === 1 ? 'D' : 'C';
        $st1 = ' (20.00 - 08.00 น.)';
        // print_r($sf1 . $st1);
        // exit();
        renderView("production_sch2/target_greentire", [
          "transId" => $transId,
          "date" => $date,
          "shift" => $shift,
          "shift1" => $sf . $st,
          "shift2" => $sf1 . $st1

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
      $data1 = $this->targetGreentireApi->shiftwork($shiftDate);
      $shiftcheck = $data1[0]["ShiftFor"];
      $sf = (int) $shiftcheck === 1 ? 'C' : 'D';
      $st = ' (08.00 - 20.00 น.)';
      $sf1 = (int) $shiftcheck === 1 ? 'D' : 'C';
      $st1 = ' (20.00 - 08.00 น.)';
      return renderView("production_sch2/target_greentire_report", [
        "data" => $data,
        "id" => $transId,
        "date" => $shiftDate,
        "shift1" => $sf . '<BR>' . $st,
        "shift2" => $sf1 . '<BR>' . $st1
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
      $data1 = $this->targetGreentireApi->shiftwork($shiftDate);
      $shiftcheck = $data1[0]["ShiftFor"];
      $sf = (int) $shiftcheck === 1 ? 'C' : 'D';
      $st = ' (08.00 - 20.00 น.)';
      $sf1 = (int) $shiftcheck === 1 ? 'D' : 'C';
      $st1 = ' (20.00 - 08.00 น.)';
      return renderView("production_sch2/target_greentire_report_excel", [
        "data" => $data,
        "id" => $transId,
        "date" => $shiftDate,
        "shift1" => $sf . '<BR>' . $st,
        "shift2" => $sf1 . '<BR>' . $st1
      ]);
    } catch (\Exception $e) {
      return $e->getMessage();
    }
  }

  public function receivesch()
  {

    renderView('production_sch2/receivesch');
  }
  public function tirebill()
  {

    renderView('production_sch2/disbursementtire');
  }
  public function greentrieprim()
  {

    renderView('production_sch2/actprintcount');
  }
  public function insertcar()
  {

    renderView('production_sch2/InsertCar');
  }
  public function facetireproduct()
  {

    renderView('production_sch2/facetireproduct');
  }

  public function plantireproduct()
  {

    renderView('production_sch2/plantireproduct');
  }

  public function insertcfacetirecar()
  {

    renderView('production_sch2/InsertfaceCar');
  }

  public function ordersumaryOfmount()
  {

    renderView('production_sch2/ordersumaryOfmount');
  }
  public function load()
  {
    $date_sch = filter_input(INPUT_GET, "date_sch");
    $shift     = filter_input(INPUT_GET, "shift");
    $date_sch = date('Y-m-d', strtotime($date_sch));
    $dataTrans = $this->targetGreentireApi->load($date_sch, $shift);
    foreach ($dataTrans as $value) {
      $sorted[] = (object) [
        'ID' => $value['Id'],
        'ItemID' => $value['ItemId'],
        'ItemName' => $value['ItemGTName'],
        'Color' => $value['Color'],
        'SpareOfcure' => $value['SpareOfcure'],
        'StockInplan' => $value['StockInplan'],
        'StockInplan2' => $value['StockInplan2'],
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
        'CountPlan' => $value['CountPlan'],
        'CalStock' => $value['CalStock']
      ];
    }
    $sorted = json_encode($sorted);
    echo $sorted;
  }

  public function gen()
  {

    $gen_emp   = null;
    $date_emp  = null;
    $shift_emp = null;
    $date_gen = $_POST['date_gen'];
    $shift_gen     =  $_POST['gen_shift'];
    $date_sch = $_POST['date_sch'];
    $shift     =  $_POST['shift'];
    $copy     = $_POST['copy'];
    $gen_emp   = $_POST['gen_emp'];
    $date_emp  = $_POST['date_emp'];
    $shift_emp = $_POST['shift_emp'];
    $date_sch = date('Y-m-d', strtotime($date_sch));
    $date_gen = date('Y-m-d', strtotime($date_gen));
    $insert = $this->targetGreentireApi->insertSchTable($date_sch, $shift, $copy, $date_gen, $shift_gen, $gen_emp, $date_emp, $shift_emp);
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

  public function updateschrecive()
  {
    $CountIn   = $_POST['CountIn'];
    $CountOut = $_POST['CountOut'];
    $CountNotSpec = $_POST['CountNotSpec'];
    $CountReal = $_POST['CountReal'];
    $CalStock = $_POST['CalStock'];
    $id   = $_POST['id'];
    $date_sch = $_POST['date_sch'];
    $shift = $_POST['shift'];
    $date_sch = date('Y-m-d', strtotime($date_sch));
    $insert = $this->targetGreentireApi->UpdateSchReiveTable($CountIn, $CountOut, $CountNotSpec, $CountReal, $id, $date_sch, $shift, $CalStock);
    if ($insert == true) {
      echo json_encode([
        "result" => 200,
        "message" => "UpdateSchTable Successful"
      ]);
    } else {
      echo json_encode([
        "result" => 404,
        "message" => "no save"
      ]);
    }
  }
  public function loaditem()
  {
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
  public function loaditemEXT()
  {
    $test =  $this->targetGreentireApi->loaditemEXT();
    foreach ($test as $value) {
      $sorted[] = (object) [
        'ItemGT' => $value['ItemBOM'],
        'ItemGTName' => $value['ITEMNAME']
      ];
    }
    $sorted = json_encode($sorted);
    echo $sorted;
  }

  public function additem()
  {

    $itemid   = $_POST['itemid'];
    $id     = $_POST['id'];
    $updateItemgremtire = $this->targetGreentireApi->InsertItemGreentireTable($itemid, $id);
    if ($updateItemgremtire == true) {
      echo json_encode([
        "result" => 200,
        "message" => "Update Successful"
      ]);
    } else {
      echo json_encode([
        "result" => 404,
        "message" => "Update Failed!"
      ]);
    }
  }
  public function deleterow()
  {
    $id = $_POST['id'];
    $delete =  $this->targetGreentireApi->DeleteSchTable($id);
    if ($delete == true) {
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
  public function loadprint()
  {
    $date_sch = filter_input(INPUT_GET, "date_sch");
    $shift     = filter_input(INPUT_GET, "shift");
    $date_sch = date('Y-m-d', strtotime($date_sch));
    $dataTrans = $this->targetGreentireApi->loadprint($date_sch, $shift);
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
        'TireLackDay' => $value['TireLackDay'],
        'TotalShareMold' => $value['TotalShareMold'],
        'GroupId' => $value['GroupId']

      ];
    }
    $sorted = json_encode($sorted);
    echo $sorted;
    //  var_dump($sorted);
  }

  public function checkdata()
  {
    $date_sch = filter_input(INPUT_GET, "date_sch");
    $shift     = filter_input(INPUT_GET, "shift");
    $status   = 1;
    $shiftname = (int) $shift === 1 ? 'กลางวัน (08.00 - 20.00 น.)' : 'กลางคืน (20.00 - 08.00 น.)';


    $checkdata = $this->targetGreentireApi->checkcompleteSchReciveTable($date_sch, $shift);


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

  public function genprint()
  {

    $gen_emp   = null;
    $date_emp  = null;
    $shift_emp = null;
    $date_gen = $_POST['date_gen'];
    $shift_gen     =  $_POST['gen_shift'];
    $date_sch = $_POST['date_sch'];
    $shift     =  $_POST['shift'];
    $copy     = $_POST['copy'];
    $gen_emp   = $_POST['gen_emp'];
    $date_emp  = $_POST['date_emp'];
    $shift_emp = $_POST['shift_emp'];
    $date_sch = date('Y-m-d', strtotime($date_sch));
    $date_gen = date('Y-m-d', strtotime($date_gen));

    if ($shift_gen == 1) {
      $date_gen2 = date('Y-m-d', strtotime($date_gen));
      $shift_gen2 = 2;
    } else {

      $date_gen2 = date('Y-m-d', strtotime($date_gen . '+1 day'));
      $shift_gen2 = 1;
    }

    $insert = $this->targetGreentireApi->insertSchPrintTable($date_sch, $shift, $copy, $date_gen, $shift_gen, $gen_emp, $date_emp, $shift_emp, $date_gen2, $shift_gen2);
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

  public function additemprint()
  {

    $itemid   = $_POST['itemid'];
    $id     = $_POST['id'];
    $sch_date    = $_POST['sch_date'];
    $shift     = $_POST['shift'];
    //var_dump($sch_date); exit();
    $updateItemgremtire = $this->targetGreentireApi->InsertItemGreentirePrintTable($itemid, $id, $sch_date, $shift);
    if ($updateItemgremtire == true) {
      echo json_encode([
        "result" => 200,
        "message" => "Update Successful"
      ]);
    } else {
      echo json_encode([
        "result" => 404,
        "message" => "Update Failed!"
      ]);
    }
  }

  public function updateschprint()
  {
    $SumPrint         = $_POST['SumPrint'];
    $Countprintcure   = $_POST['Countprintcure'];
    $Rateprint        = $_POST['Rateprint'];
    $CountPrintcurFG  = $_POST['CountPrintcurFG'];
    $RatePrintFG      = $_POST['RatePrintFG'];
    $CountCure        = $_POST['CountCure'];
    $SpareOfcure      =  $_POST['SpareOfcure'];
    $id               = $_POST['id'];
    $insert = $this->targetGreentireApi->UpdateSchprintable($SumPrint, $Countprintcure, $Rateprint, $CountPrintcurFG, $RatePrintFG, $CountCure, $SpareOfcure, $id);
    if ($insert == true) {
      echo json_encode([
        "result" => 200,
        "message" => "UpdateSchTable Successful"
      ]);
    } else {
      echo json_encode([
        "result" => 404,
        "message" => "no save"
      ]);
    }
  }

  public function deleterowprint()
  {
    $id = $_POST['id'];
    $delete =  $this->targetGreentireApi->DeleteSchprintTable($id);
    if ($delete == true) {
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

  public function checkdataprint()
  {
    $date_sch = filter_input(INPUT_GET, "date_sch");
    $shift     = filter_input(INPUT_GET, "shift");
    $status   = 1;
    $shiftname = (int) $shift === 1 ? 'กลางวัน (08.00 - 20.00 น.)' : 'กลางคืน (20.00 - 08.00 น.)';


    $checkdata = $this->targetGreentireApi->checkcompleteSchReciveprintTable($date_sch, $shift);


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

  public function gentire()
  {

    $gen_emp   = null;
    $date_emp  = null;
    $shift_emp = null;
    $date_gen = $_POST['date_gen'];
    $shift_gen     =  $_POST['gen_shift'];
    $date_sch = $_POST['date_sch'];
    $shift     =  $_POST['shift'];
    $copy     = $_POST['copy'];
    $gen_emp   = $_POST['gen_emp'];
    $date_emp  = $_POST['date_emp'];
    $shift_emp = $_POST['shift_emp'];
    $date_sch = date('Y-m-d', strtotime($date_sch));
    $date_gen = date('Y-m-d', strtotime($date_gen));
    $insert = $this->targetGreentireApi->insertSchtireTable($date_sch, $shift, $copy, $date_gen, $shift_gen, $gen_emp, $date_emp, $shift_emp);
    if ($insert == true) {
      echo json_encode([
        "result" => 200,
        "message" => "Generate Successful"
      ]);
    } else {
      echo json_encode([
        "result" => 404,
        "message" => $date_sch
      ]);
    }
  }

  public function updateSchtireTable()
  {


    $date_sch = $_POST['date_sch'];
    $shift     =  $_POST['shift'];
    $date_sch = date('Y-m-d', strtotime($date_sch));

    $insert = $this->targetGreentireApi->updateSchtireTable($date_sch, $shift);
    if ($insert == true) {
      echo json_encode([
        "result" => 200,
        "message" => "Generate Successful"
      ]);
    } else {
      echo json_encode([
        "result" => 404,
        "message" => $date_sch
      ]);
    }
  }

  public function updateSchtireTableStock()
  {


    $date_sch = $_POST['date_sch'];
    $shift     =  $_POST['shift'];
    $date_sch = date('Y-m-d', strtotime($date_sch));

    $insert = $this->targetGreentireApi->updateSchtireTableStock($date_sch, $shift);
    if ($insert == true) {
      echo json_encode([
        "result" => 200,
        "message" => "Generate Successful"
      ]);
    } else {
      echo json_encode([
        "result" => 404,
        "message" => $date_sch
      ]);
    }
  }


  public function loadtire()
  {
    $date_sch = filter_input(INPUT_GET, "date_sch");
    $shift     = filter_input(INPUT_GET, "shift");
    $date_sch = date('Y-m-d', strtotime($date_sch));
    $dataTrans = $this->targetGreentireApi->loadtire($date_sch, $shift);
    foreach ($dataTrans as $value) {
      $sorted[] = (object) [
        'ID' => $value['Id'],
        'ItemID' => $value['ItemId'],
        'ItemName' => $value['ItemGTName'],
        'Color' => $value['Color'],
        'Target' => $value['Target'],
        'Target1' => $value['Target1'],
        'Actual' => $value['Actual'],
        'Stock' => $value['Stock'],
        'Total' => $value['Total'],
        'TireNotSpac' => $value['TireNotSpac'],
        'TotalSystem' => $value['TotalSystem'],
        'CheckCountOut' => $value['CheckCountOut'],
        'BL' => $value['BL'],
        'CompareNum' => $value['CompareNum'],
        'CompareBill' => $value['CompareBill'],
        'CountNum' => $value['CountNum'],
        'Produce' => $value['Produce'],

        'Car1_1' => $value['Car1_1'],
        'Car1_2' => $value['Car1_2'],
        'Car1_3' => $value['Car1_3'],
        'Car1_4' => $value['Car1_4'],
        'Car1_5' => $value['Car1_5'],
        'Car1_6' => $value['Car1_6'],
        'Car1_7' => $value['Car1_7'],
        'Car1_8' => $value['Car1_8'],
        'Car2_1' => $value['Car2_1'],
        'Car2_2' => $value['Car2_2'],
        'Car2_3' => $value['Car2_3'],
        'Car2_4' => $value['Car2_4'],
        'Car2_5' => $value['Car2_5'],
        'Car2_6' => $value['Car2_6'],
        'Car2_7' => $value['Car2_7'],
        'Car2_8' => $value['Car2_8'],

        'CarNumber1_1' => $value['CarNumber1_1'],
        'CarNumber1_2' => $value['CarNumber1_2'],
        'CarNumber1_3' => $value['CarNumber1_3'],
        'CarNumber1_4' => $value['CarNumber1_4'],
        'CarNumber1_5' => $value['CarNumber1_5'],
        'CarNumber1_6' => $value['CarNumber1_6'],
        'CarNumber1_7' => $value['CarNumber1_7'],
        'CarNumber1_8' => $value['CarNumber1_8'],
        'CarNumber2_1' => $value['CarNumber2_1'],
        'CarNumber2_2' => $value['CarNumber2_2'],
        'CarNumber2_3' => $value['CarNumber2_3'],
        'CarNumber2_4' => $value['CarNumber2_4'],
        'CarNumber2_5' => $value['CarNumber2_5'],
        'CarNumber2_6' => $value['CarNumber2_6'],
        'CarNumber2_7' => $value['CarNumber2_7'],
        'CarNumber2_8' => $value['CarNumber2_8'],
        'TotalPayOfCar' => $value['TotalPayOfCar'],
        'Stock2' => $value['Stock2'],
        'CalStock' => $value['CalStock']

      ];
    }
    $sorted = json_encode($sorted);
    echo $sorted;
  }

  public function checkdatadisbursement()
  {
    $date_sch = filter_input(INPUT_GET, "date_sch");
    $shift     = filter_input(INPUT_GET, "shift");
    $status   = 1;
    $shiftname = (int) $shift === 1 ? 'กลางวัน (08.00 - 20.00 น.)' : 'กลางคืน (20.00 - 08.00 น.)';


    $checkdata = $this->targetGreentireApi->checkcompleteSchdisburTable($date_sch, $shift);


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

  public function deleterowdisbursement()
  {
    $id = $_POST['id'];
    $delete =  $this->targetGreentireApi->Deletedisbursement($id);
    if ($delete == true) {
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

  public function UpdateSchDisburTable()
  {
    $TireNotSpac   = $_POST['TireNotSpac'];
    $date_sch = $_POST['date_sch'];
    $shift = $_POST['shift'];
    $id   = $_POST['id'];
    $CalStock = $_POST['CalStock'];
    $date_sch = date('Y-m-d', strtotime($date_sch));
    $insert = $this->targetGreentireApi->UpdateSchDisburTable($TireNotSpac, $date_sch, $shift, $id, $CalStock);
    if ($insert == true) {
      echo json_encode([
        "result" => 200,
        "message" => "UpdateSchTable Successful"
      ]);
    } else {
      echo json_encode([
        "result" => 404,
        "message" => "no save"
      ]);
    }
  }

  public function UpdateSchDisburTableCar()
  {
    $Car1_1   = $_POST['Car1_1'];
    $Car1_2   = $_POST['Car1_2'];
    $Car1_3   = $_POST['Car1_3'];
    $Car1_4   = $_POST['Car1_4'];
    $Car1_5   = $_POST['Car1_5'];
    $Car1_6   = $_POST['Car1_6'];
    $Car1_7   = $_POST['Car1_7'];
    $Car1_8   = $_POST['Car1_8'];
    $Car2_1   = $_POST['Car2_1'];
    $Car2_2   = $_POST['Car2_2'];
    $Car2_3   = $_POST['Car2_3'];
    $Car2_4   = $_POST['Car2_4'];
    $Car2_5   = $_POST['Car2_5'];
    $Car2_6   = $_POST['Car2_6'];
    $Car2_7   = $_POST['Car2_7'];
    $Car2_8   = $_POST['Car2_8'];

    $num1_1   = $_POST['CarNumber1_1'];
    $num1_2   = $_POST['CarNumber1_2'];
    $num1_3   = $_POST['CarNumber1_3'];
    $num1_4   = $_POST['CarNumber1_4'];
    $num1_5   = $_POST['CarNumber1_5'];
    $num1_6   = $_POST['CarNumber1_6'];
    $num1_7   = $_POST['CarNumber1_7'];
    $num1_8   = $_POST['CarNumber1_8'];
    $num2_1   = $_POST['CarNumber2_1'];
    $num2_2   = $_POST['CarNumber2_2'];
    $num2_3   = $_POST['CarNumber2_3'];
    $num2_4   = $_POST['CarNumber2_4'];
    $num2_5   = $_POST['CarNumber2_5'];
    $num2_6   = $_POST['CarNumber2_6'];
    $num2_7   = $_POST['CarNumber2_7'];
    $num2_8   = $_POST['CarNumber2_8'];
    $sch_date   = $_POST['date_sch'];
    $Shift   = $_POST['Shift'];
    $id   = $_POST['id'];
    $sch_date = date('Y-m-d', strtotime($sch_date));
    $insert = $this->targetGreentireApi->UpdateSchDisburTableCar(
      $Car1_1,
      $Car1_2,
      $Car1_3,
      $Car1_4,
      $Car1_5,
      $Car1_6,
      $Car1_7,
      $Car1_8,
      $Car2_1,
      $Car2_2,
      $Car2_3,
      $Car2_4,
      $Car2_5,
      $Car2_6,
      $Car2_7,
      $Car2_8,
      $num1_1,
      $num1_2,
      $num1_3,
      $num1_4,
      $num1_5,
      $num1_6,
      $num1_7,
      $num1_8,
      $num2_1,
      $num2_2,
      $num2_3,
      $num2_4,
      $num2_5,
      $num2_6,
      $num2_7,
      $num2_8,
      $id,
      $sch_date,
      $Shift
    );
    if ($insert == true) {
      echo json_encode([
        "result" => 200,
        "message" => "UpdateSchTable Successful"
      ]);
    } else {
      echo json_encode([
        "result" => 404,
        "message" => "no save"
      ]);
    }
  }

  public function genfacetire()
  {

    $gen_emp   = null;
    $date_emp  = null;
    $shift_emp = null;
    $date_gen = $_POST['date_gen'];
    $shift_gen     =  $_POST['gen_shift'];
    $date_sch = $_POST['date_sch'];
    $shift     =  $_POST['shift'];
    $copy     = $_POST['copy'];
    $gen_emp   = $_POST['gen_emp'];
    $date_emp  = $_POST['date_emp'];
    $shift_emp = $_POST['shift_emp'];
    $date_sch = date('Y-m-d', strtotime($date_sch));
    $date_gen = date('Y-m-d', strtotime($date_gen));
    $insert = $this->targetGreentireApi->insertSchfacetireTable($date_sch, $shift, $copy, $date_gen, $shift_gen, $gen_emp, $date_emp, $shift_emp);

    if ($insert == true) {
      echo json_encode([
        "result" => 200,
        "message" => $insert
      ]);
    } else {
      echo json_encode([
        "result" => 404,
        "message" => $copy
      ]);
    }
  }
  public function loadfacetire()
  {
    $date_sch = filter_input(INPUT_GET, "date_sch");
    $shift     = filter_input(INPUT_GET, "shift");
    $date_sch = date('Y-m-d', strtotime($date_sch));
    $dataTrans = $this->targetGreentireApi->loadfacetire($date_sch, $shift);
    foreach ($dataTrans as $value) {
      $sorted[] = (object) [
        'ID' => $value['Id'],
        'ItemID' => $value['ItemId'],
        'ItemName' => $value['ItemGTName'],
        'Color' => $value['Color'],
        'Stock2' => $value['Stock2'],
        'TotalProduct' => $value['TotalProduct'],
        'TotalPayOfCar' => $value['TotalPayOfCar'],
        'TireNotSpec' => $value['TireNotSpec'],
        'Total' => $value['Total'],
        'StockTire' => $value['StockTire'],
        'CompareNum' => $value['CompareNum'],
        'CheckCountOut' => $value['CheckCountOut'],
        'TotalPlanCreate' => $value['TotalPlanCreate'],
        'CompareBill' => $value['CompareBill'],

        'Car1_1' => $value['CountCar1'],
        'Car1_2' => $value['CountCar2'],
        'Car1_3' => $value['CountCar3'],
        'Car1_4' => $value['CountCar4'],
        'Car1_5' => $value['CountCar5'],
        'Car1_6' => $value['CountCar6'],
        'Car1_7' => $value['CountCar7'],
        'Car1_8' => $value['CountCar8'],
        'CarNumber1_1' => $value['NumberCar1'],
        'CarNumber1_2' => $value['NumberCar2'],
        'CarNumber1_3' => $value['NumberCar3'],
        'CarNumber1_4' => $value['NumberCar4'],
        'CarNumber1_5' => $value['NumberCar5'],
        'CarNumber1_6' => $value['NumberCar6'],
        'CarNumber1_7' => $value['NumberCar7'],
        'CarNumber1_8' => $value['NumberCar8'],
        'Pay2_1' => $value['PayOfCar'],
        'Pay2_2' => $value['PayOfCar2'],
        'Pay2_3' => $value['PayOfCar3'],
        'Pay2_4' => $value['PayOfCar4'],
        'Pay2_5' => $value['PayOfCar5'],
        'Pay2_6' => $value['PayOfCar6'],
        'Pay2_7' => $value['PayOfCar7'],
        'Pay2_8' => $value['PayOfCar8'],
        'Stock' => $value['Stock'],
        'CalStock' => $value['CalStock']

      ];
    }
    $sorted = json_encode($sorted);
    echo $sorted;
  }

  public function checkgridschfacetire()
  {
    $date_sch = filter_input(INPUT_GET, "date_sch");
    $shift     = filter_input(INPUT_GET, "shift");
    $status   = 1;
    $shiftname = (int) $shift === 1 ? 'กลางวัน (08.00 - 20.00 น.)' : 'กลางคืน (20.00 - 08.00 น.)';
    $checkdata = $this->targetGreentireApi->checkgridschfacetire($date_sch, $shift);
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
  public function additemfacetire()
  {

    $itemid   = $_POST['itemid'];
    $id     = $_POST['id'];
    $sch_date    = $_POST['sch_date'];
    $shift     = $_POST['shift'];
    $updateItemgremtire = $this->targetGreentireApi->additemfacetire($itemid, $sch_date, $shift, $id);
    if ($updateItemgremtire == true) {
      echo json_encode([
        "result" => 200,
        "message" => "Update Successful"
      ]);
    } else {
      echo json_encode([
        "result" => 404,
        "message" => "Update Failed!"
      ]);
    }
  }

  public function additemDisburs()
  {

    $itemid   = $_POST['itemid'];
    $id     = $_POST['id'];
    $sch_date    = $_POST['sch_date'];
    $shift     = $_POST['shift'];
    $updateItemgremtire = $this->targetGreentireApi->additemDisburs($itemid, $sch_date, $shift, $id);
    if ($updateItemgremtire == true) {
      echo json_encode([
        "result" => 200,
        "message" => "Update Successful"
      ]);
    } else {
      echo json_encode([
        "result" => 404,
        "message" => "Update Failed!"
      ]);
    }
  }
  public function deleterowfacetire()
  {
    $id = $_POST['id'];
    $delete =  $this->targetGreentireApi->deleterowfacetire($id);
    if ($delete == true) {
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

  public function UpdateSchFacetireTable()
  {
    $TireNotSpac   = $_POST['TireNotSpac'];
    $date_sch = $_POST['date_sch'];
    $StockTire = $_POST['StockTire'];
    $shift = $_POST['shift'];
    $id   = $_POST['id'];
    $date_sch1 =  date('Y-m-d', strtotime($date_sch));
    $CalStock = $_POST['CalStock'];
    $date_sch = date('Y-m-d', strtotime($date_sch));
    $insert = $this->targetGreentireApi->UpdateSchFacetireTable($TireNotSpac, $StockTire, $date_sch1, $shift, $id, $CalStock);

    if ($insert == true) {
      echo json_encode([
        "result" => 200,
        "message" => "UpdateSchTable Successful"
      ]);
    } else {
      echo json_encode([
        "result" => 404,
        "message" => "no save"
      ]);
    }
  }
  public function UpdateSchFacetireTableCar()
  {
    $Car1_1   = $_POST['Car1_1'];
    $Car1_2   = $_POST['Car1_2'];
    $Car1_3   = $_POST['Car1_3'];
    $Car1_4   = $_POST['Car1_4'];
    $Car1_5   = $_POST['Car1_5'];
    $Car1_6   = $_POST['Car1_6'];
    $Car1_7   = $_POST['Car1_7'];
    $Car1_8   = $_POST['Car1_8'];
    $CarNumber1_1   = $_POST['CarNumber1_1'];
    $CarNumber1_2   = $_POST['CarNumber1_2'];
    $CarNumber1_3   = $_POST['CarNumber1_3'];
    $CarNumber1_4   = $_POST['CarNumber1_4'];
    $CarNumber1_5   = $_POST['CarNumber1_5'];
    $CarNumber1_6   = $_POST['CarNumber1_6'];
    $CarNumber1_7   = $_POST['CarNumber1_7'];
    $CarNumber1_8    = $_POST['CarNumber1_8'];
    $Pay2_1   = $_POST['Pay2_1'];
    $Pay2_2   = $_POST['Pay2_2'];
    $Pay2_3   = $_POST['Pay2_3'];
    $Pay2_4   = $_POST['Pay2_4'];
    $Pay2_5   = $_POST['Pay2_5'];
    $Pay2_6   = $_POST['Pay2_6'];
    $Pay2_7   = $_POST['Pay2_7'];
    $Pay2_8   = $_POST['Pay2_8'];
    $date_sch   = $_POST['date_sch'];
    $shift   = $_POST['shift'];
    $date_sch = date('Y-m-d', strtotime($date_sch));

    $id   = $_POST['id'];
    $insert = $this->targetGreentireApi->UpdateSchFacetireTableCar(
      $Car1_1,
      $Car1_2,
      $Car1_3,
      $Car1_4,
      $Car1_5,
      $Car1_6,
      $Car1_7,
      $Car1_8,
      $CarNumber1_1,
      $CarNumber1_2,
      $CarNumber1_3,
      $CarNumber1_4,
      $CarNumber1_5,
      $CarNumber1_6,
      $CarNumber1_7,
      $CarNumber1_8,
      $Pay2_1,
      $Pay2_2,
      $Pay2_3,
      $Pay2_4,
      $Pay2_5,
      $Pay2_6,
      $Pay2_7,
      $Pay2_8,
      $id,
      $date_sch,
      $shift
    );
    if ($insert == true) {
      echo json_encode([
        "result" => 200,
        "message" => "UpdateSchTable Successful"
      ]);
    } else {
      echo json_encode([
        "result" => 404,
        "message" => "no save"
      ]);
    }
  }

  public function checkgridschplantire()
  {
    $date_sch = filter_input(INPUT_GET, "date_sch");
    $shift     = filter_input(INPUT_GET, "shift");
    $status   = 1;
    $shiftname = (int) $shift === 1 ? 'กลางวัน (08.00 - 20.00 น.)' : 'กลางคืน (20.00 - 08.00 น.)';
    $checkdata = $this->targetGreentireApi->checkgridschplantire($date_sch, $shift);
    if ($checkdata == true) {
      echo json_encode([
        "result" => true,
        "status" => 1,
        "message" => "Data is Hasrows"
      ]);
    } else {

      echo json_encode([
        "result" => false,
        "status" => 1,
        "message" => $date_sch . " " . $shiftname . "<br>Scheduler not Found !"
      ]);
    }
  }
  public function loadplantire()
  {
    $date_sch = filter_input(INPUT_GET, "date_sch");
    $shift     = filter_input(INPUT_GET, "shift");
    $date_sch = date('Y-m-d', strtotime($date_sch));
    $dataTrans = $this->targetGreentireApi->loadplantire($date_sch, $shift);
    foreach ($dataTrans as $value) {
      $sorted[] = (object) [
        'OrderLackshift' => $value['OrderLackshift'],
        'check1' => $value['check1'],
        'check2' => $value['check2'],
        'check3' => $value['check3'],
        'check4' => $value['check4'],
        'ItemId' => $value['ItemId'],
        'DSG_COLOR' => $value['DSG_COLOR'],
        'ITEMNAME' => $value['Name'],
        'GrandTotal' => $value['GrandTotal'],
        'TotalSystemPD' => $value['TotalSystemPD'],
        'Total' => $value['Total'],
        'ActualDay1C' => $value['ActualDay1C'],
        'ActualDay1D' => $value['ActualDay1D'],
        'ActualDay2C' => $value['ActualDay2C'],
        'ActualDay2D' => $value['ActualDay2D'],
        'ActualDay3C' => $value['ActualDay3C'],
        'ActualDay3D' => $value['ActualDay3D'],
        'ShiftDay1C' => $value['ShiftDay1C'],
        'ShiftDay1D' => $value['ShiftDay1D'],
        'ShiftDay2C' => $value['ShiftDay2C'],
        'ShiftDay2D' => $value['ShiftDay2D'],
        'ShiftDay3C' => $value['ShiftDay3C'],
        'ShiftDay3D' => $value['ShiftDay3D'],
        'ITEMNAME_LIST' => $value['ITEMNAME_LIST'],
        'BL' => $value['BL'],
        'StockStatus' => $value['StockStatus'],
        'checktotal' => $value['checktotal']
      ];
    }
    $sorted = json_encode($sorted);
    echo $sorted;
  }

  public function loadplantiregroup1()
  {
    $date_sch = filter_input(INPUT_GET, "date_sch");
    $shift     = filter_input(INPUT_GET, "shift");
    $date_sch = date('Y-m-d', strtotime($date_sch));
    $dataTrans = $this->targetGreentireApi->loadplantiregroup1($date_sch, $shift);
    foreach ($dataTrans as $value) {
      $sorted[] = (object) [
        'OrderLackshift' => $value['OrderLackshift'],
        'check1' => $value['check1'],
        'check2' => $value['check2'],
        'ItemId' => $value['ItemId'],
        'DSG_COLOR' => $value['DSG_COLOR'],
        'ITEMNAME' => $value['Name'],
        'GrandTotal' => $value['GrandTotal'],
        'TotalSystemPD' => $value['TotalSystemPD'],
        'Total' => $value['Total'],
        'ActualDay1C' => $value['ActualDay1C'],
        'ActualDay1D' => $value['ActualDay1D'],
        'ActualDay2C' => $value['ActualDay2C'],
        'ActualDay2D' => $value['ActualDay2D'],
        'ActualDay3C' => $value['ActualDay3C'],
        'ActualDay3D' => $value['ActualDay3D'],
        'ShiftDay1C' => $value['ShiftDay1C'],
        'ShiftDay1D' => $value['ShiftDay1D'],
        'ShiftDay2C' => $value['ShiftDay2C'],
        'ShiftDay2D' => $value['ShiftDay2D'],
        'ShiftDay3C' => $value['ShiftDay3C'],
        'ShiftDay3D' => $value['ShiftDay3D'],
        'ITEMNAME_LIST' => $value['ITEMNAME_LIST'],
        'BL' => $value['BL'],
        'StockStatus' => $value['StockStatus'],
        'check3' => $value['check3'],
        'checktotal' => $value['checktotal']
      ];
    }
    $sorted = json_encode($sorted);
    echo $sorted;
  }
  public function checkgriddateplantire()
  {
    $date_sch = filter_input(INPUT_GET, "date_sch");
    $shift     = filter_input(INPUT_GET, "shift");
    $status   = 1;
    $shiftname = (int) $shift === 1 ? 'กลางวัน (08.00 - 20.00 น.)' : 'กลางคืน (20.00 - 08.00 น.)';
    $checkdata = $this->targetGreentireApi->checkgriddateplantire($date_sch, $shift);
    //var_dump($checkdata);
    echo json_encode([
      "date1" => $checkdata[0]["DateBuild"],
      "date2" => $checkdata[1]["DateBuild"],

    ]);
  }
  public function loadModulebillbuy()
  {
    try {

      $shift = 1;
      $date = $_POST["_date"];
      // $module = $_POST["_module"];
      // $transId = $_POST["_transId"];


      //$this->targetGreentireApi->addTargetGreentire($shift, $date, $transId);
      $data = $this->targetGreentireApi->shiftwork($shift);
      $shiftcheck = $data[0]["ShiftFor"];
      $sf = (int) $shiftcheck === 1 ? 'C' : 'D';
      $st =  ' (08.00 - 20.00 น.)';
      $sf1 = (int) $shiftcheck === 1 ? 'D' : 'C';
      $st1 = ' (20.00 - 08.00 น.)';
      // print_r($sf1 . $st1);
      // exit();
      renderView("production_sch2/target_billbuy", [
        // "transId" => $transId,
        "date" => $date,
        "shift" => $shift,
        "shift1" => $sf . $st,
        "shift2" => $sf1 . $st1

      ]);
    } catch (\Exception $e) {
      return json_encode(response(false, $e->getMessage()));
    }
  }
  public function loadTargetbillbuy($shiftDate)
  {
    try {
      $data = $this->targetGreentireApi->loadTargetbillbuy($shiftDate);
      $itemid = '';


      foreach ($data as $value) {
        if ($itemid != $value['ItemID']) {
          $sorted[] = [
            'ItemID' => $value['ItemID'],
            'ItemName' => $value['ItemName'],
            'TT' => $value['TT'],
            'Weight' => $value['Weight'],
            'Shift1'  => [],
            'BillUse1' => [],
            'BillGive1' => [],
            'faceBoiler1' => [],
            'Shift2'  => [],
            'BillUse2' => [],
            'BillGive2' => [],
            'faceBoiler2' => [],
            'ColorAll' => $value['ColorAll']

          ];
        }
        $itemid = $value['ItemID'];
      }
      // echo "<pre>" . print_r($data, true) . "</pre>";
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
            'faceBoiler1' => $value['faceboiler']

          ];
        }
        if ($value['Shift'] == 2) {
          $line_b[] = [
            'ItemID' => $value['ItemID'],
            'ItemName' => $value['ItemName'],
            'Shift2' => $value['Shift'],
            'BillUse2' => $value['BillUse'],
            'BillGive2' => $value['BillGive'],
            'faceBoiler2' => $value['faceboiler']
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
      foreach ($sorted as $i) {


        $sorted1[] = [
          'ItemID' => $i['ItemID'],
          'ItemName' => $i['ItemName'],
          'TT' => $i['TT'],
          'Weight' => $i['Weight'],
          'Shift1'  => $i['Shift1']["0"],
          'BillUse1' => $i['BillUse1']["0"],
          'BillGive1' => $i['BillGive1']["0"],
          'faceBoiler1' => $i['faceBoiler1']["0"],
          'Shift2'  => $i['Shift2']["0"],
          'BillUse2' => $i['BillUse2']["0"],
          'BillGive2' => $i['BillGive2']["0"],
          'faceBoiler2' => $i['faceBoiler2']["0"],
          'ColorAll' => $i['ColorAll']

        ];
      }
      // echo "<pre>" . print_r($sorted1, true) . "<pre>";
      // exit();

      $pack = $this->datatables->get($sorted1, $_POST);
      echo json_encode($pack);
    } catch (\Exception $e) {
      return $e->getMessage();
    }
  }

  public function reportCure($transId, $shiftDate)
  {
    try {

      $data = $this->targetGreentireApi->loadTargetbillbuy($shiftDate);
      $itemid = '';


      foreach ($data as $value) {
        if ($itemid != $value['ItemID']) {
          $sorted[] = [
            'ItemID' => $value['ItemID'],
            'ItemName' => $value['ItemName'],
            'TT' => $value['TT'],
            'Weight' => $value['Weight'],
            'Shift1'  => [],
            'BillUse1' => [],
            'BillGive1' => [],
            'faceBoiler1' => [],
            'Shift2'  => [],
            'BillUse2' => [],
            'BillGive2' => [],
            'faceBoiler2' => [],
            'ColorAll' => $value['ColorAll']

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
            'faceBoiler1' => $value['faceboiler']

          ];
        }
        if ($value['Shift'] == 2) {
          $line_b[] = [
            'ItemID' => $value['ItemID'],
            'ItemName' => $value['ItemName'],
            'Shift2' => $value['Shift'],
            'BillUse2' => $value['BillUse'],
            'BillGive2' => $value['BillGive'],
            'faceBoiler2' => $value['faceboiler']
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

      // echo "<pre>" . print_r($sorted, true) . "<pre>";
      // exit();

      $data1 = $this->targetGreentireApi->shiftwork($shiftDate);
      $shiftcheck = $data1[0]["ShiftFor"];
      $sf = (int) $shiftcheck === 1 ? 'C' : 'D';
      $st = ' (08.00 - 20.00 น.)';
      $sf1 = (int) $shiftcheck === 1 ? 'D' : 'C';
      $st1 = ' (20.00 - 08.00 น.)';
      return renderView("production_sch2/target_greentirecure_report", [
        "data" => $sorted,
        "id" => $transId,
        "date" => $shiftDate,
        "shift1" => $sf . '<BR>' . $st,
        "shift2" => $sf1 . '<BR>' . $st1
      ]);
    } catch (\Exception $e) {
      return $e->getMessage();
    }
  }

  public function reportCureExcel($transId, $shiftDate)
  {
    try {

      $data = $this->targetGreentireApi->loadTargetbillbuy($shiftDate);
      $itemid = '';


      foreach ($data as $value) {
        if ($itemid != $value['ItemID']) {
          $sorted[] = [
            'ItemID' => $value['ItemID'],
            'ItemName' => $value['ItemName'],
            'TT' => $value['TT'],
            'Weight' => $value['Weight'],
            'Shift1'  => [],
            'BillUse1' => [],
            'BillGive1' => [],
            'faceBoiler1' => [],
            'Shift2'  => [],
            'BillUse2' => [],
            'BillGive2' => [],
            'faceBoiler2' => [],
            'ColorAll' => $value['ColorAll']

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
            'faceBoiler1' => $value['faceboiler']

          ];
        }
        if ($value['Shift'] == 2) {
          $line_b[] = [
            'ItemID' => $value['ItemID'],
            'ItemName' => $value['ItemName'],
            'Shift2' => $value['Shift'],
            'BillUse2' => $value['BillUse'],
            'BillGive2' => $value['BillGive'],
            'faceBoiler2' => $value['faceboiler']
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

      // echo "<pre>" . print_r($sorted, true) . "<pre>";
      // exit();
      $data1 = $this->targetGreentireApi->shiftwork($shiftDate);
      $shiftcheck = $data1[0]["ShiftFor"];
      $sf = (int) $shiftcheck === 1 ? 'C' : 'D';
      $st = ' (08.00 - 20.00 น.)';
      $sf1 = (int) $shiftcheck === 1 ? 'D' : 'C';
      $st1 = ' (20.00 - 08.00 น.)';
      return renderView("production_sch2/target_greentire_reportCure_excel", [
        "data" => $sorted,
        "id" => $transId,
        "date" => $shiftDate,
        "shift1" => $sf . '<BR>' . $st,
        "shift2" => $sf1 . '<BR>' . $st1
      ]);
    } catch (\Exception $e) {
      return $e->getMessage();
    }
  }

  public function loadplaloadordersummaryntire()
  {
    $date_sch = filter_input(INPUT_GET, "date_sch");
    $shift     = filter_input(INPUT_GET, "shift");
    $date_sch = date('Y-m-d', strtotime($date_sch));
    $dataTrans = $this->targetGreentireApi->loadplaloadordersummaryntire($date_sch, $shift);
    foreach ($dataTrans as $value) {
      $sorted[] = (object) [
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
        // 'ShiftDay3C' => $value['ShiftDay3C'],
        // 'ShiftDay3D' => $value['ShiftDay3D'],
        // 'ITEMNAME_LIST' => $value['ITEMNAME_LIST'],
        // 'BL' => $value['BL'],
        // 'StockStatus' => $value['StockStatus'],
        // 'checktotal' => $value['checktotal']
      ];
    }
    $sorted = json_encode($sorted);
    echo $sorted;
  }

  public function updateschordersumary()
  {
    $CountInOrder   = $_POST['CountInOrder'];
    $id   = $_POST['id'];
    $date_sch = $_POST['date_sch'];
    $shift = $_POST['shift'];
    $date_sch = date('Y-m-d', strtotime($date_sch));
    $insert = $this->targetGreentireApi->updateschordersumary($CountInOrder,  $id, $date_sch, $shift);
    if ($insert == true) {
      echo json_encode([
        "result" => 200,
        "message" => "UpdateSchTable Successful"
      ]);
    } else {
      echo json_encode([
        "result" => 404,
        "message" => $id
      ]);
    }
  }
  public function updateSchfacetireTableStock()
  {


    $date_sch = $_POST['date_sch'];
    $shift     =  $_POST['shift'];
    $date_sch = date('Y-m-d', strtotime($date_sch));

    $insert = $this->targetGreentireApi->updateSchfacetireTableStock($date_sch, $shift);
    if ($insert == true) {
      echo json_encode([
        "result" => 200,
        "message" => "Generate Successful"
      ]);
    } else {
      echo json_encode([
        "result" => 404,
        "message" => $date_sch
      ]);
    }
  }
}
