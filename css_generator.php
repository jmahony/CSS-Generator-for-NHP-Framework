<?php

add_action('wp_head', 'generate_styles');

function generate_styles() {
	$options = get_option('themed');

	$styleIndex = array(
		'0a' => array(
			'selectorType' => '.',
			'selector'     => 'testing',
			'property'     => 'background-color'
		),
		'0b' => array(
			'selectorType' => '.',
			'selector'     => 'testing',
			'property'     => 'color'
		),
		'1a' => array(
			'selectorType' => '.',
			'selector'     => 'testing2',
			'property'     => 'background-color'
		),
		'1b' => array(
			'selectorType' => '.',
			'selector'     => 'testing2',
			'property'     => 'color'
		)
	);

	$Stylesheet = new CssStyleSheet();

	foreach($options as $key => $value) {
		if (array_key_exists($key, $styleIndex)) {
			$CssAttribute = new CssAttribute($styleIndex[$key]['selectorType'], $styleIndex[$key]['selector']);
			$CssAttribute->addProperty($styleIndex[$key]['property'], $value);
			$Stylesheet->addAttribute($CssAttribute);
		}
	}

	echo $Stylesheet->toString();
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
	 * @author 
	 **/
	public function toString() {
		$css = '';
		$css = '<style type="text/css" media="screen">';

		foreach ($this->attributes as $attribute) {
			$css .= $attribute->toString();
		}

		$css .= '</style>';

		return $css;
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
	 * @author 
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
	 * @param selector String
	 * @author 
	 **/
	private function hasAttribute($selector) {
		return array_key_exists($selector, $this->attributes);
	}
} // END class 

/**
 * CssAttribute
 * 
 * Stores the selector, selectorType and properties of a CSS attribute
 *
 * @package default
 * @author 
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
	 * selectorType
	 *
	 * Holds the selector type part of the CSS attribute,
	 * e.g. the '.'' part of .heading { }
	 *
	 * @var String
	 **/
	private $selectorType;

	/**
	 * properties
	 *
	 * Holds the properties part of the CSS attribute,
	 * e.g. anything between { } part of .heading { }
	 * Example of array format
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
	 * @param selectorType, selector
	 * @author 
	 **/
	function __construct($selectorType = '.', $selector = null) {
		$this->selector     = $selector;
		$this->selectorType = $selectorType;
		$this->properties   = array();
	}

	/**
	 * addProperty
	 *
	 * Add a property to the properties array, e.g.
	 * addProperty('color','red')
	 *
	 * @return void
	 * @param type, value
	 * @author 
	 **/
	public function addProperty($type = null, $value = null){
		$this->properties[$type] = $value;
	}

	/**
	 * toString
	 * 
	 * Converts the properties to a printable string
	 * 
	 * @return String
	 * @author 
	 **/
	public function toString() {
		$attribute = '';
		$attribute .= $this->selectorType . $this->selector . '{';

		foreach ($this->properties as $property => $value) {
			$attribute .= $property . ':' . $value . ';';
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
	 * @author 
	 **/
	public function merge(CssAttribute $CssAttribute) {
		$properties = $CssAttribute->getProperties();
		$this->properties = array_merge($this->properties, $properties);
	}

	/**
	 * getProperty
	 * 
	 * Returns the properties array
	 * 
	 * @return Array
	 * @author 
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
	 * @author 
	 **/
	public function getSelector() {
		return $this->selector;
	}
} // END class 
?>