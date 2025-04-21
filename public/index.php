<?php

use App\Api\Http\Route;
use App\Utils\Core;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../routes/api.php';

Core::dispatch(Route::getRoutes());
