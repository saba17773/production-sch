<?php

namespace App\V2\TypeTireMaster;

use App\V2\Database\Connector;
use App\V2\Database\Handler;
use Wattanar\Sqlsrv;
use App\V2\Helper\Helper;

class TypeTireAPI
{
  public function bindGridMain()
  {
    $conn = (new Connector)->dbConnect();
    return Sqlsrv::queryJson(
      $conn,
      "SELECT *
            FROM TypeTireGroupMaster
            ORDER BY GroupID"
    );
  }

  public function bindGridLine($groupid)
  {
    $conn = (new Connector)->dbConnect();
    return Sqlsrv::queryJson(
      $conn,
      "SELECT D.ID,D.GroupID,D.DetailID,
            D.DetailDesc,D.Size,D.Sortby,G.GroupDesc
            FROM TypeTireDetailMaster D JOIN
            TypeTireGroupMaster G ON G.GroupID = D.GroupID
            WHERE D.GroupID = ?
            ORDER BY D.DetailID
            ",
      [
        $groupid
      ]
    );
  }

  public function insertGroup($groupdesc)
  {
    $conn = (new Connector)->dbConnect();
    $insertgroup = sqlsrv_query(
      $conn,
      "INSERT INTO TypeTireGroupMaster (
                GroupDesc,
                Sortby
                )
                (SELECT 
                    ?,
                    MAX(Sortby)+1 
                FROM TypeTireGroupMaster
                )
            ",
      [
        $groupdesc
      ]
    );

    if (!$insertgroup) {
      return (new Handler)->dbError();
    } else {
      return true;
    }
  }

  public function updateGroup($grpid, $grpdesc, $grpsort)
  {
    $conn = (new Connector)->dbConnect();
    $updategroup = sqlsrv_query(
      $conn,
      "UPDATE TypeTireGroupMaster SET
                GroupDesc = ?,
                Sortby = ?
                WHERE GroupID = ?
            ",
      [
        $grpdesc,
        $grpsort,
        $grpid
      ]
    );

    if (!$updategroup) {
      return (new Handler)->dbError();
    } else {
      return true;
    }
  }

  public function insertDetail($dDesc, $dSize, $dGrpId)
  {
    $conn = (new Connector)->dbConnect();
    $updategroup = sqlsrv_query(
      $conn,
      "INSERT INTO TypeTireDetailMaster (
                    GroupID,
                    DetailID,
                    DetailDesc,
                    Size,
                    Sortby
                )
                (
                    SELECT 
                        ?,
                        CASE WHEN MAX(DetailID) IS NULL 
                        THEN 1 ELSE MAX(DetailID)+1 
                        END AS DetailID,
                        ?,
                        ?,
                        CASE WHEN MAX(Sortby) IS NULL 
                        THEN 1 ELSE MAX(Sortby)+1 
                        END AS Sortby
                    FROM TypeTireDetailMaster 
                    WHERE GroupID = ?
                )
            ",
      [
        $dGrpId,
        $dDesc,
        $dSize,
        $dGrpId
      ]
    );

    if (!$updategroup) {
      return (new Handler)->dbError();
    } else {
      return true;
    }
  }

  public function updateDetail($dDesc, $dSize, $dSort, $dIdAuto)
  {
    $conn = (new Connector)->dbConnect();
    $updatedetil = sqlsrv_query(
      $conn,
      "UPDATE TypeTireDetailMaster SET 
                DetailDesc = ?,
                Size = ?,
                Sortby = ?
                WHERE ID = ?
            ",
      [
        $dDesc,
        $dSize,
        $dSort,
        $dIdAuto
      ]
    );

    if (!$updatedetil) {
      return (new Handler)->dbError();
    } else {
      return true;
    }
  }

  public function cureGridMain()
  {
    $conn = (new Connector)->dbConnect();
    return Sqlsrv::queryJson(
      $conn,
      "SELECT *
            FROM ProductionSchCure
            ORDER BY ID ASC"
    );
  }

  public function updatecure($grpid, $grpdesc, $grpsort, $active)
  {
    $conn = (new Connector)->dbConnect();
    $updategroup = sqlsrv_query(
      $conn,
      "UPDATE ProductionSchCure SET
                CurID = ?,
                CureSize = ?,
                Company = 'DSL',
                Active = ?
                WHERE ID = ?
            ",
      [
        $grpdesc,
        $grpsort,
        $active,
        $grpid
      ]
    );

    if (!$updategroup) {
      return (new Handler)->dbError();
    } else {
      return true;
    }
  }

  public function insertCure($CurID, $CureSize)
  {
    $conn = (new Connector)->dbConnect();
    $insertgroup = sqlsrv_query(
      $conn,
      "INSERT INTO ProductionSchCure (
                CurID,
                CureSize,
                Company,
                Active
                )
                VALUES
                (
                  '$CurID',
                  '$CureSize',
                  'DSL',
                  1

                )"
    );

    if (!$insertgroup) {
      return (new Handler)->dbError();
    } else {
      return true;
    }
  }
}
