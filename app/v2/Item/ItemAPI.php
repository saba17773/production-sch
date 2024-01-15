<?php

namespace App\V2\Item;

use App\V2\Database\Connector;
use App\V2\Database\Handler;
use Wattanar\Sqlsrv;

class ItemAPI
{
  public function hasItem(string $itemId) 
  {
    $conn = (new Connector)->dbConnect();
    return sqlsrv_has_rows(sqlsrv_query(
      $conn,
      "SELECT ID FROM ItemMaster
      WHERE ID = ?",
      [
        $itemId
      ]
    ));
  }

  public function getAllItemFG()
  {
    $conn = (new Connector)->dbConnect();
    return Sqlsrv::queryJson(
      $conn,
      "SELECT ID, NameTH FROM ItemMaster
      WHERE ItemGroup = 'FG'"
    );
  }

  public function getAllItem()
  {
    $conn = (new Connector)->dbConnect();
    return Sqlsrv::queryJson(
      $conn,
      "SELECT 
        ID,
        NameTH,
        Pattern,
        Brand,
        UnitID,
        SubGroup,
        ProductGroup,
        ItemGroup,
        InternalNumber,
        QtyPerPallet,
        ManualBatch,
        CheckSerial FROM ItemMaster"
    );
  }

  public function setManualBatch($itemId, $manualBatch)
  {
    $conn = (new Connector)->dbConnect();
    $setManual = sqlsrv_query(
      $conn,
      "UPDATE ItemMaster 
      SET ManualBatch = ?
      WHERE ID = ?",
      [
        $manualBatch,
        $itemId
      ]
    );

    if (!$setManual) {
      return (new Handler)->dbError();
    } else {
      return true;
    }
  }

  public function getItemInfo($itemId)
  {
    $conn = (new Connector)->dbConnect();
    return Sqlsrv::queryArray(
      $conn,
      "SELECT * FROM ItemMaster
      WHERE ID = ?",
      [
        $itemId
      ]
    );
  }

  public function setCheckSerial($itemId, $checkSerial) {
    $conn = (new Connector)->dbConnect();
    $setC = sqlsrv_query(
      $conn,
      "UPDATE ItemMaster 
      SET CheckSerial = ?
      WHERE ID = ?",
      [
        $checkSerial,
        $itemId
      ]
    );

    if (!$setC) {
      return (new Handler)->dbError();
    } else {
      return true;
    }
  }
}