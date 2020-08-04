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

			if(!isset($data['id_pegawai'])) {
				 throw new RuntimeException('Invalid parameters.');
			}
			
			if(!isset($data['tanggal'])) {
				 throw new RuntimeException('Invalid parameters.');
			}

			$startYear = date("Y");
			$startMonth = date("m");
			$sSql = "SELECT jam_absen, koordinat, stt_absen, tanggal FROM tb_absensi WHERE id_users='".$data['id_pegawai']."' and DATE(tanggal) = '".$data['tanggal']."' group by DATE(tanggal), stt_absen order by id_absen ASC limit 2";

			$result = mysqli_query($koneksi,$sSql);

			if(!$result) {
				throw new RuntimeException('Error query.');
			}

			$rowCount = mysqli_num_rows($result);

			if($rowCount) {
				while($row = mysqli_fetch_array($result)) { 
					if($row['stt_absen'] == "0") {
				    	$stt_absen = "Masuk";
				    } elseif($row['stt_absen'] == "1") {
				    	$stt_absen = "Pulang";
					}
					
					$res[] = array (
						'jam_absen' =>$row['jam_absen'],
						'koordinat' =>$row['koordinat'],
						'stt_absen' =>$stt_absen,
						'jam_absen' =>$row['jam_absen'],
						'koordinat' =>$row['koordinat'],
						'tanggal'	=>$row['tanggal']
					) ;       

				    
				}   
			}

			$response = array(
			    "status" => "success",
			    "error" => false,
			    "message" =>"success",
			    "data" => $res
			);

			header('Content-Type: application/json');
		  	echo json_encode($response);


		}catch (RuntimeException $e) {
		  $response = array(
		    "status" => "error",
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