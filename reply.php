<?php
/*$js = array('color'=>$color,'message'=>$message,'notify'=>$notify,'message_format'=>'text');		
$curl = curl_init($url);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($js));
$json_response = curl_exec($curl);
curl_close($curl);*/

require("funcs.php");
$json = file_get_contents('php://input');
$json = json_decode($json);

//$json->item->message->from->name
$text = explode(" ", $json->item->message->message);
$target = strtolower($text[1]);
unset($text[0]);
unset($text[1]);
$rtext = implode(" ", $text);

$r = getAllRequests();
if(!isset($r[$target]))
	$target = str_replace("_"," ",$target);

if(!isset($r[$target]))
{
	$array = array('color'=>'purple','message'=>"Der Benutzer '".$target."' hat keine Anfrage gestellt",'notify'=>false,'message_format'=>'text');
	header('Content-Type: application/json');
	echo json_encode($array);
}
else
{
	$sid = strtolower($r[$target][2]);
	$srv = $server[$sid];
	if(isset($server[$sid]))
	{
		$befehl = 'ServerChatToPlayer "'.$r[$target][3].'" "'.$json->item->message->from->name.' [Support]: '.$rtext.'"';
		$rsd = rconCommand($srv,$befehl);
		setRequestAsProcess($target);
		
		/*$array = array('color'=>'purple','message'=>"DEBUG: ".print_r($rsd,true),'notify'=>false,'message_format'=>'text');
		header('Content-Type: application/json');
		echo json_encode($array);*/

	} else
	{
		$array = array('color'=>'purple','message'=>"Ein interner Fehler ist aufgetreten...",'notify'=>false,'message_format'=>'text');
		header('Content-Type: application/json');
		echo json_encode($array);
	}
	
	
}
