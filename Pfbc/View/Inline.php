<?php
namespace Pfbc\View;
use Pfbc\AbstractView;
use Pfbc\AbstractElement;

class Inline extends AbstractView
{
    protected $class = 'form-inline';

    public function render()
    {
        $this->form->appendAttribute('class', $this->class);

        echo '<form', $this->form->getAttributes(), '>';
        $this->form->getErrorView()->render();

        $elements = $this->form->getElements();
        $elementSize = sizeof($elements);
        $elementCount = 0;
        for ($e = 0; $e < $elementSize; ++$e) {
            if ($e > 0) {
                echo ' ';
            }
            $element = $elements[$e];
            echo $this->renderLabel($element), ' ', $element->render(), $this->renderDescriptions($element);
            ++$elementCount;
        }

        echo '</form>';
    }

    protected function renderLabel(AbstractElement $element)
    {
        $label = $element->getLabel();
        if (!empty($label)) {
            echo '<label for="', $element->getAttribute("id"), '">';
            if ($element->isRequired()) {
                echo '<span class="required">* </span>';
            }
            echo $label;
            echo '</label>';
        }
    }
}
