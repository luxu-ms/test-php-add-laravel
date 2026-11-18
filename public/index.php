<?php

declare(strict_types=1);

use Illuminate\Http\Request;

require dirname(__DIR__) . '/vendor/autoload.php';

Request::setAllowedHttpMethodOverride([]);
$request = Request::capture();
// Keep the existing form-only body contract, rather than accepting JSON as form input.
$request->request->replace($_POST);

(require dirname(__DIR__) . '/bootstrap/app.php')->handleRequest($request);
