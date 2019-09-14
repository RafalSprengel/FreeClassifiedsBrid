<?php
require_once('header.php');
if (isset($_GET['id'])) saveTodaysVisit($_GET['id']);
$sort = (isset($_GET['sort'])) ? $_GET['sort'] : null;
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;

if (isset($_GET['sort'])) {
    if ($_GET['sort'] == 'price-low-high') $sql_sort = 'ORDER BY price ASC';
    elseif ($_GET['sort'] == 'price-high-low') $sql_sort = 'ORDER BY price DESC';
    elseif ($_GET['sort'] == 'oldest') $sql_sort = 'ORDER BY added_date ASC';
    elseif ($_GET['sort'] == 'latest') $sql_sort = 'ORDER BY added_date DESC';
    else {
        echo "page unvailable!";
        die();
    }
} else  $sql_sort = null;



function drawPagination($current_page, $no_of_all_pages){
    if ($no_of_all_pages > 0) {
        echo "
			<div class='pagination'>
				<ul>";
        if ($no_of_all_pages < 2) {
            echo "<li><a class='active' >1</a></li>";
        } else {

            if ($current_page > 1) {
                $link_backward = updateURLparameters("page", $current_page - 1);
                echo "<li><a href=$link_backward><<</a></li>";
            }
            for ($i = 1; ($no_of_all_pages + 1) > $i; $i++) {
                $class = ($current_page == $i) ? $class = 'class= "active"' : null;
                $link = updateURLparameters('page', $i);
                echo "<li><a href='$link' $class >$i</a></li>";
            }
            if ($current_page < $no_of_all_pages) {
                $link_forward = updateURLparameters("page", $current_page + 1);
                echo "<li><a href=$link_forward>>></a></li>";
            }
        }
        echo "
				</ul>
			</div>";
    }
}
function drawBreadcrumbs(){
	$arr = [];
	$link = '';
	if(!isset($_GET['lev1'])) return null;
	echo "<ul class='breadcrumb'>";
	for ($i = 1; isset($_GET['lev'.$i]); $i++){
		echo ($i>1) ? " > " : "";
		if ($i>1) $link .= "/";
		$section = sql("select name from menu where url_name = ?", array($_GET['lev'.$i]));
		$section = $section[0]->name;
		$link .= $_GET['lev'.$i];
		echo "<a href='$link'>".$section."</a>";
	}
	echo "
		</ul>";
}
function drawSortBar($sort, $max_on_page){
	echo"
		<form id='sort' >
			<select name='sort'>
				<option value=''>Sortuj</option>
				<option value='price-low-high'".(($sort=='price-low-high')?'SELECTED':null)." ><a href='costam.pl'>Najniższa cena</a></option>
				<option value='price-high-low'".(($sort=='price-high-low')?'SELECTED':null).">Najwyższa cena</option>
				<option value='oldest'".(($sort=='oldest')?'SELECTED':null).">Najstarsze</option>
				<option value='latest'".(($sort=='latest')?'SELECTED':null).">Najnowsze</option>
			</select>
			<select name='max-on-page'>
				<option value='5'".(($max_on_page=='5')?'SELECTED':null).">5</option>
				<option value='10'".(($max_on_page=='10')?'SELECTED':null).">10</option>
				<option value='20'".(($max_on_page=='20')?'SELECTED':null).">20</option>
				<option value='50'".(($max_on_page=='50')?'SELECTED':null).">50</option>
			</select>
		</form>
	";
}
function check_is_level_exists_in_database(){
	for ($i = 1; isset($_GET['lev'.$i]); $i++){
		$data = sql("select name from menu where url_name = ?", array($_GET['lev'.$i]));
		if (empty($data)) die("nie ma takiego działu!");
	}
}

?>
    <!--start index.php-->
<div id='main-wrap'>
	<?php
	for ($i = 1; isset($_GET['lev'.$i]); $i++){ // sets variable lev1_url_name,lev2_url_name, lev3_url_name, ...
		${'lev'.$i.'_url_name'} = $_GET['lev'.$i];
		$data = sql("select name from menu where url_name = ?", array(${'lev'.$i.'_url_name'}));
		${'lev'.$i.'_name'} = (count($data) > 0) ? $data[0]->name : null;
	}
	$level1_section_names = sql('select url_name, name from menu where level2=0', array());
	check_is_level_exists_in_database();
	?>
    <div id='menu'>
        <div id="wrap-category">
            <span id="category-title">KATEGORIE</span>
            <div id="category-container">
                <?php
                echo
                "<ul>";
                for ($i = 0; $i<count($level1_section_names);$i++){ // Left menu
					$q = "select count(*) as number from items where level1=? group by concat(level1)";
					$level1_count = sql($q, array($level1_section_names[$i]->name));
					$level2_names = sql('select url_name, name from menu where level1=? and level2 > 0', array($i+1));
					$class_l1 = (!empty($_GET['lev1']) && empty($_GET['lev2_url_nam']) && $_GET['lev1'] == $level1_section_names[$i]->name) ? 'active' : '';
					if (empty($level1_count[0]->number)) $level1_count = 0;
					else $level1_count = $level1_count[0]->number;
					echo"
						<li id='lev1={$level1_section_names[$i]->name}'>
								<img src='img/arrow.png' class='arrow' />
								<a href='{$level1_section_names[$i]->url_name}' class='menu$i menu-list {$class_l1}'>{$level1_section_names[$i]->name} ({$level1_count })</a>
						";
					if (count($level2_names)>0) {
						echo
						"<ul ";
						if (!empty($lev1_url_name)){
							if (strtolower($_GET['lev1']) != strtolower($level1_section_names[$i]->name)) echo 'hidden';
						}
						else echo "hidden";
						echo
						">";
						for ($y = 0;
						$y<count($level2_names);$y++){ //level2 $y- count of level2
							$q = "select count(*) as number from items where level1=? and level2=? group by concat(level1,level2)";
							$level2_count_arr = sql($q, array($level1_section_names[$i]->name, $level2_names[$y]->name));
							$count_level2 = (isset($level2_count_arr[0]->number)) ? $level2_count_arr[0]->number : 0;
							$class_l2 = (!empty($_GET['lev2_url_nam']) && $_GET['lev2_url_nam'] == $level2_names[$y]->name) ? 'active' : '';
							echo
							"<li class='submenu$i' id='lev1=".$level1_section_names[$i]->name."&lev2_url_nam=".$level2_names[$y]->name."' >
								<a href='".$level1_section_names[$i]->url_name."/".$level2_names[$y]->url_name."' class='menu-list {$class_l2}'>".$level2_names[$y]->name." (".$count_level2.")</a>
							 </li> ";
						}
						echo
						"</ul>";
					}
					echo
					"</li>";
                }
                echo
                "</ul>";
                ?>
            </div> <!-- end of category-container -->
            <!-- <button id="but-add"><a href='add_ad.php'>Dodaj ogłoszenie</a></button> -->
			<form action="add_ad.php" id='add_ad_but'>
				<button >Dodaj ogłoszenie</button>
			</form>
        </div> <!--end of wrap category -->
    </div> <!--end of menu-->
    <div id="wrap-items-list">
        <div id="items-list">
            <?php
					$max_on_page = (isset($_GET['max-on-page'])) ? $_GET['max-on-page'] : '5';
					$offset = ($current_page-1) * $max_on_page;

					if (isset($lev1_url_name) && !isset($lev2_url_name)) { //if is only lev1 set
						$q = "select items.itemId, users.login, items.title, items.description, items.price, items.added_date
								from items, menu, users where items.level1=? and menu.menuId=items.menuId and items.userID=users.userID $sql_sort LIMIT ? OFFSET ?";
						$data = sql($q, array($lev1_name, $max_on_page, $offset));
						$ads_amount = sql('select count(*) as number from items where level1=?', array($lev1_name));
						$ads_amount = $ads_amount[0]->number;
					}
					elseif (isset($lev1_url_name) && isset($lev2_url_name)){  // if is lev1 and lev2_url_nam set
						$lev2_url_name = htmlspecialchars($lev2_url_name, ENT_QUOTES);
						$ads_amount = sql("SELECT count(*) AS number FROM items WHERE level1=? and level2=?", array($lev1_url_name, $lev2_url_name));
						$ads_amount = $ads_amount[0]->number;
						$q = "select items.itemId, users.login, items.title, items.description, items.price, items.added_date
									from items, menu, users where items.level1=? and items.level2=? and menu.menuId=items.menuId and items.userID=users.userID $sql_sort LIMIT ? OFFSET ?";
						$data = sql($q, array($lev1_name, $lev2_name, $max_on_page, $offset));
						
					}elseif(isset($_GET['search'])){
						if (isset($_GET['page']) && ((!is_numeric($_GET['page']) || $_GET['page']<1) || $_GET['page']>999999999)) die('error!');
						$search_word = $_GET['search'];
						$offset = ($current_page-1) * $max_on_page;
						$data = sql("SELECT items.title, items.itemId, items.price, items.added_date, items.description, users.login, users.phone, users.city  FROM items, users WHERE title LIKE ? AND items.userID=users.userID $sql_sort LIMIT ? OFFSET ?", array("%$search_word%", $max_on_page, $offset));
						$ads_amount = sql("SELECT count(*) AS number FROM items WHERE title LIKE ?", array("%$search_word%"));
						$ads_amount = $ads_amount[0]->number;
						
					}
					else { //if is no lev1 and lev2 (main page)
						$ads_amount = sql("SELECT count(*) AS number FROM items", array());
						$ads_amount = $ads_amount[0]->number;
						$q = "select items.itemId, users.login, items.title, items.description, items.price, items.added_date
									from items, menu, users where menu.menuId=items.menuId and items.userID=users.userID $sql_sort LIMIT ? OFFSET ?";
						$data = sql($q, array($max_on_page, $offset));
					}
					$no_of_all_pages = ceil($ads_amount/$max_on_page);
					echo" <div id='breadcrumbs-sort-bar'>";
						drawBreadcrumbs();
						if ($ads_amount > 0) drawSortBar($sort, $max_on_page);
					echo"
						</div> <!--end of breadcrumbs-sort-bar-->
						<div id='ads'>
						";
						if($no_of_all_pages > 0){
							for ($i = 0;$i<count($data);$i++){
								(is_file('img-ads/'.$data[$i]->itemId.'/mini/0.jpg'))? $img_src = 'img-ads/'.$data[$i]->itemId.'/mini/0.jpg' : $img_src = 'img/no.jpg';
								$shortDesc = (strlen($data[$i]->description)>160)? substr($data[$i]->description, 0, 160).' ...' : $data[$i]->description;
								echo "
												<div class='post-block' id='".$data[$i]->itemId."' >
													<div class='post-left' id='".$data[$i]->itemId."' >
														<a href='?id=".$data[$i]->itemId."'>
															<img src='".$img_src."' class='post-left-img' />
														</a>
													</div>
													<div class='post-right'>
														<div class='post-title'><a href='?id=".$data[$i]->itemId."'>".$data[$i]->title."</a></div>
														<div class='post-meta'><span class='price'>cena: £".$data[$i]->price." </span> &nbsp|&nbsp użytkownik: ".$data[$i]->login."&nbsp | &nbsp".date("d-m-Y", $data[$i]->added_date)."</div>
														<div class='post-desc'>$shortDesc</div>	
													</div>
												</div>
									";
							}
							drawPagination($current_page, $no_of_all_pages);
						}
				
				///////  end sector items list  ////////
				///////sector shows item details ////
				// if (isset($_GET['id'])){
				// if(!is_numeric($_GET['id'])) die('invalid id!');
				// $id = $_GET['id'];
				// $data = sql('SELECT title, description, price, added_date, userId, level1, level2 FROM items WHERE itemID=?', array($id));
				// if(empty($data)) echo"nie ma danych pod tym id! ";
				// echo $data;
				// $user = sql('select login, email, phone, city, address, postcode from users where userID=?', array($data[0]->userId));
				//////// breadcrumbs in item details //////
				// echo'<ul class="breadcrumb">
				// <li><a href=?lev1='.$data[0]->level1.'>'.$data[0]->level1.'</a></li>
				// <li><a href=?lev1='.$data[0]->level1.'&lev2_url_nam='.$data[0]->level2.'>'.$data[0]->level2.'</a></li>
				// </ul>';
				/////// end of breadcrumbs  ////////////////////////


				// echo"
				// <div id='ad-bloc'>
				// <div id='ad-title'>".$data[0]->title."</div>
				// <hr>
				// <div>Opublikowane: ".date("m-d-Y", $data[0]->added_date)."</div>
				// <div>Cena: £".$data[0]->price."</div>
				// <div>Opis:</br>
				// ".$data[0]->description."
				// </div>
				// <div>Numer telefonu: ".$user[0]->phone."</div>
				// <div>Miasto: ".$user[0]->city."</div>
				// <div>Id ogłoszenia: ".$id."</div>
				// <div>Ilość wyświetleń: 12</div>
				// <table class='ad-mini-pic'>
				// <tr>
				// ";
				// if (is_dir('img-ads/'.$id.'/')) {
				// $dir = scandir('img-ads/'.$id.'/');
				// for($i=0; $i<count($dir)-3; $i++){
				// echo"<td> <img src='img-ads/$id/mini/$i.jpg' data-item_id='$id' data-item_no='$i' /> </td>";

				// }
				// }
				// echo"
				// </tr>
				// </table>

				// </div>
				// </div>
				// ";
				// }////// end sector item details //////

				/* if (isset($_GET['search'])){
					if (isset($_GET['page']) && ((!is_numeric($_GET['page']) || $_GET['page']<1) || $_GET['page']>999999999)) die('error!');
					$search_word = $_GET['search'];
					$offset = ($current_page-1) * $max_on_page;
					$data = sql("SELECT items.title, items.itemID, items.price, items.added_date, items.description, users.login, users.phone, users.city  FROM items, users WHERE title LIKE ? AND items.userID=users.userID $sql_sort LIMIT ? OFFSET ?", array("%$search_word%", $max_on_page, $offset));
					$ads_amount = sql("SELECT count(*) AS number FROM items WHERE title LIKE ?", array("%$search_word%"));
					$ads_amount = $ads_amount[0]->number;
					$no_of_all_pages = ceil($ads_amount/$max_on_page);
					if($no_of_all_pages > 0){
						for ($i = 0;$i<count($data);$i++){
							(is_file('img-ads/'.$data[$i]->itemID.'/mini/0.jpg'))? $img_src = 'img-ads/'.$data[$i]->itemID.'/mini/0.jpg' : $img_src = 'img/no.jpg';
							$shortDesc = (strlen($data[$i]->description)>160)? substr($data[$i]->description, 0, 160).' ...' : $data[$i]->description;
							echo "
											<div class='post-block' id='".$data[$i]->itemID."' >
												<div class='post-left' id='".$data[$i]->itemID."' >
													<a href='?id=".$data[$i]->itemID."'>
														<img src='".$img_src."' class='post-left-img' />
													</a>
												</div>
												<div class='post-right'>
													<div class='post-title'><a href='?id=".$data[$i]->itemID."'>".$data[$i]->title."</a></div>
													<div class='post-meta'><span class='price'>cena: £".$data[$i]->price." </span> &nbsp|&nbsp użytkownik: ".$data[$i]->login."&nbsp | &nbsp".date("d-m-Y", $data[$i]->added_date)."</div>
													<div class='post-desc'>$shortDesc</div>	
												</div>
											</div>
								";
						}
						drawPagination($current_page, $no_of_all_pages);
					}
					else echo"Nie znaleziono żadnych ogłoszeń :(";
					
					echo "</div>";
				} */
            ?>
        </div> <!--powinien byc koniec div items-list -->
    </div> <!-- end of wrap-items-list -->
</div>
    </div> <!-- end of main-wrap -->
<!--end index.php-->
<?php require_once ('footer.php'); ?>
	