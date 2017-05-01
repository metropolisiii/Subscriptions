<?php
	$db1 = mysql_connect('localhost', 'root', 'password');
	$rv = mysql_select_db('subscriptions', $db1);
	$handle = fopen ('members.csv', 'r');
	while (($row = fgetcsv($handle)) !== false){
		foreach ($row as $field){
			#find out if user already in subscribers
			$result=mysql_query("SELECT * FROM subscribers WHERE username='".$row[0]."'");
			if (mysql_num_rows($result)){
				#if so, get id
				$user=mysql_fetch_array($result);
				$userid=$user['id'];
			}
			else{
				#if not, insert and return id
				mysql_query("INSERT INTO subscribers (username, email) VALUES ('".$row[0]."', '".$row[1]."')");
				$userid = mysql_insert_id();
			}
			#see if userid and id of 15 is already in themes_subscribers
			$results2 = mysql_query("SELECT * FROM themes_subscribers WHERE theme_id=15 AND subscriber_id=".$userid);
			if (mysql_num_rows($results2)==0){
				#if not, insert into themes subscribers
				mysql_query("INSERT INTO themes_subscribers (theme_id, subscriber_id) VALUES (15, ".$userid.")");
			}
			
		}
	}
	fclose($handle);
?>