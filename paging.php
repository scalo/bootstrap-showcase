<?php
	ob_start('ob_gzhandler');

	$output = array(
 		"draw" => intval($_REQUEST['draw']),
		"recordsTotal" => 0,
		"data" => array()
	);

	$start = 0 ;
	$length = 10;

	if ( isset( $_REQUEST['start'] ) && $_REQUEST['length'] != '-1' ){
		$start = $_REQUEST['start'] ;
		$length = $_REQUEST['length'];
		
	} 
	
	$limit = "LIMIT $start,$length";
	$where = "";
	if ( isset( $_REQUEST['search']['value']) && strlen($_REQUEST['search']['value'])>2 ){
		$where ="WHERE insegna like '%".$_REQUEST['search']['value']."%'";
	}
	
	$query_count = "SELECT count(*) FROM alberghi $where";
	$query = "SELECT
		(SELECT COUNT(*)
        FROM alberghi AS t2
        WHERE t2.insegna <= t1.insegna
        order by t2.insegna
        ) AS id ,
		t1.*
		FROM alberghi AS t1
		$where ORDER BY t1.insegna  $limit ";

	$output['query']=$query;
	
	try {
		$db = new PDO('sqlite:'.realpath('data/db.sqlite'));
		$stm = $db->prepare($query_count);

		$stm->execute();
		$nrow = $stm->fetchColumn(0);
		$output['recordsTotal'] = $nrow;
		$output['recordsFiltered'] = $nrow;
		$stm = $db->prepare($query);
		$stm->execute();
		$result = $stm->fetchAll(PDO::FETCH_NUM);
		foreach($result as $row){
			array_push($output['data'] , $row);	
		}
		
	}catch (PDOException $e){
		echo $e; die();
	}
	
	echo utf8_encode(json_encode( $output ));
?>