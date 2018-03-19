<?php 
/**
* Plugin Name: Automatic Home Descriptions
* Plugin URI: http://reapmarketing.com
* Description: this is my plugin
* Author: David Petrey
* Version: 1.0
*/


if ( !defined('ABSPATH') ) {
	// Set up WordPress environment
	require_once('../../../wp-config.php');
}


global $wpdb;     

//$conn = NULL;

//  Finds out what id's need to be processed
	$d = $wpdb->get_results( "SELECT productid FROM homes WHERE populated=' ' AND builder_code !=' ' AND producttype ='Home'", ARRAY_A); 

	$newids = array();

	foreach($d as $ds){
		foreach ($ds as $k) { 
			$newids[] = $k;
		}
	}

if (strpos($_SERVER['REQUEST_URI'], "ahd-main") !== false){ echo '<pre> new id\'s '; print_r($newids); echo '</pre><hr><br>';}

// Random word spinner /////////////////////
	$exclude = array();
	function randWord($ar, $reset) {
		global $exclude;

		if($reset == true){	$exclude = array(); }

		$wrdcount = count($ar);	// $ar[2];	// array_keys($ar, $ar[2])[0]; // array_keys($ar)[1];

	    do { 
	    	$num = rand(0,$wrdcount - 1);
	    	$key = array_keys($ar)[$num];
	    } while(in_array($key, $exclude));

		array_push($exclude, $key);

		if (strpos($_SERVER['REQUEST_URI'], "ahd-main") !== false){	echo '$ar[$key]: '.$ar[$key].'<br>'; }

		return $ar[$key];

	} ///////////////////

// GETS ATTRIBUTES //////////////////
function runfeat ($feat_table, $builder, $fc, $feature_code){
	global $wpdb;
	 
	$feat_choice = array();
	$get_features = $wpdb->get_results( "SELECT Feature_Code, Feature FROM $feat_table WHERE $builder='Y'", ARRAY_A );

	foreach($get_features as $get_feature){
		for($j=0; $j< count($fc); $j++){
			$fcn = $fc[$j];
			if( $get_feature['Feature_Code'] ==  $feature_code.$fcn ){
				array_push($feat_choice, $get_feature['Feature']);
			}
		}
	}
	shuffle($feat_choice);
	if (strpos($_SERVER['REQUEST_URI'], "ahd-main") !== false){echo 'feature: "'.$feat_choice[0].'"<br>';}
	return $feat_choice[0];

} ////////////////////




// main function
function builder_task() {
	global $conn;
	global $newids;
	global $wpdb;


//Create connection
$conn = mysqli_connect('127.0.0.1', 'homestead', 'secret', 'santarita2');
//Check connection
if (!$conn) {
    //if (strpos($_SERVER['REQUEST_URI'], "ahd-main") !== false){     
   		die("Connection failed: " . mysqli_connect_error());
   	//}
} 

		$end=0;
	// CREATES NEW ROWS IN home_descriptions  ///////////  LOOP  /////////
	for($i=0; $i < count($newids); $i++){
		$getbuilder = $wpdb->get_row("SELECT builder_code FROM homes WHERE productid=$newids[$i]", ARRAY_A);
		$builder = $getbuilder['builder_code'];

echo 'buider '. $builder.'<br>';

		$kitchenQ1 = runfeat('feat_kitchen', $builder, array(1,3), 'kitchen-' );
		$kitchenQ2 = runfeat('feat_kitchen', $builder, array(2,4,5), 'kitchen-' );
		$kitchenQ3 = runfeat('feat_kitchen', $builder, array(12,13), 'kitchen-' );
		$kitchenQ4 = runfeat('feat_kitchen', $builder, array(8,9,10,11), 'kitchen-' );

		$bathQ1 = runfeat('feat_bathroom', $builder, array(3,4,5), 'secondary-bath-' );
		$bathQ2 = runfeat('feat_bathroom', $builder, array(1,13), 'secondary-bath-' );
		$bathQ3 = runfeat('feat_bathroom', $builder, array(7,9,10), 'secondary-bath-' );
		$bathQ4 = runfeat('feat_bathroom', $builder, array(1), 'master-bath-' );
		$bathQ5 = runfeat('feat_bathroom', $builder, array(5), 'master-bath-' );
		$bathQ6 = runfeat('feat_bathroom', $builder, array(3,4,10), 'master-bath-' );
		$bathQ7 = runfeat('feat_bathroom', $builder, array(6,8,9), 'master-bath-' );

		$interiorQ1 = runfeat('feat_interior', $builder, array(1,2,3), 'flooring-type' );

		$exteriorQ1 = runfeat('feat_exterior', $builder, array(12,13), 'exterior-details-' );
		$exteriorQ2 = runfeat('feat_exterior', $builder, array(1,2,3), 'frontdoor-' );
		$exteriorQ3 = runfeat('feat_exterior', $builder, array(1,2,3), 'garage-details-' );
		$exteriorQ4 = runfeat('feat_exterior', $builder, array(4,7,10), 'exterior-details-' );
		$exteriorQ5 = runfeat('feat_exterior', $builder, array(2,3), 'yard-feature-' );
		$exteriorQ6 = runfeat('feat_exterior', $builder, array(1,2,3), 'windows-material-' );

		$energyQ1 = runfeat('feat_energy', $builder, array(1,2), 'energy-efficiency-' );

		$sp1 = array(	1 => 'gorgeous', 
				2 => 'stunning', 
				3 => 'beautiful', 
				4 => 'charming', 
				5 => 'exquisite', 
				6 => 'elegant', 
				7 => 'lovely', 
				8 => 'enchanting');

		$sp2 = array(	9 => 'home',
				10 => 'house');

		$sp3 = array(	11 => 'spacious', 
				12 => 'roomy', 
				13 => 'comfortable');

		$sp4 = array(	14 => 'other', 
				15 => 'children\'s', 
				16 => 'guest');

		$sp5 = array(	1 => 'gorgeous', 
				2 => 'stunning', 
				3 => 'beautiful', 
				4 => 'charming', 
				17 => 'striking', 
				7 => 'lovely');

		$sp6 = array(	18 => 'welcoming', 
				19 => 'relaxing', 
				4 => 'charming', 
				20 => 'delightful');

		$spin1 = randWord($sp1, true);
		$spin2 = randWord($sp2, false);
		$spin3 = randWord($sp3, false);
		$spin4 = randWord($sp4, false);
		$spin5 = randWord($sp5, false);		
		$spin6 = randWord($sp6, false);		

		echo '$newids[$i]     '.$newids[$i];

	$wpdb->insert('home_descriptions', array('productid' => $newids[$i] ));

	// WRITE TO home_descriptions row  
	$sql = "UPDATE home_descriptions SET
		spin1='$spin1', spin2='$spin2', spin3='$spin3', 
		spin4='$spin4', 
		spin5='$spin5', 
		spin6='$spin6', 
		kitchen1='$kitchenQ1', 
		kitchen2='$kitchenQ2', 
		kitchen3='$kitchenQ3', 
		kitchen4='$kitchenQ4', 
		bath1='$bathQ1', bath2='$bathQ2', bath3='$bathQ3', bath4='$bathQ4', bath5='$bathQ5', bath6='$bathQ6', bath7='$bathQ7',
		interior1='$interiorQ1',
		exterior1='$exteriorQ1', exterior2='$exteriorQ2', exterior3='$exteriorQ3', exterior4='$exteriorQ4', exterior5='$exteriorQ5', exterior6='$exteriorQ6',
		energy1='$energyQ1' WHERE productid = $newids[$i]";

		if (mysqli_query($conn, $sql)) {
			$wpdb->update("homes", array("populated" => 'true'), array( 'productid' => $newids[$i] ));
	//	    if (strpos($_SERVER['REQUEST_URI'], "ahd-main") !== false){echo "<br>Record updated successfully<br><hr><br>";}
			echo "<br>Record updated successfully<br><hr><br>";
			$result = true;
		} else {
	//	    if (strpos($_SERVER['REQUEST_URI'], "ahd-main") !== false){echo "<br>Err updating record: " . mysqli_error($conn).'<br><hr><br>';}
			echo "<br>Err updating record: " . mysqli_error($conn).'<br><hr><br>';
			$result = false;

		}
	}//for

	//if (strpos($_SERVER['REQUEST_URI'], "ahd-main") !== false){ echo 'close conneciton'; }
	mysqli_close($conn);
	
}//builder_task



if(count($newids) > 0){
	builder_task();
}else{
	//if (strpos($_SERVER['REQUEST_URI'], "ahd-main") !== false){ echo '<h1>no new id\'s</h1><br>'; }
	echo '<h1>no new id\'s</h1><br>';
}


?>
