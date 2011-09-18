<?php

class Soflomo_Form extends Zend_Form
{
    /**
     * Constructor
     * 
     * @return Soflomo_Form
     */
    public function __construct ($options = null)
    {
        $this->addPrefixPath('Soflomo_Form_', 'Soflomo/Form/');
        parent::__construct($options);
    }

    /**
     * Clear all values from elements
     * 
     * @return void
     */
    public function clearValues ()
    {
        foreach ($this->getElements() as $element) {
            $element->setValue(null);
        }
    }
}
