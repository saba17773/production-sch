<?php

namespace App\Common;

class Sql
{
  public function rows($connection, $query, array $params = null)
  {
    try {
      // code
      if ($params === null) {
        $query = \sqlsrv_query($connection, $query);
      } else {
        $query = \sqlsrv_query($connection, $query, $params);
      }
      $rows = [];
      while ($nextRow = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)) {
        $rows[] = $nextRow;
      }

      return $rows;
    } catch (\Exception $e) {
      return ["error" => $e->getMessage()];
    }
  }
}
