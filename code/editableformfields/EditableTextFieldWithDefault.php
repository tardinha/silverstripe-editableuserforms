<?php

/**
 * A text field that allows a CMS Editor to specify a default value
 *
 * @author Marcus Nyeholt <marcus@silverstripe.com.au>
 */
class EditableTextFieldWithDefault extends EditableTextField
{

    private static $singular_name = 'Text field with keyword default';
    private static $plural_name = 'Text fields with keyword default';

    public function getIcon()
    {
        return USERFORMS_DIR . '/images/editabletextfield.png';
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        return $fields;
    }

    public function getFormField()
    {
        $field = parent::getFormField();
        $field->setValue(null);
        singleton('DefaultEditableFieldHelper')->updateFormField($this, $field);
        return $field;
    }
}
