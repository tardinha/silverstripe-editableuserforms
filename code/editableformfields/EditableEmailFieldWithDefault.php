<?php

/**
 * An email field that allows a CMS Editor to specify a default value
 *
 * @author Marcus Nyeholt <marcus@silverstripe.com.au>
 */
class EditableEmailFieldWithDefault extends EditableEmailField
{
    private static $singular_name = 'Email field with keyword default';
    private static $plural_name = 'Email fields with keyword default';

    public function getIcon()
    {
        return USERFORMS_DIR . '/images/editableemailfield.png';
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
