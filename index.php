<?php

use Apelon\Sync\ihrisSync,
	Apelon\Sync\rmapSync,
	Apelon\Object\rmFacilityTypeConfig;

require __DIR__ . '/vendor/autoload.php';


//Dependencies
require './etc/config.values.php'; //CONFIG

//Sync-Tool URL
$url = $site_url . "index.php";

//Config Value Checks
if(!isset($fhir_valueset_countries) 
		|| !isset($fhir_valueset_positions) 
		|| !isset($fhir_valueset_facilities)
		|| !isset($fhir_valueset_counties)
		|| !isset($fhir_valueset_regions)
		|| !isset($fhir_valueset_districts)
		|| !isset($fhir_valueset_countries)) {
	alertDanger("The Default ValueSet was not set in config.values.php");
} 

$is = new ihrisSync();
//$mysql_auth = parse_url($i2ce_site_dsn); //USE THIS IF YOUR USING THE IHRIS CONFIG FILE
//$mysql_check = $is->setMysqlConnection($mysql_auth['host'], $mysql_auth['user'], $mysql_auth['pass'], substr($mysql_auth['path'], 1));
$mysql_check = $is->setMysqlConnection($mysql_server, $mysql_user, $mysql_password, $mysql_database);
$fhir_check = $is->setFhirServer($fhir_server_url, $fhir_server_username, $fhir_server_password);

// if(!$fhir_check) { //TODO: Finish this config
// 	alertDanger("FHIR Connection failed. Please verify credentials in config.values.php");
// }
if($mysql_check==false) {
	alertDanger("MySQL Connection failed. Please verify credentials in config.values.php");
}

//HTML Helper Functions
function button($text, $action, $level = danger, $disabled = false) {
	echo '<input type="hidden" id="action" name="action" value="' . $action . '" />';
	echo '<input type="hidden" id="pageTitle" name="pageTitle" value="' . $text . '" />';
	if($disabled) { $disabledButton = 'disabled="disabled"'; } else { $disabledButton = ''; }
	echo '<input type="submit" ' . $disabled . ' class="btn btn-' . $level . '" value="' . $text . '" /> ';
}
function alert($text) {
	echo '<div class="alert alert-warning" role="alert">' . $text . '</div>';
}
function alertSuccess($text) {
	echo '<div class="alert alert-warning" role="success">' . $text . '</div>';
}
function alertDanger($text) {
	echo '<div class="alert alert-danger" role="alert">' . $text . '</div>';
}
function rmapCollectionData($config_id) {
	echo '<input type="hidden" name="rmap" value="TRUE" />';
	echo '<input type="hidden" name="rmap_config_id" value="' . $config_id . '" />';
	
}
function valuesetTextbox($valueSet) {
	//<input type="hidden" name="valuesetType" value="' . $name . '" /> //removed $name from param
	echo '<input type="hidden" name="defaultValueset" value="' . $valueSet . '" />';
	echo '<input id="textinput" name="valueset" type="text" placeholder="Default Value-Set: ' . $valueSet . '" class="form-control input-md">';
}
function regionsDropdown() {
	echo '<input type="hidden" name="defaultValueset" value="regions" />';
	echo '<input type="hidden" name="valueset" value="regions" />';
	echo '<select class="form-control input-md" name="regionsDropdown">';
		echo '<option selected value="North">North</option>';
		echo '<option selected value="East">East</option>';
		echo '<option selected value="South">South</option>';
		echo '<option selected value="West">West</option>';
	echo '</select>';
}
function formStart($inline = false) {
	global $url;
	if($inline == 'inline'){
		echo '<form class="form-inline" action="' . $url . '" method="post">';
	} else {
		echo '<form class="form-group" action="' . $url . '" method="post">';
	}
}
function formFinish() {
	echo '</form>';
}
function parentValuesetDropdown($parents) {
	echo '<select class="form-control input-sm" name="parentDropdown">';
		echo '<option selected value="NONE">Select a Parent</option>';
		foreach($parents as $parent) {
			echo '<option value="' . $parent["id"] . '/' . $parent["name"] . '">' . $parent["name"] . '</option>';
		}
	echo '</select>';
	echo '<br><br>';
}
function row() {
	echo '<div class="row">';
}
function div_end() {
	echo '</div>';
}
function div($x, $padding=false) {
	if($padding) { $pad = 'style="padding-right:30px"'; } else { $pad = ''; }
	echo '<div class="col-xl-' . $x . ' col-md-' . $x . ' col-sm-' . $x . '" ' . $pad . '>';
}
//HTML Start
echo '<html><head>';

//echo '<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>'; //TODO: JQUERY 
echo '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">';
echo '<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet" integrity="sha256-MfvZlkHCEqatNoGiOXveE8FIwMzZg4W85qfrfIFBfYc= sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">';
echo '</head><body>';
echo '<h2>Terminology Asset Management&nbsp;&nbsp;&nbsp; <a href="' . $url . '"><small>(Home) </small></a></h2>';
echo '<hr>';
echo '<div class="container">';
echo '<div class="col-xl-9 col-md-9  col-sm-5">';

if(!isset($_POST['action'])) {
	$countries = $is->fetchCountries();
	$regions = $is->fetchRegions();
	$districts = $is->fetchDistricts();
	$counties = $is->fetchCounties();
	
		// ResourceMap - Manilla
		echo '<h3 class="muted">Resource Map Sync Tools</h3>';
		echo '<div class="alert alert-warning" role="alert">Resource Map - Bali Facility Types</div>';
		row();
			formStart();
				div(5); button('Populate Resource Map Bali Facility Types', 'syncRmapFacilityTypes', 'success'); div_end();
				div(5);
				    $rmap_config_id = 'bali';
					valuesetTextbox($rmap_layer_configs[$rmap_config_id]->getDefaultValuesetName());
					rmapCollectionData($rmap_config_id);
				div_end();
			formFinish(); 
		div_end(); 
		echo '<br><hr>';
		
		// ResourceMap - Manilla
		echo '<div class="alert alert-warning" role="alert">Resource Map - Bali Facility Types</div>';
			row();
				formStart();
					div(5);button('Populate Resource Map Manilla Facility Types', 'syncRmapFacilityTypes', 'success');div_end();
					div(5);
					   $rmap_config_id = 'manilla';
						valuesetTextbox($rmap_layer_configs[$rmap_config_id]->getDefaultValuesetName());
						rmapCollectionData($rmap_config_id);
					div_end();
				formFinish();
			div_end(); 
		echo '<br>';
	
		echo '<h3 class="muted">iHRIS Sync Tools</h3>';
		
		// FACILITIES
		echo '<div class="alert alert-warning" role="alert">iHRIS - Facilities</div>';
				row();
					div(3); formStart(); button('Browse iHRIS Facilities', 'browseFacilities', 'primary');formFinish(); div_end();
					div(3); formStart(); button('Drop Facilities', 'dropFacilities', 'danger');formFinish(); div_end();
					formStart('inline'); 
						div(3); button('Populate Facilities', 'syncFacilities', 'success'); div_end();
						div(3); valuesetTextbox($fhir_valueset_facilities); div_end();
					formFinish(); 
				div_end();
		echo '<br>';
		
		// HEALTHCARE-WORKER TYPE
		echo '<div class="alert alert-warning" role="alert"iHRIS - >Healthcare-Worker Types</div>';formFinish();
			row();
				div(3); formStart(); button('Browse iHRIS Positions', 'browsePositions', 'primary');formFinish(); div_end();
				div(3); formStart(); button('Drop Positions', 'dropPositions', 'danger');formFinish(); div_end();
				formStart('inline'); 
					div(3); button('Populate Positions', 'syncPositions', 'success'); div_end();
					div(3); valuesetTextbox($fhir_valueset_positions); div_end();
				formFinish();
			div_end();
		echo '<hr>';
		
		//IHRIS-GEOGRAPHIC LOCATIONS
		// COUNTRIES
		echo '<div class="alert alert-warning" role="alert">iHRIS - Countries</div>';
			row();
				div(3); formStart(); button('Browse iHRIS Countries', 'browseCountries', 'primary');formFinish(); div_end();
				div(3); formStart(); button('Drop Countries', 'dropCountries', 'danger');formFinish(); div_end();
				formStart('inline');
					if($countries) { $countryDisabled = true; } else { $countryDisabled = false; }
					div(3); button('Populate Countries', 'syncCountries', 'success', $countryDisabled); div_end();
					div(3); valuesetTextbox($fhir_valueset_countries); div_end();
				formFinish();
			div_end();
		echo '<br>';
		
		// REGIONS
		if($countries){
			echo '<div class="alert alert-warning" role="alert">iHRIS - Regions</div>';
			row();
				row();
					div(3); formStart(); button('Browse iHRIS Regions', 'browseRegions', 'primary');formFinish(); div_end();
					div(3); formStart(); button('Drop Regions', 'dropRegions', 'danger');formFinish(); div_end();
					if($regions) {
						//div(3); formStart(); echo 'Loaded Country Regions'; parentValuesetDropdown($regions); formFinish(); div_end();
					}
					
				div_end();
				echo '<hr>';
				row();
					formStart('inline');
						div(2); button('Populate Regions', 'syncRegions', 'success'); div_end();
						div(2); regionsDropdown(); div_end();
						div(3); parentValuesetDropdown($countries); div_end();
						
					formFinish();
				div_end();
			div_end();
			echo '<br>';
			
			//DISTRICTS
			if($regions) {
				echo '<div class="alert alert-warning" role="alert">iHRIS - Districts</div>';
				row();
					row();
						div(3); formStart(); button('Browse iHRIS Districts', 'browseDistricts', 'primary');formFinish(); div_end();
						div(3); formStart(); button('Drop Districts', 'dropDistricts', 'danger');formFinish(); div_end();
					div_end();
					echo '<hr>';
					row();
						formStart('inline');
							div(3); button('Populate Districts', 'syncDistricts', 'success'); div_end();
							div(3); valuesetTextbox($fhir_valueset_districts); div_end();
							div(3); parentValuesetDropdown($regions); div_end();
						formFinish();
					div_end();
				div_end();
				echo '<br>';
				
				//COUNTIES
				if($districts) {
					echo '<div class="alert alert-warning" role="alert">iHRIS - Counties</div>';
					row();
						row();
							div(3); formStart(); button('Browse iHRIS Counties', 'browseCounties', 'primary');formFinish(); div_end();
							div(3); formStart(); button('Drop Counties', 'dropCounties', 'danger');formFinish(); div_end();
						div_end();
						echo '<hr>';
						row();
							formStart('inline');
								div(3); button('Populate Counties', 'syncCounties', 'success'); div_end();
								div(3); valuesetTextbox($fhir_valueset_counties); div_end();
								div(3); parentValuesetDropdown($districts); div_end();
							formFinish();
						div_end();
					div_end();
					echo '<br><br>';
				} else {
					alertSuccess("Please populate Districts, before you can populate Countries");
				}
			} else {
				alertSuccess("Please populate Regions, before you can populate Districts");
			}
		} else {
			alertSuccess("Please populate Countries, before you can populate Regions");
		}
} else if(isset($_POST['action'])) {
	
	$action = $_POST['action'];
	
	if(isset($_POST['valueset'])) {
		if($_POST['valueset'] != "") {
			$valueset = $_POST['valueset'];
		} else {
			$valueset = $_POST['defaultValueset'];
		}
		alert('ValueSet: "' . $valueset . '"<br>');
	} else {
		$valueset = "";
		if(substr($action, 0, 4) == 'sync') {
			alertDanger("Value-Set was Null. Set Valueset in Config or Valueset Text-Box.");
		}
	}
	
	if(isset($_POST['rmap'])){
	    $rmap_config_id = $_POST['rmap_config_id'];
	    
		$rms = new rmapSync();
		$rms->setRmapServer($rmap_server, $rmap_username, $rmap_password);
		$rms->setFhirServer($fhir_server_url, $fhir_server_username, $fhir_server_password);
	}
	
	//alert("ACTION: " . $action); //DEBUG

	if(isset($_POST['pageTitle'])) {
		echo '<h4 class="lead">' . $_POST["pageTitle"] . '</h4><hr>';
	}

	switch($action) {
		//RESOURCE-MAP ACTIONS
		case'syncRmapFacilityTypes':
			alertSuccess("Resource Map Server: " . $rms->getServer());
			$rm_config_object = $rmap_layer_configs[$rmap_config_id];
			$rm_config_object->setValuesetName($valueset);
			$update = $rms->updateRmapLayer($rm_config_object);
			echo 'UPDATE WENT: ' . strval($update);
			if(!$update) {
			    echo $rms->getError();
			}
		break;
		
		//IHRIS ACTIONS
		//BROWSE
		case 'browseCountries':
			browseCountries();
		break;
		case 'browseRegions':
			browseRegions();
		break;
		case 'browseDistricts':
			browseDistricts();
		break;
		case 'browseCounties':
			browseCounties();
		break;
		case 'browsePositions':
			browsePositions();
		break;
		case 'browseFacilities':
			browseFacilities();
		break;
		//DROP
		case 'dropPositions':
			if($is->dropPositions()) {
				alert('Drop Positions');
			} else {
				alertDanger('Drop Positions Failed');
			}
		break;
		case 'dropFacilities':
			if($is->dropFacilities()) {
				alert('Drop Facilities OK!');
			} else {
				alertDanger('Drop Facilities Failed');
			}
		break;
		case 'dropCountries':
			if($is->dropCountries()) {
				alert('Drop Countries OK!');
			} else {
				alertDanger('Drop Countries Failed');
			}
		break;
		case 'dropRegions':
			if($is->dropRegions()) {
				alert('Drop Regions OK!');
			} else {
				alertDanger('Drop Regions Failed');
			}
		break;
		case 'dropDistricts':
			if($is->dropDistricts()) {
				alert('Drop Districts OK!');
			} else {
				alertDanger('Drop Districts Failed');
			}
		break;
		case 'dropCounties':
			if($is->dropCounties()) {
				alert('Drop Counties OK!');
			} else {
				alertDanger('Drop Counties Failed');
			}
		break;
		//SYNC
		case 'syncCountries':
			$insertCountryResult = $is->insertCountry($valueset);
			if($insertCountryResult) {
				if($insertCounrtyResult != 'INVALID-VALUESET') {
					alert('Sync was OK!');
				} else {
					alertDanger('Sync Failed. Valueset "' . $valueset . '" does not exist');
				}
			} else {
				alertDanger('Country Sync Failed');
			}
		break;
		case 'syncRegions':
			if($_POST['regionsDropdown'] == "NONE") {
				alertDanger("Please select a country to sync region value-sets with");
			} else {
				$region = $_POST['regionsDropdown'];
				
				$explode = explode("/", $_POST['parentDropdown']);
				$parent_id = $explode[0]; $parent_name = $explode[1];
				
				alertSuccess("Region: " . $parent_id . " / " . $parent_name);
				$regionInsert = $is->insertRegions($region, $parent_id);
				if($regionInsert) {
					alert('Sync was OK!');
				} else {
					alertDanger('Region Sync Failed');
				}
			}
		break;
		case 'syncDistricts':
			if($_POST['parentDropdown'] == "NONE") {
				alertDanger("Please select a Region to sync District value-sets with");
			} else {
				$explode = explode("/", $_POST['parentDropdown']);
				$id = $explode[0]; $name = $explode[1];
				alertSuccess("District: " . $id . " / " . $name);
				
				if($is->insertDistricts($valueset, $id)) {
					alert('Sync was OK!');
				} else {
					alertDanger('District Sync Failed');
				}
			}
		break;
		case 'syncCounties':
			if($_POST['parentDropdown'] == "NONE") {
				alertDanger("Please select a District to sync County value-sets with");
			} else {
				$explode = explode("/", $_POST['parentDropdown']);
				$id = $explode[0]; $name = $explode[1];
				alertSuccess("County: " . $id . " / " . $name);
				
				if($is->insertDistricts($valueset, $id)) {
					alert('County was OK!');
				} else {
					alertDanger('County Sync Failed');
				}
			}
		break;
		case 'syncFacilities':
			if($is->insertFacility($valueset)) {
				alert('Sync OK!');
			} else {
				alertDanger('Facility Sync Failed');
			}
		break;
		case 'syncPositions':
			if($is->insertPosition($valueset)) {
				alert('Sync OK!');
			} else {
				alerDanger('Position Sync Failed');
			}
		break;
		
		default:
			alertDanger('No vaiable action was set');
		break;
	}
}

//BROWSE
function browsePositions() {
	$table_columns = array(0=>"id",1=>"parent",2=>"i2ce_hidden",3=>"i2ce_hidden",4=>"name");
	startTable($table_columns);
	global $is;
	foreach($is->fetchPositions() as $row) {
		echo '<tr>';
			foreach($table_columns as $tc) {
				echo '<td>' . $row[$tc] . '</td>';
			}
		echo'</tr>';
	}
	echo '<table>';
}

function browseFacilities() {
	$table_columns = array(0=>"id",1=>"parent",2=>"last_modified",3=>"i2ce_hidden",4=>"name");
	startTable($table_columns);
	global $is;
	foreach($is->fetchFacilities() as $row) {
		echo '<tr>';
			foreach($table_columns as $tc) {
				echo '<td>' . $row[$tc] . '</td>';
			}
		echo'</tr>';
	}
	echo '<table>';
}

function browseCountries() {
	$table_columns = array(0=>"id",1=>"parent",2=>"last_modified",3=>"i2ce_hidden",4=>"name", 5=>"alpha_two",6=>"code",7=>"primary",8=>"location");
	startTable($table_columns);
	global $is;
	foreach($is->fetchCountries() as $row) {
		echo '<tr>';
			foreach($table_columns as $tc) {
				echo '<td>' . $row[$tc] . '</td>';
			}
		echo'</tr>';
	}
	
	echo '<table>';
}

function browseRegions() {
	$table_columns = array(0=>"id",1=>"parent",2=>"last_modified",3=>"created",4=>"i2ce_hidden",5=>"remap",6=>"code",7=>"country",8=>"name");
	startTable($table_columns);
	global $is;
	foreach($is->fetchRegions() as $row) {
		echo '<tr>';
		foreach($table_columns as $tc) {
			echo '<td>' . $row[$tc] . '</td>';
		}
		echo'</tr>';
	}

	echo '<table>';
}

function browseDistricts() {
	$table_columns = array(0=>"id",1=>"parent",2=>"last_modified",3=>"created",4=>"i2ce_hidden", 5=>"remap",6=>"district",7=>"name");
	startTable($table_columns);
	global $is;
	foreach($is->fetchDistricts() as $row) {
		echo '<tr>';
		foreach($table_columns as $tc) {
			echo '<td>' . $row[$tc] . '</td>';
		}
		echo'</tr>';
	}

	echo '<table>';
}

function browseCounties() {
	$table_columns = array(0=>"id",1=>"parent",2=>"last_modified",3=>"i2ce_hidden",4=>"name", 5=>"alpha_two",6=>"code",7=>"primary",8=>"location");
	startTable($table_columns);
	global $is;
	foreach($is->fetchCounties() as $row) {
		echo '<tr>';
		foreach($table_columns as $tc) {
			echo '<td>' . $row[$tc] . '</td>';
		}
		echo'</tr>';
	}

	echo '<table>';
}

function startTable($columns) {
	echo '<table class="table table-striped"><tr>';
		for($x = 0; $x < count($columns); $x++) {
			echo '<td>' . $columns[$x] . '</td>';
		}
	echo '</tr>';
}

echo '</div>';

echo '</div></body></html>';

# Local Variables:
# mode: php
# c-default-style: "bsd"
# indent-tabs-mode: nil
# c-basic-offset: 4
# End:

