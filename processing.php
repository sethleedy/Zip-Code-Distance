<?php

	//var_dump($_REQUEST);

	// Connect to DB
	include("db.php");

	// Set JSON as the file type
	header('Content-type:application/json;charset=utf-8');
	
	$JArray["error"]=false;
	$JErrArray["error"]=true;

	// Check summitted data
	//if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_REQUEST["zipCodeTarget"])) {
	if (isset($_REQUEST["zipCodeTarget"])) {
		
		// Sanitize
		$JArray["targetZipCode"] = sanitize($_REQUEST["zipCodeTarget"]);
		
		// Check for zip code format
		//if ( (preg_match ('/^[0-9]{5}$/', $JArray["targetZipCode"])) || (preg_match ('/^([0-9]{5})-([0-9]{4})$/', $JArray["targetZipCode"])) ) {
		if ( (preg_match ('/^[0-9]{5}$/', $JArray["targetZipCode"])) ) { // 5 digits only
			
			// SQL Queries
			
			// Get the origination latitude and longitude:
			$q = "SELECT latitude, longitude FROM zip_codes WHERE zip_code='".$JArray["targetZipCode"]."' AND latitude IS NOT NULL";
			
			// Connect up
			connectLink($dbDetails);
			
			// Get rows from the DB
			try {
				// Prepare and execute the SQL for submitted Zip
				$statement = $link->prepare($q);
				$statement->execute();
				$result = $statement->fetchAll(PDO::FETCH_BOTH); // Array
				//var_dump($result);
				$lat_a="";
				$long_a="";
				
				list($lat_a, $long_a) = $result[0];
				
				// Find Distance SQL
				$sqlGetdistance="SELECT name, CONCAT_WS('<br />', address1, address2), city, state, stores.zip_code, phone, Concat('About this far away: ', ROUND(return_distance(".$lat_a.", ".$long_a.", latitude, longitude)), ' miles.') AS distance FROM stores LEFT JOIN zip_codes USING (zip_code) ORDER BY distance ASC LIMIT 3";
				
				
				$statement = $link->prepare($sqlGetdistance);
				$statement->execute();
				$result = $statement->fetchAll(PDO::FETCH_BOTH); // Array
				//var_dump($result);
				$rowCode="";

				// Use results
				if ($statement->rowCount() > 0) {
					// Append to Array
					$JArray["addresses"]=$result;
					
				}

				// Close Connection
				$statement=null;
				closeLink();

			} catch (PDOException $e) {

				if ($i == 1) {
					// First time ? retry
					connectLink();
				} else {
					// Second time, KO
					$statement = "(unknown)";
					echo 'PDO Connection failed: ' . $e->getMessage().'. ';
				}
			}
			
			
			// Echo out the JSON	
			echo json_encode($JArray);
			
		} else {
			$JErrArray["errorMessage"]="Invalid Zip Code Format.";
			echo json_encode($JErrArray);
		}		
		
		
	} else {
		$JErrArray["errorMessage"]="Something was amiss in the submission";
		echo json_encode($JErrArray);
	}


	function cleanInput($input) {

	  $search = array(
		'@<script[^>]*?>.*?</script>@si', 	// Strip out javascript 
		'@<[\/\!]*?[^<>]*?>@si', 			// Strip out HTML tags 
		'@<style[^>]*?>.*?</style>@siU', 	// Strip style tags properly
		'@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
	  );

		$output = preg_replace($search, '', $input);
		
		$output = trim($output);
		$output = stripslashes($output);
		$output = htmlspecialchars($output);
		
		return $output;
	}
	
	// Clean DB Input with this function. Add to the function over time.
	function sanitize($input) {
		if (is_array($input)) {
			foreach($input as $var=>$val) {
				$output[$var] = sanitize($val);
			}
		}
		else {
			if (get_magic_quotes_gpc()) {
				$input = stripslashes($input);
			}
			
			$output  = cleanInput($input);
			//$output = mysql_real_escape_string($input); // mysql_real_escape_string - This extension was deprecated in PHP 5.5.0, and it was removed in PHP 7.0.0
		}
		
		return $output;
	}
?>
