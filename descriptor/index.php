<?php header('Content-Type: application/json'); 

$path = dirname($_SERVER["SCRIPT_URI"]);
$array = array();
$array['key'] = "krim-hipchat-ark";
$array['name'] = "Ark Support Tool";
$array['description'] = "Ark Administration Plugin";
$array['vendor'] = array("name"=>"Krim","url"=>"http://krim.me");
$array['links'] = array("self"=>"");
$array['capabilities']["hipchatApiConsumer"]["scopes"] = array("send_notification","view_room");


$glance = array();
$glance["name"]["value"] = "Ark Support Requests";
$glance["queryUrl"] = $path."/glance.php";
$glance["key"] = "krim-hipchat-ark-glance";
$glance["target"] = "krim_hipchat_ark-wp";
$glance["conditions"] = array();
$glance["icon"] = array("url"=>"https://www.worldofminecraft.de/wp-content/uploads/2011/01/logo_300x293.png","url@2x"=>"https://www.worldofminecraft.de/wp-content/uploads/2011/01/logo_300x293.png");
$array['capabilities']["glance"][] = $glance;

$wp = array();
$wp["key"] = "krim_hipchat_ark-wp";
$wp["name"]["value"] = "Ark Support Requests";
$wp["location"] = "hipchat.sidebar.right";
$wp["url"] = $path."/sidebar.php";
$array['capabilities']["webPanel"][] = $wp; 

$hook = array("event"=>"room_message","url"=>$path."/reply.php","pattern"=>"^\/reply (.*)$","name"=>"Ark Antwort","authentication"=>"jwt");
$hook2 = array("event"=>"room_message","url"=>$path."/command.php","pattern"=>"^\/listplayers(.*)$","name"=>"Ark ListPlayers","authentication"=>"jwt");
$hook3 = array("event"=>"room_message","url"=>$path."/command.php","pattern"=>"^\/kick(.*)$","name"=>"Ark Kickplayer","authentication"=>"jwt");
$hook4 = array("event"=>"room_message","url"=>$path."/command.php","pattern"=>"^\/listservers$","name"=>"Ark ListServers","authentication"=>"jwt");
$hook5 = array("event"=>"room_message","url"=>$path."/command.php","pattern"=>"^\/listcommands$","name"=>"Ark ListCommands","authentication"=>"jwt");

$array['capabilities']["webhook"][] = $hook;
$array['capabilities']["webhook"][] = $hook2;
$array['capabilities']["webhook"][] = $hook3;
$array['capabilities']["webhook"][] = $hook4;
$array['capabilities']["webhook"][] = $hook5;

$array['capabilities']['installable'] = array("allowGlobal"=>false,"allowRoom"=>true,"callbackUrl"=>$path."/descriptor/install.php");

echo json_encode($array);