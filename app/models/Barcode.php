<?php

namespace App\Models;

use App\Components\Database as DB;
use Wattanar\Sqlsrv;

class Barcode 
{
    public $ID = null;
    public $QTY = null;
    public $StartBarcode = null;
    public $FinishBarcode = null;
    public $Status = null;
    public $CreateBy = null;
    public $CreateDate = null;
    public $Company = null;
    public $UpdateBy = null;
    public $UpdateDate = null;

    public function inRange($barcode) {
        $conn = DB::connect();
        $barcode = substr($barcode, 1);

        if (!is_numeric($barcode)) {
            return false;
        }

        return Sqlsrv::hasRows(
            $conn,
            "SELECT TOP 1 * FROM BarcodePrinting
            WHERE CONVERT(INT, SUBSTRING(StartBarcode, 2, 11)) <= ?
            AND CONVERT(INT, SUBSTRING(FinishBarcode, 2, 11)) >= ?",
            [
                $barcode,
                $barcode
            ]
        );
    }

    public function changeBarcode($old_barcode, $new_barcode)
    {
        $conn = DB::connect();
        
        if (sqlsrv_begin_transaction($conn) === false) {
            return false;
        }

        $update_inventtable = Sqlsrv::update(
            $conn,
            'UPDATE InventTable 
            SET Barcode = ?
            WHERE Barcode = ?',
            [
                $new_barcode,
                $old_barcode
            ]
        );

        if (!$update_inventtable) {
            sqlsrv_rollback($conn);
            return false;
        }

         $update_inventtrans = Sqlsrv::update(
            $conn,
            'UPDATE InventTrans 
            SET Barcode = ?
            WHERE Barcode = ?',
            [
                $new_barcode,
                $old_barcode
            ]
        );

        if (!$update_inventtrans) {
            sqlsrv_rollback($conn);
            return false;
        }

        if ($update_inventtable && $update_inventtrans) {
            sqlsrv_commit($conn);
            return true;
        } else {
            sqlsrv_rollback($conn);
            return false;
        }
    }
}