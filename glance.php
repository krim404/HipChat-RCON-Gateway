<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
$j = array();
$j["label"] = array("type"=>"html","value"=>"<b>Ark Support Requests</b>");
$j["status"]["type"] = "lozenge";
$j["status"]["value"] = array("label"=>"open","type"=>"current");
$j["metadata"] = array("dummy"=>true);
echo json_encode($j);