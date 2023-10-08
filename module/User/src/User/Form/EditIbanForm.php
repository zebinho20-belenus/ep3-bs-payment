<?php

namespace User\Form;

use Zend\Form\Form;
use Zend\InputFilter\Factory;

class EditIbanForm extends Form
{

    public function init()
    {
        $this->setName('eif');

        $this->add(array(
            'name' => 'eif-iban',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'eif-iban',
                'style' => 'width: 235px;',
            ),
            'options' => array(
                'notes' => 'If you like to pay bookings via SEPA mandate,<br>you can store your IBAN here',
            ),
        ));

        $this->add(array(
            'name' => 'eif-submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Update IBAN',
                'class' => 'default-button',
            ),
        ));

        /* Input filters */

        $factory = new Factory();

        $this->setInputFilter($factory->createInputFilter(array(
            'eif-iban' => array(
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'message' => 'Please type your IBAN here',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 16,
                            'message' => 'This IBAN is somewhat short ...',
                        ),
                    ),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'max' => 42,
                            'message' => 'This IBAN is somewhat long ...',
                        ),
                    ),
                    array(
                        'name' => 'Regex',
                        'options' => array(
                            'pattern' => '/^[A-Z]{2}\d{2} ?(?:(?:\w|\d| )){10,37}$/',
                            'message' => 'This IBAN contains invalid characters - sorry',
                        ),
                    ),
                ),
            ),
        )));
    }

}
