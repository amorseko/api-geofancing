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

	$rows = array();


	if(isset($data['username']) && isset($data['password']))
	{
		$username = $data['username'];
		$password = $data['password'];
		// echo $Enkripsi->decode($password);
		$query = "SELECT u.ID_users,u.username, u.password, u.level, u.nama_user, u.foto, u.id_dealer, u.user_khusus, d.longitude, d.latitude FROM tb_users u LEFT JOIN tb_dealer d ON u.id_dealer = d.id_dealer WHERE u.username = '".$username."' and u.password = '".$Enkripsi->encode($password)."' and u.status_user ='1' and level = 'mekanik'";
		// $query = "SELECT ID_users,username, password, level, nama_user, foto, id_dealer, user_khusus FROM tb_users WHERE username = '".$username."' and password = '".$Enkripsi->encode($password)."' and status_user ='1' and level = 'mekanik'";
		// echo $query;
		$result = mysqli_query($koneksi, $query) or die (mysqli_error());
		$rowCount = mysqli_num_rows($result);
		if($rowCount){
			// $qUpdate = "UPDATE tb_users SET token = '".$firebase_token."' WHERE username = '".$username."' and password = '".$Enkripsi->encode($password)."'";
			// $result = mysqli_query($koneksi, $query) or die (mysqli_error());
			
			while ($r = mysqli_fetch_assoc($result)) {
				$rows['username'] = $r['username'];
				$rows['level'] = $r['level'];
				$rows['nama_user'] = $r['nama_user'];
				$rows['id_user'] = $r['ID_users'];
				$rows['foto'] = $r['foto'];
				$rows['id_dealer'] = $r['id_dealer'];
				$rows['user_khusus'] = $r['user_khusus'];
				$rows['longitude'] = floatval($r['longitude']);
				$rows['latitude'] = floatval($r['latitude']);
				// $rows[] = array ('username'=>$r['username'], 'password'=>$r['password'], 'level'=>$r['level'], 'nama_user'=>$r['nama_user'], 'id_user'=>$r['ID_users'], 'foto'=>$r['foto'], 'id_dealer'=>$r['id_dealer'], 'token'=>$r['token']);
			}
			$status = 200;
			$remarks = true;
			$msg = "success";
		}else{

			$status = 404;
			$rows = null;//array('Result'=>"not found");
			$remarks = false;
			$msg = "User not found";
		}
		$to_encode = array('code' => $status, 
                          	'status' => $remarks,
                          	'message' => $msg,
                          	'data' => $rows,
                          );
		header('Content-Type: application/json');
		echo json_encode($to_encode);
	}
	
} else {
	http_response_code(405);
}

?>