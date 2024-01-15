<?php

namespace App\V2\TargetGreentire;

use App\V2\Database\Connector;
use App\Common\Sql;

class TargetGreentireAPI
{
  private $conn = null;
  private $sql = null;

  public function __construct()
  {
    $this->conn = new Connector();
    $this->sql = new Sql();
  }

  public function getGreentireLists()
  {
    try {
      $sql = "SELECT
        P.ItemID,
        B.ITEMID AS ITEM_GREENTIRE,
        I.ITEMGROUPID
        FROM ProductionSchTable P
        LEFT JOIN [FREY\LIVE].[DSL_AX40_SP1_LIVE].[dbo].BOMVERSION BV ON BV.ITEMID = P.ItemID AND BV.DATAAREAID = 'dv'
        LEFT JOIN [FREY\LIVE].[DSL_AX40_SP1_LIVE].[dbo].BOM B ON BV.BOMID = B.BOMID AND B.DATAAREAID = 'dv'
        LEFT JOIN [FREY\LIVE].[DSL_AX40_SP1_LIVE].[dbo].INVENTTABLE I ON I.ITEMID = B.ITEMID
        WHERE P.CreateDate >= '2019-09-03 08:00:00'
        AND P.CreateDate <= '2019-09-03 20:00:00'
        AND P.ItemID IS NOT NULL
        AND P.[Target] IS NOT NULL
        GROUP BY
        P.ItemID,
        B.ITEMID,
        I.ITEMGROUPID
        ";

      return $this->sql->rows(
        $this->conn->dbConnect(),
        $sql
      );
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  public function addShiftTrans($date, $shift, $module)
  {
    try {

      if (!isset($_SESSION["user_login"])) {
        throw new \Exception("Session expired.");
      }

      $add = \sqlsrv_query(
        $this->conn->dbConnect(),
        "INSERT INTO ShiftTrans(
          Shift,
          ShiftDate,
          CreateDate,
          CreateBy,
          ModuleId
        ) VALUES(?, ?, ?, ?, ?)",
        [
          $shift,
          $date,
          date("Y-m-d H:i:s"),
          $_SESSION["user_login"],
          $module
        ]
      );

      if ($add) {
        return response(true, "Add success");
      } else {
        return response(false, "Add failed");
      }
    } catch (\Exception $e) {
      return response(false, $e->getMessage());
    }
  }

  public function getShiftTrans($filter)
  {
    try {
      // code
      return $this->sql->rows(
        $this->conn->dbConnect(),
        "SELECT
        S.Id,
        SM.[Description] AS Shift,
        S.ShiftDate,
        S.CreateDate,
        U.Name AS CreateBy,
        S.CreateDate,
        S.UpdateBy,
        S.UpdateDate,
        S.ConfirmBy,
        S.ConfirmDate,
        M.[Description] AS Module,
        ST.[Description] AS [Status]
        FROM ShiftTrans S
        LEFT JOIN ShiftMaster SM ON S.Shift = SM.ID
        LEFT JOIN UserMaster U ON U.ID = S.CreateBy
        LEFT JOIN ModuleMaster M ON M.Id = S.ModuleId
        LEFT JOIN [Status] ST ON ST.ID = S.[Status]
        WHERE $filter
        ORDER BY S.Id DESC"
      );
    } catch (\Exception $e) {
      // exception
      return response(false, $e->getMessage());
    }
  }

  public function getShiftTransById($id)
  {
    try {
      // code
      return $this->sql->rows(
        $this->conn->dbConnect(),
        "SELECT * FROM ShiftTrans
        WHERE Id = ?",
        [
          $id
        ]
      );
    } catch (\Exception $e) {
      return $e->getMessage();
    }
  }

  public function addTargetGreentire($shift, $date, $transId)
  {
    try {

      // delete old
      sqlsrv_query(
        $this->conn->dbConnect(),
        "delete from TargetGreentire where CONVERT(date, ShiftDate) = '$date'"
      );

      $insert_sql = "SELECT
        G.ItemGT AS ITEM_GT,
        G.ItemFG AS ITEM_FG,
        P.Shift,
        P.ID,
        P.[Weight]/1000 AS NETWEIGHT,
        CASE
          WHEN PC.[Target] IS NULL OR P.Shift = 2 THEN 0
          ELSE PC.[Target]
        END AS TARGET_C,
        CASE
          WHEN PC.Shift = 1 AND PC.Actual IS NOT NULL THEN PC.Actual + PC.Scrap
          ELSE 0
        END AS ACTUAL_C,
        CASE
          WHEN PD.[Target] IS NULL OR P.Shift = 1 THEN 0
          ELSE PD.[Target]
        END AS TARGET_D,
        CASE
          WHEN PD.Shift = 2 AND PD.Actual IS NOT NULL THEN PD.Actual + PD.Scrap
          ELSE 0
        END AS ACTUAL_D,
        CASE
          WHEN PC.[Target] IS NULL OR PD.[Target] IS NULL THEN (
            CASE
              WHEN PC.[Target] IS NOT NULL THEN (PC.[Target] * P.[Weight]) / 1000
              WHEN PD.[Target] IS NOT NULL THEN (PD.[Target] * P.[Weight]) / 1000
              ELSE 0
            END
          )
          ELSE (PC.[Target] * P.[Weight]) / 1000 + (PD.[Target] * P.[Weight]) / 1000
        END AS WEIGHT_ALL,
        CASE
          WHEN PC.[Actual] IS NULL OR PD.[Actual] IS NULL THEN (
            CASE
              WHEN PC.[Actual] IS NOT NULL THEN ((PC.[Actual] + PC.Scrap) * P.[Weight]) / 1000
              WHEN PD.[Actual] IS NOT NULL THEN ((PD.[Actual] + PD.Scrap) * P.[Weight]) / 1000
              ELSE 0
            END
          )
          ELSE ( (PC.[Actual] + PC.Scrap) * P.[Weight]) / 1000 + ( (PD.[Actual] + PC.Scrap) * P.[Weight]) / 1000
        END AS WEIGHT_ACTUAL
        FROM ProductionSchTable P
        LEFT JOIN ProductionSchGreentireMaster G
        ON G.ItemFG = P.ItemID
        LEFT JOIN ProductionSchTable PC
        ON PC.SchDate = ?
        AND PC.ItemID IS NOT NULL
        AND PC.Shift = 1
        AND PC.ItemID = P.ItemID
        AND PC.ID = P.ID
        LEFT JOIN ProductionSchTable PD
        ON PD.SchDate = ?
        AND PD.ItemID IS NOT NULL
        AND PD.Shift = 2
        AND PD.ItemID = P.ItemID
        AND PD.ID = P.ID
        LEFT JOIN TargetGreentire TG
        ON G.ItemGT = TG.ItemId
        AND TG.TransId = ?
        WHERE P.SchDate = ?
        AND P.ItemID IS NOT NULL
        AND TG.ItemId IS NULL
        GROUP BY
        G.ItemGT,
        G.ItemFG,
        P.Shift,
        PC.Shift,
        PD.Shift,
        P.ID,
        P.[Weight],
        PC.[Target],
        PD.[Target],
        PC.Actual,
        PD.Actual,
        PC.Scrap,
        PD.Scrap";

      $update_sql = "SELECT
        G.ItemGT AS ITEM_GT,
        G.ItemFG AS ITEM_FG,
        P.Shift,
        P.ID,
        P.[Weight]/1000 AS NETWEIGHT,
        CASE
          WHEN PC.[Target] IS NULL OR P.Shift = 2 THEN 0
          ELSE PC.[Target]
        END AS TARGET_C,
        CASE
          WHEN PC.Shift = 1 AND PC.Actual IS NOT NULL THEN PC.Actual + PC.Scrap
          ELSE 0
        END AS ACTUAL_C,
        CASE
          WHEN PD.[Target] IS NULL OR P.Shift = 1 THEN 0
          ELSE PD.[Target]
        END AS TARGET_D,
        CASE
          WHEN PD.Shift = 2 AND PD.Actual IS NOT NULL THEN PD.Actual + PD.Scrap
          ELSE 0
        END AS ACTUAL_D,
        CASE
          WHEN PC.[Target] IS NULL OR PD.[Target] IS NULL THEN (
            CASE
              WHEN PC.[Target] IS NOT NULL THEN (PC.[Target] * P.[Weight]) / 1000
              WHEN PD.[Target] IS NOT NULL THEN (PD.[Target] * P.[Weight]) / 1000
              ELSE 0
            END
          )
          ELSE ((PC.[Target] * P.[Weight]) / 1000) + ((PD.[Target] * P.[Weight]) / 1000)
        END AS WEIGHT_ALL,
        CASE
          WHEN PC.[Actual] IS NULL OR PD.[Actual] IS NULL THEN (
            CASE
              WHEN PC.[Actual] IS NOT NULL THEN ((PC.[Actual] + PC.Scrap) * P.[Weight]) / 1000
              WHEN PD.[Actual] IS NOT NULL THEN ((PD.[Actual] + PD.Scrap) * P.[Weight]) / 1000
              ELSE 0
            END
          )
          ELSE (((PC.[Actual] + PC.Scrap) * P.[Weight]) / 1000) + (((PD.[Actual] + PC.Scrap) * P.[Weight]) / 1000)
        END AS WEIGHT_ACTUAL
        FROM ProductionSchTable P
        LEFT JOIN ProductionSchGreentireMaster G
        ON G.ItemFG = P.ItemID
        LEFT JOIN ProductionSchTable PC
        ON PC.SchDate = ?
        AND PC.ItemID IS NOT NULL
        AND PC.Shift = 1
        AND PC.ItemID = P.ItemID
        AND PC.ID = P.ID
        LEFT JOIN ProductionSchTable PD
        ON PD.SchDate = ?
        AND PD.ItemID IS NOT NULL
        AND PD.Shift = 2
        AND PD.ItemID = P.ItemID
        AND PD.ID = P.ID
        LEFT JOIN TargetGreentire TG
        ON G.ItemGT = TG.ItemId
        AND TG.TransId = ?
        WHERE P.SchDate = ?
        AND P.ItemID IS NOT NULL
        AND TG.ItemId IS NOT NULL
        GROUP BY
        G.ItemGT,
        G.ItemFG,
        P.Shift,
        PC.Shift,
        PD.Shift,
        P.ID,
        P.[Weight],
        PC.[Target],
        PD.[Target],
        PC.Actual,
        PD.Actual,
        PC.Scrap,
        PD.Scrap";


      $insertNewOne = $this->sql->rows(
        $this->conn->dbConnect(),
        $insert_sql,
        [
          date("Y-m-d", strtotime($date)),
          date("Y-m-d", strtotime($date)),
          $transId,
          date("Y-m-d", strtotime($date))
        ]
      );

      if (count($insertNewOne) > 0) {
        $tempInsert = [];
        foreach ($insertNewOne as $x) {
          if ($x["ITEM_GT"] !== null) {
            if (self::isItemDuplicate($x["ITEM_GT"], $tempInsert) === false) {
              $tempInsert[$x["ITEM_GT"]] = $x;
            } else {
              // shift = 1
              if ($x["ITEM_GT"] === $x["ITEM_GT"] && (int) $x["Shift"] === 1) {
                $tempInsert[$x["ITEM_GT"]]["TARGET_C"] += $x["TARGET_C"];
                $tempInsert[$x["ITEM_GT"]]["ACTUAL_C"] += $x["ACTUAL_C"];
                $tempInsert[$x["ITEM_GT"]]["WEIGHT_ALL"] += $x["WEIGHT_ALL"];
                $tempInsert[$x["ITEM_GT"]]["WEIGHT_ACTUAL"] += $x["WEIGHT_ACTUAL"];
                $tempInsert[$x["ITEM_GT"]]["ITEM_FG"] += $x["ITEM_FG"];
              }

              // shift = 2
              if ($x["ITEM_GT"] === $x["ITEM_GT"] && (int) $x["Shift"] === 2) {
                $tempInsert[$x["ITEM_GT"]]["TARGET_D"] += $x["TARGET_D"];
                $tempInsert[$x["ITEM_GT"]]["ACTUAL_D"] += $x["ACTUAL_D"];
                $tempInsert[$x["ITEM_GT"]]["WEIGHT_ALL"] += $x["WEIGHT_ALL"];
                $tempInsert[$x["ITEM_GT"]]["WEIGHT_ACTUAL"] += $x["WEIGHT_ACTUAL"];
                $tempInsert[$x["ITEM_GT"]]["ITEM_FG"] += $x["ITEM_FG"];
              }
            }
          }
        }

        foreach ($tempInsert as $g) {
          if ($g["ITEM_GT"] !== null) {
            $insert = \sqlsrv_query(
              $this->conn->dbConnect(),
              "INSERT INTO TargetGreentire(TransId, ShiftDate, ItemId, ItemFG, BomCPlan, BomCActual, BomDPlan, BomDActual, WeightPlan, WeightActual)
            VALUES(
              ?, ?, ?, ?, ?,
              ?, ?, ?, ?, ?
            )",
              [
                $transId,
                date("Y-m-d", strtotime($date)),
                $g["ITEM_GT"],
                $g["ITEM_FG"],
                $g["TARGET_C"],
                $g["ACTUAL_C"],
                $g["TARGET_D"],
                $g["ACTUAL_D"],
                $g["WEIGHT_ALL"],
                $g["WEIGHT_ACTUAL"]
              ]
            );

            if (!$insert) {
              throw new \Exception("Insert item : " . var_dump($g) . " error.");
            }
          }
        }
      }


      // ##################################### update #####################################
      $updateExistsRows = $this->sql->rows(
        $this->conn->dbConnect(),
        $update_sql,
        [
          date("Y-m-d", strtotime($date)),
          date("Y-m-d", strtotime($date)),
          $transId,
          date("Y-m-d", strtotime($date))
        ]
      );

      if (count($updateExistsRows) > 0) {
        $tempUpdate = [];

        foreach ($updateExistsRows as $x) {
          if ($x["ITEM_GT"] !== null) {
            if (self::isItemDuplicate($x["ITEM_GT"], $tempUpdate) === false) {
              $tempUpdate[$x["ITEM_GT"]] = $x;
            } else {
              // shift = 1
              if ($x["ITEM_GT"] === $x["ITEM_GT"] && (int) $x["Shift"] === 1) {
                $tempUpdate[$x["ITEM_GT"]]["TARGET_C"] += $x["TARGET_C"];
                $tempUpdate[$x["ITEM_GT"]]["ACTUAL_C"] += $x["ACTUAL_C"];
                $tempUpdate[$x["ITEM_GT"]]["WEIGHT_ALL"] += $x["WEIGHT_ALL"];
                $tempUpdate[$x["ITEM_GT"]]["WEIGHT_ACTUAL"] += $x["WEIGHT_ACTUAL"];
                $tempUpdate[$x["ITEM_GT"]]["ITEM_FG"] = $x["ITEM_FG"];
              }

              // shift = 2
              if ($x["ITEM_GT"] === $x["ITEM_GT"] && (int) $x["Shift"] === 2) {
                $tempUpdate[$x["ITEM_GT"]]["TARGET_D"] += $x["TARGET_D"];
                $tempUpdate[$x["ITEM_GT"]]["ACTUAL_D"] += $x["ACTUAL_D"];
                $tempUpdate[$x["ITEM_GT"]]["WEIGHT_ALL"] += $x["WEIGHT_ALL"];
                $tempUpdate[$x["ITEM_GT"]]["WEIGHT_ACTUAL"] += $x["WEIGHT_ACTUAL"];
                $tempUpdate[$x["ITEM_GT"]]["ITEM_FG"] = $x["ITEM_FG"];
              }
            }
          }
        }

        // echo "<pre>" . print_r($tempUpdate, true) . "</pre>";
        // var_dump($tempInsert);

        // exit("..");

        foreach ($tempUpdate as $g) {
          $update = \sqlsrv_query(
            $this->conn->dbConnect(),
            "UPDATE TG
            SET TG.BomCPlan = ?,
            TG.BomCActual = ?,
            TG.BomDPlan = ?,
            TG.BomDActual = ?,
            TG.WeightPlan = ?,
            TG.WeightActual = ?,
            TG.ItemFG = ?
            FROM TargetGreentire TG
            WHERE TG.ItemId = ?
            AND TG.TransId = ?
            AND TG.ShiftDate = ?",
            [
              $g["TARGET_C"],
              $g["ACTUAL_C"],
              $g["TARGET_D"],
              $g["ACTUAL_D"],
              $g["WEIGHT_ALL"],
              $g["WEIGHT_ACTUAL"],
              $g["ITEM_FG"],
              $g["ITEM_GT"],
              $transId,
              date("Y-m-d", strtotime($date))
            ]
          );

          if (!$update) {
            throw new \Exception("Update item : " . $g["ITEM_GT"] . " error.");
          }
        }
      }
      // echo "Generate Greentire Success.";
      return; //response(true, "Generate Greentire Success.");
    } catch (\Exception $e) {
      echo $e->getMessage(); //response(false, $e->getMessage());s
      return;
    }
  }


  public function isItemDuplicate($item, $arr)
  {
    if (count($arr) > 0) {
      foreach ($arr as $x) {
        if ($x["ITEM_GT"] === $item) {
          return true;
          break;
        }
      }
    }
    return false;
  }

  public function isTargetGreentireGenerated($transId)
  {
    return \sqlsrv_has_rows(\sqlsrv_query(
      $this->conn->dbConnect(),
      "SELECT TransId
      FROM TargetGreentire
      WHERE TransId = ?",
      [
        $transId
      ]
    ));
  }

  public function shiftwork($date)
  {
    return $this->sql->rows(
      $this->conn->dbConnect(),
      "SELECT TOP 1
			  P.ShiftFor
      FROM ProductionSchTable P
      LEFT JOIN ProductionSchItemMaster I ON P.ItemID = I.ID
      LEFT JOIN ProductionSchCure C ON P.Boiler = C.CurID AND P.Company = C.Company
      WHERE CONVERT(date, P.SchDate) = ?
      AND P.Shift = '1'
      AND P.ItemID IS NOT NULL AND P.ItemID != ''
      ORDER BY C.ID,P.MoldID ASC",
      [
        $date
        // $shift

      ]
    );
  }


  public function loadTargetGreentire($shiftDate)
  {
    return $this->sql->rows(
      $this->conn->dbConnect(),
      "SELECT T.*,
		T.Weight *(T.BomCPlan + T.BomDPlan) AS WeightPlan,
		T.Weight *(T.BomCActual + T.BomDActual) AS WeightActual


	 FROM(
     SELECT
      ROW_NUMBER() OVER (
        ORDER BY G.Id ASC
      ) AS Id,
      G.Id AS TransId,
      G.ItemId,
      GM.ItemGTName,
      GM.PR,
      NULL AS Code,
      GM.Pattern,
      GM.TT,
      GM.ColorAll,
      -- GM.Color2,
      -- GM.Color3,
      -- GM.Color4,
      -- GM.Color5,
      CAST(GM.[Weight] / 1000 AS NUMERIC(10, 3)) AS [Weight],
      G.BomCPlan,
      G.BomCActual,
      G.BomDPlan,
      G.BomDActual
      --G.WeightPlan,
     -- CONVERT(DECIMAL(10,2),G.WeightPlan) AS WeightPlan,

      --G.WeightActual,
     -- CONVERT(DECIMAL(10,2),G.WeightActual) AS WeightActual
      FROM TargetGreentire G
      LEFT JOIN ProductionSchGreentireMaster GM
      ON GM.ItemGT = G.ItemId
      AND GM.ItemFG = G.ItemFG
      WHERE CONVERT(date, G.ShiftDate) = ?
      GROUP BY
      G.Id,
      G.ItemId,
      GM.ItemGTName,
      GM.PR,
      GM.Pattern,
      GM.TT,
      GM.ColorAll,
      -- GM.Color2,
      -- GM.Color3,
      -- GM.Color4,
      -- GM.Color5,
      GM.[Weight],
      G.BomCPlan,
      G.BomCActual,
      G.BomDPlan,
      G.BomDActual,
      G.WeightPlan,
      G.WeightActual
      ) T",
      [
        $shiftDate
      ]
    );
  }

  public function update($name, $pk, $value)
  {
    try {
      $update = sqlsrv_query(
        $this->conn->dbConnect(),
        "UPDATE TargetGreentire
        SET $name = ?
        WHERE Id = ?
        ",
        [
          $value,
          $pk
        ]
      );

      if (!$update) {
        throw new \Exception('Error: update failed.' . var_dump(sqlsrv_errors()));
      }

      $updateWeightActual = sqlsrv_query(
        $this->conn->dbConnect(),
        "UPDATE T
          SET T.WeightActual = (
            SELECT
            CASE
              WHEN T.BomCActual IS NULL OR T.BomDActual IS NULL THEN (
                CASE
                  WHEN T.BomCActual IS NOT NULL THEN (T.BomCActual * P.[Weight]) / 1000
                  WHEN T.BomDActual IS NOT NULL THEN (T.BomDActual * P.[Weight]) / 1000
                  ELSE 0
                END
              )
              ELSE ( (T.BomCActual * P.[Weight]) + (T.BomDActual * P.[Weight]) )/1000
            END AS WEIGHT_ALL
          )
          FROM TargetGreentire T
          LEFT JOIN ProductionSchGreentireMaster P
          ON P.ItemGT = T.ItemId
          WHERE T.Id = ?;",
        [
          $pk
        ]
      );

      if (!$updateWeightActual) {
        throw new \Exception('Error: update weight failed.');
      }

      return response(true, "Update success");
    } catch (\Exception $e) {
      return response(false, $e->getMessage());
    }
  }

  public function isCreated($date, $shift, $module)
  {
    try {
      // code
      $q = sqlsrv_has_rows(sqlsrv_query(
        $this->conn->dbConnect(),
        "SELECT *
        FROM ShiftTrans
        WHERE CONVERT(date, ShiftDate) = ?
        AND ModuleId = ?
        AND [Status] = 1",
        [
          date("Y-m-d", strtotime($date)),
          $module
        ]
      ));

      if ($q === true) {
        return response(true, "Record already exists.");
      } else {
        return response(false, "Record not found.");
      }
    } catch (\Exception $e) {
      return response(false, $e->getMessage());
    }
  }

  public function cancel($id)
  {
    try {
      // code
      $cancel = sqlsrv_query(
        $this->conn->dbConnect(),
        "UPDATE ShiftTrans
        SET [Status] = 4
        WHERE ID = ?",
        [
          $id
        ]
      );

      if (!$cancel) {
        throw new \Exception("Update failed. ");
      }

      return response(true, "Cancel success.");
    } catch (\Exception $e) {
      return response(false, $e->getMessage());
    }
  }

  public function delete($id)
  {
    try {
      // code
      $delete = sqlsrv_query(
        $this->conn->dbConnect(),
        "DELETE TargetGreentire
        WHERE Id = ?",
        [
          $id
        ]
      );

      if (!$delete) {
        throw new \Exception("Update failed. ");
      }

      return response(true, "Delete success.");
    } catch (\Exception $e) {
      return response(false, $e->getMessage());
    }
  }

  public function add(
    $shift_id,
    $greentire_id,
    $bom_c_plan,
    $bom_d_plan,
    $weight_plan,
    $shift_date
  ) {
    try {
      // code
      $insert = sqlsrv_query(
        $this->conn->dbConnect(),
        "INSERT INTO TargetGreentire(
          TransId,
          ItemId,
          BomCPlan,
          BomDPlan,
          WeightPlan,
          ShiftDate
        )
        VALUES(?, ?, ?, ?, ?, ?)",
        [
          $shift_id,
          $greentire_id,
          $bom_c_plan,
          $bom_d_plan,
          (($bom_c_plan * $weight_plan) + ($bom_d_plan * $weight_plan)) / 1000,
          $shift_date
        ]
      );

      if (!$insert) {
        throw new \Exception("Add Greentire failed. " . var_dump(sqlsrv_errors()));
      }

      return response(true, "Add Greentire Success.");
    } catch (\Exception $e) {
      return response(false, $e->getMessage());
    }
  }

  public function getGreentireMaster($filter)
  {
    try {
      // code
      $sql = "SELECT
        ItemGT,
        ItemGTName,
        PR,
        Pattern,
        Color,
        Color2,
        Color3,
        Color4,
        Color5,
        TT,
        [Weight]
        FROM ProductionSchGreentireMaster

        WHERE $filter
        GROUP By
        ItemGT,
        ItemGTName,
        PR,
        Pattern,
        Color,
        Color2,
        Color3,
        Color4,
        Color5,
        TT,
        [Weight]";

      return $this->sql->queryArray(
        $this->conn->dbConnect(),
        $sql
      );
    } catch (\Exception $e) {
      return $e->getMessage();
    }
  }
  public function load($date_sch, $shift)
  {
    try {
      // code
      $sqlId = "1=1";

      if ($id !== null) {
        $sqlId = "P.Id = $id";
      }
      if ($shift == 1) {
        $dateref = date('Y-m-d', strtotime($date_sch . ' -1 days'));
        $shiftref = 2;
      } else {
        $dateref = $date_sch;
        $shiftref = 1;
      }

      $query = $this->sql->rows(
        $this->conn->dbConnect(),
        "SELECT
        P.Id,
        P.ItemId,
        I.ItemGTName,
        I.Color,
        ISNULL(PPT.SpareOfcure, 0 ) AS SpareOfcure,
        ISNULL(GT.StockInplan, 0 ) AS StockInplan2 ,
        ISNULL(PPT.SpareOfcure, 0 ) + ISNULL(GT.StockInplan, 0 ) AS TOTAL,
        P.shift,
        P.CountIn,
        P.CountOut,
        P.CountNotSpec,
        P.CountReal,
        PPT.CountCure,
        P.CountShift,
        P.CountPlan,
        P.CalStock,
        P.StockInplan,
        
        (ISNULL(P.StockInplan, 0 ) +ISNULL(P.CountIn, 0 ) ) - (ISNULL(P.CountNotSpec, 0 )+ ISNULL(P.CountOut, 0 )) AS TotalSockGT,
       -- P.StockInplan AS TotalSockGT,
        CASE
          -- WHEN (ISNULL(GT.StockInplan, 0 ) +ISNULL(P.CountIn, 0 ) ) - (ISNULL(P.CountNotSpec, 0 )+ ISNULL(P.CountOut, 0 )) <> P.CountReal THEN 'X'
          WHEN P.CountIn <> P.CountPlan THEN 'X'
        ELSE '-'
        END AS  CheckCountShift,
        CASE
        WHEN   ISNULL(PPT.CountCure, 0 ) <> ISNULL(P.CountOut, 0 ) THEN 'X'
        ELSE '-'
        END AS CheckCountOut,
        ((ISNULL(P.StockInplan, 0 ) +ISNULL(P.CountIn, 0 ) ) - (ISNULL(P.CountNotSpec, 0 )+ ISNULL(P.CountOut, 0 ))) - ISNULL(P.CountReal,0) AS  Chekdata
        FROM ProductionSchReciveTable P
        LEFT JOIN (
        SELECT
        PP.ItemGTName,
        PP.ItemGT,
        -- ISNULL(PP.Color, '-' ) +'/' + ISNULL(PP.Color2, '-' )+'/'+ ISNULL(PP.Color3, '-' ) +'/' + ISNULL(PP.Color4, '-' )  +'/' + ISNULL(PP.Color5, '-' ) As Color
        PP.ColorAll As Color
        FROM ProductionSchGreentireMaster PP
         GROUP BY PP.ItemGTName,PP.ItemGT, PP.ColorAll
          )I ON I.ItemGT = P.ItemId
        LEFT JOIN (
        SELECT
          ItemId ,StockInplan
       FROM ProductionSchReciveTable WHERE Sch_date = ? AND Shift = ?
          )GT ON GT.ItemId = P.ItemId
          LEFT JOIN ProductionGreentirePrintTable PPT ON PPT.ItemId = P.ItemId  AND PPT.Sch_date = ? AND PPT.Shift = ?
         WHERE P.Sch_date = ? AND P.Shift = ?
         ORDER BY P.ItemId ASC",
        [
          $dateref,
          $shiftref,
          $date_sch,
          $shift,
          $date_sch,
          $shift
        ]
      );

      return $query;
    } catch (\Exception $e) {
      return response(false, $e->getMessage());
    }
  }

  public function insertSchTable($date_sch, $shift, $copy, $date_gen, $shift_gen, $gen_emp, $date_emp, $shift_emp)
  {
    try {
      $date = date("Y-m-d H:i:s");


      if ($copy == 0) {

        if ($shift == 1) {
          $dateref = $date_sch;
          $shiftref = 2;
          $daterecive = date('Y-m-d', strtotime($date_sch . ' -1 days'));
          $dateNext = $date_sch;
        } else {
          $dateref = date('Y-m-d', strtotime($date_sch . ' +1 days'));
          $daterecive = $date_sch;
          $shiftref = 1;
          $dateNext = date('Y-m-d', strtotime($date_sch . ' +1 days'));
        }


        $insertSch = \sqlsrv_query(
          $this->conn->dbConnect(),
          "INSERT INTO ProductionSchReciveTable (
           Sch_date,
           Shift,
           CrateDate,
           CreateBy
          ) VALUES(?, ?, ?, ?)",
          [
            $date_sch,
            $shift,
            $date,
            $_SESSION["user_login"]

          ]
        );

        $update = \sqlsrv_query(
          $this->conn->dbConnect(),
          "UPDATE X
          SET X.StockInplan = Y.StockInplan  + ISNULL(X.CalStock,0)
          FROM  ProductionSchReciveTable X
          LEFT JOIN(
          SELECT P1.ItemId,(ISNULL(P2.StockInplan,0) +ISNULL(P1.CountIn,0))-(ISNULL(P1.CountOut,0) + ISNULL(P1.CountNotSpec,0)) AS StockInplan
          FROM ProductionSchReciveTable P1 
          LEFT JOIN ProductionSchReciveTable P2 ON P1.ItemId = P2.ItemId AND P2.Sch_date = ? AND P2.Shift = ?
          WHERE P1.Sch_date = ? AND P1.Shift = ?)Y
          ON X.ItemId = Y.ItemId
          WHERE X.Sch_date = ? AND X.Shift = ?",
          [
            $daterecive,
            $shiftref,
            $date_sch,
            $shift,
            $date_sch,
            $shift
          ]
        );

        if (!$insertSch) {
          sqlsrv_rollback($this->conn->dbConnect());
          return false;
        }

        sqlsrv_commit($this->conn->dbConnect());
        return true;
      } else {

        $q = sqlsrv_has_rows(sqlsrv_query(
          $this->conn->dbConnect(),
          "SELECT *
         FROM ProductionSchReciveTable
         WHERE Sch_date = ? AND Shift = ?",
          [
            $date_sch,
            $shift
          ]
        ));

        if ($q === true) {
          $delete = sqlsrv_query(
            $this->conn->dbConnect(),
            "DELETE ProductionSchReciveTable
           WHERE Sch_date = ? AND Shift = ?",
            [
              $date_sch,
              $shift
            ]
          );
        }

        // $checksch = sqlsrv_has_rows(sqlsrv_query(
        //   $this->conn->dbConnect(),
        //   "SELECT *
        //  FROM BuildSch
        //  WHERE DateBuild = ? AND Shift = ?",
        //   [
        //     $date_gen,
        //     $shift_gen
        //   ]
        // ));

        // if ($checksch === false) {
        //   return false;
        // }

        if ($shift == 1) {
          $dateref = $date_sch;
          $shiftref = 2;
          $daterecive = date('Y-m-d', strtotime($date_sch . ' -1 days'));
          $dateNext = $date_sch;
        } else {
          $dateref = date('Y-m-d', strtotime($date_sch . ' +1 days'));
          $daterecive = $date_sch;
          $shiftref = 1;
          $dateNext = date('Y-m-d', strtotime($date_sch . ' +1 days'));
        }


        $insertSch = \sqlsrv_query(
          $this->conn->dbConnect(),
          "INSERT INTO ProductionSchReciveTable (
            ItemId,
            Shift,
            Sch_date,
           -- StockInplan,
            CountShift,
            CountPlan
           )
           SELECT 
            X.ItemId,
            '$shift' AS Shift,
            '$date_sch' AS DateBuild,
            --CASE WHEN Q.Target IS NULL OR Q.Target = '' THEN Q2.CountShift ELSE Q.Target END AS CountShift,
            --CASE WHEN Q.Actual IS NULL OR Q.Actual = '' THEN Q2.CountPlan ELSE Q.Target END AS CountPlan
            Q.Target AS CountShift ,
            Q.Actual AS CountPlan
            FROM 
            (SELECT ItemId FROM ( 
             SELECT ItemId FROM ProductionSchReciveTable where Sch_date = ? and Shift = ?
             UNION ALL
             SELECT ItemId from BuildSch where DateBuild = ? and Shift = ?)A
            GROUP BY A.ItemId)X
            LEFT JOIN BuildSch Q ON X.ItemId = Q.ItemId AND Q.DateBuild = ? and Q.Shift = ?",
          [
            $daterecive,
            $shiftref,
            $date_gen,
            $shift_gen,
            $date_gen,
            $shift_gen
          ]
        );

        $update = \sqlsrv_query(
          $this->conn->dbConnect(),
          "UPDATE X
          SET X.StockInplan = Y.StockInplan + ISNULL(X.CalStock,0)
          FROM  ProductionSchReciveTable X
          LEFT JOIN(
          SELECT P1.ItemId,(ISNULL(P2.StockInplan,0) +ISNULL(P1.CountIn,0))-(ISNULL(P1.CountOut,0) + ISNULL(P1.CountNotSpec,0)) AS StockInplan
          FROM ProductionSchReciveTable P1 
          LEFT JOIN ProductionSchReciveTable P2 ON P1.ItemId = P2.ItemId AND P2.Sch_date = ? AND P2.Shift = ?
          WHERE P1.Sch_date = ? AND P1.Shift = ?)Y
          ON X.ItemId = Y.ItemId
          WHERE X.Sch_date = ? AND X.Shift = ?",
          [
            $daterecive,
            $shiftref,
            $date_sch,
            $shift,
            $date_sch,
            $shift
          ]
        );

        if (!$insertSch) {
          sqlsrv_rollback($this->conn->dbConnect());
          return false;
        }

        sqlsrv_commit($this->conn->dbConnect());
        return true;
      }
    } catch (Exception $e) {
      return false;
    }
  }

  public function UpdateSchReiveTable($CountIn, $CountOut, $CountNotSpec, $CountReal, $id, $date_sch, $shift, $CalStock)
  {

    $date = date("Y-m-d H:i:s");
    if ($shift == 1) {
      $dateref = date('Y-m-d', strtotime($date_sch . ' -1 days'));
      $shiftref = 2;
    } else {
      $dateref = $date_sch;
      $shiftref = 1;
    }

    $update = \sqlsrv_query(
      $this->conn->dbConnect(),
      "UPDATE ProductionSchReciveTable
      SET CountIn = ?,
      CountOut = ? ,
      CountNotSpec = ?,
      CountReal = ?,
      CalStock = ?,
      UpdateBy = ?,
      UpdateDate =?
      WHERE Id = ?",
      [
        $CountIn,
        $CountOut,
        $CountNotSpec,
        $CountReal,
        $CalStock,
        $_SESSION["user_login"],
        $date,
        $id
      ]
    );
    // $getdata = $this->sql->rows(
    //   $this->conn->dbConnect(),
    //   "SELECT (P2.StockInplan +P.CountIn)-(P.CountOut + P.CountNotSpec) AS StockInplan
    //   FROM ProductionSchReciveTable P
    //   LEFT JOIN ProductionSchReciveTable P2 ON P2.ItemId = P.Itemid
    //   AND P2.Sch_date = ? AND P2.Shift = ?
    //   WHERE P.Id = ?",
    //   [
    //     $dateref,
    //     $shiftref,
    //     $id
    //   ]
    // );

    $updateStockInplan = \sqlsrv_query(
      $this->conn->dbConnect(),
      "UPDATE X
      SET X.StockInplan = ISNULL(Y.StockInplan,0) + ISNULL(X.CalStock,0)
      FROM  ProductionSchReciveTable X
       LEFT JOIN(
       SELECT P1.ItemId,(ISNULL(P2.StockInplan,0) +ISNULL(P1.CountIn,0))-(ISNULL(P1.CountOut,0) + ISNULL(P1.CountNotSpec,0)) AS StockInplan
       FROM ProductionSchReciveTable P1 
       LEFT JOIN ProductionSchReciveTable P2 ON P1.ItemId = P2.ItemId AND P2.Sch_date = ? AND P2.Shift = ?
       WHERE P1.Sch_date = ? AND P1.Shift = ?)Y
       ON X.ItemId = Y.ItemId
       WHERE X.Id = ?",
      [
        $dateref,
        $shiftref,
        $date_sch,
        $shift,
        $id
      ]
    );

    $updateStockOrder = \sqlsrv_query(
      $this->conn->dbConnect(),
      "UPDATE X
      SET X.StockOrder = Y.StockPrderCheck 
      FROM  ProductionSchReciveTable X
      LEFT JOIN(
      SELECT
        T1.ItemId,
        (ISNULL(T2.StockOrder,0) + ISNULL(T1.CountIn,0)) - ISNULL(T1.CountOut,0) AS StockPrderCheck
      FROM ProductionSchReciveTable T1
      LEFT JOIN ProductionSchReciveTable T2 ON T1.ItemId = T2.ItemId
      AND T2.Sch_date = ? AND T2.Shift = ?
      WHERE T1.Sch_date = ? and T1.Shift = ?
     )Y ON X.ItemId = Y.ItemId
      WHERE X.Sch_date = ? AND X.Shift = ?",
      [
        $dateref,
        $shiftref,
        $date_sch,
        $shift,
        $date_sch,
        $shift
      ]
    );

    if ($updateStockInplan) {
      return true;
    } else {
      return false;
    }
  }

  public function loaditem()
  {


    try {
      // code
      $query = $this->sql->rows(
        $this->conn->dbConnect(),
        "SELECT
              ItemGT ,ItemGTName
              FROM ProductionSchGreentireMaster P
              GROUP BY ItemGT ,ItemGTName"
      );

      return $query;
    } catch (\Exception $e) {
      return response(false, $e->getMessage());
    }
  }

  public function loaditemEXT()
  {


    try {
      // code
      $query = $this->sql->rows(
        $this->conn->dbConnect(),
        "SELECT 
          ItemExt AS ItemBOM
          ,Name AS ITEMNAME
          FROM ProductionSchEXTMaster
          GROUP BY   ItemExt,Name  ORDER BY ItemExt ASC"
      );

      return $query;
    } catch (\Exception $e) {
      return response(false, $e->getMessage());
    }
  }


  public function InsertItemGreentireTable($itemid, $id)
  {


    $date = date("Y-m-d H:i:s");
    $getdata = $this->sql->rows(
      $this->conn->dbConnect(),
      "SELECT
         TOP 1
         P.ItemId,
         (P.StockInplan +P.CountIn)-(P.CountOut + P.CountNotSpec) AS StockInplan
        FROM ProductionSchReciveTable P WHERE P.ItemId = ?
        ORDER BY P.Sch_date desc , P.Shift desc",
      [$itemid]
    );




    $update = \sqlsrv_query(
      $this->conn->dbConnect(),
      "UPDATE ProductionSchReciveTable
        SET ItemId = ?,
      --  StockInplan = ? ,
        UpdateBy = ?,
        UpdateDate =?
        WHERE Id = ?",
      [
        $itemid,
        //  $getdata[0]["StockInplan"],
        $_SESSION["user_login"],
        $date,
        $id
      ]
    );

    if ($update) {
      return true;
    } else {
      return false;
    }
  }

  public function DeleteSchTable($id)
  {

    $delete = sqlsrv_query(
      $this->conn->dbConnect(),
      "DELETE ProductionSchReciveTable
      WHERE Id = ?",
      [
        $id
      ]
    );

    if ($delete) {
      return true;
    } else {
      return false;
    }
  }

  public function loadprint($date_sch, $shift)
  {
    try {
      // code

      if ($shift == 1) {
        $dateref = $date_sch;
        $shiftref = 2;
        $daterecive = date('Y-m-d', strtotime($date_sch . ' -1 days'));
        $dateNext = $date_sch;
      } else {
        $dateref = date('Y-m-d', strtotime($date_sch . ' +1 days'));
        $daterecive = $date_sch;
        $shiftref = 1;
        $dateNext = date('Y-m-d', strtotime($date_sch . ' +1 days'));
      }

      $query = $this->sql->rows(
        $this->conn->dbConnect(),
        "SELECT
  		    T.Id,
   		    T.ItemId,
         	T.ItemGTName,
          T.Color,
          T.TotalShareMold,
          T.GroupId,
          T.SumPrint,
          T.TotalPrint,
          T.Time,
          T.Countprintcure,
          T.Rateprint,
          T.Time AS TimeCureFG,
          T.CountPrintcurFG,
          T.RatePrintFG,
          T.GreentireShift,
          T.GreentireDay,
          T.CountCure,
          T.SpareOfcure,
          T.StockInplan,
          T.Total,
          (T.Total/NULLIF(T.GreentireDay,0))/24     AS TotalHours,
          (T.Total*100)/NULLIF(T.GreentireDay,0) AS PersenGreentire,
          T.LackShift,
          T.TargetTemp,
          CASE
            WHEN (T.Total*100)/NULLIF(T.GreentireDay,0) > 100 THEN '4'
            WHEN (T.Total*100)/NULLIF(T.GreentireDay,0) > 50 THEN '3'
            WHEN (T.Total*100)/NULLIF(T.GreentireDay,0) > 17 THEN '2'
            ELSE '1' END AS  OrderLackshift,
          T.TargetTemp - (ISNULL(T.LackShift,0) * -1 ) AS LackShift2,
          T.Countprintcure + T.CountPrintcurFG AS CountPrint,
          T.CureDay,
          T.BL,
          T.Actual,
          (T.TargetTemp + T.StockInplan) - T.CountCure AS  TireLackShift,
          (T.Actual + T.TargetTemp + T.StockInplan) - T.GreentireDay AS TireLackDay
          FROM(
    		  SELECT
            PGT.Id,
            PGT.ItemId,
            I.ItemGTName,
            I.Color,
            SMM.Total AS TotalShareMold,
            SMM.GroupId,
            PGT.SumPrint,
            ISNULL(SMM.Total,0) - (ISNULL(PGT.Countprintcure,0) + ISNULL(PGT.CountPrintcurFG,0)) AS  TotalPrint,
            IM.Time,
            PGT.Countprintcure,
            PGT.Rateprint,
            IM.Time AS TimeCureFG,
            PGT.CountPrintcurFG,
            PGT.RatePrintFG,
            (ISNULL(PGT.Countprintcure,0) * ISNULL(PGT.Rateprint,0)) + (ISNULL(PGT.CountPrintcurFG,0) * ISNULL(PGT.RatePrintFG,0)) AS GreentireShift,
            ((ISNULL(PGT.Countprintcure,0) * ISNULL(PGT.Rateprint,0)) + (ISNULL(PGT.CountPrintcurFG,0) * ISNULL(PGT.RatePrintFG,0))) * 2 AS GreentireDay,
            PGT.CountCure,
            PGT.SpareOfcure,
    		    ISNULL(P2.StockInplan,0) AS StockInplan,
    		    ISNULL(PGT.SpareOfcure, 0 ) +ISNULL( P2.StockInplan,0) AS Total,
            CASE
              WHEN ISNULL(PGT.Countprintcure, 0 ) + ISNULL(PGT.CountPrintcurFG, 0 ) > 0 THEN
                CASE WHEN (ISNULL(PGT.SpareOfcure, 0 ) +ISNULL( P2.StockInplan,0)) < (PGT.Countprintcure * PGT.Rateprint) + (PGT.CountPrintcurFG * PGT.RatePrintFG) THEN
		              (ISNULL(PGT.SpareOfcure, 0 ) +ISNULL( P2.StockInplan,0)) - ((PGT.Countprintcure * PGT.Rateprint) + (PGT.CountPrintcurFG * PGT.RatePrintFG))
                  END
            ELSE
            '' END AS LackShift,
            ISNULL(BS2.Target,0) AS TargetTemp ,
            ((PGT.Countprintcure * PGT.Rateprint) + (PGT.CountPrintcurFG * PGT.RatePrintFG)) * 2 AS CureDay,
            BS2.BL,
            BS.Target AS Actual
            FROM ProductionGreentirePrintTable PGT
            LEFT JOIN (
              SELECT
                PP.ItemGTName,
                PP.ItemGT,
                PP.ColorAll AS Color
                FROM ProductionSchGreentireMaster PP
                GROUP BY PP.ItemGTName,PP.ItemGT, PP.ColorAll
                )I ON I.ItemGT = PGT.ItemId
                LEFT JOIN (
                SELECT
                  PP.ItemIdGreentire,
                  PP.Time
                  FROM ProductionSchItemMaster PP
                  Group by PP.ItemIdGreentire,PP.Time
                )IM ON IM.ItemIdGreentire = PGT.ItemId
                LEFT JOIN ProductionSchReciveTable P ON  P.ItemId = PGT.ItemId AND P.Sch_date = ? AND P.Shift = ?
                LEFT JOIN ProductionSchReciveTable P2 ON P2.ItemId = PGT.ItemId AND P2.Sch_date = ? AND P2.Shift = ?
                LEFT JOIN BuildSch BS ON BS.ItemId = PGT.ItemId AND BS.DateBuild = ? AND BS.Shift = ?
                LEFT JOIN BuildSch BS2 ON BS2.ItemId = PGT.ItemId AND BS2.DateBuild = ? AND BS2.Shift = ?
                LEFT JOIN (
    		        SELECT ItemId,Total,GroupId FROM ShareMoldMaster 
    		        GROUP BY ItemId,Total,GroupId
    		      ) SMM ON SMM.ItemId = PGT.ItemId
                WHERE PGT.Sch_date = ? AND PGT.Shift = ?

               )T
               ORDER BY T.GroupId ,T.ItemId ASC",
        [
          $date_sch,
          $shift,
          $daterecive,
          $shiftref,
          $dateNext,
          $shiftref,
          $date_sch,
          $shift,
          $date_sch,
          $shift

        ]
      );

      $x = 0;
      $data = [];
      $CountPrint = [];
      $SumPrint = 0;
      $SumPrintItem = "";
      $checkdata = [];
      $datamold = [];
      foreach ($query as $value) {
        if ($x !== (int) $value["GroupId"] && $value["GroupId"] !== NULL &&  $value["GroupId"] !== "") {

          $x = (int) $value["GroupId"];  //1
          $SumPrint = (int) $value["Countprintcure"] + (int) $value["CountPrintcurFG"];
          $SumPrintItem = $value["ItemId"]; //1
          if (!isset($CountPrint[$value["ItemId"]])) {
            $CountPrint[$value["ItemId"]] += $SumPrint;
          }
        } else if ($x === (int) $value["GroupId"] && $value["GroupId"] !== NULL &&  $value["GroupId"] !== "") {
          $value["TotalShareMold"]  = 0;
          $value["TotalPrint"]  = 0;

          //$SumPrint = 0 ;
          //$SumPrintItem = "";
          //  $SumPrint +=$value["Countprintcure"] ;
          // $CountPrint[$SumPrintItem] += $SumPrint;
          // if (isset($CountPrint[$value["ItemId"]])) {
          $CountPrint[$SumPrintItem] += (int) $value["Countprintcure"] + (int) $value["CountPrintcurFG"];
          // $value["Countprintcure"] = 0;
          // }
        }

        $data[]  = $value;
      }
      foreach ($data as $valueData) {
        foreach ($CountPrint as $key => $valueCountPrint) {
          if ($valueData["ItemId"] === $key) {
            $checkdata = (int) $valueData["TotalShareMold"] - (int) $valueCountPrint;
            $valueData["TotalPrint"] = $checkdata;
          } else if ($valueData["TotalPrint"] === NULL ||  $valueData["TotalPrint"] === "") {
            $valueData["TotalPrint"] = 0;
          }
        }

        $datamold[] = $valueData;
      }
      // echo '<pre>';
      //  print_r ($CountPrint); exit();
      //    echo '</pre>';
      return $datamold;

      //}
    } catch (\Exception $e) {
      return response(false, $e->getMessage());
    }
  }

  public function checkcompleteSchReciveTable($date_sch, $shift)
  {
    $date_sch = date('Y-m-d', strtotime($date_sch));
    return \sqlsrv_has_rows(\sqlsrv_query(
      $this->conn->dbConnect(),
      "SELECT *
      FROM ProductionSchReciveTable
      WHERE Sch_date = ? AND Shift = ?",
      [
        $date_sch,
        $shift
      ]
    ));
  }

  public function insertSchPrintTable($date_sch, $shift, $copy, $date_gen, $shift_gen, $gen_emp, $date_emp, $shift_emp)
  {
    try {
      $date = date("Y-m-d H:i:s");
      $userCreate = $_SESSION["user_login"];
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
      if ($copy == 0) {
        $insertSch = \sqlsrv_query(
          $this->conn->dbConnect(),
          "INSERT INTO ProductionGreentirePrintTable (
           Sch_date,
           Shift,
           CreateDate,
           CreateBy
          ) VALUES(?, ?, ?, ?)",
          [
            $date_sch,
            $shift,
            $date,
            $_SESSION["user_login"]

          ]
        );



        if (!$insertSch) {
          sqlsrv_rollback($this->conn->dbConnect());
          return false;
        } else {
          sqlsrv_commit($this->conn->dbConnect());
        }


        $updateprintsch1 = \sqlsrv_query(
          $this->conn->dbConnect(),
          "UPDATE T
          SET T.CountCure = A.BillUse,
              T.SpareOfcure = A.faceBoiler
          FROM ProductionGreentirePrintTable T
          LEFT JOIN(
          SELECT PR.ItemId,  
          Case WHEN X.BillUse IS NULL THEN X2.BillGive ELSE X.BillUse END AS BillUse,
          X2.faceBoiler
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
            SUM(PT.BillGive) AS BillGive,
            --SUM(PT.BillUse) AS BillUse
            SUM(PT.faceBoiler) AS faceBoiler 
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

        $updateprintsch2 = \sqlsrv_query(
          $this->conn->dbConnect(),
          "UPDATE T
          SET  T.SpareOfcure = A.faceBoiler,
          T.CountCure = A.BillGive
        FROM ProductionGreentirePrintTable T
        LEFT JOIN(
          SELECT PR.ItemId,  
            X.faceBoiler,
            Case WHEN X2.BillUse IS NULL THEN X.BillGive ELSE X2.BillUse END AS BillGive
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
                GROUP BY GM.ItemGT
            ) X ON PR.ItemId= X.ItemGT
            LEFT JOIN (
              SELECT 
                GM.ItemGT,
                SUM(PT.BillGive) AS BillGive,
                SUM(PT.BillUse) AS BillUse
                --SUM(PT.faceBoiler) AS faceBoiler 
                FROM ProductionSchTable  PT
                LEFT JOIN ProductionSchGreentireMaster GM ON PT.ItemID = GM.ItemFG
                WHERE PT.SchDate = ? AND PT.Shift = ? AND PT.ItemID IS NOT NULL
                GROUP BY GM.ItemGT
            ) X2 ON PR.ItemId= X2.ItemGT
                WHERE PR.Sch_date = ? AND PR.Shift = ?
            )A ON T.ItemId = A.ItemId 
         WHERE T.Sch_date = ? AND T.Shift = ?",
          [
            $date_sch,
            $shift,
            $datenext,
            $shiftnext,
            $datenext,
            $shiftnext,
            $datenext,
            $shiftnext


          ]
        );
        if (!$updateprintsch1) {
          sqlsrv_rollback($this->conn->dbConnect());
          return false;
        } else {
          sqlsrv_commit($this->conn->dbConnect());
          return true;
        }




        //sqlsrv_commit($this->conn->dbConnect());
        //return true;
      } else {

        $q = sqlsrv_has_rows(sqlsrv_query(
          $this->conn->dbConnect(),
          "SELECT *
         FROM ProductionGreentirePrintTable
         WHERE Sch_date = ? AND Shift = ?",
          [
            $date_sch,
            $shift
          ]
        ));

        if ($q === true) {
          $delete = sqlsrv_query(
            $this->conn->dbConnect(),
            "DELETE ProductionGreentirePrintTable
           WHERE Sch_date = ? AND Shift = ?",
            [
              $date_sch,
              $shift
            ]
          );
        }

        $insertSchPrint = \sqlsrv_query(
          $this->conn->dbConnect(),
          "INSERT INTO ProductionGreentirePrintTable (
            ItemId,
            Shift,
            Sch_date,
            CreateBy,
            CreateDate

           )
           SELECT
           ItemId,
           '$shift' AS Shift,
           '$date_sch' AS Sch_date,
           '$userCreate' AS CreateBy,
           '$date' AS CreateDate
           FROM ProductionSchReciveTable
           WHERE Sch_date = ? AND Shift = ?
           UNION
           SELECT
           ItemId,
           '$shift' AS Shift,
           '$date_sch' AS Sch_date ,
           '$userCreate' AS CreateBy,
           '$date' AS CreateDate
           FROM BuildSch
           WHERE DateBuild = ? AND Shift = ?",
          [
            $date_gen,
            $shift_gen,
            $date_gen,
            $shift_gen
          ]
        );

        $updateprintsch1 = \sqlsrv_query(
          $this->conn->dbConnect(),
          "UPDATE T
          SET T.CountCure = A.BillUse,
              T.SpareOfcure = A.faceBoiler
          FROM ProductionGreentirePrintTable T
          LEFT JOIN(
          SELECT PR.ItemId,  
          Case WHEN X.BillUse IS NULL THEN X2.BillGive ELSE X.BillUse END AS BillUse,
          X2.faceBoiler
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
            SUM(PT.BillGive) AS BillGive,
            --SUM(PT.BillUse) AS BillUse
            SUM(PT.faceBoiler) AS faceBoiler 
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

        $updateprintsch2 = \sqlsrv_query(
          $this->conn->dbConnect(),
          "UPDATE T
          SET  T.SpareOfcure = A.faceBoiler,
          T.CountCure = A.BillGive
        FROM ProductionGreentirePrintTable T
        LEFT JOIN(
          SELECT PR.ItemId,  
            X.faceBoiler,
            Case WHEN X2.BillUse IS NULL THEN X.BillGive ELSE X2.BillUse END AS BillGive
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
                GROUP BY GM.ItemGT
            ) X ON PR.ItemId= X.ItemGT
            LEFT JOIN (
              SELECT 
                GM.ItemGT,
                SUM(PT.BillGive) AS BillGive,
                SUM(PT.BillUse) AS BillUse
                --SUM(PT.faceBoiler) AS faceBoiler 
                FROM ProductionSchTable  PT
                LEFT JOIN ProductionSchGreentireMaster GM ON PT.ItemID = GM.ItemFG
                WHERE PT.SchDate = ? AND PT.Shift = ? AND PT.ItemID IS NOT NULL
                GROUP BY GM.ItemGT
            ) X2 ON PR.ItemId= X2.ItemGT
                WHERE PR.Sch_date = ? AND PR.Shift = ?
            )A ON T.ItemId = A.ItemId 
         WHERE T.Sch_date = ? AND T.Shift = ?",
          [
            $date_sch,
            $shift,
            $datenext,
            $shiftnext,
            $datenext,
            $shiftnext,
            $datenext,
            $shiftnext,



          ]
        );

        if (!$insertSchPrint) {
          sqlsrv_rollback($this->conn->dbConnect());
          return false;
        }

        sqlsrv_commit($this->conn->dbConnect());
        return true;
      }
    } catch (Exception $e) {
      return false;
    }
  }
  public function InsertItemGreentirePrintTable($itemid, $id, $date_sch, $shift)
  {


    $date = date("Y-m-d H:i:s");
    $date_sch = date('Y-m-d', strtotime($date_sch));
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
    $q = sqlsrv_has_rows(sqlsrv_query(
      $this->conn->dbConnect(),
      "SELECT ItemId
        FROM ProductionGreentirePrintTable
        WHERE Sch_date = ? AND Shift = ? AND ItemId = ?",
      [
        $date_sch,
        $shift,
        $itemid
      ]
    ));

    if ($q === true) {
      return false;
    }



    $update = \sqlsrv_query(
      $this->conn->dbConnect(),
      "UPDATE ProductionGreentirePrintTable
        SET ItemId = ?,
        UpdateBy = ?,
        UpdateDate =?
        WHERE Id = ?",
      [
        $itemid,
        $_SESSION["user_login"],
        $date,
        $id
      ]
    );

    $updateprintsch1 = \sqlsrv_query(
      $this->conn->dbConnect(),
      "UPDATE T
      SET T.CountCure = A.BillUse,
          T.SpareOfcure = A.faceBoiler
      FROM ProductionGreentirePrintTable T
      LEFT JOIN(
      SELECT PR.ItemId,  
      Case WHEN X.BillUse IS NULL THEN X2.BillGive ELSE X.BillUse END AS BillUse,
      X2.faceBoiler
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
        SUM(PT.BillGive) AS BillGive,
        --SUM(PT.BillUse) AS BillUse
        SUM(PT.faceBoiler) AS faceBoiler 
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

    $updateprintsch2 = \sqlsrv_query(
      $this->conn->dbConnect(),
      "UPDATE T
        SET  T.SpareOfcure = A.faceBoiler,
        T.CountCure = A.BillGive
      FROM ProductionGreentirePrintTable T
      LEFT JOIN(
        SELECT PR.ItemId,  
          X.faceBoiler,
          Case WHEN X2.BillUse IS NULL THEN X.BillGive ELSE X2.BillUse END AS BillGive
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
              GROUP BY GM.ItemGT
          ) X ON PR.ItemId= X.ItemGT
          LEFT JOIN (
            SELECT 
              GM.ItemGT,
              SUM(PT.BillGive) AS BillGive,
              SUM(PT.BillUse) AS BillUse
              --SUM(PT.faceBoiler) AS faceBoiler 
              FROM ProductionSchTable  PT
              LEFT JOIN ProductionSchGreentireMaster GM ON PT.ItemID = GM.ItemFG
              WHERE PT.SchDate = ? AND PT.Shift = ? AND PT.ItemID IS NOT NULL
              GROUP BY GM.ItemGT
          ) X2 ON PR.ItemId= X2.ItemGT
              WHERE PR.Sch_date = ? AND PR.Shift = ?
          )A ON T.ItemId = A.ItemId 
       WHERE T.Sch_date = ? AND T.Shift = ?",
      [
        $date_sch,
        $shift,
        $datenext,
        $shiftnext,
        $datenext,
        $shiftnext,
        $datenext,
        $shiftnext,



      ]
    );

    if ($update) {
      return true;
    } else {
      return false;
    }
  }

  public function UpdateSchprintable($SumPrint, $Countprintcure, $Rateprint, $CountPrintcurFG, $RatePrintFG, $CountCure, $SpareOfcure, $id)
  {

    $date = date("Y-m-d H:i:s");
    $update = \sqlsrv_query(
      $this->conn->dbConnect(),
      "UPDATE ProductionGreentirePrintTable
      SET SumPrint = ?,
      Countprintcure = ?,
      Rateprint = ?,
      CountPrintcurFG =?,
      RatePrintFG = ?,
      CountCure = ? ,
      SpareOfcure = ?,
      UpdateBy = ?,
      UpdateDate =?
      WHERE Id = ?",
      [
        $SumPrint,
        $Countprintcure,
        $Rateprint,
        $CountPrintcurFG,
        $RatePrintFG,
        $CountCure,
        $SpareOfcure,
        $_SESSION["user_login"],
        $date,
        $id
      ]
    );

    if ($update) {
      return true;
    } else {
      return false;
    }
  }

  public function DeleteSchprintTable($id)
  {

    $delete = sqlsrv_query(
      $this->conn->dbConnect(),
      "DELETE ProductionGreentirePrintTable
      WHERE Id = ?",
      [
        $id
      ]
    );

    if ($delete) {
      return true;
    } else {
      return false;
    }
  }

  public function checkcompleteSchReciveprintTable($date_sch, $shift)
  {
    $date_sch = date('Y-m-d', strtotime($date_sch));
    return \sqlsrv_has_rows(\sqlsrv_query(
      $this->conn->dbConnect(),
      "SELECT *
      FROM ProductionGreentirePrintTable
      WHERE Sch_date = ? AND Shift = ?",
      [
        $date_sch,
        $shift
      ]
    ));
  }

  public function insertSchtireTable($date_sch, $shift, $copy, $date_gen, $shift_gen, $gen_emp, $date_emp, $shift_emp)
  {
    try {
      $date = date("Y-m-d H:i:s");


      if ($copy == 0) {



        $insertSch = \sqlsrv_query(
          $this->conn->dbConnect(),
          "INSERT INTO ProductionGreentireDisburseTable (
           Sch_date,
           Shift,
           CreateDate,
           CreateBy
          ) VALUES(?, ?, ?, ?)",
          [
            $date_sch,
            $shift,
            $date,
            $_SESSION["user_login"]

          ]
        );

        if (!$insertSch) {
          sqlsrv_rollback($this->conn->dbConnect());
          return false;
        }

        sqlsrv_commit($this->conn->dbConnect());
        return true;
      } else {

        // if ($shift_gen == 1) {
        //   $shiftNext = 2;
        //   $date_Next = $date_gen;
        // } else {
        //   $shiftNext = 1;
        //   $date_Next = date('Y-m-d', strtotime($date_sch . ' +1 days'));
        // }
        // if ($shift == 1) {
        //   $date_stock = date('Y-m-d', strtotime($date_sch . ' -1 days'));
        //   $shift_stock = 2;
        //   $date_passgen = $date_stock;
        //   $shif_passgen = 1;
        // } else {
        //   $date_stock = $date_sch;
        //   $shift_stock = 1;
        //   $date_passgen = date('Y-m-d', strtotime($date_stock . ' -1 days'));
        //   $shif_passgen = 2;
        // }
        $getcheckdate = $this->sql->rows(
          $this->conn->dbConnect(),
          "SELECT  Sch_Date,Shift , COUNT(Sch_Date) OVER(PARTITION BY Sch_Date) AS CountRow
          FROM ProductionGreentireDisburseTable where Sch_Date = ?
          group by Sch_Date,Shift  order by Sch_Date desc,Shift desc
          ",
          [
            $date_sch
          ]
        );
        $datacheckCunt = $getcheckdate[0]["CountRow"];
        $shifcheck = $getcheckdate[0]["Shift"];


        if ($datacheckCunt == "" || $datacheckCunt == NULL) {

          $getdate = $this->sql->rows(
            $this->conn->dbConnect(),
            "SELECT TOP 1 Sch_Date,Shift 
                FROM ProductionGreentireDisburseTable where Sch_Date < ?
                group by Sch_Date,Shift  order by Sch_Date desc,Shift desc",
            [$date_sch]
          );
          $getdateNext = $this->sql->rows(
            $this->conn->dbConnect(),
            "SELECT TOP 1 Sch_Date,Shift 
                FROM ProductionGreentireDisburseTable where Sch_Date > ?
                group by Sch_Date,Shift  order by Sch_Date asc,Shift asc",
            [$date_sch]
          );
          $dateold = $getdate[0]["Sch_Date"];
          $shiftold = $getdate[0]["Shift"];
          $datenext = $getdateNext[0]["Sch_Date"];
          $shifnext = $getdateNext[0]["Shift"];
        } else {

          if ($datacheckCunt == 1) {
            if ($shift == 1) {
              $getdate = $this->sql->rows(
                $this->conn->dbConnect(),
                "SELECT TOP 1 Sch_Date,Shift 
                FROM ProductionGreentireDisburseTable where Sch_Date < ?
                group by Sch_Date,Shift  order by Sch_Date desc,Shift desc",
                [$date_sch]
              );
              if ($shifcheck == 1) {
                $getdateNext = $this->sql->rows(
                  $this->conn->dbConnect(),
                  "SELECT TOP 1 Sch_Date,Shift 
                        FROM ProductionGreentireDisburseTable where Sch_Date > ?
                        group by Sch_Date,Shift  order by Sch_Date asc,Shift asc",
                  [$date_sch]
                );
              } else {
                $getdateNext = $this->sql->rows(
                  $this->conn->dbConnect(),
                  "SELECT TOP 1 Sch_Date,Shift 
                        FROM ProductionGreentireDisburseTable where Sch_Date >= ?
                        group by Sch_Date,Shift  order by Sch_Date asc,Shift asc",
                  [$date_sch]
                );
              }
              $dateold = $getdate[0]["Sch_Date"];
              $shiftold = $getdate[0]["Shift"];
              $datenext = $getdateNext[0]["Sch_Date"];
              $shifnext = $getdateNext[0]["Shift"];
            } else {
              if ($shifcheck == 1) {
                $getdate = $this->sql->rows(
                  $this->conn->dbConnect(),
                  "SELECT TOP 1 Sch_Date,Shift 
                        FROM ProductionGreentireDisburseTable where Sch_Date <= ?
                        group by Sch_Date,Shift  order by Sch_Date desc,Shift desc",
                  [$date_sch]
                );
                $dateold = $getdate[0]["Sch_Date"];
                $shiftold = $getdate[0]["Shift"];
              } else {
                $getdate = $this->sql->rows(
                  $this->conn->dbConnect(),
                  "SELECT TOP 2 Sch_Date,Shift 
                        FROM ProductionGreentireDisburseTable where Sch_Date <= ?
                        group by Sch_Date,Shift  order by Sch_Date desc,Shift desc",
                  [$date_sch]
                );
                $dateold = $getdate[1]["Sch_Date"];
                $shiftold = $getdate[1]["Shift"];
              }
              $getdateNext = $this->sql->rows(
                $this->conn->dbConnect(),
                "SELECT TOP 1 Sch_Date,Shift 
                            FROM ProductionGreentireDisburseTable where Sch_Date > ?
                            group by Sch_Date,Shift  order by Sch_Date asc,Shift asc",
                [$date_sch]
              );
              $dateold = $dateold;
              $shiftold = $shiftold;
              $datenext = $getdateNext[0]["Sch_Date"];
              $shifnext = $getdateNext[0]["Shift"];
            }
          } else {
            if ($shift == 1) {
              $getdate = $this->sql->rows(
                $this->conn->dbConnect(),
                "SELECT TOP 3 Sch_Date,Shift 
                  FROM ProductionGreentireDisburseTable where Sch_Date <= ?
                  group by Sch_Date,Shift  order by Sch_Date desc,Shift desc
                  ",
                [
                  $date_sch

                ]
              );

              $getdateNext = $this->sql->rows(
                $this->conn->dbConnect(),
                "SELECT TOP 1 Sch_Date,Shift 
                  FROM ProductionGreentireDisburseTable where Sch_Date <= ?
                  group by Sch_Date,Shift  order by Sch_Date desc,Shift desc
                  ",
                [
                  $date_sch

                ]
              );
              $dateold = $getdate[2]["Sch_Date"];
              $shiftold = $getdate[2]["Shift"];
              $datenext = $getdateNext[0]["Sch_Date"];
              $shifnext = $getdateNext[0]["Shift"];
            } else {
              $getdate = $this->sql->rows(
                $this->conn->dbConnect(),
                "SELECT TOP 3 Sch_Date,Shift 
                  FROM ProductionGreentireDisburseTable where Sch_Date <= ?
                  group by Sch_Date,Shift  order by Sch_Date desc,Shift desc
                  ",
                [
                  $date_sch

                ]
              );
              $getdateNext = $this->sql->rows(
                $this->conn->dbConnect(),
                "SELECT TOP 1 Sch_Date,Shift 
                  FROM ProductionGreentireDisburseTable where Sch_Date > ?
                  group by Sch_Date,Shift  order by Sch_Date asc,Shift asc
                  ",
                [
                  $date_sch

                ]
              );
              $dateold = $getdate[1]["Sch_Date"];
              $shiftold = $getdate[1]["Shift"];
              $datenext = $getdateNext[0]["Sch_Date"];
              $shifnext = $getdateNext[0]["Shift"];
            }
          }
        }
        // return $datenext
        // exit();
        $q = sqlsrv_has_rows(sqlsrv_query(
          $this->conn->dbConnect(),
          "SELECT *
         FROM ProductionGreentireDisburseTable
         WHERE Sch_date = ? AND Shift = ?",
          [
            $date_sch,
            $shift
          ]
        ));

        if ($q === true) {
          $delete = sqlsrv_query(
            $this->conn->dbConnect(),
            "DELETE ProductionGreentireDisburseTable
           WHERE Sch_date = ? AND Shift = ?",
            [
              $date_sch,
              $shift
            ]
          );
        }

        $checksch = sqlsrv_has_rows(sqlsrv_query(
          $this->conn->dbConnect(),
          "SELECT *
         FROM BuildSch
         WHERE DateBuild = ? AND Shift = ?",
          [
            $date_gen,
            $shift_gen
          ]
        ));

        if ($checksch === false) {
          return false;
        }
        // return 1234;
        $insertSch = \sqlsrv_query(
          $this->conn->dbConnect(),
          "INSERT INTO ProductionGreentireDisburseTable (
            ItemId,
            Sch_date,
            Shift,
            Target,
            Target1,
            Actual,
            BL
          --  Stock
          )
          SELECT
		        T.ItemBOM AS ItemId,
	          '$date_sch' AS Sch_date,
            '$shift' AS Shift,
            ISNULL(SUM(T.Target),0) AS Target,
            ISNULL(SUM(T.Target1),0) AS Target1,
            ISNULL(SUM(T.Actual),0) AS Actual,
            ISNULL(SUM(T.BL),0) AS BL
         --   (T.Stock + T.Total) - (T.TireNotSpac +T.Produce)  AS Stock
            FROM
            (
		          SELECT
			          BB.ItemExt AS ItemBOM,
			          B1.Target,
			          B2.Target1,
			          B1.Actual,
			          B1.BL
			          -- ISNULL(PQ1.Stock,0) AS Stock,
			          -- ISNULL(PQ.Car1_1,0) + ISNULL(PQ.Car1_2,0) + ISNULL(PQ.Car1_3,0) + ISNULL(PQ.Car1_4,0) +
			          -- ISNULL(PQ.Car1_5,0) + ISNULL(PQ.Car1_6,0)+ ISNULL(PQ.Car1_7,0) + ISNULL(PQ.Car1_8,0) AS Total,
			          -- ISNULL(PQ.TireNotSpac,0) AS TireNotSpac,
			          -- ISNULL(PQ.Actual,0) AS Produce
                FROM
		            (
			            SELECT X.ItemId FROM(
				          SELECT ItemId FROM BuildSch where DateBuild = ? and Shift = ?
				          UNION ALL
				          SELECT ItemId FROM BuildSch where DateBuild = ? and Shift = ?)
				          X GROUP BY X.ItemId
		            )P
		          LEFT JOIN ProductionSchEXTMaster BB ON BB.ItemGT = P.ItemId
		          LEFT JOIN(
		            SELECT BB.ItemExt AS ItemIdBom1,SUM(P2.Target) AS Target,
							  SUM(P2.Actual) AS Actual,
							  SUM(P2.BL) AS BL
							  FROM BuildSch P2
							  LEFT JOIN ProductionSchEXTMaster BB ON BB.ItemGT = P2.ItemId
						    WHERE P2.DateBuild = ? AND P2.Shift = ?
						    GROUP By BB.ItemExt
              )B1 ON B1.ItemIdBom1 = BB.ItemExt
		          LEFT JOIN(
		            SELECT BB.ItemExt AS ItemIdBom2, SUM(P2.Target) AS Target1 FROM BuildSch P2
								LEFT JOIN ProductionSchEXTMaster BB ON BB.ItemGT = P2.ItemId
						    WHERE P2.DateBuild = ? AND P2.Shift = ?
						    GROUP By BB.ItemExt
              )B2 ON B2.ItemIdBom2 = BB.ItemExt
		          -- LEFT JOIN ProductionGreentireDisburseTable PQ ON PQ.ItemId = BB.ItemExt AND PQ.Sch_date = ? AND PQ.Shift = ?
		          -- LEFT JOIN ProductionGreentireDisburseTable PQ1 ON PQ1.ItemId = BB.ItemExt AND PQ1.Sch_date = ? AND PQ1.Shift = ?
              GROUP BY
                BB.ItemExt,
			          B1.Target,
			          B2.Target1,
			          B1.Actual,
			          B1.BL
			          -- PQ1.Stock,
			          -- PQ.Car1_1,
			          -- PQ.Car1_2,
			          -- PQ.Car1_3,
			          -- PQ.Car1_4,
			          -- PQ.Car1_5,
			          -- PQ.Car1_6,
			          -- PQ.Car1_7,
			          -- PQ.Car1_8,
			          -- PQ.TireNotSpac,
			         -- PQ.Actual)T
               )T
			          GROUP BY T.ItemBOM",
          [
            $date_gen,
            $shift_gen,
            $datenext,
            $shifnext,
            $date_gen,
            $shift_gen,
            $datenext,
            $shifnext
            // $date_stock,
            // $shift_stock,
            // $date_passgen,
            // $shif_passgen

          ]
        );

        $insertSch = \sqlsrv_query(
          $this->conn->dbConnect(),
          "INSERT INTO ProductionGreentireDisburseTable (
            ItemId,
            Sch_date,
            Shift
           
          )
          SELECT 
            ItemId ,
            '$date_sch' AS Sch_date,
            '$shift' AS Shift
          FROM ProductionGreentireDisburseTable 
          WHERE Sch_date = ? AND Shift = ?
            AND ItemId NOT IN (SELECT A.ItemId 
              FROM ProductionGreentireDisburseTable A 
              JOIN  ProductionGreentireDisburseTable B ON A.ItemId = B.ItemId 
              AND B.Sch_date = ? AND B.Shift = ?
              WHERE A.Sch_date = ? AND A.Shift = ?)",
          [
            $dateold,
            $shiftold,
            $dateold,
            $shiftold,
            $date_sch,
            $shift


          ]
        );

        // $updateStock = \sqlsrv_query(
        //   $this->conn->dbConnect(),
        //   "UPDATE X
        //   SET X.Stock = ISNULL(Y.TotalSystem,0) + ISNULL(X.CalStock,0)
        //   FROM ProductionGreentireDisburseTable X
        //   LEFT JOIN
        //   (
        //     SELECT 
        //       T.ItemId,
        //       (T.Stock + T.Total) - (T.TireNotSpac +T.Produce)  AS TotalSystem

        //     FROM(
        //       SELECT 
        // 	      P.ItemId,
        // 			  ISNULL(P.Car1_1,0) + ISNULL(P.Car1_2,0) + ISNULL(P.Car1_3,0) + ISNULL(P.Car1_4,0) +
        // 			  ISNULL(P.Car1_5,0) + ISNULL(P.Car1_6,0)+ ISNULL(P.Car1_7,0) + ISNULL(P.Car1_8,0) AS Total,
        // 			  ISNULL(P.TireNotSpac,0) AS TireNotSpac,
        // 			  ISNULL(P.Actual,0) AS Produce,
        // 			  ISNULL(P2.Stock,0) AS Stock

        //         FROM ProductionGreentireDisburseTable P
        //         LEFT JOIN ProductionGreentireDisburseTable P2 on P.ItemId = P2.ItemId AND P2.Sch_date = ? AND P2.Shift = ?
        //         WHERE P.Sch_date = ? AND P.Shift = ?)T
        //     )Y ON X.ItemId = Y.ItemId 
        //         WHERE X.Sch_date = ? AND X.Shift = ?",
        //   [
        //     $date_stock,
        //     $shift_stock,
        //     $date_sch,
        //     $shift,
        //     $date_sch,
        //     $shift

        //   ]
        // );

        $updateStock = \sqlsrv_query(
          $this->conn->dbConnect(),
          "UPDATE X
          SET X.Stock = Y.Stock
	    	--SELECT  Y.ItemId,Y.Stock
          FROM ProductionGreentireDisburseTable X
          LEFT JOIN
          (
          
		    	SELECT 
		  	T.ItemId,
			 ((T.Stock2 + T.Total2) - (T.TireNotSpac2 +T.Produce2) )  + T.CalStock AS Stock
			
			 FROM(
			      SELECT 
              P1.ItemId,
              ISNULL(P2.Car1_1,0) + ISNULL(P2.Car1_2,0) + ISNULL(P2.Car1_3,0) + ISNULL(P2.Car1_4,0) +
			  ISNULL(P2.Car1_5,0) + ISNULL(P2.Car1_6,0)+ ISNULL(P2.Car1_7,0) + ISNULL(P2.Car1_8,0) AS Total2,
			  ISNULL(P2.Actual,0) AS Produce2,
			  ISNULL(P2.TireNotSpac,0) AS TireNotSpac2,
			  ISNULL(P2.Stock,0) AS Stock2,
			  ISNULL(P1.CalStock,0) AS CalStock
			  
           --   ISNULL(P2.Stock,0) + ISNULL(P1.CalStock,0) AS Stock
              
              FROM ProductionGreentireDisburseTable  P1
              LEFT JOIN ProductionGreentireDisburseTable P2 ON P1.ItemId = P2.ItemId 
              AND P2.Sch_date = ?
              AND P2.Shift = ?
              WHERE P1.Sch_date = ? AND P1.Shift =?)T
            )Y ON X.ItemId = Y.ItemId 
                WHERE X.Sch_date = ? AND X.Shift = ?",
          [
            $dateold,
            $shiftold,
            $date_sch,
            $shift,
            $date_sch,
            $shift

          ]
        );

        if (!$updateStock) {
          sqlsrv_rollback($this->conn->dbConnect());
          return false;
        }

        sqlsrv_commit($this->conn->dbConnect());

        return true;
      }
    } catch (Exception $e) {
      return false;
    }
  }

  public function loadtire($date_sch, $shift)
  {
    try {
      // code
      $sqlId = "1=1";

      // if ($id !== null) {
      //   $sqlId = "P.Id = $id";
      // }
      // if ($shift == 1) {
      //   $dateref = $date_sch;
      //   $datelast = date('Y-m-d', strtotime($date_sch . ' -1 days'));
      //   //  $dateNext = $date_sch;
      //   $shiftref = 2;
      // } else {
      //   $dateref = date('Y-m-d', strtotime($date_sch . ' +1 days'));
      //   $datelast = $date_sch;
      //   $shiftref = 1;
      // }

      $getcheckdate = $this->sql->rows(
        $this->conn->dbConnect(),
        "SELECT  Sch_Date,Shift , COUNT(Sch_Date) OVER(PARTITION BY Sch_Date) AS CountRow
        FROM ProductionGreentireDisburseTable where Sch_Date = ?
        group by Sch_Date,Shift  order by Sch_Date desc,Shift desc
        ",
        [
          $date_sch
        ]
      );
      $datacheckCunt = $getcheckdate[0]["CountRow"];
      $shifcheck = $getcheckdate[0]["Shift"];


      if ($datacheckCunt == "" || $datacheckCunt == NULL) {

        $getdate = $this->sql->rows(
          $this->conn->dbConnect(),
          "SELECT TOP 1 Sch_Date,Shift 
              FROM ProductionGreentireDisburseTable where Sch_Date < ?
              group by Sch_Date,Shift  order by Sch_Date desc,Shift desc",
          [$date_sch]
        );
        $getdateNext = $this->sql->rows(
          $this->conn->dbConnect(),
          "SELECT TOP 1 Sch_Date,Shift 
              FROM ProductionGreentireDisburseTable where Sch_Date > ?
              group by Sch_Date,Shift  order by Sch_Date asc,Shift asc",
          [$date_sch]
        );
        $dateold = $getdate[0]["Sch_Date"];
        $shiftold = $getdate[0]["Shift"];
        $datenext = $getdateNext[0]["Sch_Date"];
        $shifnext = $getdateNext[0]["Shift"];
      } else {

        if ($datacheckCunt == 1) {
          if ($shift == 1) {
            $getdate = $this->sql->rows(
              $this->conn->dbConnect(),
              "SELECT TOP 1 Sch_Date,Shift 
              FROM ProductionGreentireDisburseTable where Sch_Date < ?
              group by Sch_Date,Shift  order by Sch_Date desc,Shift desc",
              [$date_sch]
            );
            if ($shifcheck == 1) {
              $getdateNext = $this->sql->rows(
                $this->conn->dbConnect(),
                "SELECT TOP 1 Sch_Date,Shift 
                      FROM ProductionGreentireDisburseTable where Sch_Date > ?
                      group by Sch_Date,Shift  order by Sch_Date asc,Shift asc",
                [$date_sch]
              );
            } else {
              $getdateNext = $this->sql->rows(
                $this->conn->dbConnect(),
                "SELECT TOP 1 Sch_Date,Shift 
                      FROM ProductionGreentireDisburseTable where Sch_Date >= ?
                      group by Sch_Date,Shift  order by Sch_Date asc,Shift asc",
                [$date_sch]
              );
            }
            $dateold = $getdate[0]["Sch_Date"];
            $shiftold = $getdate[0]["Shift"];
            $datenext = $getdateNext[0]["Sch_Date"];
            $shifnext = $getdateNext[0]["Shift"];
          } else {
            if ($shifcheck == 1) {
              $getdate = $this->sql->rows(
                $this->conn->dbConnect(),
                "SELECT TOP 1 Sch_Date,Shift 
                      FROM ProductionGreentireDisburseTable where Sch_Date <= ?
                      group by Sch_Date,Shift  order by Sch_Date desc,Shift desc",
                [$date_sch]
              );
              $dateold = $getdate[0]["Sch_Date"];
              $shiftold = $getdate[0]["Shift"];
            } else {
              $getdate = $this->sql->rows(
                $this->conn->dbConnect(),
                "SELECT TOP 2 Sch_Date,Shift 
                      FROM ProductionGreentireDisburseTable where Sch_Date <= ?
                      group by Sch_Date,Shift  order by Sch_Date desc,Shift desc",
                [$date_sch]
              );
              $dateold = $getdate[1]["Sch_Date"];
              $shiftold = $getdate[1]["Shift"];
            }
            $getdateNext = $this->sql->rows(
              $this->conn->dbConnect(),
              "SELECT TOP 1 Sch_Date,Shift 
                          FROM ProductionGreentireDisburseTable where Sch_Date > ?
                          group by Sch_Date,Shift  order by Sch_Date asc,Shift asc",
              [$date_sch]
            );
            $dateold = $dateold;
            $shiftold = $shiftold;
            $datenext = $getdateNext[0]["Sch_Date"];
            $shifnext = $getdateNext[0]["Shift"];
          }
        } else {
          if ($shift == 1) {
            $getdate = $this->sql->rows(
              $this->conn->dbConnect(),
              "SELECT TOP 3 Sch_Date,Shift 
                FROM ProductionGreentireDisburseTable where Sch_Date <= ?
                group by Sch_Date,Shift  order by Sch_Date desc,Shift desc
                ",
              [
                $date_sch

              ]
            );

            $getdateNext = $this->sql->rows(
              $this->conn->dbConnect(),
              "SELECT TOP 1 Sch_Date,Shift 
                FROM ProductionGreentireDisburseTable where Sch_Date <= ?
                group by Sch_Date,Shift  order by Sch_Date desc,Shift desc
                ",
              [
                $date_sch

              ]
            );
            $dateold = $getdate[2]["Sch_Date"];
            $shiftold = $getdate[2]["Shift"];
            $datenext = $getdateNext[0]["Sch_Date"];
            $shifnext = $getdateNext[0]["Shift"];
          } else {
            $getdate = $this->sql->rows(
              $this->conn->dbConnect(),
              "SELECT TOP 3 Sch_Date,Shift 
                FROM ProductionGreentireDisburseTable where Sch_Date <= ?
                group by Sch_Date,Shift  order by Sch_Date desc,Shift desc
                ",
              [
                $date_sch

              ]
            );
            $getdateNext = $this->sql->rows(
              $this->conn->dbConnect(),
              "SELECT TOP 1 Sch_Date,Shift 
                FROM ProductionGreentireDisburseTable where Sch_Date > ?
                group by Sch_Date,Shift  order by Sch_Date asc,Shift asc
                ",
              [
                $date_sch

              ]
            );
            $dateold = $getdate[1]["Sch_Date"];
            $shiftold = $getdate[1]["Shift"];
            $datenext = $getdateNext[0]["Sch_Date"];
            $shifnext = $getdateNext[0]["Shift"];
          }
        }
      }

      $query = $this->sql->rows(
        $this->conn->dbConnect(),
        "SELECT T.Id,
        T.ItemId,
        T.ItemGTName,
        T.Color,
        T.Target,
        T.Target1,
        T.Actual,
       -- T.Stock2,
        (T.Stock2 + T.Total2) - (T.TireNotSpac2 +T.Produce2) AS Stock2,
        T.CalStock,
        T.Stock,
        T.Total,
        T.TireNotSpac,
        T.Produce,
        (T.Stock + T.Total) - (T.TireNotSpac +T.Produce)  AS TotalSystem,
         CASE
           WHEN ((T.Stock + T.Total) - (T.TireNotSpac +T.Produce)) + T.Stock + T.Actual >= T.Produce THEN '-'
           ELSE 'X'
           END AS CheckCountOut,
        T.BL,
        T.CountNum - ((T.Stock + T.Total) - (T.TireNotSpac +T.Produce)) AS CompareNum,
        T.CountNum,
         CASE
           WHEN T.Total =  (ISNULL(FT.PayOfCar,0) + ISNULL(FT.PayOfCar2,0) + ISNULL(FT.PayOfCar3,0)
           + ISNULL(FT.PayOfCar4,0)
           +ISNULL(FT.PayOfCar5,0) + ISNULL(FT.PayOfCar6,0)+ ISNULL(FT.PayOfCar7,0)
           + ISNULL(FT.PayOfCar8,0)) THEN 'TRUE'
           ELSE 'FALSE'
           END AS CompareBill,
           T.Car1_1,T.Car1_2,T.Car1_3,T.Car1_4,T.Car1_5,T.Car1_6,T.Car1_7,T.Car1_8,
           T.Car2_1,T.Car2_2,T.Car2_3,T.Car2_4,T.Car2_5,T.Car2_6,T.Car2_7,T.Car2_8,
           T.CarNumber1_1,T.CarNumber1_2,T.CarNumber1_3,T.CarNumber1_4,T.CarNumber1_5,T.CarNumber1_6,T.CarNumber1_7,T.CarNumber1_8,
           T.CarNumber2_1,T.CarNumber2_2,T.CarNumber2_3,T.CarNumber2_4,T.CarNumber2_5,T.CarNumber2_6,T.CarNumber2_7,T.CarNumber2_8,
           (ISNULL(FT.PayOfCar,0) + ISNULL(FT.PayOfCar2,0) + ISNULL(FT.PayOfCar3,0)
           + ISNULL(FT.PayOfCar4,0)
           +ISNULL(FT.PayOfCar5,0) + ISNULL(FT.PayOfCar6,0)+ ISNULL(FT.PayOfCar7,0)
           + ISNULL(FT.PayOfCar8,0)) AS TotalPayOfCar
        FROM
         (
           SELECT
           P.Id,
           P.ItemId,
           I.ITEMNAME AS ItemGTName,
           C.DSG_COLOR AS Color,
           ISNULL(P.Target,0) AS Target,
           ISNULL(P.Stock,0) AS Stock,
           ISNULL(P.CalStock,0) AS CalStock,
           ISNULL(P.Target1,0) AS Target1,
           ISNULL(P.Actual,0)AS Actual,
           ISNULL(P3.Stock,0) AS Stock2,
           ISNULL(P.Car1_1,0) + ISNULL(P.Car1_2,0) + ISNULL(P.Car1_3,0) + ISNULL(P.Car1_4,0) +
           ISNULL(P.Car1_5,0) + ISNULL(P.Car1_6,0)+ ISNULL(P.Car1_7,0) + ISNULL(P.Car1_8,0) AS Total,
           ISNULL(P3.Car1_1,0) + ISNULL(P3.Car1_2,0) + ISNULL(P3.Car1_3,0) + ISNULL(P3.Car1_4,0) +
           ISNULL(P3.Car1_5,0) + ISNULL(P3.Car1_6,0)+ ISNULL(P3.Car1_7,0) + ISNULL(P3.Car1_8,0) AS Total2,
           ISNULL(P.TireNotSpac,0) AS TireNotSpac,
           ISNULL(P.Actual,0) AS Produce,
           ISNULL(P3.Actual,0) AS Produce2,
           ISNULL(P.BL,0) AS BL,
           ISNULL(P3.TireNotSpac,0) AS TireNotSpac2,
           ISNULL(P.Car2_1,0) + ISNULL(P.Car2_2,0) + ISNULL(P.Car2_3,0) + ISNULL(P.Car2_4,0) +
           ISNULL(P.Car2_5,0) + ISNULL(P.Car2_6,0)+ ISNULL(P.Car2_7,0) + ISNULL(P.Car2_8,0) AS CountNum,
           P.Car1_1,P.Car1_2,P.Car1_3,P.Car1_4,P.Car1_5,P.Car1_6,P.Car1_7,P.Car1_8,
           P.Car2_1,P.Car2_2,P.Car2_3,P.Car2_4,P.Car2_5,P.Car2_6,P.Car2_7,P.Car2_8,
           P.CarNumber1_1,P.CarNumber1_2,P.CarNumber1_3,P.CarNumber1_4,P.CarNumber1_5,P.CarNumber1_6,P.CarNumber1_7,P.CarNumber1_8,
           P.CarNumber2_1,P.CarNumber2_2,P.CarNumber2_3,P.CarNumber2_4,P.CarNumber2_5,P.CarNumber2_6,P.CarNumber2_7,P.CarNumber2_8
           FROM ProductionGreentireDisburseTable P
        

           LEFT JOIN ProductionGreentireDisburseTable P2 ON P2.ItemId = P.ItemId AND P2.Sch_date = ?
            AND P2.Shift = ?
            LEFT JOIN ProductionGreentireDisburseTable P3 ON P3.ItemId = P.ItemId AND P3.Sch_date = ?
             AND P3.Shift = ?
           LEFT JOIN [FREY\LIVE].[DSL_AX40_SP1_LIVE].[dbo].[INVENTTABLE] I ON I.ITEMID = P.ItemId

           LEFT JOIN [FREY\LIVE].[DSL_AX40_SP1_LIVE].[dbo].[DSG_ColorSizeTypeTires] C ON C.ITEMID = P.ItemId
           WHERE P.Sch_date = ? AND P.Shift = ?
         )T
         LEFT JOIN ProductionGreentireFaceOfireTable FT ON T.ItemId = FT.ItemId AND FT.Sch_Date = ? AND FT.Shift = ?
         ORDER BY T.ItemId ASC",
        [
          $datenext,
          $shifnext,
          $dateold,
          $shiftold,
          $date_sch,
          $shift,
          $date_sch,
          $shift
        ]
      );

      return $query;
    } catch (\Exception $e) {
      return response(false, $e->getMessage());
    }
  }

  public function checkcompleteSchdisburTable($date_sch, $shift)
  {
    $date_sch = date('Y-m-d', strtotime($date_sch));
    return \sqlsrv_has_rows(\sqlsrv_query(
      $this->conn->dbConnect(),
      "SELECT *
      FROM ProductionGreentireDisburseTable
      WHERE Sch_date = ? AND Shift = ?",
      [
        $date_sch,
        $shift
      ]
    ));
  }

  public function Deletedisbursement($id)
  {

    $delete = sqlsrv_query(
      $this->conn->dbConnect(),
      "DELETE ProductionGreentireDisburseTable
      WHERE Id = ?",
      [
        $id
      ]
    );

    if ($delete) {
      return true;
    } else {
      return false;
    }
  }

  public function UpdateSchDisburTable($TireNotSpac, $date_sch, $shift, $id, $CalStock)
  {

    $date = date("Y-m-d H:i:s");
    // if ($shift == 1) {
    //   $dateref = date('Y-m-d', strtotime($date_sch . ' -1 days'));
    //   $shiftref = 2;
    // } else {
    //   $dateref = $date_sch;
    //   $shiftref = 1;
    // }

    $getcheckdate = $this->sql->rows(
      $this->conn->dbConnect(),
      "SELECT  Sch_Date,Shift , COUNT(Sch_Date) OVER(PARTITION BY Sch_Date) AS CountRow
      FROM ProductionGreentireDisburseTable where Sch_Date = ?
      group by Sch_Date,Shift  order by Sch_Date desc,Shift desc
      ",
      [
        $date_sch
      ]
    );
    $datacheckCunt = $getcheckdate[0]["CountRow"];
    $shifcheck = $getcheckdate[0]["Shift"];


    if ($datacheckCunt == "" || $datacheckCunt == NULL) {

      $getdate = $this->sql->rows(
        $this->conn->dbConnect(),
        "SELECT TOP 1 Sch_Date,Shift 
            FROM ProductionGreentireDisburseTable where Sch_Date < ?
            group by Sch_Date,Shift  order by Sch_Date desc,Shift desc",
        [$date_sch]
      );
      $getdateNext = $this->sql->rows(
        $this->conn->dbConnect(),
        "SELECT TOP 1 Sch_Date,Shift 
            FROM ProductionGreentireDisburseTable where Sch_Date > ?
            group by Sch_Date,Shift  order by Sch_Date asc,Shift asc",
        [$date_sch]
      );
      $dateold = $getdate[0]["Sch_Date"];
      $shiftold = $getdate[0]["Shift"];
      $datenext = $getdateNext[0]["Sch_Date"];
      $shifnext = $getdateNext[0]["Shift"];
    } else {

      if ($datacheckCunt == 1) {
        if ($shift == 1) {
          $getdate = $this->sql->rows(
            $this->conn->dbConnect(),
            "SELECT TOP 1 Sch_Date,Shift 
            FROM ProductionGreentireDisburseTable where Sch_Date < ?
            group by Sch_Date,Shift  order by Sch_Date desc,Shift desc",
            [$date_sch]
          );
          if ($shifcheck == 1) {
            $getdateNext = $this->sql->rows(
              $this->conn->dbConnect(),
              "SELECT TOP 1 Sch_Date,Shift 
                    FROM ProductionGreentireDisburseTable where Sch_Date > ?
                    group by Sch_Date,Shift  order by Sch_Date asc,Shift asc",
              [$date_sch]
            );
          } else {
            $getdateNext = $this->sql->rows(
              $this->conn->dbConnect(),
              "SELECT TOP 1 Sch_Date,Shift 
                    FROM ProductionGreentireDisburseTable where Sch_Date >= ?
                    group by Sch_Date,Shift  order by Sch_Date asc,Shift asc",
              [$date_sch]
            );
          }
          $dateold = $getdate[0]["Sch_Date"];
          $shiftold = $getdate[0]["Shift"];
          $datenext = $getdateNext[0]["Sch_Date"];
          $shifnext = $getdateNext[0]["Shift"];
        } else {
          if ($shifcheck == 1) {
            $getdate = $this->sql->rows(
              $this->conn->dbConnect(),
              "SELECT TOP 1 Sch_Date,Shift 
                    FROM ProductionGreentireDisburseTable where Sch_Date <= ?
                    group by Sch_Date,Shift  order by Sch_Date desc,Shift desc",
              [$date_sch]
            );
            $dateold = $getdate[0]["Sch_Date"];
            $shiftold = $getdate[0]["Shift"];
          } else {
            $getdate = $this->sql->rows(
              $this->conn->dbConnect(),
              "SELECT TOP 2 Sch_Date,Shift 
                    FROM ProductionGreentireDisburseTable where Sch_Date <= ?
                    group by Sch_Date,Shift  order by Sch_Date desc,Shift desc",
              [$date_sch]
            );
            $dateold = $getdate[1]["Sch_Date"];
            $shiftold = $getdate[1]["Shift"];
          }
          $getdateNext = $this->sql->rows(
            $this->conn->dbConnect(),
            "SELECT TOP 1 Sch_Date,Shift 
                        FROM ProductionGreentireDisburseTable where Sch_Date > ?
                        group by Sch_Date,Shift  order by Sch_Date asc,Shift asc",
            [$date_sch]
          );
          $dateold = $dateold;
          $shiftold = $shiftold;
          $datenext = $getdateNext[0]["Sch_Date"];
          $shifnext = $getdateNext[0]["Shift"];
        }
      } else {
        if ($shift == 1) {
          $getdate = $this->sql->rows(
            $this->conn->dbConnect(),
            "SELECT TOP 3 Sch_Date,Shift 
              FROM ProductionGreentireDisburseTable where Sch_Date <= ?
              group by Sch_Date,Shift  order by Sch_Date desc,Shift desc
              ",
            [
              $date_sch

            ]
          );

          $getdateNext = $this->sql->rows(
            $this->conn->dbConnect(),
            "SELECT TOP 1 Sch_Date,Shift 
              FROM ProductionGreentireDisburseTable where Sch_Date <= ?
              group by Sch_Date,Shift  order by Sch_Date desc,Shift desc
              ",
            [
              $date_sch

            ]
          );
          $dateold = $getdate[2]["Sch_Date"];
          $shiftold = $getdate[2]["Shift"];
          $datenext = $getdateNext[0]["Sch_Date"];
          $shifnext = $getdateNext[0]["Shift"];
        } else {
          $getdate = $this->sql->rows(
            $this->conn->dbConnect(),
            "SELECT TOP 3 Sch_Date,Shift 
              FROM ProductionGreentireDisburseTable where Sch_Date <= ?
              group by Sch_Date,Shift  order by Sch_Date desc,Shift desc
              ",
            [
              $date_sch

            ]
          );
          $getdateNext = $this->sql->rows(
            $this->conn->dbConnect(),
            "SELECT TOP 1 Sch_Date,Shift 
              FROM ProductionGreentireDisburseTable where Sch_Date > ?
              group by Sch_Date,Shift  order by Sch_Date asc,Shift asc
              ",
            [
              $date_sch

            ]
          );
          $dateold = $getdate[1]["Sch_Date"];
          $shiftold = $getdate[1]["Shift"];
          $datenext = $getdateNext[0]["Sch_Date"];
          $shifnext = $getdateNext[0]["Shift"];
        }
      }
    }

    $update = \sqlsrv_query(
      $this->conn->dbConnect(),
      "UPDATE ProductionGreentireDisburseTable
      SET TireNotSpac = ?,
      CalStock = ?,
      UpdateBy = ?,
      UpdateDate =?
      WHERE Id = ?",
      [
        $TireNotSpac,
        $CalStock,
        $_SESSION["user_login"],
        $date,
        $id
      ]
    );

    // $getdata = $this->sql->rows(
    //   $this->conn->dbConnect(),
    //   "SELECT
    //   ((ISNULL(P2.Stock,0) + (ISNULL(P.Car1_1,0) + ISNULL(P.Car1_2,0) +
    //   ISNULL(P.Car1_3,0) + ISNULL(P.Car1_4,0) +
    //   ISNULL(P.Car1_5,0) + ISNULL(P.Car1_6,0)+ ISNULL(P.Car1_7,0) +
    //   ISNULL(P.Car1_8,0))) - (ISNULL(P.TireNotSpac,0) + ISNULL(B.Actual,0))) + ISNULL(P.CalStock,0)
    //   AS Stock
    //   FROM ProductionGreentireDisburseTable P
    //   LEFT JOIN ProductionGreentireDisburseTable P2 ON P2.ItemId = P.ItemId
    //   AND P2.Sch_date = ? AND P2.Shift = ?
    //   LEFT JOIN BuildSch B ON B.ItemId = P.ItemId
    //    AND B.DateBuild = ? AND B.Shift = ?
    //    WHERE P.Id = ?",
    //   [
    //     $dateref,
    //     $shiftref,
    //     $date_sch,
    //     $shift,
    //     $id
    //   ]
    // );

    // $getdata = $this->sql->rows(
    //   $this->conn->dbConnect(),
    //   "SELECT 
    //   P1.ItemId,
    //   ISNULL(P2.Stock,0) + ISNULL(P1.CalStock,0) AS Stock
    //   FROM ProductionGreentireDisburseTable  P1
    //   LEFT JOIN ProductionGreentireDisburseTable P2 ON P1.ItemId = P2.ItemId 
    //   AND P2.Sch_date = ? 
    //   AND P2.Shift = ?
    //   WHERE P1.Id",
    //   [
    //     $dateold,
    //     $shiftold,
    //     $id
    //   ]
    // );

    $getdate = $this->sql->rows(
      $this->conn->dbConnect(),
      "SELECT 
      T.ItemId,
			((T.Stock2 + T.Total2) - (T.TireNotSpac2 +T.Produce2) )  + T.CalStock AS Stock
      FROM (
      SELECT 
        P1.ItemId,
        ISNULL(P2.Car1_1,0) + ISNULL(P2.Car1_2,0) + ISNULL(P2.Car1_3,0) + ISNULL(P2.Car1_4,0) +
        ISNULL(P2.Car1_5,0) + ISNULL(P2.Car1_6,0)+ ISNULL(P2.Car1_7,0) + ISNULL(P2.Car1_8,0) AS Total2,
        ISNULL(P2.Actual,0) AS Produce2,
        ISNULL(P2.TireNotSpac,0) AS TireNotSpac2,
        ISNULL(P2.Stock,0) AS Stock2,
        ISNULL(P1.CalStock,0) AS CalStock
         FROM ProductionGreentireDisburseTable  P1
         LEFT JOIN ProductionGreentireDisburseTable P2 ON P1.ItemId = P2.ItemId 
         AND P2.Sch_date = ? 
         AND P2.Shift = ?
         WHERE P1.Id = ?)T",
      [
        $dateold,
        $shiftold,
        $id

      ]
    );
    $getdatastock =  $getdate[0]["Stock"];
    $updateStockInplan = \sqlsrv_query(
      $this->conn->dbConnect(),
      "UPDATE ProductionGreentireDisburseTable
       SET Stock = ?
       WHERE Id = ?",
      [
        $getdatastock,
        $id
      ]
    );

    if ($update) {
      return true;
    } else {
      return false;
    }
  }
  public function UpdateSchDisburTableCar(
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
    $date_sch,
    $shift
  ) {

    $date = date("Y-m-d H:i:s");
    // if ($Shift == 1) {
    //   $date_stock = date('Y-m-d', strtotime($sch_date . ' -1 days'));
    //   $shift_stock = 2;
    // } else {
    //   $date_stock = $sch_date;
    //   $shift_stock = 1;
    // }

    $getcheckdate = $this->sql->rows(
      $this->conn->dbConnect(),
      "SELECT  Sch_Date,Shift , COUNT(Sch_Date) OVER(PARTITION BY Sch_Date) AS CountRow
      FROM ProductionGreentireDisburseTable where Sch_Date = ?
      group by Sch_Date,Shift  order by Sch_Date desc,Shift desc
      ",
      [
        $date_sch
      ]
    );
    $datacheckCunt = $getcheckdate[0]["CountRow"];
    $shifcheck = $getcheckdate[0]["Shift"];


    if ($datacheckCunt == "" || $datacheckCunt == NULL) {

      $getdate = $this->sql->rows(
        $this->conn->dbConnect(),
        "SELECT TOP 1 Sch_Date,Shift 
            FROM ProductionGreentireDisburseTable where Sch_Date < ?
            group by Sch_Date,Shift  order by Sch_Date desc,Shift desc",
        [$date_sch]
      );
      $getdateNext = $this->sql->rows(
        $this->conn->dbConnect(),
        "SELECT TOP 1 Sch_Date,Shift 
            FROM ProductionGreentireDisburseTable where Sch_Date > ?
            group by Sch_Date,Shift  order by Sch_Date asc,Shift asc",
        [$date_sch]
      );
      $dateold = $getdate[0]["Sch_Date"];
      $shiftold = $getdate[0]["Shift"];
      $datenext = $getdateNext[0]["Sch_Date"];
      $shifnext = $getdateNext[0]["Shift"];
    } else {

      if ($datacheckCunt == 1) {
        if ($shift == 1) {
          $getdate = $this->sql->rows(
            $this->conn->dbConnect(),
            "SELECT TOP 1 Sch_Date,Shift 
            FROM ProductionGreentireDisburseTable where Sch_Date < ?
            group by Sch_Date,Shift  order by Sch_Date desc,Shift desc",
            [$date_sch]
          );
          if ($shifcheck == 1) {
            $getdateNext = $this->sql->rows(
              $this->conn->dbConnect(),
              "SELECT TOP 1 Sch_Date,Shift 
                    FROM ProductionGreentireDisburseTable where Sch_Date > ?
                    group by Sch_Date,Shift  order by Sch_Date asc,Shift asc",
              [$date_sch]
            );
          } else {
            $getdateNext = $this->sql->rows(
              $this->conn->dbConnect(),
              "SELECT TOP 1 Sch_Date,Shift 
                    FROM ProductionGreentireDisburseTable where Sch_Date >= ?
                    group by Sch_Date,Shift  order by Sch_Date asc,Shift asc",
              [$date_sch]
            );
          }
          $dateold = $getdate[0]["Sch_Date"];
          $shiftold = $getdate[0]["Shift"];
          $datenext = $getdateNext[0]["Sch_Date"];
          $shifnext = $getdateNext[0]["Shift"];
        } else {
          if ($shifcheck == 1) {
            $getdate = $this->sql->rows(
              $this->conn->dbConnect(),
              "SELECT TOP 1 Sch_Date,Shift 
                    FROM ProductionGreentireDisburseTable where Sch_Date <= ?
                    group by Sch_Date,Shift  order by Sch_Date desc,Shift desc",
              [$date_sch]
            );
            $dateold = $getdate[0]["Sch_Date"];
            $shiftold = $getdate[0]["Shift"];
          } else {
            $getdate = $this->sql->rows(
              $this->conn->dbConnect(),
              "SELECT TOP 2 Sch_Date,Shift 
                    FROM ProductionGreentireDisburseTable where Sch_Date <= ?
                    group by Sch_Date,Shift  order by Sch_Date desc,Shift desc",
              [$date_sch]
            );
            $dateold = $getdate[1]["Sch_Date"];
            $shiftold = $getdate[1]["Shift"];
          }
          $getdateNext = $this->sql->rows(
            $this->conn->dbConnect(),
            "SELECT TOP 1 Sch_Date,Shift 
                        FROM ProductionGreentireDisburseTable where Sch_Date > ?
                        group by Sch_Date,Shift  order by Sch_Date asc,Shift asc",
            [$date_sch]
          );
          $dateold = $dateold;
          $shiftold = $shiftold;
          $datenext = $getdateNext[0]["Sch_Date"];
          $shifnext = $getdateNext[0]["Shift"];
        }
      } else {
        if ($shift == 1) {
          $getdate = $this->sql->rows(
            $this->conn->dbConnect(),
            "SELECT TOP 3 Sch_Date,Shift 
              FROM ProductionGreentireDisburseTable where Sch_Date <= ?
              group by Sch_Date,Shift  order by Sch_Date desc,Shift desc
              ",
            [
              $date_sch

            ]
          );

          $getdateNext = $this->sql->rows(
            $this->conn->dbConnect(),
            "SELECT TOP 1 Sch_Date,Shift 
              FROM ProductionGreentireDisburseTable where Sch_Date <= ?
              group by Sch_Date,Shift  order by Sch_Date desc,Shift desc
              ",
            [
              $date_sch

            ]
          );
          $dateold = $getdate[2]["Sch_Date"];
          $shiftold = $getdate[2]["Shift"];
          $datenext = $getdateNext[0]["Sch_Date"];
          $shifnext = $getdateNext[0]["Shift"];
        } else {
          $getdate = $this->sql->rows(
            $this->conn->dbConnect(),
            "SELECT TOP 3 Sch_Date,Shift 
              FROM ProductionGreentireDisburseTable where Sch_Date <= ?
              group by Sch_Date,Shift  order by Sch_Date desc,Shift desc
              ",
            [
              $date_sch

            ]
          );
          $getdateNext = $this->sql->rows(
            $this->conn->dbConnect(),
            "SELECT TOP 1 Sch_Date,Shift 
              FROM ProductionGreentireDisburseTable where Sch_Date > ?
              group by Sch_Date,Shift  order by Sch_Date asc,Shift asc
              ",
            [
              $date_sch

            ]
          );
          $dateold = $getdate[1]["Sch_Date"];
          $shiftold = $getdate[1]["Shift"];
          $datenext = $getdateNext[0]["Sch_Date"];
          $shifnext = $getdateNext[0]["Shift"];
        }
      }
    }

    $update = \sqlsrv_query(
      $this->conn->dbConnect(),
      "UPDATE ProductionGreentireDisburseTable
          SET Car1_1 = ?,
              Car1_2 = ?,
              Car1_3 = ?,
              Car1_4 = ?,
              Car1_5 = ?,
              Car1_6 = ?,
              Car1_7 = ?,
              Car1_8 = ?,
              Car2_1 = ?,
              Car2_2 = ?,
              Car2_3 = ?,
              Car2_4 = ?,
              Car2_5 = ?,
              Car2_6 = ?,
              Car2_7 = ?,
              Car2_8 = ?,
              CarNumber1_1 = ?,
              CarNumber1_2 = ?,
              CarNumber1_3 = ?,
              CarNumber1_4 = ?,
              CarNumber1_5 = ?,
              CarNumber1_6 = ?,
              CarNumber1_7 = ?,
              CarNumber1_8 = ?,
              CarNumber2_1 = ?,
              CarNumber2_2 = ?,
              CarNumber2_3 = ?,
              CarNumber2_4 = ?,
              CarNumber2_5 = ?,
              CarNumber2_6 = ?,
              CarNumber2_7 = ?,
              CarNumber2_8 = ?,
              UpdateBy = ?,
              UpdateDate =?
          WHERE Id = ?",
      [
        $Car1_1, $Car1_2, $Car1_3, $Car1_4, $Car1_5,
        $Car1_6, $Car1_7, $Car1_8, $Car2_1, $Car2_2,
        $Car2_3, $Car2_4, $Car2_5, $Car2_6, $Car2_7,
        $Car2_8, $num1_1, $num1_2, $num1_3, $num1_4,
        $num1_5, $num1_6, $num1_7, $num1_8, $num2_1,
        $num2_2, $num2_3, $num2_4, $num2_5, $num2_6,
        $num2_7, $num2_8,
        $_SESSION["user_login"],
        $date,
        $id
      ]
    );

    // $updateStock = \sqlsrv_query(
    //   $this->conn->dbConnect(),
    //   "UPDATE X
    //   SET X.Stock = Y.TotalSystem + ISNULL(X.CalStock,0)
    //   FROM ProductionGreentireDisburseTable X
    //   LEFT JOIN
    //   (
    //     SELECT 
    //       T.ItemId,
    //       (T.Stock + T.Total) - (T.TireNotSpac +T.Produce)  AS TotalSystem
    //     FROM(
    //       SELECT 
    //         P.ItemId,
    //         ISNULL(P.Car1_1,0) + ISNULL(P.Car1_2,0) + ISNULL(P.Car1_3,0) + ISNULL(P.Car1_4,0) +
    //         ISNULL(P.Car1_5,0) + ISNULL(P.Car1_6,0)+ ISNULL(P.Car1_7,0) + ISNULL(P.Car1_8,0) AS Total,
    //         ISNULL(P.TireNotSpac,0) AS TireNotSpac,
    //         ISNULL(P.Actual,0) AS Produce,
    //         ISNULL(P2.Stock,0) AS Stock
    //         FROM ProductionGreentireDisburseTable P
    //         LEFT JOIN ProductionGreentireDisburseTable P2 on P.ItemId = P2.ItemId AND P2.Sch_date = ? AND P2.Shift = ?
    //         WHERE P.Sch_date = ? AND P.Shift = ?)T
    //     )Y ON X.ItemId = Y.ItemId 
    //         WHERE X.Id = ?",
    //   [
    //     $date_stock,
    //     $shift_stock,
    //     $sch_date,
    //     $Shift,
    //     $id


    //   ]
    // );

    $updateStock = \sqlsrv_query(
      $this->conn->dbConnect(),
      "UPDATE X
        SET X.Stock = Y.Stock
      --SELECT  Y.ItemId,Y.Stock
       FROM ProductionGreentireDisburseTable X
       LEFT JOIN
      (
      SELECT 
        T.ItemId,
       ((T.Stock2 + T.Total2) - (T.TireNotSpac2 +T.Produce2) )  - T.CalStock AS Stock
      FROM(
        SELECT 
          P1.ItemId,
          ISNULL(P2.Car1_1,0) + ISNULL(P2.Car1_2,0) + ISNULL(P2.Car1_3,0) + ISNULL(P2.Car1_4,0) +
          ISNULL(P2.Car1_5,0) + ISNULL(P2.Car1_6,0)+ ISNULL(P2.Car1_7,0) + ISNULL(P2.Car1_8,0) AS Total2,
          ISNULL(P2.Actual,0) AS Produce2,
          ISNULL(P2.TireNotSpac,0) AS TireNotSpac2,
          ISNULL(P2.Stock,0) AS Stock2,
          ISNULL(P1.CalStock,0) AS CalStock
          FROM ProductionGreentireDisburseTable  P1
          LEFT JOIN ProductionGreentireDisburseTable P2 ON P1.ItemId = P2.ItemId 
          AND P2.Sch_date = ?
          AND P2.Shift = ?
          WHERE P1.Sch_date = ? AND P1.Shift =?)T
        )Y ON X.ItemId = Y.ItemId 
            WHERE X.Sch_date = ? AND X.Shift = ?",
      [
        $dateold,
        $shiftold,
        $date_sch,
        $shift,
        $date_sch,
        $shift

      ]
    );


    if ($update) {
      return true;
    } else {
      return false;
    }
  }
  public function insertSchfacetireTable($date_sch, $shift, $copy, $date_gen, $shift_gen, $gen_emp, $date_emp, $shift_emp)
  {
    try {
      $date = date("Y-m-d H:i:s");


      if ($copy == 0) {



        $insertSch = \sqlsrv_query(
          $this->conn->dbConnect(),
          "INSERT INTO ProductionGreentireFaceOfireTable (
               Sch_date,
               Shift,
               CreateDate,
               CreateBy
              ) VALUES(?, ?, ?, ?)",
          [
            $date_sch,
            $shift,
            $date,
            $_SESSION["user_login"]

          ]
        );

        if (!$insertSch) {
          sqlsrv_rollback($this->conn->dbConnect());
          return false;
        }

        sqlsrv_commit($this->conn->dbConnect());
        return true;
      } else {

        if ($shift == 1) {
          $shiftNext = 2;
          $date_Next = date('Y-m-d', strtotime($date_sch . ' -1 days'));
        } else {
          $shiftNext = 1;
          $date_Next = $date_sch;
        }

        $userCreate = $_SESSION["user_login"];

        $q = sqlsrv_has_rows(sqlsrv_query(
          $this->conn->dbConnect(),
          "SELECT *
             FROM ProductionGreentireFaceOfireTable
             WHERE Sch_date = ? AND Shift = ?",
          [
            $date_sch,
            $shift
          ]
        ));

        // if ($q === true) {
        //   $delete = sqlsrv_query(
        //     $this->conn->dbConnect(),
        //     "DELETE ProductionGreentireFaceOfireTable
        //        WHERE Sch_date = ? AND Shift = ?",
        //     [
        //       $date_sch,
        //       $shift
        //     ]
        //   );
        // }

        // $checksch = sqlsrv_has_rows(sqlsrv_query(
        //   $this->conn->dbConnect(),
        //   "SELECT *
        //   FROM BuildSch
        //   WHERE DateBuild = ? AND Shift = ?",
        //   [
        //     $date_gen,
        //     $shift_gen
        //   ]
        // ));
        //
        // if($checksch === false){
        //   return false;
        //
        // }

        $getcheckdate = $this->sql->rows(
          $this->conn->dbConnect(),
          "SELECT  Sch_Date,Shift , COUNT(Sch_Date) OVER(PARTITION BY Sch_Date) AS CountRow
          FROM ProductionGreentireFaceOfireTable where Sch_Date = ?
          group by Sch_Date,Shift  order by Sch_Date desc,Shift desc
          ",
          [
            $date_sch
          ]
        );
        $datacheckCunt = $getcheckdate[0]["CountRow"];
        // $datacheck123 = $getcheckdate[0]["CountRow"];


        if ($datacheckCunt == "" || $datacheckCunt == NULL) {

          $getdate = $this->sql->rows(
            $this->conn->dbConnect(),
            "SELECT TOP 1 Sch_Date,Shift 
                FROM ProductionGreentireFaceOfireTable where Sch_Date < ?
                group by Sch_Date,Shift  order by Sch_Date desc,Shift desc",
            [$date_sch]
          );
          $getdateNext = $this->sql->rows(
            $this->conn->dbConnect(),
            "SELECT TOP 1 Sch_Date,Shift 
                FROM ProductionGreentireFaceOfireTable where Sch_Date > ?
                group by Sch_Date,Shift  order by Sch_Date asc,Shift asc",
            [$date_sch]
          );
          $dateold = $getdate[0]["Sch_Date"];
          $shiftold = $getdate[0]["Shift"];
          $datenext = $getdateNext[0]["Sch_Date"];
          $shifnext = $getdateNext[0]["Shift"];
        } else {

          if ($datacheckCunt == 1) {
            if ($shift == 1) {
              $getdate = $this->sql->rows(
                $this->conn->dbConnect(),
                "SELECT TOP 1 Sch_Date,Shift 
                FROM ProductionGreentireFaceOfireTable where Sch_Date < ?
                group by Sch_Date,Shift  order by Sch_Date desc,Shift desc",
                [$date_sch]
              );
              if ($shifcheck == 1) {
                $getdateNext = $this->sql->rows(
                  $this->conn->dbConnect(),
                  "SELECT TOP 1 Sch_Date,Shift 
                        FROM ProductionGreentireFaceOfireTable where Sch_Date > ?
                        group by Sch_Date,Shift  order by Sch_Date asc,Shift asc",
                  [$date_sch]
                );
              } else {
                $getdateNext = $this->sql->rows(
                  $this->conn->dbConnect(),
                  "SELECT TOP 1 Sch_Date,Shift 
                        FROM ProductionGreentireFaceOfireTable where Sch_Date >= ?
                        group by Sch_Date,Shift  order by Sch_Date asc,Shift asc",
                  [$date_sch]
                );
              }
              $dateold = $getdate[0]["Sch_Date"];
              $shiftold = $getdate[0]["Shift"];
              $datenext = $getdateNext[0]["Sch_Date"];
              $shifnext = $getdateNext[0]["Shift"];
            } else {
              if ($shifcheck == 1) {
                $getdate = $this->sql->rows(
                  $this->conn->dbConnect(),
                  "SELECT TOP 1 Sch_Date,Shift 
                        FROM ProductionGreentireFaceOfireTable where Sch_Date <= ?
                        group by Sch_Date,Shift  order by Sch_Date desc,Shift desc",
                  [$date_sch]
                );
                $dateold = $getdate[0]["Sch_Date"];
                $shiftold = $getdate[0]["Shift"];
              } else {
                $getdate = $this->sql->rows(
                  $this->conn->dbConnect(),
                  "SELECT TOP 2 Sch_Date,Shift 
                        FROM ProductionGreentireFaceOfireTable where Sch_Date <= ?
                        group by Sch_Date,Shift  order by Sch_Date desc,Shift desc",
                  [$date_sch]
                );
                $dateold = $getdate[1]["Sch_Date"];
                $shiftold = $getdate[1]["Shift"];
              }
              $getdateNext = $this->sql->rows(
                $this->conn->dbConnect(),
                "SELECT TOP 1 Sch_Date,Shift 
                            FROM ProductionGreentireFaceOfireTable where Sch_Date > ?
                            group by Sch_Date,Shift  order by Sch_Date asc,Shift asc",
                [$date_sch]
              );
              $dateold = $dateold;
              $shiftold = $shiftold;
              $datenext = $getdateNext[0]["Sch_Date"];
              $shifnext = $getdateNext[0]["Shift"];
            }
          } else {
            if ($shift == 1) {
              $getdate = $this->sql->rows(
                $this->conn->dbConnect(),
                "SELECT TOP 3 Sch_Date,Shift 
                  FROM ProductionGreentireFaceOfireTable where Sch_Date <= ?
                  group by Sch_Date,Shift  order by Sch_Date desc,Shift desc
                  ",
                [
                  $date_sch

                ]
              );

              $getdateNext = $this->sql->rows(
                $this->conn->dbConnect(),
                "SELECT TOP 1 Sch_Date,Shift 
                  FROM ProductionGreentireFaceOfireTable where Sch_Date <= ?
                  group by Sch_Date,Shift  order by Sch_Date desc,Shift desc
                  ",
                [
                  $date_sch

                ]
              );
              $dateold = $getdate[2]["Sch_Date"];
              $shiftold = $getdate[2]["Shift"];
              $datenext = $getdateNext[0]["Sch_Date"];
              $shifnext = $getdateNext[0]["Shift"];
            } else {
              $getdate = $this->sql->rows(
                $this->conn->dbConnect(),
                "SELECT TOP 3 Sch_Date,Shift 
                  FROM ProductionGreentireFaceOfireTable where Sch_Date <= ?
                  group by Sch_Date,Shift  order by Sch_Date desc,Shift desc
                  ",
                [
                  $date_sch

                ]
              );
              $getdateNext = $this->sql->rows(
                $this->conn->dbConnect(),
                "SELECT TOP 1 Sch_Date,Shift 
                  FROM ProductionGreentireFaceOfireTable where Sch_Date > ?
                  group by Sch_Date,Shift  order by Sch_Date asc,Shift asc
                  ",
                [
                  $date_sch

                ]
              );
              $dateold = $getdate[1]["Sch_Date"];
              $shiftold = $getdate[1]["Shift"];
              $datenext = $getdateNext[0]["Sch_Date"];
              $shifnext = $getdateNext[0]["Shift"];
            }
          }
        }


        $insertSch = \sqlsrv_query(
          $this->conn->dbConnect(),
          "INSERT INTO ProductionGreentireFaceOfireTable (
                ItemId,
                Sch_Date,
                Shift,
                Sch_dateIn,
                ShiftIn,
                CreateBy,
                CreateDate,
                Stock

              )
              SELECT
			  X.ItemId,
              X.Sch_Date,
              X.Shift,
              X.Sch_DateIn,
              X.ShiftIn,
              X.CreateBy,
              X.CreateDate,
             -- ((X.Stock2 + X.TotalProduct ) - (X.TotalPayOfCar + X.TireNotSpec)) + X.CalStock AS Stock
             (X.Stock2+ X.TotalProduct) - (X.TotalPayOfCar + ISNULL(X.TireNotSpec,0))+ X.CalStock AS Stock
            -- X.Stock2 + X.CalStock AS Stock

		FROM(
				SELECT
              T.ItemId,
              T.Sch_Date,
              T.Shift,
              T.Sch_DateIn,
              T.ShiftIn,
              T.CreateBy,
              T.CreateDate,
              (ISNULL(PP.NumberCar1,0) + ISNULL(PP.NumberCar2,0) +ISNULL(PP.NumberCar3,0)
            + ISNULL(PP.NumberCar4,0)
  		      + ISNULL(PP.NumberCar5,0) + ISNULL(PP.NumberCar6,0) + ISNULL(PP.NumberCar7,0)
            + ISNULL(PP.NumberCar8,0) )AS TotalProduct,
             (ISNULL(PP.PayOfCar,0) + ISNULL(PP.PayOfCar2,0) + ISNULL(PP.PayOfCar3,0)
            + ISNULL(PP.PayOfCar4,0)
  		      +ISNULL(PP.PayOfCar5,0) + ISNULL(PP.PayOfCar6,0)+ ISNULL(PP.PayOfCar7,0)
            + ISNULL(PP.PayOfCar8,0)) AS TotalPayOfCar,
            ISNULL(PP.Stock,0) AS Stock2,
            ISNULL (P.CalStock,0) AS CalStock,
            ISNULL(PP.TireNotSpec,0) AS TireNotSpec
              FROM
              (
                SELECT
                BB.ITEMID AS ItemId,
  	            '$date_sch' AS Sch_Date,
  			        '$shift' AS Shift,
  			        '$date_gen' AS Sch_DateIn,
  			        '$shift_gen' AS ShiftIn,
                '$userCreate' AS CreateBy,
                '$date' AS CreateDate
  	             FROM BuildSch P
                 LEFT JOIN [FREY\LIVE].[DSL_AX40_SP1_LIVE].[dbo].[BOMVERSION] B ON B.ITEMID = P.ItemId  AND B.DSG_RefCompanyId = 'DSL' and B.DATAAREAID  = 'dv' AND B.ACTIVE = '1'
                 LEFT JOIN [FREY\LIVE].[DSL_AX40_SP1_LIVE].[dbo].[BOM] BB ON BB.BOMID = B.BOMID and BB.DATAAREAID = B.DATAAREAID
                 JOIN [FREY\LIVE].[DSL_AX40_SP1_LIVE].[dbo].[INVENTTABLE] IT ON IT.ITEMID = BB.ITEMID AND IT.DSGSUBGROUPID IN ('SM0801','SM0804','SM0806','SM0807')
                  WHERE P.DateBuild = ? AND P.Shift = ? AND IT.DATAAREAID = 'dv'
                   GROUP BY BB.ITEMID
	                UNION ALL
	                 SELECT ItemId,
			              '$date_sch' AS Sch_Date,
			              '$shift' AS Shift,
			              '$date_gen' AS Sch_DateIn,
			              '$shift_gen' AS ShiftIn,
                    '$userCreate' AS CreateBy,
                    '$date' AS CreateDate
	                   FROM ProductionGreentireFaceOfireTable WHERE SCH_Date = ? AND Shift = ?
                    )T
                    LEFT JOIN ProductionGreentireFaceOfireTable PP ON PP.ItemId = T.ItemId AND PP.SCH_Date = ? AND PP.Shift = ?
                    LEFT JOIN ProductionGreentireFaceOfireTable P ON P.ItemId = T.ItemId AND P.SCH_Date = ? AND P.Shift = ?

                    GROUP BY T.ItemId,T.Sch_Date,T.Shift,T.Sch_DateIn,T.ShiftIn,T.CreateBy,T.CreateDate,PP.Stock,PP.NumberCar1,PP.NumberCar2,PP.NumberCar3,PP.NumberCar4,PP.CalStock,
                    PP.NumberCar5,PP.NumberCar6,PP.NumberCar7,PP.NumberCar8,PP.PayOfCar,PP.PayOfCar2,PP.PayOfCar3,PP.PayOfCar4,PP.PayOfCar5,PP.PayOfCar6,PP.PayOfCar7,PP.PayOfCar8,PP.TireNotSpec,P.CalStock,PP.TireNotSpec) X",
          [
            $date_gen,
            $shift_gen,
            $dateold,
            $shiftold,
            $dateold,
            $shiftold,
            $date_gen,
            $shift_gen

          ]
        );

        if (!$insertSch) {
          sqlsrv_rollback($this->conn->dbConnect());
          return false;
        }

        sqlsrv_commit($this->conn->dbConnect());
        return true;
      }
    } catch (Exception $e) {
      return false;
    }
  }
  public function loadfacetire($date_sch, $shift)
  {
    try {
      // // code
      // $sqlId = "1=1";
      // if ($id !== null) {
      //   $sqlId = "P.Id = $id";
      // }
      // if ($shift == 1) {
      //   $dateref = date('Y-m-d', strtotime($date_sch . ' -1 days'));
      //   //  $dateNext = $date_sch;
      //   $shiftref = 2;
      // } else {
      //   $dateref = $date_sch;
      //   //$dateNext = date('Y-m-d', strtotime($date_sch . ' +1 days'));
      //   $shiftref = 1;
      // }

      $getcheckdate = $this->sql->rows(
        $this->conn->dbConnect(),
        "SELECT  Sch_Date,Shift , COUNT(Sch_Date) OVER(PARTITION BY Sch_Date) AS CountRow
        FROM ProductionGreentireFaceOfireTable where Sch_Date = ?
        group by Sch_Date,Shift  order by Sch_Date desc,Shift desc
        ",
        [
          $date_sch
        ]
      );
      $datacheckCunt = $getcheckdate[0]["CountRow"];
      // $datacheck123 = $getcheckdate[0]["CountRow"];


      if ($datacheckCunt == "" || $datacheckCunt == NULL) {

        $getdate = $this->sql->rows(
          $this->conn->dbConnect(),
          "SELECT TOP 1 Sch_Date,Shift 
              FROM ProductionGreentireFaceOfireTable where Sch_Date < ?
              group by Sch_Date,Shift  order by Sch_Date desc,Shift desc",
          [$date_sch]
        );
        $getdateNext = $this->sql->rows(
          $this->conn->dbConnect(),
          "SELECT TOP 1 Sch_Date,Shift 
              FROM ProductionGreentireFaceOfireTable where Sch_Date > ?
              group by Sch_Date,Shift  order by Sch_Date asc,Shift asc",
          [$date_sch]
        );
        $dateold = $getdate[0]["Sch_Date"];
        $shiftold = $getdate[0]["Shift"];
        $datenext = $getdateNext[0]["Sch_Date"];
        $shifnext = $getdateNext[0]["Shift"];
      } else {

        if ($datacheckCunt == 1) {
          if ($shift == 1) {
            $getdate = $this->sql->rows(
              $this->conn->dbConnect(),
              "SELECT TOP 1 Sch_Date,Shift 
              FROM ProductionGreentireFaceOfireTable where Sch_Date < ?
              group by Sch_Date,Shift  order by Sch_Date desc,Shift desc",
              [$date_sch]
            );
            if ($shifcheck == 1) {
              $getdateNext = $this->sql->rows(
                $this->conn->dbConnect(),
                "SELECT TOP 1 Sch_Date,Shift 
                      FROM ProductionGreentireFaceOfireTable where Sch_Date > ?
                      group by Sch_Date,Shift  order by Sch_Date asc,Shift asc",
                [$date_sch]
              );
            } else {
              $getdateNext = $this->sql->rows(
                $this->conn->dbConnect(),
                "SELECT TOP 1 Sch_Date,Shift 
                      FROM ProductionGreentireFaceOfireTable where Sch_Date >= ?
                      group by Sch_Date,Shift  order by Sch_Date asc,Shift asc",
                [$date_sch]
              );
            }
            $dateold = $getdate[0]["Sch_Date"];
            $shiftold = $getdate[0]["Shift"];
            $datenext = $getdateNext[0]["Sch_Date"];
            $shifnext = $getdateNext[0]["Shift"];
          } else {
            if ($shifcheck == 1) {
              $getdate = $this->sql->rows(
                $this->conn->dbConnect(),
                "SELECT TOP 1 Sch_Date,Shift 
                      FROM ProductionGreentireFaceOfireTable where Sch_Date <= ?
                      group by Sch_Date,Shift  order by Sch_Date desc,Shift desc",
                [$date_sch]
              );
              $dateold = $getdate[0]["Sch_Date"];
              $shiftold = $getdate[0]["Shift"];
            } else {
              $getdate = $this->sql->rows(
                $this->conn->dbConnect(),
                "SELECT TOP 2 Sch_Date,Shift 
                      FROM ProductionGreentireFaceOfireTable where Sch_Date <= ?
                      group by Sch_Date,Shift  order by Sch_Date desc,Shift desc",
                [$date_sch]
              );
              $dateold = $getdate[1]["Sch_Date"];
              $shiftold = $getdate[1]["Shift"];
            }
            $getdateNext = $this->sql->rows(
              $this->conn->dbConnect(),
              "SELECT TOP 1 Sch_Date,Shift 
                          FROM ProductionGreentireFaceOfireTable where Sch_Date > ?
                          group by Sch_Date,Shift  order by Sch_Date asc,Shift asc",
              [$date_sch]
            );
            $dateold = $dateold;
            $shiftold = $shiftold;
            $datenext = $getdateNext[0]["Sch_Date"];
            $shifnext = $getdateNext[0]["Shift"];
          }
        } else {
          if ($shift == 1) {
            $getdate = $this->sql->rows(
              $this->conn->dbConnect(),
              "SELECT TOP 3 Sch_Date,Shift 
                FROM ProductionGreentireFaceOfireTable where Sch_Date <= ?
                group by Sch_Date,Shift  order by Sch_Date desc,Shift desc
                ",
              [
                $date_sch

              ]
            );

            $getdateNext = $this->sql->rows(
              $this->conn->dbConnect(),
              "SELECT TOP 1 Sch_Date,Shift 
                FROM ProductionGreentireFaceOfireTable where Sch_Date <= ?
                group by Sch_Date,Shift  order by Sch_Date desc,Shift desc
                ",
              [
                $date_sch

              ]
            );
            $dateold = $getdate[2]["Sch_Date"];
            $shiftold = $getdate[2]["Shift"];
            $datenext = $getdateNext[0]["Sch_Date"];
            $shifnext = $getdateNext[0]["Shift"];
          } else {
            $getdate = $this->sql->rows(
              $this->conn->dbConnect(),
              "SELECT TOP 3 Sch_Date,Shift 
                FROM ProductionGreentireFaceOfireTable where Sch_Date <= ?
                group by Sch_Date,Shift  order by Sch_Date desc,Shift desc
                ",
              [
                $date_sch

              ]
            );
            $getdateNext = $this->sql->rows(
              $this->conn->dbConnect(),
              "SELECT TOP 1 Sch_Date,Shift 
                FROM ProductionGreentireFaceOfireTable where Sch_Date > ?
                group by Sch_Date,Shift  order by Sch_Date asc,Shift asc
                ",
              [
                $date_sch

              ]
            );
            $dateold = $getdate[1]["Sch_Date"];
            $shiftold = $getdate[1]["Shift"];
            $datenext = $getdateNext[0]["Sch_Date"];
            $shifnext = $getdateNext[0]["Shift"];
          }
        }
      }
      $query = $this->sql->rows(
        $this->conn->dbConnect(),
        "SELECT
        T.Id,
        T.ItemId,
  	    T.ItemGTName,
  	    T.Color ,
  	    --T.Stock2,
        (T.Stock2+ T.TotalProduct2) - (T.TotalPayOfCar2 + ISNULL(T.TireNotSpec2,0)) AS Stock2,
  	    T.Stock,
  	    T.CalStock,
  	    T.TotalProduct,
  	    T.TotalPayOfCar,
  	    T.TireNotSpec,
  	    --(T.Stock + T.TotalProduct) - (T.TotalPayOfCar + ISNULL(T.TireNotSpec,0))
       -- AS Total,
        (T.Stock+ T.TotalProduct) - (T.TotalPayOfCar + ISNULL(T.TireNotSpec,0))
        AS Total,
  	    T.StockTire,
  	    ISNULL(T.StockTire,0) - ((T.Stock + T.TotalProduct) -
        (T.TotalPayOfCar + ISNULL(T.TireNotSpec,0))) AS CompareNum,
  	    CASE
  		    WHEN (T.Stock + T.TotalProduct)- ISNULL(TireNotSpec,0) < TotalPayOfCar
          THEN 'X'
          ELSE '-'
        END AS CheckCountOut,
        T.TotalPlanCreate,
        CASE
          WHEN T.TotalPayOfCar = T.TotalPlanCreate THEN 'TRUE'
          ELSE 'FALSE'
        END AS CompareBill,
        T.CountCar1,
        T.CountCar2,
        T.CountCar3,
        T.CountCar4,
        T.CountCar5,
        T.CountCar6,
        T.CountCar7,
        T.CountCar8,
        T.NumberCar1,
        T.NumberCar2,
        T.NumberCar3,
        T.NumberCar4,
        T.NumberCar5,
        T.NumberCar6,
        T.NumberCar7,
        T.NumberCar8,
        T.PayOfCar,
        T.PayOfCar2,
        T.PayOfCar3,
        T.PayOfCar4,
        T.PayOfCar5,
        T.PayOfCar6,
        T.PayOfCar7,
        T.PayOfCar8
        FROM(
          SELECT
            I.ITEMNAME AS ItemGTName,
            C.DSG_COLOR AS Color,
  		      ISNULL(P2.Stock,0) AS Stock2,
  		      (ISNULL(P.NumberCar1,0) + ISNULL(P.NumberCar2,0) +ISNULL(P.NumberCar3,0)
            + ISNULL(P.NumberCar4,0)
  		      + ISNULL(P.NumberCar5,0) + ISNULL(P.NumberCar6,0) + ISNULL(P.NumberCar7,0)
            + ISNULL(P.NumberCar8,0) ) AS TotalProduct,
            (ISNULL(P2.NumberCar1,0) + ISNULL(P2.NumberCar2,0) +ISNULL(P2.NumberCar3,0)
            + ISNULL(P2.NumberCar4,0)
  		      + ISNULL(P2.NumberCar5,0) + ISNULL(P2.NumberCar6,0) + ISNULL(P2.NumberCar7,0)
            + ISNULL(P2.NumberCar8,0) ) AS TotalProduct2,

  		      (ISNULL(P.PayOfCar,0) + ISNULL(P.PayOfCar2,0) + ISNULL(P.PayOfCar3,0)
            + ISNULL(P.PayOfCar4,0)
  		      +ISNULL(P.PayOfCar5,0) + ISNULL(P.PayOfCar6,0)+ ISNULL(P.PayOfCar7,0)
            + ISNULL(P.PayOfCar8,0)) AS TotalPayOfCar,

            (ISNULL(P2.PayOfCar,0) + ISNULL(P2.PayOfCar2,0) + ISNULL(P2.PayOfCar3,0)
            + ISNULL(P2.PayOfCar4,0)
  		      +ISNULL(P2.PayOfCar5,0) + ISNULL(P2.PayOfCar6,0)+ ISNULL(P2.PayOfCar7,0)
            + ISNULL(P2.PayOfCar8,0)) AS TotalPayOfCar2,
  		      (ISNULL(D.Car1_1,0) + ISNULL(D.Car1_2,0) + ISNULL(D.Car1_3,0)
            + ISNULL(D.Car1_4,0) +ISNULL(D.Car1_5,0) + ISNULL(D.Car1_6,0)+
             ISNULL(D.Car1_7,0) + ISNULL(D.Car1_8,0)) AS TotalPlanCreate,
             ISNULL(P2.TireNotSpec,0) AS TireNotSpec2,
            p.*
  		      FROM ProductionGreentireFaceOfireTable P
  		        -- LEFT JOIN (
              --   SELECT
  			      --     PP.ItemGTName,
              --     PP.ItemGT,
              --     PP.ColorAll As Color
              --     FROM ProductionSchGreentireMaster PP
  				    --     GROUP BY PP.ItemGTName,PP.ItemGT, PP.ColorAll
              -- )I  ON I.ItemGT = P.ItemId
            LEFT JOIN [FREY\LIVE].[DSL_AX40_SP1_LIVE].[dbo].[INVENTTABLE] I ON I.ITEMID = P.ItemId
  			    LEFT JOIN [FREY\LIVE].[DSL_AX40_SP1_LIVE].[dbo].[DSG_ColorSizeTypeTires] C ON C.ITEMID = P.ItemId
            LEFT JOIN ProductionGreentireFaceOfireTable P2 ON P2.ItemId = P.ItemId
             AND P2.Sch_Date = ? AND P2.Shift = ?
             LEFT JOIN ProductionGreentireDisburseTable D ON D.ItemId = P.ItemId
             AND D.Sch_date = P.Sch_DateIn AND D.Shift = P.ShiftIn
            WHERE P.Sch_Date = ? AND P.Shift = ?
        )T ORDER BY T.ItemId ASC",
        [
          $dateold,
          $shiftold,
          $date_sch,
          $shift
        ]
      );
      return $query;
    } catch (\Exception $e) {
      return response(false, $e->getMessage());
    }
  }

  public function checkgridschfacetire($date_sch, $shift)
  {
    $date_sch = date('Y-m-d', strtotime($date_sch));
    return \sqlsrv_has_rows(\sqlsrv_query(
      $this->conn->dbConnect(),
      "SELECT *
      FROM ProductionGreentireFaceOfireTable
      WHERE Sch_Date = ? AND Shift = ?",
      [
        $date_sch,
        $shift
      ]
    ));
  }
  public function additemfacetire($itemid, $sch_date, $shift, $id)
  {
    $date = date("Y-m-d H:i:s");
    $q = sqlsrv_has_rows(sqlsrv_query(
      $this->conn->dbConnect(),
      "SELECT ItemId
        FROM ProductionGreentireFaceOfireTable
        WHERE SCH_Date = ? AND Shift = ? AND ItemId = ?",
      [
        $sch_date,
        $shift,
        $itemid
      ]
    ));
    if ($q === true) {
      return false;
    }
    $update = \sqlsrv_query(
      $this->conn->dbConnect(),
      "UPDATE ProductionGreentireFaceOfireTable
      SET ItemId = ?,
        UpdateBy = ?,
        UpdateDate =?
        WHERE Id = ?",
      [
        $itemid,
        $_SESSION["user_login"],
        $date,
        $id
      ]
    );
    if ($update) {
      return true;
    } else {
      return false;
    }
  }
  public function deleterowfacetire($id)
  {

    $delete = sqlsrv_query(
      $this->conn->dbConnect(),
      "DELETE ProductionGreentireFaceOfireTable
      WHERE Id = ?",
      [
        $id
      ]
    );

    if ($delete) {
      return true;
    } else {
      return false;
    }
  }
  public function UpdateSchFacetireTable($TireNotSpac, $StockTire, $date_sch, $shift, $id, $CalStock)
  {

    $date = date("Y-m-d H:i:s");
    // if ($shift == 1) {
    //   $dateref = date('Y-m-d', strtotime($date_sch . ' -1 days'));
    //   $shiftref = 2;
    // } else {
    //   $dateref = $date_sch;
    //   $shiftref = 1;
    // }
    $getcheckdate = $this->sql->rows(
      $this->conn->dbConnect(),
      "SELECT  Sch_Date,Shift , COUNT(Sch_Date) OVER(PARTITION BY Sch_Date) AS CountRow
      FROM ProductionGreentireFaceOfireTable where Sch_Date = ?
      group by Sch_Date,Shift  order by Sch_Date desc,Shift desc
      ",
      [
        $date_sch
      ]
    );
    $datacheckCunt = $getcheckdate[0]["CountRow"];
    // $datacheck123 = $getcheckdate[0]["CountRow"];


    if ($datacheckCunt == "" || $datacheckCunt == NULL) {

      $getdate = $this->sql->rows(
        $this->conn->dbConnect(),
        "SELECT TOP 1 Sch_Date,Shift 
            FROM ProductionGreentireFaceOfireTable where Sch_Date < ?
            group by Sch_Date,Shift  order by Sch_Date desc,Shift desc",
        [$date_sch]
      );
      $getdateNext = $this->sql->rows(
        $this->conn->dbConnect(),
        "SELECT TOP 1 Sch_Date,Shift 
            FROM ProductionGreentireFaceOfireTable where Sch_Date > ?
            group by Sch_Date,Shift  order by Sch_Date asc,Shift asc",
        [$date_sch]
      );
      $dateold = $getdate[0]["Sch_Date"];
      $shiftold = $getdate[0]["Shift"];
      $datenext = $getdateNext[0]["Sch_Date"];
      $shifnext = $getdateNext[0]["Shift"];
    } else {

      if ($datacheckCunt == 1) {
        if ($shift == 1) {
          $getdate = $this->sql->rows(
            $this->conn->dbConnect(),
            "SELECT TOP 1 Sch_Date,Shift 
            FROM ProductionGreentireFaceOfireTable where Sch_Date < ?
            group by Sch_Date,Shift  order by Sch_Date desc,Shift desc",
            [$date_sch]
          );
          if ($shifcheck == 1) {
            $getdateNext = $this->sql->rows(
              $this->conn->dbConnect(),
              "SELECT TOP 1 Sch_Date,Shift 
                    FROM ProductionGreentireFaceOfireTable where Sch_Date > ?
                    group by Sch_Date,Shift  order by Sch_Date asc,Shift asc",
              [$date_sch]
            );
          } else {
            $getdateNext = $this->sql->rows(
              $this->conn->dbConnect(),
              "SELECT TOP 1 Sch_Date,Shift 
                    FROM ProductionGreentireFaceOfireTable where Sch_Date >= ?
                    group by Sch_Date,Shift  order by Sch_Date asc,Shift asc",
              [$date_sch]
            );
          }
          $dateold = $getdate[0]["Sch_Date"];
          $shiftold = $getdate[0]["Shift"];
          $datenext = $getdateNext[0]["Sch_Date"];
          $shifnext = $getdateNext[0]["Shift"];
        } else {
          if ($shifcheck == 1) {
            $getdate = $this->sql->rows(
              $this->conn->dbConnect(),
              "SELECT TOP 1 Sch_Date,Shift 
                    FROM ProductionGreentireFaceOfireTable where Sch_Date <= ?
                    group by Sch_Date,Shift  order by Sch_Date desc,Shift desc",
              [$date_sch]
            );
            $dateold = $getdate[0]["Sch_Date"];
            $shiftold = $getdate[0]["Shift"];
          } else {
            $getdate = $this->sql->rows(
              $this->conn->dbConnect(),
              "SELECT TOP 2 Sch_Date,Shift 
                    FROM ProductionGreentireFaceOfireTable where Sch_Date <= ?
                    group by Sch_Date,Shift  order by Sch_Date desc,Shift desc",
              [$date_sch]
            );
            $dateold = $getdate[1]["Sch_Date"];
            $shiftold = $getdate[1]["Shift"];
          }
          $getdateNext = $this->sql->rows(
            $this->conn->dbConnect(),
            "SELECT TOP 1 Sch_Date,Shift 
                        FROM ProductionGreentireFaceOfireTable where Sch_Date > ?
                        group by Sch_Date,Shift  order by Sch_Date asc,Shift asc",
            [$date_sch]
          );
          $dateold = $dateold;
          $shiftold = $shiftold;
          $datenext = $getdateNext[0]["Sch_Date"];
          $shifnext = $getdateNext[0]["Shift"];
        }
      } else {
        if ($shift == 1) {
          $getdate = $this->sql->rows(
            $this->conn->dbConnect(),
            "SELECT TOP 3 Sch_Date,Shift 
              FROM ProductionGreentireFaceOfireTable where Sch_Date <= ?
              group by Sch_Date,Shift  order by Sch_Date desc,Shift desc
              ",
            [
              $date_sch

            ]
          );

          $getdateNext = $this->sql->rows(
            $this->conn->dbConnect(),
            "SELECT TOP 1 Sch_Date,Shift 
              FROM ProductionGreentireFaceOfireTable where Sch_Date <= ?
              group by Sch_Date,Shift  order by Sch_Date desc,Shift desc
              ",
            [
              $date_sch

            ]
          );
          $dateold = $getdate[2]["Sch_Date"];
          $shiftold = $getdate[2]["Shift"];
          $datenext = $getdateNext[0]["Sch_Date"];
          $shifnext = $getdateNext[0]["Shift"];
        } else {
          $getdate = $this->sql->rows(
            $this->conn->dbConnect(),
            "SELECT TOP 3 Sch_Date,Shift 
              FROM ProductionGreentireFaceOfireTable where Sch_Date <= ?
              group by Sch_Date,Shift  order by Sch_Date desc,Shift desc
              ",
            [
              $date_sch

            ]
          );
          $getdateNext = $this->sql->rows(
            $this->conn->dbConnect(),
            "SELECT TOP 1 Sch_Date,Shift 
              FROM ProductionGreentireFaceOfireTable where Sch_Date > ?
              group by Sch_Date,Shift  order by Sch_Date asc,Shift asc
              ",
            [
              $date_sch

            ]
          );
          $dateold = $getdate[1]["Sch_Date"];
          $shiftold = $getdate[1]["Shift"];
          $datenext = $getdateNext[0]["Sch_Date"];
          $shifnext = $getdateNext[0]["Shift"];
        }
      }
    }

    $update = \sqlsrv_query(
      $this->conn->dbConnect(),
      "UPDATE ProductionGreentireFaceOfireTable
      SET TireNotSpec = ?,
      StockTire = ?,
      CalStock = ?,
      UpdateBy = ?,
      UpdateDate = ?
      WHERE Id = ?",
      [
        $TireNotSpac,
        $StockTire,
        $CalStock,
        $_SESSION["user_login"],
        $date,
        $id
      ]
    );

    $getdata = $this->sql->rows(
      $this->conn->dbConnect(),
      "SELECT
    --  ((T.Stock2 + T.TotalProduct) - (T.TotalPayOfCar + ISNULL(T.TireNotSpec,0))) + T.CalStock
    --     AS Stock
    (T.Stock2+ T.TotalProduct) - (T.TotalPayOfCar + ISNULL(T.TireNotSpec,0))+ T.CalStock AS Stock
      --  T.Stock2 + T.CalStock AS Stock
        FROM
        (SELECT
	        ISNULL(P2.Stock,0) AS Stock2,
	        (ISNULL(P2.NumberCar1,0) + ISNULL(P2.NumberCar2,0) +ISNULL(P2.NumberCar3,0)
          + ISNULL(P2.NumberCar4,0)
  		    + ISNULL(P2.NumberCar5,0) + ISNULL(P2.NumberCar6,0) + ISNULL(P2.NumberCar7,0)
          + ISNULL(P2.NumberCar8,0) ) AS TotalProduct,
          (ISNULL(P2.PayOfCar,0) + ISNULL(P2.PayOfCar2,0) + ISNULL(P2.PayOfCar3,0)
          + ISNULL(P2.PayOfCar4,0)
  		    +ISNULL(P2.PayOfCar5,0) + ISNULL(P2.PayOfCar6,0)+ ISNULL(P2.PayOfCar7,0)
          + ISNULL(P2.PayOfCar8,0)) AS TotalPayOfCar,
          ISNULL(P2.TireNotSpec,0) AS TireNotSpec,
          ISNULL(P.CalStock,0) AS CalStock
          FROM ProductionGreentireFaceOfireTable P
          LEFT JOIN ProductionGreentireFaceOfireTable P2 ON P2.ItemId = P.ItemId
          AND P2.Sch_Date = ? AND P2.Shift = ?
	        WHERE P.Id = ?)T",
      [
        $dateold,
        $shiftold,
        $id
      ]
    );

    $updateStockInplan = \sqlsrv_query(
      $this->conn->dbConnect(),
      "UPDATE ProductionGreentireFaceOfireTable
       SET Stock = ?
       WHERE Id = ?",
      [
        $getdata[0]["Stock"],
        $id
      ]
    );

    if ($update) {
      return true;
    } else {
      return false;
    }
  }
  public function UpdateSchFacetireTableCar(
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
  ) {

    $date = date("Y-m-d H:i:s");
    // if ($shift == 1) {
    //   $dateref = date('Y-m-d', strtotime($date_sch . ' -1 days'));
    //   $shiftref = 2;
    // } else {
    //   $dateref = $date_sch;
    //   $shiftref = 1;
    // }
    $getcheckdate = $this->sql->rows(
      $this->conn->dbConnect(),
      "SELECT  Sch_Date,Shift , COUNT(Sch_Date) OVER(PARTITION BY Sch_Date) AS CountRow
      FROM ProductionGreentireFaceOfireTable where Sch_Date = ?
      group by Sch_Date,Shift  order by Sch_Date desc,Shift desc
      ",
      [
        $date_sch
      ]
    );
    $datacheckCunt = $getcheckdate[0]["CountRow"];
    // $datacheck123 = $getcheckdate[0]["CountRow"];


    if ($datacheckCunt == "" || $datacheckCunt == NULL) {

      $getdate = $this->sql->rows(
        $this->conn->dbConnect(),
        "SELECT TOP 1 Sch_Date,Shift 
            FROM ProductionGreentireFaceOfireTable where Sch_Date < ?
            group by Sch_Date,Shift  order by Sch_Date desc,Shift desc",
        [$date_sch]
      );
      $getdateNext = $this->sql->rows(
        $this->conn->dbConnect(),
        "SELECT TOP 1 Sch_Date,Shift 
            FROM ProductionGreentireFaceOfireTable where Sch_Date > ?
            group by Sch_Date,Shift  order by Sch_Date asc,Shift asc",
        [$date_sch]
      );
      $dateold = $getdate[0]["Sch_Date"];
      $shiftold = $getdate[0]["Shift"];
      $datenext = $getdateNext[0]["Sch_Date"];
      $shifnext = $getdateNext[0]["Shift"];
    } else {

      if ($datacheckCunt == 1) {
        if ($shift == 1) {
          $getdate = $this->sql->rows(
            $this->conn->dbConnect(),
            "SELECT TOP 1 Sch_Date,Shift 
            FROM ProductionGreentireFaceOfireTable where Sch_Date < ?
            group by Sch_Date,Shift  order by Sch_Date desc,Shift desc",
            [$date_sch]
          );
          if ($shifcheck == 1) {
            $getdateNext = $this->sql->rows(
              $this->conn->dbConnect(),
              "SELECT TOP 1 Sch_Date,Shift 
                    FROM ProductionGreentireFaceOfireTable where Sch_Date > ?
                    group by Sch_Date,Shift  order by Sch_Date asc,Shift asc",
              [$date_sch]
            );
          } else {
            $getdateNext = $this->sql->rows(
              $this->conn->dbConnect(),
              "SELECT TOP 1 Sch_Date,Shift 
                    FROM ProductionGreentireFaceOfireTable where Sch_Date >= ?
                    group by Sch_Date,Shift  order by Sch_Date asc,Shift asc",
              [$date_sch]
            );
          }
          $dateold = $getdate[0]["Sch_Date"];
          $shiftold = $getdate[0]["Shift"];
          $datenext = $getdateNext[0]["Sch_Date"];
          $shifnext = $getdateNext[0]["Shift"];
        } else {
          if ($shifcheck == 1) {
            $getdate = $this->sql->rows(
              $this->conn->dbConnect(),
              "SELECT TOP 1 Sch_Date,Shift 
                    FROM ProductionGreentireFaceOfireTable where Sch_Date <= ?
                    group by Sch_Date,Shift  order by Sch_Date desc,Shift desc",
              [$date_sch]
            );
            $dateold = $getdate[0]["Sch_Date"];
            $shiftold = $getdate[0]["Shift"];
          } else {
            $getdate = $this->sql->rows(
              $this->conn->dbConnect(),
              "SELECT TOP 2 Sch_Date,Shift 
                    FROM ProductionGreentireFaceOfireTable where Sch_Date <= ?
                    group by Sch_Date,Shift  order by Sch_Date desc,Shift desc",
              [$date_sch]
            );
            $dateold = $getdate[1]["Sch_Date"];
            $shiftold = $getdate[1]["Shift"];
          }
          $getdateNext = $this->sql->rows(
            $this->conn->dbConnect(),
            "SELECT TOP 1 Sch_Date,Shift 
                        FROM ProductionGreentireFaceOfireTable where Sch_Date > ?
                        group by Sch_Date,Shift  order by Sch_Date asc,Shift asc",
            [$date_sch]
          );
          $dateold = $dateold;
          $shiftold = $shiftold;
          $datenext = $getdateNext[0]["Sch_Date"];
          $shifnext = $getdateNext[0]["Shift"];
        }
      } else {
        if ($shift == 1) {
          $getdate = $this->sql->rows(
            $this->conn->dbConnect(),
            "SELECT TOP 3 Sch_Date,Shift 
              FROM ProductionGreentireFaceOfireTable where Sch_Date <= ?
              group by Sch_Date,Shift  order by Sch_Date desc,Shift desc
              ",
            [
              $date_sch

            ]
          );

          $getdateNext = $this->sql->rows(
            $this->conn->dbConnect(),
            "SELECT TOP 1 Sch_Date,Shift 
              FROM ProductionGreentireFaceOfireTable where Sch_Date <= ?
              group by Sch_Date,Shift  order by Sch_Date desc,Shift desc
              ",
            [
              $date_sch

            ]
          );
          $dateold = $getdate[2]["Sch_Date"];
          $shiftold = $getdate[2]["Shift"];
          $datenext = $getdateNext[0]["Sch_Date"];
          $shifnext = $getdateNext[0]["Shift"];
        } else {
          $getdate = $this->sql->rows(
            $this->conn->dbConnect(),
            "SELECT TOP 3 Sch_Date,Shift 
              FROM ProductionGreentireFaceOfireTable where Sch_Date <= ?
              group by Sch_Date,Shift  order by Sch_Date desc,Shift desc
              ",
            [
              $date_sch

            ]
          );
          $getdateNext = $this->sql->rows(
            $this->conn->dbConnect(),
            "SELECT TOP 1 Sch_Date,Shift 
              FROM ProductionGreentireFaceOfireTable where Sch_Date > ?
              group by Sch_Date,Shift  order by Sch_Date asc,Shift asc
              ",
            [
              $date_sch

            ]
          );
          $dateold = $getdate[1]["Sch_Date"];
          $shiftold = $getdate[1]["Shift"];
          $datenext = $getdateNext[0]["Sch_Date"];
          $shifnext = $getdateNext[0]["Shift"];
        }
      }
    }


    $date = date("Y-m-d H:i:s");
    $update = \sqlsrv_query(
      $this->conn->dbConnect(),
      "UPDATE ProductionGreentireFaceOfireTable
          SET CountCar1 = ?,
              CountCar2 = ?,
              CountCar3 = ?,
              CountCar4 = ?,
              CountCar5 = ?,
              CountCar6 = ?,
              CountCar7 = ?,
              CountCar8 = ?,
              NumberCar1 = ?,
              NumberCar2 = ?,
              NumberCar3 = ?,
              NumberCar4 = ?,
              NumberCar5 = ?,
              NumberCar6 = ?,
              NumberCar7 = ?,
              NumberCar8 = ?,
              PayOfCar = ?,
              PayOfCar2 = ?,
              PayOfCar3 = ?,
              PayOfCar4 = ?,
              PayOfCar5 = ?,
              PayOfCar6 = ?,
              PayOfCar7 = ?,
              PayOfCar8 = ?,
              UpdateBy = ?,
              UpdateDate =?
          WHERE Id = ?",
      [
        $Car1_1, $Car1_2, $Car1_3, $Car1_4,
        $Car1_5, $Car1_6, $Car1_7, $Car1_8,
        $CarNumber1_1, $CarNumber1_2, $CarNumber1_3, $CarNumber1_4,
        $CarNumber1_5, $CarNumber1_6, $CarNumber1_7, $CarNumber1_8,
        $Pay2_1, $Pay2_2, $Pay2_3, $Pay2_4,
        $Pay2_5, $Pay2_6, $Pay2_7, $Pay2_8,
        $_SESSION["user_login"],
        $date,
        $id
      ]
    );

    $getdata = $this->sql->rows(
      $this->conn->dbConnect(),
      "SELECT
          -- ((T.Stock2 + T.TotalProduct) - (T.TotalPayOfCar + ISNULL(T.TireNotSpec,0))) + T.CalStock
          --   AS Stock
         (T.Stock2+ T.TotalProduct) - (T.TotalPayOfCar + ISNULL(T.TireNotSpec,0))+ T.CalStock AS Stock
         -- T.Stock2 + T.CalStock AS Stock
            FROM
            (SELECT
    	        ISNULL(P2.Stock,0) AS Stock2,
    	        (ISNULL(P2.NumberCar1,0) + ISNULL(P2.NumberCar2,0) +ISNULL(P2.NumberCar3,0)
              + ISNULL(P2.NumberCar4,0)
      		    + ISNULL(P2.NumberCar5,0) + ISNULL(P2.NumberCar6,0) + ISNULL(P2.NumberCar7,0)
              + ISNULL(P2.NumberCar8,0) ) AS TotalProduct,
              (ISNULL(P2.PayOfCar,0) + ISNULL(P2.PayOfCar2,0) + ISNULL(P2.PayOfCar3,0)
              + ISNULL(P2.PayOfCar4,0)
      		    +ISNULL(P2.PayOfCar5,0) + ISNULL(P2.PayOfCar6,0)+ ISNULL(P2.PayOfCar7,0)
              + ISNULL(P2.PayOfCar8,0)) AS TotalPayOfCar,
              ISNULL(P2.TireNotSpec,0) AS TireNotSpec,
              ISNULL(P.CalStock,0) AS CalStock
              FROM ProductionGreentireFaceOfireTable P
              LEFT JOIN ProductionGreentireFaceOfireTable P2 ON P2.ItemId = P.ItemId
              AND P2.Sch_Date = ? AND P2.Shift = ?
    	        WHERE P.Id = ?)T",
      [
        $dateold,
        $shiftold,
        $id
      ]
    );

    $updateStockInplan = \sqlsrv_query(
      $this->conn->dbConnect(),
      "UPDATE ProductionGreentireFaceOfireTable
           SET Stock = ?
           WHERE Id = ?",
      [
        $getdata[0]["Stock"],
        $id
      ]
    );
    //self::UpdateSchFacetireTable($x["ITEM_GT"], $tempInsert)

    if ($update) {
      return true;
    } else {
      return false;
    }
  }
  public function additemDisburs($itemid, $sch_date, $shift, $id)
  {
    $date = date("Y-m-d H:i:s");
    $q = sqlsrv_has_rows(sqlsrv_query(
      $this->conn->dbConnect(),
      "SELECT ItemId
                FROM ProductionGreentireDisburseTable
                WHERE SCH_Date = ? AND Shift = ? AND ItemId = ?",
      [
        $sch_date,
        $shift,
        $itemid
      ]
    ));
    if ($q === true) {
      return false;
    }
    $update = \sqlsrv_query(
      $this->conn->dbConnect(),
      "UPDATE ProductionGreentireDisburseTable
              SET ItemId = ?,
                UpdateBy = ?,
                UpdateDate =?
                WHERE Id = ?",
      [
        $itemid,
        $_SESSION["user_login"],
        $date,
        $id
      ]
    );
    if ($update) {
      return true;
    } else {
      return false;
    }
  }
  public function checkgridschplantire($date_sch, $shift)
  {
    $date_sch = date('Y-m-d', strtotime($date_sch));
    return \sqlsrv_has_rows(\sqlsrv_query(
      $this->conn->dbConnect(),
      "SELECT
                ItemId
              FROM ProductionGreentireDisburseTable
              WHERE Sch_date = ? AND Shift = ?
              UNION ALL
              SELECT
                ItemId
              FROM ProductionGreentireFaceOfireTable
              WHERE Sch_Date = ? AND Shift = ?",
      [
        $date_sch,
        $shift,
        $date_sch,
        $shift
      ]
    ));
  }

  public function loadplantire($date_sch, $shift)
  {
    try {
      $sqlId = "1=1";
      if ($id !== null) {
        $sqlId = "P.Id = $id";
      }
      if ($shift == 1) {
        $dateref = date('Y-m-d', strtotime($date_sch . ' -1 days'));
        $shiftref = 2;
      } else {
        $dateref = $date_sch;
        $shiftref = 1;
      }
      $getdate = $this->sql->rows(
        $this->conn->dbConnect(),
        "SELECT * FROM(
          SELECT TOP 2
            DateBuild
          FROM BuildSch
          WHERE DateBuild > CONVERT(date, ?) GROUP BY DateBuild ORDER BY DateBuild ASC
          UNION ALL
          SELECT TOP 2
            DateBuild
          FROM BuildSch
          WHERE DateBuild > CONVERT(date, ?) GROUP BY DateBuild ORDER BY DateBuild ASC)T
          GROUP BY DateBuild",
        [
          $date_sch,
          $date_sch
        ]
      );
      $date2 = $getdate[0]["DateBuild"];
      $date3 = $getdate[1]["DateBuild"];
      $query = $this->sql->rows(
        $this->conn->dbConnect(),
        "SELECT*
        FROM(  SELECT DB.*
      ,ROUND(ISNULL(NULLIF(DB.OrderLackshift,0) / NULLIF(DB.check2,0),0),2) AS check3,
        CASE
          WHEN DB.OrderLackshift <= 0 OR DB.check2 = 0
          THEN CASE
				  WHEN DB.check1 < 20 THEN 1.00
				  WHEN DB.check1 < 50 THEN 0.95
				  WHEN DB.check1 < 75 THEN 1.90
				  WHEN DB.check1 < 100 THEN 0.32
				  ELSE  0.20 END
		    ELSE CASE
				  WHEN ROUND(DB.OrderLackshift / DB.check2,2) = 1 THEN 1
				  WHEN ROUND(DB.OrderLackshift / DB.check2,2) = 2 THEN 3
          WHEN ROUND(DB.OrderLackshift / DB.check2,2) = 3 THEN 4
          WHEN ROUND(DB.OrderLackshift / DB.check2,2) = 4 THEN 5
          WHEN ROUND(DB.OrderLackshift / DB.check2,2) = 0.48 THEN 2
          WHEN ROUND(DB.OrderLackshift / DB.check2,2) = 0.95 THEN 6
          WHEN ROUND(DB.OrderLackshift / DB.check2,2) = 1.43 THEN 7
          WHEN ROUND(DB.OrderLackshift / DB.check2,2) = 1.9 THEN 8
          WHEN ROUND(DB.OrderLackshift / DB.check2,2) = 0.32 THEN 9
          WHEN ROUND(DB.OrderLackshift / DB.check2,2) = 0.65 THEN 10
          WHEN ROUND(DB.OrderLackshift / DB.check2,2) = 0.97 THEN 11
          WHEN ROUND(DB.OrderLackshift / DB.check2,2) = 1.29 THEN 12
          WHEN ROUND(DB.OrderLackshift / DB.check2,2) = 0.24 THEN 13
          WHEN ROUND(DB.OrderLackshift / DB.check2,2) = 0.49 THEN 14
          WHEN ROUND(DB.OrderLackshift / DB.check2,2) = 0.73 THEN 15
          WHEN ROUND(DB.OrderLackshift / DB.check2,2) = 0.98 THEN 16
          WHEN ROUND(DB.OrderLackshift / DB.check2,2) = 0.2 THEN 17
          WHEN ROUND(DB.OrderLackshift / DB.check2,2) = 0.39 THEN 18
          WHEN ROUND(DB.OrderLackshift / DB.check2,2) = 1.59 THEN 19
				  ELSE 20 END
			  END AS checktotal,
        CASE
			    WHEN  DB.Total = 0 AND DB.GrandTotal =0 AND DB.ActualDay1C =0 AND DB.ActualDay1D = 0 AND DB.ActualDay2C = 0
			    AND  DB.ActualDay2D = 0 AND DB.ActualDay3C = 0 AND DB.ActualDay3D = 0 AND ISNULL(DB.ShiftDay1C,0) = 0  AND ISNULL(DB.ShiftDay1D,0) = 0
			    AND ISNULL(DB.ShiftDay2C,0) = 0 AND ISNULL(DB.ShiftDay2D,0) = 0 AND ISNULL(DB.ShiftDay3C,0) = 0 AND ISNULL(DB.ShiftDay3D,0) = 0
			    THEN 0
			  ELSE 1 END AS checkzero,
      CASE
          WHEN $shift = '1'
        THEN
        CASE WHEN  ISNULL(DB.ShiftDay1D,0) < 0 THEN 1
         ELSE 2 END
        ELSE
        CASE WHEN  ISNULL(DB.ShiftDay2C,0) < 0 THEN 1 
        ELSE  2 END
        END AS ShiftCheck
      FROM (
        SELECT T.*,T2.ITEMNAME_LIST,
          CASE
            WHEN  $shift = '1'
			      THEN CASE
					    WHEN T.Total+(T.ActualDay1C + T.ActualDay1D + T.ActualDay2C + T.ActualDay2D + T.ActualDay3C + T.ActualDay3D ) > 0
					    THEN CASE WHEN T.Total - T.ActualDay1C < 0
							THEN ISNULL((NULLIF(0,0) / (NULLIF(T.ActualDay1D,0)+0.00)),0)*100
							ELSE ISNULL((NULLIF(T.Total - T.ActualDay1C,0) / (NULLIF(T.ActualDay1D,0)+0.00)),0)*100 END
              ELSE 0 END
          ELSE CASE
					  WHEN T.Total+(T.ActualDay1C + T.ActualDay1D + T.ActualDay2C + T.ActualDay2D + T.ActualDay3C + T.ActualDay3D ) > 0
					  THEN CASE WHEN T.Total - T.ActualDay1D < 0
              THEN ISNULL((NULLIF(0,0) / (NULLIF(T.ActualDay2C,0)+0.00)),0)*100
							ELSE ISNULL((NULLIF(T.Total - T.ActualDay1D,0) / (NULLIF(T.ActualDay2C,0)+0.00)),0)*100 END
              ELSE 0 END
          END AS check1,
       
        CASE
          WHEN  $shift = '1'
			    THEN CASE
					  WHEN T.ActualDay1D > 0
					  THEN 
					  CASE
					    WHEN T.Total+(T.ActualDay1C + T.ActualDay1D + T.ActualDay2C + T.ActualDay2D + T.ActualDay3C + T.ActualDay3D ) > 0
					    THEN CASE WHEN T.Total - T.ActualDay1C < 0
							THEN 
							CASE
						  WHEN ISNULL((NULLIF(0,0) / (NULLIF(T.ActualDay1D,0)+0.00)),0)*100 <= 20
						  THEN 1.00
						  WHEN ISNULL((NULLIF(0,0) / (NULLIF(T.ActualDay1D,0)+0.00)),0)*100 <= 50
						  THEN 2.10
						WHEN ISNULL((NULLIF(0,0) / (NULLIF(T.ActualDay1D,0)+0.00)),0)*100 <= 75
					    THEN 3.10
						  WHEN ISNULL((NULLIF(0,0) / (NULLIF(T.ActualDay1D,0)+0.00)),0)*100 < 100
					    THEN 4.10
              ELSE 5.10 END
							ELSE 
							CASE
						  WHEN ISNULL((NULLIF(T.Total - T.ActualDay1C,0) / (NULLIF(T.ActualDay1D,0)+0.00)),0)*100  <= 20
						  THEN 1.00
						  WHEN ISNULL((NULLIF(T.Total - T.ActualDay1C,0) / (NULLIF(T.ActualDay1D,0)+0.00)),0)*100  <= 50
						  THEN 2.10
						WHEN ISNULL((NULLIF(T.Total - T.ActualDay1C,0) / (NULLIF(T.ActualDay1D,0)+0.00)),0)*100  <= 75
					    THEN 3.10
						  WHEN ISNULL((NULLIF(T.Total - T.ActualDay1C,0) / (NULLIF(T.ActualDay1D,0)+0.00)),0)*100  < 100
					    THEN 4.10
              ELSE 5.10 END
							END
              ELSE 0 END
					    ELSE 0 END
				 ELSE  CASE
				  WHEN T.ActualDay2C > 0
					THEN CASE
					  WHEN T.Total+(T.ActualDay1C + T.ActualDay1D + T.ActualDay2C + T.ActualDay2D + T.ActualDay3C + T.ActualDay3D ) > 0
					  THEN CASE WHEN T.Total - T.ActualDay1D < 0
              THEN 
              CASE
						WHEN ISNULL((NULLIF(0,0) / (NULLIF(T.ActualDay2C,0)+0.00)),0)*100 <= 20
						THEN 1.00
						WHEN ISNULL((NULLIF(0,0) / (NULLIF(T.ActualDay2C,0)+0.00)),0)*100 <= 50
						THEN 2.10
						WHEN ISNULL((NULLIF(0,0) / (NULLIF(T.ActualDay2C,0)+0.00)),0)*100 <= 75
						THEN 3.10
						WHEN ISNULL((NULLIF(0,0) / (NULLIF(T.ActualDay2C,0)+0.00)),0)*100 < 100
						THEN 3.10
						WHEN ISNULL((NULLIF(0,0) / (NULLIF(T.ActualDay2C,0)+0.00)),0)*100 < 100
						THEN 4.10
            ELSE 5.10 END
            ELSE
					   CASE
						WHEN ISNULL((NULLIF(T.Total - T.ActualDay1D,0) / (NULLIF(T.ActualDay2C,0)+0.00)),0)*100 <= 20
						THEN 1.00
						WHEN ISNULL((NULLIF(T.Total - T.ActualDay1D,0) / (NULLIF(T.ActualDay2C,0)+0.00)),0)*100 <= 50
						THEN 2.10
						WHEN ISNULL((NULLIF(T.Total - T.ActualDay1D,0) / (NULLIF(T.ActualDay2C,0)+0.00)),0)*100 <= 75
						THEN 3.10
						WHEN ISNULL((NULLIF(T.Total - T.ActualDay1D,0) / (NULLIF(T.ActualDay2C,0)+0.00)),0)*100 < 100
						THEN 3.10
						WHEN ISNULL((NULLIF(T.Total - T.ActualDay1D,0) / (NULLIF(T.ActualDay2C,0)+0.00)),0)*100 < 100
						THEN 4.10
            ELSE 5.10 END
							END
              ELSE 0 END
					  ELSE 0 END
        END AS check2,
        CASE
          WHEN  $shift = '1'
			    THEN CASE
					WHEN T.Total - T.ActualDay1C > 0
					THEN T.Total - T.ActualDay1C
					ELSE 0 END
        ELSE  CASE
					WHEN T.Total - T.ActualDay1D > 0
					THEN
					T.Total - T.ActualDay1D
					ELSE 0 END
        END AS check4
      FROM(
        SELECT
        D.ItemId,
        QTR.Name,
        ISNULL(PM.OrderLackshift,0) AS OrderLackshift ,
        ((D.Stock2 + D.TotalProduct) - (D.TotalPayOfCar + D.TireNotSpec)) +(D.StockPD + D.TotalPD) - (D.TireNotSpacPD +D.ProducePD) AS Total,  
        (D.StockPD + D.TotalPD) - (D.TireNotSpacPD +D.ProducePD)  AS TotalSystemPD,
        ((D.Stock2 + D.TotalProduct) - (D.TotalPayOfCar + D.TireNotSpec)) AS GrandTotal,
        ISNULL(B1.Actual,0) AS ActualDay1C,
        ISNULL(B2.Actual,0) AS ActualDay1D,
        ISNULL(B3.Actual,0) AS ActualDay2C,
        ISNULL(B4.Actual,0) AS ActualDay2D,
        ISNULL(B5.Actual,0) AS ActualDay3C,
        ISNULL(B6.Actual,0) AS ActualDay3D,
        CASE
          WHEN B1.Actual = 0 OR B1.Actual = NULL
        THEN NULL
        ELSE (((D.Stock2 + D.TotalProduct) - (D.TotalPayOfCar + D.TireNotSpec))+((D.StockPD + D.TotalPD) - (D.TireNotSpacPD +D.ProducePD))) - ISNULL(B1.Actual,0)
        END AS ShiftDay1C,
        CASE
          WHEN B2.Actual = 0 OR B2.Actual = NULL
        THEN NULL
        ELSE (((D.Stock2 + D.TotalProduct) - (D.TotalPayOfCar + D.TireNotSpec))+((D.StockPD + D.TotalPD) - (D.TireNotSpacPD +D.ProducePD))) - (ISNULL(B1.Actual,0) +ISNULL(B2.Actual,0))
        END AS ShiftDay1D,
        CASE
          WHEN B3.Actual = 0 OR B3.Actual = NULL
        THEN NULL
        ELSE (((D.Stock2 + D.TotalProduct) - (D.TotalPayOfCar + D.TireNotSpec))+((D.StockPD + D.TotalPD) - (D.TireNotSpacPD +D.ProducePD))) - (ISNULL(B1.Actual,0) +ISNULL(B2.Actual,0) + ISNULL(B3.Actual,0))
        END AS ShiftDay2C,
        CASE
          WHEN B4.Actual = 0 OR B4.Actual = NULL
        THEN NULL
        ELSE (((D.Stock2 + D.TotalProduct) - (D.TotalPayOfCar + D.TireNotSpec))+((D.StockPD + D.TotalPD) - (D.TireNotSpacPD +D.ProducePD))) - (ISNULL(B1.Actual,0) +ISNULL(B2.Actual,0) + ISNULL(B3.Actual,0) + ISNULL(B4.Actual,0))
        END AS ShiftDay2D,
        (((D.Stock2 + D.TotalProduct) - (D.TotalPayOfCar + D.TireNotSpec))+((D.StockPD + D.TotalPD) - (D.TireNotSpacPD +D.ProducePD))) - (ISNULL(B1.Actual,0) +ISNULL(B2.Actual,0) + ISNULL(B3.Actual,0) + ISNULL(B4.Actual,0) + ISNULL(B5.Actual,0)) AS ShiftDay3C,
        (((D.Stock2 + D.TotalProduct) - (D.TotalPayOfCar + D.TireNotSpec))+((D.StockPD + D.TotalPD) - (D.TireNotSpacPD +D.ProducePD))) - (ISNULL(B1.Actual,0) +ISNULL(B2.Actual,0) + ISNULL(B3.Actual,0) + ISNULL(B4.Actual,0) + ISNULL(B5.Actual,0) + ISNULL(B6.Actual,0)) AS ShiftDay3D,
        
        D.BL ,
        CASE
          WHEN (((D.Stock2 + D.TotalProduct) - (D.TotalPayOfCar + D.TireNotSpec))+((D.StockPD + D.TotalPD) - (D.TireNotSpacPD +D.ProducePD))) - (B1.Actual +B2.Actual) > (B3.Actual + B4.Actual)
        THEN 'มากกว่า 1 วัน'
        ELSE '0'
        END AS StockStatus ,
        CASE
          WHEN $shift = 1
        THEN
        CASE WHEN ((((D.Stock2 + D.TotalProduct) - (D.TotalPayOfCar + D.TireNotSpec)) +(D.StockPD + D.TotalPD) - (D.TireNotSpacPD +D.ProducePD)) - ISNULL(B1.Actual,0)) < 0 
        AND ((((D.Stock2 + D.TotalProduct) - (D.TotalPayOfCar + D.TireNotSpec)) +(D.StockPD + D.TotalPD) - (D.TireNotSpacPD +D.ProducePD)) - (ISNULL(B1.Actual,0)+ISNULL(B2.Actual,0))) < 0 THEN 0 
        ELSE CASE  WHEN (((D.Stock2 + D.TotalProduct) - (D.TotalPayOfCar + D.TireNotSpec)) +(D.StockPD + D.TotalPD) - (D.TireNotSpacPD +D.ProducePD)) - ISNULL(B1.Actual,0) < 0 THEN 1 ELSE 
        CASE  WHEN (((D.Stock2 + D.TotalProduct) - (D.TotalPayOfCar + D.TireNotSpec)) +(D.StockPD + D.TotalPD) - (D.TireNotSpacPD +D.ProducePD)) - (ISNULL(B1.Actual,0)+ISNULL(B2.Actual,0)) < 0 THEN 2 ELSE 3 END
         END
         END
        ELSE
         CASE WHEN ((((D.Stock2 + D.TotalProduct) - (D.TotalPayOfCar + D.TireNotSpec)) +(D.StockPD + D.TotalPD) - (D.TireNotSpacPD +D.ProducePD)) - ISNULL(B2.Actual,0)) < 0
          AND ((((D.Stock2 + D.TotalProduct) - (D.TotalPayOfCar + D.TireNotSpec)) +(D.StockPD + D.TotalPD) - (D.TireNotSpacPD +D.ProducePD)) - (ISNULL(B2.Actual,0)+ISNULL(B3.Actual,0))) < 0 THEN 0 
        ELSE CASE  WHEN (((D.Stock2 + D.TotalProduct) - (D.TotalPayOfCar + D.TireNotSpec)) +(D.StockPD + D.TotalPD) - (D.TireNotSpacPD +D.ProducePD)) - ISNULL(B2.Actual,0) < 0 THEN 1 ELSE 
        CASE  WHEN (((D.Stock2 + D.TotalProduct) - (D.TotalPayOfCar + D.TireNotSpec)) +(D.StockPD + D.TotalPD) - (D.TireNotSpacPD +D.ProducePD)) - (ISNULL(B2.Actual,0)+ISNULL(B3.Actual,0)) < 0 THEN 2 ELSE 3 END
         END
         END
        END AS TEST,
       
        QTR.DSG_COLOR
        FROM(
          SELECT
          T.ItemId,
          ISNULL(P2.Stock,0) AS Stock2,
          (ISNULL(P.NumberCar1,0) + ISNULL(P.NumberCar2,0) +ISNULL(P.NumberCar3,0)+
          ISNULL(P.NumberCar4,0)+ ISNULL(P.NumberCar5,0) + ISNULL(P.NumberCar6,0) +
          ISNULL(P.NumberCar7,0)+ ISNULL(P.NumberCar8,0) ) AS TotalProduct,
          (ISNULL(P.PayOfCar,0) + ISNULL(P.PayOfCar2,0) + ISNULL(P.PayOfCar3,0)+
          ISNULL(P.PayOfCar4,0)+ISNULL(P.PayOfCar5,0) + ISNULL(P.PayOfCar6,0)+
          ISNULL(P.PayOfCar7,0) + ISNULL(P.PayOfCar8,0)) AS TotalPayOfCar,
          ISNULL(P.TireNotSpec,0) AS TireNotSpec,
          ISNULL(PD2.Stock,0) AS StockPD,
          ISNULL(PD.Car1_1,0) + ISNULL(PD.Car1_2,0) + ISNULL(PD.Car1_3,0) + ISNULL(PD.Car1_4,0) +
          ISNULL(PD.Car1_5,0) + ISNULL(PD.Car1_6,0)+ ISNULL(PD.Car1_7,0) + ISNULL(PD.Car1_8,0) AS TotalPD,
          ISNULL(PD.TireNotSpac,0) AS TireNotSpacPD,
          ISNULL(PD.Actual,0) AS ProducePD,
          PD.BL
          FROM(
            SELECT
            X.ItemId
            FROM(
              SELECT
              ItemId
              FROM ProductionGreentireDisburseTable
              WHERE Sch_date = ? AND Shift = ?
              UNION ALL
              SELECT
              ItemId
              FROM ProductionGreentireFaceOfireTable
              WHERE Sch_Date = ? AND Shift = ?)X
              GROUP BY  X.ItemId) T
              LEFT JOIN ProductionGreentireFaceOfireTable P ON P.ItemId = T.ItemId AND P.Sch_date = ? AND P.Shift = ?
              LEFT JOIN ProductionGreentireFaceOfireTable P2 ON P2.ItemId = T.ItemId AND P2.Sch_date = ? AND P2.Shift = ?
              LEFT JOIN ProductionGreentireDisburseTable PD ON PD.ItemId = T.ItemId AND PD.Sch_date = ? AND PD.Shift = ?
              LEFT JOIN ProductionGreentireDisburseTable PD2 ON PD2.ItemId = T.ItemId AND PD2.Sch_date = ? AND PD2.Shift = ?
            )D


            LEFT JOIN(
              SELECT
              IE.ItemExt AS ITEMID,
              SUM(BS.Target) AS Actual
              FROM BuildSch BS
              LEFT JOIN ProductionSchEXTMaster IE ON IE.ItemGT = BS.ItemId
              WHERE BS.DateBuild = '$date_sch' AND BS.Shift = '1' GROUP BY IE.ItemExt
            )B1 ON B1.ITEMID = D.ItemId

            LEFT JOIN(
              SELECT
              IE.ItemExt AS ITEMID,
              SUM(BS.Target) AS Actual
              FROM BuildSch BS
              LEFT JOIN ProductionSchEXTMaster IE ON IE.ItemGT = BS.ItemId
              WHERE BS.DateBuild = '$date_sch' AND BS.Shift = '2' GROUP BY IE.ItemExt
            )B2 ON B2.ITEMID = D.ItemId

            LEFT JOIN(
              SELECT
              IE.ItemExt AS ITEMID,
              SUM(BS.Target) AS Actual
              FROM BuildSch BS
              LEFT JOIN ProductionSchEXTMaster IE ON IE.ItemGT = BS.ItemId
              WHERE BS.DateBuild = '$date2' AND BS.Shift = '1' GROUP BY IE.ItemExt
            )B3 ON B3.ITEMID = D.ItemId

            LEFT JOIN(
              SELECT
                IE.ItemExt AS ITEMID,
                SUM(BS.Target) AS Actual
                FROM BuildSch BS
                LEFT JOIN ProductionSchEXTMaster IE ON IE.ItemGT = BS.ItemId
                WHERE BS.DateBuild = '$date2' AND BS.Shift = '2' GROUP BY IE.ItemExt
            )B4 ON B4.ITEMID = D.ItemId

            LEFT JOIN(
              SELECT
                  IE.ItemExt AS ITEMID,
                  SUM(BS.Target) AS Actual
                  FROM BuildSch BS
                  LEFT JOIN ProductionSchEXTMaster IE ON IE.ItemGT = BS.ItemId
                  WHERE BS.DateBuild = '$date3' AND BS.Shift = '1' GROUP BY IE.ItemExt
            )B5 ON B5.ITEMID = D.ItemId

            LEFT JOIN(
              SELECT
                    IE.ItemExt AS ITEMID,
                    SUM(BS.Target) AS Actual
                    FROM BuildSch BS
                    LEFT JOIN ProductionSchEXTMaster IE ON IE.ItemGT = BS.ItemId
                    WHERE BS.DateBuild = '$date3' AND BS.Shift = '2' GROUP BY IE.ItemExt
            )B6 ON B6.ITEMID = D.ItemId

            LEFT JOIN (
              SELECT Itemext,DSG_COLOR, Name FROM ProductionSchEXTMaster
      		    GROUP BY Itemext,DSG_COLOR, Name
            ) QTR ON QTR.Itemext = D.ItemId
            LEFT JOIN(
		          SELECT T2.ITEMID,T2.OrderLackshift
			          FROM(
				          SELECT *,ROW_NUMBER() OVER (PARTITION BY T1.ITEMID ORDER BY T1.OrderLackshift ) R
				          FROM(
					          SELECT
            					T.ITEMID,
            					CASE
            					WHEN (T.Total*100)/NULLIF(T.GreentireDay,0) > 100 THEN '4'
            					WHEN (T.Total*100)/NULLIF(T.GreentireDay,0) > 50 THEN '3'
            					WHEN (T.Total*100)/NULLIF(T.GreentireDay,0) > 17 THEN '2'
            					ELSE '1' END AS  OrderLackshift
					              FROM(
              						SELECT B.Itemext AS ITEMID,
              						ISNULL(BS.SpareOfcure, 0 ) +ISNULL( P2.StockInplan,0) AS Total ,
              						((ISNULL(BS.Countprintcure,0) * ISNULL(BS.Rateprint,0)) + (ISNULL(BS.CountPrintcurFG,0) * ISNULL(BS.RatePrintFG,0))) * 2 AS GreentireDay
              						FROM ProductionGreentirePrintTable BS
              						LEFT JOIN ProductionSchReciveTable P2 ON P2.ItemId = BS.ItemId AND P2.Sch_date = '$dateref' AND P2.Shift = '$shiftref'
              						LEFT JOIN ProductionSchEXTMaster B ON B.ItemGT = BS.ItemId
                          WHERE BS.sch_date = '$date_sch' AND BS.shift = '$shift' )T)T1
			                    GROUP BY T1.ITEMID,T1.OrderLackshift)T2
			                    WHERE T2.R = 1)PM ON PM.ITEMID = D.ItemId)T
                  		LEFT JOIN(
                  			SELECT ItemEXT,ITEMNAME_LIST = STUFF((
                  			SELECT ',' + BB.NAME
                  			FROM ProductionSchCompondMaster BB
                  			WHERE  BB.ItemEXT = P.ItemEXT
                  			GROUP BY BB.NAME
                  			FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 1, '')
                  			FROM ProductionSchCompondMaster P
                  			GROUP BY ItemEXT
                      )T2 ON T.ItemId = T2.ItemEXT WHERE  T.TEST IN ('0','2'))DB
                      )X WHERE X.checkzero <> 0  AND X.ShiftCheck = 1",
        [
          $date_sch,
          $shift,
          $date_sch,
          $shift,
          $date_sch,
          $shift,
          $dateref,
          $shiftref,
          $date_sch,
          $shift,
          $dateref,
          $shiftref
        ]
      );
      return $query;
    } catch (\Exception $e) {
      return response(false, $e->getMessage());
    }
  }
  public function checkgriddateplantire($date_sch, $shift)
  {
    $date_sch = date('Y-m-d', strtotime($date_sch));

    $getdate = $this->sql->rows(
      $this->conn->dbConnect(),
      "SELECT * FROM(
        SELECT TOP 2
          DateBuild
        FROM BuildSch
        WHERE DateBuild > CONVERT(date, ?) GROUP BY DateBuild ORDER BY DateBuild ASC
        UNION ALL
        SELECT TOP 2
          DateBuild
        FROM BuildSch
        WHERE DateBuild > CONVERT(date, ?) GROUP BY DateBuild ORDER BY DateBuild ASC)T
        GROUP BY DateBuild",
      [
        $date_sch,
        $date_sch
      ]
    );
    return $getdate;
  }

  public function loadplantiregroup1($date_sch, $shift)
  {
    try {
      $sqlId = "1=1";
      if ($id !== null) {
        $sqlId = "P.Id = $id";
      }
      if ($shift == 1) {
        $dateref = date('Y-m-d', strtotime($date_sch . ' -1 days'));
        $shiftref = 2;
      } else {
        $dateref = $date_sch;
        $shiftref = 1;
      }
      $getdate = $this->sql->rows(
        $this->conn->dbConnect(),
        "SELECT * FROM(
          SELECT TOP 2
            DateBuild
          FROM BuildSch
          WHERE DateBuild > CONVERT(date, ?) GROUP BY DateBuild ORDER BY DateBuild ASC
          UNION ALL
          SELECT TOP 2
            DateBuild
          FROM BuildSch
          WHERE DateBuild > CONVERT(date, ?) GROUP BY DateBuild ORDER BY DateBuild ASC)T
          GROUP BY DateBuild",
        [
          $date_sch,
          $date_sch
        ]
      );
      $date2 = $getdate[0]["DateBuild"];
      $date3 = $getdate[1]["DateBuild"];

      $query = $this->sql->rows(
        $this->conn->dbConnect(),
        "SELECT * FROM
        (SELECT DB.*
        ,ROUND(ISNULL(NULLIF(DB.OrderLackshift,0) / NULLIF(DB.check2,0),0),2) AS check3,
	       CASE
          WHEN DB.OrderLackshift <= 0 OR DB.check2 = 0
          THEN CASE
				  WHEN DB.check1 < 20 THEN 1.00
				  WHEN DB.check1 < 50 THEN 0.95
				  WHEN DB.check1 < 75 THEN 1.90
				  WHEN DB.check1 < 100 THEN 0.32
				  ELSE  0.20 END
		      ELSE CASE
				  WHEN ROUND(DB.OrderLackshift / DB.check2,2) = 1 THEN 1
				  WHEN ROUND(DB.OrderLackshift / DB.check2,2) = 2 THEN 3
          WHEN ROUND(DB.OrderLackshift / DB.check2,2) = 3 THEN 4
          WHEN ROUND(DB.OrderLackshift / DB.check2,2) = 4 THEN 5
          WHEN ROUND(DB.OrderLackshift / DB.check2,2) = 0.48 THEN 2
          WHEN ROUND(DB.OrderLackshift / DB.check2,2) = 0.95 THEN 6
          WHEN ROUND(DB.OrderLackshift / DB.check2,2) = 1.43 THEN 7
          WHEN ROUND(DB.OrderLackshift / DB.check2,2) = 1.9 THEN 8
          WHEN ROUND(DB.OrderLackshift / DB.check2,2) = 0.32 THEN 9
          WHEN ROUND(DB.OrderLackshift / DB.check2,2) = 0.65 THEN 10
          WHEN ROUND(DB.OrderLackshift / DB.check2,2) = 0.97 THEN 11
          WHEN ROUND(DB.OrderLackshift / DB.check2,2) = 1.29 THEN 12
          WHEN ROUND(DB.OrderLackshift / DB.check2,2) = 0.24 THEN 13
          WHEN ROUND(DB.OrderLackshift / DB.check2,2) = 0.49 THEN 14
          WHEN ROUND(DB.OrderLackshift / DB.check2,2) = 0.73 THEN 15
          WHEN ROUND(DB.OrderLackshift / DB.check2,2) = 0.98 THEN 16
          WHEN ROUND(DB.OrderLackshift / DB.check2,2) = 0.2 THEN 17
          WHEN ROUND(DB.OrderLackshift / DB.check2,2) = 0.39 THEN 18
          WHEN ROUND(DB.OrderLackshift / DB.check2,2) = 1.59 THEN 19
				  ELSE 20 END
			  END AS checktotal,
        CASE
			    WHEN  DB.Total = 0 AND DB.GrandTotal =0 AND DB.ActualDay1C =0 AND DB.ActualDay1D = 0 AND DB.ActualDay2C = 0
			    AND  DB.ActualDay2D = 0 AND DB.ActualDay3C = 0 AND DB.ActualDay3D = 0 AND ISNULL(DB.ShiftDay1C,0) = 0  AND ISNULL(DB.ShiftDay1D,0) = 0
			    AND ISNULL(DB.ShiftDay2C,0) = 0 AND ISNULL(DB.ShiftDay2D,0) = 0 AND ISNULL(DB.ShiftDay3C,0) = 0 AND ISNULL(DB.ShiftDay3D,0) = 0
			    THEN 0
			  ELSE 1 END AS checkzero,
        CASE
          WHEN $shift = 1
        THEN
        CASE WHEN  ISNULL(DB.ShiftDay1C,0) < 0 THEN 1
         ELSE 2 END
        ELSE
        CASE WHEN  ISNULL(DB.ShiftDay1D,0) < 0 THEN 1 
        ELSE  2 END
        END AS ShiftCheck
		    FROM(
        SELECT T.*,T2.ITEMNAME_LIST,
	      CASE WHEN T.Total+(T.ActualDay1C + T.ActualDay1D + T.ActualDay2C + T.ActualDay2D + T.ActualDay3C + T.ActualDay3D ) > 0
        THEN  CASE WHEN $shift = '1' THEN ISNULL((NULLIF(T.Total,0) / (NULLIF(T.ActualDay1C,0)+0.00)),0)*100 ELSE ISNULL((NULLIF(T.Total,0) / (NULLIF(T.ActualDay1D,0)+0.00)),0)*100 END
        ELSE 0
        END AS check1,
        CASE
          WHEN  $shift = '1'
			    THEN CASE
					  WHEN T.ActualDay1C > 0
					  THEN CASE
						  WHEN ISNULL((NULLIF(T.Total,0) / (NULLIF(T.ActualDay1C,0)+0.00)),0)*100 <= 20
						  THEN 1.00
						  WHEN ISNULL((NULLIF(T.Total,0) / (NULLIF(T.ActualDay1C,0)+0.00)),0)*100 <= 50
						  THEN 2.10
              WHEN ISNULL((NULLIF(T.Total,0) / (NULLIF(T.ActualDay1C,0)+0.00)),0)*100 <= 75
						  THEN 3.10
						  WHEN ISNULL((NULLIF(T.Total,0) / (NULLIF(T.ActualDay1C,0)+0.00)),0)*100 < 100
					    THEN 4.10
						  ELSE 5.10 END
					ELSE 0 END
        ELSE CASE
				  WHEN T.ActualDay1D > 0
					THEN CASE
						WHEN ISNULL((NULLIF(T.Total,0) / (NULLIF(T.ActualDay1D,0)+0.00)),0)*100 <= 20
						THEN 1.00
						WHEN ISNULL((NULLIF(T.Total,0) / (NULLIF(T.ActualDay1D,0)+0.00)),0)*100 <= 50
						THEN 2.10
						WHEN ISNULL((NULLIF(T.Total,0) / (NULLIF(T.ActualDay1D,0)+0.00)),0)*100 <= 75
					  THEN 3.10
            WHEN ISNULL((NULLIF(T.Total,0) / (NULLIF(T.ActualDay1D,0)+0.00)),0)*100 < 100
					  THEN 4.10
						ELSE 5.10 END
					ELSE 0 END
          END AS check2
		      FROM(
            SELECT
            D.ItemId,
            QTR.Name,
            PM.OrderLackshift,
            ((D.Stock2 + D.TotalProduct) - (D.TotalPayOfCar + D.TireNotSpec)) +(D.StockPD + D.TotalPD) - (D.TireNotSpacPD +D.ProducePD)AS Total,
            (D.StockPD + D.TotalPD) - (D.TireNotSpacPD +D.ProducePD)  AS TotalSystemPD,
            ((D.Stock2 + D.TotalProduct) - (D.TotalPayOfCar + D.TireNotSpec)) AS GrandTotal,
            ISNULL(B1.Actual,0) AS ActualDay1C,
            ISNULL(B2.Actual,0) AS ActualDay1D,
            ISNULL(B3.Actual,0) AS ActualDay2C,
            ISNULL(B4.Actual,0) AS ActualDay2D,
            ISNULL(B5.Actual,0) AS ActualDay3C,
            ISNULL(B6.Actual,0) AS ActualDay3D,
            CASE
          WHEN B1.Actual IN (0,NULL)
        THEN NULL
        ELSE (((D.Stock2 + D.TotalProduct) - (D.TotalPayOfCar + D.TireNotSpec))+((D.StockPD + D.TotalPD) - (D.TireNotSpacPD +D.ProducePD))) - ISNULL(B1.Actual,0)
        END AS ShiftDay1C,
        CASE
          WHEN B2.Actual IN (0,NULL)
        THEN NULL
        ELSE (((D.Stock2 + D.TotalProduct) - (D.TotalPayOfCar + D.TireNotSpec))+((D.StockPD + D.TotalPD) - (D.TireNotSpacPD +D.ProducePD))) - (ISNULL(B1.Actual,0) +ISNULL(B2.Actual,0))
        END AS ShiftDay1D,
        CASE
          WHEN B3.Actual IN (0,NULL)
        THEN NULL
        ELSE (((D.Stock2 + D.TotalProduct) - (D.TotalPayOfCar + D.TireNotSpec))+((D.StockPD + D.TotalPD) - (D.TireNotSpacPD +D.ProducePD))) - (ISNULL(B1.Actual,0) +ISNULL(B2.Actual,0) + ISNULL(B3.Actual,0))
        END AS ShiftDay2C,
        CASE
          WHEN B4.Actual IN (0,NULL)
        THEN NULL
        ELSE (((D.Stock2 + D.TotalProduct) - (D.TotalPayOfCar + D.TireNotSpec))+((D.StockPD + D.TotalPD) - (D.TireNotSpacPD +D.ProducePD))) - (ISNULL(B1.Actual,0) +ISNULL(B2.Actual,0) + ISNULL(B3.Actual,0) + ISNULL(B4.Actual,0))
        END AS ShiftDay2D,
        (((D.Stock2 + D.TotalProduct) - (D.TotalPayOfCar + D.TireNotSpec))+((D.StockPD + D.TotalPD) - (D.TireNotSpacPD +D.ProducePD))) - (ISNULL(B1.Actual,0) +ISNULL(B2.Actual,0) + ISNULL(B3.Actual,0) + ISNULL(B4.Actual,0) + ISNULL(B5.Actual,0)) AS ShiftDay3C,
        (((D.Stock2 + D.TotalProduct) - (D.TotalPayOfCar + D.TireNotSpec))+((D.StockPD + D.TotalPD) - (D.TireNotSpacPD +D.ProducePD))) - (ISNULL(B1.Actual,0) +ISNULL(B2.Actual,0) + ISNULL(B3.Actual,0) + ISNULL(B4.Actual,0) + ISNULL(B5.Actual,0) + ISNULL(B6.Actual,0)) AS ShiftDay3D,
        
        D.BL ,
            CASE
              WHEN (((D.Stock2 + D.TotalProduct) - (D.TotalPayOfCar + D.TireNotSpec))+((D.StockPD + D.TotalPD) - (D.TireNotSpacPD +D.ProducePD))) - (B1.Actual +B2.Actual) > (B3.Actual + B4.Actual)
              THEN 'มากกว่า 1 วัน'
              ELSE '0'
            END AS StockStatus ,
            CASE
          WHEN $shift = 1
            THEN
            CASE WHEN ((((D.Stock2 + D.TotalProduct) - (D.TotalPayOfCar + D.TireNotSpec)) +(D.StockPD + D.TotalPD) - (D.TireNotSpacPD +D.ProducePD)) - ISNULL(B1.Actual,0)) < 0 
            AND ((((D.Stock2 + D.TotalProduct) - (D.TotalPayOfCar + D.TireNotSpec)) +(D.StockPD + D.TotalPD) - (D.TireNotSpacPD +D.ProducePD)) - (ISNULL(B1.Actual,0)+ISNULL(B2.Actual,0))) < 0 THEN 0 
            ELSE CASE  WHEN (((D.Stock2 + D.TotalProduct) - (D.TotalPayOfCar + D.TireNotSpec)) +(D.StockPD + D.TotalPD) - (D.TireNotSpacPD +D.ProducePD)) - ISNULL(B1.Actual,0) < 0 THEN 1 ELSE 
            CASE  WHEN (((D.Stock2 + D.TotalProduct) - (D.TotalPayOfCar + D.TireNotSpec)) +(D.StockPD + D.TotalPD) - (D.TireNotSpacPD +D.ProducePD)) - (ISNULL(B1.Actual,0)+ISNULL(B2.Actual,0)) < 0 THEN 2 ELSE 3 END
             END
             END
            ELSE
             CASE WHEN ((((D.Stock2 + D.TotalProduct) - (D.TotalPayOfCar + D.TireNotSpec)) +(D.StockPD + D.TotalPD) - (D.TireNotSpacPD +D.ProducePD)) - ISNULL(B2.Actual,0)) < 0 
             AND ((((D.Stock2 + D.TotalProduct) - (D.TotalPayOfCar + D.TireNotSpec)) +(D.StockPD + D.TotalPD) - (D.TireNotSpacPD +D.ProducePD)) - (ISNULL(B2.Actual,0)+ISNULL(B3.Actual,0))) < 0 THEN 0 
            ELSE CASE  WHEN (((D.Stock2 + D.TotalProduct) - (D.TotalPayOfCar + D.TireNotSpec)) +(D.StockPD + D.TotalPD) - (D.TireNotSpacPD +D.ProducePD)) - ISNULL(B2.Actual,0) < 0 THEN 1 ELSE 
            CASE  WHEN (((D.Stock2 + D.TotalProduct) - (D.TotalPayOfCar + D.TireNotSpec)) +(D.StockPD + D.TotalPD) - (D.TireNotSpacPD +D.ProducePD)) - (ISNULL(B2.Actual,0)+ISNULL(B3.Actual,0)) < 0 THEN 2 ELSE 3 END
             END
             END
            END AS TEST,
            QTR.DSG_COLOR
            FROM(
              SELECT
              T.ItemId,
              ISNULL(P2.Stock,0) AS Stock2,
              (ISNULL(P.NumberCar1,0) + ISNULL(P.NumberCar2,0) +ISNULL(P.NumberCar3,0)+
              ISNULL(P.NumberCar4,0)+ ISNULL(P.NumberCar5,0) + ISNULL(P.NumberCar6,0) +
              ISNULL(P.NumberCar7,0)+ ISNULL(P.NumberCar8,0) ) AS TotalProduct,
              (ISNULL(P.PayOfCar,0) + ISNULL(P.PayOfCar2,0) + ISNULL(P.PayOfCar3,0)+
              ISNULL(P.PayOfCar4,0)+ISNULL(P.PayOfCar5,0) + ISNULL(P.PayOfCar6,0)+
              ISNULL(P.PayOfCar7,0) + ISNULL(P.PayOfCar8,0)) AS TotalPayOfCar,
              ISNULL(P.TireNotSpec,0) AS TireNotSpec,
              ISNULL(PD2.Stock,0) AS StockPD,
              ISNULL(PD.Car1_1,0) + ISNULL(PD.Car1_2,0) + ISNULL(PD.Car1_3,0) + ISNULL(PD.Car1_4,0) +
              ISNULL(PD.Car1_5,0) + ISNULL(PD.Car1_6,0)+ ISNULL(PD.Car1_7,0) + ISNULL(PD.Car1_8,0) AS TotalPD,
              ISNULL(PD.TireNotSpac,0) AS TireNotSpacPD,
              ISNULL(PD.Actual,0) AS ProducePD,
              PD.BL
              FROM(
                SELECT
                X.ItemId
                FROM(
                  SELECT
                  ItemId
                  FROM ProductionGreentireDisburseTable
                  WHERE Sch_date = ? AND Shift = ?
                  UNION ALL
                  SELECT
                  ItemId
                  FROM ProductionGreentireFaceOfireTable
                  WHERE Sch_Date = ? AND Shift = ?)X
                  GROUP BY  X.ItemId) T
                  LEFT JOIN ProductionGreentireFaceOfireTable P ON P.ItemId = T.ItemId AND P.Sch_date = ? AND P.Shift = ?
                  LEFT JOIN ProductionGreentireFaceOfireTable P2 ON P2.ItemId = T.ItemId AND P2.Sch_date = ? AND P2.Shift = ?
                  LEFT JOIN ProductionGreentireDisburseTable PD ON PD.ItemId = T.ItemId AND PD.Sch_date = ? AND PD.Shift = ?
                  LEFT JOIN ProductionGreentireDisburseTable PD2 ON PD2.ItemId = T.ItemId AND PD2.Sch_date = ? AND PD2.Shift = ?
            )D
            LEFT JOIN(
              SELECT
              IE.ItemExt AS ITEMID,
              SUM(BS.Target) AS Actual
              FROM BuildSch BS
              LEFT JOIN ProductionSchEXTMaster IE ON IE.ItemGT = BS.ItemId
              WHERE BS.DateBuild = '$date_sch' AND BS.Shift = '1' GROUP BY IE.ItemExt
            )B1 ON B1.ITEMID = D.ItemId

            LEFT JOIN(
              SELECT
              IE.ItemExt AS ITEMID,
              SUM(BS.Target) AS Actual
              FROM BuildSch BS
              LEFT JOIN ProductionSchEXTMaster IE ON IE.ItemGT = BS.ItemId
              WHERE BS.DateBuild = '$date_sch' AND BS.Shift = '2' GROUP BY IE.ItemExt
            )B2 ON B2.ITEMID = D.ItemId

            LEFT JOIN(
              SELECT
              IE.ItemExt AS ITEMID,
              SUM(BS.Target) AS Actual
              FROM BuildSch BS
              LEFT JOIN ProductionSchEXTMaster IE ON IE.ItemGT = BS.ItemId
              WHERE BS.DateBuild = '$date2' AND BS.Shift = '1' GROUP BY IE.ItemExt
            )B3 ON B3.ITEMID = D.ItemId

          LEFT JOIN(
            SELECT
              IE.ItemExt AS ITEMID,
              SUM(BS.Target) AS Actual
              FROM BuildSch BS
              LEFT JOIN ProductionSchEXTMaster IE ON IE.ItemGT = BS.ItemId
              WHERE BS.DateBuild = '$date2' AND BS.Shift = '2' GROUP BY IE.ItemExt
          )B4 ON B4.ITEMID = D.ItemId

        LEFT JOIN(
          SELECT
              IE.ItemExt AS ITEMID,
              SUM(BS.Target) AS Actual
              FROM BuildSch BS
              LEFT JOIN ProductionSchEXTMaster IE ON IE.ItemGT = BS.ItemId
              WHERE BS.DateBuild = '$date3' AND BS.Shift = '1' GROUP BY IE.ItemExt
        )B5 ON B5.ITEMID = D.ItemId

      LEFT JOIN(
        SELECT
              IE.ItemExt AS ITEMID,
              SUM(BS.Target) AS Actual
              FROM BuildSch BS
              LEFT JOIN ProductionSchEXTMaster IE ON IE.ItemGT = BS.ItemId
              WHERE BS.DateBuild = '$date3' AND BS.Shift = '2' GROUP BY IE.ItemExt
      )B6 ON B6.ITEMID = D.ItemId
      LEFT JOIN (
        SELECT Itemext,DSG_COLOR, Name FROM ProductionSchEXTMaster
		      GROUP BY Itemext,DSG_COLOR, Name
      ) QTR ON QTR.Itemext = D.ItemId

      LEFT JOIN(
		    SELECT T2.ITEMID,T2.OrderLackshift
			    FROM(
				  SELECT *,ROW_NUMBER() OVER (PARTITION BY T1.ITEMID ORDER BY T1.OrderLackshift ) R
				    FROM(
					    SELECT
					    T.ITEMID,
					    CASE
					      WHEN (T.Total*100)/NULLIF(T.GreentireDay,0) > 100 THEN '4'
					      WHEN (T.Total*100)/NULLIF(T.GreentireDay,0) > 50 THEN '3'
					      WHEN (T.Total*100)/NULLIF(T.GreentireDay,0) > 17 THEN '2'
					      ELSE '1' END AS  OrderLackshift
					      FROM(
						      SELECT B.Itemext AS ITEMID,
						      ISNULL(BS.SpareOfcure, 0 ) +ISNULL( P2.StockInplan,0) AS Total ,
						      ((ISNULL(BS.Countprintcure,0) * ISNULL(BS.Rateprint,0)) + (ISNULL(BS.CountPrintcurFG,0) * ISNULL(BS.RatePrintFG,0))) * 2 AS GreentireDay
						      FROM ProductionGreentirePrintTable BS
						      LEFT JOIN ProductionSchReciveTable P2 ON P2.ItemId = BS.ItemId AND P2.Sch_date = '$dateref' AND P2.Shift = '$shiftref'
						      LEFT JOIN ProductionSchEXTMaster B ON B.ItemGT = BS.ItemId
						      WHERE BS.sch_date = '$date_sch' AND BS.shift = '$shift' )T )T1
			            GROUP BY T1.ITEMID,T1.OrderLackshift)T2
			            WHERE T2.R = 1)PM ON PM.ITEMID = D.ItemId)T

		    LEFT JOIN(
			    SELECT ItemEXT,ITEMNAME_LIST = STUFF((
			    SELECT ',' + BB.NAME
			    FROM ProductionSchCompondMaster BB
			    WHERE  BB.ItemEXT = P.ItemEXT
			    GROUP BY BB.NAME
			    FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 1, '')
			    FROM ProductionSchCompondMaster P
          GROUP BY ItemEXT)T2 ON T.ItemId = T2.ItemEXT WHERE  T.TEST IN ('0','1'))DB
          )X WHERE X.checkzero <> 0 AND X.ShiftCheck = 1",
        [
          $date_sch,
          $shift,
          $date_sch,
          $shift,
          $date_sch,
          $shift,
          $dateref,
          $shiftref,
          $date_sch,
          $shift,
          $dateref,
          $shiftref
        ]
      );
      return $query;
    } catch (\Exception $e) {
      return response(false, $e->getMessage());
    }
  }
  public function loadTargetbillbuy($shiftDate)
  {
    return $this->sql->rows(
      $this->conn->dbConnect(),
      "SELECT 
        X.ItemGT AS ItemID,
        X.ItemGTName AS ItemName,
        X.Shift,
        SUM(X.BillUse) AS BillUse,
        SUM (X.BillGive) AS BillGive,
        SUM(X.faceboiler) AS faceboiler ,
        -- X.BillUse,
        -- X.BillGive,
        -- X.faceboiler,
        X.TT,
        X.Weight,
        X.ColorAll
        FROM(
          SELECT
          P.ItemID,
          GM.ItemGT,
          P.BillUse,
          P.BillGive,
          P.faceboiler,
          P.Shift,
          GM.ColorAll,
          GM.ItemGTName,
          GM.Weight,
          GM.TT
          FROM ProductionSchTable P
          LEFT JOIN ProductionSchGreentireMaster GM ON P.ItemID = GM.ItemFG
          WHERE P.SchDate= ?  AND P.Company= 'dsl' AND P.ItemID IS NOT NULL AND P.Shift = '1'
          
          UNION ALL 
          
          SELECT
            P.ItemID,
            GM.ItemGT,
            P.BillUse,
            P.BillGive,
            P.faceboiler,
            P.Shift,
            GM.ColorAll,
            GM.ItemGTName,
            GM.Weight,
            GM.TT
            FROM ProductionSchTable P
            LEFT JOIN ProductionSchGreentireMaster GM ON P.ItemID = GM.ItemFG
            WHERE P.SchDate= ?  AND P.Company= 'dsl' AND P.ItemID IS NOT NULL AND P.Shift = '2')X
            --\\WHERE X.ItemGT IN ('I-0028064','I-0060019')
            GROUP BY X.ItemGT,X.ItemGTName,X.Shift,X.TT,X.Weight, X.ColorAll
            ORDER BY X.ItemGT ASC",
      [
        $shiftDate,
        $shiftDate
      ]
    );
  }

  public function insertSchTableOrder($date_sch, $shift, $copy, $date_gen, $shift_gen, $gen_emp, $date_emp, $shift_emp)
  {
    try {
      $date = date("Y-m-d H:i:s");


      if ($copy == 0) {

        if ($shift == 1) {
          $dateref = $date_sch;
          $shiftref = 2;
          $daterecive = date('Y-m-d', strtotime($date_sch . ' -1 days'));
          $dateNext = $date_sch;
        } else {
          $dateref = date('Y-m-d', strtotime($date_sch . ' +1 days'));
          $daterecive = $date_sch;
          $shiftref = 1;
          $dateNext = date('Y-m-d', strtotime($date_sch . ' +1 days'));
        }


        $insertSch = \sqlsrv_query(
          $this->conn->dbConnect(),
          "INSERT INTO ProductionSchReciveTable (
           Sch_date,
           Shift,
           CrateDate,
           CreateBy
          ) VALUES(?, ?, ?, ?)",
          [
            $date_sch,
            $shift,
            $date,
            $_SESSION["user_login"]

          ]
        );

        $update = \sqlsrv_query(
          $this->conn->dbConnect(),
          "UPDATE X
          SET X.StockInplan = Y.StockInplan  + ISNULL(X.CalStock,0)
          FROM  ProductionSchReciveTable X
          LEFT JOIN(
          SELECT P1.ItemId,(ISNULL(P2.StockInplan,0) +ISNULL(P1.CountIn,0))-(ISNULL(P1.CountOut,0) + ISNULL(P1.CountNotSpec,0)) AS StockInplan
          FROM ProductionSchReciveTable P1 
          LEFT JOIN ProductionSchReciveTable P2 ON P1.ItemId = P2.ItemId AND P2.Sch_date = ? AND P2.Shift = ?
          WHERE P1.Sch_date = ? AND P1.Shift = ?)Y
          ON X.ItemId = Y.ItemId
          WHERE X.Sch_date = ? AND X.Shift = ?",
          [
            $daterecive,
            $shiftref,
            $date_sch,
            $shift,
            $date_sch,
            $shift
          ]
        );

        if (!$insertSch) {
          sqlsrv_rollback($this->conn->dbConnect());
          return false;
        }

        sqlsrv_commit($this->conn->dbConnect());
        return true;
      } else {

        $q = sqlsrv_has_rows(sqlsrv_query(
          $this->conn->dbConnect(),
          "SELECT *
         FROM ProductionSchReciveTable
         WHERE Sch_date = ? AND Shift = ?",
          [
            $date_sch,
            $shift
          ]
        ));

        if ($q === true) {
          $delete = sqlsrv_query(
            $this->conn->dbConnect(),
            "DELETE ProductionSchReciveTable
           WHERE Sch_date = ? AND Shift = ?",
            [
              $date_sch,
              $shift
            ]
          );
        }

        // $checksch = sqlsrv_has_rows(sqlsrv_query(
        //   $this->conn->dbConnect(),
        //   "SELECT *
        //  FROM BuildSch
        //  WHERE DateBuild = ? AND Shift = ?",
        //   [
        //     $date_gen,
        //     $shift_gen
        //   ]
        // ));

        // if ($checksch === false) {
        //   return false;
        // }

        if ($shift == 1) {
          $dateref = $date_sch;
          $shiftref = 2;
          $daterecive = date('Y-m-d', strtotime($date_sch . ' -1 days'));
          $dateNext = $date_sch;
        } else {
          $dateref = date('Y-m-d', strtotime($date_sch . ' +1 days'));
          $daterecive = $date_sch;
          $shiftref = 1;
          $dateNext = date('Y-m-d', strtotime($date_sch . ' +1 days'));
        }


        $insertSch = \sqlsrv_query(
          $this->conn->dbConnect(),
          "INSERT INTO ProductionSchReciveTable (
            ItemId,
            Shift,
            Sch_date,
           -- StockInplan,
            CountShift,
            CountPlan
           )
           SELECT 
            X.ItemId,
            '$shift' AS Shift,
            '$date_sch' AS DateBuild,
            --CASE WHEN Q.Target IS NULL OR Q.Target = '' THEN Q2.CountShift ELSE Q.Target END AS CountShift,
            --CASE WHEN Q.Actual IS NULL OR Q.Actual = '' THEN Q2.CountPlan ELSE Q.Target END AS CountPlan
            Q.Target AS CountShift ,
            Q.Actual AS CountPlan
            FROM 
            (SELECT ItemId FROM ( 
             SELECT ItemId FROM ProductionSchReciveTable where Sch_date = ? and Shift = ?
             UNION ALL
             SELECT ItemId from BuildSch where DateBuild = ? and Shift = ?)A
            GROUP BY A.ItemId)X
            LEFT JOIN BuildSch Q ON X.ItemId = Q.ItemId AND Q.DateBuild = ? and Q.Shift = ?",
          [
            $daterecive,
            $shiftref,
            $date_gen,
            $shift_gen,
            $date_gen,
            $shift_gen
          ]
        );

        $update = \sqlsrv_query(
          $this->conn->dbConnect(),
          "UPDATE X
          SET X.StockInplan = Y.StockInplan + ISNULL(X.CalStock,0)
          FROM  ProductionSchReciveTable X
          LEFT JOIN(
          SELECT P1.ItemId,(ISNULL(P2.StockInplan,0) +ISNULL(P1.CountIn,0))-(ISNULL(P1.CountOut,0) + ISNULL(P1.CountNotSpec,0)) AS StockInplan
          FROM ProductionSchReciveTable P1 
          LEFT JOIN ProductionSchReciveTable P2 ON P1.ItemId = P2.ItemId AND P2.Sch_date = ? AND P2.Shift = ?
          WHERE P1.Sch_date = ? AND P1.Shift = ?)Y
          ON X.ItemId = Y.ItemId
          WHERE X.Sch_date = ? AND X.Shift = ?",
          [
            $daterecive,
            $shiftref,
            $date_sch,
            $shift,
            $date_sch,
            $shift
          ]
        );

        $updateStockOrder = \sqlsrv_query(
          $this->conn->dbConnect(),
          "UPDATE X
          SET X.StockOrder = Y.StockPrderCheck 
          FROM  ProductionSchReciveTable X
          LEFT JOIN(
          SELECT
		        T1.ItemId,
		        (ISNULL(T2.StockOrder,0) + ISNULL(T1.CountIn,0)) - ISNULL(T1.CountOut,0) AS StockPrderCheck
		      FROM ProductionSchReciveTable T1
		      LEFT JOIN ProductionSchReciveTable T2 ON T1.ItemId = T2.ItemId
		      AND T2.Sch_date = ? AND T2.Shift = ?
		      WHERE T1.Sch_date = ? and T1.Shift = ?
         )Y ON X.ItemId = Y.ItemId
          WHERE X.Sch_date = ? AND X.Shift = ?",
          [
            $daterecive,
            $shiftref,
            $date_sch,
            $shift,
            $date_sch,
            $shift
          ]
        );

        if (!$insertSch) {
          sqlsrv_rollback($this->conn->dbConnect());
          return false;
        }

        sqlsrv_commit($this->conn->dbConnect());
        return true;
      }
    } catch (Exception $e) {
      return false;
    }
  }

  public function loadplaloadordersummaryntire($date_sch, $shift)
  {
    try {
      $date_sch = date('Y-m-d', strtotime($date_sch));
      if ($shift == 1) {
        $dateNext = $date_sch;
        $shiftNext = 2;
        $datepass = date('Y-m-d', strtotime($date_sch . ' -1 days'));
        $shiftpass = 2;
      } else {
        $dateNext = date('Y-m-d', strtotime($date_sch . ' +1 days'));
        $shiftNext = 1;
        $datepass = $date_sch;
        $shiftpass = 1;
      }

      $query = $this->sql->rows(
        $this->conn->dbConnect(),
        "SELECT *
          ,T.StockInplan + T.CountIn AS GreentireInDept
          ,(T.StockInplan + T.CountIn)- T.CountOut AS SummaryInDept
          ,CASE WHEN $shift = 1 THEN (T.SpareOfcure +T.CountCure) - T.BomCActual ELSE (T.SpareOfcure +T.CountCure) - T.BomDActual END AS CalCure   -- คำนวณหน้าเตา
          ,((T.StockInplan + T.CountIn)- T.CountOut) + T.SpareOfcure2 AS  SummaryCure -- คงเหลือในแผนก + หน้าเตา
          ,CASE WHEN T.Actual = T.CountIn THEN '-' ELSE 'X' END AS CompareCreateRecve -- สร้าง/ รับเข้า
          ,CASE WHEN T.CountOut = T.CountCure THEN '-' ELSE 'X' END AS CompareBillBuy -- เบิก/ จ่าย
          ,CASE WHEN $shift = 1 THEN  CASE WHEN ((T.SpareOfcure +T.CountCure) - T.BomCActual) = T.SpareOfcure  THEN '-' ELSE 'X' END
          ELSE CASE WHEN ((T.SpareOfcure +T.CountCure) - T.BomDActual) = T.SpareOfcure THEN '-' ELSE 'X' END END AS CompareFaceTire --ยางหน้าเตาคำนวณ/นับจริง 
          ,CASE WHEN T.SpareOfcure = T.CountInOrder THEN '-' ELSE 'X' END AS CompareReal -- เปรียบเทียบนับจริง
          ,CASE WHEN  $shift = 1 THEN T.BomCActual ELSE  T.BomDActual END AS  BomCheck
        FROM(
        SELECT 
             TP.Id,
             TP.ItemId ,
             GM.ColorAll ,
             GM.ItemGTName ,
             ISNULL(BT.Actual, 0) AS Actual ,  -- สร้างโครงผลิตได้
             ISNULL(TT.BomCActual, 0) AS BomCActual , -- อบยางผลิตได้ กะกลางวัน
             ISNULL(TT.BomDActual, 0) AS BomDActual , -- อบยางผลิตได้ กะกลางคืน
             ISNULL(TP.SpareOfcure, 0) AS SpareOfcure , -- Spare หน้าเตา
            -- ISNULL(TR2.StockInplan, 0) AS StockInplan ,  -- stock ใน แผนก
            ISNULL(TR2.StockInplan, 0) AS StockInplan , 
             ISNULL(TR.CountIn, 0) AS CountIn , -- รับเข้า greentire
             ISNULL(TR.CountOut, 0) AS CountOut , -- จ่าออก greentire
             ISNULL(TP.CountCure, 0) AS CountCure , -- อบยางเบิก greentire
             ISNULL(TP2.SpareOfcure, 0) AS SpareOfcure2, -- Spare หน้าเตา กะล่วงหน้า
             ISNULL(TP.CountInOrder,0) AS CountInOrder   -- นับจริง 
        FROM ProductionGreentirePrintTable TP
        LEFT JOIN
          (SELECT ItemGT,
              ColorAll,
              ItemGTName
           FROM ProductionSchGreentireMaster
           GROUP BY ItemGT,
              ColorAll,
              ItemGTName) GM ON GM.ItemGT = TP.ItemId
        LEFT JOIN BuildSch BT ON BT.ItemId = TP.ItemId
        AND BT.DateBuild = ?
        AND BT.Shift = ?
        LEFT JOIN TargetGreentire TT ON TT.ItemId = TP.ItemId
        AND CONVERT(date,ShiftDate) = ?
        LEFT JOIN ProductionSchReciveTable TR ON TR.ItemID = TP.ItemId
        AND TR.Sch_date = ?
        AND TR.Shift = ?
        LEFT JOIN ProductionSchReciveTable TR2 ON TR2.ItemID = TP.ItemId
        AND TR2.Sch_date = ?
        AND TR2.Shift = ?
        LEFT JOIN ProductionGreentirePrintTable TP2 ON TP2.ItemId = TP.ItemId
        AND TP2.Sch_date = ?
        AND TP2.Shift = ?
        WHERE TP.sch_date = ?
          AND TP.Shift = ?)T",
        [
          $date_sch,
          $shift,
          $date_sch,
          $date_sch,
          $shift,
          $datepass,
          $shiftpass,
          $dateNext,
          $shiftNext,
          $date_sch,
          $shift


        ]
      );
      return $query;
    } catch (\Exception $e) {
      return response(false, $e->getMessage());
    }
  }

  public function updateschordersumary($CountInOrder,  $id, $date_sch, $shift)
  {


    $update = \sqlsrv_query(
      $this->conn->dbConnect(),
      "UPDATE ProductionGreentirePrintTable
      SET CountInOrder = ?
      
      WHERE Id = ?",
      [
        $CountInOrder,
        $id
      ]
    );


    if ($update) {
      return true;
    } else {
      return false;
    }
  }
  public function updateSchtireTable($date_sch, $shift)
  {
    try {
      $date = date("Y-m-d H:i:s");



      $getcheckdate = $this->sql->rows(
        $this->conn->dbConnect(),
        "SELECT  Sch_Date,Shift , COUNT(Sch_Date) OVER(PARTITION BY Sch_Date) AS CountRow
          FROM ProductionGreentireDisburseTable where Sch_Date = ?
          group by Sch_Date,Shift  order by Sch_Date desc,Shift desc
          ",
        [
          $date_sch
        ]
      );
      $datacheckCunt = $getcheckdate[0]["CountRow"];
      $shifcheck = $getcheckdate[0]["Shift"];


      if ($datacheckCunt == "" || $datacheckCunt == NULL) {

        $getdate = $this->sql->rows(
          $this->conn->dbConnect(),
          "SELECT TOP 1 Sch_Date,Shift 
                FROM ProductionGreentireDisburseTable where Sch_Date < ?
                group by Sch_Date,Shift  order by Sch_Date desc,Shift desc",
          [$date_sch]
        );
        $getdateNext = $this->sql->rows(
          $this->conn->dbConnect(),
          "SELECT TOP 1 Sch_Date,Shift 
                FROM ProductionGreentireDisburseTable where Sch_Date > ?
                group by Sch_Date,Shift  order by Sch_Date asc,Shift asc",
          [$date_sch]
        );
        $dateold = $getdate[0]["Sch_Date"];
        $shiftold = $getdate[0]["Shift"];
        $datenext = $getdateNext[0]["Sch_Date"];
        $shifnext = $getdateNext[0]["Shift"];
      } else {

        if ($datacheckCunt == 1) {
          if ($shift == 1) {
            $getdate = $this->sql->rows(
              $this->conn->dbConnect(),
              "SELECT TOP 1 Sch_Date,Shift 
                FROM ProductionGreentireDisburseTable where Sch_Date < ?
                group by Sch_Date,Shift  order by Sch_Date desc,Shift desc",
              [$date_sch]
            );
            if ($shifcheck == 1) {
              $getdateNext = $this->sql->rows(
                $this->conn->dbConnect(),
                "SELECT TOP 1 Sch_Date,Shift 
                        FROM ProductionGreentireDisburseTable where Sch_Date > ?
                        group by Sch_Date,Shift  order by Sch_Date asc,Shift asc",
                [$date_sch]
              );
            } else {
              $getdateNext = $this->sql->rows(
                $this->conn->dbConnect(),
                "SELECT TOP 1 Sch_Date,Shift 
                        FROM ProductionGreentireDisburseTable where Sch_Date >= ?
                        group by Sch_Date,Shift  order by Sch_Date asc,Shift asc",
                [$date_sch]
              );
            }
            $dateold = $getdate[0]["Sch_Date"];
            $shiftold = $getdate[0]["Shift"];
            $datenext = $getdateNext[0]["Sch_Date"];
            $shifnext = $getdateNext[0]["Shift"];
          } else {
            if ($shifcheck == 1) {
              $getdate = $this->sql->rows(
                $this->conn->dbConnect(),
                "SELECT TOP 1 Sch_Date,Shift 
                        FROM ProductionGreentireDisburseTable where Sch_Date <= ?
                        group by Sch_Date,Shift  order by Sch_Date desc,Shift desc",
                [$date_sch]
              );
              $dateold = $getdate[0]["Sch_Date"];
              $shiftold = $getdate[0]["Shift"];
            } else {
              $getdate = $this->sql->rows(
                $this->conn->dbConnect(),
                "SELECT TOP 2 Sch_Date,Shift 
                        FROM ProductionGreentireDisburseTable where Sch_Date <= ?
                        group by Sch_Date,Shift  order by Sch_Date desc,Shift desc",
                [$date_sch]
              );
              $dateold = $getdate[1]["Sch_Date"];
              $shiftold = $getdate[1]["Shift"];
            }
            $getdateNext = $this->sql->rows(
              $this->conn->dbConnect(),
              "SELECT TOP 1 Sch_Date,Shift 
                            FROM ProductionGreentireDisburseTable where Sch_Date > ?
                            group by Sch_Date,Shift  order by Sch_Date asc,Shift asc",
              [$date_sch]
            );
            $dateold = $dateold;
            $shiftold = $shiftold;
            $datenext = $getdateNext[0]["Sch_Date"];
            $shifnext = $getdateNext[0]["Shift"];
          }
        } else {
          if ($shift == 1) {
            $getdate = $this->sql->rows(
              $this->conn->dbConnect(),
              "SELECT TOP 3 Sch_Date,Shift 
                  FROM ProductionGreentireDisburseTable where Sch_Date <= ?
                  group by Sch_Date,Shift  order by Sch_Date desc,Shift desc
                  ",
              [
                $date_sch

              ]
            );

            $getdateNext = $this->sql->rows(
              $this->conn->dbConnect(),
              "SELECT TOP 1 Sch_Date,Shift 
                  FROM ProductionGreentireDisburseTable where Sch_Date <= ?
                  group by Sch_Date,Shift  order by Sch_Date desc,Shift desc
                  ",
              [
                $date_sch

              ]
            );
            $dateold = $getdate[2]["Sch_Date"];
            $shiftold = $getdate[2]["Shift"];
            $datenext = $getdateNext[0]["Sch_Date"];
            $shifnext = $getdateNext[0]["Shift"];
          } else {
            $getdate = $this->sql->rows(
              $this->conn->dbConnect(),
              "SELECT TOP 3 Sch_Date,Shift 
                  FROM ProductionGreentireDisburseTable where Sch_Date <= ?
                  group by Sch_Date,Shift  order by Sch_Date desc,Shift desc
                  ",
              [
                $date_sch

              ]
            );
            $getdateNext = $this->sql->rows(
              $this->conn->dbConnect(),
              "SELECT TOP 1 Sch_Date,Shift 
                  FROM ProductionGreentireDisburseTable where Sch_Date > ?
                  group by Sch_Date,Shift  order by Sch_Date asc,Shift asc
                  ",
              [
                $date_sch

              ]
            );
            $dateold = $getdate[1]["Sch_Date"];
            $shiftold = $getdate[1]["Shift"];
            $datenext = $getdateNext[0]["Sch_Date"];
            $shifnext = $getdateNext[0]["Shift"];
          }
        }
      }
      // return $datenext
      // exit();




      $updateReloadbuikdshc = \sqlsrv_query(
        $this->conn->dbConnect(),
        "UPDATE Q2

          SET	 
            Q2.Actual = Q1.Actual
            ,Q2.Target = Q1.Target
            ,Q2.Target1 = Q1.Target1
            ,Q2.BL = Q1.BL
          
          
          
          FROM   ProductionGreentireDisburseTable Q2
          
          LEFT JOIN
          (
            SELECT 
              PD.ItemId
              ,ISNULL(X.Target,0)AS Target
              ,ISNULL(X.Target1,0) AS Target1
              ,ISNULL(X.Actual,0) AS Actual
              ,ISNULL(X.BL,0) AS  BL
              FROM ProductionGreentireDisburseTable PD
              LEFT JOIN(
                  SELECT 
                    BB.ItemExt,
                        B1.Target,
                        B2.Target1,
                        B1.Actual,
                        B1.BL
                  FROM (
                      SELECT X.ItemId FROM(
                            SELECT ItemId FROM BuildSch where DateBuild = '$date_sch' and Shift = '$shift'
                            UNION ALL
                            SELECT ItemId FROM BuildSch where DateBuild = ? and Shift = ?)
                            X GROUP BY X.ItemId
                            )P
                            
                           LEFT JOIN ProductionSchEXTMaster BB ON BB.ItemGT = P.ItemId
                      LEFT JOIN(
                      SELECT BB.ItemExt AS ItemIdBom1,SUM(P2.Target) AS Target,
                      SUM(P2.Actual) AS Actual,
                      SUM(P2.BL) AS BL
                      FROM BuildSch P2
                      LEFT JOIN ProductionSchEXTMaster BB ON BB.ItemGT = P2.ItemId
                        WHERE P2.DateBuild = '$date_sch' AND P2.Shift = '$shift'
                          GROUP By BB.ItemExt
                      )B1 ON B1.ItemIdBom1 = BB.ItemExt
                      LEFT JOIN(
                        SELECT BB.ItemExt AS ItemIdBom2, SUM(P2.Target) AS Target1 FROM BuildSch P2
                          LEFT JOIN ProductionSchEXTMaster BB ON BB.ItemGT = P2.ItemId
                          WHERE P2.DateBuild = ? AND P2.Shift = ?
                          GROUP By BB.ItemExt
                      )B2 ON B2.ItemIdBom2 = BB.ItemExt
                        
                     GROUP BY
                      BB.ItemExt,
                      B1.Target,
                      B2.Target1,
                      B1.Actual,
                        B1.BL) X ON X.ItemExt = PD.ItemId
                           WHERE PD.Sch_date = '$date_sch'  and PD.Shift = '$shift')
                           Q1 ON Q1.ItemId = Q2.ItemId
                          
                          WHERE Q2.Sch_date = ? and Q2.Shift = ?",
        [
          $datenext,
          $shifnext,
          $datenext,
          $shifnext,
          $date_sch,
          $shift,


        ]
      );

      if (!$updateReloadbuikdshc) {
        sqlsrv_rollback($this->conn->dbConnect());
        return false;
      }

      sqlsrv_commit($this->conn->dbConnect());

      return true;
    } catch (Exception $e) {
      return false;
    }
  }
  public function updateSchtireTableStock($date_sch, $shift)
  {
    try {
      $getcheckdate = $this->sql->rows(
        $this->conn->dbConnect(),
        "SELECT  Sch_Date,Shift , COUNT(Sch_Date) OVER(PARTITION BY Sch_Date) AS CountRow
        FROM ProductionGreentireDisburseTable where Sch_Date = ?
        group by Sch_Date,Shift  order by Sch_Date desc,Shift desc
        ",
        [
          $date_sch
        ]
      );
      $datacheckCunt = $getcheckdate[0]["CountRow"];
      $shifcheck = $getcheckdate[0]["Shift"];


      if ($datacheckCunt == "" || $datacheckCunt == NULL) {

        $getdate = $this->sql->rows(
          $this->conn->dbConnect(),
          "SELECT TOP 1 Sch_Date,Shift 
              FROM ProductionGreentireDisburseTable where Sch_Date < ?
              group by Sch_Date,Shift  order by Sch_Date desc,Shift desc",
          [$date_sch]
        );
        $getdateNext = $this->sql->rows(
          $this->conn->dbConnect(),
          "SELECT TOP 1 Sch_Date,Shift 
              FROM ProductionGreentireDisburseTable where Sch_Date > ?
              group by Sch_Date,Shift  order by Sch_Date asc,Shift asc",
          [$date_sch]
        );
        $dateold = $getdate[0]["Sch_Date"];
        $shiftold = $getdate[0]["Shift"];
        $datenext = $getdateNext[0]["Sch_Date"];
        $shifnext = $getdateNext[0]["Shift"];
      } else {

        if ($datacheckCunt == 1) {
          if ($shift == 1) {
            $getdate = $this->sql->rows(
              $this->conn->dbConnect(),
              "SELECT TOP 1 Sch_Date,Shift 
              FROM ProductionGreentireDisburseTable where Sch_Date < ?
              group by Sch_Date,Shift  order by Sch_Date desc,Shift desc",
              [$date_sch]
            );
            if ($shifcheck == 1) {
              $getdateNext = $this->sql->rows(
                $this->conn->dbConnect(),
                "SELECT TOP 1 Sch_Date,Shift 
                      FROM ProductionGreentireDisburseTable where Sch_Date > ?
                      group by Sch_Date,Shift  order by Sch_Date asc,Shift asc",
                [$date_sch]
              );
            } else {
              $getdateNext = $this->sql->rows(
                $this->conn->dbConnect(),
                "SELECT TOP 1 Sch_Date,Shift 
                      FROM ProductionGreentireDisburseTable where Sch_Date >= ?
                      group by Sch_Date,Shift  order by Sch_Date asc,Shift asc",
                [$date_sch]
              );
            }
            $dateold = $getdate[0]["Sch_Date"];
            $shiftold = $getdate[0]["Shift"];
            $datenext = $getdateNext[0]["Sch_Date"];
            $shifnext = $getdateNext[0]["Shift"];
          } else {
            if ($shifcheck == 1) {
              $getdate = $this->sql->rows(
                $this->conn->dbConnect(),
                "SELECT TOP 1 Sch_Date,Shift 
                      FROM ProductionGreentireDisburseTable where Sch_Date <= ?
                      group by Sch_Date,Shift  order by Sch_Date desc,Shift desc",
                [$date_sch]
              );
              $dateold = $getdate[0]["Sch_Date"];
              $shiftold = $getdate[0]["Shift"];
            } else {
              $getdate = $this->sql->rows(
                $this->conn->dbConnect(),
                "SELECT TOP 2 Sch_Date,Shift 
                      FROM ProductionGreentireDisburseTable where Sch_Date <= ?
                      group by Sch_Date,Shift  order by Sch_Date desc,Shift desc",
                [$date_sch]
              );
              $dateold = $getdate[1]["Sch_Date"];
              $shiftold = $getdate[1]["Shift"];
            }
            $getdateNext = $this->sql->rows(
              $this->conn->dbConnect(),
              "SELECT TOP 1 Sch_Date,Shift 
                          FROM ProductionGreentireDisburseTable where Sch_Date > ?
                          group by Sch_Date,Shift  order by Sch_Date asc,Shift asc",
              [$date_sch]
            );
            $dateold = $dateold;
            $shiftold = $shiftold;
            $datenext = $getdateNext[0]["Sch_Date"];
            $shifnext = $getdateNext[0]["Shift"];
          }
        } else {
          if ($shift == 1) {
            $getdate = $this->sql->rows(
              $this->conn->dbConnect(),
              "SELECT TOP 3 Sch_Date,Shift 
                FROM ProductionGreentireDisburseTable where Sch_Date <= ?
                group by Sch_Date,Shift  order by Sch_Date desc,Shift desc
                ",
              [
                $date_sch

              ]
            );

            $getdateNext = $this->sql->rows(
              $this->conn->dbConnect(),
              "SELECT TOP 1 Sch_Date,Shift 
                FROM ProductionGreentireDisburseTable where Sch_Date <= ?
                group by Sch_Date,Shift  order by Sch_Date desc,Shift desc
                ",
              [
                $date_sch

              ]
            );
            $dateold = $getdate[2]["Sch_Date"];
            $shiftold = $getdate[2]["Shift"];
            $datenext = $getdateNext[0]["Sch_Date"];
            $shifnext = $getdateNext[0]["Shift"];
          } else {
            $getdate = $this->sql->rows(
              $this->conn->dbConnect(),
              "SELECT TOP 3 Sch_Date,Shift 
                FROM ProductionGreentireDisburseTable where Sch_Date <= ?
                group by Sch_Date,Shift  order by Sch_Date desc,Shift desc
                ",
              [
                $date_sch

              ]
            );
            $getdateNext = $this->sql->rows(
              $this->conn->dbConnect(),
              "SELECT TOP 1 Sch_Date,Shift 
                FROM ProductionGreentireDisburseTable where Sch_Date > ?
                group by Sch_Date,Shift  order by Sch_Date asc,Shift asc
                ",
              [
                $date_sch

              ]
            );
            $dateold = $getdate[1]["Sch_Date"];
            $shiftold = $getdate[1]["Shift"];
            $datenext = $getdateNext[0]["Sch_Date"];
            $shifnext = $getdateNext[0]["Shift"];
          }
        }
      }

      $updateStockInplan = \sqlsrv_query(
        $this->conn->dbConnect(),
        "UPDATE X

        SET X.Stock = Y.Stock
        
        FROM ProductionGreentireDisburseTable X
        
        LEFT JOIN (
        
        SELECT 
                T.ItemId,
                ((T.Stock2 + T.Total2) - (T.TireNotSpac2 +T.Produce2) )  + T.CalStock AS Stock
                FROM (
                SELECT 
                  P1.ItemId,
                  ISNULL(P2.Car1_1,0) + ISNULL(P2.Car1_2,0) + ISNULL(P2.Car1_3,0) + ISNULL(P2.Car1_4,0) +
                  ISNULL(P2.Car1_5,0) + ISNULL(P2.Car1_6,0)+ ISNULL(P2.Car1_7,0) + ISNULL(P2.Car1_8,0) AS Total2,
                  ISNULL(P2.Actual,0) AS Produce2,
                  ISNULL(P2.TireNotSpac,0) AS TireNotSpac2,
                  ISNULL(P2.Stock,0) AS Stock2,
                  ISNULL(P1.CalStock,0) AS CalStock
                   FROM ProductionGreentireDisburseTable  P1
                   LEFT JOIN ProductionGreentireDisburseTable P2 ON P1.ItemId = P2.ItemId 
                   AND P2.Sch_date = ? 
                   AND P2.Shift = ?
                   WHERE   P1.Sch_date = ? 
                   AND P1.Shift = ?)T) Y ON X.ItemId = Y.ItemId
                   WHERE X.Sch_date = ? AND X.Shift = ?",
        [
          $dateold,
          $shiftold,
          $date_sch,
          $shift,
          $date_sch,
          $shift
        ]
      );

      if (!$updateStockInplan) {
        sqlsrv_rollback($this->conn->dbConnect());
        return false;
      }

      sqlsrv_commit($this->conn->dbConnect());

      return true;
    } catch (Exception $e) {
      return false;
    }
  }

  public function updateSchfacetireTableStock($date_sch, $shift)
  {
    try {
      $date = date("Y-m-d H:i:s");
      // if ($shift == 1) {
      //   $dateref = date('Y-m-d', strtotime($date_sch . ' -1 days'));
      //   $shiftref = 2;
      // } else {
      //   $dateref = $date_sch;
      //   $shiftref = 1;
      // }
      $getcheckdate = $this->sql->rows(
        $this->conn->dbConnect(),
        "SELECT  Sch_Date,Shift , COUNT(Sch_Date) OVER(PARTITION BY Sch_Date) AS CountRow
        FROM ProductionGreentireFaceOfireTable where Sch_Date = ?
        group by Sch_Date,Shift  order by Sch_Date desc,Shift desc
        ",
        [
          $date_sch
        ]
      );
      $datacheckCunt = $getcheckdate[0]["CountRow"];
      $shifcheck = $getcheckdate[0]["Shift"];


      if ($datacheckCunt == "" || $datacheckCunt == NULL) {

        $getdate = $this->sql->rows(
          $this->conn->dbConnect(),
          "SELECT TOP 1 Sch_Date,Shift 
              FROM ProductionGreentireFaceOfireTable where Sch_Date < ?
              group by Sch_Date,Shift  order by Sch_Date desc,Shift desc",
          [$date_sch]
        );
        $getdateNext = $this->sql->rows(
          $this->conn->dbConnect(),
          "SELECT TOP 1 Sch_Date,Shift 
              FROM ProductionGreentireFaceOfireTable where Sch_Date > ?
              group by Sch_Date,Shift  order by Sch_Date asc,Shift asc",
          [$date_sch]
        );
        $dateold = $getdate[0]["Sch_Date"];
        $shiftold = $getdate[0]["Shift"];
        $datenext = $getdateNext[0]["Sch_Date"];
        $shifnext = $getdateNext[0]["Shift"];
      } else {

        if ($datacheckCunt == 1) {
          if ($shift == 1) {
            $getdate = $this->sql->rows(
              $this->conn->dbConnect(),
              "SELECT TOP 1 Sch_Date,Shift 
              FROM ProductionGreentireFaceOfireTable where Sch_Date < ?
              group by Sch_Date,Shift  order by Sch_Date desc,Shift desc",
              [$date_sch]
            );
            if ($shifcheck == 1) {
              $getdateNext = $this->sql->rows(
                $this->conn->dbConnect(),
                "SELECT TOP 1 Sch_Date,Shift 
                      FROM ProductionGreentireFaceOfireTable where Sch_Date > ?
                      group by Sch_Date,Shift  order by Sch_Date asc,Shift asc",
                [$date_sch]
              );
            } else {
              $getdateNext = $this->sql->rows(
                $this->conn->dbConnect(),
                "SELECT TOP 1 Sch_Date,Shift 
                      FROM ProductionGreentireFaceOfireTable where Sch_Date >= ?
                      group by Sch_Date,Shift  order by Sch_Date asc,Shift asc",
                [$date_sch]
              );
            }
            $dateold = $getdate[0]["Sch_Date"];
            $shiftold = $getdate[0]["Shift"];
            $datenext = $getdateNext[0]["Sch_Date"];
            $shifnext = $getdateNext[0]["Shift"];
          } else {
            if ($shifcheck == 1) {
              $getdate = $this->sql->rows(
                $this->conn->dbConnect(),
                "SELECT TOP 1 Sch_Date,Shift 
                      FROM ProductionGreentireFaceOfireTable where Sch_Date <= ?
                      group by Sch_Date,Shift  order by Sch_Date desc,Shift desc",
                [$date_sch]
              );
              $dateold = $getdate[0]["Sch_Date"];
              $shiftold = $getdate[0]["Shift"];
            } else {
              $getdate = $this->sql->rows(
                $this->conn->dbConnect(),
                "SELECT TOP 2 Sch_Date,Shift 
                      FROM ProductionGreentireFaceOfireTable where Sch_Date <= ?
                      group by Sch_Date,Shift  order by Sch_Date desc,Shift desc",
                [$date_sch]
              );
              $dateold = $getdate[1]["Sch_Date"];
              $shiftold = $getdate[1]["Shift"];
            }
            $getdateNext = $this->sql->rows(
              $this->conn->dbConnect(),
              "SELECT TOP 1 Sch_Date,Shift 
                          FROM ProductionGreentireFaceOfireTable where Sch_Date > ?
                          group by Sch_Date,Shift  order by Sch_Date asc,Shift asc",
              [$date_sch]
            );
            $dateold = $dateold;
            $shiftold = $shiftold;
            $datenext = $getdateNext[0]["Sch_Date"];
            $shifnext = $getdateNext[0]["Shift"];
          }
        } else {
          if ($shift == 1) {
            $getdate = $this->sql->rows(
              $this->conn->dbConnect(),
              "SELECT TOP 3 Sch_Date,Shift 
                FROM ProductionGreentireFaceOfireTable where Sch_Date <= ?
                group by Sch_Date,Shift  order by Sch_Date desc,Shift desc
                ",
              [
                $date_sch

              ]
            );

            $getdateNext = $this->sql->rows(
              $this->conn->dbConnect(),
              "SELECT TOP 1 Sch_Date,Shift 
                FROM ProductionGreentireFaceOfireTable where Sch_Date <= ?
                group by Sch_Date,Shift  order by Sch_Date desc,Shift desc
                ",
              [
                $date_sch

              ]
            );
            $dateold = $getdate[2]["Sch_Date"];
            $shiftold = $getdate[2]["Shift"];
            $datenext = $getdateNext[0]["Sch_Date"];
            $shifnext = $getdateNext[0]["Shift"];
          } else {
            $getdate = $this->sql->rows(
              $this->conn->dbConnect(),
              "SELECT TOP 3 Sch_Date,Shift 
                FROM ProductionGreentireFaceOfireTable where Sch_Date <= ?
                group by Sch_Date,Shift  order by Sch_Date desc,Shift desc
                ",
              [
                $date_sch

              ]
            );
            $getdateNext = $this->sql->rows(
              $this->conn->dbConnect(),
              "SELECT TOP 1 Sch_Date,Shift 
                FROM ProductionGreentireFaceOfireTable where Sch_Date > ?
                group by Sch_Date,Shift  order by Sch_Date asc,Shift asc
                ",
              [
                $date_sch

              ]
            );
            $dateold = $getdate[1]["Sch_Date"];
            $shiftold = $getdate[1]["Shift"];
            $datenext = $getdateNext[0]["Sch_Date"];
            $shifnext = $getdateNext[0]["Shift"];
          }
        }
      }


      $updateStockInplan = \sqlsrv_query(
        $this->conn->dbConnect(),
        "UPDATE X 
        SET X.Stock = Y.Stock
        FROM ProductionGreentireFaceOfireTable X
        LEFT JOIN(
          SELECT
        T.ItemId
        ,(T.Stock2+ T.TotalProduct) - (T.TotalPayOfCar + ISNULL(T.TireNotSpec,0))+ T.CalStock AS Stock
          --  T.Stock2 + T.CalStock AS Stock
            FROM
            (SELECT
              P.ItemId,
              ISNULL(P2.Stock,0) AS Stock2,
              (ISNULL(P2.NumberCar1,0) + ISNULL(P2.NumberCar2,0) +ISNULL(P2.NumberCar3,0)
              + ISNULL(P2.NumberCar4,0)
              + ISNULL(P2.NumberCar5,0) + ISNULL(P2.NumberCar6,0) + ISNULL(P2.NumberCar7,0)
              + ISNULL(P2.NumberCar8,0) ) AS TotalProduct,
              (ISNULL(P2.PayOfCar,0) + ISNULL(P2.PayOfCar2,0) + ISNULL(P2.PayOfCar3,0)
              + ISNULL(P2.PayOfCar4,0)
              +ISNULL(P2.PayOfCar5,0) + ISNULL(P2.PayOfCar6,0)+ ISNULL(P2.PayOfCar7,0)
              + ISNULL(P2.PayOfCar8,0)) AS TotalPayOfCar,
              ISNULL(P2.TireNotSpec,0) AS TireNotSpec,
              ISNULL(P.CalStock,0) AS CalStock
              FROM ProductionGreentireFaceOfireTable P
              LEFT JOIN ProductionGreentireFaceOfireTable P2 ON P2.ItemId = P.ItemId
              AND P2.Sch_Date = ? AND P2.Shift = ?
              WHERE P.Sch_Date = ? AND P.Shift = ?)T)Y
              ON X.ItemId = Y.ItemId 
              WHERE X.Sch_Date = ? AND X.Shift = ?",
        [
          $dateold,
          $shiftold,
          $date_sch,
          $shift,
          $date_sch,
          $shift
        ]
      );

      if (!$updateStockInplan) {
        sqlsrv_rollback($this->conn->dbConnect());
        return false;
      }

      sqlsrv_commit($this->conn->dbConnect());

      return true;
    } catch (Exception $e) {
      return false;
    }
  }
}
