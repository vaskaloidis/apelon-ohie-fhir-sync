<?php

namespace Apelon\Sync;

use GuzzleHttp\Client;
use Apelon\Object\rmFacilityTypeConfig;

require __DIR__ . '/../../vendor/autoload.php';

class rmapSync {
	
	private $conn, $dtsServer, $dtsUser, $dtsPassword, $rmapServer, $rmapUser, $rmapPassword, $payload, $collectionsUrl;
	private $rc, $error;
	
	
	public function getError() {
	    return $this->error;
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
	}
	
	/*
	 * Set a Resource Map Layer-Type Config
	 */
	public function setRmapConfig($config) {
	    $this->rc = $config;
	}
	
	public function getRmapConfig() {
	    return $this->rc;
	}
	
	public function getServer() {
		return $this->rmapServer;
	}
	
	public function setRmapServer($server, $username, $password) {
		$this->rmapServer = $server . "api/collections/";
		$this->rmapUser = $username;
		$this->rmapPassword = $password;
		//TODO: Aadd true or false return if credentials are correct
	}
	
	public function updateRmapLayer($rmo = null)  {
	   if(isset($rmo)) {
	       $this->rc = $rmo;
	   }
		$url = $this->rmapServer . $this->rc->getCollectionId() . "/layers/" . $this->rc->getLayerId() . ".json";
		$collectionsUrl = $this->rmapServer . 'collections/' . $this->rc->getCollectionId() . '/layers.json';
		
		try {
		    $payload = $this->buildPayload();
		    
    		$guzzle = new Client(); 
    		$request = $guzzle->request('PUT', $url, [
    				'content-type' => 'application/json',
    				'auth' => [$this->rmapUser, $this->rmapPassword]
    		],array());
    		$request->setBody($payload);
    		$response = $request->send();
    		return true;
		} catch(Exception $e) {
		    $this->e = $e->getMessage();
		    return false;
		}
		
		echo print_r($response);
	}
	
	private function addLine($command) {
		$this->payload .= $command . PHP_EOL;
	}
	
	
	public function buildPayload() {
        $options =$this->buildOptions($this->rc->getValuesetName());
		
		$json = array(
			'layer' => array(
				'id' => $this->rc->getLayerId(),
				'name' => $this->rc->getLayerName(), //Change to Dynamic
				'ord' => 2, //Change to Dynamic
				'fields_attributes' => array(
				   '0' => array(
				        'id' => $this->rc->getFieldName(),
				        'name' => $this->rc->getLayerName(),
				        'code' => $this->rc->getFieldCode(),
				        'kind' => 'select_one',
				        'ord' => $this->rc->getFieldOrder(),
				        'layer_id' => $this->rc->getLayerId(),
				        'config' => array(
				            'options' => $options
				        )
				    )
				)
			)
		);
		echo json_encode($json, JSON_FORCE_OBJECT);
		return $json;
	}
		
	public function buildOptions($valueset) {
        $fhirData = $this->getFhirData($valueset)->expansion->contains;
        if(!$fhirData) {
            return false;
        }
        $return = array();
        $size = iterator_count($fhirData);
        $option_id = $this->rc->getNextId();
        
        for ($x = 0; $x < $size; $x++) {
            $f = $fhirData[$x];
            $name_data = $f->display['value']; $name = $name_data[0];
            $code_data = $f->code['value']; $code = $code_data[0];
            $return[] = array(
                'id' => strval($option_id),
                'code' => strval($code),
                'label' => strval($name)
                );
            $option_id++;
        }
        return $return;
	}
	
	public function buildPayload3($layerId, $valueSet, $layerName, $optionName, $NEXT_ID = 2) {
		$optName2 = strtolower($optionName);
		$explode = explode(" ", $optName2); 
		$optionName2 = $explode[0] . "_" . $explode[1];
		$this->addLine("{");
			$this->addLine("\"layer\":{");
			$this->addLine("\"id\":\"" . $layerId . "\",");
			$this->addLine("\"name\":\"" . $layerName . "\",");
			$this->addLine("\"ord\":\"2\",");
			$this->addLine("\"fields_attributes\":{");
				$this->addLine("\"0\":{ ");
					$this->addLine("\"id\":\"13377\",");
					$this->addLine("\"name\":\"" . $optionName . "\",");
					$this->addLine("\"code\":\"" . $optionName2 . "\",");
					$this->addLine("\"kind\":\"select_one\",");
					$this->addLine("\"ord\":\"1\",");
					$this->addLine("\"layer_id\":\"" . $layerId . "\",");
					$this->addLine("\"config\":{");
						$this->addLine("\"options\":[");
							//$NEXT_ID = 81; //NEXT_ID //TODO: THIS NEEDS TO BE RETREIVED DYNAMICALLY FROM API
							$count = 0;
							$fhirData = $this->getFhirData($valueSet)->expansion->contains;
							if(!$fhirData) { return false; }
							$size = iterator_count($fhirData);
							for ($x = 0; $x < $size; $x++) {
								$row = $fhirData[$x];
								$this->addLine("{ ");
									$this->addLine("\"id\":\"" . $NEXT_ID . "\",");
									$this->addLine("\"code\":\"" . $row->display['value'] . "\",");
									$this->addLine("\"label\":\"" . $row->code['value'] . "\"");
								$this->addLine("}");
								//addLine(count + " / " + facilitiesData.size());
								if($count != ($size-1)) {
									$this->addLine(",");
								}
								$count++; $NEXT_ID++;
							}
						
						$this->addLine("]");
					$this->addLine("}");
				$this->addLine("}");
			$this->addLine("}");
		$this->addLine("}");
	$this->addLine("}");
	echo '<br><hr>';
	echo $this->payload;
	}
	
    /**
     * Returns an array of parsed FHIR Data from the value-set passed-in
     * @param type $valueSet DTS FHIR Value-Set name to retreive
     */
    public function getFhirData($valuesetName) {
        $url = $this->dtsServer . "ValueSet/" . $valuesetName . "/$" . "expand";
        $context = stream_context_create(array(
        'http' => array(
            'header' => "Authorization: Basic " . base64_encode($this->dtsUser . ":" . $this->dtsPassword) . "\r\n"
                )
            )
        );
        try {
        	$xml = file_get_contents($url, false, $context);
        } catch(Exception $e) {
        	echo "Failure connecting to DTS FHIR Server";
        	return false;
        }
        
        if($xml != null && $xml) {
        	$xml = simplexml_load_string($xml);
        	//var_dump($xml->expansion); //Keep for Testing
        	return $xml;
        } else {
        	return false;		
        }
    }
}