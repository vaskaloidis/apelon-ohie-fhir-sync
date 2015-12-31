<?php
//APELON-OHIE SYNC

namespace Apelon\Config;
use Apelon\Object\rmFacilityTypeConfig;
require __DIR__ . '/../vendor/autoload.php';

$apelon_sync_users = array(
	"vas" => "b09ca716f24a18d5217663ebea8930e4"
	);

//MYSQL AUTH
$mysql_server = 'localhost';
$mysql_user = 'ihris_manage';
$mysql_password = 'apelon1';
$mysql_database = 'ihris_manage';
//FHIR AUTH
$fhir_server_url = 'http://40.143.220.156:8081/dtsserverws/fhir/';
$fhir_server_username = 'dtsadminuser';
$fhir_server_password = 'dtsadmin';
//RESOURCE-MAP AUTH
$rmap_username = "vkaloidis@apelon.com";
$rmap_password = "apelon123";
$rmap_server = "http://resourcemap.instedd.org/";


//FHIR VALUE-SETS
$fhir_facility_type_valueset = 'valueset-c80-facilitycodes';
//SITE URL
$site_url = 'http://hardevhim.ct.apelon.com:83/ohie-fhir-sync/';

//IHRIS
$fhir_valueset_facilities = $fhir_facility_type_valueset;
$fhir_valueset_positions = 'HeathCareWorkerTypes';
$fhir_valueset_countries = 'Countries';
$fhir_valueset_counties = 'Countries';
$fhir_valueset_regions = 'Countries';
$fhir_valueset_districts = 'Countries';

//RESOURCE-MAP

$bali_config = new rmFacilityTypeConfig();
$bali_config->setCollectionId("1667");
$bali_config->setLayerId("1669");
$bali_config->setLayerName("Medical Facility Information");
$bali_config->setLayerOrder("2");
$bali_config->setDefaultValuesetName($fhir_facility_type_valueset);
$bali_config->setFieldName("Facility Type");
$bali_config->setFieldCode("facility_type");
$bali_config->setFieldOrder("1");
$bali_config->setFieldId("14371");
$bali_config->setNextId("2");

$manilla_config = new rmFacilityTypeConfig();
$manilla_config->setCollectionId("1666");
$manilla_config->setLayerId("1670");
$manilla_config->setLayerName("Medical Facility Information");
$manilla_config->setLayerOrder("2");
$manilla_config->setDefaultValuesetName("valueset-c80-facilitycodes");
$manilla_config->setFieldName("Facility Type");
$manilla_config->setFieldCode("facility_type");
$manilla_config->setFieldOrder("1");
$manilla_config->setFieldId("14368");
$manilla_config->setNextId("2");

$rmap_layer_configs = array(
    'bali' => $bali_config,
    'manilla' => $manilla_config
);

/**
 * Initialization string for user access.  See http://open.intrahealth.org/mediawiki/Pluggable_Authentication
 *  
 */
$i2ce_site_user_access_init = null;



/**
 * the configuration xml file for the site module.  You need to set this.
 */
$i2ce_site_module_config = "/var/lib/iHRIS/sites/manage/iHRIS-Manage-BLANK.xml";

echo '<script type="text/javascript">';
	echo "var site_url = '" . $site_url . "';";
echo '</script>';

/*****************************************************************
 *                                                               *
 *                   END USER CUSTOMIZATION                      *
 *              Do not edit anything below this line             *
 *                                                               *
 *****************************************************************/






# Local Variables:
# mode: php
# c-default-style: "bsd"
# indent-tabs-mode: nil
# c-basic-offset: 4
# End:
