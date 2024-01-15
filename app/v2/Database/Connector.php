<?php

namespace App\V2\Database;

use Wattanar\Sqlsrv;

class Connector
{
  public static $schConn = null;
  public static $axConn = null;

  public function dbConnect($db = null)
  {
    if ($db === null) {
      if (!isset(self::$schConn)) {
        self::$schConn = Sqlsrv::connect(
          "mormont\develop",
          "EAconnection",
          "l;ylfu;yo0yomiN",
          "PRODUCTION_SCH_LIVE"
        );
        return self::$schConn;
      }
      return self::$schConn;
    } else if ($db === "AX") {
      if (!isset(self::$axConn)) {
        self::$axConn = Sqlsrv::connect(
          "frey\live",
          "EAconnection",
          "l;ylfu;yo0yomiN",
          "DSL_AX40_SP1_LIVE"
        );
        return self::$axConn;
      }
      return self::$axConn;
    } else {
      return null;
    }
  }

  public function dbConnectAx()
  {
    self::dbConnect("AX");
    // return Sqlsrv::connect("frey\live", "EAconnection", "l;ylfu;yo0yomiN", "DSL_AX40_SP1_LIVE");
  }
}
