<?php
    //header('Content-Type: application/json');
    
    
    /*
     * getVal
     */
    function getVal($val){
        
        if (isset($_GET[$val])){
            
            return $_GET[$val];
        }else
            return null;
        
    }
    
    
    function delFile($tracker,$id){
       
            $id = stripslashes(str_replace("\"", "", $id));
            if ($file = $tracker->selectFile($id)){
                
                
                if (file_exists($file[0]['src'])){
                    if (!unlink($file[0]['src']))
                        return -1; //
                }else
                    return -2;
                
                if ($tracker->delFile($id)){
                   return 0;
                }else{
                   return -3;
                }
                
            }
        
    }
    
    
   $result["success"] = false;
   $result["post"] = $_POST;
   
   $param = strip_tags(getVal('param'));
    
    if ($param){
        
        include('conf.php');
        include('models/tracker.class.php');
        
    
        $table_points = $mysql_tbl_pref.$mysql_tbl_points;
        $tracker = new tracker($mysql_host, $mysql_user, $mysql_password, $mysql_db,$table_points);
        
        // send param of get
        $result['param'] = $param;

        
        switch ($param ) {
            case 'null':
                 $result['param'] = 'null';
            break;
            
            case 'test':
                $result['param'] = 'test';
                $result['get'] = $_GET;
                $result['post'] = $_POST;
                $result["success"] = true;
            break;
            
            case 'selectAllTrackers':
                if(isset($_GET['start'])){
                    
                    $start = intval(stripslashes(urldecode($_GET['start'])));
                    if(isset($_GET['limit'])){
                        
                        $limit = intval(stripslashes(urldecode($_GET['limit'])));
                        $result['trackers'] = $tracker->selectRows('al_points', null, $start.','.$limit);
                    }else
                        $result['trackers'] = $tracker->selectRows('al_points', null, $start.',10');
                        
                }else{
                    
                     $result['trackers'] = $tracker->selectRows('al_points', null, 10);
                }
                //$result["trackers"] = $tracker->selectAll();
                $result['totalCount'] =$tracker->selectCount('al_points');
                $result["success"] = true;
            break;
            
            case 'selectTracker':
                $result["rows"] = $tracker->selectTracker(1);
            break;
        
            //selectAllCity
            case 'selectAllCity':
                $result["rows"] = $tracker->selectAllCity();
            break;
        
            //All
            case 'All':
                $result["rows"]['citys'] = $tracker->selectRows('al_city');
                $result["rows"]['files'] =  $tracker->selectRows('al_files');
                $result["rows"]['country'] = $tracker->selectRows('al_country');
                $result["rows"]['region'] = $tracker->selectRows('al_region');
                $result["rows"]['trackers'] = $tracker->selectRows('al_points', null, 10);
                $result["rows"]['totalCount'] =$tracker->selectCount('al_points');
                $result["rows"]['users'] = $tracker->selectRows('al_users');
                $result["success"] = true;
            break;
            
            
            //AllActive
            case 'AllActive':
                $result["rows"]['citys'] = $tracker->selectRows('al_city');
                $result["rows"]['files'] =  $tracker->selectRows('al_files');
                //$result["rows"]['country'] = $tracker->selectRows('al_country');
                //$result["rows"]['region'] = $tracker->selectRows('al_region');
                $result["rows"]['trackers'] = $tracker->selectRows('al_points', 'visible = 1');
                $result["rows"]['users'] = $tracker->selectRows('al_users');
                $result["success"] = true;
            break;
            
            
            //AllActive by ID
            case 'AllActiveByUserId':
               
                
                if (isset($_GET['userID'])){
                    $id =  $_GET['userID'];
                }else{
                    $id = 0;
                    $result["message"] = 'user ID not set <br />';
                }
                    
                $result["rows"]['citys'] = $tracker->selectRows('al_city');
                $result["rows"]['files'] =  $tracker->selectRows('al_files');
                $result["rows"]['users'] = $tracker->selectRows('al_users');
                $result["rows"]['country'] = $tracker->selectRows('al_country');
                //$result["rows"]['region'] = $tracker->selectRows('al_region');
                
                if (($id > 0)){
                    $result["rows"]['trackers'] = $tracker->selectRows('al_points', "visible = 1 AND id_user = '$id'");
                    $result["success"] = true;
                }else{
                    $result["rows"]['trackers'] = $tracker->selectRows('al_points', 'visible = 1');
                     $result["success"] = true;
                }
                
               
            break;
            
            //trackersByUserId
            case 'trackersByUserId':
                
                if (isset($_GET['userID'])){
                    $id =  $_GET['userID'];
                    $result["rows"]['trackers'] = $tracker->selectRows('al_points', "visible = 1 AND id_user = '$id'");
                    $result["success"] = true;
                }else{
                    $id = 0;
                    $result["message"] = 'user ID not set <br />';
                }
                
            break;
            
            //addItem
            case 'addItem':
                $result["message"] = '';
                
                if (isset($_GET['tbl'])){
                    $table = 'al_'.stripslashes(urldecode($_GET['tbl']));
                    
                    if(isset($_GET['item'])){
                        
                        $item = stripslashes(urldecode($_GET['item']));
                        
                        if (!($tracker->checkDuplicat($table,'name',$item))){
                            $tracker->insert($table , array('name'=>$item));
                            $result["message"] .= 'item '.$item.' was added to table '.$table.' <br />';
                        }else{
                            $result["message"] .= 'error at adding item to table '.$table.', duplicated item <br />';
                        }
                        $result["success"] = true;

                        
                    }else{
                        $result["message"] = 'item not set <br />';  
                    }
                    
                }else{
                    $result["message"] = 'table not set <br />';
                }
            break;
        
        
            
            //delItem
            case 'delItem':
                $result["message"] = '';
                
                if (isset($_GET['tbl'])){
                    $table = 'al_'.stripslashes(urldecode($_GET['tbl']));
                    
                    if(isset($_GET['id'])){
                        
                        $id = stripslashes(urldecode($_GET['id']));
                        
                        if ($tracker->delRow($table,$id)){
                            
                            $result["success"] = true;
                            $result["message"] .= 'item '.$id.' was removed from table '.$table.' <br />';
                        }else{
                            $result["message"] .= 'error at deleting item from table '.$table.' <br />';
                        }

                        
                    }else{
                        $result["message"] = 'id of item not set <br />';  
                    }
                    
                }else{
                    $result["message"] = 'table not set <br />';
                }
            break;
            
            
            //delTracker
            case 'delTracker':
                $t = 0;
                $result["message"] = '';
                $id = $_GET['trackers'];
                $id = str_replace("\"", "", $id);
                
                $t = $tracker->selectTracker($id);

                if ($tracker->delRow('al_points',$id)){
                    $result["success"] = true;
                   
                    $result["message"] .= 'tracker '.$id.'was removed from DB <br />';
                    
                    // tracker whith $id was selected
                    if (!empty ($t['file'])){
                        if (delFile($tracker,$t['file']))
                            $result["message"] .= 'file asociated with trecker was removed (id:'+ $t['file'] +')';
                    }
                }else{
                    $result["message"] .= 'error at deleting tracker from DB <br />';
                }
            break;
        


            //editeTracker
            case 'editeTracker':
                //echo (stripslashes($_GET['trackers']));
                $result["message"] = '';
                $get = stripslashes(urldecode($_GET['trackers']));
                
                $arrGet = json_decode($get,true);
                
                if ($arrGet){
                    //echo ($get);
                    //var_dump($arrGet);
                    //
                    // if recievd json [{"visible":"1","id":"12"},{"id":"13"}]
                    if (isset($arrGet[0])){
                    $j = 0;
                    
                    
                        // if client sends few json data
                        while(!empty($arrGet[$j])){
                            
                        
                            // get id of track
                            $trackerID = $arrGet[$j]['id'];
                            $arr =null;
                            $i = 0;
                            while (list($key, $value) = each($arrGet[$j])):
                                if ($key!='id'&&$key!='_dc'){
                                    $arr['key'][$i] = $key;
                                    $arr['val'][$i] = ($value); //addslashes
                                    $i++;
                                }
                            endwhile;
                            
                            // write to DB changhes
                            if (($arr['key'][0])!=null){
                            
                                $result["DB"] = $tracker->updateRow('al_points' , $trackerID, $arr['key'][0], $arr['val'][0]);
                                if($result["DB"]){
                                    $result["message"] .= 'track id = '.$trackerID.'  '.$arr['key'][0].' changed to '. $arr['val'][0] . '<br />';
                                    $result["success"] = true;
                                }else{
                                     $result["message"] = ' error at saving changes to DB : ' . $trackerID;
                                }
                            }
                            $j++;
                        }
                    }else{
                        //{"comments":"home","id":"13"}
                        $trackerID = $arrGet['id'];
                        $i = 0;
                        while (list($key, $value) = each($arrGet)):
                            if ($key!='id'&&$key!='_dc'){
                                $arr['key'][$i] = $key;
                                $arr['val'][$i] = ($value); //addslashes
                                $i++;
                            }
                        endwhile;
                        
                        // write to DB changhes
                        
                        $result["DB"] = $tracker->updateRow('al_points' , $trackerID, $arr['key'][0], $arr['val'][0]);
                        if($result["DB"]){
                            $result["message"] .= 'track id = '.$trackerID.'  '.$arr['key'][0].' changed to '. $arr['val'][0] . '<br />';
                            $result["success"] = true;
                        }else{
                             $result["message"] = ' error at saving changes to DB : ' . $trackerID;
                        }
                    }
                }else{
                    $result["message"] = ' error at decoding datacfrom url  : ' . $get;
                }
                
                
            break;
            
            
             //addTracker
            case 'addTracker':
               
                $result["message"] = 'adding tracker <br />';   
                
                //$arrGet = json_encode($_GET);
                //$arrGet = json_decode($arrGet);
                
                //echo implode(',',$arrGet);
                //var_dump (get_class_methods($arrGet));
                $i = 0;
                
                while (list($key, $value) = each($_GET)):
                    if ($key!='param'&&$key!='_dc'){
                        //$arrGet['name'][$i] = $key;
                        //$arrGet['val'][$i] = $value;
                        $arrGet[$key] =  stripslashes(urldecode($value));
                        $i++;
                    }
                    //print "$key ==> $value <br>";
                endwhile;
                
                //var_dump ($arrGet);                
                
                // check, number or not
                if (!is_numeric($arrGet['id_user'])){
                    
                    // chek for duplication 
                    if (!($tracker->checkDuplicat('al_users','name', $arrGet['id_user']))){
                        $tracker->insert('al_users' , array('name'=>$arrGet['id_user']));
                        $arrGet['id_user'] = $tracker->checkDuplicat('al_users','name', $arrGet['id_user']);
                        $result["message"] .= 'adding new user '.$arrGet['id_user'].' <br />';   
                    }else{
                       $result["message"] .= 'duplicated user '.$arrGet['id_user'].' <br />';   
                    }
                    
                }
                
                // check, number or not
                if (!is_numeric($arrGet['start_city_id'])){
                    
                    // chek for duplication 
                    if (!($tracker->checkDuplicat('al_city','name', $arrGet['start_city_id']))){
                        $tracker->insert('al_city' , array('name'=>$arrGet['start_city_id']));
                        $arrGet['start_city_id'] = $tracker->checkDuplicat('al_city','name', $arrGet['start_city_id']);
                        $result["message"] .= 'adding new city '.$arrGet['start_city_id'].' <br />';   
                    }else{
                       $result["message"] .= 'duplicated city '.$arrGet['start_city_id'].' <br />';   
                    }
                    
                }
                
                if (!is_numeric($arrGet['end_city_id'])){

                    // chek for duplication 
                    if (!$tracker->checkDuplicat('al_city','name', $arrGet['end_city_id'])){
                        $tracker->insert('al_city' , array('name'=>$arrGet['end_city_id']));
                        $arrGet['end_city_id'] = $tracker->checkDuplicat('al_city','name', $arrGet['end_city_id']);
                        $result["message"] .= 'adding new city '.$arrGet['end_city_id'].' <br />';   
                    }else{
                       $result["message"] .= 'duplicated city '.$arrGet['end_city_id'].' <br />';   
                    }
                }
                
                if (!is_numeric($arrGet['id_country'])){
                    
                    // chek for duplication 
                    if (!$tracker->checkDuplicat('al_country','name', $arrGet['id_country'])){
                        $tracker->insert('al_country' , array('name'=>$arrGet['id_country']));
                        $arrGet['id_country'] = $tracker->checkDuplicat('al_country','name', $arrGet['id_country']);
                        $result["message"] .= 'adding new country '.$arrGet['id_country'].' <br />';   
                    }else{
                       $result["message"] .= 'duplicated country '.$arrGet['id_country'].' <br />';   
                    }
                    
                }
                
                if (!is_numeric($arrGet['id_region'])){
                    
                    // chek for duplication 
                    if (!$tracker->checkDuplicat('al_region','name', $arrGet['id_region'])){
                        $tracker->insert('al_region' , array('name'=>$arrGet['id_region']));
                        $arrGet['id_region'] = $tracker->checkDuplicat('al_region','name', $arrGet['id_region']);
                        $result["message"] .= 'adding new region '.$arrGet['id_region'].' <br />';   
                    }else{
                       $result["message"] .= 'duplicated region '.$arrGet['id_region'].' <br />';   
                    }
                    
                }
                
                if (is_numeric($arrGet['file'])){
                    $arrGet['visible'] = 1;
                    $trackerID = $tracker->insert('al_points' , $arrGet);
                    if ($trackerID){
                        $result["message"] .= 'tracker was added '.$trackerID.' <br />';
                        $result["success"] = true;
                    }else{
                        $result["message"] .= 'error at adding tracker  <br />';
                    }
                    
                }else{
                    
                     $result["message"] .= 'need select a file from a list  <br />';
                }
            break;
            
            
            
            
            
            
            
            
            
            
            //selectAllFiles
            case 'selectAllFiles':
                $result['files'] = $tracker->selectAllFiles();
                $result["success"] = true;
            break;
        
        
             //addFile
            case 'addFile':
                
                $result["message"] = '';
                $result["success"] = true;
                
                if(count($_FILES)>0){
                
                    $name = urldecode($_FILES['uploadFile']['name']);
                    $size = urldecode($_FILES['uploadFile']['size']);
                    $type = urldecode($_FILES['uploadFile']['type']);
                    
                    $arr_ext = explode(".", $name);
                    
                    $uploadfile = 'kml/' . $name;
                    
                    if (!file_exists($uploadfile)){
                    
                        if (copy($_FILES['uploadFile']['tmp_name'], $uploadfile)) {
                            $tracker->addFile($name, $uploadfile);
                            $result["message"] .= 'Файл успешно загружен и сохранен на сервере!';
                        }else
                            $result["message"] .= ' error at copy file from tmp dir';
                            
                    }else{
                        $result["message"] .= ' duplicated file on server! ';
                    }
                
                }else{
                      $result["message"] .= ' no POST data send!';
                }
            break;
        
            //delFile
            case 'delFile':
                $result["message"] = '<br />';
                $res = 0;
                
                 if ($id = $_GET['files']){
                    
                    $res = delFile($tracker,$id);
                    
                    if ($res == -1){
                        $result["message"] .= 'error at deleting file from file system <br />';   
                    }elseif ($res == -2){
                        $result["message"] .= 'file does not exist <br />';   
                    }elseif($res == -3){
                         $result["message"] .= 'error at deleting file from DB <br />';
                    }elseif($res == 0){
                         $result["success"] = true;
                    }
                    
                 }else{
                    $result["message"] .= 'error at deleting file, id of a file is not set <br />';
                }
                
            break;
        
        
        
        }
    
    }else{
         $result['param'] = 'not set';
    }
    echo json_encode($result);
    //var_dump($result);

?>