<?php

/**
 * @author marcus
 */
class DefaultEditableFieldHelper
{

    /**
     * @note EditableFormField is a DataObject
     */
    public function updateFormField(EditableFormField $editableFormField, FormField $field = null, $fieldType = 'TextField')
    {
        // grab default value
        $default = $editableFormField->getField('Default');
        if (($field->Value() === null) && strpos($default, '$') !== false) {
            preg_match('/(.*)\$(([A-Za-z]+)\.([A-Za-z+]+))(.*)/', $default, $matches);
            if(!empty($matches[2])) {
                $match = $matches[2];
                if (strpos($match, '.')) {
                    // if it's a compound, lets get an object to use
                    list($className, $property) = explode('.', $match);
                    $item = null;
                    switch ($className) {
                        case 'Member':
                                $item = Member::currentUser();
                                break;
                        default:
                                $controller = Controller::curr();
                                if ($controller instanceof ContentController) {
                                    $item = $controller->data();
                                }
                                break;
                    }
                    if (($item instanceof DataObject) && $item->hasField($property)) {
                        /* @var $item DataObject */
                        $default = $item->$property;
                    } else {
                        // scrub it
                        $default = "";
                    }
                }
            }
        }

        $field->setValue($default);
        return $field;
    }
}
