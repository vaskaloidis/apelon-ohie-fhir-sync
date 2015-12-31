<?php
global $gui, $rmap_layer_configs; 
// ResourceMap - Manilla
echo '<h3 class="muted">Resource Map Sync Tools</h3>';
echo '<div class="alert alert-warning" role="alert">Resource Map - Bali Facility Types</div>';
$gui->row();
	$gui->formStart();
		$gui->div(5); $gui->button('Populate Resource Map Bali Facility Types', 'syncRmapFacilityTypes', 'success'); $gui->div_end();
		$gui->div(5);
		    $rmap_config_id = 'bali';
			$gui->valuesetTextbox($rmap_layer_configs[$rmap_config_id]->getDefaultValuesetName());
			$gui->rmapCollectionData($rmap_config_id);
		$gui->div_end();
	$gui->formFinish(); 
$gui->div_end(); 
echo '<br><hr>';

// ResourceMap - Manilla
echo '<div class="alert alert-warning" role="alert">Resource Map - Bali Facility Types</div>';
	$gui->row();
		$gui->formStart();
			$gui->div(5); $gui->button('Populate Resource Map Manilla Facility Types', 'syncRmapFacilityTypes', 'success'); $gui->div_end();
			$gui->div(5);
			   $rmap_config_id = 'manilla';
				$gui->valuesetTextbox($rmap_layer_configs[$rmap_config_id]->getDefaultValuesetName());
				$gui->rmapCollectionData($rmap_config_id);
			$gui->div_end();
		$gui->formFinish();
	$gui->div_end(); 
echo '<br>';

echo '<h3 class="muted">iHRIS Sync Tools</h3>';