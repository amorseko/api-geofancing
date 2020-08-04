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

	if ($method_req == "POST"){
		try {
			if(!isset($_POST['jam'])) {
				 throw new RuntimeException('Invalid parameters.');
			}

			if(!isset($_POST['tanggal'])) {
				 throw new RuntimeException('Invalid parameters.');
			}

			if(!isset($_POST['id_pegawai'])) {
				 throw new RuntimeException('Invalid parameters.');
			}

			if(!isset($_POST['koordinat'])) {
				 throw new RuntimeException('Invalid parameters.');
			}

			if ( !isset($_FILES['upfile']['error']) ||  is_array($_FILES['upfile']['error']) ) {
			    throw new RuntimeException('Invalid parameters.');
			}

			if(!isset($_POST['status_absen'])) {
				 throw new RuntimeException('Invalid parameters.');
			}

			// $convertedTime = date('H:i:s',strtotime('+1 hour',strtotime($_POST['jam_zona'])));


		 	$name_file = $_FILES["upfile"]["name"];

			// echo $convertedTime;

			switch ($_FILES['upfile']['error']) {
			    case UPLOAD_ERR_OK:
			      break;
			    case UPLOAD_ERR_NO_FILE:
			      throw new RuntimeException('No file sent.');
			    case UPLOAD_ERR_INI_SIZE:
			    case UPLOAD_ERR_FORM_SIZE:
			      throw new RuntimeException('Exceeded filesize limit.');
			    default:
			      throw new RuntimeException('Unknown errors.');
			}

			if ($_FILES['upfile']['size'] > 1000000) {
			    throw new RuntimeException('Exceeded filesize limit.');
			}

			$finfo = new finfo(FILEINFO_MIME_TYPE);
			if (false === $ext = array_search(
			    $finfo->file($_FILES['upfile']['tmp_name']),
			    array(
			      'jpg' => 'image/jpeg',
			      'png' => 'image/png',
			      'gif' => 'image/gif',
			    ),
			    true
			  )) {
			    throw new RuntimeException('Invalid file format.');
			}

			if (!move_uploaded_file( $_FILES['upfile']['tmp_name'], sprintf('uploads/%s', ($_FILES['upfile']['name']) ) )) {
		 	  throw new RuntimeException('Failed to move uploaded file.');
		 	}
		 	
		 	$QueryJamMasuk = "SELECT jam_masuk, jam_pulang FROM tb_jadwal_absen LIMIT 1"; 

	 		$result = mysqli_query($koneksi, $QueryJamMasuk)  or die (mysqli_error());

	 		if($result) {
	 			$row = mysqli_fetch_assoc($result);
	 			$jam_masuk = strtotime($row['jam_masuk']);
	 			$jam_pulang = strtotime($row['jam_pulang']);
	 		} else {
		 		throw new RuntimeException('Data empty for time attendance');
		 	}

		 	if($_POST['status_absen'] == '0') {
                //echo strtotime($_POST['jam']).'-'.$jam_masuk;
		 		if(strtotime($_POST['jam']) > $jam_masuk) {
		 			throw new RuntimeException('not accept');
		 		}
		 	}

		 	if($_POST['status_absen'] == '1') {

		 		if(strtotime($_POST['jam']) < $jam_pulang) {
		 			throw new RuntimeException('not accept');
		 		}
		 	}


		 	// $sSql = "INSERT INTO tb_absensi_temp (id_pegawai,tgl_absen,jam_masuk,stt_absen,image_absen,waktu_absen_by_zona) VALUES ('".$_POST['id_pegawai']."', '".$_POST['tanggal']."','".$_POST['tanggal'].' '.$_POST['jam']."','0', '".$name_file."','".$_POST['tanggal'].' '.$convertedTime."')";

		 	$sSql = "INSERT INTO tb_absensi(id_users,jam_absen,koordinat,foto_absen,stt_absen,tanggal) VALUES('".$_POST['id_pegawai']."','".$_POST['tanggal'].' '.$_POST['jam']."','".$_POST['koordinat']."','".$name_file."','".$_POST['status_absen']."','".$_POST['tanggal']."')";

		 	$exec = mysqli_query($koneksi, $sSql) or die (mysqli_error());

		 	if(!$exec) {
		 		throw new RuntimeException('Failed to save database');
		 	}

		 	$response = array(
		 	  "status" => "success",
		 	  "error" => false,
		 	  "message" => "success"
		 	);
			header('Content-Type: application/json');

		 	echo json_encode($response);

		} catch (RuntimeException $e) {
		  $response = array(
		    "status" => "error",
		    "error" => true,
		    "message" => $e->getMessage()
		  );
			header('Content-Type: application/json');
		  echo json_encode($response);
		}
	} else {
		http_response_code(405);
	}

?>