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

if($method_req == "POST") {
	try {
		$data = json_decode(file_get_contents('php://input'), true);

		if(isset($data['password_old']) && isset($data['password_new']) && isset($data['id_user'])) {

			$query = "SELECT count(ID_users) FROM tb_users WHERE ID_users = '".$data['id_user']."'";
			$result = mysqli_query($koneksi, $query) or die (mysqli_error());
			$rowCount = mysqli_num_rows($result);
			if($rowCount){
				$newPass = $Enkripsi->encode($data['password_new']);
				$qUpdate = "UPDATE tb_users SET password = '".$newPass."' WHERE ID_users = '".$data['id_user']."'";

				$exec = mysqli_query($koneksi, $qUpdate) or die (mysqli_error());

			 	if(!$exec) {
			 		throw new RuntimeException('Failed to update database');
			 	}

			 	$response = array(
			 	  "status" => true,
			 	  "error" => false,
			 	  "message" => "success"
			 	);
				header('Content-Type: application/json');

		 	echo json_encode($response);


			} else {

				throw new RuntimeException('Data not found .');
			}
		} else {
			throw new RuntimeException('Data input not valid .');
		}
	} catch(RuntimeException $e) {
		$response = array(
		    "status" => false,
		    "error" => true,
		    "message" => $e->getMessage(),
		    "data" => $res
		 );
		header('Content-Type: application/json');
		echo json_encode($response);
	}
	
} else {
	http_response_code(405);
}
?>