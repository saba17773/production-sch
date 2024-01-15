<?php

namespace App\V2\BuildSch;

use App\V2\BuildSch\BuildSchAPI;
use App\Common\Datatables;

class BuildSchController
{
  private $BuildSchApi = null;
  private $datatables = null;

  public function __construct()
  {
    $this->BuildSchApi = new BuildSchAPI();
    $this->datatables = new Datatables();
  }
  public function index()
  {
    renderView("production_sch2/build_sch");
  }
  public function export()
  {
    renderView("production_sch2/build_sch_export");
  }
  public function import()
  {
    try {
      $date = date('Y-m-d', strtotime($_POST["ImportDate"]));
      $shift = $_POST["ImportShift"];

      $fileExcel = str_replace(" ", "_", $_FILES["ImportFile"]["name"]);
      $type = pathinfo($fileExcel,PATHINFO_EXTENSION);
      $fileExcelRenamed = "buildsch_".date("ymd_his").".". $type;
      $target_dir = "./resources/build/";
      $target_file = $target_dir . $fileExcelRenamed;

      if (move_uploaded_file($_FILES["ImportFile"]["tmp_name"], $target_file)){
        $f = new \SpreadsheetReader($target_file);

        foreach ($f as $key => $value) {
          if ($key >= 2) {
            if ($value[0]!="") {
              $databuild[] =[
                "ItemId" => str_replace(' ', '',$value[0]),
                "OrderWeek" => $value[3],
                "NumberBL" => $value[4],
                "BL" => $value[5],
                "TargetTemp" => $value[6],
                "Adjust" => $value[7],
                "Target" => $value[8],
                "Actual" => $value[9],
                "Remark" => $value[10],
                "OverLose" => $value[11],
                "DateBuild" => $date,
                "Shift" => $shift
              ];
            }
          }
        }
        // echo "<pre>".print_r($databuild,true)."</pre>";
        $delete = $this->BuildSchApi->clearBuildSch($date,$shift);
        $result = $this->BuildSchApi->addBuildSch($databuild);
        // return json_encode($result);
        if ($result==true) {
          renderView('production_sch2/build_sch');
        }
      }
      
    } catch (Exception $e) {
      echo \json_encode($e->getMessage());
    }
  }
  public function getBuildLists()
  {
    try {

      $filter = [
        "Shift" => "SM.[Description]"
      ];
      
      $date = $_GET["date"];
      $shift = $_GET["shift"];
      // $date = '2020-01-02';
      // $shift = 1;
      // $data = $this->BuildSchApi->getBuildLists($this->datatables->filter($_POST, $filter));
      // $pack = $this->datatables->get($data, $_POST);
      $data = $this->BuildSchApi->getBuildLists($date,$shift);
      $pack = $this->datatables->get($data, $_POST);
      return json_encode($pack);
    } catch (\Exception $e) {
      return json_encode(response(false, $e->getMessage()));
    }
  }
  public function getGreentireList()
  {
    try {
      // $date = $_POST["date"];
      $date = '2020-01-03';
      $data = $this->BuildSchApi->getGreentireList($date);
      return json_encode($data);
    } catch (Exception $e) {
      echo \json_encode($e->getMessage());
    }
  }
  public function clear()
  {
    try {
      $date = date('Y-m-d', strtotime($_POST["date"]));
      $shift = $_POST["shift"];

      $result = $this->BuildSchApi->clearBuildSch($date,$shift);
      return json_encode($result);
    } catch (Exception $e) {
      echo \json_encode($e->getMessage());
    }
  }
  public function getBuildGroup()
  {
    try {

      $filter = [
        "Shift" => "SM.[Description]",
        "CreateBy" => "U.Name"
      ];
      
      $data = $this->BuildSchApi->getBuildGroup($this->datatables->filter($_POST, $filter));
      $pack = $this->datatables->get($data, $_POST);
      return json_encode($pack);
    } catch (\Exception $e) {
      return json_encode(response(false, $e->getMessage()));
    }
  }
  public function buildsch_list($shift,$date)
  {
    return renderView("production_sch2/build_sch_all", [
      "date" => $date,
      "shift" => $shift
    ]);
  }
  public function importCheck()
  {
    try {
      $date = date('Y-m-d', strtotime($_POST["date"]));
      $shift = $_POST["shift"];

      $result = $this->BuildSchApi->importCheck($date,$shift);
      return json_encode($result);
    } catch (Exception $e) {
      echo \json_encode($e->getMessage());
    }
  }
}