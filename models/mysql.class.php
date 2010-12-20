<?php

class db{

	public $db;
	public $num_of_rows;
	
	private $host;
	private $user ;
	private $password;
	private $db_name;
	
	 
	


	//***********************
	// __construct
	//***********************
	public function __construct($h, $u, $p, $dbn)	{
		$this->host = $h;
		$this->user = $u;
		$this->password = $p;
		$this->db_name = $dbn;
		
		$this->db = @mysql_connect($h, $u, $p) or $this->error();
		mysql_select_db($dbn, $this->db) or $this->error();
		mysql_query("SET NAMES 'utf8'"); 
                mysql_query("SET CHARACTER SET 'utf8'");
		
		
		return $this->db;

			
	}
	
	//***********************
	// __destruct
	//***********************
	function __destruct() {
		mysql_close($this->db);
	}
	
	//***********************
	// _error
	//***********************
	private function error($error = null){
		if(!$error)
			$error = mysql_error(). 'q = '. $error;
		return $error;
	}


	//***********************
	// query
	//***********************
	public function select($query){
		$i = 0;
		$rows;
		$row;
		
		$q = mysql_query($query) or $this->error($query);
		//var_dump($q);
		//$this->num_of_rows = mysql_num_rows($q);
		if($q){
			$row = mysql_fetch_array($q, MYSQL_ASSOC);
			while (!empty($row)){
				$rows[$i++] = $row;
				$row = mysql_fetch_array($q, MYSQL_ASSOC);
			}
			
			mysql_free_result($q);
			//var_dump($row);
			if (!empty($rows))
			     return $rows;
			else return $row;
		}else{
			return $q;
		}

	}
	
	
	//***********************
	// query
	//***********************
	public function query($query){
		
		$q = mysql_query($query) or $this->error($query);
		
		return $q;
	}

	//***********************
	// charset_utf_win
	//***********************
	function charset_utf_win($s,$browser_chek){
		if ($browser_chek==1){
			$agent=$_SERVER['HTTP_USER_AGENT'];
			$browser="none";
			if (strpos($agent, "MSIE") !== false ){
				$r = $s;
			}else{
				$r = iconv("UTF-8", "WINDOWS-1251" ,$s);
			}
		}else{
			$r = iconv("UTF-8", "WINDOWS-1251" ,$s);
		}
	
		
		
		
		return $r;
	}


}
?>