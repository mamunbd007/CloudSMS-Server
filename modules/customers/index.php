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


/* *
 * URL: http://localhost/sms/modules/customers/createcustomer
 * Parameters: username, password, name, phone
 * Method: POST
 * */
$app->post('/createcustomer', function () use ($app) {

    $json = $app->request->getBody();
    $data = json_decode($json, true);

    $db = new DbOperation();
    $res = $db->createCustomer($data["username"],$data["password"], $data["name"], $data["phone"], $data["email"]);
    if ($res == 0) {
        $response["error"] = false;
        $response["message"] = "You are successfully registered";
        echoResponse(201, $response);
    } else if ($res == 1) {
        $response["error"] = true;
        $response["message"] = "Oops! An error occurred while registereing";
        $response['result'] = $res;
        echoResponse(200, $response);
    } else if ($res == 2) {
        $response["error"] = true;
        $response["message"] = "Sorry, this customer  already existed";
        echoResponse(200, $response);
    }
});

/* *
 * URL: http://localhost/sms/modules/customers/updatecustomer
 * Parameters: name, balance
 * Method: POST
 * */
$app->post('/updatecustomer',  function() use ($app){
	
/*	$username = $app->request->post('username');
    $password = $app->request->post('password');
	$name = $app->request->post('name');
	$phone = $app->request->post('phone');
	$email = $app->request->post('email');   
	$response = array();   */
	
	$json = $app->request->getBody();
    $data = json_decode($json, true);
	
    $db = new DbOperation();
	//$result = $db->updateCustomer($username,$password,$name,$phone,$email);
    $result = $db->updateCustomer($data['username'], $data['password'], $data['name'], $data['phone'], $data['email']);
	
    
    if($result){
        $response['error'] = false;
        $response['message'] = "Customer updated successfully";
       //$response['result'] =  $result;
    }else{
        $response['error'] = true;
        $response['message'] = "Could not update Customer";
       // $response['result'] =  $result;
    }
    echoResponse(200,$response);
});






/* *
 * URL: http://localhost/sms/modules/customers/updatevendorbalance
 * Parameters: name, balance
 * Method: POST
 * */
$app->post('/addvendortocustomer',  function() use ($app){
    $cid = $app->request->post('cid');
    $vid = $app->request->post('vid');
    $db = new DbOperation();

    $result = $db->addVendorToCustomer($cid,$vid);
    $response = array();
    if($result){
        $response['error'] = false;
        $response['message'] = "Vendor added updated successfully";
        //$response['result'] =  $result;
    }else{
        $response['error'] = true;
        $response['message'] = "Could not add Vendor";
         //$response['result'] =  $result;
    }
    echoResponse(200,$response);
});

/* *
 * URL: http://localhost/sms/modules/customers/updatecustomerbalance
 * Parameters: username, balance
 * Method: POST
 * */
$app->post('/updatecustomerbalance',  function() use ($app){
    $name = $app->request->post('username');
    $balance = $app->request->post('balance');
    $db = new DbOperation();
    $result = $db->updateCustomerBalance($name,$balance);
    $response = array();
    if($result){
        $response['error'] = false;
        $response['message'] = "Customer Balance updated successfully";
        //$response['result'] =  $result;
        echoResponse(404,$response);
    }else{
        $response['error'] = true;
        $response['message'] = "Could not update Customer Balance";
        echoResponse(200,$response);
        // $response['result'] =  $result;
    }

});



/* URL: http://localhost/sms/modules/customers/updatevendorbalance
* Parameters: name, balance
* Method: POST
* */
$app->post('/login',  function() use ($app){
    $json = $app->request->getBody();
    $data = json_decode($json, true);

    $db = new DbOperation();
    //$result=$data;
    $result = $db->checkLogin($data["username"],$data["password"]);
    $response = array();
    if($result){
        $response['error'] = false;
        $response['message'] = "User Exists";
        //$response['$result'] = $result;


    }else{
        $response['error'] = true;
        $response['message'] = "User doesn't exist";
        //$response['$result'] = $result;
        //echoResponse(423,$response);
    }
    echoResponse(200,$response);

});


/* *
 * URL: http://localhost/sms/modules/customers/customer/:username *
 * Method: GET
 * */
$app->get('/all', function() use ($app){
    $db = new DbOperation();
    $result = $db->getCustomers();
    $response = array();
	if($result)
	{
		$response['error'] = false;
		$response['customers'] = $result;
	}
	else
	{
		$response['error'] = true;
		//$response['customers'] = $result;
	}
    
    echoResponse(200,$response);
});

/* *
 * URL: http://localhost/sms/modules/customers/customer/:username *
 * Method: GET
 * $app->get('/customer/:username', 'authenticateCustomer', function($username) use ($app){
 * */
$app->get('/customer/:username',  function($username) use ($app){
    $db = new DbOperation();
    $result = $db->getCustomer($username);
    $response = array();
    $response['error'] = false;
    $response['customer'] = $result;
    echoResponse(200,$response);
});


/* *
 * URL: http://localhost/sms/modules/customers/customer/:id *
 * Method: GET
 * */
/*$app->get('/customer/:id',  function($id) use ($app){
    $db = new DbOperation();
    $result = $db->getCustomerByID($id);
    $response = array();
    $response['error'] = false;
    $response['customer'] = $result;
    echoResponse(200,$response);
});*/
/* *
 * URL: http://localhost/sms/modules/customers/customer/balance/:username *
 * Method: GET
 * */
$app->get('/customer/balance/:username', 'authenticateCustomer', function($username) use ($app){
    $db = new DbOperation();
    $result = $db->getCustomer($username);
    $response = array();
    $response['error'] = false;
    $response['customer'] = $result;
    echoResponse(200, $response['customer']["balance"]);
});


/* *
 * URL: http://localhost/sms/modules/customers/sms
 * Parameters: number, message
 * Method: POST
 * */
$app->post('/sms', function() use ($app){
    verifyRequiredParams(array('phone','sms'));
    $phone = $app->request->post('phone');
    $sms = $app->request->post('sms');
    $db = new DbOperation();



    /*$client = new Client([
        // Base URI is used with relative requests
        'base_uri' => 'http://localhost/',
        // You can set any number of default request options.
        'timeout'  => 2.0,
    ]);*/
    /*$response = $client->request('POST', 'http://localhost/StudentApp/v1/sms', [
        'form_params' => [
            'number' => '123',
            'message' => 'asdsvvdf'
        ]
    ]);*/
    $client = new GuzzleHttp\Client(['base_uri' => 'http://localhost/sms/v1/']);
    // Send a request to https://foo.com/api/test
   // $response = $client->request('GET', 'getsms');
    $response = $client->request('POST', 'sms', [
        'form_params' => [
            'number' => '1233',
            'message' => 'asdsvvd3f'
        ]
    ]);
    $response = $response->getBody()->getContents();
    echoResponse(200,$response);
});

/* *
 * URL: http://localhost/StudentApp/v1/pushsms
 * Parameters: jsonbody
 * Method: POST
 * */
$app->post('/multiple_sms', 'authenticateCustomer', function() use ($app){

    $response = array();
    try {
        $json = $app->request->getBody();
        $data = json_decode($json, true);
        $sms=$data["sms"];

        //check if required keys exist in the request body
       // $key_tp = array_key_exists("tp", $data);
        $key_id = array_key_exists("id", $data);
        $key_sms = array_key_exists("sms", $data);
        $key_number = array_key_exists("number", $sms[0]);
        $key_text = array_key_exists("text", $sms[0]);

        //if not, give error response with correct sample
        if (!$key_sms||!$key_id||!$key_number||!$key_text)
        {

            $response["error"] = true;
            $response["reason"] = "parameter/s missing";
            $sample=array();
            //$sample["tp"]=1;
            $sample["id"]=101;
            $sample["sms"][0]["number"]="01XXXXXXXX1";
            $sample["sms"][0]["text"]="Body of the SMS 1";
            $sample["sms"][1]["number"]="01XXXXXXXX1";
            $sample["sms"][1]["text"]="Body of the SMS 2";
            $response["sample"] = $sample;
        }
        //if yes
        else {
            //$response["error"] = "false";
           // $sms=$data["sms"];
           // $count=count($sms,2);
            //if tp is less than total sms; give error message
           // if($data["tp"]<$count)
           //{
           //     $response["error"] = true;
           //     $response["message"] = "tp<total sms";
           // }
            //if not, give successful message
           // else
            //{
            $db = new DbOperation();
            $response=$db->updateQueueForMultipleSms($data);

            //$response["message"] = "successful";

            //}
        }
        //send response
        echoResponse(200,$response);
    }
    catch (Exception $exception)
    {
        $response["error"] = true;
        $response["message"] = $exception->getMessage();
        $sample=array();
        //$sample["tp"]=1;
        $sample["id"]=101;
        $sample["sms"][0]["number"]="01XXXXXXXX1";
        $sample["sms"][0]["text"]="Body of the SMS 1";
        $sample["sms"][1]["number"]="01XXXXXXXX1";
        $sample["sms"][1]["text"]="Body of the SMS 2";
        $response["sample"] = $sample;
        echoResponse(200,$response);
    }
});


/* *
 * URL: http://localhost/sms/modules/customers/single_sms
 * Parameters: jsonbody
 * Method: POST
 * */
$app->post('/single_sms', 'authenticateCustomer', function() use ($app){

    $response = array();
    try {
        $json = $app->request->getBody();
        $data = json_decode($json, true);

        //check if required keys exist in the request body
        $key_id = array_key_exists("id", $data);
        $key_numbers = array_key_exists("numbers", $data);
        $key_sms = array_key_exists("sms", $data);

        //if not, give error response with correct sample
        if (!$key_sms||!$key_numbers||!$key_id) {
            $response["error"] = true;
            $response["reason"] = "parameter/s missing";
            $sample=array();
            //$sample["tp"]=1;
            $sample["id"]=101;
            $sample["sms"]="Body of the SMS 1";
            $sample["numbers"][0]="01XXXXXXXX1";
            $sample["numbers"][1]="01XXXXXXXX2";
            $sample["numbers"][2]="01XXXXXXXX3";
            $response["sample"] = $sample;

        }
        //if yes
        else {
            //$response["error"]=false;
            $db = new DbOperation();
            $response=$db->updateQueueForSingleSms($data);

        }
        //send response
        echoResponse(200,$response);
    }
    catch (Exception $exception)
    {
        $response["error"] = true;
        $sample=array();
        //$sample["tp"]=1;
        $sample["id"]=101;
        $sample["sms"]="Body of the SMS 1";
        $sample["numbers"][0]="01XXXXXXXX1";
        $sample["numbers"][1]="01XXXXXXXX1";
        $sample["numbers"][2]="01XXXXXXXX1";
        $response["sample"] = $sample;
        $response["message"] = $exception->getMessage();
        echoResponse(200,$response);
    }
});


/* *
 * URL: http://localhost/sms/modules/customers/check
 * Parameters: jsonbody
 * Method: POST
 * */
$app->post('/check', function() use ($app)
{
    $user = $app->request->post('user');
    $pass = $app->request->post('pass');
    $todo = $app->request->post('todo');
    $sender = $app->request->post('sender');
    $receiver=$app->request->post('receiver');
    $message=$app->request->post('message');
    $service_type=$app->request->post('service_type');
    $response1["message"]="1 SMS successfully sent to 1 recipient";
    $response1["voucher"]=rand();
    $response["error"]=0;
    $response1["receiver"]=$receiver;
    $response["single_sms"]=$response1;

    echoResponse(200,$response);
});

/* *
 * URL: http://localhost/sms/modules/customers/test
 * Parameters: jsonbody
 * Method: POST
 * */
$app->post('/test', function() use ($app)
{
    $user = $app->request->post('number');
    $pass = $app->request->post('message');
    echoResponse(200,"Success");
});


//Authenciate customer to check if customer has valid api key
function authenticateCustomer(\Slim\Route $route)
{
    $headers = apache_request_headers();
    $response = array();
    $app = \Slim\Slim::getInstance();
    if (isset($headers['Authorization'])) {
        $db = new DbOperation();
        $api_key = $headers['Authorization'];
        if (!$db->isValidCustomer($api_key)) {
            $response["error"] = true;
            $response["message"] = "Access Denied. Invalid Api key";
            echoResponse(401, $response);
            $app->stop();
        }
    } else {
        $response["error"] = true;
        $response["message"] = "Api key is misssing. Please include Api key at header Authorization";
        echoResponse(400, $response);
        $app->stop();
    }
}


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