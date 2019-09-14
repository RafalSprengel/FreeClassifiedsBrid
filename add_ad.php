<?php 
require_once('header.php');
echo"<!-- stert add.php -->";


if (!isset($_SESSION['userId'])) {
	Header('location:index.php?error=3');
	die();
}
$error='';
((empty($_POST['sent']))) ? $sent=false :$sent=true;

if ($sent){
	$arr = Array('level1', 'dayCode', 'title', 'desc', 'price', 'phone', 'town');
	foreach($arr as $field){
		if($_POST[$field] == ''){
			$error=1;
			echo"Pole ".$arr[$i]." nie moze byc puste! <br>";
		}
	}
	if ($error) die();
}
// 16777216 (16mb)
// 10485760 (10MB)


if (($sent) && empty($error)){ // form has been sent and no errors occurs
	$level1 = $_POST['level1'];
	//(isset($_POST['level2'])) ? $level2 = $_POST['level2'] : $level2 = 0;
	$level2 = (isset($_POST['level2'])) ? $_POST['level2'] : 0;
	$title = $_POST['title'];
	$desc =	$_POST['desc'];
	$price = $_POST['price'];
	$phone = $_POST['phone'];
	$town = $_POST['town'];
	$postcode = $_POST['postcode'];
	$dayCode = $_POST['dayCode'];
	$arrDaysCodes = array('7'=>7,'14'=>14,'28'=>'28');
	$expiryDate = time()+(60*60*24*$arrDaysCodes[$dayCode]);
	$menuID = sql("SELECT menuID FROM menu WHERE level1=? and level2=?", array($level1, $level2));
	$level1Name = sql("SELECT name FROM menu WHERE level1=? and level2=0", array($level1));
	$level2Name = sql("SELECT name FROM menu WHERE level1=? and level2=?", array($level1, $level2));
	$userName = sql("SELECT login FROM users WHERE userID=?", array($_SESSION['userId']));
	$res= sql('INSERT INTO items (level1, level2, title, description, price, phone, town, postcode, menuID, userID, added_date, expiry_date ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)', array(
		$level1Name[0]->name, $level2Name[0]->name, $title, $desc, $price, $phone, $town, $postcode, $menuID[0]->menuID, $_SESSION['userId'], time(), $expiryDate));
	if($res===false) {
		echo'<br>Błąd podczas dodawania ogłoszenia! spróbój jeszcze raz, jeżeli błąd nadal będzie występował skontaktuj się z administratorem strony.';
		echo'<a href="index.php">Powrót do strony głównej</a>';
	}else{
		
		if (!empty($_FILES['fotki'])){ // adding pictures
			$arr=$_FILES['fotki'];
			$maxFilesSize= 16777216; //  (16MB)
			$sizeAll=0;
			$i=0;
			while(!empty($arr['name'][$i])){
				$sizeAll = $sizeAll + $arr['size'][$i];
				$name=$arr['name'][$i];
				$tmp_name=$arr['tmp_name'][$i];
				$new_name = $i.'.jpg';
				$size=$arr['size'][$i];
				$type=$arr['type'][$i];
				$query = sql('SELECT itemID FROM items WHERE userID=?', array($_SESSION['userId']));
				$itemId = $query[0]->itemID;
				$dir = 'img-ads/'.$itemId.'/';
				if(!is_dir($dir)) mkdir($dir, 0777);
				if(!is_dir($dir.'mini')) mkdir($dir.'mini', 0777);
				if (is_uploaded_file($tmp_name)){
					if($size<=0) $error.='<br>* plik '.$i.' zbyt mały, lub brak pliku';
					if($type!='image/jpg' && $type!='image/jpeg') {
						$ext = explode('.', $arr['name'][$i]);
						$error.='<br>* Zdjęcia muszą być w formacie jpg lub jpeg, natomiast rozszerzenie pliku "'.$name.'" to: ".'.$ext[1].'" !';
					}
					if (($sent) && empty($error)) { //form has been sent and are no any errors
						move_uploaded_file($tmp_name, $dir.$new_name);
						$img = imagecreatefromjpeg($dir.$new_name);
						$width = imagesx($img);
						$height = imagesy($img); 
						if(($height/$width)<=0){
							$width_mini = 240;
							$height_mini = $height / $width * $width_mini;
						} else {
							$height_mini = 120;
							$width_mini = $width / $height * $height_mini;
						}
						$img_mini = imagecreatetruecolor($width_mini, $height_mini);
						imagecopyresampled($img_mini, $img, 0, 0, 0, 0, $width_mini , $height_mini, $width , $height);
						imagejpeg($img_mini,$dir.'mini/'.$new_name,80);
						imagedestroy($img);
						imagedestroy($img_mini);
					}
				} else {
					$error .= "<br>* Błąd przy ładowaniu pliku";
				}
				$i++;
			}
			if($sizeAll>$maxFilesSize) $error.='<br>* Limit dla wszystkich plików to 16MB!';
		}

		// echo'Twoje ogłoszenie zostało dodane! <a href="index.php"> Powrót do stony głównej </a>';
		$_SESSION['tryAdd'] = 1;
		header('Location: profil.php?msg=1');
	}
	
} else{ //errors occurs
	$names = sql('SELECT name FROM menu WHERE level2=0', array());
	echo"
		<DIV id='wrap-add'>
			<H2><center>Dodaj ogłoszenie:</center></H2>
			<form action='add_ad.php' id='add_ad_form' method='POST'  ENCTYPE='multipart/form-data'>
				<INPUT type='hidden' name='sent' value='true'/>
				<SELECT id='category-sel' name='level1' required >
					<option value=''>Kategoria</option>
					";
					for($i=0; $i<count($names); $i++){
					echo'<option value='.($i+1).'>'.$names[$i]->name.'</option>';
					};
					echo"
				</SELECT><br>
				<div id='suboption'></div><br>
				<SELECT name='dayCode' required>
					<option value='' disabled selected hidden>Ważność ogłoszenia</option>
					<option value='7'>7 dni</option>
					<option value='14'>14 dni</option>
					<option value='28'>28 dni</option>
				</SELECT>
				<INPUT type='text' name='title' id='add_title' placeholder='Tytuł ogłoszenia' pattern='^[^<>]{1,40}$' title='niedozwolone znaki to: `\` `<` `>` ,max 40 znaków' required/>
				<textarea name='desc' id='add_desc' placeholder='opis' pattern='[\S^\\\<>]{1,40}' title='niedozwolone znaki to: `<` `>` ,max 1000 znaków' required></textarea>
				<input type='text' name='price' id='add_price' placeholder='cena' pattern='^[£$]?[0-9]{1,9}([,.][0-9]{1,2})?[£$]?$' title='Proszę wpisać prawidłową cenę' required />
				<INPUT type='tel' name='phone' id='add_phone' placeholder='Numer telefonu' pattern='^[0-9]{10,12}$' title='Numer telefonu powinien skada się z 10 do 12 cyfr' required />
				<INPUT type='text' name='town' id='add_town' placeholder='Miasto' pattern='^[a-zA-Z ]{0,40}$' title='dozwolone znaki a-z, maz. 30 znaków' required />
				<INPUT type='text' name='postcode' id='add_postcode' pattern='^([Gg][Ii][Rr] 0[Aa]{2})|((([A-Za-z][0-9]{1,2})|(([A-Za-z][A-Ha-hJ-Yj-y][0-9]{1,2})|(([A-Za-z][0-9][A-Za-z])|([A-Za-z][A-Ha-hJ-Yj-y][0-9]?[A-Za-z])))) {0,1}[0-9][A-Za-z]{2})$' title='niepoprawny postkod' placeholder='Postcode' />
				<BR>Dodaj zdjęcia (max. 6): <span id='size-info'></span><progress id='prog-info' value='0'></progress><BR>
				<table id='tab-pic-add'>
					<tr><td><div class='div-file'><INPUT type='file' name='fotki[]' class='file-but' accept='image/*' /></div></td>
						<td><div class='div-file'><INPUT type='file' name='fotki[]' class='file-but' accept='image/*' /></div></td>
						<td><div class='div-file'><INPUT type='file' name='fotki[]' class='file-but' accept='image/*' /></div></td>
					</tr>
					<tr>
						<td><div class='div-file'><INPUT type='file' name='fotki[]' class='file-but' accept='image/*' /></div></td>
						<td><div class='div-file'><INPUT type='file' name='fotki[]' class='file-but' accept='image/*' /></div></td>
						<td><div class='div-file'><INPUT type='file' name='fotki[]' class='file-but ' accept='image/*' /></div></td>
					</tr>
				</table>
			<form/>
			<BR><BUTTON>Anuluj</BUTTON><BUTTON type='submit' form='add_ad_form' id='send'/>Dodaj ogłoszenie</BUTTON>
			<DIV class='add-form-error'>{$error}</DIV>
			
		</DIV>
		";
}
echo"<!-- end add.php -->";
require_once('footer.php');
?>
