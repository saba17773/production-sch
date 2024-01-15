<?php

$app->get('/change_code', 'App\Controllers\BuildingController::changeCode');
$app->post('/api/v1/build/check', 'App\Controllers\BuildingController::checkBuild');
$app->post('/api/v1/building/change_code', 'App\Controllers\BuildingController::saveChangeCode');