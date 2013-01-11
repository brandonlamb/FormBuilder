<?php
namespace Pfbc\Element;

class jQueryUIDate extends Textbox
{
    protected $attributes = array(
        "type" => "text",
        "autocomplete" => "off"
    );

    protected $jQueryOptions;

    public function getCSSFiles()
    {
        return array(
            $this->form->getPrefix() . "://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/smoothness/jquery-ui.css"
        );
    }

    public function getJSFiles()
    {
        return array(
            $this->form->getPrefix() . "://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"
        );
    }

    public function jQueryDocumentReady()
    {
        parent::jQueryDocumentReady();
        echo 'jQuery("#', $this->attributes["id"], '").datepicker(', $this->jQueryOptions(), ');';
    }

    public function render()
    {
        $this->validation[] = new \Pfbc\Validation\Date;
        parent::render();
    }
}
