<?php

namespace Apelon\Util;

require __DIR__ . '/../../vendor/autoload.php';

class Gui {

	public $url;

	public function __construct($url) {
		$this->url = $url;
	}

	public function loadCss($css) {
		echo '<link href="view/css/' . $css . '.css" rel="stylesheet">';
	}

	//Load Views
	public function loadView($view) {
		switch($view){
			case 'resource-map-sections':
				include 'view/resource-map-sections.php';
			break;
			case 'ihris-sections':
				include 'view/ihris-sections.php';
			break;
			case 'head':
				include 'view/head.php';
			break;
			case 'navbar':
				include 'view/navbar.php';
			break;
			case 'foot' :
				include 'view/foot.php';
			break;
			default:
			case 'login':
				include 'view/login.php';
			break;
		}
	}

	//HTML Helper Functions
	public function setAnchor($anchor) {
		echo '<a name="' . $anchor . '"></a>';
	}
	public function button($text, $action, $level = danger, $disabled = false) {
		echo '<input type="hidden" id="action" name="action" value="' . $action . '" />';
		echo '<input type="hidden" id="pageTitle" name="pageTitle" value="' . $text . '" />';
		if($disabled) { $disabledButton = 'disabled="disabled"'; } else { $disabledButton = ''; }
		echo '<input type="submit" ' . $disabled . ' class="btn btn-' . $level . '" value="' . $text . '" /> ';
	}
	public function alert($text) {
		echo '<div class="alert alert-warning" role="alert">' . $text . '</div>';
	}
	public function alertSuccess($text) {
		echo '<div class="alert alert-warning" role="success">' . $text . '</div>';
	}
	public function alertDanger($text) {
		echo '<div class="alert alert-danger" role="alert">' . $text . '</div>';
	}
	public function rmapCollectionData($config_id) {
		echo '<input type="hidden" name="rmap" value="TRUE" />';
		echo '<input type="hidden" name="rmap_config_id" value="' . $config_id . '" />';
		
	}
	public function valuesetTextbox($valueSet) {
		//<input type="hidden" name="valuesetType" value="' . $name . '" /> //removed $name from param
		echo '<input type="hidden" name="defaultValueset" value="' . $valueSet . '" />';
		echo '<input id="textinput" name="valueset" type="text" placeholder="Default Value-Set: ' . $valueSet . '" class="form-control input-md">';
	}public 
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
	public function formStart($inline = false) {
		if($inline == 'inline'){
			echo '<form class="form-inline" action="' . $this->url . '" method="post">';
		} else {
			echo '<form class="form-group" action="' . $this->url . '" method="post">';
		}
	}
	public function formFinish() {
		echo '</form>';
	}
	public function parentValuesetDropdown($parents) {
		echo '<select class="form-control input-sm" name="parentDropdown">';
			echo '<option selected value="NONE">Select a Parent</option>';
			foreach($parents as $parent) {
				echo '<option value="' . $parent["id"] . '/' . $parent["name"] . '">' . $parent["name"] . '</option>';
			}
		echo '</select>';
		echo '<br><br>';
	}
	public function row() {
		echo '<div class="row">';
	}
	public function div_end() {
		echo '</div>';
	}
	public function div($x, $padding=false) {
		if($padding) { $pad = 'style="padding-right:30px"'; } else { $pad = ''; }
		echo '<div class="col-xl-' . $x . ' col-md-' . $x . ' col-sm-' . $x . '" ' . $pad . '>';
	}
	//BROWSE
	public function browsePositions($is) {
		$table_columns = array(0=>"id",1=>"parent",2=>"i2ce_hidden",3=>"i2ce_hidden",4=>"name");
		startTable($table_columns);
		foreach($is->fetchPositions() as $row) {
			echo '<tr>';
				foreach($table_columns as $tc) {
					echo '<td>' . $row[$tc] . '</td>';
				}
			echo'</tr>';
		}
		echo '<table>';
	}

	public function browseFacilities($is) {
		$table_columns = array(0=>"id",1=>"parent",2=>"last_modified",3=>"i2ce_hidden",4=>"name");
		startTable($table_columns);
		foreach($is->fetchFacilities() as $row) {
			echo '<tr>';
				foreach($table_columns as $tc) {
					echo '<td>' . $row[$tc] . '</td>';
				}
			echo'</tr>';
		}
		echo '<table>';
	}

	public function browseCountries($is) {
		$table_columns = array(0=>"id",1=>"parent",2=>"last_modified",3=>"i2ce_hidden",4=>"name", 5=>"alpha_two",6=>"code",7=>"primary",8=>"location");
		startTable($table_columns);
		foreach($is->fetchCountries() as $row) {
			echo '<tr>';
				foreach($table_columns as $tc) {
					echo '<td>' . $row[$tc] . '</td>';
				}
			echo'</tr>';
		}
		
		echo '<table>';
	}

	public function browseRegions($is) {
		$table_columns = array(0=>"id",1=>"parent",2=>"last_modified",3=>"created",4=>"i2ce_hidden",5=>"remap",6=>"code",7=>"country",8=>"name");
		startTable($table_columns);
		foreach($is->fetchRegions() as $row) {
			echo '<tr>';
			foreach($table_columns as $tc) {
				echo '<td>' . $row[$tc] . '</td>';
			}
			echo'</tr>';
		}

		echo '<table>';
	}

	public function browseDistricts($is) {
		$table_columns = array(0=>"id",1=>"parent",2=>"last_modified",3=>"created",4=>"i2ce_hidden", 5=>"remap",6=>"district",7=>"name");
		startTable($table_columns);
		foreach($is->fetchDistricts() as $row) {
			echo '<tr>';
			foreach($table_columns as $tc) {
				echo '<td>' . $row[$tc] . '</td>';
			}
			echo'</tr>';
		}

		echo '<table>';
	}

	public function browseCounties($is) {
		$table_columns = array(0=>"id",1=>"parent",2=>"last_modified",3=>"i2ce_hidden",4=>"name", 5=>"alpha_two",6=>"code",7=>"primary",8=>"location");
		startTable($table_columns);	foreach($is->fetchCounties() as $row) {
			echo '<tr>';
			foreach($table_columns as $tc) {
				echo '<td>' . $row[$tc] . '</td>';
			}
			echo'</tr>';
		}

		echo '<table>';
	}

	public function startTable($columns) {
		echo '<table class="table table-striped"><tr>';
			for($x = 0; $x < count($columns); $x++) {
				echo '<td>' . $columns[$x] . '</td>';
			}
		echo '</tr>';
	}
}