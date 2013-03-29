<?php
namespace Pfbc\View;

use Pfbc\AbstractView,
    Pfbc\ViewInterface,
    Pfbc\AbstractElement;

class Stacked extends AbstractView implements ViewInterface
{
    protected $class = 'form-stacked';

    /**
     * @{inherit}
     */
    public function render()
    {
        null !== $this->class && $this->form->appendAttribute('class', $this->class);

        echo '<form', $this->form->getAttributes(), '><fieldset><div class="clearfix">';
        $title = $this->form->getAttribute('title');
        if (!empty($title)) {
            echo '<legend>', $title, '</legend>';
        }
        $this->form->getErrorView()->render();

        $elements = $this->form->getElements();
        $elementSize = sizeof($elements);
        $elementCount = 0;
        for ($e = 0; $e < $elementSize; ++$e) {
            $element = $elements[$e];

            if ($element instanceof Element\Hidden || $element instanceof Element\Html) {
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
                echo '<div class="input">', $this->renderLabel($element), $element->render(), $this->renderDescriptions($element), '</div>';
                ++$elementCount;
            }
        }

        echo '</div></fieldset></form>';
    }

    /**
     * @{inherit}
     */
    public function renderLabel(AbstractElement $element)
    {
        $label = $element->getLabel();
        if (!empty($label)) {
            echo '<label for="', $element->getAttribute('id'), '">';
            echo $element->isRequired() ? '<span class="required">' . $label . ' * </span>' : $label;
            echo '</label>';
        }
    }
}
