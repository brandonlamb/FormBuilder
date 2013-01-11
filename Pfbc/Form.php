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

        $session = new \Phalcon\Session\Bag('pfbc-' . $id);

        if (empty($session->errors[$element])) {
            $session->errors[$element] = array();
        }

        foreach ($errors as $error) {
            $session->errors[$element][] = $error;
        }
    }

    protected static function setSessionValue($id, $element, $value)
    {
        $session = new \Phalcon\Session\Bag('pfbc-' . $id);
        $session->values[$element] = $value;
    }

    public static function clearErrors($id = 'pfbc')
    {
        $session = new \Phalcon\Session\Bag('pfbc-' . $id);
        if (!empty($session->errors)) {
            unset($session->errors);
        }
    }

    public static function clearValues($id = 'pfbc')
    {
        $session = new \Phalcon\Session\Bag('pfbc-' . $id);
        if (!empty($session->values)) {
            unset($session->values);
        }
    }

    protected static function getSessionValues($id = 'pfbc')
    {
        $values = array();
        $session = new \Phalcon\Session\Bag('pfbc-' . $id);
        if (!empty($session->values)) {
            $values = $session->values;
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
        $session = new \Phalcon\Session\Bag('pfbc-' . $id);
        return !empty($session->form) ? unserialize($session->form) : false;
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
        if (session_id() == '') {
            $errors[''] = array("Error: The pfbc project requires an active session to function properly.  Simply add session_start() to your script before any output has been sent to the browser.");
        } else {
            $errors = array();
            $id = $this->attributes['id'];
            $session = new \Phalcon\Session\Bag('pfbc-' . $id);
            if (!empty($session->errors)) {
                $errors = $session->errors;
            }
        }

        return $errors;
    }

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

        /*When validation errors occur, the form's submitted values are saved in a session
        array, which allows them to be pre-populated when the user is redirected to the form.*/
        $values = self::getSessionValues($this->attributes['id']);
        if (!empty($values))
            $this->setValues($values);
        $this->applyValues();

        if ($returnHTML) {
            ob_start();
        }

        $this->renderCSS();
        $this->view->render();
        $this->renderJS();

        // The form's instance is serialized and saved in a session variable for use during validation
        $this->save();

        if ($returnHTML) {
            $html = ob_get_contents();
            ob_end_clean();

            return $html;
        }
    }

    protected function renderCSS()
    {
        $this->renderCSSFiles();

        echo '<style type="text/css">';
        $this->view->renderCSS();
        $this->errorView->renderCSS();

        foreach ($this->elements as $element) {
            $element->renderCSS();
        }
        echo '</style>';
    }

    protected function renderCSSFiles()
    {
        $urls = array();
        if (!in_array('bootstrap', $this->prevent)) {
            $urls[] = $this->prefix . "://netdna.bootstrapcdn.com/twitter-bootstrap/2.2.1/css/bootstrap-combined.min.css";
        }

        foreach ($this->elements as $element) {
            $elementUrls = $element->getCSSFiles();
            if (is_array($elementUrls))
                $urls = array_merge($urls, $elementUrls);
        }

        /*This section prevents duplicate css files from being loaded.*/
        if (!empty($urls)) {
            $urls = array_values(array_unique($urls));
            foreach ($urls as $url) {
                echo '<link type="text/css" rel="stylesheet" href="', $url, '"/>';
            }
        }
    }

    protected function renderJS()
    {
        $this->renderJSFiles();

        echo '<script type="text/javascript">';
        $this->view->renderJS();
        foreach ($this->elements as $element)
            $element->renderJS();

        $id = $this->attributes['id'];

        echo 'jQuery(document).ready(function() {';

        /*When the form is submitted, disable all submit buttons to prevent duplicate submissions.*/
        echo <<<JS
        jQuery("#$id").bind("submit", function() {
            jQuery(this).find("input[type=submit]").attr("disabled", "disabled");
        });
JS;

        /*jQuery is used to set the focus of the form's initial element.*/
        if (!in_array("focus", $this->prevent))
            echo 'jQuery("#', $id, ' :input:visible:enabled:first").focus();';

        $this->view->jQueryDocumentReady();
        foreach ($this->elements as $element)
            $element->jQueryDocumentReady();

        /*For ajax, an anonymous onsubmit javascript function is bound to the form using jQuery.  jQuery's
        serialize function is used to grab each element's name/value pair.*/
        if (!empty($this->ajax)) {
            echo <<<JS
            jQuery("#$id").bind("submit", function() {
JS;

            /*Clear any existing validation errors.*/
            $this->errorView->clear();

            echo <<<JS
                jQuery.ajax({
                    url: "{$this->attributes["action"]}",
                    type: "{$this->attributes["method"]}",
                    data: jQuery("#$id").serialize(),
                    success: function(response) {
                                                response = JSON.parse(response);
                        if (response != undefined && typeof response == "object" && response.errors) {
JS;

            $this->errorView->applyAjaxErrorResponse();

            echo <<<JS
                            jQuery("html, body").animate({ scrollTop: jQuery("#$id").offset().top }, 500 );
                        } else {
JS;


            /*A callback function can be specified to handle any post submission events.*/
            if (!empty($this->ajaxCallback))
                echo $this->ajaxCallback, "(response);";

            /*After the form has finished submitting, re-enable all submit buttons to allow additional submissions.*/
            echo <<<JS
                        }
                        jQuery("#$id").find("input[type=submit]").removeAttr("disabled");
                    }
                });

                return false;
            });
JS;
        }

        echo '}); </script>';
    }

    protected function renderJSFiles()
    {
        $urls = array();
        if (!in_array("jQuery", $this->prevent)) {
            $urls[] = $this->prefix . "://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js";
        }

        if (!in_array('bootstrap', $this->prevent)) {
            $urls[] = $this->prefix . "://netdna.bootstrapcdn.com/twitter-bootstrap/2.2.1/js/bootstrap.min.js";
        }

        foreach ($this->elements as $element) {
            $elementUrls = $element->getJSFiles();
            if (is_array($elementUrls)) {
                $urls = array_merge($urls, $elementUrls);
            }
        }

        /*This section prevents duplicate js files from being loaded.*/
        if (!empty($urls)) {
            $urls = array_values(array_unique($urls));
            foreach ($urls as $url) {
                echo '<script type="text/javascript" src="', $url, '"></script>';
            }
        }
    }

    /**
     * The save method serialized the form's instance and saves it in the session
     * @return $this
     */
    protected function save()
    {
        $this->session = new \Phalcon\Session\Bag('pfbc-' . $this->attributes['id']);
        $this->session->form = serialize($data);
        return $this;
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
