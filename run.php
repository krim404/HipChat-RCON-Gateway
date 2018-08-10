<?php
require_once("funcs.php");


$donot = array("teleportplayeridtome","fly (","teleporttoplayer ","infinitestats ("," walk ("," ghost (","kill (","enablespectator (","destroymytarget (","stopspectating (");

function sendToHipChat($message,$url,$rcon)
{
	global $donot;
	$message = strip_tags($message);
	$name = '';
	$notify = false;
	$color = 'gray';
	if($url == center)
		$name = 'CENTER';
	elseif($url == rag)
		$name = 'RAG';	
	elseif($url == island)
		$name = 'ISLAND';	
		
	$tmp = explode(" (", $message);
	$tmp2 = explode(")",$tmp[1]);
	
	if(isRequestProcess($tmp2[0]))
	{
		if(!isRequestTimeout($tmp2[0]))
		{
			$js = array('color'=>$color,'message'=>$message,'notify'=>$notify,'message_format'=>'text');
			curlhs(sup,$js);	
		}
	}
	
	if(strpos($message, "AdminCmd") !== false)
	{
		foreach($donot as $e)
		{
			if(strpos(strtolower($message), $e) !== false)
				return;
		}

		$message = $name.': '.$message;	
		$url = admin;
	}
	
	else if(strpos($message, hilferuf) !== false)
	{
		if(strpos($message,"SERVER:") !== false)
			return;
		
		saveHelptoAPC($tmp2[0],$message,$name);
		
		$message = $name.': '.$message.' @all';	
		$notify = true;
		$color = "red";
		$url = sup;
		$rcon->send_command("serverchattoplayer ".$tmp2[0]." Dein Hilferuf wurde an die Supporter weitergeleitet");
	}
	
	$js = array('color'=>$color,'message'=>$message,'notify'=>$notify,'message_format'=>'text');
	curlhs($url,$js);		
	
}

$timeout = 3;
foreach($server as $name => $values)
{
	$rcon = new Rcon($values[0], $values[1], $values[2], $timeout);
	if ($rcon->connect())
	{
	  //echo "Sende an: ".$values[3];
	  $chat = $rcon->send_command('getchat');
	  $e = explode("\n", $chat);
	  foreach($e as $c)
	  {
		  if(strlen($c) > 5 && strpos($c,"Server received") === false)
		  	sendToHipChat($c,$values[3],$rcon);
	  }
	}
	$rcon->disconnect();
}