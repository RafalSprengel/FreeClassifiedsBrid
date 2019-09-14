<?php
session_start();
include 'lib.php';
if(isset($_POST['logout']) && $_POST['logout']=='yes') {
	session_destroy();
	$_SESSION = array();
	Header('location:index.php');
	die();
}
else if ((isset($_POST['user'])) && (isset($_POST['pass']))){
	$user = sql('select userID, login, email, active from users where login=? and password=?', array($_POST['user'], md5($_POST['pass'])));
	if(empty($user)){
		$attempts = sql('SELECT usersLog.failedLoginAttempts FROM usersLog, users WHERE users.login=? AND users.userID=usersLog.userID', array($_POST['user']));
		$allAttempts = ($attempts[0]->failedLoginAttempts)+1;
		$res = sql('UPDATE usersLog, users SET usersLog.failedLoginDate=?, usersLog.failedLoginAttempts=?, usersLog.failedLoginIp=? WHERE users.login=? AND users.userID=usersLog.userID', array(time(), $allAttempts, $_SERVER['REMOTE_ADDR'], $_POST['user']));
		Header('location:index.php?error=1');
		die("login error");
	}else {
		sql('UPDATE usersLog SET lastLoggedDate=?, failedLoginAttempts=0, lastLoggedIp=? WHERE userId=?', array(time(), $_SERVER['REMOTE_ADDR'], $user[0]->userID));
		if($user[0]->active == 'yes'){
			$_SESSION['userId'] = $user[0]->userID;
			$_SESSION['loggedin'] = 1;
			$_SESSION['login'] = $user[0]->login;
			session_regenerate_id();
			$link='../ogloszenia';
			//$link=$_SERVER['SERVER_NAME'] unblock this when page is running on normal server (not xampp)
			header("Location:$link");
		}else Header('location:index.php?error=2&email='.$user[0]->email);
	}	
}

echo"Nie ma takiej strony :(<!-- end login.php -->";
?>