<?php

// mavrick.id.au

if($_POST['output']) 
{

	ob_start();
	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
	
	// We'll be outputting a PDF
	header('Content-type: text/plain');
	
	// It will be called downloaded.pdf
	header('Content-Disposition: attachment; filename="pbsvmsg.cfg"');
	
	die(stripslashes($_POST['output']));

}

ob_start();

if($_POST) 
{

	sleep(1);
	
	$_POST['msg'] = explode(',',$_POST['msg']);
	
	if(!$_POST['msg']) $_POST['msg'] = explode(',',$_POST['message']);

	$tmp = array();
	$s = $_POST['start'];
	
	foreach($_POST['msg'] as $k => $v) {
		if(trim($v)) {
			$tmp[] = $v;
		}
	}
	
	$_POST['msg'] = $tmp; unset($tmp);
	
	if(empty($_POST['msg'])) die(';ERROR:'."\n".';Please enter in a few messages to generate your PB code.');
	
	/*
	
	PB_SV_TASK X Y "say <message>"
	
	* A = number of messages to display
	* B = the time delay between displaying messages
	
	* Y = (B * A) + the X
	
	*/
	
	//$total = (((count($_POST['msg'])-1) * $_POST['delay']) + ($s*2));

	$total = (count($_POST['msg'])*$_POST['delay']);
	
	$total = ($total+$_POST['start']);
	
	?>
pb_sv_taskempty

<?php
	foreach($_POST['msg'] as $k => $v) { 
	
	$v = str_replace(':comma:',',',$v);
?>
pb_sv_task <?=((($k*$_POST['delay'])+$s))?> <?=$total?> "<?=($_POST['from']=='pb' ? 'pb_sv_say ' : false)?>say <?=$v?>"
<?php

	}
	
	$output = ob_get_clean();
	ob_start();
	
	if($_POST['save']) 
	{
	
		require($_SERVER['DOCUMENT_ROOT'].'/guid/includes/guid/guid.settings.php');
		
		/* make connection to database */
		$conn = mysql_connect($settings['hostname'], $settings['username'], $settings['password']) or die(mysql_error());
		$db = mysql_select_db($settings['database'], $conn) or die(mysql_error());
	
		if((int)$_POST['msgID']&&$_POST['email']) 
		{
		
			$sql = "SELECT * FROM `msgindex` WHERE `id` = '".$_POST['msgID']."' AND `email` = '".$_POST['email']."'";
			$query = mysql_query($sql, $conn) or die(mysql_error());
			
			if(mysql_num_rows($query)) 
			{
			
				$id = $_POST['msgID'];
				
				$sql = "DELETE FROM `msgdata` WHERE `indexID` = '".$id."'";
				$query = mysql_query($sql, $conn) or die(mysql_error());
				
				foreach($_POST['msg'] as $k => $v) 
				{
					$v = str_replace(':comma:',',',$v);
					$sql = "INSERT INTO `msgdata` (`indexID`,`msg`,`msgIndex`) VALUES ('".$id."','".$v."','".$k."')";
					$query = mysql_query($sql, $conn) or die(mysql_error());
				}
				
				?>
                $('overlayMiddle').setHTML('Message information has been saved to message ID: <?=$id?>');
                <?php
			
			} 
			else 
			{
			
				?>
                $('overlayMiddle').setHTML('Unable to verify message ID with your email address.');
                <?php
			
			}
		
		} 
		else 
		{
	
			$sql = "INSERT INTO `msgindex` (`email`,`ip`,`delay`,`length`,`from`) VALUES ('".addslashes($_POST['email'])."','".$_SERVER['REMOTE_ADDR']."','".$s."','".$_POST['delay']."','".$_POST['from']."')";
			$query = mysql_query($sql, $conn) or die(mysql_error());
			$id = mysql_insert_id();
			
			$_POST['msg'] = (!is_array($_POST['msg']) ? explode(',',$_POST['msg']) : $_POST['msg']);
			
			foreach($_POST['msg'] as $k => $v) 
			{
				$v = str_replace(':comma:',',',$v);
				$sql = "INSERT INTO `msgdata` (`indexID`,`msg`,`msgIndex`) VALUES ('".$id."','".addslashes($v)."','".$k."')";
				$query = mysql_query($sql, $conn) or die(mysql_error());
			}
		
		}
	
		//$output = ob_get_clean();
		
		$toName = $_POST['email'];
		$toEmail = $_POST['email'];
		
		$body = 'Your message rotation has been saved and a copy has been attached to this email.<br /><br />Your message ID is: '.$id.'<br /><br />You can access your server message roation from this link or enter in the message ID on the message roation page.<br /><br />Regards,<br /><br />mavrick.id.au';
		
		$subject = 'Your Message Rotation Has Been Saved';
		
		require("htmlmimemail/htmlMimeMail.php");
		
		$mail = new htmlMimeMail();
		
		/* set from */
		$mail->setFrom('"mavrick.id.au" <no-reply@mavrick.id.au>');
		
		/* set subject */
		$mail->setSubject($subject);
		
		/* body */
		$mail->setHTML($body);
		
		/* set custom headers */
		$mail->setHeader('X-Sender', 'no-reply@mavrick.id.au');
		$mail->setHeader('Reply-To', 'no-reply@mavrick.id.au');
		$mail->setHeader('X-Priority', '1');
		
		$result = $mail->send(array('"'.$toName.'" <'.$toEmail.'>'));
		
		?>
        var msgID = '<?=$id?>';
        $('overlayMiddle').setHTML('The message ID has been sent to your email account.');
        <?php
	
		die;
	
	}

	die($output);

}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="en-au" />
<meta name="copyright" content="2008, mavrick.id.au" />
<meta name="author" content="mavrick.id.au" />
<meta name="owner" content="mavrick.id.au" />
<meta name="description" content="Punkbuster Console Message Generator - Compatible for any game that runs Punkbuster!" />
<meta name="keywords" content="punkbuster, evenbalance, cod4, call of duty, cod waw, cod:waw, punkbuster messages, cod4 server messages, cod4 server message, punkbuster message generator, cod4 message generator" />
<title>Punkbuster Console Message Generator v1.4</title>
<link href="/css/punkbuster.css" rel="stylesheet" type="text/css" media="screen" />
</head>
<body>
<div align="left" id="fb_postForm">
  <h1>Punkbuster Console Message Generator v1.4</h1>
  <div class="info">Compatible for any game that runs Punkbuster!</div><br />
  <div class="fb_gen" id="fb_top">
  	<div class="fb_container">
		<div class="item">
			<div class="header">
				<div class="left"></div>
				<div class="title" title="Other links">Other links</div>
				<div class="right"></div>
			</div>
			<div class="middle" rel="middle"><strong>Updated:</strong> <a href="/cod5-map-generator.php">COD:WAW Map Rotation Generator</a><br />
			  <strong>Updated:</strong> <a href="/cod4-map-generator.php">COD 4 Map Rotation Generator</a><br />
			  <a href="#changelog">Change log</a><br /><br />
			  </div>
				<div class="bottom">
				<div class="left"></div>
				<div class="right"></div>
			</div>
		</div>
	</div>
  </div>
  <div><br />
  <div class="fb_gen">
  	<div class="fb_container">
		<div class="item">
			<div class="header">
				<div class="left"></div>
				<div class="title" title="Load Saved Messages">Load Saved Messages</div>
				<div class="right"></div>
			</div>
			<div class="middle" rel="middle">
			<div style="padding:6px;">
				Message ID: <input type="text" name="msgID" id="fb_msgID" /><br />
			</div>
			<div style="float:right;text-align:right;margin:0 auto;">
			<input type="button" name="loadmsg" id="fb_loadmsg" value="Load Message ID" />
			</div>
			<div style="padding:6px;">
				Email Address: <input type="text" name="emailAddy" id="fb_emailAddy" /><br />
			</div>			
			</div>
			<div class="bottom">
				<div class="left"></div>
				<div class="right"></div>
			</div>
		</div>
	</div>
  </div>
  <div><br />
  <div id="fb_settings">
  	<div class="fb_container">
		<div class="item">
			<div class="header">
				<div class="left"></div>
				<div class="title" title="Settings">Settings</div>
				<div class="right"></div>
			</div>
			<div class="middle" rel="middle">
				<div class="info">
					Select the desired settings for your message rotation code. The default message rotation settings have been set by default. In most cases you do <strong>NOT</strong> need to change these.
				</div>
				<div style="padding:6px;">
					Display first message after:
					<select name="msg_start" id="msg_start" lang="en" xml:lang="en">
						<option value="10">10 seconds</option>
						<option value="15">15 seconds</option>
						<option value="20">20 seconds</option>
						<option value="25">25 seconds</option>
						<option value="30">30 seconds</option>
						<option value="35">35 seconds</option>
						<option value="40">40 seconds</option>
						<option value="45">45 seconds</option>
						<option value="50">50 seconds</option>
						<option value="55">55 seconds</option>
						<option value="60">60 seconds</option>
					</select>
			  </div>
				  <div style="padding:6px;">
					Display messages every: 
					<select name="msg_delay" id="msg_delay" lang="en" xml:lang="en">
						<option value="30">30 seconds</option>
						<option value="40">40 seconds</option>
						<option value="50">50 seconds</option>
						<option value="60">60 seconds</option>
						<option value="70">70 seconds</option>
						<option value="80">80 seconds</option>
						<option value="90">90 seconds</option>
						<option value="100">100 seconds</option>
						<option value="110">110 seconds</option>
						<option value="120">120 seconds</option>
						<option value="120">130 seconds</option>
						<option value="120">140 seconds</option>
						<option value="150">150 seconds</option>
						<option value="180">180 seconds</option>
					</select>
				  </div>
				  <div style="padding:6px;">
					Display messages from: 
					<select name="msg_from" id="msg_from" lang="en" xml:lang="en">
						<option value="console">Console:</option>
						<option value="pb">PunkBuster ADMIN:</option>
					</select>
				  </div>
			</div>
			<div class="bottom">
				<div class="left"></div>
				<div class="right"></div>
			</div>
		</div>
	</div>
  </div>
  <hr /><br />
  <div id="fb_msgs">
  	<div class="fb_container">
		<div class="item">
			<div class="header">
				<div class="left"></div>
				<div class="title" title="Messages">Messages</div>
				<div class="right"></div>
			</div>
			<div class="middle" rel="middle">
				<div class="info">
					To add more message item press the green plus button. You need at least 1 message to generate your message rotation code. Once you have entered in all your messages click the "Generate Output" button.
				</div>
			  <div id="container">
				  <div class="msg" id="msg">
					  <div class="msg_title">Message 1:</div><input name="message[]" id="msg_1" lang="en" style="width:300px;" />
					  <div align="left" class="msg_add"><img src="/images/icons/icon_add.gif" title="Add New Messgae Row" id="msg_add" border="0" style="cursor:pointer;cursor:hand;" /></div>
				  </div>
			  </div>
			  <hr />
			  <div>
				  <input type="button" value="Generate Output" id="generate" name="generate" />
			  </div>
		    </div>
			<div class="bottom">
				<div class="left"></div>
				<div class="right"></div>
			</div>
		</div>
	</div>
  </div><br />
  <hr />
  <div id="fb_output">
  	<div class="fb_container">
		<div class="item">
			<div class="header">
				<div class="left"></div>
				<div class="title" title="Output">Output</div>
				<div class="right"></div>
			</div>
			<div class="middle" rel="middle">
				<div class="info">
					Using IE 6,7,8? There can be formatting issues with the returned information, try using Firefox, Safari or Chrome.
				</div>
				  <form name="form" id="fb_form" method="post" action="/punkbuster.php">
				  <div>
					  <textarea name="output" id="output" rows="15" style="width:500px;" readonly="readonly"></textarea>
				    <div class="commands">
						<input type="button" name="selectall" value="Select All" id="fb_select" disabled="disabled" /> - or - 
						<input type="button" name="download" value="Download pbsvmsg.cfg" id="fb_download" disabled="disabled" />
					  - or - 
					  <input type="button" name="save" value="Save (new!)" id="fb_save" disabled="disabled" />
					  </div>
				  </div>
				  </form>
		  </div>
			<div class="bottom">
				<div class="left"></div>
				<div class="right"></div>
			</div>
		</div>
	</div>
  </div><br />
	<div id="colorcodes">
		<div class="fb_container">
			<div class="item">
				<div class="header">
					<div class="left"></div>
					<div class="title" title="Color Codes">Color Codes</div>
					<div class="right"></div>
				</div>
				<div class="middle" rel="middle">
					<div id="color_red">^1 <span class="red">red</span><br /></div>
					<div id="color_green">^2 <span class="green">green</span><br /></div>
					<div id="color_yellow">^3 <span class="yellow">yellow</span><br /></div>
					<div id="color_blue">^4 <span class="blue">blue</span><br /></div>
					<div id="color_lightblue">^5 <span class="lightblue">light blue</span><br /></div>
					<div id="color_pink">^6 <span class="pink">pink</span><br /></div>
					<div id="color_white">^7 <span class="white">white</span><br /></div>
					<div id="color_grey">^8 <span class="grey">grey</span><br /></div>
					<div id="color_black">^9 black</div>
                    <br />
				</div>
				<div class="bottom">
					<div class="left"></div>
					<div class="right"></div>
				</div>
			</div>
		</div>
	</div>
	<div class="fb_gen">
  	<div class="fb_container">
		<div class="item">
			<div class="header">
				<div class="left"></div>
				<div class="title" title="How-To setup the pbsvmsg.cfg">How-To setup the pbsvmsg.cfg</div>
				<div class="right"></div>
			</div>
			<div class="middle" rel="middle">
			  <div>
				<div>
					  <strong>Step 1</strong>: Create a new file called: <strong>pbsvmsg.cfg</strong> within the <strong>/pb/</strong> folder of your COD4/WaW <strong>dedicated server files</strong>.<br />
					  <strong>Step 2</strong>: Add the 'output' code in this file.<br />
					<strong>Step 3</strong>: Save the file within the <strong>/pb/</strong> folder of your COD4/WaW <strong>dedicated server files</strong>.	  </div>
			  </div>
		  </div>
			<div class="bottom">
				<div class="left"></div>
				<div class="right"></div>
			</div>
		</div>
	</div>
  </div><br />
  <div class="fb_gen">
  	<div class="fb_container">
		<div class="item">
			<div class="header">
				<div class="left"></div>
				<div class="title" title="How-To include the pbsvmsg.cfg">How-To include the pbsvmsg.cfg</div>
				<div class="right"></div>
			</div>
			<div class="middle" rel="middle">
			  <div>
				  <div>
					  Add the following text: 
<pre>
pb_sv_load pbsvmsg.cfg
</pre> 
					  <p>to the bottom of the <strong>pbsv.cfg</strong> file located in <strong>/pb/</strong> of your COD4/WaW <strong>dedicated server files</strong>.<br />
					    <br />
					    <strong>Don't have the <em>pbsv.cfg</em> file within the /pb/ folder of your COD4/WaW dedicated server files?:</strong><br />
					    <br />
				    No need to worry, if you have access to the <strong>FTP</strong> of your COD4/WaW <strong>dedicated server files</strong> i'm assuming you have access to <strong>rcon</strong>.</p>
					  <p><strong>Update:</strong></p>
					  <p>A lot of reported cases where you edit he /pb/ configs or generate the config via rcon but doesn't get put into /pb/ check to see if &quot;<strong>/.callofduty4/pb/</strong>&quot; or for WaW &quot;<strong>/.callofduty5/pb/</strong>&quot; exist. If it does then put the message file in there and edit he pbsv.cfg file within that folder! <br />
				        <br />
					    If you don't I <strong>CANNOT</strong> help you, sorry!<br />
				        <br />
				        <strong>Step 1</strong>: Log into rcon from either a 3rd party application or COD4/WaW itself from console.<br />
                                                                  </p>
					  <pre>
/rcon login rconpassword
</pre>
					  <strong>Step 2</strong>: Create the <strong>pbsv.cfg</strong> file within the <strong>/pb/</strong> folder of your COD4/WaW <strong>dedicated server files</strong>.<br />
<pre>
/rcon pb_sv_writecfg
</pre>
					  <strong>Step 3</strong>: Go back to the top of <em>How-To include the pbsvmsg.cfg</em> and start again.
				  </div>
			  </div>
		  </div>
			<div class="bottom">
				<div class="left"></div>
				<div class="right"></div>
			</div>
		</div>
	</div>
  </div><br />
  <div class="fb_gen">
  	<div class="fb_container">
		<div class="item">
			<div class="header">
				<div class="left"></div>
				<div class="title" title="How-To setup your main config.cfg">How-To setup your main config.cfg</div>
				<div class="right"></div>
			</div>
			<div class="middle" rel="middle">
			  <div>
			  <div>
			  Add the following text: 
<pre>
wait 5
exec pbsv.cfg
</pre> 
			  to the bottom of the <strong>config.cfg</strong> file located in <strong>/main/</strong> of your COD4/WaW <strong>dedicated server files</strong>.<br />
			  <br />
			  Download your main config.cfg file to your desktop. Open it up w/ notepad.<br />
			  The last step is to add this line to the bottom of your config.cfg file.
			  <br /><br />
			  <strong>Please Note!</strong> Your main config name will vary. e.g.:<br />
<pre>
server.cfg
dedicated.cfg
etc...
</pre>
			  Save it &amp; upload it your COD4/WaW server.<br />
			  Restart your server and now your messages should work!
			  </div>
			  </div>
		  </div>
			<div class="bottom">
				<div class="left"></div>
				<div class="right"></div>
			</div>
		</div>
	</div>
  </div><br />
  <hr />
  <div class="fb_gen">
  	<div class="fb_container">
		<div class="item">
			<div class="header">
				<div class="left"></div>
				<div class="title" title="The Command Overview">The Command Overview</div>
				<div class="right"></div>
			</div>
			<div class="middle" rel="middle">
			  <div>
				  The basic command is: PB_SV_TASK <strong>X</strong> <strong>Y</strong> "say <strong>&lt;message&gt;</strong>" and here we will discover the values required for <strong>X</strong> and <strong>Y</strong> in the command based on how many messages you want to display and the delay between messages showing.<br /><br />
				  To get an equal time between displaying say messages on your server, you can use the following simple maths:<br /><br />
				  * <strong>A</strong> = number of messages to display<br />
				  * <strong>B</strong> = the time delay between displaying messages<br /><br />
				  Then the '<strong>Y</strong>' value for all messages is:<br />
				  <br />
				  * <strong>Y</strong> = (<strong>B</strong>* <strong>A</strong>) + the <strong>X</strong> value of the first say task.	  </div>
		  </div>
			<div class="bottom">
				<div class="left"></div>
				<div class="right"></div>
			</div>
		</div>
	</div>
  </div><br />
  <div class="fb_gen">
  	<div class="fb_container">
		<div class="item">
			<div class="header">
				<div class="left"></div>
				<div class="title" title="So what does that mean in English?">So what does that mean in English?</div>
				<div class="right"></div>
			</div>
			<div class="middle" rel="middle">
  <div>
    <p>Lets say you have 5 messages to display and you want 30 seconds  between each message and that you want the first one to start 10  seconds after you start the server. </p>
    <p>Right, the <strong>X</strong> value in the <em>first</em> task statement is 10 (as  you wanted) and the successive <strong>X</strong> value for the tasks is 30 more than  the last (so the second is 40, the third 70, etc).<br />
    The <strong>Y</strong> value <em>never</em> changes and must include the 10 second offset of the first message, so the <strong>Y</strong> value for <strong>all</strong> messages (from the equation above) is (30*5) + 10 = 160 seconds. </p>
    <p>So your task list will look like this: </p>
<pre>
pb_sv_task 10 160 &quot;say Message1&quot;
pb_sv_task 40 160 &quot;say Message2&quot;  // 30 second delay + the 10 second X value above
pb_sv_task 70 160 &quot;say Message3&quot;  // 30 second delay + the 40 second X value above
pb_sv_task 100 160 &quot;say Message4&quot; // etc
pb_sv_task 130 160 &quot;say Message5&quot;
</pre>
    <p>In this example, the messages will repeat 160 seconds after the  first message. Your first message will repeat at 160 seconds (not  170!), which is 30 seconds after the last message appeared at 130  seconds. So you have 30 second gap between <strong>all</strong> messages. </p>
    <p>Thats only applies if you want your messages to display evenly  spaced. If you want them all jumbled up and looking random, make the <strong>Y</strong>  values all different. </p>
    <p>Ultimately, regardless of how you set this up, the last '<strong>X</strong>'  value should be less than the <strong>Y</strong> values, otherwise the messages will mix  up. </p>
    <p>A variation on this is if you want a number of messages, '<strong>A</strong>',  to display evenly over '<strong>B</strong>' number of seconds. In this case, the  difference between the '<strong>X</strong>' values in the <em>pb_sv_task</em> statement is: </p>
    <ul>
      <li>dX = <strong>B</strong>/<strong>A</strong> </li>
    </ul>
    <p>Your 'Y' values, again <em>never</em> change, can be calculated by: </p>
    <ul>
      <li><strong>Y</strong> = <strong>B</strong> + the <strong>X</strong> value of the first say task </li>
    </ul>
    <p>For example: You want 5 messages to display over 240 seconds (4  minutes) and you want the first message to display 10 seconds after  server start (ie, the first task X value is 10). The difference between  the 'X' values must be 240/5 = 48 and <strong>all</strong> 'Y' values must be 240 + 10 = 250.  So your task list will look like this: </p>
<pre>
pb_sv_task 10 250 &quot;say Message1&quot;
pb_sv_task 58 250 &quot;say Message2&quot;  // 48 second delay + the 10 second X value above
pb_sv_task 106 250 &quot;say Message3&quot; // 48 second delay + the 58 second X value above
pb_sv_task 154 250 &quot;say Message4&quot; // 48 second delay + the 106 second X value above
pb_sv_task 202 250 &quot;say Message5&quot; //etc
</pre>
    <p>Add the delay of 48 seconds between the last message and the first message repeating and the total is 250 - your 'Y' value. </p>
  </div>
  </div>
			<div class="bottom">
				<div class="left"></div>
				<div class="right"></div>
			</div>
		</div>
	</div>
  </div><br />
  <div class="fb_gen">
  	<div class="fb_container">
		<div class="item">
			<div class="header">
				<div class="left"></div>
				<div class="title" title="Change log">Change log<a name="changelog" id="changelog"></a></div>
				<div class="right"></div>
			</div>
			<div class="middle" rel="middle">
			  <div>
				<strong>09/05/2009</strong><br />
				Added Save feature<br />
				Fixed comma bug &quot;,&quot;<br />
				Added in special comments for .callofduty4 and .callofduty5 pb folders
			  </div>
			</div>
			<div class="bottom">
				<div class="left"></div>
				<div class="right"></div>
			</div>
		</div>
	</div>
  </div>
  <div class="overlay" id="fb_overlay">
  	
  </div>
  <div class="fb_gen_overlay" id="fb_overlayMsg">
  	<div class="fb_container">
		<div class="item">
			<div class="header">
				<div class="left"></div>
				<div class="title" title="Save you message rotation" id="overlayTitle">Save your message rotation</div>
				<div class="right"></div>
			</div>
			<div class="middle" rel="middle" id="overlayMiddle">
			  <div>
				  <div style="float:right;"><input type="button" name="saveMsg" id="fb_saveMsg" value="Save Message" /></div>
				  <div style="padding:6px;">
				  	Email: <input type="text" name="email" id="fb_emailAddyMsg" />
				  </div>	  
			  </div>
			</div>
			<div class="bottom">
				<div class="left"></div>
				<div class="right"></div>
			</div>
		</div>
	</div>
  </div>
</div>
<script language="javascript" type="text/javascript" src="/js/mootools.js"></script>
<script language="javascript" type="text/javascript" src="/js/punkbuster.js"></script>
</body>
</html>
<?php ob_end_flush(); ?>