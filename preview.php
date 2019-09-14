
<?php
$itemId = $_GET['itemId'];
for($i=0; $i<7; $i++){
	if(!is_file("img-ads/$itemId/$i.jpg")){
		break;
	}
}
if($i==0) {
	die();
}
$picsLength= $i;
echo"
<div class='wrap' style='overflow: visible;'>
	<div class='preview-wrap' style='overflow: visible;'>
		<div class='preview-medium-pic-wrap'>
			<img class='preview-medium-pic-img' src='img-ads/$itemId/0.jpg'/>
		</div>
		<div class='preview-thumb-wrap'>
			<table><tr>
";
			for($i=0; $i<$picsLength; $i++){
				echo"<td>
						<img src='img-ads/$itemId/mini/$i.jpg' class='preview-mini' id='$i'/>
					</td>";
			}
echo"		
			</tr></table>
		</div>
	</div>
</div>
";
?>
