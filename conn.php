<?php
	// $host = "localhost";
	// $user = "root";
	// $pass = "";
	// $db   = "db_dascom";
	$host = "localhost";
	$user = "septlpik_hartoyo";
	$pass = "w?z_3evbG_o2";
	$db   = "septlpik_toyoga";
	$koneksi = mysqli_connect($host, $user, $pass, $db);
	if(mysqli_connect_errno()){
		echo "Gagal Terhubung ".mysqli_connect_error();
	}
	mysqli_set_charset($koneksi,"utf8");
?>