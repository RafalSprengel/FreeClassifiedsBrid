<?php
require_once ('lib.php');

if (isset($_GET['suggest'])){
	$suggest = $_GET['suggest'];
	$query = sql("SELECT `title` FROM `items` WHERE `title` LIKE '%$suggest%' LIMIT 5 ");
	$result='';
	for($i=0; count($query)>$i ;$i++){
		$result .= $query[$i]->title.";";
	}
	print_r($result);
} elseif (isset($_GET['list'])){
	print_r($_GET['list']);
}
?>