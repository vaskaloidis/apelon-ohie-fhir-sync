<?php

namespace Apelon\Sync;

require __DIR__ . '/../../vendor/autoload.php';

/**
 * Description of ihrisFhirDAO
 *
 * @author vasili
 */
class ihrisSync {
    
    private $conn, $dtsServer, $dtsUser, $dtsPassword;
    
    public function __construct() {
		//Construct
    }
    
    
    /**
     * Set the iHRIS MySql backend URL, Username, Password and the databsae 
     * iHRIS is currently using
     * 
     * @param type $url MySQL Server URL
     * @param type $user MySQL Username
     * @param type $password MySQL Password
     * @param type $db MySQL Database Name
     */
    public function setMysqlConnection($url, $user, $password, $db) {
    	
        $this->conn = mysqli_connect($url, $user, $password, $db);
        
        if(!$this->conn) {
		return false;
        } else {
        	return  true;
        }
        //test
    }
    
    /**
     * Set DTS FHIRE Server Info
     * 
     * @param type $url URL of the DTS FHIR Server
     * @param type $username DTS Server Username
     * @param type $password DTS Server Password
     */
    public function setFhirServer($url, $username, $password) {
        $this->dtsServer = $url;
        $this->dtsUser = $username;
        $this->dtsPassword = $password;
        
        //TODO: Add FHIR Credential Check here, return true or false
    }
    
    /**
     * Returns an array of parsed FHIR Data from the value-set passed-in
     * @param type $valueSet DTS FHIR Value-Set name to retreive
     */
    public function getFhirData($valueSet) {
        $url = $this->dtsServer . "ValueSet/" . $valueSet . "/$" . "expand";
        $context = stream_context_create(array(
        'http' => array(
            'header' => "Authorization: Basic " . base64_encode($this->dtsUser . ":" . $this->dtsPassword) . "\r\n"
                )
            )
        );
        try {
        	$xml = file_get_contents($url, false, $context);
        } catch(Exception $e) {
        	return 'INVALID-VALUESET';
        }
        
        if($xml != null && $xml) {
        	$xml = simplexml_load_string($xml);
        	//var_dump($xml->expansion); //DEBUG
        	return $xml;
        } else {
        	return false;		
        }
    }
    
    public function nextId($prefix, $table) {
    	$count = strlen($prefix);
    	$base_sql = "SELECT * FROM `" . $table . "`";
    	$base_query = mysqli_query($this->conn, $base_sql);
    	$row_count = mysqli_num_rows($base_query);
    	 
    	if($row_count == 0) {
    		return mysqli_real_escape_string($this->conn, $prefix . "0");
    	} else {
    		$found = false;
    		while(!$found) {
    		    $next_id = $prefix . $row_count;
    		    $id_check_sql = "SELECT * FROM `" . $table . "` WHERE `id` = '" . $next_id . "'";
    		    $id_check_result = mysqli_query($this->conn, $id_check_sql);
    		    if(mysqli_num_rows($id_check_result) == 0) {
    		        $found = true;
    		    } else {
    		        $row_count++;
    		    }
    		}
    		return  mysqli_real_escape_string($this->conn, $prefix . $row_count);
    		//return
    	}
    	
    	
    	//$next_id_sql = "select max(CAST((substring(id,8)) AS DECIMAL(5,2))) from `hippo_region`;";
    	//$next_id_sql = "SELECT MAX(SUBSTRING(CONVERT(id, SIGNED), " . $count . ")) FROM `" . $table . "`";
    }
    
    //COUNTRIES
    public function dropCountries() {
        $sql = "TRUNCATE table `hippo_country`";
        $query = mysqli_query($this->conn, $sql);
        
        if(!$query) {
        	return false;
        } else {
        	return true;
        }
    }
    private function insertCountryQuery($name, $code) {
        //Country-Code
	$explode = explode(" ", $name);
	$country_only = substr($name, 0, strpos($name, "(")-1);
	if($this->getCountryCode($country_only)) {
		$countryCode = $this->getCountryCode($country_only);	
	}else if(substr($explode[1], 0, 1)=="(") { //Single-Word Country
		$countryCode = strtoupper(substr($name, 0, 2));
	} else { //Multiple-Word Country
        if(strcasecmp($explode[1], 'and')!=0 &&
		  	strcasecmp($explode[1], 'of')!=0 &&
		  	strcasecmp($explode[1], 'the')!=0){ //Second Word is not and / of / the
			$firstWord = $explode[0]; $secondWord = $explode[1];
        	$countryCode = strtoupper($firstWord[0] . $secondWord[0]);
		} else {
			$firstWord = $explode[0]; $thirdWord = $explode[2];
			$countryCode = strtoupper($firstWord[0] . $thirdWord[0]);
		}
	}	

        $sql = "INSERT INTO "
                . "`ihris_manage`.`hippo_country` "
                    . "(`id`, "
                    . "`parent`, "
                    . "`last_modified`, "
                    . "`i2ce_hidden`,"
                    . "`name`, "
                    . "`alpha_two`, "
                    . "`code`, "
                    . "`primary`, "
                    . "`location`) "
                . " VALUES ("
                	. "'" . $this->nextid('country|', 'hippo_country') . "', "
                    . "'NULL', "
                    . "NOW(), "
                    . "'0', "
                    . "'" . mysqli_real_escape_string($this->conn, $name) . "', "
                    . "'" . mysqli_real_escape_string($this->conn, strtoupper($countryCode)) . "', "
                    . "'" . mysqli_real_escape_string($this->conn, $code) . "', "
                    . "'1', "
                    . "'1') ";
            $query = mysqli_query($this->conn, $sql);
            
            //echo '<b>' . $sql  . '</b><br>'; //DEBUG
            
            if(!$query) {
            	//return false;
            } else {
            	//return true;
            }
    }
    public function insertCountry($valueSet) {
        $fhirData = $this->getFhirData($valueSet)->expansion->contains;
        if(!$fhirData) {
        	return false;
        }
        $size = iterator_count($fhirData);
        for ($x = 1; $x < $size; $x++) {
			$f = $fhirData[$x];
            echo "Country Inserted: " . $f->display['value'] . " - " .  $f->code['value'] . "<br>";
            $insert = $this->insertCountryQuery($f->display['value'], $f->code['value']);
            if(!$insert) {
                //return false;
            }
        }
    }
    public function fetchCountries() {
    	$sql = "SELECT * FROM `hippo_country`";
    	$query = mysqli_query($this->conn, $sql);
    	if($query) {
        	if(mysqli_errno($this->conn)) {
        		echo 'MySQL Query Error, SQL: ' . $sql . ' ERROR: ' . mysqli_error($this->conn);
        		return false;
        	} else {
        		$posts = array();
        		while($row = mysqli_fetch_array($query, MYSQLI_BOTH)) {
        			$posts[] = $row;
        		}
        		if(count($posts)>0) {
        			return  $posts;
        		} else {
        			return  false;
        		}
        		
        	}
    	} else {
    	    return false;
    	}
    }
    
    //REGION
    public function dropRegions() {
        $sql = "TRUNCATE table `hippo_region`";
        $query = mysqli_query($this->conn, $sql);
        
        if(!$query) {
        	return false;
        } else {
        	return true;
        }
    }
    
    public function getCountryName($id) {
        $sql = "SELECT * FROM `hippo_country` WHERE `id` = '" . $id . "'";
        $query = mysqli_query($this->conn, $sql);
        $assoc = mysqli_fetch_assoc($query);
        return $assoc['name'];
    }
    
    public function getRegionName($id) {
        $sql = "SELECT * FROM `hippo_region` WHERE `id` = '" . $id . "'";
        $query = mysqli_query($this->conn, $sql);
        $assoc = mysqli_fetch_assoc($query);
        return $assoc['name'];
    }
    
    public function getDistrictName($id) {
        $sql = "SELECT * FROM `hippo_district` WHERE `id` = '" . $id . "'";
        $query = mysqli_query($this->conn, $sql);
        $assoc = mysqli_fetch_assoc($query);
        return $assoc['name'];
    }
    
    public function insertRegions($region_name, $countryId) {
        $insert = $this->insertRegionQuery($region_name, $countryId);
        return true;
    }
    private function insertRegionQuery($name, $countryId) {
    	try {
    	    	$sql = "INSERT INTO "
                . "`ihris_manage`.`hippo_region` "
                    . "(`id`, "
                    . "`parent`, "
                    . "`last_modified`, "
                    . "`created`, " 
                    . "`i2ce_hidden`,"
                    . "`remap`,"
                    . "`code`,"
                    . "`country`,"
                    . "`name` "
                    . ") "
                . " VALUES ("
                	. "'" . $this->nextId('region|', 'hippo_region') . "', "
                    . "'NULL', "
                    . "NOW(), "
                    . "NOW(), "
                    . "'0', "
                    . "'NULL', "
                    . "'NULL', "
                    . "'" . mysqli_real_escape_string($this->conn, $countryId) . "', "
                    . "'" . mysqli_real_escape_string($this->conn, $this->getCountryName($countryId) . ' - ' . $name) . "') ";
    	    	
	    	$query = mysqli_query($this->conn, $sql);
    	} catch(Exception $e) {
    		echo "Error: " . $e->getMessage();
    	}
    }
    public function fetchRegions($option = false) {
    	$sql = "SELECT * FROM `hippo_region`";
    	$query = mysqli_query($this->conn, $sql);
    	if(mysqli_errno($this->conn)) {
    		echo 'MySQL Query Error, SQL: ' . $sql . ' ERROR: ' . mysqli_error($this->conn);
    		return false;
    	} else {
    		$posts = array();
    		$countries = array();
    		while($row = mysqli_fetch_array($query, MYSQLI_BOTH)) {
    			if($option) {
    				if(!in_array($row["country"], $countries)) {
    					$countries[] = $row["country"];
    				}
    			} else {
    				$posts[] = $row;
    			}
    		}
    		if($option) {
    			return $countries;
    		} else {
	    		 if(count($posts) > 0) {
	    			return  $posts;
	    		} else {
	    			return false;
	    		}
    		}
    	}
    }
    
    
    //DISTRICT
    public function dropDistricts() {
        $sql = "TRUNCATE table `hippo_district`";
   		$query = mysqli_query($this->conn, $sql);
        
        if(!$query) {
        	return false;
        } else {
        	return true;
        }
    }
    public function insertDistricts($valueset, $id) {
    	$fhirData = $this->getFhirData($valueset)->expansion->contains;
        if(!$fhirData) {
        	return false;
        }
        $size = iterator_count($fhirData);
        
        for ($x = 0; $x < $size; $x++) {
			$f = $fhirData[$x];
            echo "District Inserted: " . $f->display['value'] . " - " .  $f->code['value'] . "<br>";
            $insert = $this->insertDistrictQuery($x, $f->display['value'], $id);
        }
        return true;
       
    }
    private function insertDistrictQuery($id, $name, $regionId) {
    	    	$sql = "INSERT INTO "
                . "`ihris_manage`.`hippo_district` "
                    . "(`id`, "
                    . "`parent`, "
                    . "`last_modified`, "
                    . "`created`, " 
                    . "`i2ce_hidden`,"
                    . "`remap`,"
                    . "`code`,"
                    . "`region`,"
                    . "`name` "
                    . ") "
                . " VALUES ("
                	. "'" . $this->nextid('district|', 'hippo_district') . "', "
                    . "'NULL', "
                    . "NOW(), "
                    . "NOW(), "
                    . "'0', "
                    . "'NULL', "
                    . "'NULL', "
                    . "'" . mysqli_real_escape_string($this->conn, $regionId) . "', "
                    . "'" . mysqli_real_escape_string($this->conn, $this->getRegionName($regionId) . ' - ' . $name) . "') ";
    	    	
    	$query = mysqli_query($this->conn, $sql);
    }
    
    public function fetchDistricts() {
    	$sql = "SELECT * FROM `hippo_district`";
    	$query = mysqli_query($this->conn, $sql);
    	if(mysqli_errno($this->conn)) {
    		echo 'MySQL Query Error, SQL: ' . $sql . ' ERROR: ' . mysqli_error($this->conn);
    		return false;
    	} else {
    		$posts = array();
    		while($row = mysqli_fetch_array($query, MYSQLI_BOTH)) {
    			$posts[] = $row;
    		}
    	    if(count($posts) > 0) {
    			return  $posts;
    		} else {
    			return false;
    		}
    	}
    }
    
    
    //COUNTY
    private function insertCountyQuery($name, $district) {
    	    	$sql = "INSERT INTO "
                . "`ihris_manage`.`hippo_county` "
                    . "(`id`, "
                    . "`parent`, "
                    . "`last_modified`, "
                    . "`created`, "
                    . "`i2ce_hidden`,"
                    . "`remap`,"
                    . "`district`, "
                    . "`name` "
                    . ") "
                . " VALUES ("
                	. "'" . $this->nextid('county|', 'hippo_county') . "', "
                    . "'NULL', "
                    . "NOW(), "
                    . "NOW(), "
                    . "'0', "
                    . "'NULL', "
                   . "'" . mysqli_real_escape_string($this->conn, $district) . "', "
                    . "'" . mysqli_real_escape_string($this->conn, $this->getDistrictName($district) . ' - ' . $name) . "') ";
    	    	
    	$query = mysqli_query($this->conn, $sql);
    }
    public function dropCounties() {
        $sql = "TRUNCATE table `hippo_county`";
        $query = mysqli_query($this->conn, $sql);
        
        if(!$query) {
        	return false;
        } else {
        	return true;
        }
    }
    /**
     * You can create a set of County's based on a Value Set and a District ID (that the County's belong to)
     * @param unknown $valueSet to create the County's from
     * @param unknown $districtId of all the County's being created here
     */
    public function insertCounties($valueSet, $countyId) {
	    $fhirData = $this->getFhirData($valueSet)->expansion->contains;
	    if(!$fhirData) {
	    	return false;
	    }
        $size = iterator_count($fhirData);
        for ($x = 0; $x < $size; $x++) {
			$f = $fhirData[$x];
			echo "County Inserted: " . $f->display['value'] . " - " .  $f->code['value'] . "<br>";
            $this->insertCountyQuery($f->display['value'], $countyId); 
        }
        return true;
    }
    public function fetchCounties() {
    	$sql = "SELECT * FROM `hippo_county`";
    	$query = mysqli_query($this->conn, $sql);
    	if(mysqli_errno($this->conn)) {
    		echo 'MySQL Query Error, SQL: ' . $sql . ' ERROR: ' . mysqli_error($this->conn);
    		return false;
    	} else {
    		$posts = array();
    		while($row = mysqli_fetch_array($query, MYSQLI_BOTH)) {
    			$posts[] = $row;
    		}
    	    if(count($posts) > 0) {
    			return  $posts;
    		} else {
    			return false;
    		}
    	}
    }
    
    //FACILITY
    private function insertFacilityQuery($id, $name) {
    	$sql = "INSERT INTO "
                . "`ihris_manage`.`hippo_facility_type` "
                    . "(`id`, "
                    . "`parent`, "
                    . "`last_modified`, "
                    . "`i2ce_hidden`,"
                    . "`name` "
                    . ") "
                . " VALUES ("
                	. "'" . $this->nextid('facility|', 'hippo_facility_type') . "', "
                    . "'NULL', "
                    . "NOW(), "
                    . "'0', "
                    . "'" . mysqli_real_escape_string($this->conn, $name) . "') ";
    	$query = mysqli_query($this->conn, $sql);
    	
    	if(!$query) {
    		return false;
    	} else {
    		return true;
    	}
    }
    public function dropFacilities() {
        $sql = "TRUNCATE table hippo_facility_type";
    	$query = mysqli_query($this->conn, $sql);
        
        if(!$query) {
        	return false;
        } else {
        	return true;
        }
    }
    public function insertFacility($valueSet) {
   		$fhirData = $this->getFhirData($valueSet)->expansion->contains;
   		if(!$fhirData) {
   			return false;
   		}
        $size = iterator_count($fhirData);
        for ($x = 0; $x < $size; $x++) {
			$f = $fhirData[$x];
			echo "Facility Inserted: " . $f->display['value'] . " - " .  $f->code['value'] . "<br>";
            $this->insertFacilityQuery($x, $f->display['value']);
        }
    }
    public function fetchFacilities() {
    	$sql = "SELECT * FROM hippo_facility_type";
    	$query = mysqli_query($this->conn, $sql);
    	if(mysqli_errno($this->conn)) {
    		echo 'MySQL Query Error, SQL: ' . $sql . ' ERROR: ' . mysqli_error($this->conn);
    		return false;
    	} else {
    		$posts = array();
    		while($row = mysqli_fetch_array($query, MYSQLI_BOTH)) {
    			$posts[] = $row;
    		}
    	    if(count($posts) > 0) {
    			return  $posts;
    		} else {
    			return false;
    		}
    	}
    	
    }
    
    //POSITIONS
    public function fetchPositions() {
    	$sql = "SELECT * FROM hippo_position_type";
    	$query = mysqli_query($this->conn, $sql);
    	if(mysqli_errno($this->conn)) {
    		echo 'MySQL Query Error, SQL: ' . $sql . ' ERROR: ' . mysqli_error($this->conn);
    		return false;
    	} else {
    		$posts = array();
    		while($row = mysqli_fetch_array($query, MYSQLI_BOTH)) {
    			$posts[] = $row;
    		}
    		if(count($posts) > 0) {
    			return  $posts;
    		} else {
    			return false;
    		}
    	}
    }
    private function insertPositionQuery($id, $name) {
    	$sql = "INSERT INTO "
                . "`ihris_manage`.`hippo_position_type` "
                    . "(`id`, "
                    . "`parent`, "
                    . "`last_modified`, "
                    . "`i2ce_hidden`,"
                    . "`name` "
                    . ") "
                . " VALUES ("
                	. "'" . $this->nextid('position|', 'hippo_position_type') . "', "
                    . "'NULL', "
                    . "NOW(), "
                    . "'0', "
                    . "'" . mysqli_real_escape_string($this->conn, $name) . "') ";
    	$query = mysqli_query($this->conn, $sql);
    	 
    	if(!$query) {
    		return false;
    	} else {
    		return true;
    	}
    }
    public function dropPositions() {
        $sql = "TRUNCATE table `hippo_position_type`";
    	$query = mysqli_query($this->conn, $sql);
        if(!$query) {
        	return false;
        } else {
        	return true;
        }
    }
    public function insertPosition($valueSet) {
    	$fhirData = $this->getFhirData($valueSet)->expansion->contains;
    	if(!$fhirData) {
    		return false;
    	}
        $size = iterator_count($fhirData);
        for ($x = 0; $x < $size; $x++) {
			$f = $fhirData[$x];
            echo "Position Inserted: " . $f->display['value'] . " - " .  $f->code['value'] . "<br>";
            $this->insertPositionQuery($x, $f->display['value']);
        }
    }
    
    public function getCountryCode($country) {
        $country_code_map = array(    
        // <editor-fold defaultstate="collapsed" desc="user-description">
		"IM" => "Isle of Man",
		"IL" => "Israel",
		"IT" => "Italy",
		"JM" => "Jamaica",
		"JP" => "Japan",
		"JE" => "Jersey",
		"JT" => "Johnston Island",
		"JO" => "Jordan",
		"KZ" => "Kazakhstan",
		"KE" => "Kenya",
		"KI" => "Kiribati",
		"KW" => "Kuwait",
		"KG" => "Kyrgyzstan",
		"LA" => "Laos",
		"LV" => "Latvia",
		"LB" => "Lebanon",
		"LS" => "Lesotho",
		"LR" => "Liberia",
		"LY" => "Libya",
		"LI" => "Liechtenstein",
		"LT" => "Lithuania",
		"LU" => "Luxembourg",
		"MO" => "Macau SAR China",
		"MK" => "Macedonia",
		"MG" => "Madagascar",
		"MW" => "Malawi",
		"MY" => "Malaysia",
		"MV" => "Maldives",
		"ML" => "Mali",
		"MT" => "Malta",
		"MH" => "Marshall Islands",
		"MQ" => "Martinique",
		"MR" => "Mauritania",
		"MU" => "Mauritius",
		"YT" => "Mayotte",
		"FX" => "Metropolitan France",
		"MX" => "Mexico",
		"FM" => "Micronesia",
		"MI" => "Midway Islands",
		"MD" => "Moldova",
		"MC" => "Monaco",
		"MN" => "Mongolia",
		"ME" => "Montenegro",
		"MS" => "Montserrat",
		"MA" => "Morocco",
		"MZ" => "Mozambique",
		"MM" => "Myanmar [Burma]",
		"NA" => "Namibia",
		"NR" => "Nauru",
		"NP" => "Nepal",
		"NL" => "Netherlands",
		"AN" => "Netherlands Antilles",
		"NT" => "Neutral Zone",
		"NC" => "New Caledonia",
		"NZ" => "New Zealand",
		"NI" => "Nicaragua",
		"NE" => "Niger",
		"NG" => "Nigeria",
		"NU" => "Niue",
		"NF" => "Norfolk Island",
		"KP" => "North Korea",
		"VD" => "North Vietnam",
		"MP" => "Northern Mariana Islands",
		"NO" => "Norway",
		"OM" => "Oman",
		"PC" => "Pacific Islands Trust Territory",
		"PK" => "Pakistan",
		"PW" => "Palau",
		"PS" => "Palestinian Territories",
		"PA" => "Panama",
		"PZ" => "Panama Canal Zone",
		"PG" => "Papua New Guinea",
		"PY" => "Paraguay",
		"YD" => "People's Democratic Republic of Yemen",
		"PE" => "Peru",
		"PH" => "Philippines",
		"PN" => "Pitcairn Islands",
		"PL" => "Poland",
		"PT" => "Portugal",
		"PR" => "Puerto Rico",
		"QA" => "Qatar",
		"RO" => "Romania",
		"RU" => "Russia",
		"RW" => "Rwanda",
		"RE" => "Réunion",
		"BL" => "Saint Barthélemy",
		"SH" => "Saint Helena",
		"KN" => "Saint Kitts and Nevis",
		"LC" => "Saint Lucia",
		"MF" => "Saint Martin",
		"PM" => "Saint Pierre and Miquelon",
		"VC" => "Saint Vincent and the Grenadines",
		"WS" => "Samoa",
		"SM" => "San Marino",
		"SA" => "Saudi Arabia",
		"SN" => "Senegal",
		"RS" => "Serbia",
		"CS" => "Serbia and Montenegro",
		"SC" => "Seychelles",
		"SL" => "Sierra Leone",
		"SG" => "Singapore",
		"SK" => "Slovakia",
		"SI" => "Slovenia",
		"SB" => "Solomon Islands",
		"SO" => "Somalia",
		"ZA" => "South Africa",
		"GS" => "South Georgia and the South Sandwich Islands",
		"KR" => "South Korea",
		"ES" => "Spain",
		"LK" => "Sri Lanka",
		"SD" => "Sudan",
		"SR" => "Suriname",
		"SJ" => "Svalbard and Jan Mayen",
		"SZ" => "Swaziland",
		"SE" => "Sweden",
		"CH" => "Switzerland",
		"SY" => "Syria",
		"ST" => "São Tomé and Príncipe",
		"TW" => "Taiwan",
		"TJ" => "Tajikistan",
		"TZ" => "Tanzania",
		"TH" => "Thailand",
		"TL" => "Timor-Leste",
		"TG" => "Togo",
		"TK" => "Tokelau",
		"TO" => "Tonga",
		"TT" => "Trinidad and Tobago",
		"TN" => "Tunisia",
		"TR" => "Turkey",
		"TM" => "Turkmenistan",
		"TC" => "Turks and Caicos Islands",
		"TV" => "Tuvalu",
		"UM" => "U.S. Minor Outlying Islands",
		"PU" => "U.S. Miscellaneous Pacific Islands",
		"VI" => "U.S. Virgin Islands",
		"UG" => "Uganda",
		"UA" => "Ukraine",
		"SU" => "Union of Soviet Socialist Republics",
		"AE" => "United Arab Emirates",
		"GB" => "United Kingdom",
		"US" => "United States",
		"ZZ" => "Unknown or Invalid Region",
		"UY" => "Uruguay",
		"UZ" => "Uzbekistan",
		"VU" => "Vanuatu",
		"VA" => "Vatican City",
		"VE" => "Venezuela",
		"VN" => "Vietnam",
		"WK" => "Wake Island",
		"WF" => "Wallis and Futuna",
		"EH" => "Western Sahara",
		"YE" => "Yemen",
		"ZM" => "Zambia",
		"ZW" => "Zimbabwe",
		"AX" => "Åland Islands",
        // </editor-fold>
		);

		$search = array_search($country, $country_code_map);
		if($search) {
			return $search;
		} else {
			return false;
		}
    }     
}


