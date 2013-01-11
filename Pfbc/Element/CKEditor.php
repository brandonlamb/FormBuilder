<?php
namespace Pfbc\Element;

class CKEditor extends Textarea
{
    protected $basic;

    public function render()
    {
        echo '<textarea', $this->getAttributes(array('value', 'required')), '>';
        if (!empty($this->_attributes["value"])) {
            echo $this->_attributes["value"];
        }
        echo '</textarea>';
    }

    public function renderJS()
    {
        if (!empty($this->basic)) {
            echo <<<JS
var basicConfig = {
    height: 100,
    plugins: 'about,basicstyles,clipboard,list,indent,enterkey,entities,link,pastetext,toolbar,undo,wysiwygarea',
    forcePasteAsPlainText : true,
    removeButtons: 'Anchor,Underline,Strike,Subscript,Superscript',
    toolbarGroups: [
        { name: 'document',	   groups: [ 'mode', 'document', 'doctools' ] },
        { name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
        { name: 'forms' },
        { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
        { name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align' ] },
        { name: 'links' },
        { name: 'insert' },
        { name: 'styles' },
        { name: 'colors' },
        { name: 'tools' },
        { name: 'others' },
        { name: 'about' }
    ]
};
JS;
        }

        echo 'CKEDITOR.replace("', $this->_attributes["id"], '"';
        if (!empty($this->basic)) {
            echo ', basicConfig';
        }
        echo ');';

        $ajax = $this->form->getAjax();
        $id = $this->form->getAttribute("id");
        if (!empty($ajax)) {
            echo 'jQuery("#', $id, '").bind("submit", function() { CKEDITOR.instances["', $this->_attributes["id"], '"].updateElement(); });';
        }
    }

    public function getJSFiles()
    {
        return array($this->form->getResourcesPath() . '/ckeditor/ckeditor.js');
    }
}
