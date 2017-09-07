<?php

class DbOperationForCron
{
    private $con;

    function __construct()
    {
        require_once dirname(__FILE__).'/DbConnectForCron.php';
        $db = new DbConnectForCron();
        $this->con = $db->connect();
    }


    //Method to register a new Customer
    public function getQueueStatus($id)
    {
        $stmt = $this->con->prepare("SELECT * FROM `sms_queue_status` WHERE id=?");
        $stmt->bind_param("i",$id);
        $stmt->execute();
        $status = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $status;
    }
}