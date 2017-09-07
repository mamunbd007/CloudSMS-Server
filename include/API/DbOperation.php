<?php

class DbOperation
{
    private $con;

    function __construct()
    {
        require_once dirname(__FILE__) . '/DbConnect.php';
        $db = new DbConnect();
        $this->con = $db->connect();
    }
	
	
	/*
	** Customer Section
	**/

   //Method to register a new Customer
    public function createCustomer($username,$pass,$name,$phone,$email){
        if (!$this->isCustomerExists($username)) {
            $password = md5($pass);
            $apikey = $this->generateApiKey();
            $balance=0;
            $tp=0;
            $routingid=0;
            $stmt = $this->con->prepare("INSERT INTO customers(username, password, api_key, name, phone, email, balance, tp, routingid) values(?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssiii",  $username, $password, $apikey, $name, $phone, $email, $balance, $tp, $routingid);
            $result = $stmt->execute();
			
			
            $stmt->close();
			//return $result;
            if ($result) {
                return 0;
            } else {
                return 1;
            }
        } else {
            return 2;
        }
    }
	
	//Method to check the customer username already exist or not
    private function isCustomerExists($username) {
        $stmt = $this->con->prepare("SELECT id from customers WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }
	
	//Method to update customer info
    public function updateCustomer($username,$password,$name,$phone,$email){
			
            if( $id = $this->getCustomerId($username))
            {
				//$password = md5($pass);
				$password=md5($password);

                $stmt = $this->con->prepare("UPDATE customers SET  password = ?, name = ?, phone = ? , email = ? WHERE username = ?");
                $stmt->bind_param("sssss", $password,$name,$phone,$email,$username);
                $result = $stmt->execute();
                $stmt->close();
                return $id;
            }
            else
            {
                return 0;
            }
    }
	
	private function isCustomerExistsById($id) {
        $stmt = $this->con->prepare("SELECT username from customers WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }
	
	//Method to get Customer Id from Customer name
    private function getCustomerId($username)
    {
        $stmt = $this->con->prepare("SELECT id from customers WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $id = $stmt->get_result()->fetch_assoc();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        if( $id)
        {
            return reset($id);
        }
        else
        {
            return 0;
        }

    }
	
	 //Method to get Customer Tp from Customer ID
    private function getCustomerTp($id)
    {
        $stmt = $this->con->prepare("SELECT tp from customers WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $tp = $stmt->get_result()->fetch_assoc();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        if( $tp)
        {
            return reset($tp);
        }
        else
        {
            return 0;
        }

    }
	
	//Method to add and update Customer Balance
    public function updateCustomerBalance($username,$balance){
        if( $id = $this->getCustomerId($username))
        {

            $stmt = $this->con->prepare("UPDATE customers SET balance = ? WHERE id = ?");
            $stmt->bind_param("ii",  $balance, $id);
            $result = $stmt->execute();
            $stmt->close();
            return $id;
        }
        else
        {
            return 0;
        }
    }

    //Method to get single customer details
    public function getCustomer($username){
        $stmt = $this->con->prepare("SELECT * FROM customers WHERE username=?");
        $stmt->bind_param("s",$username);
        $stmt->execute();
        $customer = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $customer;
    }

    //Method to get single customer details
    public function getCustomerByID($id){
        $stmt = $this->con->prepare("SELECT * FROM customers WHERE id=?");
        $stmt->bind_param("i",$id);
        $stmt->execute();
        $customer = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $customer;
    }

    //Method to get customer details
    public function getCustomers(){
        $stmt = $this->con->prepare("SELECT * FROM customers");
       // $stmt->bind_param("s",$username);
        $stmt->execute();
        $customers = $stmt->get_result()->fetch_all(1);
        $stmt->close();
        return $customers;
    }

    //Checking the Customer is valid or not by api key
    public function isValidCustomer($api_key){
        $stmt = $this->con->prepare("SELECT id from customers WHERE api_key=?");
        $stmt->bind_param("s",$api_key);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows>0;
    }
	
	
	/*
	** Customer Section Ended
	**
	**
	**
	*/
	
	/*
	** Vendor Section
	**
	**
	**
	*/
	
	 //Method to register a new Vendor
		public function createVendor($name){
			if (!$this->isVendorExists($name)) {
				$balance=0;
				$tp=0;
				$billingid=0;
				$stmt = $this->con->prepare("INSERT INTO vendors(name, balance, tp, billingid) values(?, ?, ?, ?)");
				$stmt->bind_param("siii",  $name, $balance, $tp, $billingid);
				$result = $stmt->execute();
				$stmt->close();


				if ($result) {
					return 0;
				} else {
					return 1;
				}
			} else {
				return 2;
			}
		}

		//Method to check the vendor name already exist or not
		private function isVendorExists($name) {
			$stmt = $this->con->prepare("SELECT * from vendors WHERE name = ?");
			$stmt->bind_param("s", $name);
			$stmt->execute();
			$stmt->store_result();
			$num_rows = $stmt->num_rows;
			$stmt->close();
			return $num_rows > 0;
		}

		//Method to check the vendor id already exist or not
		private function isVendorExistsById($id) {
			$stmt = $this->con->prepare("SELECT name from vendors WHERE id = ?");
			$stmt->bind_param("i", $id);
			$stmt->execute();
			$stmt->store_result();
			$num_rows = $stmt->num_rows;
			$stmt->close();
			return $num_rows > 0;
		}
		
		//Method to get Vendor Id from vendor name
		private function getVendorId($name)
		{
			$stmt = $this->con->prepare("SELECT id from vendors WHERE name = ?");
			$stmt->bind_param("s", $name);
			$stmt->execute();
			$id = $stmt->get_result()->fetch_assoc();
			$num_rows = $stmt->num_rows;
			$stmt->close();
			if( $id)
			{
				return reset($id);
			}
			else
			{
				return 0;
			}

		}

		//Method to add and update Vendor Balance
		public function updateVendorBalance($name,$balance){
				if( $id = $this->getVendorId($name))
				{

					$stmt = $this->con->prepare("UPDATE vendors SET balance = ? WHERE id = ?");
					$stmt->bind_param("ii",  $balance, $id);
					$result = $stmt->execute();
					$stmt->close();
					return $id;
				}
				else
				{
					return 0;
				}
		}
		
		    //Method to get customer details
    public function getVendors(){
        $stmt = $this->con->prepare("SELECT * FROM vendors");
       // $stmt->bind_param("s",$username);
        $stmt->execute();
        $vendors = $stmt->get_result()->fetch_all(1);
        $stmt->close();
        return $vendors;
    }
	
	//Method to update customer info
    public function updateVendor($name,$tp,$balance){
			
            if( $id = $this->getVendorId($name))
            {
				//$password = md5($pass);
				//$password=md5($password);

                $stmt = $this->con->prepare("UPDATE vendors SET  name = ?, balance = ? , tp = ? WHERE id = ?");
                $stmt->bind_param("siii", $name,$balance,$tp,$id);
                $result = $stmt->execute();
                $stmt->close();
                return $id;
            }
            else
            {
                return 0;
            }
    }
	

	/*
	** Vendor Section ENDED
	**
	**
	**
	*/
	
	
	
	//Method for add billing
    public function addBilling($rate_chart, $type, $prefix, $rate){
			$db=$this->con;
			try{
				$db->begin_transaction();
				$lcr = 0;
				$checkrate = 1;
				$db->query("INSERT INTO billing(rate_chart,type) values (?, ?)");
				$db -> bind_param("ss", $rate_chart, $type);
				$result_one = $db->execute();
			
				for ($i = 0; $i < count($prefix); $i++) {
								
					$prefix = $prefix[$i];
					$rete = $rate[$i];
					
					$db->query("INSERT INTO billing_branch_rate(prefix,rate,lcr,check_rate,bid) values(?,?, ?, ?, ?)");
					$db->bind_param("idiis",  $prefix,$rate,$lcr,$checkrate,$rate_chart);
					$result_two = $db->execute();
					
				}
				$db->commit();
			}catch (Exception $e)
				{
					$db->rollback();
					echo "Error <br>".$e->getMessage();
				}
    }
	
	//Method for add billing
    public function getPrefixForRouting($id)
	{
		$stmt = $this->con->prepare("SELECT id from billing_branch_rate where ");			
    }
	
	//Method for get Billing Branch Rate
    public function getBillingBranchRate($id,$type)
	{
		$stmt = $this->con->prepare("SELECT * from billing_branch_rate where id=? AND type=?");	
		$stmt->bind_param("is", $id, $type);
		$stmt->execute();		
		$billing_branch = $stmt->get_result()->fetch_all(1);
		$stmt->close();
		return $billing_branch;
    }
	
	//Method for get Billing Branch Rate
    public function getBillingBranchRateByType($type)
	{
		$stmt = $this->con->prepare("SELECT * from billing_branch_rate where type=?");	
		$stmt->bind_param("s", $type);
		$stmt->execute();		
		$billing_branch = $stmt->get_result()->fetch_all(1);
		$stmt->close();
		return $billing_branch;
    }
	
	


    //Method to check the customer exist or not (Login Auth)
    public function checkLogin($username,$password) {
        $stmt = $this->con->prepare("SELECT * from admins WHERE username = ? AND password=?");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        //$stmt->store_result();
        $sms_queue = $stmt->get_result()->fetch_all(1);
        $stmt->close();
        return $sms_queue;
    }

    



   


    //Method to add and update Vendor Balance
    public function addVendorToCustomer($cid,$vid)
    {

        if (($id = $this->isVendorExistsById($vid))&&$this->isCustomerExistsById($cid))
        {
            //  {

            $stmt = $this->con->prepare("UPDATE customers SET vid = ? WHERE id = ?");
            $stmt->bind_param("ii", $vid, $cid);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        }
        else
        {
            return 0;
        }

      //  }
      //  else
      //  {
       //     return 0;
     //   }
    }

    // Method  for get branch id by customer or vendor type
	public function getBranchIds($type){
		$stmt = $this->con->prepare("SELECT * from billing WHERE type = ?");
        $stmt->bind_param("s", $type);
		$stmt->execute();
        $id = $stmt->get_result()->fetch_all(1);
        $num_rows = $stmt->num_rows;
        $stmt->close();
		return $id;
        /* if( $id)
        {
            return $id;
        }
        else
        {
            return 0;
        } */
	}

    
    //Method to Reporting
    public function updateQueueForMultipleSms($data){

        if($this->isCustomerExistsById($data["id"]))
        {

            $id=$data["id"];
            $sms=$data["sms"];
            $count=count($sms,2);
            $vendorid=0;
            $tp=$this->getCustomerTp($id);
            //$response=$tp;
            if($tp==0)
            {
                $response["error"] = true;
                $response["message"]="TP limit 0";
                return $response;
            }
            else if($count>$tp)
            {
                $response["error"] = false;
                $response["sms_kept"]=$tp;
                $response["sms_discarded"]=$count-$tp;
                $count=$tp;
                $response["alert"]="TP limit crossed";

            }
            for($i=0;$i<$count;$i++)
            {

                $stmt = $this->con->prepare("INSERT INTO sms_queue(customerid, number, sms, vendorid) values(?, ?, ?, ?)");
                $stmt->bind_param("issi",  $id, $sms[$i]["number"], $sms[$i]["text"], $vendorid);
                $result = $stmt->execute();
                $stmt->close();
                $response["error"] = false;
                //$response["alert"]="Crossed TP limit. First ".$count." sms are kept, rest are discarded.";
                $response["message"]="Successful";
                //$response="Crossed TP limit. First ".$count." sms are kept, rest are discarded.";
            }
            return $response;
          }
        else
        {
            $response["error"] = true;
            $response["message"]="Customer ID not found";
            return $response;
        }
    }

    public function updateQueueForSingleSms($data){

        if($this->isCustomerExistsById($data["id"]))
        {
            $id=$data["id"];
            $sms=$data["sms"];
            $numbers=$data["numbers"];
            $count=count($numbers,2);
            $vendorid=0;
            $tp=$this->getCustomerTp($id);
            if($tp==0)
            {
                $response["error"] = true;
                $response["message"]="TP limit 0";
                return $response;
            }
            else if($count>$tp)
            {

                $response["error"] = false;
                $response["sms_kept"]=$tp;
                $response["sms_discarded"]=$count-$tp;
                $count=$tp;
                $response["alert"]="TP limit crossed";
                //$response["message"]="Partially Successful";

            }
            for($i=0;$i<$count;$i++)
            {
                $stmt = $this->con->prepare("INSERT INTO sms_queue(customerid, number, sms, vendorid) values(?, ?, ?, ?)");
                $stmt->bind_param("issi",  $id, $numbers[$i], $sms, $vendorid);
                $result = $stmt->execute();
                $stmt->close();
                $response["error"] = false;
                $response["message"]="Successful";
            }
            return $response;
        }
        else
        {
            $response["error"] = true;
            $response["message"]="Customer ID not found";
            return $response;
        }
    }

    //Method to generate a unique api key every time
    private function generateApiKey(){
        return md5(uniqid(rand(), true));
    }
}