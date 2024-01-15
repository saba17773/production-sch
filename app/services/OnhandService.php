<?php

namespace App\Services;

use App\Components\Database;
use App\Components\Security;
use Wattanar\Sqlsrv;

class OnhandService
{
	public function all()
	{
    $warehouse = $_SESSION["user_warehouse"];
		$conn = Database::connect();

    if ($_SESSION["user_permission"] !== 11) {
      
      return Sqlsrv::queryJson(
        $conn, 
        "SELECT 
        (
          case
            when ITM.NameTH is null then I.GT_Code
            else ITM.ID
          end
          ) [CodeID],
        ITM.NameTH [ItemName], 
        WM.Description [Warehouse], 
        L.Description [Location], 
        I.Batch,
        SUM(I.QTY) [QTY]
        from InventTable I 
        left join WarehouseMaster WM ON WM.ID = I.WarehouseID
        left join Location L ON L.ID = I.LocationID
        left join ItemMaster ITM ON ITM.ID = I.ItemID
        where I.Status NOT IN (3,4) AND I.WarehouseID = '$warehouse'
        group by 
        I.GT_Code, 
        ITM.ID,
        ITM.NameTH, 
        WM.Description, 
        L.Description, 
        I.Batch
        order by I.GT_Code asc"
      );

    } else {
      return Sqlsrv::queryJson($conn, 
        "SELECT 
          (
          case
            when ITM.NameTH is null then I.GT_Code
            else ITM.ID
          end
          ) [CodeID],
        ITM.NameTH [ItemName], 
        WM.Description [Warehouse], 
        L.Description [Location], 
        I.Batch,
        SUM(I.QTY) [QTY]
        from InventTable I 
        left join WarehouseMaster WM ON WM.ID = I.WarehouseID
        left join Location L ON L.ID = I.LocationID
        left join ItemMaster ITM ON ITM.ID = I.ItemID
        where I.Status NOT IN (3,4)
        group by 
        I.GT_Code, 
        ITM.ID,
        ITM.NameTH, 
        WM.Description, 
        L.Description, 
        I.Batch
        order by I.GT_Code asc"
      );
    }
	}

	public function getGreentireHold()
	{
		$conn = Database::connect();
		$query = Sqlsrv::queryArray(
			$conn, 
			"SELECT 
      IT.ID,
      IT.Barcode,
      IT.DateBuild,
      IT.BuildingNo,
      IT.GT_Code,
      IT.CuringDate,
      IT.CuringCode,
      IT.ItemID,
      IM.NameTH,
      IT.Batch,
      IT.QTY,
      UN.Description [Unit],
      IT.PressNo,
      IT.PressSide,
      IT.MoldNo,
      IT.TemplateSerialNo,
      IT.CuredTireReciveDate,
      IT.CuredTireLineNo,
      IT.XrayDate,
      IT.XrayNo,
      IT.FinalReceiveDate,
      G.Description [GateDescription],
      IT.WarehouseReceiveDate,
      IT.WarehouseTransReceiveDate,
      IT.LoadingDate,
      IT.DONo,
      IT.PickingListID,
      IT.OrderID,
      D.DisposalDesc [Disposal],
      WH.Description [WH],
      LC.Description [LC],
      S.Description [Status], 
      IT.Company,
      U.Name,
      U.Username,
      IT.UpdateDate,
      IT.CreateDate,
      (
        SELECT TOP 1 DefectID FROM InventTrans 
        WHERE DefectID IS NOT NULL
        AND IT.Barcode = InventTrans.Barcode
        ORDER BY CreateDate DESC
    	) as DefectID,
      (
        SELECT DF.Description FROM Defect DF
        WHERE DF.ID = (
          SELECT TOP 1 DefectID FROM InventTrans 
          WHERE DefectID IS NOT NULL
          AND IT.Barcode = InventTrans.Barcode
          ORDER BY CreateDate DESC
        )
      ) as DefectDesc,
      (
        SELECT TOP 1 S.Description FROM InventTrans X
        LEFT JOIN ShiftMaster S ON S.ID = X.Shift
        WHERE X.Shift IS NOT NULL
        AND IT.Barcode = X.Barcode
        ORDER BY X.CreateDate ASC
      ) as Shift
      FROM InventTable IT
      LEFT JOIN ItemMaster IM ON IT.ItemID = IM.ID
      LEFT JOIN UnitMaster UN ON UN.ID = IT.Unit
      LEFT JOIN DisposalToUseIn D ON D.ID = IT.DisposalID
      LEFT JOIN WarehouseMaster WH ON WH.ID = IT.WarehouseID
      LEFT JOIN Location LC ON LC.ID = IT.LocationID
      LEFT JOIN InventStatus S ON S.ID = IT.Status
      LEFT JOIN UserMaster U ON U.ID = IT.CreateBy
      LEFT JOIN Gate G ON G.ID = IT.GateReceiveNo
      WHERE IT.Status = 5 -- hold
      AND IT.WarehouseID = 1 -- greentire
      AND IT.DisposalID = 10  -- hold
      ORDER BY IT.UpdateDate DESC"
		);

		$result = [];

		foreach ($query as $v) {
			$v["Barcode"] = Security::_encode($v["Barcode"]);
			$result[] = $v;
		}

		return json_encode($result);
	}

	public function getFinalHold()
	{
		$conn = Database::connect();

    if (isset($_GET['filterscount']))
        {
            $filterscount = $_GET['filterscount'];
            
            if ($filterscount > 0)
            {
                $sql = "";
                $where = "WHERE (";
                $tmpdatafield = "";
                $tmpfilteroperator = "";
                for ($i=0; $i < $filterscount; $i++)
                {
                    // get the filter's value.
                    $filtervalue = $_GET["filtervalue" . $i];
                    // get the filter's condition.
                    $filtercondition = $_GET["filtercondition" . $i];
                    // get the filter's column.
                    $filterdatafield = $_GET["filterdatafield" . $i];
                    // get the filter's operator.
                    $filteroperator = $_GET["filteroperator" . $i];
                    
                    if ($tmpdatafield == "")
                    {
                        $tmpdatafield = $filterdatafield;           
                    }
                    else if ($tmpdatafield <> $filterdatafield)
                    {
                        $where .= ")AND(";
                    }
                    else if ($tmpdatafield == $filterdatafield)
                    {
                        if ($tmpfilteroperator == 0)
                        {
                            $where .= " AND ";
                        }
                        else $where .= " OR ";  
                    }
                    
                    // build the "WHERE" clause depending on the filter's condition, value and datafield.
                    switch($filtercondition)
                    {
                        case "CONTAINS":
                            $where .= " " . $filterdatafield . " LIKE '%" . $filtervalue ."%'";
                            break;
                        case "DOES_NOT_CONTAIN":
                            $where .= " " . $filterdatafield . " NOT LIKE '%" . $filtervalue ."%'";
                            break;
                        case "EQUAL":
                            $where .= " " . $filterdatafield . " = '" . $filtervalue ."'";
                            break;
                        case "NOT_EQUAL":
                            $where .= " " . $filterdatafield . " <> '" . $filtervalue ."'";
                            break;
                        case "GREATER_THAN":
                            $where .= " " . $filterdatafield . " > '" . $filtervalue ."'";
                            break;
                        case "LESS_THAN":
                            $where .= " " . $filterdatafield . " < '" . $filtervalue ."'";
                            break;
                        case "GREATER_THAN_OR_EQUAL":
                            $where .= " " . $filterdatafield . " >= '" . $filtervalue ."'";
                            break;
                        case "LESS_THAN_OR_EQUAL":
                            $where .= " " . $filterdatafield . " <= '" . $filtervalue ."'";
                            break;
                        case "STARTS_WITH":
                            $where .= " " . $filterdatafield . " LIKE '" . $filtervalue ."%'";
                            break;
                        case "ENDS_WITH":
                            $where .= " " . $filterdatafield . " LIKE '%" . $filtervalue ."'";
                            break;
                    }
                                    
                    if ($i == $filterscount - 1)
                    {
                        $where .= ")";
                    }
                    
                    $tmpfilteroperator = $filteroperator;
                    $tmpdatafield = $filterdatafield;           
                }
                // build the query.
                $sql = "SELECT TOP 100 * FROM () X " . $where . "ORDER BY X.ID DESC"; 
            }
        }

		$query =  Sqlsrv::queryArray($conn, 
			"SELECT
      IT.ID,
      IT.Barcode,
      IT.DateBuild,
      IT.BuildingNo,
      IT.GT_Code,
      IT.CuringDate,
      IT.CuringCode,
      IT.ItemID,
      IM.NameTH,
      IT.Batch,
      IT.QTY,
      UN.Description [Unit],
      IT.PressNo,
      IT.PressSide,
      IT.MoldNo,
      IT.TemplateSerialNo,
      IT.CuredTireReciveDate,
      IT.CuredTireLineNo,
      IT.XrayDate,
      IT.XrayNo,
      IT.FinalReceiveDate,
      G.Description [GateDescription],
      IT.WarehouseReceiveDate,
      IT.WarehouseTransReceiveDate,
      IT.LoadingDate,
      IT.DONo,
      IT.PickingListID,
      IT.OrderID,
      D.DisposalDesc [Disposal],
      WH.Description [WH],
      LC.Description [LC],
      S.Description [Status], 
      IT.Company,
      U.Name,
      U.Username,
      IT.UpdateDate,
      IT.CreateDate,
      (
        SELECT TOP 1 DefectID FROM InventTrans 
        WHERE DefectID IS NOT NULL
        AND IT.Barcode = InventTrans.Barcode
        ORDER BY CreateDate DESC
      ) as DefectID,
      (
        SELECT DF.Description FROM Defect DF
        WHERE DF.ID = (
          SELECT TOP 1 DefectID FROM InventTrans 
          WHERE DefectID IS NOT NULL
          AND IT.Barcode = InventTrans.Barcode
          ORDER BY CreateDate DESC
        )
      ) as DefectDesc,
      (
        SELECT TOP 1 S.Description FROM InventTrans X
        LEFT JOIN ShiftMaster S ON S.ID = X.Shift
        WHERE X.Shift IS NOT NULL
        AND IT.Barcode = X.Barcode
        ORDER BY X.CreateDate ASC
      ) as Shift
      FROM InventTable IT
      LEFT JOIN ItemMaster IM ON IT.ItemID = IM.ID
      LEFT JOIN UnitMaster UN ON UN.ID = IT.Unit
      LEFT JOIN DisposalToUseIn D ON D.ID = IT.DisposalID
      LEFT JOIN WarehouseMaster WH ON WH.ID = IT.WarehouseID
      LEFT JOIN Location LC ON LC.ID = IT.LocationID
      LEFT JOIN InventStatus S ON S.ID = IT.Status
      LEFT JOIN UserMaster U ON U.ID = IT.CreateBy
      LEFT JOIN Gate G ON G.ID = IT.GateReceiveNo
      WHERE IT.Status = 5 -- hold
      AND IT.WarehouseID = 2 -- final
      AND IT.DisposalID IN (9, 10) -- return, hold
      ORDER BY IT.UpdateDate DESC"	
		);

		$result = [];

		foreach ($query as $v) {
			$v["Barcode"] = Security::_encode($v["Barcode"]);
			$result[] = $v;
		}

		return json_encode($result);
	}	

	public function updateOnhand($item_code, $type)
	{
		return false;
	}

  public function isItemExist($WarehouseID, $LocationID, $Batch, $Company, $CodeID)
  {
      $conn = Database::connect();
      return Sqlsrv::hasRows(
          $conn,
          "SELECT CodeID 
          FROM Onhand
          WHERE WarehouseID = ?
          AND LocationID = ?
          AND Batch = ?
          AND Company = ?
          AND CodeID  = ?
          AND QTY >= 0",
          [
              $WarehouseID,
              $LocationID,
              $Batch,
              $Company,
              $CodeID
          ]
      );
  }
}