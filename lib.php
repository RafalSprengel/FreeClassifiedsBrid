<?php
// include_once("dBug.php");
function sql($query, $par=null, $base_name = 'Brid', $lang=null){ //$query- there where should be input parameter just type "?", $par- put all parameter to array for ex. array($par1, $par2, ...), $base_name (default='Brid')
	$queryChanged = null;
	if ($lang == 'PL'){
		for($i=0; $i<count($par); $i++){
			if(preg_match('/(L|l)/', $par[$i])){
				$nopl = array('%','E','O','A','S','L','Z','Z','C','N','e','o','a','s','l','z','z','c','n');
				$universal = array('','(E|Ę)','(O|Ó)','(A|Ą)','(S|Ś)','(L|Ł)','(Z|Ż)','(Z|Ź)','(C|Ć)','(N|Ń)','(e|ę)','(o|ó)','(a|ą)','(s|ś)','(l|ł)','(z|ż)','(z|ź)','(c|ć)','(n|ń)');
				$par[$i] = str_replace($nopl, $universal, $par[$i]);
				if(!$queryChanged) $query = preg_replace('/(=\s\?|=\?|(LIKE\s\?)|(LIKE))/', ' RLIKE ?', $query);
				$queryChanged = 1;
			}
		}
	}
	$host = 'localhost';
	$par = ($par)? $par : array();
	$base_name = ($base_name)? $base_name : 'Brid';
	try{
      $db = new PDO("mysql:host=$host; dbname=$base_name;charset=utf8", "Brid", "");
      $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	  $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);//safety
	}
	catch(PDOException $e){
      echo 'Połączenie nie mogło zostać utworzone, powód: ' . $e->getMessage();
	  return false;
	  die();
	};
   
	try{
	  $zapytanie = $db->prepare($query);
	  $zapytanie->execute($par);
	  $dane = $zapytanie->fetchAll(PDO::FETCH_OBJ);	
	  //echo "query to *** $query ***<br>";
	}
	catch (PDOException $e){
	  echo "<br>Błąd związany z SQL, błąd to :".$e->getMessage()."<br>";
	  echo "query to *** $query ***<br>";
	  echo"***par to:<br>";
	  foreach($par as $numb=>$val){
		  echo"par[$numb]: $val <br>";
	  }
	  return false;
	  die();
	}
	return $dane;
}

function counterToday($id=null){
	$database = ($id) ? 'items_visitings' : null;
	$table = ($id) ? "items_vist_id_$id" : "website_visits";
	$today = date("Y-m-d",time());
	$check_table = sql("SHOW TABLES LIKE '$table'", $database);
	if(count($check_table)<1) return '0';
	$counter = sql("SELECT `counter` FROM $table WHERE `date` = '$today'", null, $database);
	if(count($counter)>0) return $counter[0]->counter;
	else return '0';
	
}

function countAll($id=null){
	$database = ($id) ? 'items_visitings' : null; 
	$table = ($id) ? "items_vist_id_$id" : "website_visits";
	$check_table = sql("SHOW TABLES LIKE '$table'", $database);
	if(count($check_table)<1) return '0';
	$counter = sql("SELECT sum(`counter`) AS `all` FROM $table ",null, $database);
	return $counter[0]->all;
}

function counterLastDay($id=null, $coun=null){
	$database = ($id) ? 'items_visitings' : null;
	$table = ($id) ? "items_vist_id_$id" : "website_visits";
	$check_table = sql("SHOW TABLES LIKE '$table'",null,$database);
	if(count($check_table)<1) return '0';
	$counter = sql("SELECT `date`, `counter` FROM $table ORDER BY `date` DESC LIMIT 1", array(),$database);
	if ($coun) return $counter[0]->counter;
	if (count($counter)>0) return $counter[0]->date;
	else return null;
} 

function saveTodaysVisit($id=null){ //if id is passed function adds counter +1 to visit item, if not- adds counter +1 to website
	$table = ($id) ? "items_vist_id_$id" : "website_visits";
	$database = null;
	$today = date('Y-m-d');
	if(!isset($_COOKIE[$table]) || $_COOKIE[$table] !== $today){ // if I have not visited today
		if($id){
			$database = 'items_visitings';
			sql("CREATE DATABASE IF NOT EXISTS `items_visitings`");
		}
		sql("CREATE TABLE IF NOT EXISTS $table (`date` date NOT NULL DEFAULT '0000-00-00', `counter` int(11) NOT NULL DEFAULT 1, PRIMARY KEY (`date`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci", null, $database);
		if(counterLastDay($id) == $today) // if someone else visited the site today.
			sql("UPDATE $table SET `counter`=`counter`+1 WHERE `date`=?", array($today), $database);
		else
			sql("INSERT INTO $table (`date`, `counter`) VALUES(?,?)", array($today, 1), $database);
		setcookie($table, $today, time()+60*60*24);
	}
	//setcookie($table, '', time()-3600); //pozwala na zwiekszenie licznika odwiedzin po kazdym przeladowaniu strony, wylaczyc to gdy funkcjonalnosc na stronie bedzie gotowa
}

function updateURLparameters($name, $value){
	
	$link = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
	$test = !Preg_match('/(.*)\?(.*)/', $link);
	if (!Preg_match('/(.*)\?(.*)/', $link)) return "$link?$name=$value";
	if (Preg_match('/(.+)\?(.+)/', $link) && !Preg_match("/(\?|&)$name=/", $link)) return "$link&$name=$value";
	if (Preg_match('/(.+)\?(.+)/', $link) && Preg_match("/(.)+(\?|&)page=[0-9]+/", $link)) return preg_replace("/(\?|&)$name=[0-9]+/", "$1$name=$value", $link);
	else echo"if nie zostało spełnione!";
}
if(isset($_POST['action'])){
	session_start();
	switch($_POST['action']){
		case 'islogged' :
			session_start();
			if(isset($_SESSION['userId'])) echo '{"logged" : "yes"}';
			else echo '{"logged" : "no"}';
			die();
			break;
			
		case 'logout' :
			session_start();
			session_destroy();
			$_SESSION = array();
			Header('location:index.php');
			die();
			break;

		case 'login' :
			if(isset($_POST['user']) && isset($_POST['pass'])){
				$user_password = sql('select password from users where login=?', array($_POST['user']));
				if((count($user_password) > 0) && ($user_password[0]->password != md5($_POST['user']))) {
					$attempts = sql('SELECT usersLog.failedLoginAttempts FROM usersLog, users WHERE users.login=? AND users.userID=usersLog.userID', array($_POST['user']));
					$allAttempts = ($attempts[0]->failedLoginAttempts)+1;
					$res = sql('UPDATE usersLog, users SET usersLog.failedLoginDate=?, usersLog.failedLoginAttempts=?, usersLog.failedLoginIp=? WHERE users.login=? AND users.userID=usersLog.userID', array(time(), $allAttempts, $_SERVER['REMOTE_ADDR'], $_POST['user']));
				}
				$user = sql('select userID, login, email, active from users where login=? and password=?', array($_POST['user'], md5($_POST['pass'])));
				if(empty($user)){
					echo'{"valid" : "no", "active" : "no"}';
					die();
				}else{
					if($user[0]->active == 'yes'){
						$_SESSION['userId'] = $user[0]->userID;
						$_SESSION['loggedin'] = 1;
						$_SESSION['login'] = $user[0]->login;
						session_regenerate_id();
						$link='../ogloszenia';
						//$link=$_SERVER['SERVER_NAME'] unblock this when page is running on normal server (not xampp)
						echo'{"valid" : "yes", "active" : "yes"}';
					}else echo'{"valid" : "yes", "active" : "no", "email" : "'.$user[0]->email.'"}';
						//Header('location:index.php?error=2&email='.$user[0]->email);
				}		
			}
			break;

		case 'extendAdd' :
			if(isset($_POST['daysSelected']) && isset($_POST['itemId'])){
				$daysArray = array('7'=>7, '14'=>14, '28'=>28);
				if(!($item = sql("SELECT added_date, expiry_date FROM items WHERE itemID=? AND userID=?", array($_POST['itemId'], $_SESSION['userId'])))) die('Error: Can`t connect with database!');
				(($item[0]->expiry_date) < time()) ? $startDate= time() : $startDate= $item[0]->expiry_date;
				$newDate = $startDate + (($daysArray[$_POST['daysSelected']]) * 60*60*24);
				if($newDate > (time()+(28*60*60*24))) die('Error: Forbidden operation!');
				sql("UPDATE items SET expiry_date=? WHERE itemID=? AND userID=?", array($newDate, $_POST['itemId'], $_SESSION['userId']));
				die();
			}
			break;

		case 'deleteItem' :
			sql("DELETE FROM items WHERE itemID=? AND userID=?", array($_POST['itemId'], $_SESSION['userId']));
			//Header('location:profil.php?action=myAds');
			die();
			break;

		default : 
			die("Nie ma takiej strony:(");
			
	}//end switch
}
?>