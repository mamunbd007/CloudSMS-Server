<?php

class DbOperationForQM
{
    private $con;

    function __construct()
    {
        require_once dirname(__FILE__) . '/DbConnectForQM.php';
        $db = new DbConnectForCron();
        $this->con = $db->connect();
    }

    public function getQueue()
    {
        $stmt = $this->con->prepare("SELECT *  from post_sms_queue");
        $stmt->execute();
        $sms_queue = $stmt->get_result()->fetch_all(1);
        /* echo "<pre>";
         var_dump($sms_queue);
         //die();
         echo "</pre>";*/
        $stmt->close();
        return $sms_queue;
    }

    public function InsertIntoFinalQueue($cid,$number,$sms,$vid,$message,$error)
    {
        $stmt = $this->con->prepare("INSERT INTO sms_record(cid, number, sms, vid, message, error) values(?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issisi",  $cid, $number, $sms, $vid, $message,$error);
        $result = $stmt->execute();
        $stmt->close();
    }

    public function UpdateSmsQueueStatus($id,$status)
    {
        $stmt = $this->con->prepare("UPDATE `sms_queue_status` SET status=? where id=?");
        $stmt->bind_param("ii",$status, $id);
        $stmt->execute();
        $stmt->close();
    }

    public function DeleteQueue()
    {
        $stmt = $this->con->prepare("DELETE FROM post_sms_queue");
        //$stmt->bind_param("ii",$status, $id);
        $stmt->execute();
        $stmt->close();
    }
}

