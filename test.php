<?php

include 'enkripsi.php';

$Enkripsi = new Enkripsi();

$password = $_GET['password'];

echo $Enkripsi->decode($password);


?>