<?php
namespace Pfbc\Element;

class TinyMCE extends Textarea
{
    protected $basic;

    public function render()
    {
        echo "<textarea", $this->getAttributes(array("value", "required")), ">";
        if (!empty($this->attributes["value"])) {
            echo $this->attributes["value"];
        }
        echo "</textarea>";
    }

    public function renderJS()
    {
        echo 'tinyMCE.init({ mode: "exact", elements: "', $this->attributes["id"], '", width: "100%"';
        if (!empty($this->basic)) {
            echo ', theme: "simple"';
        } else {
            echo ', theme: "advanced", theme_advanced_resizing: true';
        }
        echo '});';

        $ajax = $this->form->getAjax();
        $id = $this->form->getAttribute("id");
        if (!empty($ajax)) {
            echo 'jQuery("#$id").bind("submit", function() { tinyMCE.triggerSave(); });';
        }
    }

    public function getJSFiles()
    {
        return array(
            $this->form->getResourcesPath() . "/tiny_mce/tiny_mce.js"
        );
    }
}
