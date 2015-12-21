# iHRIS FHIR Data Sync

This project was created to parse FHIR Terminology data, and insert it into the MySQL database of iHRIS. To use the ihris-fhir data sync, you must set the URL and authentication credentials for the MySQL database, as well as the FHIR server.

Once you are authenticated: for each iHRIS feature listed below, you can drop the corresponding MySQL table data, and re-populate it with FHIR value set data.

First, configure the MySQL database:
```
$ihris_manage = new ihrisSync();
$ihris_manage->setMysqlConnection("server.ct.apelon.com", "mysql-user", "mysql-password", "ihris_mysql_db");
```

Next, configure the FHIR server:
```
$ihris_manage->setFhirServer("http://dts-server.com:8081/dtsserverws/fhir/", "dts-username", "dts-password");
```

iHRIS data that can be synced, and the corresponding FHIR value-set:
 - Facility Types (valueset-c80-facilitycodes)
 - Position Types (HeathCareWorkerTypes)
 - Country 
 - County
 - District
 - Region

** The value-sets for the last 4 still need to be created.

# Sync IHRIS Data
Format: insertTable(Value-Set-Name) and dropTableName()
Country Sync
```
$ihris_manager->dropCountry();
$ihris_manager->insertCountry("Country-Code-Value-Set"); //Update Value Set Name
```

Facility Sync
```
$ihris_manager->dropFacility();
$ihris_manager->insertFacility("valueset-c80-facilitycodes");
```
HealthCare Worker Type Sync
```
$ihris_manager->dropPosition();
$ihris_manager->insertPosition("HeathCareWorkerTypes");
```		
Coming-Soon: County, Region, District (need FHIR Value-Sets created)

## Purge Data
```
dropCountry()
dropCounty()
dropDistrict()
dropRegion()
dropFacility()
dropPosition()
```

## Insert Data
To populate an iHRIS table with Terminology data, you must call one of the following methods and pass in the corresponding FHIR value-set that you want to use to populate the table.
```
insertCountry('value-set-identifier')
insertCounty('value-set-identifier')
insertDistrict('value-set-identifier')
insertegion('value-set-identifier')
insertFacility('value-set-identifier')
insertPosition('value-set-identifier')