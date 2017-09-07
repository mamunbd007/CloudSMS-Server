<?php

//Create object of Class Queue to start the execution
//$routing=new Queue();


class Routing
{
    //variable for saving database connection
    private $con;

    //constructor
    function __construct($con)
    {

       //Connect to database
        //require_once dirname(__FILE__) . '/DbConnectForCron.phpron.php';
        require_once dirname(__FILE__) . '/DbOperationForRouting.php';
        //$db = new DbConnectForCron();
       // $this->con = $db->connect();
        $this->con=$con;

        //init
        $this->init();

    }

    //init
    public function init()
    {
        //object of Class DbOperationForCron
        $db=new DbOperationForRouting();

        //Get Queue
        $sms_queue=$db->getQueue();

        {
            /*
             * If Queue is empty, terminate
             */
            if(count($sms_queue,2)==0)
            {
                echo "<br>Queue Empty<br>";
                //$this->DoPriorityCalculation(3);
                return;
            }
        }


        //Update Sms Queue status
        $db->UpdateSmsQueueStatus(1,1);

        foreach ($sms_queue as $queue)
        {
            //Customer ID
            $cid=$queue["customerid"];
            //$this->DoPriorityCalculation($cid);

            {
                //Queue ID
                $rid=$db->getRoutingId($cid);

                //Queue Table

                /*
                 * routing
                 * id, billingid, vendors_allowed
                 */
                $routing=$db->getRoutingTable($rid);

            }

            {
                //Vendors Allowed Id to get Branch of Queue
                $vendors_allowed_id=$routing["vendors_allowed"];

                //Vendors Allowed(Branch of Queue)
                /*
                 * routing_branch_vendor_allowed
                 * id, vendorid, prefix, priority
                 */
                $vendors_allowed=$db->getVendors( $vendors_allowed_id);
				echo"<pre>";
				var_dump($vendors_allowed);
				echo"</pre>";
            }

            {
				$number=$queue["number"]."";
				$prefix_length=array();
				 $valid_vendors_ids=array();
				echo "<br>Prefix: ".$number."";
				foreach($vendors_allowed as $vendor)
				{
					echo "<br> Vendor Prefix: ".$vendor["prefix"]."</br>";
					//var_dump(strpos($number,$vendor["prefix"].""));
					//var_dump(strpos($number,"16ry"));
					if(strpos($number,$vendor["prefix"]."")===0)
					{
						array_push($prefix_length,strlen($vendor["prefix"]));
						array_push($valid_vendors_ids,$vendor["vendorid"]);
						//echo "<br> Exist </br>";
						
					}
				}
				
				if(count($valid_vendors_ids,2)>0)
				{
					echo " Valid vendor IDs <pre>";
					var_dump($valid_vendors_ids);
					echo "</pre>";
				}
				else
				{
					echo " No Valid vendor IDs <pre>";
					var_dump($valid_vendors_ids);
					echo "</pre>";
				}
				
                /*
             * Check Prefix with first 3 digits
             */
                
                /*$digit=3;
                $prefix=$this->GetPrefix($number,$digit);
                $valid_vendors_ids=$this->CheckPrefix($prefix,$vendors_allowed);
				var_dump($valid_vendors_ids);*/

                /*
                 * no valid vendor exists that support this prefix with 3 digits if CheckPrefix() returns -1
                 */
               /* if($valid_vendors_ids==-1)
                {
                    //check again with 5 digits
                    $digit=5;
                    $prefix=$this->GetPrefix($number,$digit);
                    $valid_vendors_ids=$this->CheckPrefix($prefix,$vendors_allowed);
                    if($valid_vendors_ids==-1)
                    {
                        //Prefix Not allowed with 5 digits;
                        //Do something here
                    }
                }*/
            }


            {
                //Billing ID (Customer)
                $bid=$routing["billingid"];

                //Billing Table (Customer)
                /*
                 * billing
                 * id, rate_chart, type, branch_rate_id
                 */
                $billing=$db->getBillingTable($bid);

                //Billing Branch Rate ID
                $billing_branch_rate_id=$billing["branch_rate_id"];
				echo " Billing Branch Rate ID <pre>";
				var_dump($valid_vendors_ids);
				echo "</pre>";
            }

            {
                //Rates Table (Branch of billing) (Customer) based on prefix
                /*
                 * billing_branch_rate
                 * id, prefix, rate, check_rate, bid
                 */
                $billing_branch_rate=$db->getBillingBranchRates($billing_branch_rate_id);
				echo " billing_branch_rate <pre>";
				var_dump($billing_branch_rate);
				echo "</pre>";

                $customer_rate=$billing_branch_rate["rate"];
            }

            {
				
             /*
             * Check if check_rate is enabled
             */
                if($billing_branch_rate["check_rate"])
                {
                    //check_rate Enabled

                    /*
                     * Check if lcr is enabled
                     */
                    if($billing_branch_rate["lcr"])
                    {
                        //LCR Enabled, Do something here
                        /*
                         * Get the Vendor with least cost
                         */
                        $vendor=$this->DoLeastCostRouting($customer_rate,$valid_vendors_ids);
                        if($vendor<0)
                        {
                            //No valid Vendor exist
                            //Do something
                        }
                        else
                        {
                            //Valid Vendor Exist
                            $db->UpdateQueueWithVid($queue["id"],$vendor["id"]);
                            echo $queue["id"]." Table Updated <br>";
                        }
                    }
                    else
                    {
                        //LCR Disabled, Do Priority Calculation
                        $vendor=$this->DoPriorityCalculation($cid,$valid_vendors_ids);
                        echo"<br><br>DoPriorityCalculation";
                        echo "<pre>";
                        var_dump($vendor);
                        echo"<br><br>";
                        echo "</pre>";
                        if($vendor["found"])
                        {
                            if($vendor["updatecount"])
                            {
                                $db->UpdateCustomerVendorPriorityCount($cid,$vendor["vid"],1);
                            }
                            else
                            {
                                $db->UpdateCustomerVendorPriorityCount($cid,$vendor["vid"],$vendor["count"]+1);
                            }
                            $db->UpdateQueueWithVid($queue["id"],$vendor["vid"]);
                        }
                    }
                }
				// Check Rate is Disabled
				else if($billing_branch_rate["lcr"])
				{
					//LCR Enabled, Do something here
					/*
					 * Get the Vendor with least cost
					 */
					$vendor=$this->DoLeastCostRouting($customer_rate,$valid_vendors_ids);
					if($vendor<0)
					{
						//No valid Vendor exist
						//Do something
					}
					else
					{
						//Valid Vendor Exist
						$db->UpdateQueueWithVid($queue["id"],$vendor["id"]);
						echo $queue["id"]." Table Updated <br>";
					}
				}
				// Check Rate and LCR both are Disabled
				else
				{
					//Do Priority Calculation
					$vendor=$this->DoPriorityCalculation($cid,$valid_vendors_ids);
					echo"<br><br>DoPriorityCalculation";
					echo "<pre>";
					var_dump($vendor);
					echo"<br><br>";
					echo "</pre>";
					if($vendor["found"])
					{
						if($vendor["updatecount"])
						{
							$db->UpdateCustomerVendorPriorityCount($cid,$vendor["vid"],1);
						}
						else
						{
							$db->UpdateCustomerVendorPriorityCount($cid,$vendor["vid"],$vendor["count"]+1);
						}
						$db->UpdateQueueWithVid($queue["id"],$vendor["vid"]);
					}
				}
            }
        }

        {
            /*
             * End of Vendor selection
             * Move to Second Queue
             */
           // $db->MoveToSecondQueue();
           // $db->UpdateSmsQueueStatus(1,0);
            echo "<br>Move to Second Queue. This Queue is Idle <br>";
        }



    }

    /*
     * Find the vendor with least cost
     */
    public function DoLeastCostRouting($customer_rate,$valid_vendors_ids)
    {
        $vendors=$this->GetVendorsAndRates($valid_vendors_ids);
        $lowest_rate = min(array_column($vendors, 'rate'));
        $min=0;
        $vid=0;
        foreach($vendors as $vendor)
        {
            if($vendor["rate"]<$customer_rate)
            {
               $min= $vendor["rate"];
               $vid=$vendor["vid"];
            }
        }
        if(!$min==0&&!$vid==0)
        {
            $valid_vendor=array();
            $valid_vendor["id"]=$vid;
            $valid_vendor["rate"]=$min;
            return $valid_vendor;
        }
        else
        {
            return 0;
        }
        //$index= array_search($lowest_rate, $vendors);
        //var_dump($vendors);
    }

    /*
     * Find the vendor with highest priority
     */
    public function DoPriorityCalculation($cid,$valid_vendors_ids)
    {
        $db=new DbOperationForRouting();
        $vendors=$db->GetPriorityList($cid);

        echo"<br>GetPriorityList";
        echo "<pre>";
        var_dump($vendors);
        echo "</pre>";
        echo"<br>";

        $vendor = array();



        $vidfound=false;
        for($i=0;$i<count($vendors,2);$i++)
        {
            $check=$this->CheckValidVendorIdForPriority($vendors[$i]["id"],$valid_vendors_ids);
            if($check)
            {
                if($vidfound==false)
                {
                    $vidfound=true;
                }
                if($vendors[$i]["count"]<$vendors[$i]["priority"])
                {
                    $vendorid=$vendors[$i]["vid"];
                    $count=$vendors[$i]["count"];
                    break;
                }
                elseif ($vendors[$i]["count"]==$vendors[$i]["priority"])
                {
                    $vendorid=0;
                   // continue;
                }
            }

        }
        if($vidfound)
        {
            if($vendorid>0)
            {
                $updatecount=false;
            }
            else {
                $first = false;
                for ($i = 0; $i < count($vendors, 2); $i++) {
                    $check = $this->CheckValidVendorIdForPriority($vendors[$i]["id"], $valid_vendors_ids);
                    if ($check) {
                        if (!$first) {
                            $first = true;
                            $updatecount = true;
                            $vendorid = $vendors[$i]["vid"];
                            $count = $vendors[$i]["count"];
                        }
                        $db->UpdateCustomerVendorPriorityCount($cid, $vendors[$i]["id"], 0);
                        //break;
                    }
                }
            }
        }
        else
        {
            $vendor["found"]=false;
            return $vendor;
        }


        $vendor["vid"]=$vendorid;
        $vendor["updatecount"]=$updatecount;
        $vendor["count"]=$count;
        $vendor["found"]=true;
        return $vendor;




    }
    public function CheckValidVendorIdForPriority($vendorid,$valid_vendors_ids)
    {
        for($i=0;$i<count($valid_vendors_ids,2);$i++)
        {
            if($vendorid==$valid_vendors_ids[$i])
            {
                echo"<br>CheckValidVendorIdForPriority";
                echo "<pre>";
                var_dump($vendorid);
                echo "</pre>";
                echo"<br>";
                return true;
            }
        }
        return false;
    }

    public function GetVendorsAndRates($vendors)
    {
        $db=new DbOperationForRouting();
        $vendors_rates=array ();
        $count=0;
        for($i=0;$i<count($vendors,2);$i++)
        {
            $vendor=$db->getVendor($vendors[$i]);
            //var_dump($vendor);
            //Get billing table
            $vendor_billing=$db->getBillingTable($vendor["billingid"]);
           // var_dump($vendor_billing);
            $vendor_billing_branch_rate=$db->getBillingBranchRates($vendor_billing["branch_rate_id"]);
            $vendors_rates[$count]["vid"]=$vendor["id"];
            $vendors_rates[$count]["rate"]=$vendor_billing_branch_rate["rate"];
            $count++;
        }
        //var_dump($vendors_rates);
        return $vendors_rates;

    }

    /*
     * Get 0 to $digit digits from the number to get the prefix
     */
    public function GetPrefix($number, $digit)
    {
       return (int) substr($number, 0, $digit);
    }

    /*
     * Check if prefix is allowed for the customer
     */
    public function CheckPrefix($prefix,$vendors_allowed)
    {
        $vendors= array();
        foreach ($vendors_allowed as $vendor)
        {

            if($vendor["prefix"]==$prefix)
            {
                /*
                 * Push valid vendor id that supports this prefix
                 */
                array_push($vendors,$vendor["vendorid"]);

            }
        }
        if(count($vendors,2)) {
            return $vendors;
        }

        //No valid vendor
        return -1;
    }

}

?>
