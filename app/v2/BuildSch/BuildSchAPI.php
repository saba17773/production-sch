<?php

namespace App\V2\BuildSch;

use App\V2\Database\Connector;
use App\Common\Sql;
use Wattanar\Sqlsrv;

class BuildSchAPI
{
  private $conn = null;
  private $sql = null;

  public function __construct()
  {
    $this->conn = new Connector();
    $this->sql = new Sql();
  }
  public function addBuildSch($databuild)
  {
    try {

      	if (!isset($_SESSION["user_login"])) {
        	throw new \Exception("Session expired.");
      	}

      	foreach ($databuild as $value) {

	      $add = \sqlsrv_query(
	        $this->conn->dbConnect(),
	        "INSERT INTO BuildSch(
	          	ItemId
				,OrderWeek
				,NumberBL
				,BL
				,TargetTemp
				,Adjust
				,Target
				,Actual
				,Remark
				,OverLose
				,DateBuild
				,Shift
				,CreateBy
				,CreateDate
	        ) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
	        [
	          	$value['ItemId']
				,$value['OrderWeek']
				,$value['NumberBL']
				,$value['BL']
				,$value['TargetTemp']
				,$value['Adjust']
				,$value['Target']
				,$value['Actual']
				,$value['Remark']
				,$value['OverLose']
				,$value['DateBuild']
				,$value['Shift']
	          	,$_SESSION["user_login"]
	          	,date("Y-m-d H:i:s")
	        ]
	      );

  		}

	    if ($add) {
	    	return response(true, "Add success");
	    } else {
	        return response(false, "Add failed");
	    }
    } catch (\Exception $e) {
      return response(false, $e->getMessage());
    }
  }
  public function getBuildLists($date,$shift)
  {
    try {
      return $this->sql->rows(
        $this->conn->dbConnect(),
        "SELECT
		B.Id
		,B.DateBuild
		,SM.[Description] AS Shift
		,B.ItemId
		,MM.ItemGTName
		,MM.ColorAll
		-- ,MM.Color2
		-- ,MM.Color3
		-- ,MM.Color4
		-- ,MM.Color5
		,B.OrderWeek
		,B.NumberBL
		,B.BL
		,B.TargetTemp
		,B.Adjust
		,B.Target
		,B.Actual
		,B.Remark
		,B.OverLose
		,U.Name AS CreateBy
		,B.CreateDate
		FROM BuildSch B
		LEFT JOIN (
			SELECT M.ItemGT,M.ItemGTName,M.ColorAll
			FROM ProductionSchGreentireMaster M
			GROUP BY M.ItemGT,M.ItemGTName,M.ColorAll
		)MM ON B.ItemId = MM.ItemGT
		LEFT JOIN ShiftMaster SM ON B.Shift = SM.ID
		LEFT JOIN UserMaster U ON U.ID = B.CreateBy
        WHERE B.DateBuild = ? AND B.Shift = ?
        ORDER BY B.Id ASC",[$date,$shift]
      );
    } catch (\Exception $e) {
      return response(false, $e->getMessage());
    }
  }
  public function getGreentireList($date)
  {
    try {
      return $this->sql->rows(
        $this->conn->dbConnect(),
        "SELECT
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
	      GM.Color,
	      GM.Color2,
	      GM.Color3,
	      GM.Color4,
	      GM.Color5,
	      CAST(GM.[Weight] / 1000 AS NUMERIC(10, 3)) AS [Weight],
	      G.BomCPlan,
	      G.BomCActual,
	      G.BomDPlan,
	      G.BomDActual,
	      G.WeightPlan,
	      G.WeightActual
		  FROM TargetGreentire G
	      LEFT JOIN ProductionSchGreentireMaster GM
	      ON GM.ItemGT = G.ItemId
	      WHERE CONVERT(date, G.ShiftDate) =  ?
	      GROUP BY
	      G.Id,
	      G.ItemId,
	      GM.ItemGTName,
	      GM.PR,
	      GM.Pattern,
	      GM.TT,
	      GM.Color,
	      GM.Color2,
	      GM.Color3,
	      GM.Color4,
	      GM.Color5,
	      GM.[Weight],
	      G.BomCPlan,
	      G.BomCActual,
	      G.BomDPlan,
	      G.BomDActual,
	      G.WeightPlan,
	      G.WeightActual",[$date]
      );
    } catch (\Throwable $th) {
      throw $th;
    }
  }
  public function clearBuildSch($date,$shift)
  {
    try {

      	if (!isset($_SESSION["user_login"])) {
        	throw new \Exception("Session expired.");
      	}

      	$delete = \sqlsrv_query(
	        $this->conn->dbConnect(),
	        "DELETE FROM BuildSch WHERE DateBuild=? AND Shift=?",
	        [
	          	$date,$shift
	        ]
	    );

	    if ($delete) {
	    	return response(true, "Delete success");
	    } else {
	        return response(false, "Delete failed");
	    }
    } catch (\Exception $e) {
      return response(false, $e->getMessage());
    }
  }
  public function getBuildGroup($filter)
  {
    try {
      return $this->sql->rows(
        $this->conn->dbConnect(),
        "SELECT
		B.DateBuild
		,SM.[Description] AS Shift
		,'Building Scheduler' AS BuildName
		,U.Name AS CreateBy
		-- ,B.CreateDate
		FROM BuildSch B
		LEFT JOIN ShiftMaster SM ON B.Shift = SM.ID
		LEFT JOIN UserMaster U ON U.ID = B.CreateBy
		WHERE $filter
		GROUP BY
		B.DateBuild
		,SM.[Description]
		,U.Name
		-- ,B.CreateDate
		ORDER BY B.DateBuild DESC"
      );
    } catch (\Exception $e) {
      return response(false, $e->getMessage());
    }
  }
  public function importCheck($date,$shift)
  {
    try {
      $check = Sqlsrv::hasRows(
        $this->conn->dbConnect(),
        "SELECT * FROM BuildSch WHERE DateBuild=? AND Shift=?",[$date,$shift]
      );
      if ($check) {
	    	return response(true, "วัน กะ ดังกล่าวมีข้อมูลแล้ว(ซ้ำ)!");
	    } else {
	        return response(false, "ยังไม่มีข้อมูล วัน กะ ดังกล่าว!");
	    }
    } catch (\Exception $e) {
      return response(false, $e->getMessage());
    }
  }
}
