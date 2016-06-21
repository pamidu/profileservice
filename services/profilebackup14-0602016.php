<?php
//require_once ($_SERVER['DOCUMENT_ROOT'] . "/include/config.php");

        
        class profile {
                public function test(){
                        echo "Hello from profile service";
                }
                 public function getProfile($email){
                        $authObj = json_decode($_COOKIE["authData"]);
                        $username = $authObj->Username;
                                $mainDomain=$GLOBALS['mainDomain'];
                        $spaceOwner = str_replace(".", "", str_replace("@", "", $username)). ".space." . $mainDomain;
                        $client = ObjectStoreClient::WithNamespace($spaceOwner,"profile","123");
                        $respond=$client->get()->bykey($email);
                        echo json_encode($respond);
                        //var_dump($GLOBALS['mainDomain']);
                }
                public function setProfile(){
                         $ec= json_decode(Flight::request()->getBody());
                     //   var_dump($ec);
                        //echo json_encode(Flight::request()->getBody());
                        //echo json_encode(Flight::request()->data);
                        $authObj = json_decode($_COOKIE["authData"]);
                        $username = $authObj->Username;
                        $mainDomain=$GLOBALS['mainDomain'];
                        $spaceOwner = str_replace(".", "", str_replace("@", "", $username)). ".space." . $mainDomain;
                        $client = ObjectStoreClient::WithNamespace($spaceOwner,"profile","123");
                        //echo json_encode($ec);
                        $storeRespond=$client->store()->byKeyField("Email")->andStore($ec);
                        echo json_encode($storeRespond);
                }
                function __construct($isHttpMode = true){
               
                        if ($isHttpMode){ 
                                $this->isHttpMode = true;
                                Flight::route("GET /userprofile/test",function(){$this->test();});
                                Flight::route("GET /userprofile/@email",function($email){$this->getProfile($email);});
                                Flight::route("POST /userprofile",function(){$this->setProfile();});
                                
                                        
      
                                header('Content-Type: application/json');
                                header('Access-Control-Allow-Headers: Content-Type');
                                header('Access-Control-Allow-Origin: *');
                                header('Access-Control-Allow-Methods: GET, POST');
                        }
                
                }
        }
?>