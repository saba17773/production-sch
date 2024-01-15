<?php

namespace App\V2\Item;

use App\V2\Item\ItemAPI;

class ItemController
{
  public function getAllItemFG()
  {
    echo (new ItemAPI)->getAllItemFG();
  }

  public function renderItem()
  {
    renderView('page/item_master');    
  }

  public function getAllItem()
  {
    echo (new ItemAPI)->getAllItem();
  }

  // public function setManualBatch()
  // {
  //   $itemId = $_POST['itemId'];

  //   if ($_POST['manualBatch'] === 'true') {
  //     $manualBatch = 1;
  //   } else {
  //     $manualBatch = 0;
  //   }

  //   $manualBatchStatus = $manualBatch;

  //   if ($itemId === '' || is_null($itemId) === true) return json_encode(['result' => false]);

  //   $res = (new ItemAPI)->setManualBatch($itemId, $manualBatchStatus);

  //   if ($res === true)  {
  //     return json_encode([
  //       'result' => true,
  //       'message' => 'Update successful!'
  //     ]);
  //   } else {
  //     return json_encode([
  //       'result' => false,
  //       'message' => $res
  //     ]);
  //   }

  // }

  public function updateMaster() {

    $itemId = $_POST['itemId'];

    if ($_POST['manualBatch'] === 'true') {
      $manualBatch = 1;
    } else {
      $manualBatch = 0;
    }

    if ($_POST['checkSerial'] === 'true') {
      $checkSerial = 1;
    } else {
      $checkSerial = 0;
    }

    if ($itemId === '' || is_null($itemId) === true) return json_encode(['result' => false]);

    $setCheckSerial = (new ItemAPI)->setCheckSerial($itemId, $checkSerial);
    $res = (new ItemAPI)->setManualBatch($itemId, $manualBatch);

    if ($res === true && $setCheckSerial === true)  {
      return json_encode([
        'result' => true,
        'message' => 'Update successful!'
      ]);
    } else {
      return json_encode([
        'result' => false,
        'message' => $res
      ]);
    }
  }
}