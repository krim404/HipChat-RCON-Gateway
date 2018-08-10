<?php
	header("Access-Control-Allow-Origin: *");
	require_once("funcs.php");
	$path = dirname($_SERVER["SCRIPT_URI"]);
	if(isset($_GET['delit']) && isset($_GET['deler']))
	{
		removeRequest($_GET['delit']);
		$msg = "Die Support Anfrage von '".strip_tags($_GET['delit'])."' wurde durch '".strip_tags($_GET['deler'])."' entfernt";
		$js = array('color'=>"green",'message'=>$msg,'notify'=>false,'message_format'=>'text');
		curlhs(sup,$js);	
	}
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="//aui-cdn.atlassian.com/aui-hipchat/0.0.3/css/aui-hipchat.min.css"></link>
        <script type="text/javascript" src="//code.jquery.com/jquery-2.2.2.min.js"></script>
        <script type="text/javascript" src="//aui-cdn.atlassian.com/aui-hipchat/0.0.3/js/aui-hipchat.min.js"></script>
        <script type="text/javascript" src="https://www.hipchat.com/atlassian-connect/all.js"></script>
        <script type="text/javascript">
	       var cu = "";
	       HipChat.user.getCurrentUser(function(err, success) {
				if (err) {
				  // error
				} else {
				  // success
				  cu = success.name;
				}
			});
	    </script>
        <meta http-equiv="refresh" content="30; URL=<?php echo $path;?>/sidebar.php"/>
    </head>
    <body style="background-color: white"> 
	    
	    <section class="aui-connect-page" role="main">
		   <section class="aui-connect-content with-list">
		      <ol class="aui-connect-list">
		         
		         <?php
			         $req = getAllRequests();
			         
				         foreach($req as $username => $data)
				         {
						 ?>  
		         
				         <li class="aui-connect-list-item">
				            <span class="aui-connect-list-item-title"><?php echo $username; ?></span>
				            <ul class="aui-connect-list-item-attributes">
				               <li>Server: <?php echo $data[2];?></li>
				               <li><?php echo date("d.m.Y H:i", $data[0]);?></li>
				            </ul>
				            <div class="aui-connect-list-item-description" style="font-size: x-small;padding-bottom: 10px">
				               <?php 
					               $d = explode(hilferuf,$data[1]);
					               echo $d[1];
					            ?>
				            </div>
				             <ul class="aui-connect-list-item-attributes">
					           <li><a onclick="HipChat.chat.focus(); HipChat.chat.appendMessage('/reply <?php echo str_replace(" ","_",$username); ?> ');">Antworten</a></li>
				               <li style="float:right"><a onclick="location.href=this.href+'?delit=<?php echo $username; ?>&deler='+cu; return false;">Entfernen</a></li>
				             </ul>
				             <span style="clear:both"></span>
				         </li>
				         
						<?php
		         		}
		         		?>
		      </ol>
			</section>
		</section>
	    
	    <?php
		    if(sizeof($req) == 0)
	         { 
		  ?>
		  <footer class="aui-connect-footer">
			<span>Keine Anfragen vorhanden</span>
		</footer>
		 <?php        
	         } 
	    ?>
	</body>
</html>
