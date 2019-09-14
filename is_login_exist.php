<?php
	require('lib.php');
	$login = $_POST['login'];
	$result = sql('select userId from users where login=?', array($login));
	if (isset($result[0]->userId)) echo"yes";
	else echo"no";
	

?>