<?php
/* *
 * WSSDB.class.php
 * @author ToanNguyen
 * @date Tue Jan 20 11:18:45 PM
 */

class WSSDB{

	private $dbInfo;
	public $connectId;
	public $queryId;
	public $record;
	
	/**
	 * Construct
	 * @param [type] $dbInfo [description]
	 */
	public function __construct($dbInfo){
		$this->dbInfo = $dbInfo;
		$this->connect();
	}
	
	/**
	 * Connect and select DB
	 * @return [type] [description]
	 */
	private function connect(){
		$this->connectId = @mysql_pconnect(
			$this->dbInfo['host'].":".$this->dbInfo['port'],
			$this->dbInfo['username'],
			$this->dbInfo['password']
		);
		if(!$this->connectId)
			$this->sqlError("Can't connect to server !");
		if(!@mysql_select_db($this->dbInfo['dbName'], $this->connectId))
			$this->sqlError("Database not found !");
		
		mysql_query("SET character_set_results=utf8", $this->connectId);
	    mb_language('uni'); 
	    mb_internal_encoding('UTF-8');
	    mysql_query("set names 'utf8'",$this->connectId);

		return $this->connectId;
	}
	
	/**
	 * Close connection
	 * @return [type] [description]
	 */
	private function close(){
		return mysql_close($this->connectId);	
	}
	
	/**
	 * Escapte String
	 * @param  [type] $str [description]
	 * @return [type]      [description]
	 */
	public function escapeString($str){
		return mysql_real_escape_string($str, $this->connectId);
	}
	
	/**
	 * Query
	 * @param  [type] $queryStr [description]
	 * @return [type]           [description]
	 */
	public function query($queryStr){
		$this->queryId = @mysql_query($queryStr, $this->connectId);
		if(!$this->queryId)
			$this->sqlError("SQL ERROR!",$queryStr);
		return $this->queryId;
	}
	
	/**
	 * Insert new ID
	 * @return [type] [description]
	 */
	public function insertId(){
		return @mysql_insert_id($this->connectId);	
	}
	
	/**
	 * Affected Rows
	 * @return [type] [description]
	 */
	public function affectedRows(){
		return @mysql_affected_rows($this->connectId);
	}
	
	/**
	 * Free the results
	 * @param  [type] $queryId [description]
	 * @return [type]          [description]
	 */
	public function freeResult($queryId){
		return @mysql_free_result($queryId);	
	}
	
	/**
	 * Get num rows
	 * @param  integer $queryId [description]
	 * @return [type]           [description]
	 */
	public function numRows($queryId = -1){
		if($queryId != -1)
			$this->queryId = $queryId;
		return @mysql_num_rows($this->queryId);	
	}
	
	/**
	 * Showing SQL errors
	 * @param  [type] $message [description]
	 * @param  string $query   [description]
	 * @return [type]          [description]
	 */
	private function sqlError($message, $query="") {
		$msgbox_title = $message;
		$sqlerror= "<table width='100%' border='1' cellpadding='0' cellspacing='0'>";
		$sqlerror.="<tr><th colspan='2'>SQL SYNTAX ERROR</th></tr>";
		$sqlerror.=($query!="")?"<tr><td nowrap> Query SQL</td><td nowrap>: ".$query."</td></tr>\n" : "";
		$sqlerror.="<tr><td nowrap> Error Number</td><td nowrap>: ".mysql_errno()." ".mysql_error()."</td></tr>\n";
        $sqlerror.="<tr><td nowrap> Date</td><td nowrap>: ".date("D, F j, Y H:i:s")."</td></tr>\n";
        $sqlerror.="<tr><td nowrap> IP</td><td>: ".getenv("REMOTE_ADDR")."</td></tr>\n";
        $sqlerror.="<tr><td nowrap> Browser</td><td nowrap>: ".getenv("HTTP_USER_AGENT")."</td></tr>\n";
		$sqlerror.="<tr><td nowrap> Script</td><td nowrap>: ".getenv("REQUEST_URI")."</td></tr>\n";
        $sqlerror.="<tr><td nowrap> Referer</td><td nowrap>: ".getenv("HTTP_REFERER")."</td></tr>\n";
        $sqlerror.="<tr><td nowrap> PHP Version </td><td>: ".PHP_VERSION."</td></tr>\n";
        $sqlerror.="<tr><td nowrap> OS</td><td>: ".PHP_OS."</td></tr>\n";
        $sqlerror.="<tr><td nowrap> Server</td><td>: ".getenv("SERVER_SOFTWARE")."</td></tr>\n";
        $sqlerror.="<tr><td nowrap> Server Name</td><td>: ".getenv("SERVER_NAME")."</td></tr>\n";
		$sqlerror.="</table>";
		$msgbox_messages = "<meta http-equiv=\"refresh\" content=\"9999\">\n<table class='smallgrey' cellspacing=1 cellpadding=0>".$sqlerror."</table>";
		$msg_header = "header_listred.gif";
		$msg_icon = "msg_error.gif";
		$imagesdir = "images";
		$redirecturl = '';
		$lang['gallery_back'] = "Back to the last request";
		if(empty($templatefolder)) $templatefolder = "templates";
		print $msgbox_messages;
		exit;
    }
	
	/**
	 * Insert
	 * @param  [type] $table    [description]
	 * @param  [type] $datasave [description]
	 * @return [type]           [description]
	 */
	public function insert($table, $datasave){
		$fields = implode(",",array_keys($datasave));
		$values = "";
		foreach($datasave as $key => $value){
			if($value == NULL)
				$value .= "NULL";
			$values .= "'".$this->escapeString($value)."',";
		}
		$values = substr($values, 0, -1);
		
		$this->queryId = $this->query("
			INSERT INTO $table ($fields)
			VALUES($values)
		");
		
		return $this->insertId();
	}
	
	/**
	 * Update
	 * @param  [type] $table    [description]
	 * @param  [type] $datasave [description]
	 * @param  [type] $where    [description]
	 * @return [type]           [description]
	 */
	public function update($table, $datasave, $where){
		$set = "";
		foreach($datasave as $key => $value){
			if($value == NULL)
				$value = "NULL";
			$set .= $key."='".$this->escapeString($value)."',";
		}
		$set = subStr($set, 0, -1);
		
		$this->queryId = $this->query("
			UPDATE $table
			SET $set
			WHERE $where
		");
		
		return $this->affectedRows();
	}
	
	/**
	 * Delete
	 * @param  [type] $table [description]
	 * @param  string $where [description]
	 * @return [type]        [description]
	 */
	public function delete($table,$where = ""){
		
		$this->queryId = $this->query("
			DELETE FROM $table
			WHERE $where
		");
		
		return $this->queryId;
	}
	
	/**
	 * Fetch array
	 * @param  integer $queryId [description]
	 * @return [type]           [description]
	 */
	public function fetch_array($queryId = -1){
		if($queryId != -1)
			$this->queryId = $queryId;
		
		$this->record = @mysql_fetch_array($this->queryId, MYSQL_ASSOC);
		
		return $this->record;
	}
	
	/**
	 * Fetch table array
	 * @param  [type] $query    [description]
	 * @param  [type] $key      [description]
	 * @param  string $sort     [description]
	 * @param  string $sortType [description]
	 * @return [type]           [description]
	 */
	public function fetch_table_array($query, $key, $sort = "", $sortType = ""){
		if($sort)
			$sort = " ORDER BY ".$sort." ".$sortType;
		else
			if($sortType)
				$sort = " ORDER BY ".$key." ".$sortType;
		
		$result = array();
		$_queryId = $this->query($query.$sort);
		while($rs = $this->fetch_array($_queryId)){
			$result[$rs[$key]] = $rs;
		}
		$this->freeResult($rs);
		
		return $result;
	}
	
	/**
	 * Query first
	 * @param  [type] $query [description]
	 * @return [type]        [description]
	 */
	public function query_first($query){
		$this->queryId = $this->query($query);
		$result = $this->fetch_array($this->queryId);

		return $result;
	}
	
	/**
	 * Fetch all
	 * @param  [type] $table [description]
	 * @param  [type] $key   [description]
	 * @return [type]        [description]
	 */
	public function fetch_all($table, $key){
		$queryId = $this->query("
			SELECT *
			FROM $table
		");
		$result = array();
		while($rs = $this->fetch_array($queryId)){
			$result[$rs[$key]] = $rs;
		}
		$this->freeResult($rs);
		
		return $result;
	}
	
	/**
	 * Fetch id string
	 * @param  [type] $table    [description]
	 * @param  string $cond     [description]
	 * @param  [type] $key      [description]
	 * @param  string $sortType [description]
	 * @return [type]           [description]
	 */
	public function fetch_id_string($table, $cond = "", $key, $sortType = ""){
		$result = "";
		$query = "SELECT $key FROM $table $cond";
		$rs = $this->fetch_table_array($query,$key,"",$sortType);
		foreach($rs as $r){
			$result .= $r[$key].",";
		}
		$result = substr($result, 0, -1);
		$this->freeResult($rs);
		return $result;
	}
	
	/**
	 * Fetch keys string
	 * @param  [type] $table    [description]
	 * @param  string $cond     [description]
	 * @param  [type] $key      [description]
	 * @param  string $sortType [description]
	 * @return [type]           [description]
	 */
	public function fetch_keys_string($table, $cond = "", $key, $sortType = ""){
		if($sortType != NULL)
			$sort = "ORDER BY ".$key." ".$sortType;
		else
			$sort = "";
		$result = "";
		$queryId = $this->query("SELECT $key FROM $table $cond $sort");
		while($rs = $this->fetch_array($queryId)){
			$result .= $rs[$key].",";	
		}
		$result = substr($result,0,-1);
		$this->freeResult($rs);
		
		return $result;
	}
	
	/**
	 * Query num rows
	 * @param  [type] $query [description]
	 * @return [type]        [description]
	 */
	public function query_num_rows($query){
		$this->queryId = $this->query($query);
		$numRows = $this->numRows($this->queryId);
		
		return $numRows;
	}
	
	/**
	 * Get table cols
	 * @param  [type] $table [description]
	 * @return [type]        [description]
	 */
	public function get_table_cols($table){
		if($table){
			$result = array();
			$this->queryId = $this->query("SHOW COLUMNS FROM {$table}");
			while($rs = $this->fetch_array($this->queryId)){
				$result[] = $rs['Field'];
			}
			return $result;
		}else
			return false;
	}
}