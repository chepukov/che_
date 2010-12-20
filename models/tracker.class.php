<?php

include('mysql.class.php');	

class tracker extends db{
    
    public $table_points;
    
    
    //***********************
    // __construct
    //***********************
    public function __construct($host, $user, $password, $db,$table){
        
        parent::__construct($host, $user, $password, $db);
        $this->table_points = $table;
    }
    
    //***********************
    // selectAll
    //***********************
    public function selectAll(){
      
        return $this->select("SELECT * FROM `$this->table_points`");

    }
    
    
    //***********************
    // selectTracker
    //***********************
    public function selectTracker($id){
        $q = "SELECT * FROM `$this->table_points` WHERE id='$id'";
        //var_dump($q);
        return $this->select($q);

    }
    
    
    //***********************
    // selectAllCity
    //***********************
    public function selectAllCity(){
        $res;
        
         $res = $this->select("SELECT * FROM `al_city`");
         //var_dump($res);
         
         return $res;

    }
    
    //***********************
    // selectAllFiles
    //***********************
    public function selectAllFiles(){
        $res;
        
         $res = $this->select("SELECT * FROM `al_files`");
         //var_dump($res);
         
         return $res;

    }
    
    //***********************
    // addFile
    //***********************
    public function addFile($name,$src){
        $res;
        
         $res = $this->query("INSERT INTO `al_files` (`id`, `src`, `name`) VALUES (NULL, '$src', '$name')");
         //var_dump($res);
         
         //return $res;

    }
    
    //***********************
    // selectFile
    //***********************
    public function selectFile($id){
      
        return $this->select('SELECT * FROM `al_files` WHERE id='.$id);

    }
    
    //***********************
    // delFile
    //***********************
    public function delFile($id){
        $res;
        
         $res = $this->query('DELETE FROM `al_files` WHERE id='.$id);
         //var_dump($res);
         
         return $res;

    }
    
    
    //***********************
    // selectAllCountry
    //***********************
    public function selectAllCountry(){
        $res;
        
         $res = $this->select("SELECT * FROM `al_country`");
         //var_dump($res);
         
         return $res;

    }
    
    
    //***********************
    // selectAllCountry
    //***********************
    public function selectAllRegions(){
        $res;
        
         $res = $this->select("SELECT * FROM `al_region`");
         //var_dump($res);
         
         return $res;

    }
    
    //***********************
    // selectRows
    //***********************
    public function selectRows($table, $where = null, $limit = null){
        $res;
        if (!empty($where)) $where = 'WHERE ' . $where;
        if (!empty($limit)) $limit = 'LIMIT ' . $limit;

        $res = $this->select("SELECT * FROM `$table` $where $limit");
         //var_dump($res);
         
        return $res;

    }
    
    //***********************
    // selectCount
    //***********************
    public function selectCount($table){
        $res;
         
         $res = $this->select("SELECT COUNT('id') FROM `$table`");
         //var_dump($res);
         return ($res[0]["COUNT('id')"]);

    }
    
    
    
    //***********************
    // insert
    //***********************
    public function insert($table , $arr){
        $res; $q = ''; $arrQ;
        $i = 0;
        
        while (list($key, $value) = each($arr)):
                $arrQ['name'][$i] = sprintf('`%s`',$key);
                $arrQ['val'][$i] = sprintf('\'%s\'',$value);
                $i++;
        endwhile;
        
        
        $names = implode(',',$arrQ['name']);
        $values = implode(',',$arrQ['val']);
        
        
        $q = "INSERT INTO $table (`id`, $names) VALUES (NULL, $values)";
        //echo $q;
        $res = $this->query($q);
        //var_dump($res);
       // $res = (mysql_fetch_array($res));
         
         return $res;

    }
    
    
    //***********************
    // insert
    //***********************
    public function checkDuplicat($table , $name, $value){
        $res; $q = '';    
        
        $q = "SELECT *  FROM `$table` WHERE  `$name` = '$value'";
        //echo $q;
        $res = $this->query($q);
        $res = (mysql_fetch_array($res));
         
        return $res['id'];

    }
    
    //***********************
    // delRow
    //***********************
    public function delRow($table,$id){
        $res;
        
         $res = $this->query('DELETE FROM `'.$table.'` WHERE id='.$id);
         //var_dump($res);
         
         return $res;

    }
    
    //***********************
    // update
    //***********************
    public function updateRow($table,$id,$key, $value){
      
        return $this->query("UPDATE `$table` SET  `$key` =  '$value' WHERE  `id` =$id");

    }
    
    
    
    
    
    
}
?>