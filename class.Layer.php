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
            if(is_array($file)) {
            
            }
			$this->_file = $file;
			$this->_url = $this->validate($url, 'url');
			$temp = $this->getDetails();
			$this->_width = $temp[0];
			$this->_height = $temp[1];
			$this->_name = $this->setName();
			$this->_extension = $this->getExtension();
			$this->_id = rand();
			$this->_type = $this->setType();
			$this->_code = $this->generateCode();
		}
		catch(Exception $e) {
			echo '<div class="alert alert-danger">';
			echo '<strong>Wyst¹pi³ b³¹d:</strong> ',  $e->getMessage(), "\n";
			echo '</div>';
		}
	}
}
?>