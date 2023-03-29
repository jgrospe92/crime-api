<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Vanier\Api\Controllers\AboutController;
use Vanier\Api\Controllers\VerdictsController;
use Vanier\Api\Controllers\CasesController;
use Vanier\Api\Controllers\CrimeScenesController;
use Vanier\Api\Controllers\JudgesController;
use Vanier\Api\Controllers\VictimsController;

// Import the app instance into this file's scope.
global $app;

// NOTE: Add your app routes here.
// The callbacks must be implemented in a controller class.
// The Vanier\Api must be used as namespace prefix. 

// ROUTE: /
$app->get('/', [AboutController::class, 'handleAboutApi']);

// Routes : cases
$app->get('/cases/{case_id}', [CasesController::class, 'handleGetCaseById']);

//Routes for Verdicts
$app->get('/verdicts', [VerdictsController::class, 'handleGetAllVerdicts']);

$app->get('/victims', [VictimsController::class, 'handleGetAllVictims']);
$app->get('/victims/{victim_id}', [VictimsController::class, 'handleGetVictimById']);

$app->get('/judges', [JudgesController::class, 'handleGetAllJudges']);
$app->get('/judges/{judge_id}', [JudgesController::class, 'handleGetJudgeById']);

$app->get('/crime_scenes', [CrimeScenesController::class, 'handleGetAllCrimeScenes']);
$app->get('/crime_scenes/{crime_sceneID}', [CrimeScenesController::class, 'handleGetCrimeById']);

// ROUTE: /hello
$app->get('/hello', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Reporting! Hello there!");    
    return $response;
});