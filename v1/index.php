<?php

//including the required files
require_once '../include/API/DbOperation.php';
require '.././libs/Slim/Slim.php';
require '.././vendor/autoload.php';
use GuzzleHttp\Client;

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();
//$app->add(new Slim_Middleware_ContentTypes());


/* *
 * URL: http://localhost/sms/v1/createcustomer
 * Parameters: username, password, name, phone
 * Method: POST
 * */
 $app->post('/createcustomer', function () use ($app) {
    //verifyRequiredParams(array('username', 'password', 'name', 'phone', 'email'));
	
  /*$response = array();
    $name = $app->request->post('name');
    $username = $app->request->post('username');
    $password = $app->request->post('password');
    $phone = $app->request->post('phone');
	$email = $app->request->post('email'); */
	
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
/*$app->post('/createcustomer', function () use ($app) {
    verifyRequiredParams(array('username', 'password', 'name', 'phone'));
    $response = array();
    $name = $app->request->post('name');
    $username = $app->request->post('username');
    $password = $app->request->post('password');
    $phone = $app->request->post('phone');
    $db = new DbOperationForCron();
    $res = $db->createCustomer($username, $password, $name, $phone);
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
        $response["message"] = "Sorry, this student  already existed";
        echoResponse(200, $response);
    }
});*/

/* *
 * URL: http://localhost/sms/v1/createvendor
 * Parameters: name
 * Method: POST
 * */
$app->post('/createvendor', function () use ($app) {
    verifyRequiredParams(array('name'));
    $response = array();
    $name = $app->request->post('name');
    $db = new DbOperationForCron();
    $res = $db->createVendor($name);
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
        $response["message"] = "Sorry, this student  already existed";
        echoResponse(200, $response);
    }
});

/* *
 * URL: http://localhost/sms/v1/updatevendorbalance
 * Parameters: name, balance
 * Method: POST
 * */
$app->post('/updatevendorbalance',  function() use ($app){
    $name = $app->request->post('name');
    $balance = $app->request->post('balance');
    $db = new DbOperationForCron();
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
 * URL: http://localhost/sms/v1/updatecustomerbalance
 * Parameters: username, balance
 * Method: POST
 * */
/*$app->post('/updatecustomerbalance',  function() use ($app){
    $name = $app->request->post('username');
    $balance = $app->request->post('balance');
    $db = new DbOperationForCron();
    $result = $db->updateCustomerBalance($name,$balance);
    $response = array();
    if($result){
        $response['error'] = false;
        $response['message'] = "Customer Balance updated successfully";
        //$response['result'] =  $result;
    }else{
        $response['error'] = true;
        $response['message'] = "Could not update Customer Balance";
        // $response['result'] =  $result;
    }
    echoResponse(200,$response);
});*/
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
 * URL: http://localhost/sms/v1/customer/:username *
 * Method: GET
 * */
$app->get('/customer/:username', 'authenticateCustomer', function($username) use ($app){
    $db = new DbOperationForCron();
    $result = $db->getCustomer($username);
    $response = array();
    $response['error'] = false;
    $response['customer'] = $result;
    echoResponse(200,$response);
});

/* *
 * URL: http://localhost/sms/v1/customer/balance/:username *
 * Method: GET
 * */
$app->get('/customer/balance/:username', 'authenticateCustomer', function($username) use ($app){
    $db = new DbOperationForCron();
    $result = $db->getCustomer($username);
    $response = array();
    $response['error'] = false;
    $response['customer'] = $result;
    echoResponse(200, $response['customer']["balance"]);
});


/* *
 * URL: http://localhost/StudentApp/v1/sms
 * Parameters: number, message
 * Method: POST
 * */
$app->post('/sms', function() use ($app){
    verifyRequiredParams(array('phone','sms'));
    $phone = $app->request->post('phone');
    $sms = $app->request->post('sms');
    $db = new DbOperationForCron();



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
    $client = new GuzzleHttp\Client(['base_uri' => 'http://localhost/StudentApp/v1/']);
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
$app->post('/pushsms', 'authenticateCustomer', function() use ($app){

    $response = array();
    try {
        $json = $app->request->getBody();
        $data = json_decode($json, true);

        //check if required keys exist in the request body
        $key_tp = array_key_exists("tp", $data);
        $key_sms = array_key_exists("sms", $data);

        //if not, give error response with correct sample
        if (!$key_tp || !$key_sms) {
            $response["error"] = true;
            $response["reason"] = "parameter/s missing";
            $sample=array();
            $sample["tp"]=1;
            $sample["sms"][0]["number"]="01XXXXXXXX1";
            $sample["sms"][0]["text"]="Body of the SMS 1";
            $sample["sms"][1]["number"]="01XXXXXXXX1";
            $sample["sms"][1]["text"]="Body of the SMS 2";
            $response["sample"] = $sample;

        }
        //if yes
        else {
            //$response["error"] = "false";
            $sms=$data["sms"];
            $count=count($sms,2);
            //if tp is less than total sms; give error message
            if($data["tp"]<$count)
            {
                $response["error"] = true;
                $response["message"] = "tp<total sms";
            }
            //if not, give successful message
            else
            {
                $response["error"] = false;
                $response["message"] = "successful";
            }
        }
        //send response
        echoResponse(200,$response);
    }
    catch (Exception $exception)
    {
        $response["error"] = true;
        $sample=array();
        $sample["tp"]=1;
        $sample["sms"][0]["number"]="01XXXXXXXX1";
        $sample["sms"][0]["text"]="Body of the SMS 1";
        $sample["sms"][1]["number"]="01XXXXXXXX1";
        $sample["sms"][1]["text"]="Body of the SMS 2";
        $response["sample"] = $sample;
        echoResponse(200,$response);
    }
});


//Authenciate customer to check if customer has valid api key
function authenticateCustomer(\Slim\Route $route)
{
    $headers = apache_request_headers();
    $response = array();
    $app = \Slim\Slim::getInstance();
    if (isset($headers['Authorization'])) {
        $db = new DbOperationForCron();
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

























/* *
 * URL: http://localhost/StudentApp/v1/createstudent
 * Parameters: name, username, password
 * Method: POST
 * */
$app->post('/createstudent', function () use ($app) {
    verifyRequiredParams(array('name', 'username', 'password'));
    $response = array();
    $name = $app->request->post('name');
    $username = $app->request->post('username');
    $password = $app->request->post('password');
    $db = new DbOperationForCron();
    $res = $db->createStudent($name, $username, $password);
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
        $response["message"] = "Sorry, this student  already existed";
        echoResponse(200, $response);
    }
});

/* *
 * URL: http://localhost/StudentApp/v1/studentlogin
 * Parameters: username, password
 * Method: POST
 * */
$app->post('/studentlogin', function () use ($app) {
    verifyRequiredParams(array('username', 'password'));
    $username = $app->request->post('username');
    $password = $app->request->post('password');
    $db = new DbOperationForCron();
    $response = array();
    if ($db->studentLogin($username, $password)) {
        $student = $db->getStudent($username);
        $response['error'] = false;
        $response['id'] = $student['id'];
        $response['name'] = $student['name'];
        $response['username'] = $student['username'];
        $response['apikey'] = $student['api_key'];
    } else {
        $response['error'] = true;
        $response['message'] = "Invalid username or password";
    }
    echoResponse(200, $response);
});

/* *
 * URL: http://localhost/StudentApp/v1/createfaculty
 * Parameters: name, username, password, subject
 * Method: POST
 * */
$app->post('/createfaculty', function () use ($app) {
    verifyRequiredParams(array('name', 'username', 'password', 'subject'));
    $name = $app->request->post('name');
    $username = $app->request->post('username');
    $password = $app->request->post('password');
    $subject = $app->request->post('subject');

    $db = new DbOperationForCron();
    $response = array();

    $res = $db->createFaculty($name, $username, $password, $subject);
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
        $response["message"] = "Sorry, this faculty already existed";
        echoResponse(200, $response);
    }
});


/* *
 * URL: http://localhost/StudentApp/v1/facultylogin
 * Parameters: username, password
 * Method: POST
 * */

$app->post('/facultylogin', function() use ($app){
    verifyRequiredParams(array('username','password'));
    $username = $app->request->post('username');
    $password = $app->request->post('password');

    $db = new DbOperationForCron();

    $response = array();

    if($db->facultyLogin($username,$password)){
        $faculty = $db->getFaculty($username);
        $response['error'] = false;
        $response['id'] = $faculty['id'];
        $response['name'] = $faculty['name'];
        $response['username'] = $faculty['username'];
        $response['subject'] = $faculty['subject'];
        $response['apikey'] = $faculty['api_key'];
    }else{
        $response['error'] = true;
        $response['message'] = "Invalid username or password";
    }

    echoResponse(200,$response);
});




/* *
 * URL: http://localhost/StudentApp/v1/createassignment
 * Parameters: name, details, facultyid, studentid
 * Method: POST
 * */
$app->post('/createassignment',function() use ($app){
    verifyRequiredParams(array('name','details','facultyid','studentid'));

    $name = $app->request->post('name');
    $details = $app->request->post('details');
    $facultyid = $app->request->post('facultyid');
    $studentid = $app->request->post('studentid');

    $db = new DbOperationForCron();

    $response = array();

    if($db->createAssignment($name,$details,$facultyid,$studentid)){
        $response['error'] = false;
        $response['message'] = "Assignment created successfully";
    }else{
        $response['error'] = true;
        $response['message'] = "Could not create assignment";
    }

    echoResponse(200,$response);

});

/* *
 * URL: http://localhost/StudentApp/v1/assignments/<student_id>
 * Parameters: none
 * Authorization: Put API Key in Request Header
 * Method: GET
 * */
$app->get('/assignments/:id', 'authenticateStudent', function($student_id) use ($app){
    $db = new DbOperationForCron();
    $result = $db->getAssignments($student_id);
    $response = array();
    $response['error'] = false;
    $response['assignments'] = array();
    while($row = $result->fetch_assoc()){
        $temp = array();
        $temp['id']=$row['id'];
        $temp['name'] = $row['name'];
        $temp['details'] = $row['details'];
        $temp['completed'] = $row['completed'];
        $temp['faculty']= $db->getFacultyName($row['faculties_id']);
        array_push($response['assignments'],$temp);
    }
    echoResponse(200,$response);
});


/* *
 * URL: http://localhost/StudentApp/v1/submitassignment/<assignment_id>
 * Parameters: none
 * Authorization: Put API Key in Request Header
 * Method: PUT
 * */

$app->put('/submitassignment/:id', 'authenticateFaculty', function($assignment_id) use ($app){
    $db = new DbOperationForCron();
    $result = $db->updateAssignment($assignment_id);
    $response = array();
    if($result){
        $response['error'] = false;
        $response['message'] = "Assignment submitted successfully";
    }else{
        $response['error'] = true;
        $response['message'] = "Could not submit assignment";
    }
    echoResponse(200,$response);
});


/* *
 * URL: http://localhost/StudentApp/v1/students
 * Parameters: none
 * Authorization: Put API Key in Request Header
 * Method: GET
 * */
$app->get('/students', 'authenticateFaculty', function() use ($app){
    $db = new DbOperationForCron();
    $result = $db->getAllStudents();
    $response = array();
    $response['error'] = false;
    $response['students'] = array();

    while($row = $result->fetch_assoc()){
        $temp = array();
        $temp['id'] = $row['id'];
        $temp['name'] = $row['name'];
        $temp['username'] = $row['username'];
        array_push($response['students'],$temp);
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

function authenticateStudent(\Slim\Route $route)
{
    $headers = apache_request_headers();
    $response = array();
    $app = \Slim\Slim::getInstance();

    if (isset($headers['Authorization'])) {
        $db = new DbOperationForCron();
        $api_key = $headers['Authorization'];
        if (!$db->isValidStudent($api_key)) {
            $response["error"] = true;
            $response["message"] = "Access Denied. Invalid Api key";
            echoResponse(401, $response);
            $app->stop();
        }
    } else {
        $response["error"] = true;
        $response["message"] = "Api key is misssing";
        echoResponse(400, $response);
        $app->stop();
    }
}


function authenticateFaculty(\Slim\Route $route)
{
    $headers = apache_request_headers();
    $response = array();
    $app = \Slim\Slim::getInstance();
    if (isset($headers['Authorization'])) {
        $db = new DbOperationForCron();
        $api_key = $headers['Authorization'];
        if (!$db->isValidFaculty($api_key)) {
            $response["error"] = true;
            $response["message"] = "Access Denied. Invalid Api key";
            echoResponse(401, $response);
            $app->stop();
        }
    } else {
        $response["error"] = true;
        $response["message"] = "Api key is misssing";
        echoResponse(400, $response);
        $app->stop();
    }
}

$app->run();