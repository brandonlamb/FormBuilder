<?php
namespace Pfbc\View;
use Pfbc\AbstractView;
use Pfbc\AbstractElement;

class SideBySide extends AbstractView
{
    protected $class = "form-horizontal";

    public function render()
    {
        $this->_form->appendAttribute("class", $this->class);

        echo '<form', $this->_form->getAttributes(), '><fieldset>';
        $this->_form->getErrorView()->render();

        $elements = $this->_form->getElements();
        $elementSize = sizeof($elements);
        $elementCount = 0;
        for ($e = 0; $e < $elementSize; ++$e) {
            $element = $elements[$e];

            if ($element instanceof \Pfbc\Element\Hidden || $element instanceof \Pfbc\Element\HTML) {
                $element->render();
            } elseif ($element instanceof \Pfbc\Element\Button) {
                if ($e == 0 || !$elements[($e - 1)] instanceof \Pfbc\Element\Button) {
                    echo '<div class="form-actions">';
                } else {
                    echo ' ';
                }

                $element->render();

                if (($e + 1) == $elementSize || !$elements[($e + 1)] instanceof \Pfbc\Element\Button) {
                    echo '</div>';
                }
            } else {
                echo '<div class="control-group">', $this->renderLabel($element), '<div class="controls">', $element->render(), $this->renderDescriptions($element), '</div></div>';
                ++$elementCount;
            }
        }

        echo '</fieldset></form>';
    }

    protected function renderLabel(AbstractElement $element)
    {
        $label = $element->getLabel();
        if (!empty($label)) {
            echo '<label class="control-label" for="', $element->getAttribute("id"), '">';
            if ($element->isRequired()) {
                echo '<span class="required">* </span>';
            }
            echo $label, '</label>';
        }
    }
}
