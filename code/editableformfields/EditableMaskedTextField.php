<?php

/**
 * A text field that allows a user to specify a default value
 *
 * @author Marcus Nyeholt <marcus@silverstripe.com.au>
 */
class EditableMaskedTextField extends EditableTextFieldWithDefault
{

    private static $singular_name = 'Text field with an input mask';
    private static $plural_name = 'Text fields with an input mask';

    private static $defaults = array(
		'TextMask' => ''
	);

    private static $db = array(
        'TextMask' => 'Varchar(255)',
    );

    public function getIcon()
    {
        return USERFORMS_DIR . '/images/maskedtextfield.png';
    }

    private function getMaskedTextField() {
        $field = new MaskedTextField('TextMask', _t('EditableMaskedTextFieldWithDefault.MASK', 'Mask'));
        $field->setInputMask($this->TextMask);

        singleton('DefaultEditableFieldHelper')->updateFormField($this, $field);

        return $field;
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $maskedTextField = $this->getMaskedTextField();
        $maskedTextFieldLabel = new LiteralField('MaskInstructions', _t('EditableMaskedTextFieldWithDefault.MASK_INSTRUCTIONS', '<p>Example: 99/99/9999</p>'
                                                                        . '<ul>'
                                                                        . '<li>a - Represents an alpha character (A-Z,a-z)</li>'
                                                                        . '<li>9 - Represents a numeric character (0-9)</li>'
                                                                        . '<li>* - Represents an alphanumeric character (A-Z,a-z,0-9)</li>'
                                                                        . '</ul>'));
        $fields->addFieldsToTab('Root.Main', CompositeField::create( array($maskedTextField, $maskedTextFieldLabel) ));

        return $fields;
    }

    public function getFormField()
    {
        $field = $this->getMaskedTextField();
        if ($this->Required) {
            // Required validation can conflict so add the Required validation messages
            // as input attributes
            $errorMessage = $this->getErrorMessage()->HTML();
            $field->setAttribute('data-rule-required', 'true');
            $field->setAttribute('data-msg-required', $errorMessage);
        }

        singleton('DefaultEditableFieldHelper')->updateFormField($this, $field);
        return $field;
    }
}
