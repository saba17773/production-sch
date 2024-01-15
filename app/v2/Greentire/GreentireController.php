<?php

namespace App\V2\Greentire;

use App\V2\Greentire\GreentireAPI;
use App\Common\Datatables;

class GreentireController
{
  private $GreentireApi = null;
  private $datatables = null;

  public function __construct()
  {
    $this->GreentireApi = new GreentireAPI();
    $this->datatables = new Datatables();
  }

  public function index()
  {
    renderView("greentire/receive");
  }

  public function export()
  {
    renderView("greentire/receive_export");
  }

  public function getGreentireList($date)
  {
    try {
      return $this->sql->rows(
        $this->conn->dbConnect(),
        "SELECT 
        ROW_NUMBER() OVER (
          ORDER BY G.Id ASC
        ) AS Id,
        G.Id AS TransId,
        G.ItemId,
        GM.ItemGTName,
        GM.PR,
        NULL AS Code,
        GM.Pattern,
        GM.TT,
        GM.Color,
        GM.Color2,
        GM.Color3,
        GM.Color4,
        GM.Color5,
        CAST(GM.[Weight] / 1000 AS NUMERIC(10, 3)) AS [Weight],
        G.BomCPlan,
        G.BomCActual,
        G.BomDPlan,
        G.BomDActual,
        G.WeightPlan,
        G.WeightActual
      FROM TargetGreentire G
        LEFT JOIN ProductionSchGreentireMaster GM 
        ON GM.ItemGT = G.ItemId
        WHERE CONVERT(date, G.ShiftDate) =  ?
        GROUP BY 
        G.Id,
        G.ItemId,
        GM.ItemGTName,
        GM.PR,
        GM.Pattern,
        GM.TT,
        GM.Color,
        GM.Color2,
        GM.Color3,
        GM.Color4,
        GM.Color5,
        GM.[Weight],
        G.BomCPlan,
        G.BomCActual,
        G.BomDPlan,
        G.BomDActual,
        G.WeightPlan,
        G.WeightActual",[$date]
      );
    } catch (\Throwable $th) {
      throw $th;
    }
  }
  
}
