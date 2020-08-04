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

			if(!isset($_POST['range_absen'])) {
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

			if (!move_uploaded_file( $_FILES['upfile']['tmp_name'], sprintf('uploads/%s.%s', sha1_file($_FILES['upfile']['tmp_name']), $ext ) )) {
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

		 	$_idAbsen = $_POST['id_pegawai'].$_POST['tanggal'];
		 	

		 	if($_POST['status_absen'] == '0') {

		 		//if(strtotime($_POST['jam']) > $jam_masuk) {
		 		//	throw new RuntimeException('not accept');
		 		//}
		 		
		 	    //checking this id absent was exist
		 	    $qChecking = "SELECT id_absen FROM tb_absen_web WHERE id_absen = '".$_idAbsen."'";
		 	    $checking = mysqli_query($koneksi,$qChecking) or die(mysqli_error());
		 	    $rowCount = mysqli_num_rows($checking);
		 	    // echo $rowCount;
		 	    if($rowCount) {
		 	        $_totalID = "1";
		 	        
		 	    } else {
		 	        $_totalID = "";
		 	    }
		 	    
		 	    // echo $_totalID;
		 	    
		 	    if($_totalID == "1") {
		 	        throw new RuntimeException('ID absent was exist');
		 	    }
		 	    //end here
		 		
		 		$diffMasuk = strtotime($_POST['jam']) - $jam_masuk;

		 		$_CountJam    =floor($diffMasuk / (60 * 60));

		 		$_CountMenit = $diffMasuk - $_CountJam * (60 * 60);
		 		
		 		$_resultMenit = floor( $_CountMenit / 60 );
		 		
		 		//echo $_resultMenit;

		 		if(intval($_resultMenit) > 1 && intval($_resultMenit < 15)) {
		 			$stt_absen = '2';
		 		} else {
		 			
		 		

		 			$stt_absen = $_POST['status_absen'];
		 		}

		 		$sSql = "INSERT INTO tb_absen_web (id_absen,id_users,absen_masuk,koordinat_masuk,koordinat_keluar,foto_absen_masuk,stt_absen_masuk,tanggal,range_masuk, range_keluar) VALUES ('".$_idAbsen."','".$_POST['id_pegawai']."','".$_POST['jam']."','".$_POST['koordinat']."','','".$name_file."','".$stt_absen."','".$_POST['tanggal']."','".$_POST['range_absen']."','')";


		 	}

		 	if($_POST['status_absen'] == '1') {

		 		if(strtotime($_POST['jam']) < $jam_pulang) {
		 			throw new RuntimeException('not accept');
		 		}

		 		$sSql = "UPDATE tb_absen_web SET absen_keluar = '".$_POST['jam']."', koordinat_keluar = '".$_POST['koordinat']."', range_keluar = '".$_POST['range_absen']."', foto_absen_keluar='".$nama_file."', stt_absen_keluar = '".$_POST['status_absen']."' WHERE id_absen = '".$_idAbsen."'";
		 	}
		 	

		 	//checking for time absent



		 	

		 	// $sSql = "INSERT INTO tb_absensi_temp (id_pegawai,tgl_absen,jam_masuk,stt_absen,image_absen,waktu_absen_by_zona) VALUES ('".$_POST['id_pegawai']."', '".$_POST['tanggal']."','".$_POST['tanggal'].' '.$_POST['jam']."','0', '".$name_file."','".$_POST['tanggal'].' '.$convertedTime."')";

		 	//$sSql = "INSERT INTO tb_absensi(id_users,jam_absen,koordinat,foto,stt_absen,tanggal) VALUES('".$_POST['id_pegawai']."','".$_POST['tanggal'].' '.$_POST['jam']."','".$_POST['koordinat']."','".$name_file."','".$_POST['status_absen']."','".$_POST['tanggal']."')";
            // echo $sSql;
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