<?php
/*
 * Created on 14/mag/2013
 *
 * xmlToHtml.php
*/



class xmlToHtml {

	protected $xmlObj;
    protected $wellFormed = true;
    function __construct($xmlContent)
    {
        try {
            libxml_use_internal_errors(true);
            $this->xmlObj = simplexml_load_string($xmlContent);
            foreach (libxml_get_errors() as $error) {
                // handle errors here
                throw new Exception('errore nel file xml');
            }
        } catch (Exception $e) {
            $this->wellFormed = false;
        }

        return $this->xmlObj;
	}

	public function xmlWellFormed()
    {

        return $this->wellFormed;
    }

	public function getHtml(){
		$arrData = array();

	    // if input is object, convert into array
	    if (is_object($this->xmlObj)) {
	        $this->toHtml($this->xmlObj);
	    }


	}


	protected function toHtml($element, $label = 'document', $class = 'normal')
	{
		$arrayVar = is_object($element) ? get_object_vars($element) : null;
		if(is_object($element) and empty($arrayVar)){
				if(is_object($element) and is_array($element[0])){
					foreach ($element as $key => $value) {
						$this->toHtml($value, $key);
					}
				} elseif (is_array($element)){
				    foreach ($element as $value) {
				        $this->toHtml($value);
				    }
				}
				else {
					print('<li><span>'.$label.'</span>&nbsp;'.htmlspecialchars($element[0]).'</li>' . PHP_EOL);
				}

		} elseif (is_array($element)){
			print('<ol><span>'.$label.'</span>' . PHP_EOL);
			foreach ($element as $key => $value) {
			    if(is_object($value)){
			        $this->toHtml($value);
			    } else {
			        print('<li><span>'.$key.'</span>&nbsp;'.htmlspecialchars($value).'</li>' . PHP_EOL);
			    }
			}
			print('</ol>' . PHP_EOL);
		} elseif (is_string($element)) {
			if($label == 'document' or $label == 'Intestazione') return true;
			print('<li><span>'.$label.'</span>&nbsp;'.htmlspecialchars($element).'</li>' . PHP_EOL);
		} else {
			print('<ol><span>'.$label.'</span>' . PHP_EOL);
			$elementToParse = is_array($arrayVar) ? $arrayVar : $element;
			foreach ($elementToParse as $key => $value) {
				if($key=='@attributes' and is_array($value)){
					foreach ($value as $attLabel => $attValue) {
						print('<li class="attributes"><span>'.$attLabel.'</span>&nbsp;'.htmlspecialchars($attValue).'</li>' . PHP_EOL);
					}
				} else {
					$this->toHtml($value, $key);
				}
			}
			print('</ol>' . PHP_EOL);
		}
	}



	protected function toHtml2($label, $element)
	{

		if(is_object($element) or is_array($element)){
			// lista
			if (count($element)>1){
				print('<ol><span>'.$label.'</span>' . PHP_EOL);
				foreach ($element as $key => $value) {
					$this->toHtml($key,$value);
				}
				print('</ol>' . PHP_EOL);
			} else {

				if(is_object($element) and is_array($element[0])){
					foreach ($element as $key => $value) {
						$this->toHtml($key,$value);
					}
				} else {
					print('<li><span>'.$label.'</span>&nbsp;'.htmlspecialchars($element[0]).'</li>' . PHP_EOL);
				}

			}
		}
	}




}