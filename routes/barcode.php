<?php

$app->get('/change_barcode', 'App\Controllers\BarcodeController::changeBarcode');
$app->post('/change_barcode/save', 'App\Controllers\BarcodeController::saveChangeBarcode');