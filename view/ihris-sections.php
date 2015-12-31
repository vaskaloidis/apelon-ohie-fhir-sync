<?php
global $gui, $fhir_valueset_facilities, $fhir_valueset_positions, $fhir_valueset_countries, $countries, $regions, $districts, $Counties;
// FACILITIES
echo '<div class="alert alert-warning" role="alert">iHRIS - Facilities</div>';
		$gui->row();
			$gui->div(3); $gui->formStart(); $gui->button('Browse iHRIS Facilities', 'browseFacilities', 'primary'); $gui->formFinish(); $gui->div_end();
			$gui->div(3); $gui->formStart(); $gui->button('Drop Facilities', 'dropFacilities', 'danger'); $gui->formFinish(); $gui->div_end();
			$gui->formStart('inline'); 
				$gui->div(3); $gui->button('Populate Facilities', 'syncFacilities', 'success'); $gui->div_end();
				$gui->div(3); $gui->valuesetTextbox($fhir_valueset_facilities); $gui->div_end();
			$gui->formFinish(); 
		$gui->div_end();
echo '<br>';

// HEALTHCARE-WORKER TYPE
echo '<div class="alert alert-warning" role="alert"iHRIS - >Healthcare-Worker Types</div>';$gui->formFinish();
	$gui->row();
		$gui->div(3); $gui->formStart(); $gui->button('Browse iHRIS Positions', 'browsePositions', 'primary');$gui->formFinish(); $gui->div_end();
		$gui->div(3); $gui->formStart(); $gui->button('Drop Positions', 'dropPositions', 'danger'); $gui->formFinish(); $gui->div_end();
		$gui->formStart('inline'); 
			$gui->div(3); $gui->button('Populate Positions', 'syncPositions', 'success'); $gui->div_end();
			$gui->div(3); $gui->valuesetTextbox($fhir_valueset_positions); $gui->div_end();
		$gui->formFinish();
	$gui->div_end();
echo '<hr>';

//IHRIS-GEOGRAPHIC LOCATIONS
// COUNTRIES
echo '<div class="alert alert-warning" role="alert">iHRIS - Countries</div>';
	row();
		$gui->div(3); $gui->formStart(); $gui->button('Browse iHRIS Countries', 'browseCountries', 'primary'); $gui->formFinish(); $gui->div_end();
		$gui->div(3); $gui->formStart(); $gui->button('Drop Countries', 'dropCountries', 'danger'); $gui->formFinish(); $gui->div_end();
		formStart('inline');
			if($countries) { $countryDisabled = true; } else { $countryDisabled = false; }
			$gui->div(3); $gui->button('Populate Countries', 'syncCountries', 'success', $countryDisabled); $gui->div_end();
			$gui->div(3); $gui->valuesetTextbox($fhir_valueset_countries); $gui->div_end();
		$gui->formFinish();
	$gui->div_end();
echo '<br>';

// REGIONS
if($countries){
	echo '<div class="alert alert-warning" role="alert">iHRIS - Regions</div>';
	$gui->row();
		$gui->row();
			$gui->div(3); $gui->formStart(); $gui->button('Browse iHRIS Regions', 'browseRegions', 'primary');$gui->formFinish(); $gui->div_end();
			$gui->div(3); $gui->formStart(); $gui->button('Drop Regions', 'dropRegions', 'danger'); $gui->formFinish(); $gui->div_end();
			if($regions) {
				//div(3); formStart(); echo 'Loaded Country Regions'; parentValuesetDropdown($regions); formFinish(); div_end();
			}
			
		$gui->div_end();
		echo '<hr>';
		$gui->row();
			$gui->formStart('inline');
				$gui->div(2); $gui->button('Populate Regions', 'syncRegions', 'success'); $gui->div_end();
				$gui->div(2); $gui->regionsDropdown(); $gui->div_end();
				$gui->div(3); $gui->parentValuesetDropdown($countries); $gui->div_end();
			$gui->formFinish();
		$gui->div_end();
	$gui->div_end();
	echo '<br>';
	
	//DISTRICTS
	if($regions) {
		echo '<div class="alert alert-warning" role="alert">iHRIS - Districts</div>';
		$gui->row();
			$gui->row();
				$gui->div(3); $gui->formStart(); $gui->button('Browse iHRIS Districts', 'browseDistricts', 'primary'); $gui->formFinish(); $gui->div_end();
				$gui->div(3); formStart(); $gui->button('Drop Districts', 'dropDistricts', 'danger'); $gui->formFinish(); $gui->div_end();
			$gui->div_end();
			echo '<hr>';
			$gui->row();
				$gui->ormStart('inline');
					$gui->div(3); $gui->button('Populate Districts', 'syncDistricts', 'success'); $gui->div_end();
					$gui->div(3); $gui->valuesetTextbox($fhir_valueset_districts); $gui->div_end();
					$gui->div(3); $gui->parentValuesetDropdown($regions); $gui->div_end();
				$gui->formFinish();
			$gui->div_end();
		$gui->div_end();
		echo '<br>';
		
		//COUNTIES
		if($districts) {
			echo '<div class="alert alert-warning" role="alert">iHRIS - Counties</div>';
			$gui->row();
				$gui->row();
					$gui->div(3);$gui-> formStart();$gui->button('Browse iHRIS Counties', 'browseCounties', 'primary'); $gui->formFinish(); $gui->div_end();
					$gui->div(3); $gui->formStart(); $gui->utton('Drop Counties', 'dropCounties', 'danger'); $gui->formFinish(); $gui->div_end();
				$gui->div_end();
				echo '<hr>';
				$gui->row();
					$gui->formStart('inline');
						$gui->div(3); $gui->button('Populate Counties', 'syncCounties', 'success'); $gui->div_end();
						$gui->div(3); $gui->valuesetTextbox($fhir_valueset_counties); $gui->div_end();
						$gui->div(3); $gui->parentValuesetDropdown($districts); $gui->div_end();
					$gui->formFinish();
				$gui->div_end();
			$gui->div_end();
			echo '<br><br>';
		} else {
			$gui->alertSuccess("Please populate Districts, before you can populate Countries");
		}
	} else {
		$gui->alertSuccess("Please populate Regions, before you can populate Districts");
	}
} else {
	$gui->alertSuccess("Please populate Countries, before you can populate Regions");
}