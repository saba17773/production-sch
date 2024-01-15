<?php

namespace App\Services;

use App\Components\Database;
use Wattanar\Sqlsrv;
use App\Components\Security;

class ItemService
{
	public function all()
	{
		$conn = Database::connect();
		//return Sqlsrv::queryJson($conn, "SELECT * FROM ItemMaster"); //tan edit
		$sql = "SELECT top(100) * FROM ProductionSchItemMaster";

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

                    if ($filterdatafield === 'CheckBuild') {
                        if ((string)$filtervalue === 'true') {
                            $tmp_value = 1;
                        } else {
                            $tmp_value = 0;
                        }
                        $filtervalue = $tmp_value;
                    }
                    
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
                $sql = "SELECT TOP 100 * FROM (SELECT 
											 * FROM ProductionSchItemMaster) X " . $where; 
            }
        }

        $query = sqlsrv_query(
			$conn,
			$sql
		);

		while ($f = sqlsrv_fetch_object($query)) 
		{
			$results[] = $f;	
		}

		return json_encode($results);

	}

	public function isItem($barcode)
	{
		$barcode_decode = Security::_decode($barcode);
		$conn = Database::connect();
		return Sqlsrv::hasRows(
			$conn,
			"SELECT * FROM InventTable
			WHERE Barcode = ?
			AND ItemID IS NOT NULL",
			[$barcode_decode]
		);
	}
	public function allBrand()
	{
		$conn = Database::connect();
		return Sqlsrv::queryJson($conn, "SELECT * FROM BrandMaster");
	}
}