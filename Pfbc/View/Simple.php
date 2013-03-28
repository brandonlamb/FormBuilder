<?php
namespace Pfbc\View;

use Pfbc\AbstractView,
    Pfbc\ViewInterface,
    Pfbc\AbstractElement;

class Simple extends AbstractView implements ViewInterface
{
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
            echo $element->render();
            ++$elementCount;
        }

        echo '</form>';
    }
}
