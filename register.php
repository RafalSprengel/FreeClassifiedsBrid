<?php
require('lib.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';
$mail = new PHPMailer(true); 
$mail->CharSet = "UTF-8";

if(isset($_GET['code'])){
	$code = $_GET['code'];
	$result = sql("SELECT `login`, `active` FROM `users` WHERE `active`=?", array($code));
	if(count($result)>0){
		foreach($result as $value){
			sql("UPDATE users SET active='yes' WHERE login=?", array($value->login));
			echo"Dziękujemy! Konto ".$value->login." zostało pomyślnie aktywowane, teraz możesz zalogować sie na swoje konto <a href='http://{$_SERVER['HTTP_HOST']}'>przejdz na stronę</a>.";
		}
	}else {
		echo"Kliknięty link nie jest już aktywny lub konto zostało wcześniej aktywowane. Kliknij <a href='http://{$_SERVER['HTTP_HOST']}'>tutaj</a> aby przejść na stronę główną.";
	}
	exit;
}

if( isset($_POST['reg-login']) && isset($_POST['reg-email']) && isset($_POST['reg-pass'])){
	$reg_exp_pass = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?:[a-zA-Z0-9!@#$%\^&*_]{8,12})$/';
	if(!preg_match($reg_exp_pass, $_POST['reg-pass'])) die('password not allowed');
	$login = strip_tags($_POST['reg-login']);
	$email = strip_tags($_POST['reg-email']);
	$pass = md5($_POST['reg-pass']);
	$activ_code = md5(mt_rand(1,10000));
	
	try{
		//Server settings
		$mail->isSMTP();
		$mail->Mailer = "smtp";                               // Set mailer to use SMTP
		$mail->SMTPAuth = true;                               // Enable SMTP authentication
		//$mail->SMTPDebug = 1;                                 // Enable verbose debug output

		$mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
		$mail->Port = 465;       //or (if tls)587  or (if ssl)465                           // TCP port to connect to
		$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
		$mail->Username = 'ogloszenia.register@gmail.com';                 // SMTP username
		$mail->Password = 'Kokos-123';                           // SMTP password
		$mail->SMTPOptions = array(
			'ssl' => array(
				'verify_peer' => false,
				'verify_peer_name' => false,
				'allow_self_signed' => true
		 ));
		 
		//Recipients
		$mail->setFrom('ogloszenia.register@gmail.com', 'Ogłoszenia Bridlington');
		$mail->addAddress($email, $login);     // Add a recipient           // Name is optional
		$mail->addReplyTo('sprengel.rafal@gmail.com', 'Information');
		//Content
		$mail->isHTML(true);                                  // Set email format to HTML
		$mail->Subject = 'Aktywacja konta na '.$_SERVER['HTTP_HOST'];
		$link = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."/register.php?code=".$activ_code;
		$mail->Body    = "W celu dokończenia rejestracji na stronie {$_SERVER['HTTP_HOST']} proszę kliknąc link: <a href=$link>aktywuj teraz</a>, wiadomość może się znaleźć w folderze SPAM";
		$mail->AltBody = "W celu dokończenia rejestracji na stronie {$_SERVER['HTTP_HOST']} proszę kliknąc link: <a href=$link>aktywuj teraz</a>, wiadomość może się znaleźć w folderze SPAM";

		$mail->send();
		sql("INSERT INTO users (login, password, email, active) VALUES(?,?,?,?)", array($login, $pass, $email, $activ_code));
		$data = sql("SELECT userID FROM users WHERE login=?", array($login));
		sql("INSERT INTO usersLog (userID, registerDate) VALUES(?,?)", array($data[0]->userID, time()));
		echo"Link aktywacyjny został wysłany na podany  email! \nProszę kliknąć link aktywacyjny wysłany na maila.";
	
	} catch (Exception $e){
		echo "Błąd przy wysyłaniu maila aktywacyjnego!<br>Szczegóły: ".$mail->ErrorInfo."<br>";
	} 
	
} else echo"błąd formularza!, próba wysłania formularza z niekompletnymi danymi.";
?>

