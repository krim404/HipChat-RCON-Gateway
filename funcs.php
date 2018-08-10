<?php
require_once('rcon.php');
define("center","TOKEN");
define("rag","TOKEN");
define("island","TOKENK");
define("admin","TOKEN");
define("sup","TOKEN");

define("apcid","APCID");
define("hilferuf","HELP:");

$server = array();
$server['rag'] = array("0.0.0.0",32330,"secret",rag);
$server['island'] = array("0.0.0.0",32330,"secret",island);
$server['center'] = array("0.0.0.0",32330,"secret",center);

function saveData($roomid,$type,$data)
{
	if(!is_numeric($roomid)) return;
	if(strlen($type) > 1) return;
	$sdata = @json_encode($data);
	$sdata = str_replace("\n", "::nl::", $sdata);
	file_put_contents("descriptor/".$type."_".$roomid.".php", "<?php //".$sdata);
	return true;
}

function loadData($roomid,$type)
{
	if(!is_numeric($roomid)) return;
	if(strlen($type) > 1) return;
	$oa = file_get_contents("descriptor/".$type."_".$roomid.".php");
	$oa = substr($oa, 8);
	$oa = str_replace("::nl::","\n", $oa);
	return @json_decode($oa);
}

function refreshAccessToken($roomid) 
{
	
	$json = loadData($roomid,"i");
	if(!$json)
		return false;
		
	$req = array("grant_type"=>'client_credentials');
	$ch = curl_init($json->capabilities->oauth2Provider->tokenUrl);
	curl_setopt_array($ch, array(
	    CURLOPT_POST => TRUE,
	    CURLOPT_RETURNTRANSFER => TRUE,
	    CURLOPT_HTTPHEADER => array("Content-type: application/json"),
	    CURLOPT_USERPWD => $json->oauthId . ":" . $json->oauthSecret,
	    CURLOPT_POSTFIELDS => json_encode($req)
	));
	
	// Send the request
	$response = curl_exec($ch);

	// Check for errors
	if($response === FALSE)
	{
	    die(curl_error($ch));
	}
	
	// Decode the response
	$r = json_decode($response);
	if(isset($r->error))
	{
		$data = array();
		$data['validTill'] = (int)(time() + 60*30);
		$data['error'] = true;
		return false;
	}
		
		
	$data = array();
	$data['validTill'] = (int)(time() + ($r->expires_in / 1000) - 60);
	$data['token'] = $r->access_token;
	return saveData($roomid,"t",$data);	
}

function curlhs($url,$js)
{
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($js));
	$json_response = curl_exec($curl);
	curl_close($curl);	
}

function rconCommand($values,$command)
{
	$rete = "";
	$rcon = new Rcon($values[0], $values[1], $values[2], 3);
	if ($rcon->connect())
	{
	  $rete = $rcon->send_command($command);
	} else
	{
		return false;
	}
	$rcon->disconnect();
	return $rete;
}

function getAccessToken($roomid)
{
	if($d = loadData($roomid,"t"))
	{
		if($d->validTill < time())
		{
			if(refreshAccessToken($roomid))
				return getAccessToken($roomid);
			else
				return false;
		}
		
		if(isset($d->error))
			return false;
		else
			return $d->token;
			
	} else return false;
}

function saveHelptoAPC($username,$message,$name)
{
	$cur = @unserialize(apc_fetch(apcid));
	if(!$cur || !is_array($cur)) $cur = array();
	$id = strtolower($username);
	$cur[$id] = array(time(),$message,$name,$username,false,0);
	apc_store(apcid, serialize($cur));
} 

function setRequestAsProcess($name)
{
	$name = strtolower($name);
	$data = getAllRequests();
	if(isset($data[$name]))
	{
		$data[$name][4] = true;
		$data[$name][5] = time();
	}
	apc_store(apcid, serialize($data));
}

function isRequestProcess($name)
{
	$name = strtolower($name);
	$data = getAllRequests();
	if(isset($data[$name]))
	{
		return $data[$name][4];
	}
	return false;
}

function isRequestTimeout($name)
{
	$name = strtolower($name);
	$data = getAllRequests();
	if(isset($data[$name]))
	{
		if($data[$name][4])
		{
			if(($data[$name][5] + 600) > time())
				return false;
		}
	}
	return true;
}

function removeRequest($name)
{
	$name = strtolower($name);
	$data = getAllRequests();
	unset($data[$name]);
	apc_store(apcid, serialize($data));
}

function getAllRequests()
{
	$data = @unserialize(apc_fetch(apcid));
	if(!$data || !is_array($data)) $data = array();
	return $data;
}