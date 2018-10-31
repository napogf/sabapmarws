<?php
/*
 * Created on 23/feb/11
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */


class xml2array {
	
	protected $dom;

	function __construct($xml) {
		if (is_string($xml)) {
			$this->dom = new DOMDocument;
			$this->dom->loadXml($xml);
		}
		return FALSE;
	}


	function _process($node) {
		$occurance = array();
		if(is_object($node) and $node->hasChildNodes()){
			foreach($node->childNodes as $child) {
				$occurance[$child->nodeName]++;
			}
		} else {
			$occurance[$node->nodeName]=1;
		}
		if($node->nodeType == XML_TEXT_NODE) {
			$result = $node->nodeValue;
		} else {
			if($node->hasChildNodes()){
				$children = $node->childNodes;
				for($i=0; $i<$children->length; $i++) {
					$child = $children->item($i);
					if($child->firstChild->nodeName != '#text') {
						if($occurance[$child->nodeName] > 1) {
							$result[$child->nodeName][$i] = $this->_process($child);
						}
						else {
							$result[$child->nodeName] = $this->_process($child);
						}
					} else if ($child->firstChild->nodeName == '#text') {
						$text = $this->_process($child->firstChild);
						if (trim($text) != '') {
							$result[$child->nodeName] = $text;
						}
					}
				}
			}

			if($node->hasAttributes()) {
				$attributes = $node->attributes;

				if(!is_null($attributes)) {
					foreach ($attributes as $key => $attr) {
						$result["@".$attr->name] = $attr->value;
					}
				}
			}
		}

		return $result;
	}

	public function getHtml($node = null, $level = 0)
	{
		$node = is_null($node) ? $this->dom : $node;
		r($node->firstChild->nodeName == '#text',false);
		r(count($node->childNodes),false);
		r($node->nodeName,false);
		r($level,false);
		if(is_object($node) and $node->hasChildNodes()){
			if($node->firstChild->nodeName == '#text' and count($node->childNodes) > 1){
				print('<ul class="liv_'.$level.'">'. $node->nodeName."\n");	
				foreach($node->childNodes as $child) {
					$this->getHtml($child,$level+1);		
				} 
				print("</ul>\n");
			} elseif ($node->firstChild->nodeName == '#text' and count($node->childNodes) == 1 and $node->nodeName <> '#document') {
				print('<li><label>' . $child->nodeName .'</label>'.$child->firstChild->nodeValue ."</li>\n");
			} else {
				r($node->nodeName,false);
				foreach($node->childNodes as $child) {
					$this->getHtml($child,$level+1);
				} 				
			}
		} else {
			r($node->nodeName);
		}
	}
	
	function getResult() {
		return $this->_process($this->dom);
	}
}
?>