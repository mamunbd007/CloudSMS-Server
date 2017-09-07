<?php

class DbOperationForRouting
{
    private $con;

    function __construct()
    {
        require_once dirname(__FILE__) . '/DbConnectForRouting.php';
        $db = new DbConnectForCron();
        $this->con = $db->connect();
    }

    public function getQueue()
    {
        $stmt = $this->con->prepare("SELECT *  from sms_queue");
        $stmt->execute();
        $sms_queue = $stmt->get_result()->fetch_all(1);
        /* echo "<pre>";
         var_dump($sms_queue);
         //die();
         echo "</pre>";*/
        $stmt->close();
        return $sms_queue;
    }

    public function getRoutingId($cid)
    {
        $stmt = $this->con->prepare("SELECT routingid  from customers where id=?");
        $stmt->bind_param("i",$cid);
        $stmt->execute();
        $rid = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return reset($rid);
    }

    public function getRoutingTable($rid)
    {
        $stmt = $this->con->prepare("SELECT *  from routing where id=?");
        $stmt->bind_param("i",$rid);
        $stmt->execute();
        $routing = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $routing;
    }

    public function getVendors($vaid)
    {
        $stmt = $this->con->prepare("SELECT *  from routing_branch_vendors_allowed where branchid=?");
        $stmt->bind_param("i",$vaid);
        $stmt->execute();
        $routing = $stmt->get_result()->fetch_all(1);
        $stmt->close();
        return $routing;
    }

    public function getBillingTable($bid)
    {
        $stmt = $this->con->prepare("SELECT *  from billing where id=?");
        $stmt->bind_param("i",$bid);
        $stmt->execute();
        $billing = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $billing;
    }

    public function getBillingBranchRates($bid)
    {
        $stmt = $this->con->prepare("SELECT * FROM `billing_branch_rate` where id=? ");
        $stmt->bind_param("i",$bid);
        $stmt->execute();
        $rates = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $rates;
    }

    public function getVendor($id)
    {
        $stmt = $this->con->prepare("SELECT * FROM `vendors` where id=?");
        $stmt->bind_param("i",$id);
        $stmt->execute();
        $rates = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $rates;
    }

    public function UpdateQueueWithVid($id,$vid)
    {
        $stmt = $this->con->prepare("UPDATE `sms_queue` SET vendorid=? where id=?");
        $stmt->bind_param("ii",$vid,$id);
        $stmt->execute();
        $stmt->close();
    }

    public function UpdateCustomerVendorPriorityCount($cid,$vid,$count)
    {
        $stmt = $this->con->prepare("UPDATE `customers_vendors_priority` SET count=? where cid=? AND vid=?");
        $stmt->bind_param("iii",$count,$cid,$vid);
        $stmt->execute();
        $stmt->close();
    }

    public function UpdateSmsQueueStatus($id,$status)
    {
        $stmt = $this->con->prepare("UPDATE `sms_queue_status` SET status=? where id=?");
        $stmt->bind_param("ii",$status, $id);
        $stmt->execute();
        $stmt->close();
    }

    public function GetPriorityList($cid)
    {
        $stmt = $this->con->prepare("SELECT *  from customers_vendors_priority where cid=?");
        $stmt->bind_param("i",$cid);
        $stmt->execute();
        $routing = $stmt->get_result()->fetch_all(1);
        $stmt->close();
        return $routing;
    }

    public function MoveToSecondQueue()
    {
        /*$stmt = $this->con->prepare("START TRANSACTION;
        INSERT INTO post_sms_queue select * from sms_queue;
        DELETE FROM sms_queue;
        COMMIT;
        ");*/

        $db=$this->con;
        try {
            $db->begin_transaction();
            $db->query("INSERT INTO post_sms_queue select * from sms_queue;");
            $db->query(" DELETE FROM sms_queue;");
            $db->commit();
        }
        catch (Exception $e)
        {
            $db->rollback();
            echo "Error <br>".$e->getMessage();
        }
    }
}

