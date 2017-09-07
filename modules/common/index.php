<?php

if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}
//including the required files
require_once '../../include/API/DbOperation.php';
require '../.././libs/Slim/Slim.php';
require '../.././vendor/autoload.php';
use GuzzleHttp\Client;

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();
//$app->add(new Slim_Middleware_ContentTypes());



$app->post('/addbilling', function () use ($app) {

    $json = $app->request->getBody();
    $data = json_decode($json, true);
    echoResponse(200,$data);
});

$app->get('/billing_branch_rate/:type/:id', function ($type,$id) use ($app) {

   /*  $json = $app->request->getBody();
    $data = json_decode($json, true); */
	$db = new DbOperation();
	$result = $db->getBillingBranchRate($id,$type);
    $response = array();
	$response["result"]=$result;
	//$result[0]["id"]
    echoResponse(200,$response);
});

$app->get('/billing_branch_rate/:type', function ($type) use ($app) {

   /*  $json = $app->request->getBody();
    $data = json_decode($json, true); */
	$db = new DbOperation();
	$result = $db->getBillingBranchRateByType($type);
    $response = array();
	$response["result"]=$result;
	//$result[0]["id"]
    echoResponse(200,$response);
});







function verifyNumber($number)
{

}


function echoResponse($status_code, $response)
{
    $app = \Slim\Slim::getInstance();
    $app->status($status_code);
    $app->contentType('application/json');
    echo json_encode($response);
}


function verifyRequiredParams($required_fields)
{
    $error = false;
    $error_fields = "";
    $request_params = $_REQUEST;

    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        $app = \Slim\Slim::getInstance();
        parse_str($app->request()->getBody(), $request_params);
    }

    foreach ($required_fields as $field) {
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }

    if ($error) {
        $response = array();
        $app = \Slim\Slim::getInstance();
        $response["error"] = true;
        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        echoResponse(400, $response);
        $app->stop();
    }
}

$app->run();