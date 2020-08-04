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
			$sSql = "SELECT
			a.id_users,
			a.tanggal,
			a.jam_absen AS 'jam_masuk',
			IFNULL( b.jam_absen, '-' ) AS 'jam_pulang',
			a.koordinat AS 'titik_absen',
			IFNULL( b.koordinat, '-' ) AS 'titik_pulang'

		FROM
			( SELECT * FROM tb_absensi WHERE id_users = '".$data['id_pegawai']."' AND stt_absen = 0 ) AS a
			LEFT JOIN ( SELECT * FROM tb_absensi WHERE id_users = '".$data['id_pegawai']."' AND stt_absen = 1 ) AS b ON DATE( a.tanggal )= DATE( b.tanggal ) 
		GROUP BY
			DATE(
			a.jam_absen) order by a.tanggal DESC";

			$result = mysqli_query($koneksi,$sSql);

			if(!$result) {
				throw new RuntimeException('Error query.');
			}

			$rowCount = mysqli_num_rows($result);

			if($rowCount) {
				while($row = mysqli_fetch_array($result)) { 
					$res[] = array (
						'jam_masuk' =>$row['jam_masuk'],
						'jam_pulang' =>$row['jam_pulang'],
						'titik_absen' =>$row['titik_absen'],
						'titik_pulang' =>$row['titik_pulang'],
						'tanggal'	=> date('d F Y', strtotime($row['tanggal'])),
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