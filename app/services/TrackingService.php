<?php

namespace App\Services;

use Wattanar\Sqlsrv;
use App\Components\Database;
use App\Components\Security;
use Respect\Validation\Validator as V;
class TrackingService
{
	public function searchByBarcode()
	{
		$conn = Database::connect();
		$barcode = Security::_decode(trim($_POST["search"]));

		$str_len = strlen(trim($barcode));

		if ((int)$str_len === 9) {
			$field = 'TemplateSerialNo';
		} else {
			$isBarcodeFoil = sqlsrv_has_rows(sqlsrv_query(
				$conn,
				"SELECT BarcodeFoil FROM InventTable
				WHERE BarcodeFoil = ?
				AND BarcodeFoil IS NOT NULL",
				[$barcode]
			));

			if ($isBarcodeFoil === true) {
				$field = 'BarcodeFoil';
			} else {
				$field = 'Barcode';
			}
		}

		
		
		if (!$conn) {
			echo json_encode(["status" => 404, "message" => "connection failed"]);
			exit;
		}

		if (!V::stringType()->notEmpty()->validate($barcode)) {
			echo json_encode(["status" => 404, "message" => "กรุณากรอกข้อมูลให้ครบถ้วน"]);
			exit;
		}
		
		$query = sqlsrv_query(
			$conn,
			"SELECT * FROM InventTable WHERE $field = ?",
			array($barcode)
		);

		$hasRows = sqlsrv_has_rows($query);

		if ($hasRows === false) {
			echo json_encode(["status" => 404, "message" => "ไม่พบรายการ" . $str_len]);
			exit;
		}

		return Sqlsrv::queryJson(
			$conn, 
			"SELECT 
			IT.Barcode as BARCODE,
			IT.BarcodeFoil as BARCODEFOIL,
			DM.DisposalDesc AS DISPOSAL,
			IT.BuildingNo AS BUILDINGMC,
			IT.DateBuild as BUILDINGDATE,
			IT.GT_Code AS GTCODE,
			IT.CuringDate AS CURINGDATE,
			IT.CuringCode AS CURINGCODE,
			ITM.ID AS ITEMID,
			ITM.NameTH AS ITEMNAME,
			IT.Batch AS BATCH,
			IT.TemplateSerialNo AS TEMPLATE,
			IST.ID as STATUSID,
			IST.Description as STATUS
			FROM InventTable IT
			LEFT JOIN DisposalToUseIn DM ON DM.ID = IT.DisposalID
			LEFT JOIN ItemMaster ITM ON ITM.ID = IT.ItemID 
			LEFT JOIN InventStatus IST ON IT.Status = IST.ID
			WHERE $field = '$barcode'"
		);
	}

	public function searchByBarcode2()
	{
		$conn = Database::connect();
		$barcode = Security::_decode(trim($_POST["search"]));

		$str_len = strlen(trim($barcode));

		if ((int)$str_len === 9) {
			$field = 'TemplateSerialNo';
		} else {
			$isBarcodeFoil = sqlsrv_has_rows(sqlsrv_query(
				$conn,
				"SELECT BarcodeFoil FROM InventTable
				WHERE BarcodeFoil = ?
				AND BarcodeFoil IS NOT NULL",
				[$barcode]
			));

			if ($isBarcodeFoil === true) {
				$field = 'BarcodeFoil';
			} else {
				$field = 'Barcode';
			}
		}

		if (!$conn) {
			echo json_encode(["status" => 404, "message" => "connection failed"]);
			exit;
		}

		if (!V::stringType()->notEmpty()->validate($barcode)) {
			echo json_encode(["status" => 404, "message" => "กรุณากรอกข้อมูลให้ครบถ้วน"]);
			exit;
		}
		
		$query = sqlsrv_query(
			$conn,
			"SELECT * FROM InventTable WHERE $field = ?",
			array($barcode)
		);

		$hasRows = sqlsrv_has_rows($query);

		if ($hasRows === false) {
			echo json_encode(["status" => 404, "message" => "ไม่พบรายการ"]);
			exit;
		}

	

		if ( $field === 'BarcodeFoil' ) {
				
			$barcodeFromBarcodeFoil = Sqlsrv::queryArray(
				$conn,
				"SELECT TOP 1 Barcode FROM InventTable 
				WHERE BarcodeFoil = ?",
				[
					$barcode
				]
			);

			$inLoadingTrans = sqlsrv_has_rows(sqlsrv_query(
				$conn,
				"SELECT Barcode FROM LoadingTrans WHERE Barcode = ?",
				[
					$barcodeFromBarcodeFoil[0]['Barcode']
				]
			));

		} else if ($field === 'TemplateSerialNo') {

			$serialFromBarcode = Sqlsrv::queryArray(
				$conn,
				"SELECT TOP 1 Barcode FROM InventTable 
				WHERE TemplateSerialNo = ?",
				[
					$barcode
				]
			);

			$inLoadingTrans = sqlsrv_has_rows(sqlsrv_query(
				$conn,
				"SELECT Barcode FROM LoadingTrans WHERE Barcode = ?",
				[
					$serialFromBarcode[0]['Barcode']
				]
			));
			
		} else {
				$inLoadingTrans = sqlsrv_has_rows(sqlsrv_query(
					$conn,
					"SELECT Barcode FROM LoadingTrans WHERE Barcode = ?",
					[
						$barcode
					]
				));
		}

		if ( $inLoadingTrans === true ) {

			if ( $field === 'BarcodeFoil' ) {

				$barcodeFromBarcodeFoil = Sqlsrv::queryArray(
					$conn,
					"SELECT TOP 1 Barcode FROM InventTable 
					WHERE BarcodeFoil = ?",
					[
						$barcode
					]
				);

				$sql2 = "SELECT 
					LT.Barcode AS BARCODE,
					LT.PickingListId AS PICKINGLIST_ID,
					LDT.PickingListDate AS PICKINGLIST_DATE,
					LT.BatchNo AS BATCH,
					LT.OrderId AS SO_FACTORY,
					CS.SO_ID AS SO_DSC,
					CS.CUSTOMER_CODE,
					CS.CUSTOMER_NAME,
					LT.ItemId AS ITEM_ID,
					IM.NameTH AS ITEM_NAME,
					LDT.DeliveryDate AS DELIVERY_DATE,
					LDT.DeliveryName AS DELIVERY_NAME,
					LT.CreatedDate AS CREATE_DATE,
					IT.TemplateSerialNo AS SERIALNO,
					IT.BarcodeFoil AS BARCODE_FOIL,
					'SALE' AS DATE_TYPE
					from LoadingTrans LT 
					left join LoadingTable LDT ON LDT.PickingListId = LT.PickingListId
					left join CustomerSO CS ON CS.SO_FACTORY = LT.OrderId
					left join ItemMaster IM ON IM.ID = LT.ItemId
					left join InventTable IT ON IT.Barcode = LT.Barcode
					WHERE LT.Barcode = ?";

				return Sqlsrv::queryJson(
					$conn, 
					$sql2,
					[
						$barcodeFromBarcodeFoil[0]['Barcode']
					]
				);
			} else if ( $field === 'TemplateSerialNo' ) {
				
				$serialFromBarcode = Sqlsrv::queryArray(
					$conn,
					"SELECT TOP 1 Barcode FROM InventTable 
					WHERE TemplateSerialNo = ?",
					[
						$barcode
					]
				);

				$sql3 = "SELECT 
					LT.Barcode AS BARCODE,
					LT.PickingListId AS PICKINGLIST_ID,
					LDT.PickingListDate AS PICKINGLIST_DATE,
					LT.BatchNo AS BATCH,
					LT.OrderId AS SO_FACTORY,
					CS.SO_ID AS SO_DSC,
					CS.CUSTOMER_CODE,
					CS.CUSTOMER_NAME,
					LT.ItemId AS ITEM_ID,
					IM.NameTH AS ITEM_NAME,
					LDT.DeliveryDate AS DELIVERY_DATE,
					LDT.DeliveryName AS DELIVERY_NAME,
					LT.CreatedDate AS CREATE_DATE,
					IT.TemplateSerialNo AS SERIALNO,
					IT.BarcodeFoil AS BARCODE_FOIL,
					'SALE' AS DATE_TYPE
					from LoadingTrans LT 
					left join LoadingTable LDT ON LDT.PickingListId = LT.PickingListId
					left join CustomerSO CS ON CS.SO_FACTORY = LT.OrderId
					left join ItemMaster IM ON IM.ID = LT.ItemId
					left join InventTable IT ON IT.Barcode = LT.Barcode
					WHERE LT.Barcode = ?";

				return Sqlsrv::queryJson(
					$conn, 
					$sql3,
					[
						$serialFromBarcode[0]['Barcode']
					]
				);

			} else {
				$sql1 = "SELECT 
					LT.Barcode AS BARCODE,
					LT.PickingListId AS PICKINGLIST_ID,
					LDT.PickingListDate AS PICKINGLIST_DATE,
					LT.BatchNo AS BATCH,
					LT.OrderId AS SO_FACTORY,
					CS.SO_ID AS SO_DSC,
					CS.CUSTOMER_CODE,
					CS.CUSTOMER_NAME,
					LT.ItemId AS ITEM_ID,
					IM.NameTH AS ITEM_NAME,
					LDT.DeliveryDate AS DELIVERY_DATE,
					LT.CreatedDate AS CREATE_DATE,
					IT.TemplateSerialNo AS SERIALNO,
					IT.BarcodeFoil AS BARCODE_FOIL, 
					'SALE' AS DATE_TYPE
					from LoadingTrans LT 
					left join LoadingTable LDT ON LDT.PickingListId = LT.PickingListId
					left join CustomerSO CS ON CS.SO_FACTORY = LT.OrderId
					left join ItemMaster IM ON IM.ID = LT.ItemId
					left join InventTable IT ON IT.Barcode = LT.Barcode
					WHERE LT.Barcode = ?";

				return Sqlsrv::queryJson(
					$conn, 
					$sql1,
					[
						$barcode
					]
				);
			}
		} else {
			return Sqlsrv::queryJson(
				$conn, 
				"SELECT 
				IT.Barcode as BARCODE,
				IT.BarcodeFoil as BARCODEFOIL,
				DM.DisposalDesc AS DISPOSAL,
				IT.BuildingNo AS BUILDINGMC,
				IT.DateBuild as BUILDINGDATE,
				IT.GT_Code AS GTCODE,
				IT.CuringDate AS CURINGDATE,
				IT.CuringCode AS CURINGCODE,
				ITM.ID AS ITEMID,
				ITM.NameTH AS ITEMNAME,
				IT.Batch AS BATCH,
				IT.TemplateSerialNo AS TEMPLATE,
				IST.ID as STATUSID,
				IST.Description as [STATUS],
				'PROD' AS DATE_TYPE
				FROM InventTable IT
				LEFT JOIN DisposalToUseIn DM ON DM.ID = IT.DisposalID
				LEFT JOIN ItemMaster ITM ON ITM.ID = IT.ItemID 
				LEFT JOIN InventStatus IST ON IT.Status = IST.ID
				WHERE $field = '$barcode'"
			);
		}	
	}

	public function searchByBarcodeLine()
	{
		$conn = Database::connect();
		$barcode = trim($_POST["barcode"]);

		if (!V::stringType()->notEmpty()->validate($barcode)) {
			echo json_encode(["status" => 404, "message" => "เกิดข้อผิดพลาด"]);
			exit;
		}

		return Sqlsrv::queryJson($conn, "SELECT * FROM InventTrans WHERE Barcode='$barcode'");
	
	}
}