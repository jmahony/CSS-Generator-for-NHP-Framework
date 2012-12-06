<?php

function generate_styles($styleIndex = null) {

	$options = get_option();

	$Stylesheet = new CssStyleSheet();

	foreach ($styleIndex as $style) {
		if (array_key_exists($style['option_id'], $options)) {
			$CssAttribute = new CssAttribute($style['selector']);
			$CssAttribute->addProperty($style['property'], $options[$style['option_id']]);
			$Stylesheet->addAttribute($CssAttribute);
		}
	}

	echo $Stylesheet->toString(true); // pass true to compress output

}

/**
 * CssStyleSheet 
 *
 * Generates a style sheet
 *
 * @package default
 * @author Josh Mahony (Republique Design)
 **/
class CssStyleSheet {

	/**
	 * attributes
	 *
	 * Holds an array of CssAttribute objects
	 * with the selector as the key
	 *
	 * @var array
	 **/
	private $attributes;

	function __construct() {
		$this->attributes = array();
	}

	/**
	 * toString
	 * 
	 * Iterates through the attributes array calling the
	 * toString method of each attribute object
	 * 
	 * @return String
	 **/
	public function toString($compression = false) {
		
		$header = '<style type="text/css">';
		$css    = '';
		$footer = '</style>';

		foreach ($this->attributes as $attribute) {
			$css .= $attribute->toString();
		}

		if ($compression) {
			$css = $this->compress($css);
		}

		return $header . $css . $footer;
	}

	/**
	 * addAttribute
	 * 
	 * Adds an attribute to the stylesheet.
	 * Will merge with attribute if selector already
	 * exists in the attributes array.
	 * 
	 * @return void
	 * @param CssAttribute object
	 **/
	public function addAttribute(CssAttribute $CssAttribute) {
		$selector = $CssAttribute->getSelector();
		if ($this->hasAttribute($selector)) {
			$this->attributes[$selector]->merge($CssAttribute);
		} else {
			$this->attributes[$selector] = $CssAttribute;
		}
	}

	/**
	 * hasAttribute
	 * 
	 * Checks if selector already exists in the attributes array
	 * 
	 * @return Boolean
	 * @param selector
	 **/
	private function hasAttribute($selector = '') {
		return array_key_exists($selector, $this->attributes);
	}
	
	/**
	 * compress
	 *
	 * @param String css
	 * @return String
	 * @author 
	 **/
	private function compress($css = '') {
		$css = preg_replace('/\\n+|\\t+/', '', $css);

		return $css;
	}
} // END class 

/**
 * CssAttribute
 * 
 * Stores the selector and properties of a CSS attribute
 *
 * @package default
 * @author Josh Mahony (Republique Design)
 * @
 **/
class CssAttribute {
	
	/**
	 * selector
	 *
	 * Holds the selector part of the CSS attribute,
	 * e.g. the 'heading' part of .heading { }
	 *
	 * @var String
	 **/
	private $selector;

	/**
	 * properties
	 * 
	 * 
	 * Holds the properties part of the CSS attribute,
	 * e.g. anything between { } part of .heading { }
	 * @example
	 * array(
	 * 		'color' => 'red'
	 * );
	 *
	 * @var Array
	 **/
	private $properties;

	/**
	 * Constructor
	 *
	 * Assign the selector type and selector for the attribute
	 *
	 * @return void
	 * @param selector
	 **/
	function __construct($selector = '') {
		$this->selector   = $selector;
		$this->properties = array();
	}

	/**
	 * addProperty
	 *
	 * Add a property to the properties array, e.g.
	 * addProperty('color','red')
	 *
	 * @return void
	 * @param type
	 * @param value
	 **/
	public function addProperty($type = '', $value = ''){
		$this->properties[$type] = $value;
	}

	/**
	 * toString
	 * 
	 * Converts the properties to a printable string
	 * 
	 * @return String
	 **/
	public function toString() {
		$attribute  = '';
		$attribute .= $this->selector . '{';

		foreach ($this->properties as $property => $value) {

			if ($value != null) {
				$attribute .= $property . ':';

				if ($this->isURL($value)) {
					$attribute .= 'url("' . $value . '");';
				} else {
					$attribute .= $value . ';';
				}
			}
		}

		$attribute .= '}';

		return $attribute;
	}

	/**
	 * merges
	 * 
	 * Merges supplied CssAttribute with itself
	 * 
	 * @return String
	 * @param CssAttribute object
	 **/
	public function merge(CssAttribute $CssAttribute) {
		$properties = $CssAttribute->getProperties();
		$this->properties = array_merge($this->properties, $properties);
	}

	/**
	 * isURL
	 * 
	 * Retruns whether or not a string is a URL
	 * 
	 * @param url
	 * @return Boolean
	 **/
	public function isURL($url = '') {
		if (strpos($url, 'http') > -1) {
			return true;
		}
		return false;
	}

	/**
	 * getProperties
	 * 
	 * Returns the properties array
	 * 
	 * @return Array
	 **/
	private function getProperties() {
		return $this->properties;
	}

	/**
	 * getSelector
	 * 
	 * Returns the selector string
	 * 
	 * @return String
	 **/
	public function getSelector() {
		return $this->selector;
	}
} // END class 
?>