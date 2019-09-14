<!DOCTYPE html>
<!--start header.php -->
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Ogłoszenia Brid</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<base href="http://localhost/My/ogloszenia/" />
		<!-- <link href="/my/ogloszenia/style/style.css" rel="stylesheet" type="text/css" /> -->
		<link href="style/style.css" rel="stylesheet" type="text/css" />
		<script src="scripts/jquery-3.3.1.min.js"></script>
		<script type="text/javascript" src="scripts/MyUtils.js" charset="utf-8"></script>
		<script type="text/javascript" src="scripts/script.js" charset="utf-8"></script>
		<script type="text/javascript" src="scripts/jquery.cookie.js"></script>
	</head>
	<body>
	<?php
		require_once 'lib.php';
		saveTodaysVisit();
		session_start();
		if(isset($_GET['error'])){
			if ($_GET['error']==1) echo'<script>Xalert("error", "Błąd1", "Błędny login lub hasło!")</script>' ;
			if ($_GET['error']==2) echo'<script>Xalert("info", "Konto nieaktywne!", "Konto nie zostało jeszcze aktywowane!\nProszę aktywować konto wchodząc na konto email <b style=color:blue>'.$_GET['email'].'</b> i klikając link aktywacyjny.")</script>';
			if ($_GET['error']==3 & empty($_SESSION['userId'])) echo'<script>Xalert("info", "Niezalogowany", "Proszę się zalogować aby móc dodawać ogłoszenia.")</script>';
		}
	?>
		<header>
			<div>
				<div id="header-left">
					<a id='logo-link' href='#'>Ogłoszenia</a>
					<span id='brid'>Bridlington </span>
				</div> <!--header left -->
				<div id='search'>
						<form action='index.php' method='get' id='search-form'>
							<input list='search-datalist' name='search' placeholder='wyszukaj ogłoszenie...' required />
							<datalist id='search-datalist'></datalist>
							<button type='submit' class="but" id='submit'>Szukaj</button>
						</form>
					</div>
				<div id="header-right">
					<div id="login-form">
						<?php
							//session_start();
							if (isset($_SESSION['userId'])){
								$fet = sql('select login from users where userID=?', array($_SESSION['userId'])); 
								echo'<em>Witaj '.$fet[0]->login.'</em>';
								echo'<form method="POST" action="login.php">
										<input type="text" name="action" value="logout" hidden />
										<button type="submit" class="but" value="wyloguj" >wyloguj</button>
									</form>
								';
							} else{
							echo'
									<form action="login.php" id="login" method="POST" >
										<input type="text" name="user" class="textarea-login" autocomplete="on" placeholder="Login" required /> 
										<input type="password" name="pass" class="textarea-login" autocomplete="on" placeholder="Hasło" required />
										<button type="submit" class="but" value="Zaloguj">Zaloguj</button>
									</form>
								';
							}
						?>
					</div> <!-- login-form -->
					<?php
						if (isset($_SESSION['userId'])){
							echo"
								<div id='user-option'>
									<a href='profil.php?action=myAds' class='user-option-link'>Moje ogłoszenia</a>
									<a href='profil.php?action=myProfil' class='user-option-link'>Edytuj profil</a>
								</div>
								";
						} else{
							echo"<div id='reg-but'>Zarejestruj się</div>";
						}
					?>
				</div> <!-- header right -->
			</div>
		</header>
		<content>
<!-- end header.php -->
