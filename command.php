<?php
require("funcs.php");
$json = file_get_contents('php://input');
$json = json_decode($json);

//$json->item->message->from->name
$text = explode(" ", $json->item->message->message);
$target = strtolower($text[1]);

$commands = array("/reply NAME"=>"Antworten auf ein Support Ticket","/listcommands"=>"Eine Liste aller Befehle","/listservers"=>"Eine Liste aller verbundener Server","/listplayers SERVER"=>"Eine Liste aller aktiver Spieler inkl SteamID","/kick SERVER STEAMID"=>"Kicken eines Spielers vom Server");

//Serverliste
if($text[0] == "/listservers")
{
	$srvs = "";
	foreach($server as $name => $null)
	{
		$srvs .= $name.',';
	}
	$srvs = substr($srvs, 0,-1);
	
	$array = array('color'=>'purple','message'=>"Die möglichen Zielserver lauten: ".$srvs,'notify'=>false,'message_format'=>'text');
	header('Content-Type: application/json');
	echo json_encode($array);
} //Befehlsliste
else if($text[0] == "/listcommands")
{
	$txt = "";
	foreach($commands as $cmd => $desc)
		$txt .= $cmd." - ".$desc."\n";
	
	$array = array('color'=>'purple','message'=>"Die möglichen Befehle:\n".$txt,'notify'=>false,'message_format'=>'text');
	header('Content-Type: application/json');
	echo json_encode($array);
} //Fehlerbehandlung
else if(!isset($text[1]) || strlen($text[1]) < 2)
{
	$array = array('color'=>'purple','message'=>"Der Befehl '".$text[0]."' erfordert die Angabe eines Servers. Eine Liste der möglichen Server wird mit /listservers angezeigt",'notify'=>false,'message_format'=>'text');
	header('Content-Type: application/json');
	echo json_encode($array);
		
} else
{
	//Prüfen ob Server korrekt
	if(!isset($server[$target]))
	{
		$array = array('color'=>'purple','message'=>"Der Server ".$target." ist unbekannt. Eine Liste aller verbundener Server wird mit /listservers angezeigt",'notify'=>false,'message_format'=>'text');
		header('Content-Type: application/json');
		echo json_encode($array);
	} else
	{
		//Kontextabhängige Befehle
		switch($text[0])
		{
			case "/listplayers":
				$befehl = "ListPlayers";
				$rsd = rconCommand($server[$target],$befehl);
				$array = array('color'=>'purple','message'=>"Spielerliste auf '".$target."':\n".$rsd,'notify'=>false,'message_format'=>'text');
				header('Content-Type: application/json');
				echo json_encode($array);
			break;
			
			case "/kick":
				if(!$text[2] || strlen($text[2]) < 4 || !is_numeric($text[2]))
				{
					$txt = "Die SteamID '".$text[2]."' ist nicht gültig. Eine Liste der gültigen SteamIDs auf einem Server kann durch /listplayers SERVER ausgegeben werden";
					$array = array('color'=>'purple','message'=>$txt,'notify'=>false,'message_format'=>'text');
					header('Content-Type: application/json');
					echo json_encode($array);
				} else
				{
					$befehl = "KickPlayer ".$text[2];
					$rsd = rconCommand($server[$target],$befehl);
					$array = array('color'=>'purple','message'=>"Der Server ".$target." hat den Befehl zum Kicken der SteamID ".$text[2]." empfangen",'notify'=>false,'message_format'=>'text');
					header('Content-Type: application/json');
					echo json_encode($array);
				}
			break;
			
			default:
				$array = array('color'=>'purple','message'=>"Unbekannter Fehler bei ".$text[0],'notify'=>false,'message_format'=>'text');
				header('Content-Type: application/json');
				echo json_encode($array);
			
			break;
		}
	}
}