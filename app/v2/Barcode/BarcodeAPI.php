<?php

namespace App\V2\Barcode;

use App\V2\Database\Connector;

class BarcodeAPI
{
  public function isBarcodeCreated($barcode)
  {
    $conn = (new Connector)->dbConnect();

    $barcode = substr($barcode, 1);

    return sqlsrv_has_rows(sqlsrv_query(
      $conn,
      "SELECT TOP 1 * FROM BarcodePrinting
      WHERE CONVERT(INT, SUBSTRING(StartBarcode, 2, 11)) <= ?
      AND CONVERT(INT, SUBSTRING(FinishBarcode, 2, 11)) >= ?",
      [
          $barcode,
          $barcode
      ]
    ));
  }

  public function isBarcodeFoil($barcode)
  {
    
  }
}