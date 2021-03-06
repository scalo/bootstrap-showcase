<?php

require_once '../RestService.class.php';

class HelloService extends RestService {

	public function __construct($request, $origin) {
		parent::__construct($request);
	}
	
	protected function helloWorld() {
		if ($this->method == 'GET') {
			return "Hello World";
		} else {
		return "Only accepts GET requests";
		}
		}
	
	protected function sayHello($params) {
		if ($this->method == 'POST') {
			return $this->request;
			//return "Hello $params[0]";
		} else {
			return "Only accepts POST requests";
		}
	}
}

// Requests from the same server don't have a HTTP_ORIGIN header
if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
	$_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
}

try {
	$rest = new HelloService($_REQUEST['request'], $_SERVER['HTTP_ORIGIN']);
	echo $rest->processAPI();
} catch (Exception $e) {
	echo json_encode(Array('error' => $e->getMessage()));
}

?>