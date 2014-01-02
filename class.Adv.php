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
	
	
    /**
    * 
    *
    */
    public function __construct($file, $url) {
		$this->_file = $file;
		$this->_url = $url;
		
		$temp = $this->getDetails();
		$this->_width = $temp[0];
		$this->_height = $temp[1];
		
		$this->_name = $this->setName();
		
		$this->_extension = $this->getExtension();
		
		$this->_id = rand();
        $this->_type = $this->setType();
        $this->_code = $this->generateCode();
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
	protected function setName() {
		$temp = explode('/', $this->_file);
		$i = count($temp);
		$temp = $temp[$i-1];
		$temp = explode('.', $temp);
        //OAS problem with characters in creative name - change all , to .
        str_replace(',', '.', $temp[0]);
		return ($temp[0]);
	}
    
    protected function setType() {
        //$this->_type = $type;
        $xmlData = $this->getTypeXML();
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
    
    protected function getTypeXML() {

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
        
        //$this->debug($result);
        return $result;
    }
    

    /**
    * 
    *
    */
    protected function getTemplate($type, $extension) {
        $this->debug($type);
		$this->debug($extension);
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
        /*
        if($total > 1) {
           return true;
        }
        else {
           return false;
        }
        */
    }
    
    public function getName() {
        return $this->_name;
    }
    
    public function getID() {
        return $this->_id;
    }
    
    public function getCode () {
        return $this->_code;
    }
	
	public function getType () {
        return $this->_type;
    }
    
    protected function generateCode() {
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
       
        return $result;
     
    }
    
    /**
    * 
    *
    */
    public function showAll() {
        echo '<pre>';
		var_dump(get_object_vars($this));
        //$this->getTemplate($this->_type, $this->_extension);
        $this->getTypeXML();
        echo '</pre>';
	}
}
?>