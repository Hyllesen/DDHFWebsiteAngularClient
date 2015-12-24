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

                
                // Setup the database connection
                $db = $this->connectDB();

                $query = $db->query("SELECT * FROM items");
                
                if(!$query){
                    $errorArray = $db->errorInfo();
                    die("DB error " . $errorArray[2]);
                } else{
                    while($row = $query->fetch()){
                        array_push($data, $row);
                    }
                }
                echo json_encode($data, JSON_PRETTY_PRINT);
            } else if (count($this->args) == 1){
                echo $this->args[0];
            }
        } else {
            return "Only accepts GET requests";
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