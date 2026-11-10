<?php

/*
|--------------------------------------------------------------------------
| Entry point for shared hosting (InfinityFree) deployment
|--------------------------------------------------------------------------
|
| This is a modified copy of public/index.php. On InfinityFree there is no
| SSH/artisan access, so the Laravel app is split into two upload
| locations:
|
|   htdocs/              <- upload the CONTENTS of public/ here, then
|                            replace the index.php that lands here with
|                            THIS file (rename this file to index.php).
|   htdocs/laravel_app/  <- everything else (app/, bootstrap/, vendor/,
|                            .env, etc.) as a SUBFOLDER of htdocs/.
|
| FINAL, CONFIRMED (14 Aug): the Home level above htdocs/ on this
| InfinityFree account contains only host-managed files, including one
| literally named "DO NOT UPLOAD FILES HERE", and folder creation there
| is blocked ("Directory creation is only allowed in htdocs folders").
| laravel_app/ MUST live inside htdocs/. It is protected from direct web
| access by the .htaccess in deploy/laravel_app-htaccess — upload that as
| htdocs/laravel_app/.htaccess. Do not switch this back to a sibling
| ("../laravel_app") version again — that path does not work on this host.
|
| Do NOT use this file for local development — keep using the regular
| public/index.php for that. This file is only for the InfinityFree
| upload.
*/

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/laravel_app/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/laravel_app/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/laravel_app/bootstrap/app.php';

$app->handleRequest(Request::capture());
