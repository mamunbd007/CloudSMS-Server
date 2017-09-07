<?php

//Create object of Class Queue to start the execution
$cron=new Cron();


class Cron
{
    //variable for saving database connection
    private $con;
    //constructor
    function __construct()
    {

        //Connect to database
        require_once dirname(__FILE__) . '/DbConnectForCron.php';
        require_once dirname(__FILE__) . '/DbOperationForCron.php';
        require_once '../Routing/Routing.php';
        $db = new DbConnectForCron();
        $this->con = $db->connect();

        //init
        $this->init();

    }

    public function init()
    {
        $db1 = new DbOperationForCron();
        $status=$db1->getQueueStatus(1);
        //var_dump($status);
        if($status==1)
        {
            //Working
            echo "System is Working...";
        }
        else
        {
            //idle
            echo "System is Idle...";
            $route=new Routing($this->con);
        }
    }
}