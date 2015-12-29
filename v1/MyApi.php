<?php
require_once 'API.php';
class MyAPI extends API
{
    protected $User;
    protected $host = "localhost";
    protected $dbUsername = "root";
    protected $dbPassword = "root";
    protected $dbName = "DDHF";
    public function __construct($request, $origin) {
        parent::__construct($request);

    }

    /**
     * Example of an Endpoint
     */
     protected function items() {
        if ($this->method == 'GET') {
            if (strlen($this->verb) == 0 && count($this->args) == 0){
                // Declare the array to hold the data
                $data = Array();

                // Conenct to the database
                $db = $this->connectDB();

                // query the database for the items
                $query = $db->query("SELECT * FROM items");
                
                // check if query went well
                if(!$query){
                    $errorArray = $db->errorInfo();
                    die("DB error " . $errorArray[2]);
                } else{
                    // push the data from the query to the array
                    while($row = $query->fetch()){
                        $row = array("detailsuri" => "/items/" . $row['itemid']) + $row;
                        array_push($data, $row);
                    }
                }
                return $data;
            } else if (count($this->args) == 1 && strlen($this->verb) == 0){
                $db = $this->connectDB();

                $statement = $db->prepare("SELECT * FROM items WHERE itemid = :id");
                $statement->execute(Array(':id' => $this->args[0]));

                if($statement){
                    while($row = $statement->fetch()){
                        return $row;
                    }
                } else{
                    $errorArray = $db->errorInfo();
                    die("DB error: " . $errorArray[2]);
                }
            }
        } else if($this->method == 'POST'){
            // Code to get the Content type header.
            $headers = apache_request_headers();
            // check if the request is a create request.
            if($headers["Content-Type"] != "application/json")
                return "Content-Type has to be application/json for post";
            if(count($this->args) == 0 && strlen($this->verb) == 0){
                $input = file_get_contents('php://input');
                $data = json_decode($input, true);
                
                $itemheadline = (isset($data['itemheadline']) ? $data['itemheadline'] : null);
                $itemdescription = (isset($data['itemdescription']) ? $data['itemdescription'] : null);
                $itemreceived = (isset($data['itemreceived']) ? $data['itemreceived'] : null);
                $itemdatingfrom = (isset($data['itemdatingfrom']) ? $data['itemdatingfrom'] : null);
                $itemdatingto = (isset($data['itemdatingto']) ? $data['itemdatingto'] : null);
                $donator = (isset($data['donator']) ? $data['donator'] : null);
                $producer = (isset($data['producer']) ? $data['producer'] : null);
                $postnummer = (isset($data['postnummer']) ? $data['postnummer'] : null);

                $db = $this->connectDB();

                $statement = $db->prepare("INSERT INTO items (itemheadline, itemdescription, itemreceived, itemdatingfrom,
                                                itemdatingto, donator, producer, postnummer)
                                                values(:itemheadline, :itemdescription, :itemreceived, :itemdatingfrom,
                                                        :itemdatingto, :donator, :producer, :postnummer);");
                $statement->execute(Array(':itemheadline' => $itemheadline,
                                          ':itemdescription' => $itemdescription,
                                          ':itemreceived' => $itemreceived,
                                          'itemdatingfrom' => $itemdatingfrom,
                                          'itemdatingto' => $itemdatingto,
                                          'donator' => $donator,
                                          'producer' => $producer,
                                          'postnummer' => $postnummer));
                
                if($statement)
                    return "succes";
                else
                    return "Something went wrong";
            } elseif(count($this->args) == 1 && $this->verb == 0){
                $input = file_get_contents('php://input');
                $data = json_decode($input, true);

                $db = $this->connectDB();

                $prepareString = "UPDATE items SET ";
                $executeArray = Array();

                foreach ($data as $key => $value) {
                    if ( $key == "itemid" || $key == "created_at" ){
                        continue;
                    }
                    $prepareString .= $key . " = :" . $key . ", ";
                    $executeArray[":" . $key] = $value;
                }
                
                $prepareString = substr($prepareString, 0, -2);
                $prepareString .= " WHERE itemid = :itemid";
                $executeArray['itemid'] = $this->args[0];
                $statement = $db->prepare($prepareString);
                $statement->execute($executeArray);

                if ($statement){
                    return "succes";
                }else{
                    $errorArray = $db->errorInfo();
                    die("DB error: " . $errorArray[2]);
                }
            }
            
        } else {
            return "Request type not POST or GET";
        }
     }

     private function connectDB(){
        try{
            $db = new PDO("mysql:host=$this->host;dbname=$this->dbName", $this->dbUsername, $this->dbPassword);
            $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $e){
            die("ERROR!: " . $e->getMessage());
        }
        return $db;
     }
 }