<?php

namespace App\V2\Batch;

use App\V2\Batch\BatchAPI;

class BatchController
{
  public function renderBatchSetup()
  {
    renderView('page/batch_setup');
  }

  public function getBatchSetup()
  {
    echo (new BatchAPI)->getBatchSetup();
  }

  public function getBatchSetupActive() {
    echo (new BatchAPI)->getBatchSetupActive();
  }

  public function createNewSetup()
  {
    echo (new BatchAPI)->createNewSetup();
  }

  public function saveBatchSetup()
  {
    $format = $_POST['format'];

    if (is_null($format) || $format === '') {
      return json_encode([
        'result' => false,
        'message' => 'Format incorrect!'
      ]);
    }

    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];
    $setup_id = $_POST['setup_id'];
    $form_type = $_POST['form_type'];

    $res = (new BatchAPI)->saveBatchSetup($format, $from_date, $to_date, $setup_id, $form_type);

    if ($res === true)  {
      return json_encode([
        'result' => true,
        'message' => $form_type . ' successful!'
      ]);
    } else {
      return json_encode([
        'result' => false,
        'message' => $res
      ]);
    }
  }

  public function activeBatch()
  {
    $id = $_POST['id'];
    $active = $_POST['active'];

    if ($_POST['active'] === 'true') {
      $activeStatus = 1;
    } else {
      $activeStatus = 0;
    }

    $res = (new BatchAPI)->updateActiveBatch($id, $activeStatus);

    if ($res === true)  {
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

  public function setBatchSetupActive()
  {
    $_status = (int)$_POST['status'];

    $res = (new BatchAPI)->setBatchSetupActive($_status);

     if ($res === true)  {
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

  public function isBatchSetupActive()
  {
    if ((new BatchAPI)->isBatchSetupActive() === true) {
      echo \json_encode(['result' => true]);
    } else {
      echo \json_encode(['result' => false]);
    }
  }
}