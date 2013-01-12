<?php
namespace Pfbc\Element;
use Pfbc\Validation\RegExp;

class Date extends Textbox
{
    protected $attributes = array(
        'type' => 'date',
        'pattern' => '\d{4}-\d{2}-\d{2}'
    );

    public function __construct($label, $name, array $properties = null)
    {
        $this->attributes['placeholder'] = 'YYYY-MM-DD (e.g. ' . date('Y-m-d') . ')';
        $this->attributes['title'] = $this->attributes['placeholder'];

        parent::__construct($label, $name, $properties);
    }

    public function render()
    {
        $this->validation[] = new RegExp('/' . $this->attributes['pattern'] . '/', 'Error: The %element% field must match the following date format: ' . $this->attributes['title']);
        parent::render();
    }
}
