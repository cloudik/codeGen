<?php
class Adv {
    
    /**
	* Class is responsible for creating templates of so called "flat creatives". Layers are in extended class.
    * Set of protected variables
    *
    * @param string $_name - name of creative (to paste into field 'Creative name' in OAS
	* @param string $_file - directory for the image
	* @param string $_url - url for Landing Page
	* @param string $_type - type of the creative (boksEC, boksECL, boksECP or txtEC)
	* @param string $_txt - non obligatory; stores text of the link advertisment
	*
    */
    protected $_name;
	protected $_file;
	protected $_url;
	protected $_type = 0;
	//protected $_txt; will be used for txt advs in extended class
	protected $_width;
	protected $_height;
	protected $_extension;
    
    
    /**
    * 
    *
    */
    public function __construct($file, $url, $type = -1) {
		$this->_file = $file;
		$this->_url = $url;
		
		$temp = $this->getDetails();
		$this->_width = $temp[0];
		$this->_height = $temp[1];
		
		$this->_name = $this->getName();
		
		$this->_extension = $this->getExtension();
		
		if ($type > 0) {
			$this->_type = $type;
		}
		else {
			$this->_type = $this->getType();
		}	
	}
    
    /**
    * 
    *
    */
    protected function getDetails() {
		$temp = getimagesize($this->_file);
		return $temp;
	}
    
    /**
    * 
    *
    */
    protected function getExtension() {
		$temp = explode('/', $this->_file);
		$i = count($temp);
		$temp = $temp[$i-1];
		$temp = explode('.', $temp);
		return ($temp[1]);
	}
	
    /**
    * 
    *
    */
	protected function getName() {
		$temp = explode('/', $this->_file);
		$i = count($temp);
		$temp = $temp[$i-1];
		$temp = explode('.', $temp);
        //OAS problem with characters in creative name - change all , to .
        str_replace(',', '.', $temp[0]);
		return ($temp[0]);
	}
    
    protected function setType($type) {
        $this->_type = $type;
    }
    
     /**
    * 
    *
    */
    protected function getType() {
        switch($this->_width) {
            case 316:
                $type[0] = '018';
                $type[1] = '004';
                break;
            case ($this->_width == 750 || $this->_width == 970):
					$type = '003';
					break;
            case 300:
					switch ($this->_height) {
						case 600:
						$type = '037';
						break;
						case 150:
						$type = '034';
						break;
						case 250:
						$type[0] = '005';
						$type[1] = '034';
						break;
					}
					break;
				case ($this->_width == 450 || $this->_width == 420) :
					$type = '005';
					break;
				case 152:
					$type[0] = '011';
					$type[1] = '012';
					break;
				case 646:
					$type[0] = '013';
                    $type[1] = '015';
					break;
				default:
					$type[] = '003';
					$type[] = '005';
					$type[] = '034';
					$type[] = '037';
					break;
        }
        return $type;
    }
    
    /**
    * 
    *
    */
    protected function getTemplate($type) {
        
    }
    
    /**
    * 
    *
    */
    public function multipleAdvs() {
        if(is_array($this->_type)) {
           return 1;
        }
        else {
           return 0;
        }
    }
    
    /**
    * 
    *
    */
    public function showAll() {
        echo '<pre>';
		var_dump(get_object_vars($this));
        $this->getTemplate();
        echo '</pre>';
	}
}
?>