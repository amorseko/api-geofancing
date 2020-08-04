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

			$startYear = date("Y");
			$startMonth = date("m");
			$sSql = "SELECT absen_masuk, absen_keluar, koordinat_masuk, koordinat_keluar, stt_absen_masuk, stt_absen_keluar, tanggal FROM tb_absen_web WHERE id_users='".$data['id_pegawai']."' and YEAR(tanggal) = '".$startYear."' and MONTH(tanggal) = '".$startMonth."'";

			$result = mysqli_query($koneksi,$sSql);

			if(!$result) {
				throw new RuntimeException('Error query.');
			}

			$rowCount = mysqli_num_rows($result);

			if($rowCount) {
				while($row = mysqli_fetch_array($result)) { 
					if($row['stt_absen_masuk'] == "0") {
				    	$stt_absen_masuk = "Masuk";
				    } elseif ($row['stt_absen_masuk'] == "2") {
					    $stt_absen_masuk = "Telat";
					}
					
					if($row['stt_absen_keluar'] == "" || $row['stt_absen_keluar'] === NULL || is_null) {
					    $stt_absen_keluar = "";
					} elseif($row['stt_absen_keluar'] == "1") {
					    $stt_absen_keluar = "Pulang";
					}
					

					if($row['absen_keluar'] === NULL || is_null($row['absen_keluar'])){
						$rAbsenKeluar = "";
					} else {
						$rAbsenKeluar = $row['absen_keluar'];
					}

					$res[] = array (
						'absen_masuk' =>$row['absen_masuk'],
						'absen_keluar' =>$rAbsenKeluar,
						'koordinat_masuk' =>$row['koordinat_masuk'],
						'koordinat_keluar' =>$row['koordinat_keluar'],
						'stt_absen' =>$stt_absen_masuk,
						'stt_absen_keluar' => $stt_absen_keluar,
						'tanggal'	=>$row['tanggal']
					) ;       

				    
				}   
			}

			$response = array(
			    "status" => "success",
			    "error" => true,
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