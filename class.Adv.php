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
    * Function getDetails - function returns details of file which address is provided
    *
	* @param string $address - address of file of which we want details (such as width and height)
	* @return array $temp array with details ([0] - width, [1] - height, [2] - weight)
    */
    protected function getDetails($address) {
		
		if (@$temp = getimagesize($address)) {
			return $temp;
		} else {
			throw new Exception("Plik ".$address." nie istnieje."); 
		}
		//$temp = getimagesize($this->_file);
		//return $temp;
	}
    
    /**
    * Function getExtension - function returns extension of file which address' is provided
    *
	* @param string $address - address of file of which we want such detail
	* @return string $temp[1] - extension of file
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
	protected function setName($multi = NULL) {
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
    protected function setType($layer=NULL, $multi=NULL) {
		if (is_null($layer) && is_null($multi)) {
			$xmlData = $this->getTypeXML();
		}
		else {    
			$xmlData = $this->getTypeXML($layer, $multi);
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
    protected function getTypeXML($layer=NULL, $multi=NULL) {
		if (is_null($layer)) {
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
            if(!empty($result)) {
               
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
	       
            if($multi) {
			    $widthAdv = $this->_width[0];
			    $heightAdv = $this->_height[0];
			}
            else {
                $widthAdv = $this->_width;
			    $heightAdv = $this->_height;
            }
            
			foreach ($xml->adv as $adv) {
				foreach($adv->sizes->size as $size) {
					if(($size->width == $widthAdv) && ($size->height == $heightAdv)) {
						if($multi) {
							if($adv->files == 2)
								$result[] = $adv;
						}
						else {
							//we exclude files with more 2 too files needed
							if($adv->files == 1)
								$result[] = $adv;
						}	
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
        if(!isset($result))
            $result = NULL;

        return $result;
    }
    
    /**
    * function multipleAdvs function returns true if this type of creative can have more than one placement on the website
    *
	* @param none
	* @return bool
    */
    public function multipleAdvs($multi = NULL) {
        if($multi) 
			$total = 1;
		else	
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
    public function getCode ($multi = NULL) {
		if($multi)
			return $this->_code[1];
		else	
        return $this->_code;
    }
	
	public function checkFiles() {
		if(is_array($this->_file)) 
			return true;
		else
			return false;
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
    protected function generateCode($layer = NULL, $multi = NULL) {
		//is not layer advertisemnt
		if (is_null($layer)) {
		
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
		//is layer advertisemnt
		else {
			$xmlData = $this->getTypeXML($layer, $multi);
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
					
					//more than 2 files for adv (billboard + toplayer)
					if($multi != NULL) {
						$bill_width = array(750, 970);
						if(in_array($this->_width[0], $bill_width)) {
							if(in_array(strtolower($this->_extension[0]), $static_arr)) {
								$contents = str_replace('{IMG}', trim($this->_file[0]), $contents);
								$contents = str_replace('{FILE}', '', $contents);
							}	
							else {
								$contents = str_replace('{FILE}', trim($this->_file[0]), $contents);
								$contents = str_replace('{IMG}', '', $contents);
							}
							
							if(in_array(strtolower($this->_extension[1]), $static_arr)) {
								$contents = str_replace('{IMG2}', $this->_file[1], $contents);
								$contents = str_replace('{FILE2}', '', $contents);
							}	
							else {
								$contents = str_replace('{FILE2}', $this->_file[1], $contents);
								$contents = str_replace('{IMG2}', '', $contents);
							}
							$contents = str_replace('{URL}', trim($this->_url[0]), $contents);
							$contents = str_replace('{WIDTH}', $this->_width[0], $contents);
							$contents = str_replace('{HEIGHT}', $this->_height[0], $contents);
							$contents = str_replace('{URL2}', $this->_url[1], $contents);
							$contents = str_replace('{WIDTH2}', $this->_width[1], $contents);
							$contents = str_replace('{HEIGHT2}', $this->_height[1], $contents);
						}
						else {
							if(in_array(strtolower($this->_extension[1]), $static_arr)) {
								$contents = str_replace('{IMG}', $this->_file[1], $contents);
								$contents = str_replace('{FILE}', '', $contents);
							}	
							else {
								$contents = str_replace('{FILE}', $this->_file[1], $contents);
								$contents = str_replace('{IMG}', '', $contents);
							}
							
							if(in_array(strtolower($this->_extension[0]), $static_arr)) {
								$contents = str_replace('{IMG2}', $this->_file[0], $contents);
								$contents = str_replace('{FILE2}', '', $contents);
							}	
							else {
								$contents = str_replace('{FILE2}', $this->_file[0], $contents);
								$contents = str_replace('{IMG2}', '', $contents);
							}
							$contents = str_replace('{URL}', $this->_url[1], $contents);
							$contents = str_replace('{WIDTH}', $this->_width[1], $contents);
							$contents = str_replace('{HEIGHT}', $this->_height[1], $contents);
							$contents = str_replace('{URL2}', $this->_url[0], $contents);
							$contents = str_replace('{WIDTH2}', $this->_width[0], $contents);
							$contents = str_replace('{HEIGHT2}', $this->_height[0], $contents);
						}
					}
					//single file (toplayer, scroll or  floorAd)
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
				$testURL = trim($testURL);
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