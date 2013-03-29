<?php
namespace Pfbc\View;

use Pfbc\AbstractView,
    Pfbc\ViewInterface,
    Pfbc\AbstractElement;

class Inline extends AbstractView implements ViewInterface
{
    protected $class = 'form-inline';

    /**
     * @{inherit}
     */
    public function render()
    {
        null !== $this->class && $this->form->appendAttribute('class', $this->class);

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

    /**
     * @{inherit}
     */
    public function renderLabel(AbstractElement $element)
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
