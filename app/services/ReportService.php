<?php

namespace App\Services;

use Wattanar\Sqlsrv;
use App\Components\Database;
use App\Components\Utils;

class ReportService
{

	private $db;

	public function __construct()
	{
		$this->db = new Database;
	}

	public function greentireScrap($date, $product_group)
	{
		$date = date('Y-m-d', strtotime($date));
		$select_date = $date . ' 10:00:00';
		$next_date = date('Y-m-d' ,strtotime($date . '+1 day')) . ' 10:00:00';
		// return $select_date . ' = ' . $date_1;
		if (date('Y-m-d H:i:s') < $date . ' 10:00:00') {
			$select_date = date('Y-m-d' ,strtotime($date . '-1 day')) . ' 10:00:00';
			$next_date = date('Y-m-d' ,strtotime($date)) . ' 10:00:00';
		}

		return Sqlsrv::queryArray(
			$this->db->connect(),
			"SELECT 
			IT.Barcode,
			IT.CuringCode,
			D.ID [DefectID],
			D.Description [DefectDesc],
			ITS.Batch,
			IT.ItemID,
			GCM.ItemNumber [IDItem],
			IT.GT_Code [GT_Code],
			S.Description [Shift],
			ITS.CreateDate,
			IT.BuildingNo [MC],
			(
				SELECT TOP 1  S_S.Description [Shift] FROM InventTrans S_IT
				LEFT JOIN ShiftMaster S_S ON S_S.ID = S_IT.Shift
				WHERE S_IT.Barcode = IT.Barcode 
				AND S_IT.CreateDate = IT.CreateDate
			) [Shift_Build]
			FROM InventTable IT
			LEFT JOIN InventTrans ITS ON IT.Barcode = ITS.Barcode
			AND IT.UpdateDate = ITS.CreateDate
			LEFT JOIN GreentireCodeMaster GCM ON GCM.ID = IT.GT_Code
			LEFT JOIN Defect D ON D.ID = ITS.DefectID
			LEFT JOIN ScrapSide SS ON SS.ID = ITS.ScrapSide
			LEFT JOIN DisposalToUseIn DI ON DI.ID = IT.DisposalID
			LEFT JOIN ShiftMaster S ON S.ID = ITS.Shift
			LEFT JOIN UserMaster U ON U.ID = IT.CreateBy
			-- left join CureCodeMaster CCM ON IT.GT_Code = CCM.GreentireID
			-- left join ItemMaster IM ON IM.ID = IT.ItemID

			WHERE 
			IT.UpdateDate BETWEEN ? AND ?
			AND IT.WarehouseID = 1
			AND IT.DisposalID = 2
			AND ITS.DisposalID = 2
			AND IT.GT_Code IN 
			(
				SELECT TOP 1 CCM.GreentireID FROM CureCodeMaster CCM 
				LEFT JOIN ItemMaster IM ON CCM.ItemID = IM.ID
				WHERE CCM.GreentireID = IT.GT_Code
				AND IM.ProductGroup = ?
			)
			-- AND IM.ProductGroup = ?
			ORDER BY ITS.CreateDate ASC",
			[$select_date, $next_date, $product_group]
		);

		// return Sqlsrv::queryArray(
		// 	$this->db->connect(),
		// 	"SELECT 
		// 	IT.Barcode,
		// 	IT.GT_Code,
		// 	IT.ItemID,
		// 	(
		// 		SELECT TOP 1 ITSD.DefectID 
		// 		FROM  InventTrans ITSD
		// 		WHERE ITSD.Barcode = IT.Barcode
		// 		ORDER BY ITSD.TransID DESC
		// 	) as DefectID,
		// 	(
		// 		SELECT TOP 1 ITSD.Batch 
		// 		FROM  InventTrans ITSD
		// 		WHERE ITSD.Barcode = IT.Barcode
		// 		ORDER BY ITSD.TransID DESC
		// 	) as Batch, 
		// 	(
		// 		SELECT TOP 1 DF.Description 
		// 		FROM  InventTrans ITSD
		// 		LEFT JOIN Defect DF ON ITSD.DefectID = DF.ID 
		// 		WHERE ITSD.Barcode = IT.Barcode
		// 		ORDER BY ITSD.TransID DESC
		// 	) as DefectDesc,
		// 	(
		// 		SELECT TOP 1 SM.Description 
		// 		FROM  InventTrans ITSD
		// 		LEFT JOIN ShiftMaster SM ON SM.ID = ITSD.Shift
		// 		WHERE ITSD.Barcode = IT.Barcode
		// 		AND ITSD.DisposalID = 1 -- Greentire
		// 		AND ITSD.DocumentTypeID = 1 -- Receive
		// 		ORDER BY ITSD.TransID DESC
		// 	) as Shift,
		// 	(
		// 		SELECT TOP 1 ITS.CreateDate FROM InventTrans ITS
		// 		WHERE  ITS.Barcode = IT.Barcode
		// 		ORDER BY ITS.CreateDate DESC
		// 	) as CreateDate
		// 	FROM InventTable IT 
		// 	LEFT JOIN InventTrans ITS ON IT.Barcode = ITS.Barcode
		// 	WHERE IT.DisposalID = 2 -- scrap
		// 	AND IT.WarehouseID = 1
		// 	AND ITS.CreateDate BETWEEN ? AND ?
		// 	GROUP BY IT.Barcode, IT.GT_Code, IT.ItemID
		// 	ORDER BY IT.Barcode ASC",
		// 	[$select_date, $next_date]
		// );
	}

	public function curetireScrap($date, $product_group)
	{
		// $date = date('Y-m-d', strtotime($date));
		// $select_date = $date . ' 08:00:00';
		// $next_date = $date . ' 08:00:00'; 
		// $date_1 = date('Y-m-d H:i:s' ,strtotime($next_date . '+1 day'));
		$date = date('Y-m-d', strtotime($date));
		$select_date = $date . ' 10:00:00';
		$next_date = date('Y-m-d' ,strtotime($date . '+1 day')) . ' 10:00:00';

		if (date('Y-m-d H:i:s') < $date . ' 10:00:00') {
			$select_date = date('Y-m-d' ,strtotime($date . '-1 day')) . ' 10:00:00';
			$next_date = date('Y-m-d' ,strtotime($date)) . ' 10:00:00';
		}
		
		return Sqlsrv::queryArray(
			$this->db->connect(),
			"SELECT 
			IT.Barcode,
			IT.CuringCode,
			IT.GT_Code,
			D.ID [DefectID],
			D.Description [DefectDesc],
			ITS.Batch,
			IT.ItemID [ItemID],
			S.Description [Shift],
			IT.PressNo
			FROM InventTable IT
			LEFT JOIN InventTrans ITS ON IT.Barcode = ITS.Barcode
				AND IT.UpdateDate = ITS.CreateDate
			LEFT JOIN Defect D ON D.ID = ITS.DefectID
			LEFT JOIN ScrapSide SS ON SS.ID = ITS.ScrapSide
			LEFT JOIN DisposalToUseIn DI ON DI.ID = IT.DisposalID
			LEFT JOIN ShiftMaster S ON S.ID = ITS.Shift
			LEFT JOIN UserMaster U ON U.ID = IT.CreateBy
			-- left join CureCodeMaster CCM ON IT.GT_Code = CCM.GreentireID
			-- left join ItemMaster ITM ON ITM.ID = IT.ItemID
			WHERE 
			IT.UpdateDate BETWEEN ? AND ?
			AND IT.WarehouseID = 2
			AND IT.DisposalID = 2
			AND ITS.DisposalID = 2
			AND IT.GT_Code IN 
			(
				SELECT TOP 1 CCM.GreentireID FROM CureCodeMaster CCM 
				LEFT JOIN ItemMaster IM ON CCM.ItemID = IM.ID
				WHERE CCM.GreentireID = IT.GT_Code
				AND IM.ProductGroup = ?
			)
			ORDER BY ITS.CreateDate ASC",
			[$select_date, $next_date, $product_group]
		);
	}

	public function curingReportPdf($date) {
		$date = date('Y-m-d', strtotime($date));
		$conn = Database::connect();
		return Sqlsrv::queryArray(
			$conn,
			"SELECT 
				PressNo
				,CreateBy
				,Name
				,PressSide
				,CuringCode
				,SUM(Q1)[Q1]
				,SUM(Q2)[Q2]
				,SUM(Q3)[Q3]
				,SUM(Q4)[Q4]

			FROM(
			SELECT	T.CreateBy
				,U.Name
				,I.PressNo
				,I.PressSide
				,I.CuringCode
				,CONVERT(date,I.CuringDate)[date_b]
				,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '08:00:01' AND CONVERT(time,I.CuringDate) <= '11:00:00')[Q1]
				,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '11:00:01' AND CONVERT(time,I.CuringDate) <= '14:00:00')[Q2]
				,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '14:00:01' AND CONVERT(time,I.CuringDate) <= '17:00:00')[Q3]
				,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '17:00:01' AND CONVERT(time,I.CuringDate) <= '20:00:00')[Q4]
			FROM InventTable I
			LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.CuringDate=T.CreateDate AND T.QTY>0
			LEFT JOIN UserMaster U ON T.CreateBy=U.ID
			WHERE CONVERT(date,I.CuringDate)= ?
			AND I.PressNo IS NOT NULL
			AND LEFT(I.PressNo, 1) LIKE '%A%' 
			)Z WHERE Q1 IS NOT NULL OR Q2 IS NOT NULL OR Q3 IS NOT NULL OR Q4 IS NOT NULL
			GROUP BY 
			Z.CreateBy
			,Z.Name
			,Z.PressNo
			,Z.PressSide
			,Z.CuringCode
			ORDER BY PressNo ASC",
			[$date]
		);
	}

	// J Report
	public function BuildingServiceallpdf($datebuilding,$shift,$group, $product_group)
	{
		$datenight_original = str_replace('-', '/', $datebuilding);
		$datenight = date('Y-m-d 20:00:00', strtotime($datenight_original));
		$datebuildingnight = date('Y-m-d 08:00:00',strtotime($datenight . "+1 days"));
		$datebuildingnight_date_only = date('Y-m-d',strtotime($datenight_original . "+1 days"));
		// $datebuildingnight = date('Y-m-d 08:00:00', strtotime($datebuildingnight));
		$conn = Database::connect();
		if ($shift=='day') {			
			return Sqlsrv::queryJson($conn, "SELECT	
					BuildingNo
					,GT_Code
					,SUM(QTY_1)[Q1]
					,SUM(QTY_2)[Q2]
					,SUM(QTY_3)[Q3]
					,SUM(QTY_4)[Q4]
					,SUM(QTY_5)[Q5]
					,SUM(QTY_6)[Q6]
					,'day'[Shift]
					
				FROM(
				SELECT	I.BuildingNo
						,I.GT_Code
						,T.Shift
						,S.Description
						,CONVERT(date,I.DateBuild)[date_b]
						,(SELECT I.QTY where CONVERT(time,I.DateBuild) >= '08:00:01' AND CONVERT(time,I.DateBuild) <= '10:00:00')[QTY_1]
						,(SELECT I.QTY where CONVERT(time,I.DateBuild) >= '10:00:01' AND CONVERT(time,I.DateBuild) <= '12:00:00')[QTY_2]
						,(SELECT I.QTY where CONVERT(time,I.DateBuild) >= '12:00:01' AND CONVERT(time,I.DateBuild) <= '14:00:00')[QTY_3]
						,(SELECT I.QTY where CONVERT(time,I.DateBuild) >= '14:00:01' AND CONVERT(time,I.DateBuild) <= '16:00:00')[QTY_4]
						,(SELECT I.QTY where CONVERT(time,I.DateBuild) >= '16:00:01' AND CONVERT(time,I.DateBuild) <= '18:00:00')[QTY_5]
						,(SELECT I.QTY where CONVERT(time,I.DateBuild) >= '18:00:01' AND CONVERT(time,I.DateBuild) <= '20:00:00')[QTY_6]
				FROM InventTable I
				LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.DateBuild=T.CreateDate
				LEFT JOIN ShiftMaster S ON T.Shift=S.ID
				WHERE CONVERT(date,I.DateBuild)=?
				AND I.CheckBuild = 1
				AND I.GT_Code IN 
				(
					SELECT TOP 1 CCM.GreentireID FROM CureCodeMaster CCM 
					LEFT JOIN ItemMaster IM ON CCM.ItemID = IM.ID
					WHERE CCM.GreentireID = I.GT_Code
					AND IM.ProductGroup = ?
				)
				)Z
				WHERE QTY_1 IS NOT NULL OR QTY_2 IS NOT NULL OR QTY_3 IS NOT NULL OR QTY_4 IS NOT NULL OR QTY_5 IS NOT NULL OR QTY_6 IS NOT NULL
				GROUP BY 
				Z.BuildingNo
				,Z.GT_Code ORDER BY BuildingNo ASC
				",
				[
					$datebuilding,
					$product_group
				]
			);

		}else{

				return Sqlsrv::queryJson($conn, "SELECT	
					BuildingNo
					,GT_Code
					,SUM(QTY_1)[Q1]
					,SUM(QTY_2)[Q2]
					,SUM(QTY_3)[Q3]
					,SUM(QTY_4)[Q4]
					,SUM(QTY_5)[Q5]
					,SUM(QTY_6)[Q6]
					,'night'[Shift]
					
				FROM(
				SELECT	I.BuildingNo
						,I.GT_Code
						,T.Shift
						,S.Description
						,CONVERT(date,I.DateBuild)[date_b]
						,(SELECT I.QTY where I.DateBuild >= '$datebuilding 20:00:01' AND I.DateBuild <= '$datebuilding 22:00:00')[QTY_1]
						,(SELECT I.QTY where I.DateBuild >= '$datebuilding 22:00:01' AND I.DateBuild <= '$datebuilding 23:59:59')[QTY_2]
						,(SELECT I.QTY where I.DateBuild >= '$datebuildingnight_date_only 00:00:01' AND I.DateBuild <= '$datebuildingnight_date_only 02:00:00')[QTY_3]
						,(SELECT I.QTY where I.DateBuild >= '$datebuildingnight_date_only 02:00:01' AND I.DateBuild <= '$datebuildingnight_date_only 04:00:00')[QTY_4]
						,(SELECT I.QTY where I.DateBuild >= '$datebuildingnight_date_only 04:00:01' AND I.DateBuild <= '$datebuildingnight_date_only 06:00:00')[QTY_5]
						,(SELECT I.QTY where I.DateBuild >= '$datebuildingnight_date_only 06:00:01' AND I.DateBuild <= '$datebuildingnight_date_only 08:00:00')[QTY_6]
				FROM InventTable I
				LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.DateBuild=T.CreateDate AND T.QTY>0
				LEFT JOIN ShiftMaster S ON T.Shift=S.ID 
				WHERE 
				I.CheckBuild = 1
				AND I.GT_Code IN 
				(
					SELECT TOP 1 CCM.GreentireID FROM CureCodeMaster CCM 
					LEFT JOIN ItemMaster IM ON CCM.ItemID = IM.ID
					WHERE CCM.GreentireID = I.GT_Code
					AND IM.ProductGroup = ?
				) 
				AND I.DateBuild between ? AND ?
				)Z WHERE QTY_1 IS NOT NULL OR QTY_2 IS NOT NULL OR QTY_3 IS NOT NULL OR QTY_4 IS NOT NULL OR QTY_5 IS NOT NULL OR QTY_6 IS NOT NULL
				GROUP BY 
				Z.BuildingNo
				,Z.GT_Code ORDER BY BuildingNo ASC
				",
				[
					$product_group,
					$datebuilding,
					$datebuildingnight
				]
			);
		}
	}

	public function InternalServiceallpdf($dateinter)
	{
		$conn = Database::connect();
		return Sqlsrv::queryJson(
			$conn, 
			"SELECT  
				J.InventJournalID
				,J.ItemID
				,CONVERT(time,J.CreateDate)[time_create]
				,I.TemplateSerialNo
				,I.ItemID
				,IT.NameTH
				,I.CuringCode
				,R.Description[Note]
				,IJ.EmpCode
				,E.FirstName
				,E.LastName
				,E.DivisionCode
				,D.Description[Department]
				,J.CreateBy
				,ITS.Batch
				,U.Name
				,1[qty]
				,ROW_NUMBER() OVER(ORDER BY name ASC) AS Row
				,S.Description
			FROM InventJournalTrans J
			LEFT JOIN InventTable I ON J.BarcodeID=I.Barcode AND J.ItemID=I.ItemID
			LEFT JOIN ItemMaster IT ON I.ItemID=IT.ID
			LEFT JOIN RequsitionNote R ON J.RequsitionID=R.ID
			LEFT JOIN InventJournalTable IJ ON J.InventJournalID=IJ.InventJournalID
			LEFT JOIN Employee E ON IJ.EmpCode=E.Code
			LEFT JOIN DivisionMaster D ON E.DivisionCode=D.Code
			LEFT JOIN UserMaster U ON J.CreateBy=U.ID
			LEFT JOIN InventTrans ITS ON ITS.InventJournalID = J.InventJournalID AND J.BarcodeID = ITS.Barcode
			LEFT JOIN Status S ON Ij.Status=S.ID
			WHERE CONVERT(date,J.CreateDate) = ?
			AND IJ.JournalTypeID = 'MOV'
			ORDER BY CONVERT(time,J.CreateDate) ASC",
			[
				$dateinter
			]
		);
	}

		public function CuringServiceallpdf1($datecuring,$shift,$press1)
	{	
		$datenight = str_replace('-', '/', $datecuring);
		$datecuringnight = date('Y-m-d',strtotime($datenight . "+1 days"));
		$date1 = $datecuring.' 20:00:01';	$date2 = $datecuring.' 23:00:00';
		$date3 = $datecuring.' 23:00:01';	$date4 = $datecuringnight.' 02:00:00';
		$date5 = $datecuringnight.' 02:00:01';	$date6 = $datecuringnight.' 05:00:00';
		$date7 = $datecuringnight.' 05:00:01';	$date8 = $datecuringnight.' 08:00:00';
		$conn = Database::connect();
		if ($shift=='day') {
			return Sqlsrv::queryJson($conn, "SELECT 
					PressNo
					-- ,CreateBy
					-- ,Name
					,PressSide
					-- ,CuringCode
					,SUM(Q1)[Q1]
					,SUM(Q2)[Q2]
					,SUM(Q3)[Q3]
					,SUM(Q4)[Q4]

			FROM(
			SELECT	T.CreateBy
					,U.Name
					,I.PressNo
					,I.PressSide
					,I.CuringCode
					,CONVERT(date,I.CuringDate)[date_b]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '08:00:01' AND CONVERT(time,I.CuringDate) <= '11:00:00')[Q1]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '11:00:01' AND CONVERT(time,I.CuringDate) <= '14:00:00')[Q2]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '14:00:01' AND CONVERT(time,I.CuringDate) <= '17:00:00')[Q3]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '17:00:01' AND CONVERT(time,I.CuringDate) <= '20:00:00')[Q4]
			FROM InventTable I
			LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.CuringDate=T.CreateDate AND T.QTY>0
			LEFT JOIN UserMaster U ON T.CreateBy=U.ID
			WHERE CONVERT(date,I.CuringDate)=?  
			AND I.PressNo IS NOT NULL
			AND LEFT(I.PressNo, 1) LIKE '%$press1%' 
			)Z WHERE Q1 IS NOT NULL OR Q2 IS NOT NULL OR Q3 IS NOT NULL OR Q4 IS NOT NULL
			GROUP BY 
			-- Z.CreateBy
			-- ,Z.Name
			Z.PressNo
			,Z.PressSide
			-- ,Z.CuringCode
			ORDER BY PressNo ASC",[$datecuring]);
		}else{
			return Sqlsrv::queryJson($conn, "SELECT 
					PressNo
					-- ,CreateBy
					-- ,Name
					,PressSide
					-- ,CuringCode
					,SUM(Q1)[Q1]
					,SUM(Q2)[Q2]
					,SUM(Q3)[Q3]
					,SUM(Q4)[Q4]

			FROM(
			SELECT	T.CreateBy
					,U.Name
					,I.PressNo
					,I.PressSide
					,I.CuringCode
					,CONVERT(date,I.CuringDate)[date_b]
					,(SELECT I.QTY where I.CuringDate >= '$date1' AND I.CuringDate <= '$date2')[Q1]
					,(SELECT I.QTY where I.CuringDate >= '$date3' AND I.CuringDate <= '$date4')[Q2]
					,(SELECT I.QTY where I.CuringDate >= '$date5' AND I.CuringDate <= '$date6')[Q3]
					,(SELECT I.QTY where I.CuringDate >= '$date7' AND I.CuringDate <= '$date8')[Q4]
			FROM InventTable I
			LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.CuringDate=T.CreateDate AND T.QTY>0
			LEFT JOIN UserMaster U ON T.CreateBy=U.ID
			WHERE I.PressNo IS NOT NULL
			AND LEFT(I.PressNo, 1) LIKE '%$press1%' 
			)Z WHERE Q1 IS NOT NULL OR Q2 IS NOT NULL OR Q3 IS NOT NULL OR Q4 IS NOT NULL
			GROUP BY 
			-- Z.CreateBy
			-- Z.Name
			Z.PressNo
			,Z.PressSide
			-- ,Z.CuringCode
			ORDER BY PressNo ASC");
		}
	}
	
	public function CuringServiceallpdf2($datecuring,$shift,$press2)
	{
		$datenight = str_replace('-', '/', $datecuring);
		$datecuringnight = date('Y-m-d',strtotime($datenight . "+1 days"));
		$date1 = $datecuring.' 20:00:01';	$date2 = $datecuring.' 23:00:00';
		$date3 = $datecuring.' 23:00:01';	$date4 = $datecuringnight.' 02:00:00';
		$date5 = $datecuringnight.' 02:00:01';	$date6 = $datecuringnight.' 05:00:00';
		$date7 = $datecuringnight.' 05:00:01';	$date8 = $datecuringnight.' 08:00:00';
		$conn = Database::connect();
		if ($shift=='day') {
			return Sqlsrv::queryJson($conn, "SELECT 
					PressNo
					-- ,CreateBy
					-- ,Name
					,PressSide
					-- ,CuringCode
					,SUM(Q1)[Q1]
					,SUM(Q2)[Q2]
					,SUM(Q3)[Q3]
					,SUM(Q4)[Q4]

			FROM(
			SELECT	T.CreateBy
					,U.Name
					,I.PressNo
					,I.PressSide
					,I.CuringCode
					,CONVERT(date,I.CuringDate)[date_b]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '08:00:01' AND CONVERT(time,I.CuringDate) <= '11:00:00')[Q1]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '11:00:01' AND CONVERT(time,I.CuringDate) <= '14:00:00')[Q2]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '14:00:01' AND CONVERT(time,I.CuringDate) <= '17:00:00')[Q3]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '17:00:01' AND CONVERT(time,I.CuringDate) <= '20:00:00')[Q4]
			FROM InventTable I
			LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.CuringDate=T.CreateDate AND T.QTY>0
			LEFT JOIN UserMaster U ON T.CreateBy=U.ID
			WHERE CONVERT(date,I.CuringDate)=?  
			AND I.PressNo IS NOT NULL
			AND LEFT(I.PressNo, 1) LIKE '%$press2%' 
			)Z WHERE Q1 IS NOT NULL OR Q2 IS NOT NULL OR Q3 IS NOT NULL OR Q4 IS NOT NULL
			GROUP BY 
			-- Z.CreateBy
			-- Z.Name
			Z.PressNo
			,Z.PressSide
			-- ,Z.CuringCode
			ORDER BY PressNo ASC",[$datecuring]);
		}else{
			return Sqlsrv::queryJson($conn, "SELECT 
					PressNo
					-- ,CreateBy
					-- ,Name
					,PressSide
					-- ,CuringCode
					,SUM(Q1)[Q1]
					,SUM(Q2)[Q2]
					,SUM(Q3)[Q3]
					,SUM(Q4)[Q4]

			FROM(
			SELECT	T.CreateBy
					,U.Name
					,I.PressNo
					,I.PressSide
					,I.CuringCode
					,CONVERT(date,I.CuringDate)[date_b]
					,(SELECT I.QTY where I.CuringDate >= '$date1' AND I.CuringDate <= '$date2')[Q1]
					,(SELECT I.QTY where I.CuringDate >= '$date3' AND I.CuringDate <= '$date4')[Q2]
					,(SELECT I.QTY where I.CuringDate >= '$date5' AND I.CuringDate <= '$date6')[Q3]
					,(SELECT I.QTY where I.CuringDate >= '$date7' AND I.CuringDate <= '$date8')[Q4]
			FROM InventTable I
			LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.CuringDate=T.CreateDate AND T.QTY>0
			LEFT JOIN UserMaster U ON T.CreateBy=U.ID
			WHERE I.PressNo IS NOT NULL
			AND LEFT(I.PressNo, 1) LIKE '%$press2%' 
			)Z WHERE Q1 IS NOT NULL OR Q2 IS NOT NULL OR Q3 IS NOT NULL OR Q4 IS NOT NULL
			GROUP BY 
			-- Z.CreateBy
			-- Z.Name
			Z.PressNo
			,Z.PressSide
			-- ,Z.CuringCode
			ORDER BY PressNo ASC");
		}
	}

	public function CuringServiceallpdf3($datecuring,$shift,$press3)
	{
		$datenight = str_replace('-', '/', $datecuring);
		$datecuringnight = date('Y-m-d',strtotime($datenight . "+1 days"));
		$date1 = $datecuring.' 20:00:01';	$date2 = $datecuring.' 23:00:00';
		$date3 = $datecuring.' 23:00:01';	$date4 = $datecuringnight.' 02:00:00';
		$date5 = $datecuringnight.' 02:00:01';	$date6 = $datecuringnight.' 05:00:00';
		$date7 = $datecuringnight.' 05:00:01';	$date8 = $datecuringnight.' 08:00:00';
		$conn = Database::connect();
		if ($shift=='day') {
			return Sqlsrv::queryJson($conn, "SELECT 
					PressNo
					-- ,CreateBy
					-- ,Name
					,PressSide
					-- ,CuringCode
					,SUM(Q1)[Q1]
					,SUM(Q2)[Q2]
					,SUM(Q3)[Q3]
					,SUM(Q4)[Q4]

			FROM(
			SELECT	T.CreateBy
					,U.Name
					,I.PressNo
					,I.PressSide
					,I.CuringCode
					,CONVERT(date,I.CuringDate)[date_b]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '08:00:01' AND CONVERT(time,I.CuringDate) <= '11:00:00')[Q1]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '11:00:01' AND CONVERT(time,I.CuringDate) <= '14:00:00')[Q2]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '14:00:01' AND CONVERT(time,I.CuringDate) <= '17:00:00')[Q3]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '17:00:01' AND CONVERT(time,I.CuringDate) <= '20:00:00')[Q4]
			FROM InventTable I
			LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.CuringDate=T.CreateDate AND T.QTY>0
			LEFT JOIN UserMaster U ON T.CreateBy=U.ID
			WHERE CONVERT(date,I.CuringDate)=? 
			AND I.PressNo IS NOT NULL
			AND LEFT(I.PressNo, 1) LIKE '%$press3%' 
			)Z WHERE Q1 IS NOT NULL OR Q2 IS NOT NULL OR Q3 IS NOT NULL OR Q4 IS NOT NULL
			GROUP BY 
			-- Z.CreateBy
			-- Z.Name
			Z.PressNo
			,Z.PressSide
			-- ,Z.CuringCode
			ORDER BY PressNo ASC",[$datecuring]);
		}else{
			return Sqlsrv::queryJson($conn, "SELECT 
					PressNo
					-- ,CreateBy
					-- ,Name
					,PressSide
					-- ,CuringCode
					,SUM(Q1)[Q1]
					,SUM(Q2)[Q2]
					,SUM(Q3)[Q3]
					,SUM(Q4)[Q4]

			FROM(
			SELECT	T.CreateBy
					,U.Name
					,I.PressNo
					,I.PressSide
					,I.CuringCode
					,CONVERT(date,I.CuringDate)[date_b]
					,(SELECT I.QTY where I.CuringDate >= '$date1' AND I.CuringDate <= '$date2')[Q1]
					,(SELECT I.QTY where I.CuringDate >= '$date3' AND I.CuringDate <= '$date4')[Q2]
					,(SELECT I.QTY where I.CuringDate >= '$date5' AND I.CuringDate <= '$date6')[Q3]
					,(SELECT I.QTY where I.CuringDate >= '$date7' AND I.CuringDate <= '$date8')[Q4]
			FROM InventTable I
			LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.CuringDate=T.CreateDate AND T.QTY>0
			LEFT JOIN UserMaster U ON T.CreateBy=U.ID
			WHERE I.PressNo IS NOT NULL
			AND LEFT(I.PressNo, 1) LIKE '%$press3%' 
			)Z WHERE Q1 IS NOT NULL OR Q2 IS NOT NULL OR Q3 IS NOT NULL OR Q4 IS NOT NULL
			GROUP BY 
			-- Z.CreateBy
			-- Z.Name
			Z.PressNo
			,Z.PressSide
			-- ,Z.CuringCode
			ORDER BY PressNo ASC");
		}
	}

	public function CuringServiceallpdfQ1($datecuring,$shift,$press1)
	{	
		$datenight = str_replace('-', '/', $datecuring);
		$datecuringnight = date('Y-m-d',strtotime($datenight . "+1 days"));
		$date1 = $datecuring.' 20:00:01';	$date2 = $datecuring.' 23:00:00';
		$date3 = $datecuring.' 23:00:01';	$date4 = $datecuringnight.' 02:00:00';
		$date5 = $datecuringnight.' 02:00:01';	$date6 = $datecuringnight.' 05:00:00';
		$date7 = $datecuringnight.' 05:00:01';	$date8 = $datecuringnight.' 08:00:00';
		$conn = Database::connect();
		if ($shift=='day') {
			return Sqlsrv::queryArray($conn, "SELECT 
					PressNo
					-- ,CreateBy
					-- ,Name
					,PressSide
					,CuringCode
					,rate12
					,SUM(Q1)[Q1]
					,SUM(Q2)[Q2]
					,SUM(Q3)[Q3]
					,SUM(Q4)[Q4]

			FROM(
			SELECT	T.CreateBy
					,U.Name
					,I.PressNo
					,I.PressSide
					,I.CuringCode
					,C.rate12
					,CONVERT(date,I.CuringDate)[date_b]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '08:00:01' AND CONVERT(time,I.CuringDate) <= '11:00:00')[Q1]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '11:00:01' AND CONVERT(time,I.CuringDate) <= '14:00:00')[Q2]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '14:00:01' AND CONVERT(time,I.CuringDate) <= '17:00:00')[Q3]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '17:00:01' AND CONVERT(time,I.CuringDate) <= '20:00:00')[Q4]
			FROM InventTable I
			LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.CuringDate=T.CreateDate AND T.QTY>0
			LEFT JOIN UserMaster U ON T.CreateBy=U.ID
			LEFT JOIN CureCodeMaster C ON I.CuringCode=C.ID
			WHERE CONVERT(date,I.CuringDate)=?  
			AND I.PressNo IS NOT NULL
			AND LEFT(I.PressNo, 1) LIKE '%$press1%' 
			)Z WHERE Q1 IS NOT NULL OR Q2 IS NOT NULL OR Q3 IS NOT NULL OR Q4 IS NOT NULL
			GROUP BY 
			-- Z.CreateBy
			-- ,Z.Name
			Z.PressNo
			,Z.PressSide
			,Z.CuringCode
			,Z.rate12
			ORDER BY PressNo ASC",[$datecuring]);
		}else{
			return Sqlsrv::queryArray($conn, "SELECT 
					PressNo
					-- ,CreateBy
					-- ,Name
					,PressSide
					,CuringCode
					,rate12
					,SUM(Q1)[Q1]
					,SUM(Q2)[Q2]
					,SUM(Q3)[Q3]
					,SUM(Q4)[Q4]

			FROM(
			SELECT	T.CreateBy
					,U.Name
					,I.PressNo
					,I.PressSide
					,I.CuringCode
					,C.rate12
					,CONVERT(date,I.CuringDate)[date_b]
					,(SELECT I.QTY where I.CuringDate >= '$date1' AND I.CuringDate <= '$date2')[Q1]
					,(SELECT I.QTY where I.CuringDate >= '$date3' AND I.CuringDate <= '$date4')[Q2]
					,(SELECT I.QTY where I.CuringDate >= '$date5' AND I.CuringDate <= '$date6')[Q3]
					,(SELECT I.QTY where I.CuringDate >= '$date7' AND I.CuringDate <= '$date8')[Q4]
			FROM InventTable I
			LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.CuringDate=T.CreateDate AND T.QTY>0
			LEFT JOIN UserMaster U ON T.CreateBy=U.ID
			LEFT JOIN CureCodeMaster C ON I.CuringCode=C.ID
			WHERE I.PressNo IS NOT NULL
			AND LEFT(I.PressNo, 1) LIKE '%$press1%' 
			)Z WHERE Q1 IS NOT NULL OR Q2 IS NOT NULL OR Q3 IS NOT NULL OR Q4 IS NOT NULL
			GROUP BY 
			-- Z.CreateBy
			-- ,Z.Name
			Z.PressNo
			,Z.PressSide
			,Z.CuringCode
			,Z.rate12
			ORDER BY PressNo ASC");
		}
	}

	public function CuringServiceallpdfQ2($datecuring,$shift,$press2)
	{	
		$datenight = str_replace('-', '/', $datecuring);
		$datecuringnight = date('Y-m-d',strtotime($datenight . "+1 days"));
		$date1 = $datecuring.' 20:00:01';	$date2 = $datecuring.' 23:00:00';
		$date3 = $datecuring.' 23:00:01';	$date4 = $datecuringnight.' 02:00:00';
		$date5 = $datecuringnight.' 02:00:01';	$date6 = $datecuringnight.' 05:00:00';
		$date7 = $datecuringnight.' 05:00:01';	$date8 = $datecuringnight.' 08:00:00';
		$conn = Database::connect();
		if ($shift=='day') {
			return Sqlsrv::queryArray($conn, "SELECT 
					PressNo
					-- ,CreateBy
					-- ,Name
					,PressSide
					,CuringCode
					,rate12
					,SUM(Q1)[Q1]
					,SUM(Q2)[Q2]
					,SUM(Q3)[Q3]
					,SUM(Q4)[Q4]

			FROM(
			SELECT	T.CreateBy
					,U.Name
					,I.PressNo
					,I.PressSide
					,I.CuringCode
					,C.rate12
					,CONVERT(date,I.CuringDate)[date_b]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '08:00:01' AND CONVERT(time,I.CuringDate) <= '11:00:00')[Q1]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '11:00:01' AND CONVERT(time,I.CuringDate) <= '14:00:00')[Q2]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '14:00:01' AND CONVERT(time,I.CuringDate) <= '17:00:00')[Q3]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '17:00:01' AND CONVERT(time,I.CuringDate) <= '20:00:00')[Q4]
			FROM InventTable I
			LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.CuringDate=T.CreateDate AND T.QTY>0
			LEFT JOIN UserMaster U ON T.CreateBy=U.ID
			LEFT JOIN CureCodeMaster C ON I.CuringCode=C.ID
			WHERE CONVERT(date,I.CuringDate)=?  
			AND I.PressNo IS NOT NULL
			AND LEFT(I.PressNo, 1) LIKE '%$press2%' 
			)Z WHERE Q1 IS NOT NULL OR Q2 IS NOT NULL OR Q3 IS NOT NULL OR Q4 IS NOT NULL
			GROUP BY 
			-- Z.CreateBy
			-- ,Z.Name
			Z.PressNo
			,Z.PressSide
			,Z.CuringCode
			,Z.rate12
			ORDER BY PressNo ASC",[$datecuring]);
		}else{
			return Sqlsrv::queryArray($conn, "SELECT 
					PressNo
					-- ,CreateBy
					-- ,Name
					,PressSide
					,CuringCode
					,rate12
					,SUM(Q1)[Q1]
					,SUM(Q2)[Q2]
					,SUM(Q3)[Q3]
					,SUM(Q4)[Q4]

			FROM(
			SELECT	T.CreateBy
					,U.Name
					,I.PressNo
					,I.PressSide
					,I.CuringCode
					,C.rate12
					,CONVERT(date,I.CuringDate)[date_b]
					,(SELECT I.QTY where I.CuringDate >= '$date1' AND I.CuringDate <= '$date2')[Q1]
					,(SELECT I.QTY where I.CuringDate >= '$date3' AND I.CuringDate <= '$date4')[Q2]
					,(SELECT I.QTY where I.CuringDate >= '$date5' AND I.CuringDate <= '$date6')[Q3]
					,(SELECT I.QTY where I.CuringDate >= '$date7' AND I.CuringDate <= '$date8')[Q4]
			FROM InventTable I
			LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.CuringDate=T.CreateDate AND T.QTY>0
			LEFT JOIN UserMaster U ON T.CreateBy=U.ID
			LEFT JOIN CureCodeMaster C ON I.CuringCode=C.ID
			WHERE I.PressNo IS NOT NULL
			AND LEFT(I.PressNo, 1) LIKE '%$press2%' 
			)Z WHERE Q1 IS NOT NULL OR Q2 IS NOT NULL OR Q3 IS NOT NULL OR Q4 IS NOT NULL
			GROUP BY 
			-- Z.CreateBy
			-- ,Z.Name
			Z.PressNo
			,Z.PressSide
			,Z.CuringCode
			,Z.rate12
			ORDER BY PressNo ASC");
		}
	}

	public function CuringServiceallpdfQ3($datecuring,$shift,$press3)
	{	
		$datenight = str_replace('-', '/', $datecuring);
		$datecuringnight = date('Y-m-d',strtotime($datenight . "+1 days"));
		$date1 = $datecuring.' 20:00:01';	$date2 = $datecuring.' 23:00:00';
		$date3 = $datecuring.' 23:00:01';	$date4 = $datecuringnight.' 02:00:00';
		$date5 = $datecuringnight.' 02:00:01';	$date6 = $datecuringnight.' 05:00:00';
		$date7 = $datecuringnight.' 05:00:01';	$date8 = $datecuringnight.' 08:00:00';
		$conn = Database::connect();
		if ($shift=='day') {
			return Sqlsrv::queryArray($conn, "SELECT 
					PressNo
					-- ,CreateBy
					-- ,Name
					,PressSide
					,CuringCode
					,rate12
					,SUM(Q1)[Q1]
					,SUM(Q2)[Q2]
					,SUM(Q3)[Q3]
					,SUM(Q4)[Q4]

			FROM(
			SELECT	T.CreateBy
					,U.Name
					,I.PressNo
					,I.PressSide
					,I.CuringCode
					,C.rate12
					,CONVERT(date,I.CuringDate)[date_b]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '08:00:01' AND CONVERT(time,I.CuringDate) <= '11:00:00')[Q1]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '11:00:01' AND CONVERT(time,I.CuringDate) <= '14:00:00')[Q2]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '14:00:01' AND CONVERT(time,I.CuringDate) <= '17:00:00')[Q3]
					,(SELECT I.QTY where CONVERT(time,I.CuringDate) >= '17:00:01' AND CONVERT(time,I.CuringDate) <= '20:00:00')[Q4]
			FROM InventTable I
			LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.CuringDate=T.CreateDate AND T.QTY>0
			LEFT JOIN UserMaster U ON T.CreateBy=U.ID
			LEFT JOIN CureCodeMaster C ON I.CuringCode=C.ID
			WHERE CONVERT(date,I.CuringDate)=?  
			AND I.PressNo IS NOT NULL
			AND LEFT(I.PressNo, 1) LIKE '%$press3%' 
			)Z WHERE Q1 IS NOT NULL OR Q2 IS NOT NULL OR Q3 IS NOT NULL OR Q4 IS NOT NULL
			GROUP BY 
			-- Z.CreateBy
			-- ,Z.Name
			Z.PressNo
			,Z.PressSide
			,Z.CuringCode
			,Z.rate12
			ORDER BY PressNo ASC",[$datecuring]);
		}else{
			return Sqlsrv::queryArray($conn, "SELECT 
					PressNo
					-- ,CreateBy
					-- ,Name
					,PressSide
					,CuringCode
					,rate12
					,SUM(Q1)[Q1]
					,SUM(Q2)[Q2]
					,SUM(Q3)[Q3]
					,SUM(Q4)[Q4]

			FROM(
			SELECT	T.CreateBy
					,U.Name
					,I.PressNo
					,I.PressSide
					,I.CuringCode
					,C.rate12
					,CONVERT(date,I.CuringDate)[date_b]
					,(SELECT I.QTY where I.CuringDate >= '$date1' AND I.CuringDate <= '$date2')[Q1]
					,(SELECT I.QTY where I.CuringDate >= '$date3' AND I.CuringDate <= '$date4')[Q2]
					,(SELECT I.QTY where I.CuringDate >= '$date5' AND I.CuringDate <= '$date6')[Q3]
					,(SELECT I.QTY where I.CuringDate >= '$date7' AND I.CuringDate <= '$date8')[Q4]
			FROM InventTable I
			LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.CuringDate=T.CreateDate AND T.QTY>0
			LEFT JOIN UserMaster U ON T.CreateBy=U.ID
			LEFT JOIN CureCodeMaster C ON I.CuringCode=C.ID
			WHERE I.PressNo IS NOT NULL
			AND LEFT(I.PressNo, 1) LIKE '%$press3%' 
			)Z WHERE Q1 IS NOT NULL OR Q2 IS NOT NULL OR Q3 IS NOT NULL OR Q4 IS NOT NULL
			GROUP BY 
			-- Z.CreateBy
			-- ,Z.Name
			Z.PressNo
			,Z.PressSide
			,Z.CuringCode
			,Z.rate12
			ORDER BY PressNo ASC");
		}
	}

	public function Curingname1_4($datecuring,$shift,$press01,$press04)
	{	
		$datenight = str_replace('-', '/', $datecuring);
		$datecuringnight = date('Y-m-d',strtotime($datenight . "+1 days"));
		$date1 = $datecuring.' 20:00:01';	$date2 = $datecuring.' 23:00:00';
		$date3 = $datecuring.' 23:00:01';	$date4 = $datecuringnight.' 02:00:00';
		$date5 = $datecuringnight.' 02:00:01';	$date6 = $datecuringnight.' 05:00:00';
		$date7 = $datecuringnight.' 05:00:01';	$date8 = $datecuringnight.' 08:00:00';
		$conn = Database::connect();
		if ($shift=='day') {
			return Sqlsrv::queryArray($conn, "SELECT TOP 2 
					CreateBy
					,Name
			FROM(
			SELECT	T.CreateBy
					,U.Name
			FROM InventTable I
			LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.CuringDate=T.CreateDate AND T.QTY>0
			LEFT JOIN UserMaster U ON T.CreateBy=U.ID
			WHERE CONVERT(date,I.CuringDate)=?  
			AND I.PressNo IS NOT NULL
			AND I.PressNo BETWEEN '$press01' AND '$press04'
			)Z 
			GROUP BY 
			Z.CreateBy
			,Z.Name",[$datecuring]);
		}else{
			return Sqlsrv::queryArray($conn, "SELECT TOP 2 
					CreateBy
					,Name
			FROM(
			SELECT	T.CreateBy
					,U.Name
			FROM InventTable I
			LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.CuringDate=T.CreateDate AND T.QTY>0
			LEFT JOIN UserMaster U ON T.CreateBy=U.ID
			WHERE I.CuringDate BETWEEN ? AND ? 
			AND I.PressNo IS NOT NULL
			AND I.PressNo BETWEEN '$press01' AND '$press04'
			)Z 
			GROUP BY 
			Z.CreateBy
			,Z.Name",[$date1,$date8]);
		}
	}

	public function Curingname5_8($datecuring,$shift,$press05,$press08)
	{	
		$datenight = str_replace('-', '/', $datecuring);
		$datecuringnight = date('Y-m-d',strtotime($datenight . "+1 days"));
		$date1 = $datecuring.' 20:00:01';	$date2 = $datecuring.' 23:00:00';
		$date3 = $datecuring.' 23:00:01';	$date4 = $datecuringnight.' 02:00:00';
		$date5 = $datecuringnight.' 02:00:01';	$date6 = $datecuringnight.' 05:00:00';
		$date7 = $datecuringnight.' 05:00:01';	$date8 = $datecuringnight.' 08:00:00';
		$conn = Database::connect();
		if ($shift=='day') {
			return Sqlsrv::queryArray($conn, "SELECT TOP 2 
					CreateBy
					,Name
			FROM(
			SELECT	T.CreateBy
					,U.Name
			FROM InventTable I
			LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.CuringDate=T.CreateDate AND T.QTY>0
			LEFT JOIN UserMaster U ON T.CreateBy=U.ID
			WHERE CONVERT(date,I.CuringDate)=?  
			AND I.PressNo IS NOT NULL
			AND I.PressNo BETWEEN '$press05' AND '$press08'
			)Z 
			GROUP BY 
			Z.CreateBy
			,Z.Name",[$datecuring]);
		}else{
			return Sqlsrv::queryArray($conn, "SELECT TOP 2 
					CreateBy
					,Name
			FROM(
			SELECT	T.CreateBy
					,U.Name
			FROM InventTable I
			LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.CuringDate=T.CreateDate AND T.QTY>0
			LEFT JOIN UserMaster U ON T.CreateBy=U.ID
			WHERE I.CuringDate BETWEEN ? AND ? 
			AND I.PressNo IS NOT NULL
			AND I.PressNo BETWEEN '$press05' AND '$press08'
			)Z 
			GROUP BY 
			Z.CreateBy
			,Z.Name",[$date1,$date8]);
		}
	}

	public function Curingname9_12($datecuring,$shift,$press09,$press12)
	{	
		$datenight = str_replace('-', '/', $datecuring);
		$datecuringnight = date('Y-m-d',strtotime($datenight . "+1 days"));
		$date1 = $datecuring.' 20:00:01';	$date2 = $datecuring.' 23:00:00';
		$date3 = $datecuring.' 23:00:01';	$date4 = $datecuringnight.' 02:00:00';
		$date5 = $datecuringnight.' 02:00:01';	$date6 = $datecuringnight.' 05:00:00';
		$date7 = $datecuringnight.' 05:00:01';	$date8 = $datecuringnight.' 08:00:00';
		$conn = Database::connect();
		if ($shift=='day') {
			return Sqlsrv::queryArray($conn, "SELECT TOP 2
					CreateBy
					,Name
			FROM(
			SELECT	T.CreateBy
					,U.Name
			FROM InventTable I
			LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.CuringDate=T.CreateDate AND T.QTY>0
			LEFT JOIN UserMaster U ON T.CreateBy=U.ID
			WHERE CONVERT(date,I.CuringDate)=?  
			AND I.PressNo IS NOT NULL
			AND I.PressNo BETWEEN '$press09' AND '$press12'
			)Z 
			GROUP BY 
			Z.CreateBy
			,Z.Name",[$datecuring]);
		}else{
			return Sqlsrv::queryArray($conn, "SELECT TOP 2 
					CreateBy
					,Name
			FROM(
			SELECT	T.CreateBy
					,U.Name
			FROM InventTable I
			LEFT JOIN InventTrans T ON I.Barcode=T.Barcode AND I.CuringDate=T.CreateDate AND T.QTY>0
			LEFT JOIN UserMaster U ON T.CreateBy=U.ID
			WHERE I.CuringDate BETWEEN ? AND ? 
			AND I.PressNo IS NOT NULL
			AND I.PressNo BETWEEN '$press09' AND '$press12'
			)Z 
			GROUP BY 
			Z.CreateBy
			,Z.Name",[$date1,$date8]);
		}
	}

	public function CuringServiceallgrouppdf($datecuring,$shift)
	{
		$datenight = str_replace('-', '/', $datecuring);
		$datecuringnight = date('Y-m-d',strtotime($datenight . "+1 days"));
		$conn = Database::connect();

		if ($shift=='day') {
			return Sqlsrv::queryJson($conn, "SELECT 
						Shift,
						Description
					FROM(
					SELECT   T.Shift 
							,S.Description
					FROM InventTrans T 
					LEFT JOIN ShiftMaster S ON T.Shift=S.ID
					WHERE CONVERT(date,T.CreateDate)= ?
					) Z
					GROUP BY 
					Z.Shift,
					Z.Description",[$datecuring]);
		}else{
			return Sqlsrv::queryJson($conn, "SELECT 
						Shift,
						Description
					FROM(
					SELECT   T.Shift 
							,S.Description
					FROM InventTrans T 
					LEFT JOIN ShiftMaster S ON T.Shift=S.ID
					WHERE CONVERT(date,T.CreateDate) BETWEEN ? AND ?
					) Z
					GROUP BY 
					Z.Shift,
					Z.Description",[$datecuring,$datecuringnight]);
		}
	}

	public function GreentireInventoryServiceallpdf()
	{
		$conn = Database::connect();
		return Sqlsrv::queryJson($conn, "SELECT ROW_NUMBER() OVER(ORDER BY CodeID ASC) AS Row
				,CodeID
				,SUM(onhand)[onhand]
				,SUM(hold)[hold]
				,SUM(repair)[repair]
				,Batch
		FROM(
		SELECT	O.CodeID,
				(SELECT O.QTY WHERE O.LocationID=2)[onhand],
				(SELECT O.QTY WHERE O.LocationID=9)[hold],
				(SELECT O.QTY WHERE O.LocationID=10)[repair],
				O.Batch
		FROM Onhand O
		WHERE O.WarehouseID =1 AND O.QTY IS NOT NULL AND O.QTY!=0
		)Z
		GROUP BY
		Z.CodeID, Z.Batch",[$dateinter]);
	}

	public function GreentireInventoryServiceallpdfwarehousesent($shift,$time,$counttime,$datewarehouse)
	{
		$conn = Database::connect();
		if ($shift=='day') {

			if ($counttime==1) {
				if ($time==1) {
					$timeto=$datewarehouse.' 08:00';	$timefrom=$datewarehouse.' 11:00';
				}elseif ($time==2) {
					$timeto=$datewarehouse.' 11:00';	$timefrom=$datewarehouse.' 14:00';
				}elseif ($time==3) {
					$timeto=$datewarehouse.' 14:00';	$timefrom=$datewarehouse.' 17:00';
				}elseif ($time==4) {
					$timeto=$datewarehouse.' 17:00';	$timefrom=$datewarehouse.' 20:00';
				}
			}elseif ($counttime==2) {
				if ($time=='1,2') {
					$timeto1=$datewarehouse.' 08:00';	$timefrom1=$datewarehouse.' 11:00';
					$timeto2=$datewarehouse.' 11:00';	$timefrom2=$datewarehouse.' 14:00';
				}elseif ($time=='1,3') {
					$timeto1=$datewarehouse.' 08:00';	$timefrom1=$datewarehouse.' 11:00';
					$timeto2=$datewarehouse.' 14:00';	$timefrom2=$datewarehouse.' 17:00';
				}elseif ($time=='1,4') {
					$timeto1=$datewarehouse.' 08:00';	$timefrom1=$datewarehouse.' 11:00';
					$timeto2=$datewarehouse.' 17:00';	$timefrom2=$datewarehouse.' 20:00';
				}elseif ($time=='2,3') {
					$timeto1=$datewarehouse.' 11:00';	$timefrom1=$datewarehouse.' 14:00';
					$timeto2=$datewarehouse.' 14:00';	$timefrom2=$datewarehouse.' 17:00';
				}elseif ($time=='2,4') {
					$timeto1=$datewarehouse.' 11:00';	$timefrom1=$datewarehouse.' 14:00';
					$timeto2=$datewarehouse.' 17:00';	$timefrom2=$datewarehouse.' 20:00';
				}elseif ($time=='3,4') {
					$timeto1=$datewarehouse.' 14:00';	$timefrom1=$datewarehouse.' 17:00';
					$timeto2=$datewarehouse.' 17:00';	$timefrom2=$datewarehouse.' 20:00';
				}
			}elseif ($counttime==3) {
				if ($time=='1,2,3') {
					$timeto1=$datewarehouse.' 08:00';	$timefrom1=$datewarehouse.' 11:00';
					$timeto2=$datewarehouse.' 11:00';	$timefrom2=$datewarehouse.' 14:00';
					$timeto3=$datewarehouse.' 14:00';	$timefrom3=$datewarehouse.' 17:00';
				}elseif ($time=='1,2,4') {
					$timeto1=$datewarehouse.' 08:00';	$timefrom1=$datewarehouse.' 11:00';
					$timeto2=$datewarehouse.' 11:00';	$timefrom2=$datewarehouse.' 14:00';
					$timeto3=$datewarehouse.' 17:00';	$timefrom3=$datewarehouse.' 20:00';
				}elseif ($time=='1,3,4') {
					$timeto1=$datewarehouse.' 08:00';	$timefrom1=$datewarehouse.' 11:00';
					$timeto2=$datewarehouse.' 14:00';	$timefrom2=$datewarehouse.' 17:00';
					$timeto3=$datewarehouse.' 17:00';	$timefrom3=$datewarehouse.' 20:00';
				}elseif ($time=='2,3,4') {
					$timeto1=$datewarehouse.' 11:00';	$timefrom1=$datewarehouse.' 14:00';
					$timeto2=$datewarehouse.' 14:00';	$timefrom2=$datewarehouse.' 17:00';
					$timeto3=$datewarehouse.' 17:00';	$timefrom3=$datewarehouse.' 20:00';
				}
			}elseif ($counttime==4) {
				if ($time=='1,2,3,4') {
					$timeto1=$datewarehouse.' 08:00';	$timefrom1=$datewarehouse.' 11:00';
					$timeto2=$datewarehouse.' 11:00';	$timefrom2=$datewarehouse.' 14:00';
					$timeto3=$datewarehouse.' 14:00';	$timefrom3=$datewarehouse.' 17:00';
					$timeto4=$datewarehouse.' 17:00';	$timefrom4=$datewarehouse.' 20:00';
				}
			}

			if ($counttime==1) {
				return Sqlsrv::queryJson($conn, "SELECT ROW_NUMBER() OVER(ORDER BY ItemID ASC) AS Row
						,ItemID
						,CuringCode
						,NameTH
						,Batch
						,SUM(QTY)[QTY]
					FROM(
					SELECT T.ItemID
					,T.CuringCode 
					,I.NameTH
					,T.Batch
					,T.QTY
					FROM InventTable T
					LEFT JOIN ItemMaster I ON T.ItemID=I.ID
					WHERE 
					T.WarehouseTransReceiveDate >=? AND T.WarehouseTransReceiveDate <=?
					)Z
					GROUP BY
					Z.ItemID
					,Z.CuringCode
					,Z.NameTH
					,Z.Batch
					,Z.QTY",[$timeto,$timefrom]);
			}elseif ($counttime==2) {
				return Sqlsrv::queryJson($conn, "SELECT ROW_NUMBER() OVER(ORDER BY ItemID ASC) AS Row
						,ItemID
						,CuringCode
						,NameTH
						,Batch
						,SUM(QTY)[QTY]
					FROM(
					SELECT T.ItemID
					,T.CuringCode 
					,I.NameTH
					,T.Batch
					,T.QTY
					FROM InventTable T
					LEFT JOIN ItemMaster I ON T.ItemID=I.ID
					WHERE 
					T.WarehouseTransReceiveDate >=? AND T.WarehouseTransReceiveDate <=?
					OR T.WarehouseTransReceiveDate >=? AND T.WarehouseTransReceiveDate <=?
					)Z
					GROUP BY
					Z.ItemID
					,Z.CuringCode
					,Z.NameTH
					,Z.Batch
					,Z.QTY",[$timeto1,$timefrom1,$timeto2,$timefrom2]);
			}elseif ($counttime==3) {
				return Sqlsrv::queryJson($conn, "SELECT ROW_NUMBER() OVER(ORDER BY ItemID ASC) AS Row
						,ItemID
						,CuringCode
						,NameTH
						,Batch
						,SUM(QTY)[QTY]
					FROM(
					SELECT T.ItemID
					,T.CuringCode 
					,I.NameTH
					,T.Batch
					,T.QTY
					FROM InventTable T
					LEFT JOIN ItemMaster I ON T.ItemID=I.ID
					WHERE 
					T.WarehouseTransReceiveDate >=? AND T.WarehouseTransReceiveDate <=?
					OR T.WarehouseTransReceiveDate >=? AND T.WarehouseTransReceiveDate <=?
					OR T.WarehouseTransReceiveDate >=? AND T.WarehouseTransReceiveDate <=?
					)Z
					GROUP BY
					Z.ItemID
					,Z.CuringCode
					,Z.NameTH
					,Z.Batch
					,Z.QTY",[$timeto1,$timefrom1,$timeto2,$timefrom2,$timeto3,$timefrom3]);
			}elseif ($counttime==4) {
				return Sqlsrv::queryJson($conn, "SELECT ROW_NUMBER() OVER(ORDER BY ItemID ASC) AS Row
						,ItemID
						,CuringCode
						,NameTH
						,Batch
						,SUM(QTY)[QTY]
					FROM(
					SELECT T.ItemID
					,T.CuringCode 
					,I.NameTH
					,T.Batch
					,T.QTY
					FROM InventTable T
					LEFT JOIN ItemMaster I ON T.ItemID=I.ID
					WHERE 
					 T.WarehouseTransReceiveDate >=? AND T.WarehouseTransReceiveDate <=?
					OR T.WarehouseTransReceiveDate >=? AND T.WarehouseTransReceiveDate <=?
					OR T.WarehouseTransReceiveDate >=? AND T.WarehouseTransReceiveDate <=?
					OR T.WarehouseTransReceiveDate >=? AND T.WarehouseTransReceiveDate <=?
					)Z
					GROUP BY
					Z.ItemID
					,Z.CuringCode
					,Z.NameTH
					,Z.Batch
					,Z.QTY",[$timeto1,$timefrom1,$timeto2,$timefrom2,$timeto3,$timefrom3,$timeto4,$timefrom4]);
			}
		
		}else if ($shift=='night') {
			$datenight = str_replace('-', '/', $datewarehouse);
			$datewarehousenight = date('Y-m-d',strtotime($datenight . "+1 days"));
			$timeto_n="20:00";	$timefrom_n="08:00";
			return Sqlsrv::queryJson($conn, "SELECT ROW_NUMBER() OVER(ORDER BY ItemID ASC) AS Row
						,ItemID
						,CuringCode
						,NameTH
						,Batch
						,SUM(QTY)[QTY]
					FROM(
					SELECT T.ItemID
					,T.CuringCode 
					,I.NameTH
					,T.Batch
					,T.QTY
					FROM InventTable T
					LEFT JOIN ItemMaster I ON T.ItemID=I.ID
					WHERE CONVERT(date,T.WarehouseTransReceiveDate) BETWEEN ? AND ?
					AND CONVERT(time,T.WarehouseTransReceiveDate) >= ? AND CONVERT(time,T.WarehouseTransReceiveDate) <= ?
					)Z
					GROUP BY
					Z.ItemID
					,Z.CuringCode
					,Z.NameTH
					,Z.Batch
					,Z.QTY",[$datewarehouse,$datewarehousenight,$timeto_n,$timefrom_n]);
		}

	}

	public function GreentireInventoryServiceallpdfwarehouserecive($shift,$time,$datewarehouse,$brand)
	{
		return json_encode([]);
		exit;
		$conn = Database::connect();
		if ($shift=='day') {

			if ($counttime==1) {
				if ($time==1) {
					$timeto=$datewarehouse." 08:00:00.000";$timefrom=$datewarehouse." 11:00:00.000";
				}elseif ($time==2) {
					$timeto=$datewarehouse." 11:00:00.000";$timefrom=$datewarehouse." 14:00:00.000";
				}elseif ($time==3) {
					$timeto=$datewarehouse." 14:00:00.000";$timefrom=$datewarehouse." 17:00:00.000";
				}elseif ($time==4) {
					$timeto=$datewarehouse." 17:00:00.000";$timefrom=$datewarehouse." 20:00:00.000";
				}
			}elseif ($counttime==2) {
				if ($time=='1,2') {
					$timeto1=$datewarehouse." 08:00:00.000";$timefrom1=$datewarehouse." 11:00:00.000";
					$timeto2=$datewarehouse." 11:00:00.000";$timefrom2=$datewarehouse." 14:00:00.000";
				}elseif ($time=='1,3') {
					$timeto1=$datewarehouse." 08:00:00.000";$timefrom1=$datewarehouse." 11:00:00.000";
					$timeto2=$datewarehouse." 14:00:00.000";$timefrom2=$datewarehouse." 17:00:00.000";
				}elseif ($time=='1,4') {
					$timeto1=$datewarehouse." 08:00:00.000";$timefrom1=$datewarehouse." 11:00:00.000";
					$timeto2=$datewarehouse." 17:00:00.000";$timefrom2=$datewarehouse." 20:00:00.000";
				}elseif ($time=='2,3') {
					$timeto1=$datewarehouse." 11:00:00.000";$timefrom1=$datewarehouse." 14:00:00.000";
					$timeto2=$datewarehouse." 14:00:00.000";$timefrom2=$datewarehouse." 17:00:00.000";
				}elseif ($time=='2,4') {
					$timeto1=$datewarehouse." 11:00:00.000";$timefrom1=$datewarehouse." 14:00:00.000";
					$timeto2=$datewarehouse." 17:00:00.000";$timefrom2=$datewarehouse." 20:00:00.000";
				}elseif ($time=='3,4') {
					$timeto1=$datewarehouse." 14:00:00.000";$timefrom1=$datewarehouse." 17:00:00.000";
					$timeto2=$datewarehouse." 17:00:00.000";$timefrom2=$datewarehouse." 20:00:00.000";
				}
			}elseif ($counttime==3) {
				if ($time=='1,2,3') {
					$timeto1=$datewarehouse." 08:00:00.000";$timefrom1=$datewarehouse." 11:00:00.000";
					$timeto2=$datewarehouse." 11:00:00.000";$timefrom2=$datewarehouse." 14:00:00.000";
					$timeto3=$datewarehouse." 14:00:00.000";$timefrom3=$datewarehouse." 17:00:00.000";
				}elseif ($time=='1,2,4') {
					$timeto1=$datewarehouse." 08:00:00.000";$timefrom1=$datewarehouse." 11:00:00.000";
					$timeto2=$datewarehouse." 11:00:00.000";$timefrom2=$datewarehouse." 14:00:00.000";
					$timeto3=$datewarehouse." 17:00:00.000";$timefrom3=$datewarehouse." 20:00:00.000";
				}elseif ($time=='1,3,4') {
					$timeto1=$datewarehouse." 08:00:00.000";$timefrom1=$datewarehouse." 11:00:00.000";
					$timeto2=$datewarehouse." 14:00:00.000";$timefrom2=$datewarehouse." 17:00:00.000";
					$timeto3=$datewarehouse." 17:00:00.000";$timefrom3=$datewarehouse." 20:00:00.000";
				}elseif ($time=='2,3,4') {
					$timeto1=$datewarehouse." 11:00:00.000";$timefrom1=$datewarehouse." 14:00:00.000";
					$timeto2=$datewarehouse." 14:00:00.000";$timefrom2=$datewarehouse." 17:00:00.000";
					$timeto3=$datewarehouse." 17:00:00.000";$timefrom3=$datewarehouse." 20:00:00.000";
				}
			}elseif ($counttime==4) {
				if ($time=='1,2,3,4') {
					$timeto1=$datewarehouse." 08:00:00.000";$timefrom1=$datewarehouse." 11:00:00.000";
					$timeto2=$datewarehouse." 11:00:00.000";$timefrom2=$datewarehouse." 14:00:00.000";
					$timeto3=$datewarehouse." 14:00:00.000";$timefrom3=$datewarehouse." 17:00:00.000";
					$timeto4=$datewarehouse." 17:00:00.000";$timefrom4=$datewarehouse." 20:00:00.000";
				}
			}

			if ($counttime==1) {
				return Sqlsrv::queryJson($conn, "SELECT 
						case
						    when  ROW_NUMBER() OVER(ORDER BY ItemID ASC) < 14    then 1
						    when  ROW_NUMBER() OVER(ORDER BY ItemID ASC) < 27    then 2
						    when  ROW_NUMBER() OVER(ORDER BY ItemID ASC) < 41    then 3
						    when  ROW_NUMBER() OVER(ORDER BY ItemID ASC) < 55    then 4
						end Pages
						,ROW_NUMBER() OVER(ORDER BY ItemID ASC) AS Row
						,ItemID
						,CuringCode
						,NameTH
						,Batch
						,SUM(QTY)[QTY]
						,BrandID
						,Brand
						,Pattern
					FROM(
						SELECT T.ItemID
						,T.CuringCode 
						,I.NameTH
						,T.Batch
						,T.QTY
						,B.BrandID
						,I.Brand
						,I.Pattern
						FROM InventTable T
						LEFT JOIN ItemMaster I ON T.ItemID=I.ID
						LEFT JOIN BrandMaster B ON I.Brand=B.BrandName
						WHERE T.WarehouseReceiveDate >= ? AND T.WarehouseReceiveDate <= ?
						AND B.BrandID IN ($brand)
					)Z  
					GROUP BY
					Z.ItemID
					,Z.CuringCode
					,Z.NameTH
					,Z.Batch
					,Z.QTY
					,Z.BrandID
					,Z.Brand
					,Z.Pattern",
					[$timeto,$timefrom]);
			}elseif ($counttime==2) {
				return Sqlsrv::queryJson($conn, "SELECT 
						case
						    when  ROW_NUMBER() OVER(ORDER BY ItemID ASC) < 14    then 1
						    when  ROW_NUMBER() OVER(ORDER BY ItemID ASC) < 27    then 2
						    when  ROW_NUMBER() OVER(ORDER BY ItemID ASC) < 41    then 3
						    when  ROW_NUMBER() OVER(ORDER BY ItemID ASC) < 55    then 4
						end Pages
						,ROW_NUMBER() OVER(ORDER BY ItemID ASC) AS Row
						,ItemID
						,CuringCode
						,NameTH
						,Batch
						,SUM(QTY)[QTY]
						,BrandID
						,Brand
						,Pattern
					FROM(
					SELECT T.ItemID
					,T.CuringCode 
					,I.NameTH
					,T.Batch
					,T.QTY
					,B.BrandID
					,I.Brand
					,I.Pattern
					FROM InventTable T
					LEFT JOIN ItemMaster I ON T.ItemID=I.ID
					LEFT JOIN BrandMaster B ON I.Brand=B.BrandName
					WHERE T.WarehouseReceiveDate >= ? AND T.WarehouseReceiveDate <= ?
					OR T.WarehouseReceiveDate >= ? AND T.WarehouseReceiveDate <= ?
					
					)Z WHERE BrandID IN ($brand)
					GROUP BY
					Z.ItemID
					,Z.CuringCode
					,Z.NameTH
					,Z.Batch
					,Z.QTY
					,Z.BrandID
					,Z.Brand
					,Z.Pattern",
					[$timeto1,$timefrom1,$timeto2,$timefrom2]);
			}elseif ($counttime==3) {
				return Sqlsrv::queryJson($conn, "SELECT 
						case
						    when  ROW_NUMBER() OVER(ORDER BY ItemID ASC) < 14    then 1
						    when  ROW_NUMBER() OVER(ORDER BY ItemID ASC) < 27    then 2
						    when  ROW_NUMBER() OVER(ORDER BY ItemID ASC) < 41    then 3
						    when  ROW_NUMBER() OVER(ORDER BY ItemID ASC) < 55    then 4
						end Pages
						,ROW_NUMBER() OVER(ORDER BY ItemID ASC) AS Row
						,ItemID
						,CuringCode
						,NameTH
						,Batch
						,SUM(QTY)[QTY]
						,BrandID
						,Brand
					,Pattern
					FROM(
					SELECT T.ItemID
					,T.CuringCode 
					,I.NameTH
					,T.Batch
					,T.QTY
					,B.BrandID
					,I.Brand
					,I.Pattern
					FROM InventTable T
					LEFT JOIN ItemMaster I ON T.ItemID=I.ID
					LEFT JOIN BrandMaster B ON I.Brand=B.BrandName
					WHERE 
					T.WarehouseReceiveDate >= ? AND T.WarehouseReceiveDate <= ?
					OR T.WarehouseReceiveDate >= ? AND T.WarehouseReceiveDate <= ?
					OR T.WarehouseReceiveDate >= ? AND T.WarehouseReceiveDate <= ?
					
					)Z WHERE BrandID IN ($brand)
					GROUP BY
					Z.ItemID
					,Z.CuringCode
					,Z.NameTH
					,Z.Batch
					,Z.QTY
					,Z.BrandID
					,Z.Brand
					,Z.Pattern",
					[$timeto1,$timefrom1,$timeto2,$timefrom2,$timeto3,$timefrom3]);
			}elseif ($counttime==4) {
				return Sqlsrv::queryJson($conn, "SELECT 
						case
						    when  ROW_NUMBER() OVER(ORDER BY ItemID ASC) < 14    then 1
						    when  ROW_NUMBER() OVER(ORDER BY ItemID ASC) < 27    then 2
						    when  ROW_NUMBER() OVER(ORDER BY ItemID ASC) < 41    then 3
						    when  ROW_NUMBER() OVER(ORDER BY ItemID ASC) < 55    then 4
						end Pages
						,ROW_NUMBER() OVER(ORDER BY ItemID ASC) AS Row
						,ItemID
						,CuringCode
						,NameTH
						,Batch
						,SUM(QTY)[QTY]
						,BrandID
						,Brand
					,Pattern
					FROM(
					SELECT T.ItemID
					,T.CuringCode 
					,I.NameTH
					,T.Batch
					,T.QTY
					,B.BrandID
					,I.Brand
					,I.Pattern
					FROM InventTable T
					LEFT JOIN ItemMaster I ON T.ItemID=I.ID
					LEFT JOIN BrandMaster B ON I.Brand=B.BrandName
					WHERE 
					T.WarehouseReceiveDate >= ? AND T.WarehouseReceiveDate <= ?
					OR T.WarehouseReceiveDate >= ? AND T.WarehouseReceiveDate <= ?
					OR T.WarehouseReceiveDate >= ? AND T.WarehouseReceiveDate <= ?
					OR T.WarehouseReceiveDate >= ? AND T.WarehouseReceiveDate <= ?
					
					)Z WHERE BrandID IN ($brand)
					GROUP BY
					Z.ItemID
					,Z.CuringCode
					,Z.NameTH
					,Z.Batch
					,Z.QTY
					,Z.BrandID
					,Z.Brand
					,Z.Pattern",
					[$timeto1,$timefrom1,$timeto2,$timefrom2,$timeto3,$timefrom3,$timeto4,$timefrom4]);
			}
		
		}else if ($shift=='night') {
			$datenight = str_replace('-', '/', $datewarehouse);
			$datewarehousenight = date('Y-m-d',strtotime($datenight . "+1 days"));
			$timeto_n="20:00:00.000";	$timefrom_n="08:00:00.000";
			$date_1 = $datewarehouse." ".$timeto_n;
			$date_2 = $datewarehousenight." ".$timefrom_n;
			return Sqlsrv::queryJson($conn, "SELECT 
						case
						    when  ROW_NUMBER() OVER(ORDER BY ItemID ASC) < 14    then 1
						    when  ROW_NUMBER() OVER(ORDER BY ItemID ASC) < 27    then 2
						    when  ROW_NUMBER() OVER(ORDER BY ItemID ASC) < 41    then 3
						    when  ROW_NUMBER() OVER(ORDER BY ItemID ASC) < 55    then 4
						end Pages
						,ROW_NUMBER() OVER(ORDER BY ItemID ASC) AS Row
						,ItemID
						,CuringCode
						,NameTH
						,Batch
						,SUM(QTY)[QTY]
						,BrandID
					FROM(
					SELECT T.ItemID
					,T.CuringCode 
					,I.NameTH
					,T.Batch
					,T.QTY
					,B.BrandID
					,I.Brand
					,I.Pattern
					FROM InventTable T
					LEFT JOIN ItemMaster I ON T.ItemID=I.ID
					LEFT JOIN BrandMaster B ON I.Brand=B.BrandName
					WHERE T.WarehouseReceiveDate BETWEEN ? AND ?
					)Z WHERE BrandID IN ($brand)
					GROUP BY
					Z.ItemID
					,Z.CuringCode
					,Z.NameTH
					,Z.Batch
					,Z.QTY
					,Z.BrandID
					,I.Brand
					,I.Pattern",
					[$date_1,$date_2]);
		}

	}

	public function CuringServiceallpresspdf($datecuring,$press,$shift)
	{
		$datenight = str_replace('-', '/', $datecuring);
		$datecuringnight = date('Y-m-d',strtotime($datenight . "+1 days"));
		$date1 = $datecuring.' 08:00:01';	
		$date2 = $datecuring.' 20:00:00';
		$date3 = $datecuring.' 20:00:01';	
		$date4 = $datecuringnight.' 08:00:00';
		$conn = Database::connect();

		if ($shift=="day") {
			return Sqlsrv::queryJson($conn, "SELECT *
			FROM(
				SELECT 
				S1.CuringTime [S1_CuringTime],
				S1.Row [S1_Row],
				S1.TemplateSerialNo [S1_TemplateSerialNo],
				S1.Barcode [S1_Barcode],
				S1.Description [S1_Description],
				S1.CuringCode [S1_CuringCode],
				S1.PressSide [S1_PressSide],
				S2.CuringTime [S2_CuringTime],
				S2.Row [S2_Row],
				S2.TemplateSerialNo [S2_TemplateSerialNo],
				S2.Barcode [S2_Barcode],
				S2.Description [S2_Description],
				S2.CuringCode [S2_CuringCode],
				S2.PressSide [S2_PressSide]
			FROM 
			(

			SELECT CONVERT(time,I.CuringDate) CuringTime
					,ROW_NUMBER() OVER(ORDER BY TemplateSerialNo ASC) AS Row
					,I.TemplateSerialNo
					,I.Barcode
					,S.Description
					,I.PressNo
					,I.CuringCode
					,I.PressSide
			FROM InventTable I
			LEFT JOIN InventTrans T ON I.DateBuild=T.CreateDate AND I.Barcode=T.Barcode AND T.QTY>0
			LEFT JOIN ShiftMaster S ON T.Shift=S.ID
			WHERE I.CuringDate BETWEEN ? AND ?
			AND I.PressSide='L' AND I.PressNo=?
				GROUP BY
					I.CuringDate
					-- CONVERT(time,I.CuringDate)
					,I.TemplateSerialNo
					,I.Barcode
					,S.Description
					,I.PressNo
					,I.CuringCode
					,I.PressSide
			)S1

			FULL JOIN 

			(
			SELECT CONVERT(time,I.CuringDate)CuringTime
					,ROW_NUMBER() OVER(ORDER BY TemplateSerialNo ASC) AS Row
					,I.TemplateSerialNo
					,I.Barcode
					,S.Description
					,I.PressNo
					,I.CuringCode
					,I.PressSide
			FROM InventTable I
			LEFT JOIN InventTrans T ON I.DateBuild=T.CreateDate AND I.Barcode=T.Barcode AND T.QTY>0
			LEFT JOIN ShiftMaster S ON T.Shift=S.ID
			WHERE I.CuringDate BETWEEN ? AND ?
			AND I.PressSide='R' AND I.PressNo=?
				GROUP BY
				I.CuringDate
					-- CONVERT(time,I.CuringDate)
					,I.TemplateSerialNo
					,I.Barcode
					,S.Description	
					,I.PressNo
					,I.CuringCode
					,I.PressSide
			)S2

			ON S1.Row = S2.Row
			)TABLE1",[$date1,$date2,$press,$date1,$date2,$press]);
		}else{
			return Sqlsrv::queryJson($conn, "SELECT *
			FROM(
			SELECT 
			S1.CuringTime [S1_CuringTime],
			S1.Row [S1_Row],
			S1.TemplateSerialNo [S1_TemplateSerialNo],
			S1.Barcode [S1_Barcode],
			S1.Description [S1_Description],
			S1.CuringCode [S1_CuringCode],
			S1.PressSide [S1_PressSide],
			S2.CuringTime [S2_CuringTime],
			S2.Row [S2_Row],
			S2.TemplateSerialNo [S2_TemplateSerialNo],
			S2.Barcode [S2_Barcode],
			S2.Description [S2_Description],
			S2.CuringCode [S2_CuringCode],
			S2.PressSide [S2_PressSide]
			FROM 
			(

			SELECT CONVERT(time,I.CuringDate)CuringTime
					,ROW_NUMBER() OVER(ORDER BY TemplateSerialNo ASC) AS Row
					,I.TemplateSerialNo
					,I.Barcode
					,S.Description
					,I.PressNo
					,I.CuringCode
					,I.PressSide
			FROM InventTable I
			LEFT JOIN InventTrans T ON I.DateBuild=T.CreateDate AND I.Barcode=T.Barcode AND T.QTY>0
			LEFT JOIN ShiftMaster S ON T.Shift=S.ID
			WHERE I.CuringDate BETWEEN ? AND ?
			AND I.PressSide='L' AND I.PressNo=?
				GROUP BY
					CONVERT(time,I.CuringDate)
					,I.TemplateSerialNo
					,I.Barcode
					,S.Description
					,I.PressNo
					,I.CuringCode
					,I.PressSide
			)S1

			FULL JOIN 

			(
			SELECT CONVERT(time,I.CuringDate)CuringTime
					,ROW_NUMBER() OVER(ORDER BY TemplateSerialNo ASC) AS Row
					,I.TemplateSerialNo
					,I.Barcode
					,S.Description
					,I.PressNo
					,I.CuringCode
					,I.PressSide
			FROM InventTable I
			LEFT JOIN InventTrans T ON I.DateBuild=T.CreateDate AND I.Barcode=T.Barcode AND T.QTY>0
			LEFT JOIN ShiftMaster S ON T.Shift=S.ID
			WHERE I.CuringDate BETWEEN ? AND ?
			AND I.PressSide='R' AND I.PressNo=?
				GROUP BY
					CONVERT(time,I.CuringDate)
					,I.TemplateSerialNo
					,I.Barcode
					,S.Description	
					,I.PressNo
					,I.CuringCode
					,I.PressSide
			)S2

			ON S1.Row = S2.Row
			)TABLE1",[$date3,$date4,$press,$date3,$date4,$press]);
		}
		
	}

	public function CuringServiceallpresspdfGTL($datecuring,$press,$shift)
	{
		$datenight = str_replace('-', '/', $datecuring);
		$datecuringnight = date('Y-m-d',strtotime($datenight . "+1 days"));
		$date1 = $datecuring.' 08:00:01';	$date2 = $datecuring.' 20:00:00';
		$date3 = $datecuring.' 20:00:01';	$date4 = $datecuringnight.' 08:00:00';
		
		$conn = Database::connect();
		if ($shift=="day") {
			return Sqlsrv::queryJson($conn, "SELECT GT_Code 
			FROM InventTable 
			WHERE CuringDate BETWEEN ? AND ? 
			AND PressSide='L' AND PressNo=?
			GROUP BY GT_Code",[$date1,$date2,$press]);
		}else{
			return Sqlsrv::queryJson($conn, "SELECT GT_Code 
			FROM InventTable 
			WHERE CuringDate BETWEEN ? AND ? 
			-- WHERE CuringDate BETWEEN '2017-05-04 20:00:00' AND '2017-05-05 08:00:00'
			AND PressSide='L' AND PressNo=?
			GROUP BY GT_Code",[$date3,$date4,$press]);
		}
	}

	public function CuringServiceallpresspdfGTR($datecuring,$press,$shift)
	{
		$datenight = str_replace('-', '/', $datecuring);
		$datecuringnight = date('Y-m-d',strtotime($datenight . "+1 days"));
		$date1 = $datecuring.' 08:00:01';	$date2 = $datecuring.' 20:00:00';
		$date3 = $datecuring.' 20:00:01';	$date4 = $datecuringnight.' 08:00:00';
		$conn = Database::connect();
		if ($shift=="day") {
			return Sqlsrv::queryJson($conn, "SELECT GT_Code 
			FROM InventTable 
			WHERE CuringDate BETWEEN ? AND ? 
			AND PressSide='R' AND PressNo=?
			GROUP BY GT_Code",[$date1,$date2,$press]);
		}else{
			return Sqlsrv::queryJson($conn, "SELECT GT_Code 
			FROM InventTable 
			WHERE CuringDate BETWEEN ? AND ? 
			AND PressSide='R' AND PressNo=?
			GROUP BY GT_Code",[$date3,$date4,$press]);
		}
	}

	public function CuringServiceallpresspdfweekly($datecuring,$press,$shift)
	{
		$datenight = str_replace('-', '/', $datecuring);
		$datecuringnight = date('Y-m-d',strtotime($datenight . "+1 days"));
		$date1 = $datecuring.' 08:00:01';	$date2 = $datecuring.' 20:00:00';
		$date3 = $datecuring.' 20:00:01';	$date4 = $datecuringnight.' 08:00:00';
		$conn = Database::connect();
		if ($shift=="day") {
			return Sqlsrv::queryJson($conn, "SELECT Batch 
			FROM InventTable  
			WHERE CuringDate BETWEEN ? AND ? AND PressNo=? 
			GROUP BY Batch",[$date1,$date2,$press]);
		}else{
			return Sqlsrv::queryJson($conn, "SELECT Batch 
			FROM InventTable  
			WHERE CuringDate BETWEEN ? AND ? AND PressNo=? 
			GROUP BY Batch",[$date3,$date4,$press]);
		}
	}
	
	public function CuringServiceallpresspdfCurcodeL($datecuring,$press,$shift)
	{
		$datenight = str_replace('-', '/', $datecuring);
		$datecuringnight = date('Y-m-d',strtotime($datenight . "+1 days"));
		$date1 = $datecuring.' 08:00:01';	$date2 = $datecuring.' 20:00:00';
		$date3 = $datecuring.' 20:00:01';	$date4 = $datecuringnight.' 08:00:00';
		$conn = Database::connect();
		if ($shift=="day") {
			return Sqlsrv::queryJson($conn, "SELECT CuringCode 
			FROM InventTable 
			WHERE CuringDate BETWEEN ? AND ?
			AND PressSide='L' AND PressNo=?
			GROUP BY CuringCode",[$date1,$date2,$press]);
		}else{
			return Sqlsrv::queryJson($conn, "SELECT CuringCode 
			FROM InventTable 
			WHERE CuringDate BETWEEN ? AND ?
			AND PressSide='L' AND PressNo=?
			GROUP BY CuringCode",[$date3,$date4,$press]);
		}
	}

	public function CuringServiceallpresspdfCurcodeR($datecuring,$press,$shift)
	{
		$datenight = str_replace('-', '/', $datecuring);
		$datecuringnight = date('Y-m-d',strtotime($datenight . "+1 days"));
		$date1 = $datecuring.' 08:00:01';	$date2 = $datecuring.' 20:00:00';
		$date3 = $datecuring.' 20:00:01';	$date4 = $datecuringnight.' 08:00:00';
		$conn = Database::connect();
		if ($shift=="day") {
			return Sqlsrv::queryJson($conn, "SELECT CuringCode 
			FROM InventTable 
			WHERE CuringDate BETWEEN ? AND ?
			AND PressSide='R' AND PressNo=?
			GROUP BY CuringCode",[$date1,$date2,$press]);
		}else{
			return Sqlsrv::queryJson($conn, "SELECT CuringCode 
			FROM InventTable 
			WHERE CuringDate BETWEEN ? AND ?
			AND PressSide='R' AND PressNo=?
			GROUP BY CuringCode",[$date3,$date4,$press]);
		}
	}

	public function curingAx($date, $shift, $product_group)
	{
		$date_today = date('Y-m-d', strtotime($date));
		$date_tomorrow = date('Y-m-d', strtotime($date . '+1 days'));
		// echo $date_tomorrow; exit;
		$conn = Database::connect();
		if ($shift === 'day') {
			return Sqlsrv::queryJson(
				$conn,
				"SELECT 
				IT.Barcode,
				IT.CuringCode,
				IT.ItemID as ItemNo,
				IT.CuringDate,
				(
					SELECT 
						CASE 
							WHEN IT.CuringDate BETWEEN '$date_today 08:00:01' AND '$date_today 10:00:00' THEN 1
							ELSE 0
						END 
				) as Q1,
				(
					SELECT 
						CASE 
							WHEN IT.CuringDate BETWEEN '$date_today 10:00:01' AND '$date_today 12:00:00' THEN 1
							ELSE 0
						END 
				) as Q2,
				(
					SELECT 
						CASE 
							WHEN IT.CuringDate BETWEEN '$date_today 12:00:01' AND '$date_today 14:00:00' THEN 1
							ELSE 0
						END 
				) as Q3,
				(
					SELECT 
						CASE 
							WHEN IT.CuringDate BETWEEN '$date_today 14:00:01' AND '$date_today 16:00:00' THEN 1
							ELSE 0
						END 
				) as Q4,
				(
					SELECT 
						CASE 
							WHEN IT.CuringDate BETWEEN '$date_today 16:00:01' AND '$date_today 18:00:00' THEN 1
							ELSE 0
						END 
				) as Q5,
				(
					SELECT 
						CASE 
							WHEN IT.CuringDate BETWEEN '$date_today 18:00:01' AND '$date_today 20:00:00' THEN 1
							ELSE 0
						END 
				) as Q6
				FROM InventTable IT 
				WHERE IT.CuringDate IS NOT NULL 
				AND IT.CuringDate <> ''
				AND IT.CuringDate between '$date_today 08:00:01' and '$date_today 20:00:00'
				AND IT.CheckBuild = 1
				AND IT.GT_Code IN 
				(
					SELECT TOP 1 CCM.GreentireID FROM CureCodeMaster CCM 
					LEFT JOIN ItemMaster IM ON CCM.ItemID = IM.ID
					WHERE CCM.GreentireID = IT.GT_Code
					AND IM.ProductGroup = ?
				)
				AND CONVERT(date, IT.CuringDate) = ?",
				[
					$product_group,
					$date
				]
			);
		} else {
			return Sqlsrv::queryJson(
				$conn,
				"SELECT 
				IT.Barcode,
				IT.CuringCode,
				IT.ItemID as ItemNo,
				IT.CuringDate,
				(
					SELECT 
						CASE 
							WHEN IT.CuringDate BETWEEN '$date_today 20:00:01' AND '$date_today 22:00:00' THEN 1
							ELSE 0
						END 
				) as Q1,
				(
					SELECT 
						CASE 
							WHEN IT.CuringDate BETWEEN '$date_today 22:00:01' AND '$date_tomorrow 00:00:00' THEN 1
							ELSE 0
						END 
				) as Q2,
				(
					SELECT 
						CASE 
							WHEN IT.CuringDate BETWEEN '$date_tomorrow 00:00:01' AND '$date_tomorrow 02:00:00' THEN 1
							ELSE 0
						END 
				) as Q3,
				(
					SELECT 
						CASE 
							WHEN IT.CuringDate BETWEEN '$date_tomorrow 02:00:01' AND '$date_tomorrow 04:00:00' THEN 1
							ELSE 0
						END 
				) as Q4,
				(
					SELECT 
						CASE 
							WHEN IT.CuringDate BETWEEN '$date_tomorrow 04:00:01' AND '$date_tomorrow 06:00:00' THEN 1
							ELSE 0
						END 
				) as Q5,
				(
					SELECT 
						CASE 
							WHEN IT.CuringDate BETWEEN '$date_tomorrow 06:00:01' AND '$date_tomorrow 08:00:00' THEN 1
							ELSE 0
						END 
				) as Q6
				FROM InventTable IT 
				WHERE IT.CuringDate IS NOT NULL 
				AND IT.CheckBuild = 1
				AND IT.CuringDate <> ''
				AND IT.CuringDate between '$date_today 20:00:01' and '$date_tomorrow 08:00:00'
				AND IT.GT_Code IN 
				(
					SELECT TOP 1 CCM.GreentireID FROM CureCodeMaster CCM 
					LEFT JOIN ItemMaster IM ON CCM.ItemID = IM.ID
					WHERE CCM.GreentireID = IT.GT_Code
					AND IM.ProductGroup = ?
				)",
				[
					$product_group
				]
			);
		}
	}

	public function CureInventoryServiceallpdf($product_group)
	{
		$conn = Database::connect();
		return Sqlsrv::queryJson($conn, "SELECT ROW_NUMBER() OVER(ORDER BY CuringDate ASC) AS Row
					,CuringDate
					,CuringCode
					,PressNo
					,PressSide
					,TemplateSerialNo
					,Barcode
			FROM(
			SELECT	I.CuringDate
					,I.CuringCode
					,I.PressNo
					,I.PressSide
					,I.TemplateSerialNo
					,I.Barcode
			FROM InventTable I
			WHERE I.FinalReceiveDate IS NULL 
			AND I.Status=1 AND I.WarehouseID=4
			AND I.CuringDate IS NOT NULL
			AND I.GT_Code IN 
			(
				SELECT TOP 1 CCM.GreentireID FROM CureCodeMaster CCM 
				LEFT JOIN ItemMaster IM ON CCM.ItemID = IM.ID
				WHERE CCM.GreentireID = I.GT_Code
				AND IM.ProductGroup = ?
			)
			)Z
			GROUP BY
			Z.CuringDate,
			Z.CuringCode,
			Z.PressNo,
			Z.PressSide,
			Z.TemplateSerialNo,
			Z.Barcode ORDER BY Z.CuringDate ASC",
			[
				$product_group
			]
		);
	}

	public function greentireInventoryV2($product_group) {

		$conn = Database::connect();
		return Sqlsrv::queryJson(
			$conn,
			"SELECT 
			A.Batch, A.GT_Code,  SUM(A.hold) [hold], SUM(A.repair) [repair], SUM(A.onhand) [onhand] 
			from (
			--Onhand
			select IT.Batch, IT.GT_Code,  0 [hold], 0 [repair], IT.QTY [onhand] 
			from InventTable IT 
			where IT.WarehouseID = 1
			and IT.LocationID = 2
			and DisposalID <> 11
			and Status <> 4
			and IT.CheckBuild = 1
			AND IT.GT_Code IN 
			(
				SELECT TOP 1 CCM.GreentireID FROM CureCodeMaster CCM 
				LEFT JOIN ItemMaster IM ON CCM.ItemID = IM.ID
				WHERE CCM.GreentireID = IT.GT_Code
				AND IM.ProductGroup = ?
			)

			union all

			-- Hold
			select IT.Batch, IT.GT_Code, IT.QTY [hold], 0 [repair], 0 [onhand]
			from InventTable IT 
			where IT.WarehouseID = 1
			--and IT.LocationID = 4 
			and DisposalID  = 10
			and Status = 5
			and IT.CheckBuild = 1
			AND IT.GT_Code IN 
			(
				SELECT TOP 1 CCM.GreentireID FROM CureCodeMaster CCM 
				LEFT JOIN ItemMaster IM ON CCM.ItemID = IM.ID
				WHERE CCM.GreentireID = IT.GT_Code
				AND IM.ProductGroup = ?
			)

			union all

			-- Repair
			select IT.Batch, IT.GT_Code, 0 [hold],  IT.QTY [repair],0 [onhand] 
			from InventTable IT 
			where IT.WarehouseID = 1
			and DisposalID  = 12
			and Status = 5
			and IT.CheckBuild = 1
			AND IT.GT_Code IN 
			(
				SELECT TOP 1 CCM.GreentireID FROM CureCodeMaster CCM 
				LEFT JOIN ItemMaster IM ON CCM.ItemID = IM.ID
				WHERE CCM.GreentireID = IT.GT_Code
				AND IM.ProductGroup = ?
			)
			) A group by A.Batch, A.GT_Code",
			[
				$product_group,
				$product_group,
				$product_group
			]
		);
	}

	public function WIPServiceallpdf($product_group){
		// edit by harit 1/2/18
		$conn = Database::connect();
		return Sqlsrv::queryJson(
			$conn,
			"SELECT 
			A.Batch, A.CureCode, A.NameTH, SUM(A.hold) [hold], SUM(A.repair) [repair], SUM(A.onhand) [onhand] ,SUM(A.retur) [return]
			from (
			--Onhand
			select IT.Batch, CCM.ID [CureCode], IM.NameTH, 0 [hold], 0 [repair], IT.QTY [onhand] ,0 [retur]
			from InventTable IT 
			left join CureCodeMaster CCM ON CCM.ID = IT.CuringCode
			left join ItemMaster IM ON IM.ID = CCM.ItemID
			where IT.WarehouseID = 2 
			and IT.LocationID = 4 
			and DisposalID <> 11
			and Status <> 4
			AND IT.GT_Code IN 
			(
				SELECT TOP 1 CCM.GreentireID FROM CureCodeMaster CCM 
				LEFT JOIN ItemMaster IM ON CCM.ItemID = IM.ID
				WHERE CCM.GreentireID = IT.GT_Code
				AND IM.ProductGroup = ?
			)

			union all

			-- Hold
			select IT.Batch, CCM.ID [CureCode], IM.NameTH, IT.QTY [hold], 0 [repair], 0 [onhand] ,0 [retur]
			from InventTable IT 
			left join CureCodeMaster CCM ON CCM.ID = IT.CuringCode
			left join ItemMaster IM ON IM.ID = CCM.ItemID
			where IT.WarehouseID = 2 
			--and IT.LocationID = 4 
			and DisposalID  = 10
			and Status = 5
			AND IT.GT_Code IN 
			(
				SELECT TOP 1 CCM.GreentireID FROM CureCodeMaster CCM 
				LEFT JOIN ItemMaster IM ON CCM.ItemID = IM.ID
				WHERE CCM.GreentireID = IT.GT_Code
				AND IM.ProductGroup = ?
			)

			union all

			-- Repair
			select IT.Batch, CCM.ID [CureCode], IM.NameTH,0 [hold],  IT.QTY [repair],0 [onhand] ,0 [retur]
			from InventTable IT 
			left join CureCodeMaster CCM ON CCM.ID = IT.CuringCode
			left join ItemMaster IM ON IM.ID = CCM.ItemID
			where IT.WarehouseID = 2 
			and DisposalID  = 12
			and Status = 5
			AND IT.GT_Code IN 
			(
				SELECT TOP 1 CCM.GreentireID FROM CureCodeMaster CCM 
				LEFT JOIN ItemMaster IM ON CCM.ItemID = IM.ID
				WHERE CCM.GreentireID = IT.GT_Code
				AND IM.ProductGroup = ?
			)
			
			union all
			-- Return
			select IT.Batch, CCM.ID [CureCode], IM.NameTH,0 [hold], 0 [repair],0 [onhand] ,IT.QTY [retur]
			from InventTable IT 
			left join CureCodeMaster CCM ON CCM.ID = IT.CuringCode
			left join ItemMaster IM ON IM.ID = CCM.ItemID
			where IT.WarehouseID = 2 
			and DisposalID  = 9
			and Status = 5
			AND IT.GT_Code IN 
			(
				SELECT TOP 1 CCM.GreentireID FROM CureCodeMaster CCM 
				LEFT JOIN ItemMaster IM ON CCM.ItemID = IM.ID
				WHERE CCM.GreentireID = IT.GT_Code
				AND IM.ProductGroup = ?
			)
			
			) A group by A.Batch, A.CureCode, A.NameTH",
			[
				$product_group,
				$product_group,
				$product_group,
				$product_group
			]
		);

		// return Sqlsrv::queryJson($conn, "SELECT ROW_NUMBER() OVER(ORDER BY CodeID ASC) AS Row
		// 		,CodeID
		// 		,ID
		// 		,batch
		// 		,item_name
		// 		,SUM(onhand)[onhand]
		// 		,SUM(hold)[hold]
		// 		,SUM(repair)[repair]
		// FROM(
		// SELECT	O.CodeID,
		// 	C.ID,
		// 	O.Batch[batch],
		// 	I.NameTH[item_name],
		// 	(SELECT O.QTY WHERE O.WarehouseID=2 AND O.LocationID=4)[onhand],
		// 	(SELECT O.QTY WHERE O.WarehouseID=2 AND O.LocationID=11)[hold],
		// 	(SELECT O.QTY WHERE O.WarehouseID=2 AND O.LocationID=12)[repair]
		// FROM Onhand O
		// LEFT JOIN CureCodeMaster C ON O.CodeID=C.ItemID
		// LEFT JOIN ItemMaster I ON I.ID = O.CodeID
		// WHERE O.WarehouseID =2 AND O.QTY IS NOT NULL AND O.QTY!=0
		// )Z
		// GROUP BY
		// Z.CodeID,
		// Z.ID,
		// Z.batch,
		// Z.item_name");
	}

	public function cureCodeMasterReport()
	{
		$conn = Database::connect();
		return Sqlsrv::queryJson(
			$conn,
			"SELECT
			IM.ID AS ITEMID,
			IM.NameTH AS ITEMNAME,
			IM.Pattern AS PATTERN,
			IM.Brand AS BRAND,
			CM.GreentireID AS GTCODE,
			CM.ID AS CURECODE
			FROM ItemMaster IM
			LEFT JOIN CureCodeMaster CM ON IM.ID = CM.ItemID
			WHERE CM.ID IS NOT NULL"
		);
	}

	public function curingPress($date_curing, $press_no, $shift)
	{
		$conn = Database::connect();
		$shift_name = '';
		if ($shift === 'day') {
			// day
			$start_date = date('Y-m-d', strtotime($date_curing)) . ' 08:00:00';
			$end_date = date('Y-m-d', strtotime($date_curing)) . ' 20:00:00';

			$shift_name = 'กลางวัน';
		} else {
			// night
			$start_date = date('Y-m-d', strtotime($date_curing)) . ' 20:00:00';
			$end_date = date('Y-m-d', strtotime($date_curing . '+1 days')) . ' 08:00:00';
			$shift_name = 'กลางคืน';
		}

		// return $start_date . ' / ' . $end_date;
		
		$L = Sqlsrv::queryJson(
			$conn,
			"SELECT * , (
				SELECT TOP 1 SM.Description FROM InventTrans ITS
				LEFT JOIN ShiftMaster SM ON SM.ID = ITS.Shift
				WHERE ITS.Barcode = IT.Barcode 
				AND ITS.DisposalID = 1
				AND ITS.WarehouseID = 1
				AND ITS.DocumentTypeID = 1
				ORDER BY ITS.CreateDate ASC
			) [Shift]
			FROM InventTable IT
			WHERE IT.PressSide = 'L' 
			AND IT.CuringDate BETWEEN '$start_date' AND '$end_date'
			AND IT.PressNo = '$press_no'
			AND IT.CuringDate IS NOT NULL
			ORDER BY IT.CuringDate ASC"
		);

		$R = Sqlsrv::queryJson(
			$conn,
			"SELECT * , (
				SELECT TOP 1 SM.Description FROM InventTrans ITS
				LEFT JOIN ShiftMaster SM ON SM.ID = ITS.Shift
				WHERE ITS.Barcode = IT.Barcode 
				AND ITS.DisposalID = 1
				AND ITS.WarehouseID = 1
				AND ITS.DocumentTypeID = 1
				ORDER BY ITS.CreateDate ASC
			) [Shift]
			FROM InventTable IT
			WHERE IT.PressSide = 'R' 
			AND IT.CuringDate BETWEEN '$start_date' AND '$end_date'
			AND IT.PressNo = '$press_no'
			AND IT.CuringDate IS NOT NULL
			ORDER BY IT.CuringDate ASC"
		);
		
		return [
			'L' => $L,
			'R' => $R,
			'shift' => $shift_name,
			'date_curing' => $date_curing,
			'weekly' => (new Utils)->getWeek($date_curing)
		];
	}

	public function buildingMachine($date, $shift, $machine)
	{
		if ($shift === 'day') {
			$start_date = date('Y-m-d', strtotime($date)) . ' 08:00:00';
			$end_date 	= date('Y-m-d', strtotime($date)) . ' 20:00:00';
			$shift 		= 'A';
		}else{
			$start_date = date('Y-m-d', strtotime($date)) . ' 20:00:00';
			$end_date 	= date('Y-m-d', strtotime($date. '+1 days')) . ' 08:00:00';
			$shift 		= 'B';
		}
		$conn = Database::connect();
		return Sqlsrv::queryJson(
			$conn,
			"SELECT 
				IT.BuildingNo
				,IT.GT_Code
				,IT.Barcode
				,IT.CreateDate
				,SH.Description
				,U.Name
				,IT.DisposalID
				,D.DisposalDesc
				FROM InventTable IT JOIN
				     InventTrans T
				     ON T.Barcode = IT.Barcode
				     AND T.CreateDate = IT.CreateDate
				     JOIN UserMaster U
				     ON U.ID = IT.CreateBy
				     JOIN ShiftMaster SH
				     ON SH.ID = T.Shift
				     JOIN DisposalToUseIn D
				     ON IT.DisposalID = D.ID
				WHERE IT.CreateDate >= '$start_date' AND IT.CreateDate <= '$end_date'
				AND IT.BuildingNo IN ($machine)
				AND IT.CheckBuild=1
				ORDER BY IT.BuildingNo,IT.CreateDate ASC"
		);
	}

	public function Loading($pickingListId,$orderId,$createDate)
	{
		$conn = Database::connect();
		return Sqlsrv::queryJson(
			$conn,
			"SELECT A.warehouse_desc,A.location_desc,A.ItemId,A.NameTH,A.BatchNo,SUM(A.AX_QTY)AX_QTY,SUM(A.STR_QTY)STR_QTY
			FROM
			(
						SELECT ItemId,NameTH,BatchNo,0[AX_QTY],SUM(QTY)[STR_QTY],warehouse_desc,location_desc
						FROM(
									SELECT LT.* , 
									WM.Description as warehouse_desc, 
									L.Description as location_desc ,
									LS.Description as StatusDesc,
									UM.Name as Fullname,
									IT.TemplateSerialNo as SerialName,
									IM.NameTH
									FROM LoadingTrans LT
									LEFT JOIN InventTable IT ON LT.Barcode = IT.Barcode
									LEFT JOIN WarehouseMaster WM ON WM.ID = IT.WarehouseID
									LEFT JOIN Location L ON L.ID = IT.LocationID
									LEFT JOIN LoadingStatus LS ON LS.ID = LT.Status
									LEFT JOIN UserMaster UM ON LT.CreatedBy = UM.ID
									LEFT JOIN ItemMaster IM ON LT.ItemId = IM.ID
									WHERE LT.OrderId = '$orderId'
									AND LT.PickingListId = '$pickingListId'
									AND LT.Status<>6
						) Z
						GROUP BY Z.ItemId,Z.NameTH,Z.warehouse_desc,Z.location_desc,Z.BatchNo
						
						UNION
						
						SELECT CPT.ITEMID[ItemId],IT.DSGTHAIITEMDESCRIPTION[NameTH],ID.INVENTBATCHID[BatchNo],ABS(SUM(FLOOR(IVT.QTY)))[AX_QTY],0[STR_QTY],'Warehouse FG'warehouse_desc,'Loading'location_desc
						FROM [LIVE].[DSL_AX40_SP1_LIVE].[dbo].[SALESTABLE] SO JOIN 
							 (
							  SELECT 
							  MAX(CJ.PACKINGSLIPID)PACKINGSLIPID
							  ,CJ.DATAAREAID
							  ,CJ.SALESID
							  FROM [LIVE].[DSL_AX40_SP1_LIVE].[dbo].[CUSTPACKINGSLIPJOUR] CJ
							  GROUP BY CJ.DATAAREAID,CJ.SALESID
							 )CJ ON CJ.DATAAREAID = SO.DATAAREAID
							 AND CJ.SALESID = SO.SALESID
							 JOIN [LIVE].[DSL_AX40_SP1_LIVE].[dbo].[CUSTPACKINGSLIPTRANS] CPT 
							 ON CPT.DATAAREAID = CJ.DATAAREAID
							 AND CPT.PACKINGSLIPID = CJ.PACKINGSLIPID
							 JOIN [LIVE].[DSL_AX40_SP1_LIVE].[dbo].[INVENTTRANS] IVT
							 ON IVT.DATAAREAID = CPT.DATAAREAID
							 AND IVT.INVENTTRANSID = CPT.INVENTTRANSID
							 JOIN [LIVE].[DSL_AX40_SP1_LIVE].[dbo].[INVENTDIM] ID
							 ON ID.INVENTDIMID = IVT.INVENTDIMID
							 AND ID.DATAAREAID = IVT.DATAAREAID
							 JOIN [LIVE].[DSL_AX40_SP1_LIVE].[dbo].[INVENTTABLE] IT
							 ON CPT.ITEMID=IT.ITEMID
							 AND IT.DATAAREAID<>'dv'
						WHERE SO.SALESID = '$orderId'
						AND SO.DATAAREAID = 'STR'
						GROUP BY
						CPT.ITEMID,IT.DSGTHAIITEMDESCRIPTION,ID.INVENTBATCHID
						-- SO.SALESID,CJ.PACKINGSLIPID,CPT.ITEMID,IT.DSGTHAIITEMDESCRIPTION,CPT.INVENTTRANSID,IVT.INVENTDIMID
						-- ,ID.INVENTLOCATIONID,ID.INVENTBATCHID
						-- ,ID.WMSLOCATIONID 
			)A
			GROUP BY A.warehouse_desc,A.location_desc,A.ItemId,A.NameTH,A.BatchNo"
		);
	}
}