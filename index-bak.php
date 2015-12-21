<?php
require __DIR__ . '/vendor/autoload.php';

use APELON\ihrisFhirSync\ihrisSync;

?>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        echo '<b>IHRIS MySQL Sync</b><br>';
		$ihris_manager = new ihrisSync();
		$ihris_manager->setMysqlConnection("hardevhim.ct.apelon.com", "ihris_manage", "apelon1", "ihris_manage");
		$ihris_manager->setFhirServer("http://40.143.220.156:8081/dtsserverws/fhir/", "dtsadminuser", "dtsadmin");
		
		$ihris_manager->dropCountry();
		$ihris_manager->insertCountry("valueset-c80-facilitycodes"); //Update Value Set Name
		
		$ihris_manager->dropFacility();
		$ihris_manager->insertFacility("valueset-c80-facilitycodes");
		
		$ihris_manager->dropPosition();
		$ihris_manager->insertPosition("HeathCareWorkerTypes");
        ?>
    </body>
</html>
