<?php

include_once(dirname(__FILE__).'/../config/dbconfig.php');

class Database{
	private $conn;

	function __construct(){
		$this->conn = new PDO('mysql:host='.DBCONFIG::HOST.';dbname='.DBCONFIG::DBNAME, DBCONFIG::USER, DBCONFIG::PASSWORD);
    	$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	function __destruct(){
		$this->conn = null;
	}

	function call($proc, $args){
		try {
			$count = count($args);
		    $stmt = $this->conn->prepare('CALL '.$proc.($count>0?'('.str_repeat('?,',$count-1).'?)':'()'));
		    $stmt->execute($args);
		    $count = $stmt->rowCount();

		    return [
		    	'success'=>true,
		    	'count'  =>$count,
		    	'result' =>$count==0?null:$stmt->fetchAll()
		    ];
		}
		catch(PDOException $e){
		    return [
		    	'success'=>false,
		    	'message'=>$e->getMessage()
		    ];
		}
	}
}

?>