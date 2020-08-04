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


	$response = array();
	$res = array();

	if ($method_req == "POST"){
		try {

			$data = json_decode(file_get_contents('php://input'), true);

			if(!isset($data['app_id'])) {
				 throw new RuntimeException('Invalid parameters.');
			}
			
		

			$startYear = date("Y");
			$startMonth = date("m");
			$sSql = "SELECT jarak FROM tb_jarak WHERE app_id = '".$data['app_id']."' LIMIT 1";

			$result = mysqli_query($koneksi,$sSql);

			if(!$result) {
				throw new RuntimeException('Error query.');
			}

			$rowCount = mysqli_num_rows($result);

			if($rowCount) {
				while($row = mysqli_fetch_array($result)) { 
					
					$res[] = array (
						'jarak' =>intval($row['jarak'])
					) ;       

				    
				}   
			}

			$response = array(
			    "code" => 200,
			    "status" => false,
			    "message" =>"success",
			    "data" => $res
			);

			header('Content-Type: application/json');
		  	echo json_encode($response);


		}catch (RuntimeException $e) {
		    
		  $response = array(
			    "code" => 404,
			    "status" => true,
			    "message" =>$e->getMessage(),
			    "data" => $res
		  );
		  
		  header('Content-Type: application/json');
		  echo json_encode($response);
		}
	} else {
		http_response_code(405);
	}
?>