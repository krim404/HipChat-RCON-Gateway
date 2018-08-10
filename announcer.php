<?php
require_once("funcs.php");

$timeout = 3;
$arr = array(	"serverchat Regeln und Infos unter https://tinyurl.com/BDH-Info",
	  			"serverchat Schreibt HELP: [Euer Problem] im Chat um ein Support Ticket zu erstellen"
	  		);
	  		
$which = rand(0,sizeof($arr)-1);
foreach($server as $name => $values)
{
	$rcon = new Rcon($values[0], $values[1], $values[2], $timeout);
	if ($rcon->connect())
	{
	 // echo "Sende an: ".$values[3]." ".$arr[$which];
	  $chat = $rcon->send_command($arr[$which]);
	}
	$rcon->disconnect();
}