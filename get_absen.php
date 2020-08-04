<?php
include 'conn.php';

header('Content-Type: text/javascript; charset=utf8');

header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Credentials', 'true');

header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

header('Access-Control-Allow-Headers Content-Type');

header('Access-Control-Max-Age: 3628800');

$method_req = $_SERVER['REQUEST_METHOD'];

function getDistance($latitude1, $longitude1, $latitude2, $longitude2) {  
  $earth_radius = 6371;

  $dLat = deg2rad($latitude2 - $latitude1);  
  $dLon = deg2rad($longitude2 - $longitude1);  

  $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * sin($dLon/2) * sin($dLon/2);  
  $c = 2 * asin(sqrt($a));  
  $d = $earth_radius * $c;  

  return $d;  
}


if($method_req == "POST") {
	$data = json_decode(file_get_contents('php://input'), true);

	if(isset($data['long']) && isset($data['lat']) && isset($data['id_dealer']) && isset($data['id_user'])) {
		$sQuery = "SELECT longitude,latitude FROM id_dealer = '".$data['id_dealer']."'";
		$result = mysqli_query($koneksi, $query) or die (mysqli_error());
		$rowCount = mysqli_num_rows($result);
		if($rowCount > 0) {
			$row = mysql_fetch_assoc($result);
			$distance = getDistance($data['lat'],$data['long'], $row['latitude'], $row['longitude']);
			$result = $distance
			// if ($distance < 100) {
			//   $result = $distance;
			// } else {
			//   $result = $distance;
			// }
		} else {
			$result = "Data Not Found !";
		}
		
	} else {

	}
} else {

	http_response_code(405);
}
?>