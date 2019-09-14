<?php
require_once('header.php');

if (isset($_SESSION['userId']) && !empty($_SESSION['userId'])){
	$userData = sql('SELECT login, email FROM users WHERE userID=?', array($_SESSION['userId']));
}
if(isset($_POST['message'])){
	if(!empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['message'])){
		$check = '/^[a-zA-Z0-9.\-_]+@[a-zA-Z0-9\-.]+\.[a-zA-Z]{2,15}$/';
		if(!preg_match($check, $_POST['email'])) {echo"Nieprawidłowy adres email!"; die();}

	}else {
		echo"<script>Xalert('error', 'Błąd!','Obowiązkowe pola formularza nie zostały wypełnione!')</script>"; die();
	}
	die();
}
if(isset($_GET['id'])){
	$id = $_GET['id'];
	$itemData = sql("SELECT items.title, items.added_date, items.expiry_date, users.login, items.phone, items.town, usersLog.registerDate, usersLog.lastLoggedDate, items.description, items.views FROM users, items, usersLog WHERE items.itemID=? AND users.userID=items.userID AND users.userID=usersLog.userID", array($id));
	if(count($itemData) > 0){
		echo"
			<div id='show-item'>
				<div id='left-column'>
					<div id='inner-left-column'>
						<h1 id='title'>Treśc ogłoszenia</h1>
						<ul>
							<li><strong>Opublikowane:</strong> ".date('d-m-Y H:s', $itemData[0]->added_date)."
							<li><strong>Wygasa:</strong> ".date('d-m-Y H:s', $itemData[0]->expiry_date)."
							<li><strong>Numer telefonu:</strong> ".$itemData[0]->phone."
							<li><strong>Miasto:</strong> ".$itemData[0]->town."
						</ul>
						<h3>Opis:</h3>
							".$itemData[0]->description."
						<div id='pictures'>
		";
							if (is_dir('img-ads/'.($id).'/')) {
								$dir = scandir('img-ads/'.$id.'/');
								for($i=0; $i<count($dir)-3; $i++){
									echo"<img src='img-ads/$id/mini/$i.jpg' id='$i'/>";
								}
							}
		echo"		</div>
					</div>
						<div id='views'><strong>ilość wszystkich wyświetleń:</strong> ".$itemData[0]->views."
						</div>
				</div>
				<div id='right-column'>
					<div id='navbar' >
						<div id='contact' class='navSelected fade'>Kontakt</div>
						<div id='author' >Autor</div>
						<div id='map' >Mapa</div>
					</div>
					<div id='content'>
						<div id='content-contact'>
								<p><img src='img/letter.png' id='letter-ico'/>Aby skontaktować się z autorem ogłoszenia, wypełnij poniższy formularz.</p>
								<form action='showitem.php' method='POST'>
									<input name='message' hidden/>
									<input ".((isset($userData[0]->login)) ? "value=".$userData[0]->login : "placeholder='Imię' autofocus")." name='name' required/>
									<input ".((isset($userData[0]->email)) ? "value=".$userData[0]->email : "placeholder='email'")." name='email' required/>
									<input value='Re: {$itemData[0]->description}' name='title'required/>
									<textarea rows='10' placeholder='wiadomość' name='message' required ".((!empty($userData)) ? "autofocus" : "")."></textarea>
									<div><input  style='width:auto' type='checkbox' value='copy'/><span>Wyślij kopię tej wiadomości na mój email</span></div>
									<button type='submit' class='style-transparent-but'>Wyślij</button>
								
								</form>
					</div>
						<div id='content-author' class='hidden'>
							<h3>Informacje o autorze ogłoszenia</h3>
							<div class='avatar-wrap'><img src='img/avatar.png' class='avatar-img'></div>
							<p><strong>Dodane przez: </strong>".$itemData[0]->login."</p>
							<p><strong>Zarejestrowany od: </strong> ".date('d-m-Y H:s', $itemData[0]->added_date)."</p>
							<p><strong>Ostatnio zalogowany: </strong> ".date('d-m-Y H:s', $itemData[0]->lastLoggedDate)."</p>
							<br>lastloggeddate to: ".($itemData[0]->lastLoggedDate);
		echo"			</div>
						<div id='content-map' class='hidden'>zawartość zakładki Mapa</div>
					</div>
				</div>
			</div>
		";
	}else echo"nie znaleziono przedmiotu !"; 
}else echo"Nie znaleziono takiej strony :(";
require_once('footer.php');
?>