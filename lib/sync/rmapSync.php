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
    		$guzzle = new Client(); 
    		$response = $guzzle->request('PUT', $url, [
    				'json' => $this->buildPayload(),
    				'auth' => [$this->rmapUser, $this->rmapPassword]
    		]);

    		echo "RESPONSE: " . $response->getStatusCode();
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
            $thisArray = array(
                'id' => strval($option_id),
                'code' => strval($code),
                'label' => strval($name)
                );
            if(size($thisArray)>0){
            	array_push($return, $thisArray);
            }
            $option_id++;
        }
        return $return;
	}
	
	public function buildPayload() {
		$json = array(
			'layer' => array(
				'id' => $this->rc->getLayerId(),
				'name' => $this->rc->getLayerName(), //Change to Dynamic
				'ord' => 2, //Change to Dynamic
				'fields_attributes' => array(
				   '0' => array(
				        'id' => $this->rc->getFieldId(),
				        'name' => $this->rc->getLayerName(),
				        'code' => $this->rc->getFieldCode(),
				        'kind' => 'select_one',
				        'ord' => $this->rc->getFieldOrder(),
				        'layer_id' => $this->rc->getLayerId(),
				        'config' => array(
				            'options' => $this->buildOptions($this->rc->getValuesetName())
				        )
				    )
				)
			)
		);

		//TODO:
		$config = array();
		$fields_attributes() = array();
		$layer = array();
		$json = array();

		$config['options'] = $this->buildOptions($this->rc->getValuesetName())
		$field_0 = array(
				        'id' => $this->rc->getFieldId(),
				        'name' => $this->rc->getLayerName(),
				        'code' => $this->rc->getFieldCode(),
				        'kind' => 'select_one',
				        'ord' => $this->rc->getFieldOrder(),
				        'layer_id' => $this->rc->getLayerId(),
				        'config' => $config
				    );
		$fields
		$fields_attributes['0'] = $field_0;
		$layer



		echo json_encode($json, JSON_FORCE_OBJECT);
		return $json;
	}
	
	public function buildPayload_MANUAL() {
		$this->addLine("{");
			$this->addLine("\"layer\":{");
				$this->addLine("\"id\":\"" . $this->rc->getLayerId() . "\",");
				$this->addLine("\"name\":\"" . $this->rc->getLayerName() . "\",");
				$this->addLine("\"ord\":\"2\",");
					$this->addLine("\"fields_attributes\":{");
						$this->addLine("\"0\":{ ");
							$this->addLine("\"id\":\"" . $this->rc->getFieldName() . "\",");
							$this->addLine("\"name\":\"" . $this->rc->getlayerName() . "\",");
							$this->addLine("\"code\":\"" .$this->rc->getFieldCode() . "\",");
							$this->addLine("\"kind\":\"select_one\",");
							$this->addLine("\"ord\":\"" . $this->rc->getFieldOrder() . "\",");
							$this->addLine("\"layer_id\":\"" . $this->rc->getLayerId() . "\",");
							$this->addLine("\"config\":{");
								$this->addLine("\"options\":[");
									$count = 0;
									$fhirData = $this->getFhirData($this->rc->getValuesetName())->expansion->contains;
									if(!$fhirData) { return false; }
									$size = iterator_count($fhirData);
									$NEXT_ID = $this->rc->getNextId();
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