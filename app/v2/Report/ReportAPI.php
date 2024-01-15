<?php

namespace App\V2\Report;

use App\V2\Database\Connector;
use App\V2\Database\Handler;
use Wattanar\Sqlsrv;
use App\V2\Helper\Helper;

class ReportAPI
{


	public function loademployee($boiler, $date, $shift)
	{
		$db = new Connector;
		$conn = $db->dbConnect();


		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT E.FirstName +' '+ E.LastName [FullName],PT.Company
	        FROM ProductionSchEmployee PE
	        LEFT JOIN Employee E ON PE.EmployeeID=E.Code
	        LEFT JOIN ProductionSchTable PT ON PE.TransID=PT.ID
	        WHERE PE.BoilerID=? AND PE.SchDate=? AND PE.Shift=? AND PT.Company=?
	        GROUP BY E.FirstName,E.LastName,PT.Company",
			[$boiler, $date, $shift, $_SESSION["user_company"]]
		);

		$e = [];
		foreach ($query as $value) {
			array_push($e, $value['FullName']);
		}

		$istext = implode("<br>", $e);
		return $istext;
	}

	public function loadremark($id)
	{
		$db = new Connector;
		$conn = $db->dbConnect();

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT M.Description AS Remark
	        FROM ProductionSchProblem PM
	        LEFT JOIN ProductionSchProblemMaster M ON PM.ProblemID=M.ProblemID
	        WHERE PM.TransID IN ($id)"
		);

		$e = [];
		foreach ($query as $value) {
			array_push($e, $value['Remark']);
		}

		$istext = implode("<br>", $e);
		return $istext;
	}

	public function loadremark_byCure($curid, $date, $shift)
	{
		$db = new Connector;
		$conn = $db->dbConnect();

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT M.Description AS Remark
	        FROM ProductionSchProblem PM
	        LEFT JOIN ProductionSchProblemMaster M ON PM.ProblemID=M.ProblemID
	        WHERE PM.BoilerID = ?
	        AND CONVERT(varchar(7), PM.SchDate, 120) = ?
	        AND PM.Shift = ?
	        GROUP BY M.Description",
			[$curid, $date, $shift]
		);

		$e = [];
		foreach ($query as $value) {
			array_push($e, $value['Remark']);
		}

		$istext = implode("<br>", $e);
		return $istext;
	}

	public function reportSchPdf($date, $shift)
	{
		$db = new Connector;
		$conn = $db->dbConnect();

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT
				P.ID,
				P.Boiler,
				C.CureSize,
				P.MoldID,
				P.ItemID,
				-- I.ItemName,
				CASE
					WHEN I.ItemName IS NOT NULL THEN REPLACE(I.ItemName, 'EU', '')+' '+I.Brand+
						CASE
							WHEN CHARINDEX('EU',ItemName,1) != 0 THEN ' EU'
						ELSE '' END
				ELSE I.ItemName END AS ItemName,
				P.Time,
				CASE
				WHEN P.Target=0 THEN NULL
				ELSE P.Target END AS Target,
				CASE
				WHEN P.Actual1=0 THEN NULL
				ELSE P.Actual1 END AS Actual1,
				CASE
				WHEN P.Actual2=0 THEN NULL
				ELSE P.Actual2 END AS Actual2,
				CASE
				WHEN P.Actual=0 THEN NULL
				ELSE P.Actual END AS Actual,
				P.Weight,
			   (SELECT COUNT(*) FROM ProductionSchTable WHERE SchDate= P.SchDate AND Shift = P.Shift AND Boiler=P.Boiler AND Company=P.Company AND ItemID IS NOT NULL AND P.ItemID != '' ) AS rowspan,
			   P.ShiftFor
			FROM ProductionSchTable P

			LEFT JOIN ProductionSchItemMaster I ON P.ItemID = I.ID
			LEFT JOIN ProductionSchCure C ON P.Boiler = C.CurID AND P.Company = C.Company
			WHERE P.SchDate = ?
			AND P.Shift = ?
			AND P.ItemID IS NOT NULL AND P.ItemID != ''
			AND P.Company = ?
			ORDER BY C.ID,P.MoldID ASC , P.ItemID DESC",
			[$date, $shift, $_SESSION["user_company"]]
		);

		return $query;
	}

	public function reportSchCuring($date, $shift)
	{
		$db = new Connector;
		$conn = $db->dbConnect();

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT
		        P.Boiler,
			    C.CurID,
			    C.CureSize,
			    SUM(P.Time) AS Time,
			    SUM(P.Target) AS Target,
			    SUM(P.Actual) AS Actual,
			    SUM(P.Weight) AS Weight

	        FROM ProductionSchTable P

	        LEFT JOIN ProductionSchItemMaster I ON P.ItemID = I.ID
	        LEFT JOIN ProductionSchCure C ON P.Boiler = C.CurID
	        WHERE CONVERT(varchar(7), P.SchDate, 120) = ?
	        AND P.Shift = ?
	        AND P.ItemID IS NOT NULL AND P.ItemID != ''
	        AND P.Company = ?
	        GROUP BY
				P.Boiler,
			    C.CurID,
			    C.CureSize
			ORDER BY CAST (SUBSTRING(CAST(P.Boiler AS VARCHAR(6)),2,3) AS INT) DESC",
			[$date, $shift, $_SESSION["user_company"]]
		);

		return $query;
	}

	public function countBoiler($date, $shift)
	{
		$db = new Connector;
		$conn = $db->dbConnect();
		$date = date('Y-m-d', strtotime($date));

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT COUNT(Boiler) AS CountBoiler
			FROM (
				SELECT
				Boiler
				FROM ProductionSchTable
				WHERE SchDate=?
				AND Shift=?
				AND Company=?
				AND ItemID IS NOT NULL
				AND Target > 0
				GROUP BY Boiler
			)B",
			[$date, $shift, $_SESSION['user_company']]
		);
		return $query[0]['CountBoiler'];
	}

	public function countMold($date, $shift)
	{
		$db = new Connector;
		$conn = $db->dbConnect();
		$date = date('Y-m-d', strtotime($date));

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT COUNT(MoldID) AS Mold FROM ProductionSchTable
			WHERE SchDate=?
			AND Shift=?
			AND Company=?
			AND ItemID IS NOT NULL
			AND Target > 0
			AND MoldID IN ('A1','B1')",
			[$date, $shift, $_SESSION['user_company']]
		);
		return $query[0]['Mold'];
	}

	public function reportSchSummary($date)
	{
		$db = new Connector;
		$conn = $db->dbConnect();

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT
	        	ItemID,
				ItemName,
				Time,
				SUM(Target) AS Target,
				CASE
				WHEN SUM(Actual) IS NULL THEN 0
				ELSE SUM(Actual) END AS Actual,
				CASE
				WHEN SUM(Scrap) IS NULL THEN 0
				ELSE SUM(Scrap) END AS Scrap,
				CASE
				WHEN SUM(Actual) > 0 THEN (Weight/1000)
				ELSE 0 END  AS Weight,
				(Weight/1000) AS WeightDefault,
				Shift
			FROM
			(

				SELECT
					P.ID,
					P.ItemID,
					-- I.ItemName,
					CASE
						WHEN I.ItemName IS NOT NULL THEN REPLACE(I.ItemName, 'EU', '')+' '+I.Brand+
							CASE
								WHEN CHARINDEX('EU',ItemName,1) != 0 THEN ' EU'
							ELSE '' END
					ELSE I.ItemName END AS ItemName,
					SUM(P.Time) [Time],
					SUM(P.Target) [Target],
					SUM(P.Actual) [Actual],
					SUM(P.Scrap) [Scrap],
					P.Weight [Weight],
					P.Shift,
					P.MoldID
				FROM ProductionSchTable P
				LEFT JOIN ProductionSchItemMaster I ON P.ItemID = I.ID
				WHERE P.SchDate = ?
				AND P.Shift = 1
				AND P.ItemID IS NOT NULL AND P.ItemID != ''
				AND P.Company = ?
				GROUP BY P.ID,P.ItemID,I.ItemName,I.Brand,P.Shift,P.MoldID,P.Weight

				UNION

				SELECT
					P.ID,
					P.ItemID,
					-- I.ItemName,
					CASE
						WHEN I.ItemName IS NOT NULL THEN REPLACE(I.ItemName, 'EU', '')+' '+I.Brand+
							CASE
								WHEN CHARINDEX('EU',ItemName,1) != 0 THEN ' EU'
							ELSE '' END
					ELSE I.ItemName END AS ItemName,
					SUM(P.Time) [Time],
					SUM(P.Target) [Target],
					SUM(P.Actual) [Actual],
					SUM(P.Scrap) [Scrap],
					P.Weight [Weight],
					P.Shift,
					P.MoldID
				FROM ProductionSchTable P
				LEFT JOIN ProductionSchItemMaster I ON P.ItemID = I.ID
				WHERE P.SchDate = ?
				AND P.Shift = 2
				AND P.ItemID IS NOT NULL AND P.ItemID != ''
				AND P.Company = ?
				GROUP BY P.ID,P.ItemID,I.ItemName,I.Brand,P.Shift,P.MoldID,P.Weight
			)Z
			GROUP BY
			Z.ItemID,
			Z.ItemName,
			Z.Time,
			Z.Shift,
			Z.Weight
			ORDER BY ItemID,Shift ASC",
			[$date, $_SESSION["user_company"], $date, $_SESSION["user_company"]]
		);

		return $query;
	}

	public function reportSchWeight($date)
	{
		$db = new Connector;
		$conn = $db->dbConnect();

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT
				P.SchDate
				,P.Shift
			    ,P.ItemID
			    -- ,I.ItemName
			    ,CASE
					WHEN I.ItemName IS NOT NULL THEN REPLACE(I.ItemName, 'EU', '')+' '+I.Brand+
						CASE
							WHEN CHARINDEX('EU',ItemName,1) != 0 THEN ' EU'
						ELSE '' END
				ELSE I.ItemName END AS ItemName
			    ,I.TT
			    ,I.Pattern
			    ,SUM(P.Target) AS Target
			    ,SUM(P.Actual) AS Actual
			    ,SUM(P.Weight/10000) AS Weight
			    ,SUM(I.NetWeight/10000) AS WeightTarget
			    -- ,SUM(P.Weight)*SUM(P.Actual) AS Weight
			    -- ,SUM(I.NetWeight)*SUM(P.Target) AS WeightTarget
			    ,I.Color1
				,I.Color2
				,I.Color3
				,I.Color4
				,I.Color5

			FROM ProductionSchTable P
			LEFT JOIN ProductionSchItemMaster I ON P.ItemID = I.ID
			WHERE CONVERT(varchar(7), P.SchDate, 120) = ?
			AND P.ItemID IS NOT NULL AND P.ItemID != ''
			AND P.Company = ?
			GROUP BY
				P.SchDate
				,P.Shift
			    ,P.ItemID
			    ,I.ItemName
			    ,I.Brand
			    ,I.TT
			    ,I.Pattern
			    ,I.Color1
				,I.Color2
				,I.Color3
				,I.Color4
				,I.Color5
			ORDER BY P.ItemID,P.SchDate",
			[$date, $_SESSION["user_company"]]
		);

		return $query;
	}

	public function reportReceiveGreentirePdf($date, $shift)
	{
		$db = new Connector;
		$conn = $db->dbConnect();

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT
		        P.ItemID,
		        -- I.ItemName,
		        CASE
					WHEN I.ItemName IS NOT NULL THEN REPLACE(I.ItemName, 'EU', '')+' '+I.Brand+
						CASE
							WHEN CHARINDEX('EU',ItemName,1) != 0 THEN ' EU'
						ELSE '' END
				ELSE I.ItemName END AS ItemName,
		        I.PR,
		        I.TT,
		        I.Pattern,
		        I.Brand,
		        I.Color1,
		        I.Color2,
		        I.Color3,
		        I.Color4,
		        I.Color5
	        FROM ProductionSchTable P

	        LEFT JOIN ProductionSchItemMaster I ON P.ItemID = I.ID

	        WHERE P.SchDate = ?
	        AND P.Shift = ?
	        AND P.ItemID IS NOT NULL AND P.ItemID != ''
	        AND P.Company = ?

	        GROUP BY
				P.ItemID,
		        I.ItemName,
		        I.Brand,
		        I.PR,
		        I.TT,
		        I.Pattern,
		        I.Brand,
		        I.Color1,
		        I.Color2,
		        I.Color3,
		        I.Color4,
		        I.Color5",
			[$date, $shift, $_SESSION["user_company"]]
		);

		return $query;
	}

	public function countItemExist($date, $shift, $boiler)
	{
		$db = new Connector;
		$conn = $db->dbConnect();

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT count(*) AS Rows FROM ProductionSchTable
	        WHERE SchDate = ?
	        AND Shift = ?
	        AND Boiler = ?
	        AND Company = ?
	       AND ItemID IS NOT NULL
	        AND ItemID != ''",
			[$date, $shift, $boiler, $_SESSION["user_company"]]
		);

		return $query[0]['Rows'];
	}

	public function countMoldExist($date, $shift, $boiler)
	{
		$db = new Connector;
		$conn = $db->dbConnect();

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT TOP 1 SUBSTRING(MoldID, 1, 1) AS MoldID FROM ProductionSchTable
	        WHERE SchDate = ?
	        AND Shift = ?
	        AND Boiler = ?
	        AND Company = ?
	        AND ItemID IS NOT NULL
	        AND ItemID != ''
	        GROUP BY MoldID",
			[$date, $shift, $boiler, $_SESSION["user_company"]]
		);

		return $query[0]['MoldID'];
	}

	public function getBoilerbyDate($date, $shift)
	{
		$db = new Connector;
		$conn = $db->dbConnect();

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT
				P.Boiler,C.CureSize
			FROM ProductionSchTable P
			LEFT JOIN ProductionSchCure C ON P.Boiler = C.CurID AND P.Company = C.Company
			WHERE P.SchDate = ?
			AND P.Shift = ?
			AND P.Company = ?
			GROUP BY P.Boiler,C.CureSize",
			[$date, $shift, $_SESSION["user_company"]]
		);

		return $query;
	}

	public function loadremark_byItem($date, $shift, $item)
	{
		$db = new Connector;
		$conn = $db->dbConnect();

		$data = Sqlsrv::queryArray(
			$conn,
			"SELECT ID FROM ProductionSchTable
			WHERE SchDate = ?
			AND Shift = ?
			AND ItemID = ?",
			[$date, $shift, $item]
		);

		$id_list = [];
		foreach ($data as $v) {
			array_push($id_list, $v['ID']);
		}

		$id = implode(",", $id_list);

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT M.Description AS Remark
	        FROM ProductionSchProblem PM
	        LEFT JOIN ProductionSchProblemMaster M ON PM.ProblemID=M.ProblemID
	        WHERE PM.TransID IN ($id)"
		);

		if (count($query) > 0) {
			$e = [];
			$r = '';
			foreach ($query as $value) {
				if ($r != $value['Remark']) {
					array_push($e, $value['Remark']);
				}
				$r = $value['Remark'];
			}

			$istext = implode("<br>", $e);
			return $istext;
		} else {
			return " ";
		}
	}
	public function reportgatgetReceiveGreentirePdf($date_sch, $shift)
	{
		$db = new Connector;
		// code
		$sqlId = "1=1";

		// if ($id !== null) {
		// 	$sqlId = "P.Id = $id";
		// }
		if ($shift == 1) {
			$dateref = date('Y-m-d', strtotime($date_sch . ' -1 days'));
			$shiftref = 2;
		} else {
			$dateref = $date_sch;
			$shiftref = 1;
		}
		$conn = $db->dbConnect();

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT
            P.Id,
            P.ItemId,
            I.ItemGTName,
            I.Color,
            ISNULL(PPT.SpareOfcure, 0 ) AS SpareOfcure,
            ISNULL(P.StockInplan, 0 ) AS StockInplan ,
            ISNULL(PPT.SpareOfcure, 0 ) + ISNULL(GT.StockInplan, 0 ) AS TOTAL,
            P.shift,
            P.CountIn,
            P.CountOut,
            P.CountNotSpec,
            P.CountReal,
            PPT.CountCure,
            P.CountShift,
            P.CountPlan,
             (ISNULL(P.StockInplan, 0 ) +ISNULL(P.CountIn, 0 ) ) - (ISNULL(P.CountNotSpec, 0 )+ ISNULL(P.CountOut, 0 )) AS TotalSockGT,
			--P.StockInplan AS TotalSockGT,
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
             ORDER BY P.ItemId ASC
             ",
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
	}

	public function reportgatgetprintGreentirePdf($date_sch, $shift)
	{
		$db = new Connector;
		$conn = $db->dbConnect();
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

		$getdata = Sqlsrv::queryArray(
			$conn,
			"SELECT TOP 1 Sch_datein , ShiftIn
				FROM ProductionGreentirePrintTable
				WHERE Sch_date = ? AND Shift = ?",
			[
				$date_sch,
				$shift
			]
		);

		$datein = $getdata[0]["Sch_datein"];
		$shiftin = $getdata[0]["ShiftIn"];

		if ($shiftin == 1) {
			$datein2 = $datein;
			$shiftin2 = 2;
		} else {
			$datein2 = date('Y-m-d', strtotime($datein . ' +1 days'));
			$shiftin2 = 1;
		}

		$query = Sqlsrv::queryArray(
			$conn,
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
	          (T.Actual + T.TargetTemp + T.StockInplan)-T.GreentireDay AS TireLackDay
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
	    		        LEFT JOIN ShareMoldMaster SMM ON SMM.ItemId = PGT.ItemId
	                WHERE PGT.Sch_date = ? AND PGT.Shift = ?

	               )T
	               ORDER BY T.ItemId ASC",
			[
				$date_sch,
				$shift,
				$daterecive,
				$shiftref,
				$datein2,
				$shiftin2,
				$datein,
				$shiftin,
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
	}

	public function reportgatgetsplitGreentirePdf($date, $shift)
	{
		$db = new Connector;
		$conn = $db->dbConnect();
		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT T.*,
			SUM( T.Countprintcure)OVER(PARTITION BY T.TYPE)  TT,
			SUM( T.CureDay)OVER(PARTITION BY T.TYPE)  TCD
			FROM
			(
				SELECT
				M.GroupDesc Type,
				M.DetailDesc Type2,
				M.Size,
				CASE WHEN D.Countprintcure IS NULL THEN 0 ELSE D.Countprintcure END AS Countprintcure,
				CASE WHEN D.CureDay IS NULL THEN 0 ELSE D.CureDay END AS CureDay,
				CASE WHEN D.CountPrint IS NULL THEN 0 ELSE D.CountPrint END AS CountPrint,
				CASE WHEN D.GreentireDay IS NULL THEN 0 ELSE D.GreentireDay END AS GreentireDay,
				M.ID,M.Sortby,M.DetailID,M.GroupID,M.Sortby_D
				FROM
				(
					SELECT G.GroupID,G.GroupDesc,G.Sortby,
					D.ID,D.DetailID,D.DetailDesc,D.Size,D.Sortby AS Sortby_D
					FROM TypeTireGroupMaster G
					JOIN TypeTireDetailMaster D ON G.GroupID = D.GroupID
				)M
				LEFT OUTER JOIN
				(
					SELECT
					PM.Size,
					PM.TypeTires,
					PM.TypeTiresByRim,
					sum(PT.Countprintcure + PT.CountPrintcurFG) AS Countprintcure,
					sum(((PT.Countprintcure * PT.Rateprint) + (PT.CountPrintcurFG * PT.RatePrintFG)) * 2) AS CureDay,
					sum(PT.Countprintcure + PT.CountPrintcurFG) AS CountPrint,
					sum(((PT.Countprintcure * PT.Rateprint) + (PT.CountPrintcurFG * PT.RatePrintFG)) * 2) AS GreentireDay
					FROM ProductionGreentirePrintTable  PT
					LEFT JOIN
					(	SELECT
						ItemGT
						,Size
						,TypeTires
						,TypeTiresByRim
						FROM ProductionSchGreentireMaster
						GROUP BY
						ItemGT
						,Size
						,TypeTires
						,TypeTiresByRim
					)PM ON PM.ItemGT = PT.ItemId
					WHERE Sch_date = ? AND Shift = ?
					GROUP BY PM.Size, PM.TypeTires, PM.TypeTiresByRim
				)D
				ON D.TypeTiresByRim = M.DetailDesc
			)T
			GROUP BY T.Type,
				T.Type2,
				T.Size,
				T.Countprintcure,
				T.CureDay,
				T.CountPrint,
				T.GreentireDay,
				T.ID,T.Sortby,T.DetailID,
				T.GroupID,T.Sortby_D
			ORDER BY T.Sortby,T.Sortby_D
			",
			[
				$date,
				$shift,
			]
		);

		return $query;
	}

	public function countrowsplit($Type)
	{
		$db = new Connector;
		$conn = $db->dbConnect();
		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT COUNT(D.DetailID) Countrows
			FROM TypeTireGroupMaster G JOIN
			TypeTireDetailMaster D
			ON G.GroupID = D.GroupID
			WHERE G.GroupDesc = ?",
			[
				$Type
			]
		);
		return $query[0]['Countrows'];
	}

	public function TypeAll()
	{
		$db = new Connector;
		$conn = $db->dbConnect();
		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT GroupDesc Type
		FROM TypeTireGroupMaster
		ORDER BY Sortby
		"
		);
		return $query;
	}
	public function reportdisbursementtirePdf($date_sch, $shift, $dateold, $shiftold, $datenext, $shifnext)
	{
		$db = new Connector;
		$conn = $db->dbConnect();
		$sqlId = "1=1";

		if ($id !== null) {
			$sqlId = "P.Id = $id";
		}
		if ($shift == 1) {
			$dateref = $date_sch;
			$datelast = date('Y-m-d', strtotime($date_sch . ' -1 days'));
			//  $dateNext = $date_sch;
			$shiftref = 2;
		} else {
			$dateref = date('Y-m-d', strtotime($date_sch . ' +1 days'));
			$datelast = $date_sch;
			$shiftref = 1;
		}


		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT T.Id,
			 T.ItemId,
			 T.ItemGTName,
			 T.Color,
			 T.Target,
			 T.Target1,
			 T.Actual,
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
					WHEN T.Total = (ISNULL(FT.PayOfCar,0) + ISNULL(FT.PayOfCar2,0) + ISNULL(FT.PayOfCar3,0)
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
					ISNULL(P.Target1,0) AS Target1,
					P.Actual,
					ISNULL(P.Stock,0) AS Stock,
					ISNULL(P.Car1_1,0) + ISNULL(P.Car1_2,0) + ISNULL(P.Car1_3,0) + ISNULL(P.Car1_4,0) +
					ISNULL(P.Car1_5,0) + ISNULL(P.Car1_6,0)+ ISNULL(P.Car1_7,0) + ISNULL(P.Car1_8,0) AS Total,
					ISNULL(P2.Car1_1,0) + ISNULL(P2.Car1_2,0) + ISNULL(P2.Car1_3,0) + ISNULL(P2.Car1_4,0) +
					ISNULL(P2.Car1_5,0) + ISNULL(P2.Car1_6,0)+ ISNULL(P2.Car1_7,0) + ISNULL(P2.Car1_8,0) AS Total2,
					ISNULL(P.TireNotSpac,0) AS TireNotSpac,
					ISNULL(P.Actual,0) AS Produce,
					P.BL,
					ISNULL(P2.TireNotSpac,0) AS TireNotSpac2,
					ISNULL(P.Car2_1,0) + ISNULL(P.Car2_2,0) + ISNULL(P.Car2_3,0) + ISNULL(P.Car2_4,0) +
					ISNULL(P.Car2_5,0) + ISNULL(P.Car2_6,0)+ ISNULL(P.Car2_7,0) + ISNULL(P.Car2_8,0) AS CountNum,
					P.Car1_1,P.Car1_2,P.Car1_3,P.Car1_4,P.Car1_5,P.Car1_6,P.Car1_7,P.Car1_8,
					P.Car2_1,P.Car2_2,P.Car2_3,P.Car2_4,P.Car2_5,P.Car2_6,P.Car2_7,P.Car2_8,
					P.CarNumber1_1,P.CarNumber1_2,P.CarNumber1_3,P.CarNumber1_4,P.CarNumber1_5,P.CarNumber1_6,P.CarNumber1_7,P.CarNumber1_8,
					P.CarNumber2_1,P.CarNumber2_2,P.CarNumber2_3,P.CarNumber2_4,P.CarNumber2_5,P.CarNumber2_6,P.CarNumber2_7,P.CarNumber2_8
					FROM ProductionGreentireDisburseTable P
						--LEFT JOIN (
				 --     SELECT
						--     PP.ItemGTName,
				 --        PP.ItemGT,
				 --        PP.ColorAll As Color
				 --        FROM ProductionSchGreentireMaster PP
						 --     GROUP BY PP.ItemGTName,PP.ItemGT, PP.ColorAll
				 -- )I ON I.ItemGT = P.ItemId

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
	}

	public function reportgreentirefacetirePdf($date_sch, $shift, $dateref, $shiftref, $dateNext, $shiftNext)
	{
		$db = new Connector;
		$conn = $db->dbConnect();
		// if ($shift == 1) {
		// 	$dateref = date('Y-m-d', strtotime($date_sch . ' -1 days'));
		// 	$dateNext = $date_sch;
		// 	$shiftref = 2;
		// } else {
		// 	$dateref = $date_sch;
		// 	$dateNext = date('Y-m-d', strtotime($date_sch . ' +1 days'));
		// 	$shiftref = 1;
		// }
		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT
        T.Id,
        T.ItemId,
  	    T.ItemGTName,
  	    T.Color ,
  	    T.Stock2,
  	    T.TotalProduct,
  	    T.TotalPayOfCar,
  	    T.TireNotSpec,
  	    -- (T.Stock2 + T.TotalProduct) - (T.TotalPayOfCar + ISNULL(T.TireNotSpec,0)) 
        -- AS Total,
		(T.Stock+ T.TotalProduct) - (T.TotalPayOfCar + ISNULL(T.TireNotSpec,0))
        AS Total,
  	    T.StockTire,
  	    ISNULL(T.StockTire,0) - ((T.Stock2 + T.TotalProduct) -
				(T.TotalPayOfCar + ISNULL(T.TireNotSpec,0))) AS CompareNum,
  	    CASE
  		    WHEN (T.Stock2 + T.TotalProduct)- ISNULL(TireNotSpec,0) < TotalPayOfCar
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
  		      ISNULL(P.Stock,0) AS Stock2,
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
	  			   LEFT JOIN [FREY\LIVE].[DSL_AX40_SP1_LIVE].[dbo].[DSG_ColorSizeTypeTires] C
						 ON C.ITEMID = P.ItemId
            LEFT JOIN ProductionGreentireFaceOfireTable P2 ON P2.ItemId = P.ItemId
             AND P2.Sch_Date = ? AND P2.Shift = ?
            LEFT JOIN ProductionGreentireDisburseTable D ON D.ItemId = P.ItemId
             AND D.Sch_date = P.Sch_DateIn AND D.Shift = P.ShiftIn
            WHERE P.Sch_Date = ? AND P.Shift = ?
        )T ORDER BY T.ItemId ASC",
			[
				$dateref,
				$shiftref,
				$date_sch,
				$shift
			]
		);

		return $query;
	}

	public function reportgatgetplantirePdf($date_sch, $shift)
	{
		$db = new Connector;
		$conn = $db->dbConnect();
		if ($shift == 1) {
			$dateref = date('Y-m-d', strtotime($date_sch . ' -1 days'));
			$shiftref = 2;
		} else {
			$dateref = $date_sch;
			$shiftref = 1;
		}

		$getdate = Sqlsrv::queryArray(
			$conn,
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
			[$date_sch, $date_sch]
		);
		$date2 = $getdate[0]["DateBuild"];
		$date3 = $getdate[1]["DateBuild"];

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT X.*,
			CAST(X.checktotal2 AS INT) AS checktotal
			FROM
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
				  END AS checktotal2,
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
	}
	public function reportgatgetplantirePdfGroup2($date_sch, $shift)
	{
		$db = new Connector;
		$conn = $db->dbConnect();
		if ($shift == 1) {
			$dateref = date('Y-m-d', strtotime($date_sch . ' -1 days'));
			$shiftref = 2;
		} else {
			$dateref = $date_sch;
			$shiftref = 1;
		}

		$getdate = Sqlsrv::queryArray(
			$conn,
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
			[$date_sch, $date_sch]
		);
		$date2 = $getdate[0]["DateBuild"];
		$date3 = $getdate[1]["DateBuild"];

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT X.*,
				CAST(X.checktotal2 AS INT) AS checktotal
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
				  END AS checktotal2,
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
	}
	public function reportgatgetplantirePdfGroupall($date_sch, $shift)
	{
		$db = new Connector;
		$conn = $db->dbConnect();
		if ($shift == 3) {
			$shift = 1;
			$dateref = date('Y-m-d', strtotime($date_sch . ' -1 days'));
			$shiftref = 2;
		} else {
			$shift = 2;
			$dateref = $date_sch;
			$shiftref = 1;
		}

		$getdate = Sqlsrv::queryArray(
			$conn,
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
			[$date_sch, $date_sch]
		);
		$date2 = $getdate[0]["DateBuild"];
		$date3 = $getdate[1]["DateBuild"];

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT QQ.*,
			CASE WHEN QQ.TEST = 1 THEN CAST(QQ.checktotal2 AS INT)  
			ELSE CASE WHEN $shift = 1 
				THEN  CASE WHEN QQ.ActualDay1C = 0 
				THEN 0 ELSE  
				CAST(QQ.checktotal2 AS INT) END
			ELSE CASE  WHEN QQ.ActualDay1D = 0 THEN 0 ELSE  CAST(QQ.checktotal2 AS INT) END END END AS checktotal FROM(
				SELECT DB.ItemId,
			   DB.Name,
			   DB.OrderLackshift,
			   DB.Total,
			   DB.TotalSystemPD,
			   DB.GrandTotal,
			   DB.ActualDay1C,
			   DB.ActualDay1D,
			   DB.ActualDay2C,
			   DB.ActualDay2D,
			   DB.ActualDay3C,
			   DB.ActualDay3D,
			   DB.ShiftDay1C,
			   DB.ShiftDay1D,
			   DB.ShiftDay2C,
			   DB.ShiftDay2D,
			   DB.ShiftDay3C,
			   DB.ShiftDay3D,
			   DB.BL,
			   DB.StockStatus,
			   DB.TEST,
			   DB.DSG_COLOR,
			   DB.ITEMNAME_LIST,
	   ROUND(ISNULL(NULLIF(DB.OrderLackshift,0) / NULLIF(DB.check2,0),0),2) AS check3,
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
			 END AS checktotal2,
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
				 WHERE Sch_date =? AND Shift = ?
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
	   
		 
		 UNION ALL
		 
   
		SELECT DB.ItemId,
			   DB.Name,
			  
			   DB.OrderLackshift,
			   DB.Total,
			   DB.TotalSystemPD,
			   DB.GrandTotal,
			   DB.ActualDay1C,
			   DB.ActualDay1D,
			   DB.ActualDay2C,
			   DB.ActualDay2D,
			   DB.ActualDay3C,
			   DB.ActualDay3D,
			   DB.ShiftDay1C,
			   DB.ShiftDay1D,
			   DB.ShiftDay2C,
			   DB.ShiftDay2D,
			   DB.ShiftDay3C,
			   DB.ShiftDay3D,
			   DB.BL,
			   DB.StockStatus,
			   DB.TEST,
			   DB.DSG_COLOR,
			   DB.ITEMNAME_LIST,
	 ROUND(ISNULL(NULLIF(DB.OrderLackshift,0) / NULLIF(DB.check2,0),0),2) AS check3,
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
			 END AS checktotal2,
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
	   --CASE
	   --  WHEN  $shift = '1'
	   --		THEN CASE
	   --			WHEN T.Total - T.ActualDay1C > 0
	   --			THEN T.Total - T.ActualDay1C
	   --			ELSE 0 END
	   --ELSE  CASE
	   --			WHEN T.Total - T.ActualDay1D > 0
	   --			THEN
	   --			T.Total - T.ActualDay1D
	   --			ELSE 0 END
	   --END AS check4
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
					 ) QQ  WHERE QQ. checkzero = 1 ORDER BY QQ.ItemId ASC",
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
				$shiftref,
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
	}
	public function getdateplantire($date_sch)
	{
		$db = new Connector;
		$conn = $db->dbConnect();
		$getdate = Sqlsrv::queryArray(
			$conn,
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
			[$date_sch, $date_sch]
		);
		return $getdate;
	}

	public function reportSchbillbuy($date)
	{
		$db = new Connector;
		$conn = $db->dbConnect();

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT 
				A.ItemID,
				A.ItemName,
				A.NameTH,
				SUM(A.BillGive) AS BillGive,
				SUM(A.BillUse) AS  BillUse,
				SUM(A.faceBoiler) AS faceBoiler,
				A.Shift FROM(
					SELECT
					P.ID,
					P.ItemID,
					CASE
						WHEN I.ItemName IS NOT NULL THEN REPLACE(I.ItemName, 'EU', '')+' '+I.Brand+
					CASE
						WHEN CHARINDEX('EU',ItemName,1) != 0 THEN ' EU'
					ELSE '' END
					ELSE I.ItemName END AS ItemName,
					I.ItemNameThai[NameTH],
					P.BillUse,
					P.BillGive,
					P.faceBoiler,
					P.Shift
					FROM ProductionSchTable P
					LEFT JOIN Employee E ON P.Employee=E.Code
					LEFT JOIN ProductionSchItemMaster I ON P.ItemID=I.ID
					LEFT JOIN ProductionSchCure C ON P.Boiler=C.CurID
					WHERE P.SchDate= ?  AND P.Company= ? AND P.ItemID IS NOT NULL)A
					GROUP BY A.ItemID,A.ItemName,A.NameTH,A.Shift
					ORDER BY A.ItemID ASC",
			[
				$date,
				$_SESSION["user_company"]
			]
		);

		return $query;
	}
	public function reportSchOrder($date, $shift)
	{
		$db = new Connector;
		$conn = $db->dbConnect();
		$date = date('Y-m-d', strtotime($date));
		if ($shift == 1) {
			$dateNext = $date;
			$shiftNext = 2;
			$datepass = date('Y-m-d', strtotime($date . ' -1 days'));
			$shiftpass = 2;
		} else {
			$dateNext = date('Y-m-d', strtotime($date . ' +1 days'));
			$shiftNext = 1;
			$datepass = $date;
			$shiftpass = 1;
		}
		//	return $datepass;
		$query = Sqlsrv::queryArray(
			$conn,
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
				$date,
				$shift,
				$date,
				$date,
				$shift,
				$datepass,
				$shiftpass,
				$dateNext,
				$shiftNext,
				$date,
				$shift


			]
		);

		return $query;
	}

	public function reportSchSummaryMonth($Year, $Month, $firstday)
	{
		$db = new Connector;

		$conn = $db->dbConnect();

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT * 
				,(T2.SpareOfcure + T2.StockInplan) + T2.CountIn AS TotalGreentire --รวมgreentire ทั้งหมด
				,(T2.StockInplan + T2.CountIn + 0) - ( T2.CountOut + 0) AS AmountInplan  -- คงเหลือในแผนก
				,(T2.Bom + T2.SpareOfcure) - T2.CountCure AS CalCure -- คำนวณหน้าเตา
				,((T2.StockInplan + T2.CountIn)- T2.CountOut) + T2.SpareOfcure AS  SummaryCure -- คงเหลือในแผนก + หน้าเตา
			FROM(
				SELECT 
					T.ItemId   -- Item
					,T.ItemGTName  -- ชื่อ Item
					,T.ColorAll  -- color
					,SUM(T.Actual) AS Actual -- สร้างโครงผลิตได้
					,SUM(T.Bom) AS Bom -- อบยางผลิตได้
					,SUM(T.SpareOfcure) AS SpareOfcure -- WIP หน้าเตา
					,SUM(T.StockInplan) AS StockInplan  -- WIP Greentire
					,SUM(T.CountIn)  AS CountIn  -- รวมรับเข้า
					,SUM(T.CountOut) AS CountOut  -- รวมจ่ายออก
					,SUM(T.CountCure) AS CountCure  -- อบยางเบิก
					FROM(
						SELECT 
							TP.ItemId,
							GM.ItemGTName,
							GM.ColorAll,
							ISNULL(BT.Actual,0) AS Actual,
							CASE WHEN TP.Shift = 1 THEN ISNULL(TT.BomCActual,0) ELSE ISNULL(TT.BomDActual,0)  END AS Bom ,
							ISNULL(TP.SpareOfcure,0) AS SpareOfcure,
							ISNULL(TR2.StockInplan,0) AS StockInplan,
							ISNULL(TR.CountIn,0)AS CountIn,
							ISNULL(TR.CountOut,0) AS CountOut,
							ISNULL(TP.CountCure,0) AS CountCure
						FROM  ProductionGreentirePrintTable TP
						LEFT JOIN BuildSch BT ON BT.ItemId = TP.ItemId
						AND BT.DateBuild = TP.Sch_date AND   BT.Shift  = TP.Shift
						LEFT JOIN
						  (SELECT ItemGT,
								  ColorAll,
								  ItemGTName
							FROM ProductionSchGreentireMaster
							 GROUP BY ItemGT,ColorAll,ItemGTName)
							  GM ON GM.ItemGT = TP.ItemId
						LEFT JOIN TargetGreentire TT ON TT.ItemId = TP.ItemId
						AND CONVERT(date,TT.ShiftDate) = CONVERT(date,TP.Sch_date)
						LEFT JOIN ProductionGreentirePrintTable TP2 ON TP2.ItemId = TP.ItemId AND TP2.Sch_date = ?
						LEFT JOIN ProductionSchReciveTable TR2 ON TR2.ItemID = TP.ItemId
						AND TR2.Sch_date = ?
						LEFT JOIN ProductionSchReciveTable TR ON TR.ItemID = TP.ItemId
						AND TR.Sch_date = TP.Sch_date
						AND TR.Shift = TP.Shift
						WHERE MONTH(TP.Sch_date) = ?  and YEAR(TP.Sch_date) = ?

						--TP.Sch_date =  '2020-07-07'
						  )T
						GROUP BY T.ItemId
								,T.ItemGTName
								,T.ColorAll)T2",
			[
				$firstday,
				$firstday,
				$Month,
				$Year
			]
		);

		return $query;
	}

	public function getdatebyshift($date_sch, $shift, $tabaleuse)
	{

		$db = new Connector;
		$conn = $db->dbConnect();


		$getcheckdate = Sqlsrv::queryArray(
			$conn,
			"SELECT  Sch_Date,Shift , COUNT(Sch_Date) OVER(PARTITION BY Sch_Date) AS CountRow
			FROM $tabaleuse where Sch_Date = ?
			group by Sch_Date,Shift  order by Sch_Date desc,Shift desc
			",
			[
				$date_sch
			]
		);
		$datacheckCunt = $getcheckdate[0]["CountRow"];
		$shifcheck = $getcheckdate[0]["Shift"];


		if ($datacheckCunt == "" || $datacheckCunt == NULL) {

			$getdate = Sqlsrv::queryArray(
				$conn,
				"SELECT TOP 1 Sch_Date,Shift 
				  FROM $tabaleuse where Sch_Date < ?
				  group by Sch_Date,Shift  order by Sch_Date desc,Shift desc",
				[$date_sch]
			);
			$getdateNext = Sqlsrv::queryArray(
				$conn,
				"SELECT TOP 1 Sch_Date,Shift 
				  FROM $tabaleuse where Sch_Date > ?
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
					$getdate = Sqlsrv::queryArray(
						$conn,
						"SELECT TOP 1 Sch_Date,Shift 
				  FROM $tabaleuse where Sch_Date < ?
				  group by Sch_Date,Shift  order by Sch_Date desc,Shift desc",
						[$date_sch]
					);
					if ($shifcheck == 1) {
						$getdateNext = Sqlsrv::queryArray(
							$conn,
							"SELECT TOP 1 Sch_Date,Shift 
						  FROM $tabaleuse where Sch_Date > ?
						  group by Sch_Date,Shift  order by Sch_Date asc,Shift asc",
							[$date_sch]
						);
					} else {
						$getdateNext = Sqlsrv::queryArray(
							$conn,
							"SELECT TOP 1 Sch_Date,Shift 
						  FROM $tabaleuse where Sch_Date >= ?
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
						$getdate = Sqlsrv::queryArray(
							$conn,
							"SELECT TOP 1 Sch_Date,Shift 
						  FROM $tabaleuse where Sch_Date <= ?
						  group by Sch_Date,Shift  order by Sch_Date desc,Shift desc",
							[$date_sch]
						);
						$dateold = $getdate[0]["Sch_Date"];
						$shiftold = $getdate[0]["Shift"];
					} else {
						$getdate = Sqlsrv::queryArray(
							$conn,
							"SELECT TOP 2 Sch_Date,Shift 
						  FROM $tabaleuse where Sch_Date <= ?
						  group by Sch_Date,Shift  order by Sch_Date desc,Shift desc",
							[$date_sch]
						);
						$dateold = $getdate[1]["Sch_Date"];
						$shiftold = $getdate[1]["Shift"];
					}
					$getdateNext = Sqlsrv::queryArray(
						$conn,
						"SELECT TOP 1 Sch_Date,Shift 
							  FROM $tabaleuse where Sch_Date > ?
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
					$getdate = Sqlsrv::queryArray(
						$conn,
						"SELECT TOP 3 Sch_Date,Shift 
					FROM $tabaleuse where Sch_Date <= ?
					group by Sch_Date,Shift  order by Sch_Date desc,Shift desc
					",
						[
							$date_sch

						]
					);

					$getdateNext = Sqlsrv::queryArray(
						$conn,
						"SELECT TOP 1 Sch_Date,Shift 
					FROM $tabaleuse where Sch_Date <= ?
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
					$getdate = Sqlsrv::queryArray(
						$conn,
						"SELECT TOP 3 Sch_Date,Shift 
					FROM $tabaleuse where Sch_Date <= ?
					group by Sch_Date,Shift  order by Sch_Date desc,Shift desc
					",
						[
							$date_sch

						]
					);
					$getdateNext = Sqlsrv::queryArray(
						$conn,
						"SELECT TOP 1 Sch_Date,Shift 
					FROM $tabaleuse where Sch_Date > ?
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

		return [
			"dateold" => $dateold,
			"shiftold" => $shiftold,
			"datenext" => $datenext,
			"shifnext" => $shifnext
		];
	}

	public function reportSchDrawPdf($date, $shift)
	{
		$db = new Connector;
		$conn = $db->dbConnect();
		// $sqlId = "1=1";

		// if ($id !== null) {
		// 	$sqlId = " P.ID = $id ";
		// }
		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT 
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
			WHERE P.SchDate=? AND P.Shift=? AND P.Company=? 
			AND P.ItemID != ''
			ORDER BY C.CurID, P.MoldID ASC",
			[$date, $shift, $_SESSION["user_company"]]
		);

		return $query;
	}

	public function getBoilerbyDateDraw($date, $shift)
	{
		$db = new Connector;
		$conn = $db->dbConnect();

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT
				P.Boiler,C.CureSize
			FROM ProductionSchTable P
			LEFT JOIN ProductionSchCure C ON P.Boiler = C.CurID AND P.Company = C.Company
			WHERE P.SchDate = ?
			AND P.Shift = ?
			AND P.Company = ?
			-- AND P.ItemID IS NOT NULL
	        -- AND P.ItemID != ''
			--AND P.Boiler ='C-01'
			GROUP BY P.Boiler,C.CureSize
			ORDER BY P.Boiler,C.CureSize",
			[$date, $shift, $_SESSION["user_company"]]
		);

		return $query;
	}

	public function reportSchallPdf($date)
	{
		$db = new Connector;
		$conn = $db->dbConnect();

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT 
			A.Boiler
		   ,A.CureSize
		   ,SUM( CASE WHEN B.checkrows = 1 OR B.checkrows IS NULL  THEN 2 ELSE  1  END ) OVER (PARTITION BY A.CureSize ) AS rowtop
		   ,B.MoldID
		   ,B.ITEMID_SHIFT1
		   ,B.ITEMNAME_SHIFT1
		   ,B.TIME_SHIFT1
		   ,B.TARGET_SHIFT1
		   ,B.ACTUAL1_SHIFT1
		   ,B.ACTUAL2_SHIFT1
		   ,B.ACTUAL_SHIFT1
		   ,B.WEIGHT_SHIFT1
		   ,B.ITEMID_SHIFT2
		   ,B.ITEMNAME_SHIFT2
		   ,B.TIME_SHIFT2
		   ,B.TARGET_SHIFT2
		   ,B.ACTUAL1_SHIFT2
		   ,B.ACTUAL2_SHIFT2
		   ,B.ACTUAL_SHIFT2
		   ,B.WEIGHT_SHIFT2
		   
		   ,B.ID_SHIFT1
		   ,B.ID_SHIFT2
		   ,CASE WHEN B.checkrows is null THEN 1 ELSE  B.checkrows  END AS checkrows
		   ,CASE WHEN B.checkMoldID is null THEN 'A' ELSE  B.checkMoldID  END AS checkMoldID
		   , (SELECT COUNT(*)
			FROM (
				SELECT
		   		P.Boiler,C.CureSize
	   			FROM ProductionSchTable P
	   			LEFT JOIN ProductionSchCure C ON P.Boiler = C.CurID AND P.Company = C.Company
	   			WHERE P.SchDate = '$date'
	   			GROUP BY P.Boiler,C.CureSize
	   			)D
			LEFT JOIN
			(
				SELECT  T1.Boiler,T1.CureSize
				FROM
				(
					SELECT T.Boiler,T.CureSize
					FROM
					(
					SELECT P.Boiler,P.MoldID,C.CureSize
					FROM ProductionSchTable P
					LEFT JOIN ProductionSchCure C ON P.Boiler = C.CurID AND P.Company = C.Company
					WHERE P.SchDate = '$date'
					AND P.ItemID IS NOT NULL AND P.ItemID != ''
					AND P.Company = 'DSL'
					)T
					GROUP BY T.Boiler,T.CureSize
				)T1 
				
				)E ON D.Boiler = E.Boiler AND D.CureSize = E.CureSize
			   WHERE  D.CureSize = A.CureSize
		   
		   ) AS TotalBoiler
		FROM (
			SELECT
		   	P.Boiler,C.CureSize
	   		FROM ProductionSchTable P
	   		LEFT JOIN ProductionSchCure C ON P.Boiler = C.CurID AND P.Company = C.Company
	   		WHERE P.SchDate = '$date'
	   		--AND P.Shift = '1'
	   		AND P.Company = 'dsl'
	   		--AND P.Boiler = 'A-18'
	   		GROUP BY P.Boiler,C.CureSize
	   	) A
	   
	   LEFT JOIN(
	   		SELECT  T1.Boiler
		   ,T1.CureSize
		   ,T1.MoldID
		   ,P1.ItemID AS ITEMID_SHIFT1
		   ,I1.ItemName AS ITEMNAME_SHIFT1
		   ,P1.Time AS TIME_SHIFT1
		   ,P1.Target AS TARGET_SHIFT1
		   ,P1.Actual1 AS ACTUAL1_SHIFT1
		   ,P1.Actual2 AS ACTUAL2_SHIFT1
		   ,P1.Actual AS ACTUAL_SHIFT1
		   ,P1.Weight AS WEIGHT_SHIFT1
		   ,P2.ItemID AS ITEMID_SHIFT2
		   ,I2.ItemName AS ITEMNAME_SHIFT2
		   ,P2.Time AS TIME_SHIFT2
		   ,P2.Target AS TARGET_SHIFT2
		   ,P2.Actual1 AS ACTUAL1_SHIFT2
		   ,P2.Actual2 AS ACTUAL2_SHIFT2
		   ,P2.Actual AS ACTUAL_SHIFT2
		   ,P2.Weight AS WEIGHT_SHIFT2
		   
		   ,P1.ID AS ID_SHIFT1
		   ,P2.ID AS ID_SHIFT2
		   ,(
		   SELECT count(*)  AS Rows FROM (
			   SELECT Boiler,MoldID FROM ProductionSchTable
			   WHERE SchDate = '$date'
			  -- AND Shift = 1
			   AND Boiler = T1.Boiler
			   AND Company = 'DSL'
			  AND ItemID IS NOT NULL
			   AND ItemID != ''
			   
			   GROUP BY Boiler,MoldID)T
		   
		   ) AS checkrows
		   ,(
		   SELECT TOP 1 SUBSTRING(MoldID, 1, 1) AS MoldID FROM ProductionSchTable
		   WHERE SchDate = '$date'
		  -- AND Shift = ?
		   AND Boiler = T1.Boiler
		   AND Company = 'DSL'
		   AND ItemID IS NOT NULL
		   AND ItemID != ''
		   GROUP BY MoldID
		   ) AS checkMoldID
		   FROM
		   (
			   SELECT T.Boiler,T.CureSize,T.MoldID,T.CureID,SUM(ID_Shift1)ID_Shift1,SUM(ID_Shift2)ID_Shift2
			   FROM
			   (
				   SELECT P.Boiler,P.MoldID,C.CureSize,C.ID CureID
				   --,(SELECT COUNT(*) FROM ProductionSchTable WHERE SchDate= P.SchDate AND Shift = P.Shift AND Boiler=P.Boiler AND Company=P.Company AND ItemID IS NOT NULL AND P.ItemID != '' ) AS rowspan
				   ,CASE WHEN P.Shift = 1 THEN P.ID ELSE NULL END AS ID_Shift1
				   ,CASE WHEN P.Shift = 2 THEN P.ID ELSE NULL END AS ID_Shift2
				   FROM ProductionSchTable P
				   LEFT JOIN ProductionSchCure C ON P.Boiler = C.CurID AND P.Company = C.Company
				   WHERE P.SchDate = ?
				   AND P.ItemID IS NOT NULL AND P.ItemID != ''
				   AND P.Company = ?
			   )T
			   GROUP BY T.Boiler,T.CureSize,T.MoldID,T.CureID
		   )T1 LEFT JOIN
		   ProductionSchTable P1 ON T1.ID_Shift1 = P1.ID LEFT JOIN
		   ProductionSchItemMaster I1 ON P1.ItemID = I1.ID LEFT JOIN
		   ProductionSchTable P2 ON T1.ID_Shift2 = P2.ID LEFT JOIN
		   ProductionSchItemMaster I2 ON P2.ItemID = I2.ID
		   
	   
		   
	   
		   
		   ) B
		   ON A.Boiler = B.Boiler
		   
		   ORDER BY A.CureSize DESC,A.Boiler DESC,B.MoldID DESC",
			[$date, $_SESSION["user_company"]]
		);

		return $query;
	}

	public function getBoilerbyDateall($date)
	{
		$db = new Connector;
		$conn = $db->dbConnect();

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT
			P.Boiler,C.CureSize
		FROM ProductionSchTable P
		LEFT JOIN ProductionSchCure C ON P.Boiler = C.CurID AND P.Company = C.Company
		WHERE P.SchDate = ?
		--AND P.Shift = '1'
		AND P.Company = ?
		GROUP BY P.Boiler,C.CureSize
		ORDER BY C.CureSize DESC , P.Boiler DESC",
			[$date, $_SESSION["user_company"]]
		);

		return $query;
	}

	public function countItemExistall($date, $boiler)
	{
		$db = new Connector;
		$conn = $db->dbConnect();

		$query = Sqlsrv::queryArray(
			$conn,
			" SELECT count(*)  AS Rows FROM (
				SELECT Boiler,MoldID FROM ProductionSchTable
				WHERE SchDate = ?
			   -- AND Shift = 1
				AND Boiler = ?
				AND Company = ?
			   AND ItemID IS NOT NULL
				AND ItemID != ''
				
				GROUP BY Boiler,MoldID)T",
			[$date, $boiler, $_SESSION["user_company"]]
		);

		return $query[0]['Rows'];
	}

	public function countMoldExistall($date, $boiler)
	{
		$db = new Connector;
		$conn = $db->dbConnect();

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT TOP 1 SUBSTRING(MoldID, 1, 1) AS MoldID FROM ProductionSchTable
	        WHERE SchDate = ?
	       -- AND Shift = ?
	        AND Boiler = ?
	        AND Company = ?
	        AND ItemID IS NOT NULL
	        AND ItemID != ''
	        GROUP BY MoldID",
			[$date, $boiler, $_SESSION["user_company"]]
		);

		return $query[0]['MoldID'];
	}

	public function countBoilerall($date)
	{
		$db = new Connector;
		$conn = $db->dbConnect();
		$date = date('Y-m-d', strtotime($date));

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT COUNT(Boiler) AS CountBoiler
			FROM (
				SELECT
				Boiler
				FROM ProductionSchTable
				WHERE SchDate=?
				--AND Shift=?
				AND Company=?
				AND ItemID IS NOT NULL
				AND Target > 0
				GROUP BY Boiler
			)B",
			[$date, $_SESSION['user_company']]
		);
		return $query[0]['CountBoiler'];
	}

	public function countMoldall($date)
	{
		$db = new Connector;
		$conn = $db->dbConnect();
		$date = date('Y-m-d', strtotime($date));

		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT COUNT(MoldID) AS Mold FROM ProductionSchTable
			WHERE SchDate=?
		--	AND Shift=?
			AND Company=?
			AND ItemID IS NOT NULL
			AND Target > 0
			AND MoldID IN ('A1','B1')",
			[$date, $_SESSION['user_company']]
		);
		return $query[0]['Mold'];
	}

	public function countBoileralldata($date)
	{
		$db = new Connector;
		$conn = $db->dbConnect();
		$date = date('Y-m-d', strtotime($date));


		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT COUNT(Boiler) AS CountBoiler
			FROM (
				SELECT
				Boiler
				FROM ProductionSchTable
				
				 WHERE Company = 'dsl'
				 AND SchDate = '$date'
				--AND ItemID IS NOT NULL
				--AND Target > 0
				GROUP BY Boiler
			)B"
		);
		return $query[0]['CountBoiler'];
	}

	public function countMoldalldata($date)
	{
		$db = new Connector;
		$conn = $db->dbConnect();
		$date = date('Y-m-d', strtotime($date));


		$query = Sqlsrv::queryArray(
			$conn,
			"SELECT COUNT(MoldID) AS Mold FROM ProductionSchTable
			WHERE 
			 Company= 'dsl'
			 AND SchDate = '$date'
			--AND ItemID IS NOT NULL
			--AND Target > 0
			AND MoldID IN ('A1','B1')"

		);
		return $query[0]['Mold'];
	}
}
