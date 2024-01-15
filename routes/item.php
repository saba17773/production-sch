<?php

// API
$app->get("/api/v1/item/itemset", "App\Controllers\ItemController::getItemSet");
$app->get("/api/v1/item/normal", "App\Controllers\ItemController::getItemNormal");
$app->get("/api/v1/item/group_sm", "App\Controllers\ItemController::getItemGroupSM");
