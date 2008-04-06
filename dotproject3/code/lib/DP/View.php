<?php

/**
 * The base view class which can contain any number of elements including child views.
 * 
 * The DP_View class is the most general representation of a HTML box. It can be subclassed to produce any 
 * element of display. Each DP_View object must have a unique identifier (required by the constructor), which 
 * will be used by the DP_Template system to assign the DP_View's output to the location specified in the template.
 * 
 * @package dotproject
 * @subpackage system
 * @author ebrosnan
 * @version not.even.alpha
 * @todo Assess whether a full instance of smarty is required to insert subviews/child views.
 */
class DP_View {
	/**
	 * @var mixed $id This view's identifier.
	 */
	protected $id;
	/**
	 * @var integer $width Desired width of this element.
	 */
	protected $width;
	/**
	 * @var string $align Alignment of this view to the parent element
	 */	
	protected $align;
	/**
	 * @var integer $parent_view_id Identifier of the parent view object.
	 */
	protected $parent_view_id;
	/**
	 * @var array $child_views Array of child views inside this view.
	 */
	protected $child_views;
	/**
	 * @var array $html_options associative array for html options
	 */
	protected $html_attribs;
	/**
	 * @var array $html_attribs_allowed array of allowed html attributes
	 */
	protected $html_attribs_allowed;
	/**
	 * @var integer PLACE_BOTTOM place the child view at the bottom of this DP_View
	 */
	const PLACE_BOTTOM = 0;
	/**
	 * @var integer PLACE_TOP place the child view at the top of this DP_View
	 */
	const PLACE_TOP = 1; 
	
	public function __construct($id) {
		$this->id = $id;
		$this->parent_view_id = -1;
		$this->html_attribs = Array();
		$this->child_views = Array();
		$this->html_attribs_allowed = Array('width','align','height');
	}

	/**
	 * Magic method allows setting of html options using attributes.
	 * 
	 * Alignment will be applied to the container, either the parent table cell or a
	 * div that wraps the output of this view.
	 * 
	 * 
	 */
	public function __set($name, $value) {
		if (in_array($name, $this->html_attribs_allowed)) {
			$this->html_attribs[$name] = $value;
		}
	}
	
	public function __unset($name) {
		if (in_array($name, $this->html_attribs_allowed)) {
			unset($this->html_attribs[$name]);
		}		
	}
	
	public function __isset($name) {
		if (in_array($name, $this->html_attribs_allowed)) {
			return isset($this->html_attribs[$name]);
		}		
	}
	
	/**
	 * Call render when converted to string
	 * 
	 * @return string HTML output
	 */
	public function __toString() {
		return $this->render();	
	}

	/**
	 * Get the template identifier for this view object.
	 * 
	 * @return string Unique string identifying this view object.
	 */
	public function id() {
		return $this->id;
	}
	
	/**
	 * Get the identifier of the parent view.
	 * 
	 * @return string Unique string identifying the parent view object.
	 */
	public function parentId() {
		return $this->parent_view_id;
	}
	
	/**
	 * Set the identifier of the parent view.
	 * 
	 * @param integer $id Unique string identifying the parent view object.
	 */
	public function setParentViewId($id) {
		$this->parent_view_id = $id;
	}
	
	/**
	 * Add a child view.
	 * 
	 * Add a DP_View which will be inserted inside this view.
	 * @todo Determine standardised behaviour for views that cannot contain other views. Eg. listview
	 */
	public function add(DP_View $view, $location = DP_View::PLACE_TOP) {
		$view->setParentViewId($this->id());
		$this->child_views[] = $view;
	}
	
	/**
	 * Get the desired width of this view object.
	 * 
	 * @return integer Width of this view object in any acceptable HTML unit.
	 */
	public function width() {
		return $this->html_attribs['width'];
	}
	
	/**
	 * Get the desired height of this view object.
	 * 
	 */
	public function height() {
		return $this->html_attribs['height'];
	}
	
	/**
	 * Get the desired alignment of this object inside the parent.
	 * 
	 */
	public function align() {
		return $this->html_attribs['align'];
	}
	
	/**
	 * Set the desired alignment of this object inside the parent.
	 * 
	 * @param integer $align Alignment, one of DP_View::ALIGN_LEFT, DP_View::ALIGN_CENTER or DP_View::ALIGN_RIGHT
	 */
	public function setAlign($align) {
		$this->align = $align;
	}
	
	/**
	 * Set display options using an associative array.
	 * 
	 * Valid keys are: width, height, align
	 * 
	 * @param array $options associative array of options.
	 */
	public function setHTMLAttribs($attribs) {
		$this->html_attribs = $attribs;
	}
	
	/**
	 * Render this DP_View object's contents into HTML
	 * All DP_View objects must implement this method.
	 * 
	 * @return string HTML output of this DP_View object.
	 * @todo Force implementation of this method for subclasses using an abstract
	 */
	public function render() {
	}
	
	/**
	 * Render all child DP_View objects into HTML.
	 */
	protected function renderChildren() {
		$output = "";
		foreach ($this->child_views as $child) {
			$output .= $child->render();
		}
		return $output;
	}
	
	/**
	 * Render the child with the given ID string.
	 * 
	 * @param string $id The identifier of the child view.
	 * @return string The HTML output of the child view's render() method.
	 */
	protected function renderChildWithId($id) {
		$output = '';
		foreach ($this->child_views as $child) {
			if ($child->Id() == $id) {
				$output .= $child->render();
			}
		}
		return $output;
	}
}
?>