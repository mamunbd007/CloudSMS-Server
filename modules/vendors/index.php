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




$app->post('/createvendor', function () use ($app) {
	
    //verifyRequiredParams(array('name'));
    $json = $app->request->getBody();
    $data = json_decode($json, true);
	
    //$name = $app->request->post('name');
	
	$response = array();
	/* $response["error"] = false;
        $response["message"] = "You are successfully registered";
		 echoResponse(200, $response);*/
	
    $db = new DbOperation();
    $res = $db->createVendor($data['name']);
    if ($res == 0) {
        $response["error"] = false;
        $response["message"] = "You are successfully registered";
        echoResponse(201, $response);
    } else if ($res == 1) {
        $response["error"] = true;
        $response["message"] = "Oops! An error occurred while registereing";
        echoResponse(200, $response);
    } else if ($res == 2) {
        $response["error"] = true;
        $response["message"] = "Sorry, this vendor  already existed";
        echoResponse(200, $response);
    }
});




/* *
 * URL: http://localhost/sms/modules/vendors/updatevendor
 * Parameters: name, balance
 * Method: POST
 * */
$app->post('/updatevendor',  function() use ($app){
	
	$json = $app->request->getBody();
    $data = json_decode($json, true);
    $db   = new DbOperation();
	//$name = $app->request->post('name');
    $result = $db->updateVendor($data['name'],$data['balance'],$data['tp']);
    
	 if($result){
        $response['error'] = false;
        $response['message'] = "Vendor updated successfully";
       
    }else{
        $response['error'] = true;
        $response['message'] = "Could not update Vendor";
       
    }
    echoResponse(200,$response);
});

/* *
 * URL: http://localhost/sms/v1/updatevendorbalance
 * Parameters: name, balance
 * Method: POST
 * */
$app->post('/updatevendorbalance',  function() use ($app){
    $name = $app->request->post('name');
    $balance = $app->request->post('balance');
    $db = new DbOperation();
    $result = $db->updateVendorBalance($name,$balance);
    $response = array();
    if($result){
        $response['error'] = false;
        $response['message'] = "Vendor Balance updated successfully";
       //$response['result'] =  $result;
    }else{
        $response['error'] = true;
        $response['message'] = "Could not update Vendor Balance";
       // $response['result'] =  $result;
    }
    echoResponse(200,$response);
});

/* *
 * URL: http://localhost/sms/modules/customers/customer/:username *
 * Method: GET
 * */
$app->get('/all', function() use ($app){
    $db = new DbOperation();
    $result = $db->getVendors();
    $response = array();
	if($result)
	{
		$response['error'] = false;
		$response['vendors'] = $result;
	}
	else
	{
		$response['error'] = true;
		//$response['customers'] = $result;
	}
    
    echoResponse(200,$response);
});









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