<?php
require_once('lib.php');
if(!empty($_GET['login'])){
		$res = sql('select count(*) as number from users where login=?', array($_GET['login']));
		if ($res[0]->number == '0') echo "available";
		if ($res[0]->number == '1') echo "no available";
	
}
?>