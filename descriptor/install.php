<?php
$json = file_get_contents('php://input');
$dc = json_decode($json,true);
$oc = json_decode(file_get_contents($dc['capabilitiesUrl']),true);
$fin = array_merge($dc,$oc);
if($dc['roomId'])
	file_put_contents("i_".$dc['roomId'].".php", "<?php //".json_encode($fin));
