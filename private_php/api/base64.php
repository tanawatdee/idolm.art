<?php

$src = $_GET['src'];
$split = preg_split("/(:|;|,)/", $src)
header("Content-type: ".$split[1]);
$data = $split[3];
echo base64_decode($data);

?>