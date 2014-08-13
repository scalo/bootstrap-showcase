<?php

require_once '../RestService.class.php';

class ModelService extends RestService {
	
	protected $db;

	public function __construct($request, $origin) {
		parent::__construct($request);
		try {
			$this->db = new PDO('sqlite:'.realpath('../../data/db.sqlite'));	
		}catch (PDOException $e){
			echo $e; die();
		}
	}

	protected function load() {
		if ($this->method == 'GET') {
			$id = $this->request["id"];
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
	
	// TODO
	protected function save() {
		if ($this->method == 'POST') {
			$data = $this->request["data"];
			
			return json_decode($data);
		} else {
			return "Only accepts POST requests";
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