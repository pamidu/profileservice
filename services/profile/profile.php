<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . "/include/config.php");
        
        class profile {
                public function test(){
                        echo "Hello from profile service V 2.0.1 modified for upay (DuoWorldCommon::GetHost() )";
                }
               public function getProfile($email){
                        $mainDomain=$GLOBALS['mainDomain'];
                        //$mainDomain="my.upay.lk";
                        $headers = apache_request_headers(); 
                        if(!empty($headers['securityToken'])){
                                $token=$headers['securityToken'];
                                //echo "$token";
                                $ch = curl_init();
                                $headers = array(
                                'Accept: application/json',
                                'Content-Type: application/json',

                                );

                                $url="my.upay.lk:3048/GetSession/$token/$mainDomain";
                                //echo "$url";
                                curl_setopt($ch, CURLOPT_URL, $url);
                                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                                curl_setopt($ch, CURLOPT_HEADER, 0);
                                $body = '{}';

                                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET"); 
                                curl_setopt($ch, CURLOPT_POSTFIELDS,$body);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                    // Timeout in seconds
                                curl_setopt($ch, CURLOPT_TIMEOUT, 30);

                                 $authToken = curl_exec($ch);
                                 $authrespond=(json_decode($authToken));
                                 //echo "$authrespond->Error";
                                 if(isset($authrespond->Error)){
                                                echo $authrespond->Message;
                                 }else{
                                        $spaceOwner = str_replace(".", "", str_replace("@", "", $email))."." . $mainDomain;
                                        $client = ObjectStoreClient::WithNamespace($spaceOwner,"profile","123");
                                        $respond=$client->get()->bykey($email);
                                        echo json_encode($respond);
                                 }
                        }else{
                                echo "securityToken fot found ";
                        }

                        
                }
                public function setProfile(){
                        $ec= json_decode(Flight::request()->getBody());
                        $authObj = json_decode($_COOKIE["authData"]);
                        $username = $authObj->Username;
                        $mainDomain=$GLOBALS['mainDomain'];
                        $spaceOwner =  str_replace(".", "", str_replace("@", "", $username))."." . $mainDomain;
                        $client = ObjectStoreClient::WithNamespace($spaceOwner,"profile","123");
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
