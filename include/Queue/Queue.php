<?php
require '../.././vendor/autoload.php';
class Queue
{
    private $con;

    function __construct($con)
    {

        require_once dirname(__FILE__) . '/DbOperationForQM.php';

        $this->con=$con;
        $this->init();
    }

    public function init()
    {
        //Do something here
        $db=new DbOperationForQM();

        $db->UpdateSmsQueueStatus(2,1);
        $queue=$db->getQueue();
        echo "<pre>";
        var_dump($queue);
        echo "</pre>";
        $client = new GuzzleHttp\Client(['base_uri' => 'http://localhost/sms/modules/customers/']);

        for($i=0;$i<count($queue,2);$i++)
        {
            // Send a request to https://foo.com/api/test
            // $response = $client->request('GET', 'getsms');
            $response = $client->request('POST', 'check', [
                'form_params' => [
                    'user' => 'color_box',
                    'pass' => 'colorbox667',
                    'todo' => 'single_sms',
                    'sender' => 'color_box',
                    'receiver' => $queue[$i]["number"],
                    'message' => $queue[$i]["sms"],
                    'service-type' => 'sms'
                ]
            ]);
            $response = $response->getBody()->getContents();
            $response=json_decode($response, true);
            $db->InsertIntoFinalQueue($queue[$i]["customerid"],$queue[$i]["number"],$queue[$i]["sms"],$queue[$i]["vid"],$response["single_sms"]["message"]
                ,$response["error"]
                );

        }

        $db->DeleteQueue();

        $db->UpdateSmsQueueStatus(2,0);

    }
}

