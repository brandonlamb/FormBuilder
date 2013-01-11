<?php
namespace Pfbc\View;
use Pfbc\AbstractView;
use Pfbc\AbstractElement;

class Vertical extends AbstractView
{
    public function render()
    {
        echo '<form', $this->form->getAttributes(), '>';
        $this->form->getErrorView()->render();

        $elements = $this->form->getElements();
        $elementSize = sizeof($elements);
        $elementCount = 0;
        for ($e = 0; $e < $elementSize; ++$e) {
            $element = $elements[$e];

            if ($element instanceof \Pfbc\Element\Button) {
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
                $this->renderLabel($element);
                $element->render();
                $this->renderDescriptions($element);
                ++$elementCount;
            }
        }

        echo '</form>';
    }

    protected function renderLabel(AbstractElement $element)
    {
        $label = $element->getLabel();
        echo '<label for="', $element->getAttribute("id"), '">';
        if (!empty($label)) {
            if ($element->isRequired()) {
                echo '<span class="required">* </span>';
            }
            echo $label;
        }
        echo '</label>';
    }
}
