<?php
class Layer extends Adv {
 /**
	* Class is responsible for creating templates of so called "flat creatives". Layers are in extended class.
    * Set of protected variables
    *
    * @param string/array $_name - name of creative (to paste into field 'Creative name' in OAS
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
    
    public function __construct($file, $url) {
		try {
			$this->_file = $file;
			$this->_name = $this->setName();
            $this->_url = $this->validate($url, 'url');
            $layer = true;
			if(is_array($file)) {
				$i = 0;
				$multi = true;
				foreach($this->_file as $creative) {
					$creative = trim($creative);
					$temp = $this->getDetails($creative);
					$this->_width[$i] = $temp[0];
					$this->_height[$i] = $temp[1];
					$this->_extension[$i] = $this->getExtension($creative);
					$this->_type[$i] = $this->setType($layer, $multi);
					
					$i++;
				}
				$this->_code = $this->generateCode($layer, $multi);
				
            }
			else {
                $multi = false;
				$temp = $this->getDetails($this->_file);
				$this->_width = $temp[0];
				$this->_height = $temp[1];
				$this->_extension = $this->getExtension($this->_file);
				$this->_type = $this->setType($layer, $multi);
				$this->_code = $this->generateCode($layer, $multi);
			}
			$this->_id = rand();
			
	
		}
		catch(Exception $e) {
			echo '<div class="alert alert-danger">';
			echo '<strong>Wystąpił błąd:</strong> ',  $e->getMessage(), "\n";
			echo '</div>';
		}
	}
	
	protected function setName($multi = NULL) {
		if(is_array($this->_file))
			$temp = explode('/', $this->_file[0]);
		else
			$temp = explode('/', $this->_file);
			
		$i = count($temp);
		$temp = $temp[$i-1];
		$temp = explode('.', $temp);
        //OAS problem with characters in creative name - change all , to .
        str_replace(',', '.', $temp[0]);
		return ($temp[0]);
	}
	
}
?>