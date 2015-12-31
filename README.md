# Apelon OpenHIE-FHIR Sync

This tool was developed to populate and synchronize OpenHIE components with Terminology Data from an Apelon DTS FHIR Server. The app was written in PHP using Composer for builds, and Bootstrap as a frontend. This software populates iHRIS and Resource Map initial data from either the: web interface, command-line or a REST API.

## Install
To deploy the tool, simply pull this repository into the Apache web directory.
```
git clone git@github.com:vaskaloidis/apelon-ohie-fhir-sync.git
```
Next, you must install the Sync-Tool's PSR-4 libraries and third party libraries using composer. The libraries needed are configured in composer.json. Execute this command in the app's base directory to install the necessary libraries in the generated vendor folder. If the folder is already generated and composer.lock is already present, then execute the update command rather than the following install command..
```
composer.phar install
```

Finally, configure the config.values.php file in the etc/ folder.

## Configure
To use the tool, you must first configure the following values in config.values.php file in the etc/ folder.
```
$apelon_sync_users = array( "UserName" => "md5-encrypted-password"); //Google md5 generator to encrypt a password for app login
$mysql_server, $mysql__user, $mysql_password and $mysql__database //iHRIS Backend MySQL Database
$fhir_server_url, $fhir_server_username, $fhir_server_password; //FHIR Credentials
$rmap_username, $rmap_password, $rmap_server; //Resource Map API Credentials for local or cloud-hosted. Verify user permissions on RS.
$site_url = 'http://sync.openhie.apelon.com'; //Location of the sync webapp itself

//FHIR Default Valuesets. If a valueset is entered in textbox on frontend then that takes priority
$fhir_facility_type_valueset = 'FHIR-Facility-Type-Valueset-Name'; //Used for iHIRS and Resource Map
$fhir_valueset_position = 'FHIR-Valueset-Name-Position-Types';
$fhir_valueset_Countries, $fhir_valueset_countries, $fhir_valueset_districts; //Set Default Geographical Valuesets for iHRIS

// Resource Map Config
// Populate these Values from this JSON data (replace 1666 with Collection ID) 
// http://resourcemap.instedd.org/api/collections/1666/layers.json
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

//Add each rmFacilityTypeConfig() object to this array with the corresponding key for the frontend HTML section
$rmap_layer_configs = array(
    'bali' => $bali_config,
    'manilla' => $manilla_config
);
```

## REST
Rest Usage needs to be configured

## Command-Line Interface
CLI Needs to be configured

# Developers
The OHIE-FHIR Sync tool library was developed to run with any app. The GUI class helps generate the frontend, but the Data Access Objects have been abstracted in the lib/DAO/ folder. To use the iHRIS-FHIR Sync class and Resource Map-IHRIS Sync class, you must require the PSR-4 dependencies configured in composer.json and kept in /lib/. Like So:

Get the libraries.
```
use Apelon\Sync\ihrisSync,
        Apelon\Sync\rmapSync,
        Apelon\Object\rmFacilityTypeConfig,
        Apelon\Util\Gui;
```

### Interface
A GUI library has been created to build the frontend using Bootstrap objects. You can call it like this. Views are stored in the views/ folder and must be manually added to the  loadView() method, but here are a few included by default.
```
$url = $site_url . "index.php";
$gui = new Gui($url);
$gui->loadView('head');
$gui->loadView('navbar')
$gui->alertSuccess('This is a success message');
$gui->alertDanger("This is not succesful");
```

Examples for frontend-sync-form sections can be found in view/ihris-sections.php and view/resource-map-sections.php. These sections should be generated from a function in the GUI class eventually.


### Resource Map-FHIR Sync Library
You must first authenticate yourself against the Resource-Map server and FHIR Server.
```
$rms = new rmapSync();
$rms->setRmapServer($rmap_server, $rmap_username, $rmap_password);
$rms->setFhirServer($fhir_server_url, $fhir_server_username, $fhir_server_password);
```
You must configure a ResourceMap Object before the sync. You can find some of the data here 
```
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
```


### iHRS-FHIR Sync Library
iHRIS-FHIR Sync tool

Construct ihris-sync tool, set the iHRIS authentication credentials for the MySQL Backend and the FHIR Server credentials.
```
$ihris_manage = new ihrisSync();
$ihris_manage->setMysqlConnection("server.ct.apelon.com", "mysql-user", "mysql-password", "ihris_mysql_db");
$ihris_manage->setFhirServer("http://dts-server.com:8081/dtsserverws/fhir/", "dts-username", "dts-password");
```

iHRIS data and the recommended FHIR value-set:
```
 - Facility Types (valueset-c80-facilitycodes)
 - Position Types (HeathCareWorkerTypes)
 - Country (Countries) 
 - Region (These are not value-sets, these have been hard-coded: North, East, South, West)
 - County (UsStatesNorth, UsStatesEast, UsStatesSouth, UsStatesWest)
 - District
``

Format: insertTable(Value-Set-Name) and dropTableName()
Geography Sync
```
$ihris_manager->dropCountry();
$ihris_manager->insertCountry("Valueset-Name");
$ihris-manage->insertRegions("North/East/South/West", "Country|#id");
$ihris_manage->insertDistricts("Valueset-Name", "Region|#id");
$ihris_manage->insertCounty("Valueset-Name", "County|#id");
```

Facility-Type and Position-Type Sync
```
$ihris_manager->insertFacility("valueset-name");
$ihris_manager->insertPosition("valueset-name");
```		

Purge Data
```
$ihris_manager->dropCountries()
$ihris_manager->dropCounties()
$ihris_manager->dropDistricts()
$ihris_manager->dropRegions()
$ihris_manager->dropFacilities()
$ihris_manager->dropPositions()
```

Fetch Data
```
$positions = $ihris_manager->fetchFacilities();
$positions = $ihris_manager->fetchPositions();
$countries = $ihris_manager->fetchCountries();
$regions = $ihris_manager->fetchRegions();
$districts = $ihris_manager->fetchDistricts();
$counties = $ihris_manager->fetchCounties();
```

### Resource Map FHIR Sync Library

