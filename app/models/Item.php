<?php

namespace App\Models;

use App\Components\Database as DB;
use Wattanar\Sqlsrv;

class Item
{
    public $ID = null;
    public $NameTH = null;
    public $Pattern = null;
    public $Brand = null;
    public $UnitID = null;

    public function isItemExist() {
        $conn = DB::connect();
        return Sqlsrv::hasRows(
            $conn,
            "SELECT ID FROM ItemMaster
            WHERE ID = ?",
            [
                $this->ID
            ]
        );
    }

    public function isItemExists($item_id) {
        $conn = DB::connect();
        return sqlsrv_has_rows(sqlsrv_query(
            $conn,
            "SELECT ID FROM ItemMaster
            WHERE ID = ?",
            [
                $item_id
            ]
        ));
    }

    public function getItemSet() {
        $conn = DB::connect();
        return Sqlsrv::queryJson(
            $conn,
            "SELECT * FROM ItemMaster
            WHERE UnitID = ?",
            [
                "SET"
            ]
        );
    }

    public function getItemNormal() {
        $conn = DB::connect();
        return Sqlsrv::queryJson(
            $conn,
            "SELECT * FROM ItemMaster
            WHERE UnitID = ?",
            [
                "PCS"
            ]
        );
    }

    public function getItemGroupSM($value='')
    {
        $conn = DB::connect();
        return Sqlsrv::queryJson(
            $conn,
            "SELECT * FROM ItemMaster
            WHERE ItemGroup = ?",
            [
                "SM"
            ]
        );
    }
}