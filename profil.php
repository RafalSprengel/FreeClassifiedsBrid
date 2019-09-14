<?php
	require_once 'lib.php';
	require_once 'header.php';
	echo"<content id='wrap-profil'>";
	if(empty($_SESSION['loggedin'])) {echo'Musisz być zalogowany aby przeglądać tą stronę!'; die();}
	if(!empty($_GET['msg']) && $_GET['msg']=='1'){echo'<script>Xalert("info", null, "Twoje ogłoszenie zostało dodane!" ,function(){window.location.href="profil.php?action=myAds"})</script>';};
	echo" <!-- start profil.php --> ";
	if (isset($_GET['ordered'])){
		$orderArr = array('title'=>'title', 'views'=>'views', 'added_date'=>'added_date', 'expiry_date'=>'expiry_date');
		($_SESSION['sort']=='desc') ? $_SESSION['sort']='asc' : $_SESSION['sort']='desc';
		$query = 'SELECT itemID, title, views, expiry_date, added_date FROM items WHERE userID=? ORDER BY '.$orderArr[$_GET['ordered']].' '.$_SESSION['sort'];
		$userItems = sql($query, array($_SESSION['userId']));	
	}else $userItems = sql('SELECT itemID, title, views, expiry_date, added_date FROM items WHERE userID=?', array($_SESSION['userId']));
		
	$userDetails = sql("SELECT users.userID, users.login, users.email, usersLog.registerDate, usersLog.lastLoggedDate FROM users, usersLog WHERE users.userID=? AND users.userID=usersLog.userID", array($_SESSION['userId']));
	if(isset($_GET['action']) && $_GET['action'] == 'myAds') {
		
		echo"
		<div id='show-my-ads'>
		";
		if (count($userItems)==0){
			echo
			"<div>Obecnie nie masz żadnych ogłoszeń.</div>";
		}else{
			echo
			"
			<table>
				<thead>
					<tr>
						<th>Nr.</th>
						<th><a href='profil.php?action=myAds&ordered=title'>Tytuł</a></th>
						<th><a href='profil.php?action=myAds&ordered=views'>Wyświetleń</a></th>
						<th><a href='profil.php?action=myAds&ordered=expiry_date'>Ważność</a></th>
						<th>Opcje</th>
					</tr>
				</thead>
				<tbody>
			";
			$activeItems=0;
			for($i=0; $i<count($userItems); $i++){
				$expires = $userItems[$i]->expiry_date;
				$daysLeft = ($expires > time()) ? round(($expires-time())/60/60/24) : 0 ;
				echo "
					<tr>
						<td>".($i+1)."</td> <td>{$userItems[$i]->title}</td> <td>{$userItems[$i]->views}</td> <td>";
						if($userItems[$i]->expiry_date > time()){
							echo'<font color=green>aktywne</font><br><font size=-1>(Pozostało '.$daysLeft.' dni</font>)';
							$activeItems++;
						}else {
							echo'<font color=red>zakończone</font><br><font size=-1>('.date('d-m-Y H:s',$userItems[$i]->expiry_date).'</font>)';
						}
						echo"</td><td><button id={$userItems[$i]->itemID} class='style-transparent-but but-ads-option delete-add-but'>usuń</button>
								 <a class=' extend-add-but' id={$userItems[$i]->itemID} data-expires=$expires><button class='style-transparent-but but-ads-option' >przedłuż</button></a></td>
					</tr>
				";
			};
			echo"
				</tbody>
			</table>
			";
		}
		echo"
				 <a href='add_ad.php'><button class='style-transparent-but but-new-add'>Dodaj ogłoszenie...</button></a>
		</div><!-- end of show-my-ads -->
		<div id='show-user-info-container'>
			<div class='account-info-strap'>Informacje o koncie :</div>
			<ul>
				<li><strong><div class='avatar-wrap'><img src=img/avatar.png class='avatar-img'></div>{$userDetails[0]->login}</strong></li>
				<li><strong>Zarejestrowano:</strong> ".date('d-m-Y',$userDetails[0]->registerDate)."</li>
				<li><strong>ostatnie logowanie:</strong> ".date('d-m-Y',$userDetails[0]->lastLoggedDate)."</li>
				<li><strong>Ilość ogłoszeń:</strong>".count($userItems); if(count($userItems)>0) echo"(aktywne {$activeItems}, niekatywne ".(count($userItems)-$activeItems).")"; echo"</li>
			</ul>
		</div ><!--end of show-user-info-container-->
		";
	}else if(isset($_GET['action']) && $_GET['action'] == 'myProfil'){
		echo"
		<div id='my-profil-wrapper'>
			<div id='edit-profil'>
				<form>
					<div>
						<label for='user-name' id='my-profile'>Nazwa użytkownika: </label>
						<input type='text' id='user-name' name='userName'>
					</div>
					<div>
						<label for='name'>Imię: </label>
						<input type='text' id='name' name='name'>
					</div>
					<div>
						<label for='surname'>Nazwisko: </label>
						<input type='text' id='surname' name='surname'>
					</div>
					<div>
						<label for='email'>E-mail</label>
						<input type='text' id='email' name='email'>
					</div>
					<div>
						<label for='webpage'>Strona www</label>
						<input type='webpage' id='webpage' name='webpage'>
					</div>
					<div>
						<label for='facebook'>Facebook</label>
						<input type='facebook' id='facebook' name='facebook'>
					</div>
				
				</form>
			</div>
			<div id='info-profil'>
				tu będzie info profilu
			</div>
		</div>
		";	
	}
	require_once 'footer.php';
?>