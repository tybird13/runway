<?php
	class DatabaseManager{
		public $DBH;
		public $error_array = array();
		
		public function __construct(){
			try{
				$this->error_array['STH_ERROR'] = array();
				$this->DBH = new PDO('mysql:host=localhost;dbname=runway_student_login', 'site', 'Runway123!');
			} catch (PDOException $e) {
				print "Error!: " . $e->getMessage() . "<br/>";
				die();
			}
		}
		public function pullAllFromDatabase($sql){
			$STH = $this->DBH->prepare($sql);
			$STH->execute();
			$results = $STH->fetchAll(PDO::FETCH_ASSOC);
			array_push($this->error_array['STH_ERROR'], $STH->errorInfo());
			return $results;
		}
		
		public function accessDatabase($sql, $args){
			$STH = $this->DBH->prepare($sql);
			$STH->execute($args);
			$results = $STH->fetch(PDO::FETCH_ASSOC);
			array_push($this->error_array['STH_ERROR'], $STH->errorInfo());
			return $results;
		}
		
		public function updateDatabase($sql, $args){
			// updates or inserts into database and returns the error code
			$STH = $this->DBH->prepare($sql);
			$STH->execute($args);
			$results = $STH->errorInfo();
			return $STH->errorInfo();
		}
		public function getErrorArray(){
			return $this->error_array;	
		}
	}
?>