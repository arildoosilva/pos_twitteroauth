<html>
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/plug-ins/1.10.7/integration/bootstrap/3/dataTables.bootstrap.css">
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/1.0.6/css/dataTables.responsive.css">
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
		<script src="https://cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js"></script>
		<script src="https://cdn.datatables.net/responsive/1.0.6/js/dataTables.responsive.min.js"></script>
		<script src="https://cdn.datatables.net/plug-ins/1.10.7/integration/bootstrap/3/dataTables.bootstrap.js"></script>
		<script type="text/javascript">
			$(document).ready(function(){ 
				$('#tabela1').DataTable({
					language: {
						url: "https://cdn.datatables.net/plug-ins/1.10.7/i18n/Portuguese-Brasil.json",
					},
					// paging: false,
					searching: false,
					ordering:  false,
				});  // datatables
				$('#tabela2').DataTable({
					language: {
						url: "https://cdn.datatables.net/plug-ins/1.10.7/i18n/Portuguese-Brasil.json",
					},
					// paging: false,
					searching: false,
					ordering:  false,
				});  // datatables
				$('#tabela3').DataTable({
					language: {
						url: "https://cdn.datatables.net/plug-ins/1.10.7/i18n/Portuguese-Brasil.json",
					},
					// paging: false,
					searching: false,
					ordering:  false,
				});  // datatables
			});
		</script>
		<style type="text/css">
			table.dataTable th,
			table.dataTable td {
				white-space: nowrap;
			}
			div {
				margin-left: 10px;
				width: 99%
			}
			h2 {
				text-align: center;
			}
		</style>
	</head>
	<body>
	<?php
		require ("twitteroauth/autoload.php");
		use Abraham\TwitterOAuth\TwitterOAuth;
		
		define("CONSUMER_KEY", "Vy3EeNMvboZbFro66GQrKpPGi");
		define("CONSUMER_SECRET", "sxrLnQ77UQ7wBHVVxlQ37uSVsuMEXCWVwU4UGITleeymopraAl");
		
		// include 'twitteroauth/src/TwitterOAuth.php';
		// use Abraham\TwitterOAuth\TwitterOAuth;
		function search($string, $count, $type) {	
			$access_token = "3227234898-oMKzM9SSY9aE5I4viIisGZ58mQJceIPn6KhCiS9";
			$access_token_secret = "xHN3fYrkGAiNgSowvxigh1mMLXfegDtozP04f0s1ODRT1";
		
			$connection = new TwitterOAuth(
				CONSUMER_KEY,
				CONSUMER_SECRET,
				$access_token,
				$access_token_secret
			);
		
			$content = $connection->get("account/verify_credentials");
		
			// var_dump($content);

			$statuses = $connection->get("search/tweets", array(
			"lang" => "pt", 
			"q" => $string, 
			"count" => "20", 
			"result_type" => $type
			));
		
			// var_dump($statuses);
			// echo '<pre>'; print_r($statuses); echo '</pre>';
			return $statuses;
		}
		
		function blackList($tokens, $list) {
			return array_diff($tokens, $list);
		}
		
		function most_common($most_common, $tokens) {
			foreach ($tokens as $token) {
				if (isset($most_common[$token])) {
					$most_common[$token]++;
				}else{
					$most_common[$token] = 1;
				}
			}
			return $most_common;
		}
		
		function most_common_user($most_common_user, $users) {
			foreach ($users as $user) {
				if (isset($most_common_user[$user])) {
					$most_common_user[$user]++;
				}else{
					$most_common_user[$user] = 1;
				}
			}
			return $most_common_user;
		}
		
		function pprint($result, $table, $list) {
			$most_common = array();
			$most_common_user = array();
			$users = array();
			echo "<div><table id='". $table ."' class='table table-striped table-hover dt-responsive' 
			cellspacing='0' width='100%'><thead><tr><th>Usu&aacute;rio</th><th class='desktop'>
			Tweet</th></tr></thead><tbody>";
			foreach ($result->statuses as $singleResult) {
				$tokens = explode(' ', $singleResult->text);
				$tokens = blackList($tokens, $list);

				echo "<tr>
					<td><a href='http://twitter.com/" . $singleResult->user->screen_name . "'><img src='" .
					$singleResult->user->profile_image_url . "' alt='perfil'/> " .
					$singleResult->user->screen_name ."</a></td>
					<td>" . implode(' ', $tokens) . "</td>
				</tr>";
				
				$most_common = most_common($most_common, $tokens);
				
				array_push($users, $singleResult->user->screen_name);
				$most_common_user = most_common_user($most_common_user, $users);
			}
			echo "</tbody></table></div><br/><hr><br/>";
			
			arsort($most_common);
			echo "<h3>Palavras mais citadas:</h3>";
			echo "<ul>";
			foreach ($most_common as $key => $val) {
				echo "<li>$key = $val</li>";
			}
			echo "</ul>";
			
			arsort($most_common_user);
			echo "<h3>Usu&aacute;rios que mais escreveram:</h3>";
			echo "<ul>";
			foreach ($most_common_user as $key => $val) {
				echo "<li>$key = $val</li>";
			}
			echo "</ul>";
		}
		
		$list = array("é", "e", "te", "me", "um", "uma", "no", "RT", "a");
		
		echo "<h2>Lava Jato</h2>";
		$result = search("Lava jato", "5", "popular");
		pprint($result, "tabela1", $list);
		
		echo "<h2>#ForaPT</h2>";
		$result2 = search("#forapt", "5", "recent");
		pprint($result2, "tabela2", $list);
		
		echo "<h2>#E32015 Batman</h2>";
		$result3 = search("#e32015 batman", "5", "recent");
		pprint($result3, "tabela3", $list);
	?>
	</body>
</html>