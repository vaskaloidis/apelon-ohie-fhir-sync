<?php

session_start();

// Dependencies
use Apelon\Sync\ihrisSync,
	Apelon\Sync\rmapSync,
	Apelon\Object\rmFacilityTypeConfig,
	Apelon\Util\Gui;
require __DIR__ . '/vendor/autoload.php';

// Config File
require './etc/config.values.php';
$url = $site_url . "index.php"; // URL

//Config File-Checker
$load_error = false;
if(!isset($fhir_valueset_countries) 
		|| !isset($fhir_valueset_positions) 
		|| !isset($fhir_valueset_facilities)
		|| !isset($fhir_valueset_counties)
		|| !isset($fhir_valueset_regions)
		|| !isset($fhir_valueset_districts)
		|| !isset($fhir_valueset_countries)
		|| count($apelon_sync_users)<1) {
	$load_error = true;
} 

// User Interface
$gui = new Gui($url);

// Authenticated
$logged_in = false;
$apelon_user = false;
if(isset($_SESSION)) {
	if(isset($_SESSION["user"])) {
		if(in_array($_SESSION["user"], $apelon_sync_users)) {
			$logged_in = true;
			$apelon_user = $_SESSION["user"];
		}
	}
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
	$gui->alertDanger("MySQL Connection failed. Please verify credentials in config.values.php");
}

$gui->loadView('head');
$gui->loadView('navbar');

if($logged_in = false) {
	$gui->alertSuccess("Please login");
} else if(!isset($_POST['action'])) {
	//Set Loaded iHRIS Geographic Data
	$countries = $is->fetchCountries();
	$regions = $is->fetchRegions();
	$districts = $is->fetchDistricts();
	$counties = $is->fetchCounties();

	// Resource Map HTML
	$gui->loadView('resource-map-sections');

	//iHRIS HTML
	$gui->loadView('ihris-sections');

} else if(isset($_POST['action'])) {
	
	$action = $_POST['action'];
	
	if(isset($_POST['valueset'])) {
		if($_POST['valueset'] != "") {
			$valueset = $_POST['valueset'];
		} else {
			$valueset = $_POST['defaultValueset'];
		}
		$gui->alert('ValueSet: "' . $valueset . '"<br>');
	} else {
		$valueset = "";
		if(substr($action, 0, 4) == 'sync') {
			$gui->alertDanger("Value-Set was Null. Set Valueset in Config or Valueset Text-Box.");
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
			$gui->browseCountries($is);
		break;
		case 'browseRegions':
			$gui->browseRegions($is);
		break;
		case 'browseDistricts':
			$gui->browseDistricts($is);
		break;
		case 'browseCounties':
			$gui->browseCounties($is);
		break;
		case 'browsePositions':
			$gui->browsePositions($is);
		break;
		case 'browseFacilities':
			$gui->browseFacilities($is);
		break;
		//DROP
		case 'dropPositions':
			if($is->dropPositions()) {
				$gui->alert('Drop Positions');
			} else {
				$gui->alertDanger('Drop Positions Failed');
			}
		break;
		case 'dropFacilities':
			if($is->dropFacilities()) {
				$gui->alert('Drop Facilities OK!');
			} else {
				$gui->alertDanger('Drop Facilities Failed');
			}
		break;
		case 'dropCountries':
			if($is->dropCountries()) {
				$gui->alert('Drop Countries OK!');
			} else {
				$gui->alertDanger('Drop Countries Failed');
			}
		break;
		case 'dropRegions':
			if($is->dropRegions()) {
				$gui->alert('Drop Regions OK!');
			} else {
				$gui->alertDanger('Drop Regions Failed');
			}
		break;
		case 'dropDistricts':
			if($is->dropDistricts()) {
				$gui->alert('Drop Districts OK!');
			} else {
				$gui->alertDanger('Drop Districts Failed');
			}
		break;
		case 'dropCounties':
			if($is->dropCounties()) {
				$gui->alert('Drop Counties OK!');
			} else {
				$gui->alertDanger('Drop Counties Failed');
			}
		break;
		//SYNC
		case 'syncCountries':
			$insertCountryResult = $is->insertCountry($valueset);
			if($insertCountryResult) {
				if($insertCounrtyResult != 'INVALID-VALUESET') {
					$gui->alert('Sync was OK!');
				} else {
					$gui->alertDanger('Sync Failed. Valueset "' . $valueset . '" does not exist');
				}
			} else {
				$gui->alertDanger('Country Sync Failed');
			}
		break;
		case 'syncRegions':
			if($_POST['regionsDropdown'] == "NONE") {
				$gui->alertDanger("Please select a country to sync region value-sets with");
			} else {
				$region = $_POST['regionsDropdown'];
				
				$explode = explode("/", $_POST['parentDropdown']);
				$parent_id = $explode[0]; $parent_name = $explode[1];
				
				$gui->alertSuccess("Region: " . $parent_id . " / " . $parent_name);
				$regionInsert = $is->insertRegions($region, $parent_id);
				if($regionInsert) {
					$gui->alert('Sync was OK!');
				} else {
					$gui->alertDanger('Region Sync Failed');
				}
			}
		break;
		case 'syncDistricts':
			if($_POST['parentDropdown'] == "NONE") {
				$gui->alertDanger("Please select a Region to sync District value-sets with");
			} else {
				$explode = explode("/", $_POST['parentDropdown']);
				$id = $explode[0]; $name = $explode[1];
				$gui->alertSuccess("District: " . $id . " / " . $name);
				
				if($is->insertDistricts($valueset, $id)) {
					$gui->alert('Sync was OK!');
				} else {
					$gui->alertDanger('District Sync Failed');
				}
			}
		break;
		case 'syncCounties':
			if($_POST['parentDropdown'] == "NONE") {
				$gui->alertDanger("Please select a District to sync County value-sets with");
			} else {
				$explode = explode("/", $_POST['parentDropdown']);
				$id = $explode[0]; $name = $explode[1];
				$gui->alertSuccess("County: " . $id . " / " . $name);
				
				if($is->insertDistricts($valueset, $id)) {
					$gui->alert('County was OK!');
				} else {
					$gui->alertDanger('County Sync Failed');
				}
			}
		break;
		case 'syncFacilities':
			if($is->insertFacility($valueset)) {
				$gui->alert('Sync OK!');
			} else {
				$gui->alertDanger('Facility Sync Failed');
			}
		break;
		case 'syncPositions':
			if($is->insertPosition($valueset)) {
				$gui->alert('Sync OK!');
			} else {
				$gui->alerDanger('Position Sync Failed');
			}
		break;
		
		default:
			$gui->alertDanger('No vaiable action was set');
		break;
	}
}

$gui->loadView('foot');

# Local Variables:
# mode: php
# c-default-style: "bsd"
# indent-tabs-mode: nil
# c-basic-offset: 4
# End:

