<?php

namespace App\V2\Batch;

use App\V2\Database\Connector;
use App\V2\Database\Handler;
use Wattanar\Sqlsrv;

class BatchAPI
{
  public function getBatchSetup()
  {
    $conn = (new Connector)->dbConnect();
    return Sqlsrv::queryJson(
      $conn,
      "SELECT ID, FormatBatch, FromDate, ToDate, Active FROM BatchSetup"
    );
  }

  public function getBatchSetupActive()
  {
    $conn = (new Connector)->dbConnect();
    return Sqlsrv::queryJson(
      $conn,
      "SELECT ID, FormatBatch, FromDate, ToDate, Active 
      FROM BatchSetup
      WHERE Active = 1"
    );
  }

  public function createNewSetup()
  {
    $conn = (new Connector)->dbConnect();

    try {
      $create = sqlsrv_query(
        $conn,
        "INSERT INTO BatchSetup(FormatBatch, FromDate, ToDate, Active)
        VALUES(?, ?, ?, ?)",
        [
          self::getBatch(date('Y-m-d H:i:s')),
          date('Y-m-d'),
          date('Y-m-d'),
          0
        ]
      );

      if (!$create) {
        return (new Handler)->dbError();
      } else {
        return true;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  public function getBatch($datetime)
  {
    $date =  date('Y-m-d H:i:s', strtotime($datetime . "+4 hours"));
		$ddate = new \DateTime($date);
    $year = $ddate->format('Y');
    
		if ((int)$ddate->format('m') === 1 && (int)$ddate->format('W') === 52) {
			$year = (int)$ddate->format('Y') - 1;
		}

		$w = $year . '-' .$ddate->format("W");

		return $w;
  }

  public function saveBatchSetup($format, $from_date, $to_date, $setup_id, $form_type)
  {
    $conn = (new Connector)->dbConnect();

    try {
      if ($form_type === 'update') {
        // Update
        $update = sqlsrv_query(
          $conn,
          "UPDATE BatchSetup
          SET FormatBatch = ?,
          FromDate = ?,
          ToDate = ?
          WHERE ID = ?",
          [
            $format,
            $from_date,
            $to_date,
            $setup_id
          ]
        );

        if (!$update) {
          return (new Handler)->dbError();
        } else {
          return true;
        }
      } else {
        // Create
        $create = sqlsrv_query(
          $conn,
          "INSERT INTO BatchSetup(FormatBatch, FromDate, ToDate, Active)
          VALUES(?, ?, ?, ?)",
          [
            $format,
            $from_date,
            $to_date,
            0
          ]
        );

        if (!$create) {
          return (new Handler)->dbError();
        } else {
          return true;
        }
      }
    } catch(Exception $e) {
      return $e->getMessage();
    }
  }

  public function updateActiveBatch($id, $activeStatus)
  {
    $conn = (new Connector)->dbConnect();
    try {
      $update = sqlsrv_query(
        $conn,
        "UPDATE BatchSetup
        SET Active = ?
        WHERE ID = ?",
        [
          $activeStatus,
          $id
        ]
      );

      if (!$update) {
        return (new Handler)->dbError();
      } else {
        if ($activeStatus === 1) {
          sqlsrv_query(
            $conn,
            "UPDATE BatchSetup 
            SET Active = 0
            WHERE ID <> ?",
            [
              $id
            ]
          );
        }
        return true;
      }
    } catch(Exception $e) {
      return $e->getMessage();
    }
  }

  public function isManualBatchOn()
  {
    $conn = (new Connector)->dbConnect();

    $rows = sqlsrv_has_rows(sqlsrv_query(
      $conn,
      "SELECT * FROM BatchSetup
      WHERE Active = 1"
    ));

    return $rows;
  }

  public function getManualBatch($datetime, $item)
  {
    if (self::isManualBatchOn() === false) return self::getBatch($datetime);

    if (self::isBatchSetupActive() === false) return self::getBatch($datetime);

    $conn = (new Connector)->dbConnect();

    $isDateMatch = sqlsrv_has_rows(sqlsrv_query(
      $conn,
      "SELECT FormatBatch FROM BatchSetup
      WHERE '$datetime' BETWEEN FromDate AND ToDate"
    ));

    if ($isDateMatch === false) return self::getBatch($datetime); 
    
    $isItemActive = sqlsrv_has_rows(sqlsrv_query(
      $conn,
      "SELECT ID FROM ItemMaster
      WHERE ManualBatch = 1
      AND ID = ?",
      [
        $item
      ]
    ));

    if ($isItemActive === false) return self::getBatch($datetime); 

    $getManualBatchText = Sqlsrv::queryArray(
      $conn,
      "SELECT TOP 1 FormatBatch
      FROM BatchSetup
      WHERE Active = 1"
    );

    if (count($getManualBatchText) === 0) return self::getBatch($datetime); 

    return $getManualBatchText[0]['FormatBatch'];
  }

  public function getGreentireBatch($barcode)
  {
    $conn = (new Connector)->dbConnect();

    $batch = Sqlsrv::queryArray(
      $conn,
      "SELECT TOP 1 Batch FROM InventTrans
      WHERE Barcode = ?
      AND WarehouseID = 1 
      AND LocationID = 2
      AND DocumentTypeID = 1
      ORDER BY CreateDate ASC",
      [
        $barcode
      ]
    );

    if (count($batch) === 0) {
      return self::getBatch(date('Y-m-d H:i:s'));
    } else {
      return $batch[0]['Batch'];
    }
  }

  public function isBatchSetupActive()
  {
    $conn = (new Connector)->dbConnect();

    $config = Sqlsrv::queryArray(
      $conn,
      "SELECT TOP 1 BatchSetupActive 
      FROM Configuration"
    );

    if ((int)$config[0]['BatchSetupActive'] === 1) {
      return true;
    } else {
      return false;
    }
  }

  public function setBatchSetupActive($_status)
  {
    $conn = (new Connector)->dbConnect();

    $set = sqlsrv_query(
      $conn,
      "UPDATE Configuration 
      SET BatchSetupActive = ?",
      [
        (int)$_status
      ]
    );

    if (!$set) {
      return "Update status failed!";
    } else {
      return true;
    }
  }
}