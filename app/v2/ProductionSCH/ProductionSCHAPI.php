<?php

namespace App\V2\ProductionSCH;

use App\V2\Database\Connector;
use App\V2\Database\Handler;
use Wattanar\Sqlsrv;
use App\V2\Helper\Helper;

class ProductionSCHAPI
{

  public function checkSchTable($date_sch, $shift)
  {
    $db = new Connector;
    $conn = $db->dbConnect();
    $date = date('Y-m-d', strtotime($date_sch));

    $query = Sqlsrv::hasRows(
      $conn,
      "SELECT * FROM ProductionSchTable
      WHERE SchDate=? AND Shift=? AND Company=?",
      [$date, $shift, $_SESSION["user_company"]]
    );

    if ($query) {

      $delete = Sqlsrv::hasRows(
        $conn,
        "DELETE FROM ProductionSchTable
        WHERE SchDate=? AND Shift=? AND Company=?",
        [$date, $shift, $_SESSION["user_company"]]
      );

      $delete = Sqlsrv::hasRows(
        $conn,
        "DELETE FROM ProductionSchEmployee
        WHERE SCHDate=? AND Shift=? AND Company=?",
        [$date, $shift, $_SESSION["user_company"]]
      );

      $deleteEmp = Sqlsrv::hasRows(
        $conn,
        "DELETE FROM ProductionSchProblem
        WHERE SCHDate=? AND Shift=? AND Company=?",
        [$date, $shift, $_SESSION["user_company"]]
      );

      return true;
    } else {
      return false;
    }
  }

  public function load($date_sch, $shift, $id = null)
  {
    $db = new Connector;
    $conn = $db->dbConnect();

    $sqlId = "1=1";

    if ($id !== null) {
      $sqlId = " P.ID = $id ";
    }

    $sql = "SELECT
    P.ID,
    P.Boiler,
    P.Employee,
    P.ItemID,
    P.MoldID,
   -- P.Time,
    I.Time AS Time,
    P.Target,
    P.Actual1,
    P.Actual2,
    P.Actual,
    P.Scrap,
    CONVERT(DECIMAL(10,3),P.Weight/1000)*(P.Actual+P.Scrap) AS Weight,
    CONVERT(DECIMAL(10,3),P.Weight/1000) AS WeightDefault,
    P.SchDate,
    P.Shift,
    P.CreateBy,
    P.CreateDate,
    P.UpdateBy,
    P.UpdateDate,
    P.Status,
    P.Company,
    E.FirstName+' '+E.LastName[FullName],
    -- I.ItemName,
   
    CASE
    WHEN I.ItemName IS NOT NULL THEN REPLACE(I.ItemName, 'EU', '')+' '+I.Brand+
      CASE
        WHEN CHARINDEX('EU',ItemName,1) != 0 THEN ' EU'
      ELSE '' END
    ELSE I.ItemName END AS ItemName,
    I.ItemNameThai[NameTH],
    STR(C.ID)+'_'+C.CurID+'/'+C.CureSize[BoilerName],
    C.ID[CurID],
    P.Status,
    P.BillUse,
    P.BillGive,
    P.faceBoiler
    FROM ProductionSchTable P
    LEFT JOIN Employee E ON P.Employee=E.Code
    LEFT JOIN ProductionSchItemMaster I ON P.ItemID=I.ID
    LEFT JOIN ProductionSchCure C ON P.Boiler=C.CurID
    WHERE P.SchDate=? AND P.Shift=? AND P.Company=? AND $sqlId
    ORDER BY C.CurID, P.MoldID ASC";

    // return $sql;

    $query = Sqlsrv::queryArray(
      $conn,
      $sql,
      [$date_sch, $shift, $_SESSION["user_company"]]
    );

    return $query;
  }

  public function loadisExist($date_sch, $shift)
  {
    $db = new Connector;
    $conn = $db->dbConnect();

    $query = Sqlsrv::queryArray(
      $conn,
      "SELECT P.*,E.FirstName+' '+E.LastName[FullName],I.ItemNameThai[NameTH],str(C.ID)+'_'+C.CurID+'-'+C.CureSize[BoilerName],C.ID[CurID],P.Status  FROM ProductionSchTable P
        LEFT JOIN Employee E ON P.Employee=E.Code
        LEFT JOIN ProductionSchItemMaster I ON P.ItemID=I.ID
        LEFT JOIN ProductionSchCure C ON P.Boiler=C.CurID
        WHERE P.SchDate=? AND P.Shift=? AND P.Company=?
        AND P.ItemID IS NOT NULL
        AND P.Time IS NOT NULL
        AND P.Target IS NOT NULL
        AND P.Actual IS NOT NULL
        AND P.Scrap IS NOT NULL
        AND P.Weight IS NOT NULL",
      [$date_sch, $shift, $_SESSION["user_company"]]
    );

    return $query;
  }

  public function loademployeeSch($date_sch, $shift)
  {
    $db = new Connector;
    $conn = $db->dbConnect();

    $query = Sqlsrv::queryArray(
      $conn,
      "SELECT PE.*,E.FirstName +' '+ E.LastName [FullName]
      FROM ProductionSchEmployee PE
      LEFT JOIN Employee E ON PE.EmployeeID=E.Code
      WHERE PE.SCHDate = ? AND PE.Shift = ?",
      [$date_sch, $shift]
    );

    return $query;
  }

  public function loadremarkSch($date_sch, $shift)
  {
    $db = new Connector;
    $conn = $db->dbConnect();

    $query = Sqlsrv::queryArray(
      $conn,
      "SELECT PE.*,E.Description [Remark]
      FROM ProductionSchProblem PE
      LEFT JOIN ProductionSchProblemMaster E ON PE.ProblemID=E.ProblemID
      WHERE PE.SCHDate = ? AND PE.Shift = ?",
      [$date_sch, $shift]
    );

    return $query;
  }

  public function loademployeeby($id)
  {
    $db = new Connector;
    $conn = $db->dbConnect();

    $query = Sqlsrv::queryArray(
      $conn,
      "SELECT E.FirstName +' '+ E.LastName [FullName]
      FROM ProductionSchEmployee PE
      LEFT JOIN Employee E ON PE.EmployeeID=E.Code
      WHERE PE.TransID=?",
      [$id]
    );

    $e = [];
    foreach ($query as $value) {
      array_push($e, $value['FullName']);
    }

    $istext = implode(",", $e);
    return $istext;
  }

  public function loadremarkby($id)
  {
    $db = new Connector;
    $conn = $db->dbConnect();

    $query = Sqlsrv::queryArray(
      $conn,
      "SELECT E.Description [Remark]
        FROM ProductionSchProblem PE
        LEFT JOIN ProductionSchProblemMaster E ON PE.ProblemID=E.ProblemID
        WHERE PE.TransID=?",
      [$id]
    );

    $e = [];
    foreach ($query as $value) {
      array_push($e, $value['Remark']);
    }

    $istext = implode(",", $e);
    return $istext;
  }

  public function load_cure()
  {
    $db = new Connector;
    $conn = $db->dbConnect();

    $query = Sqlsrv::queryJson(
      $conn,
      "SELECT * FROM ProductionSchCure"
    );

    return $query;
  }

  public function loademployee()
  {
    $db = new Connector;
    $conn = $db->dbConnect();

    $query = Sqlsrv::queryJson(
      $conn,
      "SELECT E.*,D.Description[DepartmentName] FROM Employee E
        LEFT JOIN DepartmentMaster D ON E.DepartmentCode=D.Code
        WHERE E.Company=? AND E.DepartmentCode IN (30,31,43,44,1542,1543) AND E.EmpStatus <> 1
        ORDER BY E.ID ASC",
      [$_SESSION["user_company"]]
    );

    return $query;
  }

  public function check_item($item)
  {
    $db = new Connector;
    $conn = $db->dbConnect();

    return Sqlsrv::hasRows(
      $conn,
      "SELECT *
        FROM ProductionSchItemMaster
        WHERE ID =?",
      [$item]
    );
  }

  public function loaditem_by($item)
  {
    $db = new Connector;
    $conn = $db->dbConnect();

    $query = Sqlsrv::queryArray(
      $conn,
      "SELECT *
        FROM ProductionSchItemMaster
        WHERE ID =? AND Company = ? ",
      [$item, $_SESSION["user_company"]]
    );

    return $query;
  }

  public function loaditem($boiler)
  {
    $db = new Connector;
    $conn = $db->dbConnect();

    $query = Sqlsrv::queryJson(
      $conn,
      "SELECT *
        FROM ProductionSchItemMaster
        WHERE Company=? ",
      [$_SESSION["user_company"]]
    );

    return $query;
  }
  public function loaditemGT($boiler)
  {
    $db = new Connector;
    $conn = $db->dbConnect();

    $query = Sqlsrv::queryJson(
      $conn,
      "SELECT PM.*,
      IM.IsBOMActive,
      IM.Time,
      SM.GroupId,
      SM.Total
      FROM ProductionSchGreentireMaster PM
      LEFT JOIN ProductionSchItemMaster IM ON PM.ItemFG = IM.ID
      LEFT JOIN
       (SELECT ItemId,
               GroupId,
               Total
        FROM ShareMoldMaster
        GROUP BY ItemId,
                 GroupId,
                 Total)SM ON SM.ItemId = PM.ItemGT
      ORDER BY PM.ItemGT ASC"
    );

    return $query;
  }

  public function loaditemEXT($boiler)
  {
    $db = new Connector;
    $conn = $db->dbConnect();

    $query = Sqlsrv::queryJson(
      $conn,
      "SELECT
        ItemGT,
        itemext AS ItemBOM,
        Name AS ITEMNAME,
        DSG_SIZE AS DSGRIMSIZE,
        DSGPATTERNID,
        TT AS DSGTyre_Types,
        DSG_COLOR
      FROM ProductionSchEXTMaster
      ORDER BY ItemGT ASC"
    );

    return $query;
  }
  public function loaditemCP($boiler)
  {
    $db = new Connector;
    $conn = $db->dbConnect();

    $query = Sqlsrv::queryJson(
      $conn,

      "SELECT
        ItemEXT AS ItemBOM,
        ItemCP AS ItemCP,
        Name AS ITEMNAME,
        DSG_SIZE AS DSGRIMSIZE,
        DSGPATTERNID,
        TT AS DSGTyre_Types
        FROM ProductionSchCompondMaster
        GROUP BY ItemEXT,ItemCP,Name,DSG_SIZE ,DSGPATTERNID,TT
		     ORDER BY ItemEXT ASC"
    );
    return $query;
  }


  public function loadarms()
  {
    $db = new Connector;
    $conn = $db->dbConnect();

    $query = Sqlsrv::queryJson(
      $conn,
      "SELECT ArmsID FROM ProductionSchArms"
    );

    return $query;
  }

  public function loadremark()
  {
    $db = new Connector;
    $conn = $db->dbConnect();

    $query = Sqlsrv::queryJson(
      $conn,
      "SELECT ROW_NUMBER() OVER(ORDER BY ID ASC) AS RowID,*
        FROM ProductionSchProblemMaster
        WHERE Company = ?
        ORDER BY ID ASC",
      [$_SESSION["user_company"]]
    );

    return $query;
  }

  public function loadtime()
  {
    $db = new Connector;
    $conn = $db->dbConnect();

    $query = Sqlsrv::queryJson(
      $conn,
      "SELECT * FROM ProductionSchTimeMaster"
    );

    return $query;
  }

  public function getremark($transid)
  {
    $db = new Connector;
    $conn = $db->dbConnect();

    $query = Sqlsrv::queryJson(
      $conn,
      "SELECT P.*,M.ID
        FROM ProductionSchProblem P
        LEFT JOIN ProductionSchProblemMaster M ON P.ProblemID = M.ProblemID AND P.Company = M.Company
        WHERE P.TransID=?",
      [$transid]
    );

    return $query;
  }

  public function getemployee($transid)
  {
    $db = new Connector;
    $conn = $db->dbConnect();

    $query = Sqlsrv::queryJson(
      $conn,
      "SELECT P.*,M.ID,M.ParentID FROM ProductionSchEmployee P
        LEFT JOIN Employee M ON P.EmployeeID = M.Code AND P.Company=M.Company
        WHERE P.TransID=?",
      [$transid]
    );

    return $query;
  }

  public function insertSchTable($date_sch, $shift, $copy, $date_gen, $shift_gen, $gen_emp, $date_emp, $shift_emp, $shift_for)
  {
    try {

      $db = new Connector;
      $conn = $db->dbConnect();
      $date = date('Y-m-d', strtotime($date_sch));
      $date_gen = date('Y-m-d', strtotime($date_gen));
      $date_emp = date('Y-m-d', strtotime($date_emp));

      if (sqlsrv_begin_transaction($conn) === false) {
        return false;
      }

      function checkEmployee($dataEmployeeInfo, $date_sch, $shift, $boiler, $mold)
      {
        foreach ($dataEmployeeInfo as $key => $val) {
          if ($val['BoilerID'] === $boiler && $val['Mold'] === $mold) {
            return $val['EmployeeID'];
          }
        }
      }

      // function checkRemark($dataRemarkInfo, $date_sch, $shift, $boiler, $mold)
      // {

      //   foreach ($dataRemarkInfo as $key => $val) {
      //     if ($val['BoilerID'] === $boiler && $val['Mold'] === $mold) {
      //       return $val['ProblemID'];
      //     }
      //   }
      // }

      if ($copy == 0) {

        $query = Sqlsrv::queryArray(
          $conn,
          " SELECT * FROM ProductionSchCure
          WHERE Company = ?
          AND Active = '1' ",
          [
            $_SESSION["user_company"]
          ]
        );

        $mold = Sqlsrv::queryArray(
          $conn,
          "SELECT * FROM ProductionSchMold
          WHERE Company = ?",
          [
            $_SESSION["user_company"]
          ]
        );

        // $dataRemarkInfo = Sqlsrv::queryArray(
        //   $conn,
        //   "SELECT * FROM ProductionSchProblem
        //   WHERE SCHDate=?
        //   AND Shift=?
        //   AND Company=?",
        //   [
        //     $date_gen,
        //     $shift_gen,
        //     $_SESSION["user_company"]
        //   ]
        // );

        foreach ($mold as $m) {

          foreach ($query as $p) {

            $insertSchTable = sqlsrv_query(
              $conn,
              "INSERT INTO ProductionSchTable (Boiler,SchDate,Shift,MoldID,CreateDate,CreateBy,Status,Company,ShiftFor) VALUES(?,?,?,?,getdate(),?,?,?,?)",
              [
                $p['CurID'],
                $date,
                $shift,
                $m['MoldID'],
                $_SESSION["user_login"],
                1,
                $_SESSION["user_company"],
                $shift_for
              ]
            );

            // sqlsrv_next_result($insertSchTable);
            // sqlsrv_fetch($insertSchTable);
            // $lastid = sqlsrv_get_field($insertSchTable, 0);

            // $checkRemark = checkRemark($dataRemarkInfo, $date_gen, $shift_gen, $p['CurID'], $m['MoldID']);
            // if ($checkRemark) {

            //   $insertProblem = sqlsrv_query(
            //     $conn,
            //     "INSERT INTO ProductionSchProblem(
            //         TransID,
            //         BoilerID,
            //         SCHDate,
            //         Shift,
            //         Mold,
            //         ProblemID,
            //         Company
            //       ) VALUES(?,?,?,?,?,?,?)",
            //     [
            //       $lastid,
            //       $p['CurID'],
            //       $date,
            //       $shift,
            //       $m['MoldID'],
            //       $checkRemark,
            //       $_SESSION["user_company"]
            //     ]
            //   );
            // }
          }
        }

        if (!$insertSchTable) {
          sqlsrv_rollback($conn);
          return false;
        }

        sqlsrv_commit($conn);
        return true;
      } else {

        $checkdataSch = Sqlsrv::hasRows(
          $conn,
          "SELECT * FROM ProductionSchTable
          WHERE SchDate=?
          AND Shift=?
          AND Company=?
          ORDER BY SchDate DESC",
          [
            $date_gen,
            $shift_gen,
            $_SESSION["user_company"]
          ]
        );

        if ($checkdataSch) {

          $dataSchInfo = Sqlsrv::queryArray(
            $conn,
            "SELECT P1.* FROM ProductionSchTable P1
            LEFT JOIN ProductionSchCure P2 ON P2.CurID = P1.Boiler
                        WHERE SchDate = ?
                        AND P1.Shift= ?
                        AND P1.Company= ?
                        AND P2.Active = 1",
            [
              $date_gen,
              $shift_gen,
              $_SESSION["user_company"]
            ]
          );

          if ($gen_emp === "1") {
            $date_temp = $date_emp;
            $shift_temp = $shift_emp;
          } else {
            $date_temp = $date_gen;
            $shift_temp = $shift_gen;
          }

          $dataEmployeeInfo = Sqlsrv::queryArray(
            $conn,
            "SELECT * FROM ProductionSchEmployee
            WHERE SCHDate=?
            AND Shift=?
            AND Company=?",
            [
              $date_temp,
              $shift_temp,
              $_SESSION["user_company"]
            ]
          );

          // $dataRemarkInfo = Sqlsrv::queryArray(
          //   $conn,
          //   "SELECT *  FROM ProductionSchProblem
          //   WHERE SCHDate=?
          //   AND Shift=?
          //   AND Company=?",
          //   [
          //     $date_gen,
          //     $shift_gen,
          //     $_SESSION["user_company"]
          //   ]
          // );

          foreach ($dataSchInfo as $key => $value) {

            $insertSch = sqlsrv_query(
              $conn,
              "INSERT INTO ProductionSchTable (
                  Boiler,
                  ItemID,
                  Time,
                  Target,
                  -- Actual,
                  -- Scrap,
                  Weight,
                  SchDate,
                  Shift,
                  MoldID,
                  CreateDate,
                  CreateBy,
                  Status,
                  Company,
                  ShiftFor
                ) VALUES(?,?,?,?,?,?,?,?,getdate(),?,?,?,?) ; SELECT SCOPE_IDENTITY()",
              [
                $value['Boiler'],
                $value['ItemID'],
                $value['Time'],
                $value['Target'],
                // $value['Actual'],
                // $value['Scrap'],
                $value['Weight'],
                $date,
                $shift,
                $value['MoldID'],
                $_SESSION["user_login"],
                1,
                $_SESSION["user_company"],
                $shift_for
              ]
            );

            sqlsrv_next_result($insertSch);
            sqlsrv_fetch($insertSch);
            $lastid = sqlsrv_get_field($insertSch, 0);

            $checkEmployee = checkEmployee($dataEmployeeInfo, $value['SchDate'], $value['Shift'], $value['Boiler'], $value['MoldID']);
            if ($checkEmployee) {

              $insertEmployee = sqlsrv_query(
                $conn,
                "INSERT INTO ProductionSchEmployee(
                    TransID,
                    BoilerID,
                    SCHDate,
                    Shift,
                    Mold,
                    EmployeeID,
                    Company
                  ) VALUES(?,?,?,?,?,?,?)",
                [
                  $lastid,
                  $value['Boiler'],
                  $date,
                  $shift,
                  $value['MoldID'],
                  $checkEmployee,
                  $_SESSION["user_company"]
                ]
              );
            }

            // $checkRemark = checkRemark($dataRemarkInfo, $value['SchDate'], $value['Shift'], $value['Boiler'], $value['MoldID']);
            // if ($checkRemark) {

            //   $insertProblem = sqlsrv_query(
            //     $conn,
            //     "INSERT INTO ProductionSchProblem(
            //         TransID,
            //         BoilerID,
            //         SCHDate,
            //         Shift,
            //         Mold,
            //         ProblemID,
            //         Company
            //       ) VALUES(?,?,?,?,?,?,?)",
            //     [
            //       $lastid,
            //       $value['Boiler'],
            //       $date,
            //       $shift,
            //       $value['MoldID'],
            //       $checkRemark,
            //       $_SESSION["user_company"]
            //     ]
            //   );
            // }
          }

          // exit();

          if (!$insertSch) {
            sqlsrv_rollback($conn);
            return false;
          }

          sqlsrv_commit($conn);
          return true;
        } else {
          return false;
        }
      }
    } catch (Exception $e) {
      return false;
    }
  }

  public function getMold($mold)
  {
    preg_match('/[^0-9]*([0-9]+)[^0-9]*/', $mold, $regs);
    $number = (intval($regs[1])) + 1;
    $str = substr($mold, 0, 1);

    return $str . $number;
  }

  public function CopySchTable($boiler, $date_sch, $shift, $type)
  {
    $db = new Connector;
    $conn = $db->dbConnect();

    $query = Sqlsrv::queryArray(
      $conn,
      "SELECT TOP 1 * FROM ProductionSchTable
      WHERE Boiler=? AND SchDate=? AND Shift=? AND Status=? AND Company=?
      ORDER BY MoldID DESC",
      [$boiler, $date_sch, $shift, 1, $_SESSION["user_company"]]
    );

    if ($query == false) {
      return false;
      exit();
    }

    $boiler   = $query[0]['Boiler'];
    $employee = $query[0]['Employee'];
    $schdate  = $query[0]['SchDate'];
    $shift    = $query[0]['Shift'];
    // $mold     = ($query[0]['MoldID']+1);
    $mold     = self::getMold($query[0]['MoldID']);

    $company  = $query[0]['Company'];

    if ($type == 'user') {

      $insert = sqlsrv_query(
        $conn,
        "INSERT INTO ProductionSchTable (Boiler,Employee,SchDate,Shift,CreateDate,CreateBy,MoldID,Status,Company) VALUES(?,?,?,?,getdate(),?,?,?,?)",
        [$boiler, $employee, $schdate, $shift, $_SESSION["user_login"], $mold, 1, $company]
      );
    } else {

      $insert = sqlsrv_query(
        $conn,
        "INSERT INTO ProductionSchTable (Boiler,Employee,Time,Target,Actual,Scrap,Weight,SchDate,Shift,CreateDate,CreateBy,MoldID,Status,Company) VALUES(?,?,?,?,?,?,?,?,?,getdate(),?,?,?,?)",
        [$boiler, $employee, 0, 0, 0, 0, 0, $schdate, $shift, $_SESSION["user_login"], $mold, 1, $company]
      );
    }

    if ($insert) {
      return true;
    } else {
      return false;
    }
  }

  public function InsertEmployeeSchTable($idtrans, $boiler, $date_sch, $shift, $mold, $code)
  {
    $db = new Connector;
    $conn = $db->dbConnect();

    // $check_emp = Sqlsrv::hasRows(
    //   $conn,
    //   "SELECT * FROM ProductionSchEmployee
    //   WHERE BoilerID=? AND SCHDate=? AND Shift=? AND Mold LIKE '%$mold%' AND Company=?",[$boiler,$date_sch,$shift,$_SESSION["user_company"]]
    // );

    // if ($check_emp==true) {
    $delete = sqlsrv_query(
      $conn,
      "DELETE FROM ProductionSchEmployee
          WHERE BoilerID=? AND SCHDate=? AND Shift=? AND Company=? AND Mold LIKE '%$mold%' ",
      [$boiler, $date_sch, $shift, $_SESSION["user_company"]]
    );
    // }

    $query = Sqlsrv::queryArray(
      $conn,
      "SELECT * FROM ProductionSchTable
        WHERE Boiler=? AND SCHDate=? AND Shift=? AND Company=? AND MoldID LIKE '%$mold%'",
      [$boiler, $date_sch, $shift, $_SESSION["user_company"]]
    );

    foreach ($code as $value) {
      foreach ($query as $m) {
        $insert = sqlsrv_query(
          $conn,
          "INSERT INTO ProductionSchEmployee (TransID,BoilerID,SchDate,Shift,Mold,EmployeeID,Company) VALUES(?,?,?,?,?,?,?)",
          [$m['ID'], $boiler, $date_sch, $shift, $m['MoldID'], $value, $_SESSION["user_company"]]
        );
      }
    }

    if ($delete) {
      return true;
    } else {
      return false;
    }
  }

  public function InsertRemarkSchTable($transid, $boiler, $date_sch, $shift, $mold, $code)
  {
    $db = new Connector;
    $conn = $db->dbConnect();

    $check_emp = Sqlsrv::hasRows(
      $conn,
      "SELECT * FROM ProductionSchProblem
        WHERE TransID=? AND BoilerID=? AND SCHDate=? AND Shift=? AND Mold=? AND Company=?",
      [$transid, $boiler, $date_sch, $shift, $mold, $_SESSION["user_company"]]
    );

    if ($check_emp == true) {
      $delete = sqlsrv_query(
        $conn,
        "DELETE FROM ProductionSchProblem
          WHERE TransID=? AND BoilerID=? AND SCHDate=? AND Shift=? AND Mold=? AND Company=?",
        [$transid, $boiler, $date_sch, $shift, $mold, $_SESSION["user_company"]]
      );
    }

    foreach ($code as $value) {
      $insert = sqlsrv_query(
        $conn,
        "INSERT INTO ProductionSchProblem (TransID,BoilerID,SchDate,Shift,Mold,ProblemID,Company) VALUES(?,?,?,?,?,?,?)",
        [$transid, $boiler, $date_sch, $shift, $mold, $value, $_SESSION["user_company"]]
      );
    }

    if ($insert) {
      return true;
    } else {
      return false;
    }
  }

  public function InsertItemSchTable($itemid, $ratecure, $netweight, $actual = 0, $scrap = 0, $id)
  {
    $db = new Connector;
    $conn = $db->dbConnect();

    $getTime = Sqlsrv::queryArray(
      $conn,
      "SELECT * FROM ProductionSchItemMaster
        WHERE ID=?",
      [$itemid]
    );

    // $minute  = $getTime[0]['TimeMinute'];
    // $hour    = $getTime[0]['TimeHour'];
    $minute  = $getTime[0]['Time'];

    // if ($hour == 8) {
    //   $cal_target = ($ratecure / 12) * 8;
    //   $target     = round($cal_target);
    // } else {
    $target     = $ratecure;
    // }

    $insert = sqlsrv_query(
      $conn,
      "UPDATE ProductionSchTable SET ItemID = ?, [Time] = ? , Target = ? , Weight = ? , UpdateBy = ?, UpdateDate = getdate(), Actual = ?, Scrap = ?
        WHERE ID = ?",
      [$itemid, $minute, $target, $netweight, $_SESSION["user_login"], $actual, $scrap, $id]
    );

    if ($insert) {
      return true;
    } else {
      return false;
    }
  }

  public function UpdateSchTable($time, $target, $actual1, $actual2, $actual, $scrap, $weight, $arms, $item, $id)
  {
    $db = new Connector;
    $conn = $db->dbConnect();

    $query = Sqlsrv::hasRows(
      $conn,
      "SELECT * FROM ProductionSchTable
        WHERE ID=? AND Status=?",
      [$id, 3]
    );

    if ($query == true) {
      return false;
      exit();
    }

    $queryarms = Sqlsrv::queryArray(
      $conn,
      "SELECT TOP 1 * FROM ProductionSchTable
        WHERE ID=?",
      [$id]
    );

    $boiler  = $queryarms[0]['Boiler'];
    $schdate = $queryarms[0]['SchDate'];
    $shift   = $queryarms[0]['Shift'];

    $checkarms = Sqlsrv::hasRows(
      $conn,
      "SELECT TOP 1 * FROM ProductionSchTable
        WHERE SchDate=? AND Shift=? AND Boiler=? AND MoldID=? AND ItemID=? AND ID=? AND Company=?",
      [$schdate, $shift, $boiler, $arms, $item, $id, $_SESSION["user_company"]]
    );

    if ($checkarms == false) {

      $data_item = self::loaditem_by($item);
      $ratecure = $data_item[0]['RateCure'];
      $netweight = $data_item[0]['NetWeight'];
      return self::InsertItemSchTable($item, $ratecure, $netweight, $actual, $scrap, $id);
    } else {

      $update = sqlsrv_query(
        $conn,
        "UPDATE ProductionSchTable SET MoldID = ?, [Time] = ?, Target = ?, Actual1 = ?, Actual2 = ?, Actual = ?, Scrap = ?, Weight = ?,
          ItemID = ?, UpdateBy = ?, UpdateDate = getdate()
          WHERE ID = ?",
        [$arms, $time, $target, $actual1, $actual2, $actual, $scrap, $weight, $item, $_SESSION["user_login"], $id]
      );

      if ($update) {
        return true;
      } else {
        return false;
      }
    }
  }

  public function CountSchTable($id)
  {
    $db = new Connector;
    $conn = $db->dbConnect();

    $date = date('Y-m-d', strtotime($date_sch));

    $getdata = Sqlsrv::queryArray(
      $conn,
      "SELECT * FROM ProductionSchTable
        WHERE ID=?",
      [$id]
    );
    $boiler  = $getdata[0]['Boiler'];
    $schdate = $getdata[0]['SchDate'];
    $shift   = $getdata[0]['Shift'];

    $query = Sqlsrv::queryArray(
      $conn,
      "SELECT * FROM ProductionSchTable
        WHERE Boiler=? AND SchDate=? AND Shift=?",
      [$boiler, $schdate, $shift]
    );

    return $query;
  }

  public function DeleteSchTable($id)
  {
    $db = new Connector;
    $conn = $db->dbConnect();

    $delete = sqlsrv_query(
      $conn,
      "DELETE FROM ProductionSchTable
        WHERE ID = ?",
      [$id]
    );

    if ($delete) {
      return true;
    } else {
      return false;
    }
  }

  public function DeleteEmployee($id)
  {
    $db = new Connector;
    $conn = $db->dbConnect();

    $delete = sqlsrv_query(
      $conn,
      "DELETE FROM ProductionSchEmployee
        WHERE TransID = ?",
      [$id]
    );

    if ($delete) {
      return true;
    } else {
      return false;
    }
  }

  public function checkcompleteSchTable($date_sch, $shift, $status)
  {
    $db = new Connector;
    $conn = $db->dbConnect();
    $date_sch = date('Y-m-d', strtotime($date_sch));

    $query = Sqlsrv::hasRows(
      $conn,
      "SELECT * FROM ProductionSchTable
      WHERE SchDate=? AND Shift=? AND Status=? AND Company=?",
      [$date_sch, $shift, $status, $_SESSION["user_company"]]
    );

    if ($query) {
      return true;
    } else {
      return false;
    }
  }

  public function loaddate()
  {
    $db = new Connector;
    $conn = $db->dbConnect();

    $query = Sqlsrv::queryJson(
      $conn,
      "SELECT SchDate,Shift,Status
        ,CASE
        WHEN Shift=1 THEN 'กลางวัน'
        WHEN Shift=2 THEN 'กลางคืน'
        END[ShiftName]
        ,CASE
        WHEN Status=1 THEN 'รออนุมัติ'
        WHEN Status=3 THEN 'อนุมัติ'
        END[StatusName]
        FROM ProductionSchTable
        WHERE [Time] IS NOT NULL
        AND Target IS NOT NULL
        AND Actual IS NOT NULL
        AND Scrap IS NOT NULL
        AND Weight IS NOT NULL
        AND Company = ?
        GROUP BY SchDate,Shift,Status
        ORDER BY SchDate,Shift DESC",
      [$_SESSION["user_company"]]
    );

    return $query;
  }

  public function listboiler($datesch, $shift)
  {
    $db = new Connector;
    $conn = $db->dbConnect();

    $query = Sqlsrv::queryJson(
      $conn,
      "SELECT P.ID,P.Boiler,P.MoldID
        FROM ProductionSchTable P
        LEFT JOIN Employee E ON P.Employee=E.Code
        LEFT JOIN ProductionSchItemMaster I ON P.ItemID=I.ID
        LEFT JOIN ProductionSchCure C ON P.Boiler=C.CurID
        WHERE P.SchDate=? AND P.Shift=?
        AND P.Time IS NULL
        AND P.Target IS NULL
        AND P.Actual IS NULL
        AND P.Scrap IS NULL
        AND P.Weight IS NULL
        ORDER BY C.ID,P.MoldID ASC",
      [$datesch, $shift]
    );

    return $query;
  }

  public function CompleteSchTable($date_sch, $shift)
  {
    $db = new Connector;
    $conn = $db->dbConnect();

    $complete = sqlsrv_query(
      $conn,
      "UPDATE ProductionSchTable SET Status = ?, UpdateBy = ?, UpdateDate = getdate()
        WHERE SchDate = ? AND Shift = ?  AND Company = ?",
      [3, $_SESSION["user_login"], $date_sch, $shift, $_SESSION['user_company']]
    );

    if ($complete) {
      return true;
    } else {
      return false;
    }
  }

  public function UpdateSchTableList($id)
  {
    $db = new Connector;
    $conn = $db->dbConnect();

    foreach ($id as $value) {
      $update = sqlsrv_query(
        $conn,
        "UPDATE ProductionSchTable SET [Time] = ?, Target = ?, Actual = ?, Scrap = ?, Weight = ?, UpdateBy = ?, UpdateDate = getdate()
        WHERE ID = ?",
        [0, 0, 0, 0, 0, $_SESSION["user_login"], $value]
      );
    }

    if ($update) {
      return true;
    } else {
      return false;
    }
  }

  public function UpdateTime($id, $hours, $active)
  {
    $db = new Connector;
    $conn = $db->dbConnect();

    $minute = ($hours * 60);

    $update = sqlsrv_query(
      $conn,
      "UPDATE ProductionSchTimeMaster SET TimeHour = ?, TimeMinute = ? , Active = ?
      WHERE TimeID = ?",
      [$hours, $minute, $active, $id]
    );

    if ($active = 1) {
      $updateactive = sqlsrv_query(
        $conn,
        "UPDATE ProductionSchTimeMaster SET Active = ?
        WHERE TimeID != ?",
        [0, $id]
      );
    }

    if ($update) {
      return true;
    } else {
      return false;
    }
  }

  public function UpdateItem($id, $color1, $color2, $color3, $color4, $color5)
  {
    $db = new Connector;
    $conn = $db->dbConnect();

    $update = sqlsrv_query(
      $conn,
      "UPDATE ProductionSchItemMaster SET Color1 = ?, Color2 = ? , Color3 = ? , Color4 = ? , Color5 = ?
      WHERE ID = ?",
      [$color1, $color2, $color3, $color4, $color5, $id]
    );

    if ($update) {
      return true;
    } else {
      return false;
    }
  }

  public function UpdateItemGT($id, $color)
  {
    $db = new Connector;
    $conn = $db->dbConnect();

    $update = sqlsrv_query(
      $conn,
      "UPDATE ProductionSchGreentireMaster SET ColorAll = ?
      WHERE Id = ?",
      [$color, $id]
    );

    if ($update) {
      return true;
    } else {
      return false;
    }
  }

  public function UpdateRemark($id, $name)
  {
    $db = new Connector;
    $conn = $db->dbConnect();

    $update = sqlsrv_query(
      $conn,
      "UPDATE ProductionSchProblemMaster SET Description = ?
      WHERE ID = ?",
      [$name, $id]
    );

    if ($update) {
      return true;
    } else {
      return false;
    }
  }

  public function CreateRemark($name)
  {
    $db = new Connector;
    $conn = $db->dbConnect();

    $getID = Sqlsrv::queryArray(
      $conn,
      "SELECT TOP 1
      SUBSTRING(ProblemID,1,3) AS Format,
      SUBSTRING(ProblemID,4,3)+1 AS No
      FROM ProductionSchProblemMaster
      WHERE Company=?
      ORDER BY ID DESC",
      [$_SESSION["user_company"]]
    );

    $id  = $getID[0]['Format'] . $getID[0]['No'];

    $create = sqlsrv_query(
      $conn,
      "INSERT INTO ProductionSchProblemMaster(ProblemID,Description,Company) VALUES(?,?,?)",
      [$id, $name, $_SESSION["user_company"]]
    );

    if ($create) {
      return true;
    } else {
      return false;
    }
  }

  public function DeleteRemark($id, $problemid)
  {
    $db = new Connector;
    $conn = $db->dbConnect();

    $checkdelete = Sqlsrv::hasRows(
      $conn,
      "SELECT * FROM ProductionSchProblem WHERE ProblemID = ? AND Company = ? ",
      [$problemid, $_SESSION["user_company"]]
    );

    if ($checkdelete) {
      echo json_encode([
        "result" => 404,
        "message" => "ProblemID In Transaction!"
      ]);
      exit();
    }

    $delete = sqlsrv_query(
      $conn,
      "DELETE FROM ProductionSchProblemMaster WHERE ID = ? ",
      [$id]
    );

    if ($delete) {
      echo json_encode([
        "result" => 200,
        "message" => "DeleteProblem Successful"
      ]);
    } else {
      echo json_encode([
        "result" => 404,
        "message" => "DeleteProblem Failed!"
      ]);
    }
  }

  public function getParentID($company)
  {
    $db = new Connector;
    $conn = $db->dbConnect();

    $query = Sqlsrv::queryArray(
      $conn,
      "SELECT TOP 1 ParentID FROM Employee
      WHERE Company=?
      ORDER BY ID DESC ",
      [$company]
    );

    return $query[0]['ParentID'] + 1;
  }

  public function SyncEmployee($company)
  {
    $db = new Connector;
    $conn = $db->dbConnect();

    if ($company == 'DSL') {
      $codecomp = '100100';
    }

    $query = Sqlsrv::queryArray(
      $conn,
      "SELECT
          E.CODEMPID
          ,E.EMPNAME
          ,E.EMPLASTNAME
          ,S.DIVISIONID
          ,E.DEPARTMENTCODE
          ,E.COMPANYNAME
      FROM [HRTRAINING].[dbo].[Employee] E
          LEFT JOIN [HRTRAINING].[dbo].[DEPARTMENT] D ON E.DEPARTMENTCODE=D.DEPARTMENTCODE
          LEFT JOIN [HRTRAINING].[dbo].[DIVISION] S ON E.DIVISIONCODE=S.DIVISIONCODE
          LEFT JOIN [HRTRAINING].[dbo].[TEMPLOY1] T ON E.CODEMPID=T.CODEMPID
      WHERE E.STATUS != 9
      AND E.COMPANYNAME = ?
      AND S.DIVISIONID = ?
      AND E.CODEMPID NOT IN
        (
          SELECT Code FROM Employee WHERE Company=?
        )",
      [$company, $codecomp, $company]
    );

    if (count($query) != 0) {

      foreach ($query as $key => $value) {
        $insert = sqlsrv_query(
          $conn,
          "INSERT INTO Employee(ParentID,Code,FirstName,LastName,DivisionCode,DepartmentCode,Company,EmpStatus)
          VALUES(?,?,?,?,?,?,?,0)",
          [
            self::getParentID($value['COMPANYNAME']),
            $value['CODEMPID'],
            $value['EMPNAME'],
            $value['EMPLASTNAME'],
            $value['DIVISIONID'],
            $value['DEPARTMENTCODE'],
            $value['COMPANYNAME']
          ]
        );
      }

      if ($insert) {
        return json_encode([
          "result" => true,
          "message" => "SyncEmployee Successful"
        ]);
      } else {
        return json_encode([
          "result" => false,
          "message" => "SyncEmployee Failed!"
        ]);
      }
    }
  }

  public function getmasterreportsch($date, $shift)
  {
    $db = new Connector;
    $conn = $db->dbConnect();
    $date = date('Y-m-d', strtotime($date));

    $query = Sqlsrv::queryJson(
      $conn,
      "SELECT * FROM ProductionSchReportMaster
      WHERE SCHDate=? AND Shift=? AND Company=?",
      [$date, $shift, $_SESSION['user_company']]
    );

    return $query;
  }

  public function createmastersch(
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
  ) {

    $db = new Connector;
    $conn = $db->dbConnect();
    $SCHDate = date('Y-m-d', strtotime($SCHDate));

    $delete = sqlsrv_query(
      $conn,
      "DELETE FROM ProductionSchReportMaster
      WHERE SCHDate=? AND Shift=? AND Company=?",
      [$SCHDate, $Shift, $_SESSION['user_company']]
    );

    $insert = sqlsrv_query(
      $conn,
      "INSERT INTO ProductionSchReportMaster
      ( Senior,
        SectionHead,
        EmpBladder,
        EmpCuringBack,
        Auditor,
        EmpMantain,
        EmpCuring,
        EmpCutting,
        EmpWarehoure,
        EmpWorking,
        EmpSummer,
        EmpSeak,
        EmpLeave,
        EmpNoInfo,
        SCHDate,
        Shift,
        Company,
        Remark
      )
      VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
      [
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
        $_SESSION['user_company'],
        $Remark
      ]
    );

    if ($insert) {
      return json_encode([
        "result" => true,
        "message" => "Successful"
      ]);
    } else {
      return json_encode([
        "result" => false,
        "message" => "Failed!"
      ]);
    }
  }

  public function SendMail($mailTo = [], $mailCC = [], $BCC = [], $subject = '', $body = '', $sender = '')
  {
    if ($sender === '') {
      $sender_mail = 'ea_devteam@deestone.com';
    } else {
      $sender_mail = $sender;
    }

    $mail = new \PHPMailer;
    $mail->isSMTP();
    // $mail->SMTPDebug = 2;
    $mail->Host = '20.20.20.3';
    $mail->SMTPAuth = true;
    $mail->Username = 'ea_devteam@deestone.com';
    $mail->Password = 'E.D.ev53team9341';
    $mail->SMTPSecure = 'ssl';
    $mail->SMTPOptions = array(
      'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
      )
    );
    $mail->Port = 465;
    $mail->CharSet = "utf-8";
    $mail->From = $sender_mail;
    $mail->FromName = $sender_mail;
    $mail->Sender = 'ea_devteam@deestone.com';
    // $mail->setFrom('ea_devteam@deestone.com', $sender_mail);
    // $mail->addReplyTo($sender_mail);


    if (count($mailTo) > 0) {
      foreach ($mailTo as $customerMailTo) {
        $mail->addAddress($customerMailTo);
      }
    } else {
      return ['result' => false, 'message' => 'No recipients mail.'];
    }

    if (count($mailCC) > 0) {
      foreach ($mailCC as $customerMailCC) {
        $mail->addCC($customerMailCC);
      }
    }

    if (count($BCC) > 0) {
      foreach ($BCC as $MAIL_BCC) {
        $mail->addBCC($MAIL_BCC);
      }
    }

    $mail->isHTML(true);
    $mail->Subject =  $subject;

    $mail->Body    = $body;

    if (!$mail->send()) {
      // echo json_encode(["status" => 404, "message" => $mail->ErrorInfo]);
      return false;
    } else {
      // echo json_encode(["status" => 200, "message" => "Send Success"]);
      return true;
    }
  }

  public function ClearList($id)
  {
    $db = new Connector;
    $conn = $db->dbConnect();

    $checkIdRemark = Sqlsrv::hasRows(
      $conn,
      "SELECT * FROM ProductionSchProblem
        WHERE TransID=?",
      [$id]
    );

    $checkIdEmp = Sqlsrv::hasRows(
      $conn,
      "SELECT * FROM ProductionSchEmployee
        WHERE TransID=?",
      [$id]
    );

    if ($checkIdRemark) {
      $deleteRemark = sqlsrv_query(
        $conn,
        "DELETE FROM ProductionSchProblem WHERE TransID = ?",
        [$id]
      );
    }

    if ($checkIdEmp) {
      $DeleteEmp = sqlsrv_query(
        $conn,
        "DELETE FROM ProductionSchEmployee WHERE TransID = ?",
        [$id]
      );
    }

    $update = sqlsrv_query(
      $conn,
      "UPDATE ProductionSchTable SET ItemID = ?, [Time] = ?, Target = ?, Actual1 = ?, Actual2 = ?, Actual = ?, Scrap = ?, Weight = ?, UpdateBy = ?, UpdateDate = getdate()
        WHERE ID = ?",
      [NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, $_SESSION["user_login"], $id]
    );

    if ($update) {
      return true;
    } else {
      return false;
    }
  }

  public function DeleteRemarkId($id)
  {
    $db = new Connector;
    $conn = $db->dbConnect();

    $delete = sqlsrv_query(
      $conn,
      "DELETE FROM ProductionSchProblem WHERE TransID = ? ",
      [$id]
    );

    if ($delete) {
      echo json_encode([
        "result" => 200,
        "message" => $id
      ]);
    } else {
      echo json_encode([
        "result" => 404,
        "message" => "DeleteProblem Failed!"
      ]);
    }
  }

  public function DeleteEmployeeId($id)
  {
    $db = new Connector;
    $conn = $db->dbConnect();

    $delete = sqlsrv_query(
      $conn,
      "DELETE FROM ProductionSchEmployee WHERE TransID = ? ",
      [$id]
    );

    if ($delete) {
      echo json_encode([
        "result" => 200,
        "message" => $id
      ]);
    } else {
      echo json_encode([
        "result" => 404,
        "message" => "DeleteEmployee Failed!"
      ]);
    }
  }

  public function confirmSch($date, $shift, $status)
  {
    $db = new Connector;
    $conn = $db->dbConnect();
    $date = date('Y-m-d', strtotime($date));

    $query = Sqlsrv::hasRows(
      $conn,
      "SELECT * FROM ProductionSchConfirm
      WHERE SchDate=? AND Shift=? AND Active=?",
      [$date, $shift, 1]
    );

    if ($query) {

      $update = sqlsrv_query(
        $conn,
        "UPDATE ProductionSchConfirm
        SET Active=?
        WHERE SCHDate=? AND Shift=?",
        [0, $date, $shift]
      );

      if ($update) {

        $insert = sqlsrv_query(
          $conn,
          "INSERT INTO ProductionSchConfirm
          (
            SCHDate,
            Shift,
            Status,
            CreateBy,
            CreateDate,
            Active
          )
          VALUES (?,?,?,?,getdate(),?)",
          [
            $date,
            $shift,
            $status,
            $_SESSION['user_login'],
            1
          ]
        );
        if ($insert) {
          return json_encode([
            "result" => true,
            "message" => "Successful"
          ]);
        } else {
          return json_encode([
            "result" => false,
            "message" => "Failed!"
          ]);
        }
      }
    } else {

      $insert = sqlsrv_query(
        $conn,
        "INSERT INTO ProductionSchConfirm
        (
          SCHDate,
          Shift,
          Status,
          CreateBy,
          CreateDate,
          Active
        )
        VALUES (?,?,?,?,getdate(),?)",
        [
          $date,
          $shift,
          $status,
          $_SESSION['user_login'],
          1
        ]
      );

      if ($insert) {
        return json_encode([
          "result" => true,
          "message" => "Successful"
        ]);
      } else {
        return json_encode([
          "result" => false,
          "message" => "Failed!"
        ]);
      }
    }
  }

  public function checkconfirmSch($date_sch, $shift)
  {
    $db = new Connector;
    $conn = $db->dbConnect();
    $date = date('Y-m-d', strtotime($date_sch));

    $queryConfirm = Sqlsrv::hasRows(
      $conn,
      "SELECT * FROM ProductionSchConfirm
      WHERE SchDate=? AND Shift=? AND Status=? AND Active=?",
      [$date, $shift, 1, 1]
    );

    $queryConfirmed = Sqlsrv::hasRows(
      $conn,
      "SELECT * FROM ProductionSchConfirm
      WHERE SchDate=? AND Shift=? AND Status=? AND Active=?",
      [$date, $shift, 2, 1]
    );

    $queryUnlock = Sqlsrv::hasRows(
      $conn,
      "SELECT * FROM ProductionSchConfirm
      WHERE SchDate=? AND Shift=? AND Status=? AND Active=?",
      [$date, $shift, 3, 1]
    );


    if ($queryUnlock === true) {
      return json_encode([
        "result" => 3,
        "message" => "Unlock"
      ]);
    }

    if ($queryConfirmed === true) {
      return json_encode([
        "result" => 2,
        "message" => "Confirmed"
      ]);
    }

    if ($queryConfirm === true) {
      return json_encode([
        "result" => 1,
        "message" => "Confirm"
      ]);
    }

    if ($queryConfirm === false && $queryConfirmed === false && $queryUnlock === false) {
      return json_encode([
        "result" => 4,
        "message" => "Data Notfound Confirm"
      ]);
    }
  }

  public function UpdateSchTable2($billuse, $billgive, $faceBoiler, $date_sch, $shift, $id)
  {
    if ($shift == 1) {
      $dateref = date('Y-m-d', strtotime($date_sch . ' -1 days'));
      $shifref = 2;
      $datenext =  $date_sch;
      $shiftnext = 2;
    } else {
      $dateref =  $date_sch;
      $shifref = 1;
      $datenext =  date('Y-m-d', strtotime($date_sch . ' +1 days'));
      $shiftnext = 1;
    }

    $db = new Connector;
    $conn = $db->dbConnect();






    $update = sqlsrv_query(
      $conn,
      "UPDATE ProductionSchTable SET BillUse = ?, BillGive = ?, faceBoiler = ?, UpdateBy = ?, UpdateDate = getdate()
          WHERE ID = ?",
      [$billuse, $billgive, $faceBoiler, $_SESSION["user_login"], $id]
    );

    $updateprintsch = sqlsrv_query(
      $conn,
      "UPDATE T
      SET  T.CountCure = A.BillUse
      FROM ProductionGreentirePrintTable T
      LEFT JOIN(
      SELECT PR.ItemId,  
      Case WHEN X.BillUse IS NULL THEN X2.BillGive ELSE X.BillUse END AS BillUse
       FROM ProductionGreentirePrintTable PR
      LEFT JOIN (
        SELECT 
          GM.ItemGT,
          --SUM(PT.BillGive) AS BillGive,
          SUM(PT.BillUse) AS BillUse
          --SUM(PT.faceBoiler) AS faceBoiler 
          FROM ProductionSchTable  PT
          LEFT JOIN ProductionSchGreentireMaster GM ON PT.ItemID = GM.ItemFG
          WHERE PT.SchDate = ? AND PT.Shift = ? AND PT.ItemID IS NOT NULL
          GROUP BY GM.ItemGT) X ON PR.ItemId= X.ItemGT

      LEFT JOIN (
        SELECT 
        GM.ItemGT,
        SUM(PT.BillGive) AS BillGive
        --SUM(PT.BillUse) AS BillUse
        --SUM(PT.faceBoiler) AS faceBoiler 
        FROM ProductionSchTable  PT
        LEFT JOIN ProductionSchGreentireMaster GM ON PT.ItemID = GM.ItemFG
        WHERE PT.SchDate = ? AND PT.Shift = ? AND PT.ItemID IS NOT NULL
        GROUP BY GM.ItemGT) X2 ON PR.ItemId= X2.ItemGT
        WHERE PR.Sch_date = ? AND PR.Shift = ?)A ON T.ItemId = A.ItemId 
      WHERE T.Sch_date = ? AND T.Shift = ?",
      [
        $date_sch,
        $shift,
        $dateref,
        $shifref,
        $date_sch,
        $shift,
        $date_sch,
        $shift

      ]
    );

    $updateprintsch2 = sqlsrv_query(
      $conn,
      "UPDATE T
        SET  T.SpareOfcure = A.faceBoiler,
        T.CountCure = A.BillGive
      FROM ProductionGreentirePrintTable T
      LEFT JOIN(
        SELECT PR.ItemId,  
          X.faceBoiler,
          X.BillGive
        FROM ProductionGreentirePrintTable PR
          LEFT JOIN (
          SELECT 
            GM.ItemGT,
            SUM(PT.BillGive) AS BillGive,
            --SUM(PT.BillUse) AS BillUse
            SUM(PT.faceBoiler) AS faceBoiler 
            FROM ProductionSchTable  PT
            LEFT JOIN ProductionSchGreentireMaster GM ON PT.ItemID = GM.ItemFG
            WHERE PT.SchDate = ? AND PT.Shift = ? AND PT.ItemID IS NOT NULL
            GROUP BY GM.ItemGT) X ON PR.ItemId= X.ItemGT
            WHERE PR.Sch_date = ? AND PR.Shift = ?
          )A ON T.ItemId = A.ItemId 
       WHERE T.Sch_date = ? AND T.Shift = ?",
      [
        $date_sch,
        $shift,
        $datenext,
        $shiftnext,
        $datenext,
        $shiftnext


      ]
    );





    if ($updateprintsch2 && $updateprintsch) {
      return true;
    } else {
      return false;
    }
  }

  public function insertedititem($itemGT, $Timcure, $GroupItem, $counprint, $FGItem)
  {
    $db = new Connector;
    $conn = $db->dbConnect();

    $check_item = Sqlsrv::hasRows(
      $conn,
      "SELECT * FROM ShareMoldMaster
        WHERE ItemId=? ",
      [$itemGT]
    );

    if ($check_item == false) {
      $insertShareItem = sqlsrv_query(
        $conn,
        "INSERT INTO ShareMoldMaster (ItemId,Total,GroupId) VALUES(?,?,?)",
        [
          $itemGT,
          $counprint,
          $GroupItem

        ]
      );
    }
    if ($counprint == NULL || $counprint == "") {
      return false;
    }


    $UpdateItemMaster = sqlsrv_query(
      $conn,
      "UPDATE ProductionSchItemMaster SET Time = ? WHERE  ID = ?",
      [
        $Timcure,
        $FGItem
      ]
    );

    $UpdateItemShareMaster = sqlsrv_query(
      $conn,
      "UPDATE ShareMoldMaster SET [Total] = ?, GroupId = ? WHERE  ItemId = ?",
      [
        $counprint,
        $GroupItem,
        $itemGT

      ]
    );

    $UpdateItemShareMasterGroup = sqlsrv_query(
      $conn,
      "UPDATE ShareMoldMaster SET [Total] = ? WHERE  GroupId = ?",
      [
        $counprint,
        $GroupItem
      ]
    );



    if ($UpdateItemMaster) {
      return true;
    } else {
      return false;
    }
  }
  public function getmasterreportschall($date)
  {
    $db = new Connector;
    $conn = $db->dbConnect();
    $date = date('Y-m-d', strtotime($date));

    $query = Sqlsrv::queryJson(
      $conn,
      "SELECT * FROM ProductionSchReportMaster
      WHERE SCHDate=? 
     -- AND Shift=? 
      AND Company=?",
      [$date, $_SESSION['user_company']]
    );

    return $query;
  }
}
