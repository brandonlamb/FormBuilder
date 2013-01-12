<?php
namespace Pfbc;

class Form extends AbstractBase
{
	/**
	 * @var array
	 */
	protected $elements = array();

	/**
	 * @var string
	 */
	protected $prefix = "http";

	/**
	 * @var array
	 */
	protected $values = array();

	/**
	 * @var array
	 */
	protected $attributes = array();

	protected $ajax;
	protected $ajaxCallback;
	protected $errorView;
	protected $labelToPlaceholder;
	protected $resourcesPath;

	/**
	 * @var array, Prevents various automated from being automatically applied.
	 * Current options for this array included jQuery, bootstrap and focus.
	 */
	protected $prevent = array();

	/**
	 * @var View
	 */
	protected $view;

	/**
	 * Constructor
	 * @param string $id
	 */
	public function __construct($id = 'pfbc')
	{
		$this->configure(array(
			'action' => basename($_SERVER['SCRIPT_NAME']),
			'id' => preg_replace('/\W/', '-', $id),
			'method' => 'post'
		));

		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
			$this->prefix = 'https';
		}

		// The Standard view class is applied by default and will be used unless a different view is specified in the form's configure method
		if (empty($this->view)) {
			$this->view = new View\SideBySide;
		}

		if (empty($this->errorView)) {
			$this->errorView = new ErrorView\Standard;
		}

		// The resourcesPath property is used to identify where third-party resources needed by the
		// project are located. This property will automatically be set properly if the Pfbc directory
		// is uploaded within the server's document root.  If symbolic links are used to reference the Pfbc
		// directory, you may need to set this property in the form's configure method or directly in thi constructor
		$path = __DIR__ . '/Resources';
		if (strpos($path, $_SERVER['DOCUMENT_ROOT']) !== false) {
			$this->resourcesPath = substr($path, strlen($_SERVER['DOCUMENT_ROOT']));
		} else {
			$this->resourcesPath = '/Pfbc/Resources';
		}
	}

	/**
	 * When a form is serialized and stored in the session, this function prevents any non-essential information from being included
	 */
	public function __sleep()
	{
		return array('attributes', 'elements', 'errorView');
	}

	/**
	 * When ajax is used to submit the form's data, validation errors need to be manually sent back to the form using json
	 * @param string $id
	 */
	public static function renderAjaxErrorResponse($id = 'pfbc')
	{
		$form = self::recover($id);
		if ($form instanceof Form) {
			$form->errorView->renderAjaxErrorResponse();
		}
	}

	/*Valldation errors are saved in the session after the form submission, and will be displayed to the user
	when redirected back to the form.*/
	public static function setError($id, $errors, $element = '')
	{
		if (!is_array($errors)) {
			$errors = array($errors);
		}

		if (empty($_SESSION['pfbc'][$id]['errors'][$element])) {
			$_SESSION['pfbc'][$id]['errors'][$element] = array();
		}

		foreach ($errors as $error) {
			$_SESSION['pfbc'][$id]['errors'][$element][] = $error;
		}
	}

	protected static function setSessionValue($id, $element, $value)
	{
		$_SESSION['pfbc'][$id]['values'][$element] = $value;
	}

	public static function clearErrors($id = 'pfbc')
	{
		if (!empty($_SESSION['pfbc'][$id]['errors'])) {
			unset($_SESSION['pfbc'][$id]['errors']);
		}
	}

	public static function clearValues($id = 'pfbc')
	{
		if (!empty($_SESSION['pfbc'][$id]['values'])) {
			unset($_SESSION['pfbc'][$id]['values']);
		}
	}

	protected static function getSessionValues($id = 'pfbc')
	{
		$values = array();
		if (!empty($_SESSION['pfbc'][$id]['values'])) {
			$values = $_SESSION['pfbc'][$id]['values'];
		}

		return $values;
	}

	/**
	 * Determine if the form is valid
	 * @param string $id
	 * @param bool $clearValues
	 * @return bool
	 */
	public static function isValid($id = 'pfbc', $clearValues = true)
	{
		$valid = true;
		// The form's instance is recovered (unserialized) from the session
		$form = self::recover($id);
		if (empty($form)) {
			return false;
		}

		$data = ($_SERVER['REQUEST_METHOD'] == 'POST') ? $_POST : $_GET;

		// Any values/errors stored in the session for this form are cleared
		self::clearValues($id);
		self::clearErrors($id);

		// Each element's value is saved in the session and checked against any validation rules applied to the element
		if (!empty($form->elements)) {
			foreach ($form->elements as $element) {
				$name = $element->getAttribute('name');
				if (substr($name, -2) == '[]') {
					$name = substr($name, 0, -2);
				}

				// The File element must be handled differently b/c it uses the $_FILES superglobal and not $_GET or $_POST
				if ($element instanceof AbstractElement\File) {
					$data[$name] = $_FILES[$name]['name'];
				}

				if (isset($data[$name])) {
					$value = $data[$name];
					if (is_array($value)) {
						$valueSize = sizeof($value);
						for($v = 0; $v < $valueSize; ++$v)
							$value[$v] = stripslashes($value[$v]);
					} else {
						$value = stripslashes($value);
					}

					self::setSessionValue($id, $name, $value);
				} else {
					$value = null;
				}

				// If a validation error is found, the error message is saved in the session along with the element's name
				if (!$element->isValid($value)) {
					self::setError($id, $element->getErrors(), $name);
					$valid = false;
				}
			}
		}

		// If no validation errors were found, the form's session values are cleared
		if ($valid) {
			if ($clearValues) {
				self::clearValues($id);
			}
			self::clearErrors($id);
		}

		return $valid;
	}

	/**
	 * This method restores the serialized form instance.
	 * @return Form|bool
	 */
	protected static function recover($id)
	{
		return !empty($_SESSION['pfbc'][$id]['form']) ? unserialize($_SESSION['pfbc'][$id]['form']) : false;
	}

	/**
	 * Add an element to the form
	 * @param AbstractElement $element
	 * @return $this
	 */
	public function addElement(AbstractElement $element)
	{
		$element->setForm($this);

		// If the element doesn't have a specified id, a generic identifier is applied.
		$id = $element->getAttribute('id');
		if (empty($id)) {
			$element->setAttribute('id', $this->attributes['id'] . '-element-' . sizeof($this->elements));
		}
		$this->elements[] = $element;

		// For ease-of-use, the form tag's encytype attribute is automatically set if the File element class is added
		if ($element instanceof AbstractElement\File) {
			$this->attributes['enctype'] = 'multipart/form-data';
		}

		return $this;
	}

	/**
	 * Values that have been set through the setValues method, either manually by the developer
	 * or after validation errors, are applied to elements within this method.
	 * @return $this
	 */
	protected function applyValues()
	{
		foreach ($this->elements as $element) {
			$name = $element->getAttribute('name');
			if (isset($this->values[$name])) {
				$element->setAttribute('value', $this->values[$name]);
			} elseif (substr($name, -2) == '[]' && isset($this->values[substr($name, 0, -2)])) {
				$element->setAttribute('value', $this->values[substr($name, 0, -2)]);
			}
		}

		return $this;
	}

	public function getAjax()
	{
		return $this->ajax;
	}

	public function getElements()
	{
		return $this->elements;
	}

	public function getErrorView()
	{
		return $this->errorView;
	}

	public function getPrefix()
	{
		return $this->prefix;
	}

	public function getPrevent()
	{
		return $this->prevent;
	}

	public function getResourcesPath()
	{
		return $this->resourcesPath;
	}

	public function getErrors()
	{
		$errors = array();
		if (session_id() == '')
			$errors[''] = array("Error: The pfbc project requires an active session to function properly.  Simply add session_start() to your script before any output has been sent to the browser.");
		else {
			$errors = array();
			$id = $this->attributes['id'];
			if (!empty($_SESSION['pfbc'][$id]['errors']))
				$errors = $_SESSION['pfbc'][$id]['errors'];
		}

		return $errors;
	}

	/**
	 * Renders the form
	 * @param bool $returnHTML
	 */
	public function render($returnHTML = false)
	{
		if (!empty($this->labelToPlaceholder)) {
			foreach ($this->elements as $element) {
				$label = $element->getLabel();
				if (!empty($label)) {
					$element->setAttribute('placeholder', $label);
					$element->setLabel('');
				}
			}
		}

		$this->view->setForm($this);
		$this->errorView->setForm($this);

		// When validation errors occur, the form's submitted values are saved in a session
		// array, which allows them to be pre-populated when the user is redirected to the form
		$values = self::getSessionValues($this->attributes['id']);
		if (!empty($values))
			$this->setValues($values);
		$this->applyValues();

		if ($returnHTML) {
			ob_start();
		}

		$this->view->render();

		// The form's instance is serialized and saved in a session variable for use during validation
		$this->save();

		if ($returnHTML) {
			$html = ob_get_contents();
			ob_end_clean();

			return $html;
		}
	}

	/*The save method serialized the form's instance and saves it in the session.*/
	protected function save()
	{
		$_SESSION['pfbc'][$this->attributes['id']]['form'] = serialize($this);
	}

	/**
	 * An associative array is used to pre-populate form elements.  The keys of this array correspond with the element names
	 * @param array $values
	 * @return $this
	 */
	public function setValues(array $values)
	{
		$this->values = array_merge($this->values, $values);
		return $this;
	}
}
