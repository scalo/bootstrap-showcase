<?php

require_once '../RestService.class.php';

class ModelService extends RestService {
	
	protected $db;

	public function __construct($request, $origin) {
		parent::__construct($request);
		try {
			$this->db = new PDO('sqlite:'.realpath('../../data/db.sqlite'));	
			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
		}catch (PDOException $e){
			echo $e; die();
		}
	}
	
	//LOAD
	protected function load($params) {
		if ($this->method == 'GET') {
			$id = $params[0];
			if($id){
				try {
					$query = "SELECT id,tipologia,insegna,indirizzo,categoria,num_camere,num_postiletto FROM alberghi WHERE id=:id";
					$stm = $this->db->prepare($query);
					$stm->bindValue(":id", $id, PDO::PARAM_INT);
					$stm->execute();
					$result = $stm->fetchAll(PDO::FETCH_ASSOC);
				}catch (PDOException $e){
					return $e;
				}
			}else{
				return array("error"=>true,"method"=>"load","id"=>$id);
			}
			return $result[0];
		} else {
			return "Only accepts GET requests";
		}
	}
	
	// SAVE
	protected function save($params) {
		if ($this->method == 'POST') {
			$json = $this->request['data'];	
			$data = json_decode($json);
			try {
				$query = "INSERT INTO alberghi (tipologia,insegna,indirizzo,categoria,num_camere,num_postiletto) VALUES (?,?,?,?,?,?)";
				$stm = $this->db->prepare($query);
				$stm->execute($data);	
			}catch (PDOException $e){
				return array("error"=>true,"message"=>$e, "method"=>"save");
			}
			return $data;
		} else {
			return "Only accepts POST requests";
		}
	}
	
	// UPDATE TODO
	protected function update($params) {
		if ($this->method == 'POST') {
			$json = $this->request['data'];
			$data = json_decode($json);
			try {
				$query = "UPDATE alberghi SET tipologia=?,insegna=?,indirizzo=?,categoria=?,num_camere=?,num_postiletto=? WHERE id=?";
				$stm = $this->db->prepare($query);
				$stm->execute($data);
			}catch (PDOException $e){
				return array("error"=>true,"message"=>$e, "method"=>"save");
			}
			return $data;
		} else {
			return "Only accepts POST requests";
		}
	}
	
	//DELETE
	protected function delete($params) {
		if ($this->method == 'DELETE') {
			$id = $params[0];
			if($id){
				try {
					$query = "DELETE FROM alberghi WHERE id=$id";
					$stm = $this->db->prepare($query);
					$stm->execute();	
				}catch (PDOException $e){
					return array("error"=>true,"message"=>$e, "method"=>"delete","id"=>$id);
				}
				return $id;
			}else{
				return array("error"=>true,"message"=>"id null", "method"=>"delete","id"=>$id);
			}
		} else {
			return "Only accepts DELETE requests";
		}
	}
	
	protected function parameters() {
		if ($this->method == 'GET') {
			return $this->request;
		} else {
			return "Only accepts GET requests";
		}
	}
}

// Requests from the same server don't have a HTTP_ORIGIN header
if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
	$_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
}

try {
	$rest = new ModelService($_REQUEST['method'], $_SERVER['HTTP_ORIGIN']);
	echo $rest->processAPI();
} catch (Exception $e) {
	echo json_encode(Array('error' => $e->getMessage()));
}

?>