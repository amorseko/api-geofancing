<?php
$IP  = "192.168.1.201";
$Key = "0";
$result = array();
function Parse_Data ($data,$p1,$p2) {
  $data = " ".$data;
  $hasil = "";
  $awal = strpos($data,$p1);
  if ($awal != "") {
    $akhir = strpos(strstr($data,$p1),$p2);
    if ($akhir != ""){
      $hasil=substr($data,$awal+strlen($p1),$akhir-strlen($p1));
    }
  }
  return $hasil;    
}

$Connect = fsockopen($IP, "80", $errno, $errstr, 1);
if ($Connect) {
  $soap_request = "<GetAttLog>
    <ArgComKey xsi:type=\"xsd:integer\">".$Key."</ArgComKey>
    <Arg><PIN xsi:type=\"xsd:integer\">All</PIN></Arg>
  </GetAttLog>";

  $newLine = "\r\n";
  fputs($Connect, "POST /iWsService HTTP/1.0".$newLine);
  fputs($Connect, "Content-Type: text/xml".$newLine);
  fputs($Connect, "Content-Length: ".strlen($soap_request).$newLine.$newLine);
  fputs($Connect, $soap_request.$newLine);
  $buffer = "";
  while($Response = fgets($Connect, 1024)) {
    $buffer = $buffer.$Response;
  }
  $code = 200;
  $status  = "success"; 
  $error = false;
} else {
  $code = 404;
  $status  = 'failed';
  $error = true;
  echo "Koneksi Gagal";
} 

$buffer = Parse_Data($buffer,"<GetAttLogResponse>","</GetAttLogResponse>");
$buffer = explode("\r\n",$buffer);

for ($a=0; $a<count($buffer); $a++) {
  $data=Parse_Data($buffer[$a],"<Row>","</Row>");

  $export[$a]['nik'] = Parse_Data($data,"<PIN>","</PIN>");
  $export[$a]['waktu'] = Parse_Data($data,"<DateTime>","</DateTime>");
  $export[$a]['status'] = Parse_Data($data,"<Status>","</Status>");
}

$result['code'] = $code;
$result['status'] = $status;
$result['error'] = $error;
$result['data'] = $export ;
$datajson = $result;
echo json_encode($datajson);
?>