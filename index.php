<?php
//returns a big old hunk of JSON from a non-private IG account page.
function scrape_insta($username) {
	ob_start();
	$insta_source = file_get_contents('http://instagram.com/'.$username);
	$shards = explode('window._sharedData = ', $insta_source);
	$insta_json = explode(';</script>', $shards[1]); 
	$insta_array = json_decode($insta_json[0], TRUE);
	$output = ob_get_contents();
	ob_end_clean();
	return $insta_array;
}
//php function what moves the divs according to the condition
function move_catalog($username){ 
	//Supply a username
	$my_account = $username; 
	//Recording all data to one item
	$results_array = scrape_insta($my_account);
	//Checking whether there is a wanted account
	if ($results_array['entry_data']['ProfilePage'][0]['user']!=null and file_get_contents('http://instagram.com/'.$username)!=false) {
		//Checking if this account is not private
		if($results_array['entry_data']['ProfilePage'][0]['user']['is_private']==false){
			//
			for ($i=0; $i < 12; $i++) { 
				//An example of where to go from there
				$latest_array = $results_array['entry_data']['ProfilePage'][0]['user']['media']['nodes'][$i]; 
				//Outputing pictures in cycle in the corresponding blocks (divs)
				echo '<div class="image_item col-xs-12 col-sm-6 col-md-4 col-lg-4">
					<a href="http://instagram.com/p/'.$latest_array['code'].'"><img src="'.$latest_array['display_src'].'"></a>
					<p class="holder">Likes: '.$latest_array['likes']['count'].' - Comments: '.$latest_array['comments']['count'].'</p>
				</div>';
			}
		} 
		//If account is private
		else if($results_array['entry_data']['ProfilePage'][0]['user']['is_private']==true) {
			 echo '<div class="view_private_account">
					<img src="asset/images/private_view.png">
					<p>К сожалению данный пользователь ограничил доступ к своему аккаунту в <a href="https://www.instagram.com/?hl=ru">Instagram</a>. Поэтому его галерея не может быть отображена.</p>
				</div>';
		}
	} 
	//If the account is not found
	else {
		echo '<div class="view_empty_search">
				<img src="asset/images/instagram.png">
				<p>Данный веб-сайт дает вам возможность просмотра галереи того или иного пользователя <a href="https://www.instagram.com/?hl=ru">Instagram</a>. Вам нужно ввести название его галереи в поисковое поле, что находиться в правом верхнем углу веб-страници. Также у вас будет возможность посортировать каталог по дате публикаций. При нажатии на фотографию вы будете переадресованы на страницу выбраной публикации.</p>
			</div>';
	}
}

function move_sort_btn($username){
	$my_account = $username; 
	$results_array = scrape_insta($my_account);
	//If account is found and it is not private
	if ($results_array['entry_data']['ProfilePage'][0]['user']!=null and file_get_contents('http://instagram.com/'.$username)!=false) {
		if($results_array['entry_data']['ProfilePage'][0]['user']['is_private']==false){
			echo '<div class="sort_btn col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<input type="button" onclick="sort()" name="sort" id="sort" value="Сортировка по дате">
				</div>';
		}
	}
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Instaparser</title>
	<meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" href="asset/images/logo.svg" type="image/x-icon">

    <link rel="stylesheet" type="text/css" href="asset/style/main.css">

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">		<!--connect to Bootstrap lib.-->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css"> 
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script> 

    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script> <!-- connect to AJAX and jQuery lib.-->

    <script>		
    	//sorted function
		function sort() {
		    var parent = $("#shuffle"); //Found parent div, where we want use sort
		    var childrens = parent.children(); //Found childrens
		    for (var i = 0; i < 11; i++) {
		       parent.append(childrens.splice(10-i, 1)[0]); //Adding blocks(divs) to the beginning, after removing.
		    }    
		};
	</script>

</head>
<body>
	<div class="toper">
		<div class="container">
			<div class="row">
				<img src="asset/images/logo.svg" class="hidden-xs col-sm-2 col-md-2 col-lg-2">
				<p class="hidden-xs col-sm-4 col-md-6 col-lg-8">Instaparser</p>
				<form  method="post" name="insta_search" id="insta_search" >
					<ul>
						<li><input class="input_search_instaname col-xs-6 col-sm-3 col-md-2 col-lg-1" type="text" name="insta_name" id="insta_name" placeholder="Инстаграм.."></li>
						<li><input class="search_btn col-xs-6 col-sm-3 col-md-2 col-lg-1" type="submit" id="submit_search_query_instaname" value="Найти"></li>
					</ul>
				</form>		
			</div>
		</div>
	</div>
	


	<div class="container">
		<?move_sort_btn($_POST['insta_name']);?>
		<div  id="shuffle">
			<? 
				move_catalog($_POST['insta_name']);
			// echo 'Likes: '.$latest_array['likes']['count'].' - Comments: '.$latest_array['comments']['count'];
			// echo '<p>Likes: '.$latest_array['likes']['count'].' - Comments: '.$latest_array['comments']['count'].'</p>';
			?>
		</div>
	</div>

	

	<div class="footer">
		<div class="container">
			<div class="row">
				<ul>
					<li class="hidden-xs col-sm-2 col-md-4 col-lg-4"><a href="https://www.instagram.com/?hl=ru"></a></li>
					<li class="col-xs-12 col-sm-10 col-md-8 col-lg-8"><p>Учебный проект, выполнен в 2017 (c) maxorkema@gmail.com</p></li>
				</ul>
			</div>			
		</div>		
	</div>
</body>
</html>