<?php
namespace Pfbc\View;

use Pfbc\AbstractView,
    Pfbc\ViewInterface,
    Pfbc\AbstractElement;

class RightLabel extends AbstractView implements ViewInterface
{
    protected $class = 'form-horizontal';

    /**
     * @{inherit}
     */
    public function render()
    {
        null !== $this->class && $this->form->appendAttribute('class', $this->class);

        echo '<form', $this->form->getAttributes(), '><fieldset>';
        $this->form->getErrorView()->render();

        $elements = $this->form->getElements();
        $elementSize = sizeof($elements);
        $elementCount = 0;
        for ($e = 0; $e < $elementSize; ++$e) {
            $element = $elements[$e];

            if ($element instanceof Element\Hidden || $element instanceof Element\Html) {
                $element->render();
            } elseif ($element instanceof Element\Button) {
                if ($e == 0 || !$elements[($e - 1)] instanceof Element\Button) {
                    echo '<div class="form-actions">';
                } else {
                    echo ' ';
                }

                $element->render();

                if (($e + 1) == $elementSize || !$elements[($e + 1)] instanceof Element\Button) {
                    echo '</div>';
                }
            } else {
                echo '<div class="control-group"><div class="controls">', $element->render(), $this->renderLabel($element), $this->renderDescriptions($element), '</div></div>';
                ++$elementCount;
            }
        }

        echo '</fieldset></form>';
    }

    /**
     * @{inherit}
     */
    public function renderLabel(AbstractElement $element)
    {
        $label = $element->getLabel();
        if (!empty($label)) {
            echo '<label class="control-label" for="', $element->getAttribute("id"), '">';
            echo $label;
            if ($element->isRequired()) {
                echo '<span class="required"> * </span>';
            }
            echo '</label>';
        }
    }
}
