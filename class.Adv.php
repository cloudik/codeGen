<?php
class Adv {
    
    /**
	* Class is responsible for creating templates of so called "flat creatives". Layers are in extended class.
    * Set of protected variables
    *
    * @param string $_name - name of creative (to paste into field 'Creative name' in OAS
	* @param string $_file - directory for the image
	* @param string $_url - url for Landing Page
	* @param int $_id - random id of object (for jQuery purposes in view page)
	* @param int $_width - width of creative
	* @param int $_height - height of creative
	* @param string $_extension - extension of the file
	* @param string $_code - HTML code(s) generated for specific type of creative
	* @param string $_type - type of the creative (number of emission code in OAS)
	* @param string $_templateDir - templates and XML directory
	*
    */
    protected $_name;
	protected $_file;
	protected $_url;
	protected $_id;
	protected $_width;
	protected $_height;
	protected $_extension;
    protected $_code;
    protected $_type;
    protected $_templateDir = 'templates';
    
	public function debug($i) {
		echo '<pre>';
		print_r($i);
		echo '</pre>';
	}
	
	public function debugprotected($i) {
		if($i == 'code')
			$e = $this->_code;
		if($i == 'url')
			$e = $this->_url;
		echo '<pre>';
		print_r($e);
		echo '</pre>';
	}
	
    /**
    * 
    *
    */
    public function __construct($file, $url) {
		try {
			$this->_file = $file;
			$this->_url = $this->validate($url, 'url');
			$temp = $this->getDetails($this->_file);
			$this->_width = $temp[0];
			$this->_height = $temp[1];
			$this->_name = $this->setName();
			$this->_extension = $this->getExtension($this->_file);
			$this->_id = rand();
			$this->_type = $this->setType();
			$this->_code = $this->generateCode();
		}
		catch(Exception $e) {
			echo '<div class="alert alert-danger">';
			echo '<strong>Wystąpił błąd:</strong> ',  $e->getMessage(), "\n";
			echo '</div>';
		}
	}
    
    /**
    * 
    *
    */
    protected function getDetails($address) {
		
		if (@$temp = getimagesize($address)) {
			return $temp;
		} else {
			throw new Exception($address." nie istnieje."); 
		}
		//$temp = getimagesize($this->_file);
		//return $temp;
	}
    
    /**
    * 
    *
    */
    protected function getExtension($address) {
		$temp = explode('/', $address);
		$i = count($temp);
		$temp = $temp[$i-1];
		$temp = explode('.', $temp);
		return ($temp[1]);
	}
	
    /**
    * 
    *
    */
	protected function setName() {
		$temp = explode('/', $this->_file);
		$i = count($temp);
		$temp = $temp[$i-1];
		$temp = explode('.', $temp);
        //OAS problem with characters in creative name - change all , to .
        str_replace(',', '.', $temp[0]);
		return ($temp[0]);
	}
    
	/**
    * 
    *
    */
    protected function setType($width=NULL, $height=NULL) {
		if (is_null($width) && is_null($height)) {
			$xmlData = $this->getTypeXML();
		}
		else {
			$xmlData = $this->getTypeXML($width, $height);
		}
        $countElements = count($xmlData);
       
        if($countElements) {
			$i = 0;
			foreach($xmlData as $xml) {
             $result[$i] = $xml->name;
			 $i++;
			} 
        }
		else $result = NULL;
		return $result;
		
    }
    
	/**
    * 
    *
    */
    protected function getTypeXML($width=NULL, $height=NULL) {
		if (is_null($width) && is_null($height)) {
			$directory = $this->_templateDir;
			$file = $directory.'/index.xml';

			if (file_exists($file)) {
				$xml = simplexml_load_file($file);
			} 
			else {
				exit('Failed to open '.$file);
			}
	 
			$widthAdv = $this->_width;
			$heightAdv = $this->_height;
			
			foreach ($xml->adv as $adv) {
				foreach($adv->sizes->size as $size) {
					if(($size->width == $widthAdv) && ($size->height == $heightAdv)) {
					   $result[] = $adv;
					}
				}
			}
		}
		else {
			$directory = $this->_templateDir;
			$file = $directory.'/layer.xml';

			if (file_exists($file)) {
				$xml = simplexml_load_file($file);
			} 
			else {
				exit('Failed to open '.$file);
			}
	 
			$widthAdv = $width;
			$heightAdv = $height;
			
			foreach ($xml->adv as $adv) {
				foreach($adv->sizes->size as $size) {
					if(($size->width == $widthAdv) && ($size->height == $heightAdv)) {
					   $result[] = $adv;
					  
					}
				}
				if($adv->name == 'toplayer') {
					$temp[] = $adv;
				}
			}
			if (!isset($result) && !empty($result)) {
				$result = $temp;
			}
		}
		
        return $result;
    }
    
    /**
    * function multipleAdvs function returns true if this type of creative can have more than one placement on the website
    *
	* @param none
	* @return bool
    */
    public function multipleAdvs() {
        $total = count($this->_code);
        return $total;
    }
    
	/**
    * 
    *
    */
    public function getName() {
        return $this->_name;
    }
    
	/**
    * 
    *
    */
    public function getID() {
        return $this->_id;
    }
    
	/**
    * 
    *
    */
    public function getCode () {
        return $this->_code;
    }
	
	/**
    * 
    *
    */
	public function getType () {
        return $this->_type;
    }
    
	/**
    * 
    *
    */
    protected function generateCode($width=NULL, $height=NULL, $multi = NULL) {
		if (is_null($width) && is_null($height)) {
		
			$xmlData = $this->getTypeXML();
			$countElements = count($xmlData);
		   
			if($countElements) {
				$i = 0;
				foreach($xmlData as $data) {
					
					$static_arr = array('jpg', 'gif', 'png');
					if(in_array(strtolower($this->_extension), $static_arr)) {
						$file = $data->static;
					}
					else {
						$file = $data->swf;
					}

					$type = $data->types->type;
				  
					$filename = $this->_templateDir.'/'.$file;
				
					@$handle = fopen($filename, 'r');
					@$contents = fread($handle, filesize($filename));
					@fclose($handle);
			
					$contents = str_replace('{ADVID}', $type, $contents);
					$contents = str_replace('{FILE}', $this->_file, $contents);
					$contents = str_replace('{URL}', $this->_url, $contents);
					$contents = str_replace('{WIDTH}', $this->_width, $contents);
					$contents = str_replace('{HEIGHT}', $this->_height, $contents);
					
					//$result[$i]['name'] = $this->_name;
					$result[$i]['code'] = $contents;
					$i++;
				}
		   }
			else {
				$result = 'Nie znaleziono szablonu';
			}
		}
		else {
			$xmlData = $this->getTypeXML($width, $height);
			$countElements = count($xmlData);
			
			if($countElements) {
				$i = 0;
				foreach($xmlData as $data) {
					
					
					$file = $data->static;
					
					$type = $data->types->type;
				  
					$filename = $this->_templateDir.'/'.$file;
				
					@$handle = fopen($filename, 'r');
					@$contents = fread($handle, filesize($filename));
					@fclose($handle);
			
					$contents = str_replace('{ADVID}', $type, $contents);
					
					$static_arr = array('jpg', 'gif', 'png');
					if($multi != NULL) {
						
					}
					else {
						if(in_array(strtolower($this->_extension), $static_arr)) {
							$contents = str_replace('{IMG}', $this->_file, $contents);
							$contents = str_replace('{FILE}', '', $contents);
						}
						else {
							$contents = str_replace('{FILE}', $this->_file, $contents);
							$contents = str_replace('{IMG}', '', $contents);
						}
						$contents = str_replace('{URL}', $this->_url, $contents);
						$contents = str_replace('{WIDTH}', $this->_width, $contents);
						$contents = str_replace('{HEIGHT}', $this->_height, $contents);
					}
					//$result[$i]['name'] = $this->_name;
					$result[$i]['code'] = $contents;
					$i++;
				}
		   }
			else {
				$result = 'Nie znaleziono szablonu';
			}
		
		}
        return $result;
     
    }
    
    /**
    * 
    *
    */
	protected function validate($data, $tag) {
		switch($tag) {
			case 'url':
				$result = $this->validateURL($data);
				break;
			default:
				$result = NULL;
		}
		return $result;
	}
	
	/**
    * 
    *
    */
	protected function validateURL($url) {
		$i = 0;
		
		$regex = "((https?|ftp)\:\/\/)?"; // SCHEME
		$regex .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?"; // User and Pass
		$regex .= "([a-z0-9-.]*)\.([a-z]{2,3})"; // Host or IP
		$regex .= "(\:[0-9]{2,5})?"; // Port
		$regex .= "(\/([a-z0-9+\$_-]\.?)+)*\/?"; // Path
		$regex .= "(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?"; // GET Query
		$regex .= "(#[a-z_.-][a-z0-9+\$_.-]*)?"; // Anchor 
		
		if(is_array($url)) {
			foreach($url as $testURL) {
				if (strpos($testURL, 'http') !== 0) {
					$testURL = 'http://'.$testURL;
				}
		
				if(preg_match("/^$regex$/", $testURL)) {
					//return $testURL;
				} 
				else {
					//throw new Exception($testURL." może być niepoprawny."); 
					echo '<div class="alert alert-warning">';
					echo '<strong>Uwaga:</strong> URL <a href="'.$testURL.'">'.$testURL.'</a> może być niepoprawny.'."\n";
					echo '</div>';
					//return NULL;
				}
				$t[$i] = $testURL;
				$i++;
			}
			return $t;
		}
		else {
			if (strpos($url, 'http') !== 0) {
				$url = 'http://'.$url;
			}
		
			if(preg_match("/^$regex$/", $url)) {
				//return $url;
				
			} 
			else {
				//throw new Exception($url." może być niepoprawny."); 
				echo '<div class="alert alert-warning">';
				echo '<strong>Uwaga:</strong> URL <a href="'.$url.'">'.$url.'</a> może być niepoprawny.'."\n";
				echo '</div>';
				//return NULL;
			}
			//$testURL = $url;
			return $url;
		}
		/*
		if ($i > 2)
			return $testURL;
		else
			return $t;
		*/
	}

}
?>