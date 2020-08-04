<?php
include 'conn.php';

header('Content-Type: text/javascript; charset=utf8');

header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Credentials', 'true');

header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

header('Access-Control-Allow-Headers Content-Type');

header('Access-Control-Max-Age: 3628800');

$method_req = $_SERVER['REQUEST_METHOD'];

include 'enkripsi.php';

$Enkripsi = new Enkripsi();


if ($method_req == "POST"){

	$data = json_decode(file_get_contents('php://input'), true);

	if(isset($data['app_id']))  {
		$query = "SELECT data from tb_language WHERE app_id = '".$data['app_id']."'";
		// echo $query;
		$data_decode = "";
		$result = mysqli_query($koneksi, $query) or die (mysqli_error());
		$rowCount = mysqli_num_rows($result);
		$sData = "";
		if($rowCount){
			while ($r = mysqli_fetch_assoc($result)) {
				$sData = strip_tags($r['data']);
				// $rows[] = array ('data'=>$sdata);
			}
			$data_decode = json_decode($sData);
			$status = 200;
			$remarks = true;
			$msg = "success";
		} else {
			$status = 200;
			$rows = null;//array('Result'=>"not found");
			$remarks = false;
			$msg = "failed";
		}
		$encode = json_encode($sData);
		$to_encode = array('code' => $status, 
                          	'data' => $data_decode,
                          	'status' => $remarks,
                          	'message' => $msg,
                          );
		header('Content-Type: application/json');
		echo stripslashes(json_encode($to_encode,JSON_PRETTY_PRINT));
	} else {
		$rows = null;
		$remarks = false;
		$msg = "failed";
		$to_encode = array('code' => 200, 
                          	'data' => $rows,
                          	'status' => $remarks,
                          	'message' => $msg,
                          );
		header('Content-Type: application/json');
		echo json_encode($to_encode,JSON_PRETTY_PRINT);
	}
	
	
} else {
	http_response_code(405);
}

?>