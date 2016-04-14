<?php
require_once 'API.php';
require_once '../Config.php';
class MyAPI extends API
{
    protected $User;

    protected $config; 

    protected  $updateableFields = Array("itemheadline", 
                                         "itemdescription",
                                         "itemreceived",
                                         "itemdatingto", 
                                         "itemdatingfrom",
                                         "donator",
                                         "producer",
                                         "postnummer",
                                         "emnegruppe",
                                         "betegnelse");

    protected $supportedImageFormats = Array("image/png", "image/jpeg", "image/jpg");
    protected $supportedAudioFormats = Array("audio/mp4", "audio/3gp", "audio/3GPP", "audio/MPEG-4", "audio/aac", "audio/mp3");
    protected $audioFormats = Array("mp4", "3gp", "aac", "mp3");
    public function __construct($request, $origin) {
        $this->config = new Config();
        parent::__construct($request);

    }


     protected function items() {
        if (!isset($_GET['userID']) || $_GET['userID'] != $this->config->authorizedID){
            $this->status = 401;
            return $this->config->authorizedID . "  -  " . $_GET['id'] . "   -  Access denied";
        }
        if ($this->method == 'GET') {
            if (strlen($this->verb) == 0 && count($this->args) == 0){
                // Declare the array to hold the data
                $data = Array();

                // Conenct to the database
                $db = $this->connectDB();

                $query = $db->query("SELECT items.itemid, items.itemheadline, files.filename
                                    FROM items 
                                    LEFT JOIN files ON files.itemid=items.itemid
                                    ORDER BY items.itemid DESC");
                
                
                // check if query went well
                if(!$query){
                    $errorArray = $db->errorInfo();
                    die("DB error " . $errorArray[2]);
                } else{
                    // push the data from the query to the array
                    while($row = $query->fetch()){
                        if(!isset($data[0])){
                            if($row['filename'] == NULL){
                                $filename = null;
                            } else{
                                $filename = $this->config->uploadURL . $row['filename'];
                            }
                            $tempArray = array("detailsuri" => "/items/" . $row['itemid'], 
                                               "itemid" => $row['itemid'],
                                               "itemheadline" => $row['itemheadline'],
                                               "defaultimage" => $filename);
                            array_push($data, $tempArray);
                        }else{
                            if($row['itemid'] != $data[count($data)-1]['itemid']){
                                if($row['filename'] == NULL){
                                    $filename = null;
                                } else if(in_array(substr($row['filename'], strlen($row['filename'])-3), $this->audioFormats)) {
                                    $filename = null;
                                } else{
                                    $filename = "http://msondrup.dk/api/uploads/" . $row['filename'];
                                }
                                $tempArray = array("detailsuri" => "/items/" . $row['itemid'], 
                                                   "itemid" => $row['itemid'],
                                                   "itemheadline" => $row['itemheadline'],
                                                   "defaultimage" => $filename);
                                array_push($data, $tempArray);
                            } else{
                                if($data[count($data)-1]['defaultimage'] == null){
                                    if($row['filename'] == NULL){
                                    $filename = null;
                                } else if(in_array(substr($row['filename'], strlen($row['filename'])-3), $this->audioFormats)) {
                                    $filename = null;
                                } else{
                                    $filename = "http://msondrup.dk/api/uploads/" . $row['filename'];    //Hardcoded URL --> BAD
                                }
                                $tempArray = array("detailsuri" => "/items/" . $row['itemid'], 
                                                   "itemid" => $row['itemid'],
                                                   "itemheadline" => $row['itemheadline'],
                                                   "defaultimage" => $filename);
                                }
                            }
                        }
                    } 
                }
                return $data;
            } else if (count($this->args) == 1 && strlen($this->verb) == 0){
                $db = $this->connectDB();

                $statement = $db->prepare("SELECT * FROM items WHERE itemid = :id");
                $statement->execute(Array(':id' => $this->args[0]));

                if($statement){
                    $result;
                    while($row = $statement->fetch()){
                        $result = $row;
                    }
                    $statement = $db->prepare("SELECT * FROM files WHERE itemid = :id order by created_at asc");
                    $statement->execute(Array(':id' => $result['itemid']));
                    $imageArray = array();
                    $audioArray = array();
                    while($row = $statement->fetch()){
                        if($row['type'] == "audio"){
                            $audioArray['audio_' . count($audioArray)] = array("filename" => $row['filename'],
                                                                                "href" => "http://msondrup.dk/api/uploads/" . $row['filename']);
                        } elseif($row['type'] == "image"){
                            $imageArray['image_' . count($imageArray)] = array("filename" => $row['filename'],
                                                                                "href" => "http://msondrup.dk/api/uploads/" . $row['filename']);
                        }
                    }
                    if(count($imageArray) != 0){
                        $result['images'] = $imageArray;
                    }
                    if(count($audioArray) != 0){
                        $result['audios'] = $audioArray;
                    }
                    return $result;
                } else{
                    $errorArray = $db->errorInfo();
                    die("DB error: " . $errorArray[2]);
                }
            }
        } else if($this->method == 'POST'){
            // Code to get the Content type header.
            $headers = apache_request_headers();
            // check if the request is a create request.
            if(count($this->args) == 0 && strlen($this->verb) == 0 && $headers['Content-Type'] == "application/json"){
                // Method to create items
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
                $emnegruppe = (isset($data['emnegruppe']) ? $data['emnegruppe'] : null);
                $betegnelse = (isset($data['betegnelse']) ? $data['betegnelse'] : null);

                $db = $this->connectDB();

                $statement = $db->prepare("INSERT INTO items (itemheadline, itemdescription, itemreceived, itemdatingfrom,
                                                itemdatingto, donator, producer, postnummer, emnegruppe, betegnelse)
                                                values(:itemheadline, :itemdescription, :itemreceived, :itemdatingfrom,
                                                        :itemdatingto, :donator, :producer, :postnummer, :emnegruppe, :betegnelse);");
                $statement->execute(Array(':itemheadline' => $itemheadline,
                                          ':itemdescription' => $itemdescription,
                                          ':itemreceived' => $itemreceived,
                                          'itemdatingfrom' => $itemdatingfrom,
                                          'itemdatingto' => $itemdatingto,
                                          'donator' => $donator,
                                          'producer' => $producer,
                                          'postnummer' => $postnummer,
                                          'emnegruppe' => $emnegruppe,
                                          'betegnelse' => $betegnelse));
                
                if($statement){
                    // TODO skriv den oprettede række ud.
                    $statement = $db->prepare("SELECT * FROM items WHERE itemid = :itemid");
                    $statement->execute(array(":itemid" => $db->lastInsertId()));
                    $this->status = 201;
                    while($row = $statement->fetch()){
                        return $row;
                    }
                    
                }else{
                    $this->status = 500;
                    return "Database error";
                }
            } elseif(count($this->args) == 1 && $this->verb == 0 && $headers['Content-Type'] == "application/json"){
                $input = file_get_contents('php://input');
                $data = json_decode($input, true);

                $db = $this->connectDB();

                $prepareString = "UPDATE items SET ";
                $executeArray = Array();
               

                foreach ($data as $key => $value) {
                    if(in_array($key, $this->updateableFields)){
                        $prepareString .= $key . " = :" . $key . ", ";
                        $executeArray[":" . $key] = $value;
                    }
                }
                
                $prepareString = substr($prepareString, 0, -2);
                $prepareString .= " WHERE itemid = :itemid";
                $executeArray['itemid'] = $this->args[0];
                $statement = $db->prepare($prepareString);
                $statement->execute($executeArray);

                if ($statement){
                    $statement = $db->prepare("SELECT * FROM items WHERE itemid = :itemid");
                    $statement->execute(array(":itemid" => $db->lastInsertId()));
                    
                    while($row = $statement->fetch()){
                        return $row;
                    }
                }else{
                    $errorArray = $db->errorInfo();
                    die("DB error: " . $errorArray[2]);
                } 
            } elseif(count($this->args) == 1 && $this->verb == 0){
                $file = file_get_contents('php://input');
                if (strlen($file)/1000 > 100000){
                    $this->status = 405;
                    return "File is too big for upload";
                }
                if (in_array($headers['Content-Type'], $this->supportedImageFormats)){
                    $type = "image";
                    if($headers['Content-Type'] == "image/jpeg"){
                        $fileType = ".jpg";
                    } else{
                        $fileType = "." . substr($headers['Content-Type'], 6);
                    }
                } elseif(in_array($headers['Content-Type'], $this->supportedAudioFormats)){
                    $type = "audio";
                    if($headers['Content-Type'] == "audio/3GPP"){
                        $fileType = ".3gp";
                    } elseif($headers['Content-Type'] == "audio/MPEG-4"){
                        $fileType = ".mp4";
                    } else{
                        $fileType = "." . substr($headers['Content-Type'], 6);
                    }
                } else{
                    $this->status = 405;
                    return "Unknown content type";
                } 

                $dir = "../uploads/";
                $filename = uniqid($this->args[0] . "_") . $fileType;

                $result = file_put_contents($dir . $filename, file_get_contents('php://input'));  

                if($result){
                    $db = $this->connectDB();

                    $statement = $db->prepare("INSERT INTO files (filename, type, itemid) values(:filename, :type, :itemid)");
                    $statement -> execute(Array(':filename' => $filename, ':type' => $type, ':itemid' => $this->args[0]));
                    if(!$statement){
                        $this->status = 500;
                        return "database error";
                    }
                } else{
                    $this->status = 500;
                    return "File creation failed";
                }

                return array("filename" => $filename, "savedAt" => "http://msondrup.dk/api/uploads/" . $filename);
            }
            
        } elseif( $this->method == "DELETE" && strlen($this->verb) == 0 && count($this->args) == 1) {
            $db = $this->connectDB();

            $statement = $db->prepare("DELETE FROM items WHERE itemid = :itemid");
            $statement->execute(array(":itemid" => $this->args[0]));
        } elseif( $this->method == "DELETE" && strlen($this->verb) == 0 && count($this->args) == 2){
            $db = $this->connectDB();

            $statement = $db->prepare("DELETE FROM files WHERE filename = :filename");
            $statement->execute(array(":filename" => $this->args[1]));
            if($statement){
                unlink("../uploads/" . $this->args[1]);    
                return "item was deleted";
            } else{
                $this->status = 500;
                return "Database error when deleteing the file.";
            }
            
           
        } else {
            $this->status = 404;
            return "Unknown method: Request type not POST, GET or DELETE";
        }
     }

     private function connectDB(){
        try{
            $db = new PDO("mysql:host=" . $this->config->host . ";dbname=" . $this->config->dbName, $this->config->dbUsername, $this->config->dbPassword);
            $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $e){
            die("ERROR!: " . $e->getMessage());
        }
        return $db;
     }
 }