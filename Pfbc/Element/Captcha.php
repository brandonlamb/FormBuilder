<?php
namespace Pfbc\Element;
use Pfbc\AbstractElement;

class Captcha extends AbstractElement
{
    /**
     * @var string
     */
    protected $privateKey = '6LcazwoAAAAAAD-auqUl-4txAK3Ky5jc5N3OXN0_';

    /**
     * @var string
     */
    protected $publicKey = '6LcazwoAAAAAADamFkwqj5KN1Gla7l4fpMMbdZfi';

    /**
     * Constructor
     * @param string $label
     * @param array $properties
     */
    public function __construct($label = '', array $properties = null)
    {
        parent::__construct($label, 'recaptcha_response_field', $properties);
    }

    public function render()
    {
        $this->validation[] = new \Pfbc\Validation\Captcha($this->privateKey);
        require_once(__DIR__ . '/../Resources/recaptchalib.php');
        echo recaptcha_get_html($this->publicKey);
    }
}
