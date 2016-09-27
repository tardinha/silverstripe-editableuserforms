<?php

/**
 * @author marcus
 */
class DefaultEditableFieldHelper
{

    private static function processRegex($value) {
        // need to support all variants of blah $Member.Surname foo $Member.FirstName blah $Something.Else
        $regex = '/(\$([A-Za-z]+)(\.?)([A-Za-z]?)+)/';
        $result = preg_match_all($regex, $value, $matches);
        if(!empty($matches[0]) && is_array($matches[0])) {
            foreach($matches[0] as $match) {
                if (strpos($match, '.')) {
                    // if it's a compound, lets get an object to use
                    list($className, $property) = explode('.', $match);
                    $item = null;
                    $className = ltrim($className, "$");
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
                        $value = str_replace($match, $item->$property, $value);
                    } else {
                        // scrub it as we don't support it
                        $value = str_replace($match, "scrubbed", $value);
                    }

                } else {
                    // not currently supporting just a straight $Foo - what's the scope?
                    $value = str_replace($match, "", $value);
                }
            }
        }
        // remove multiple spaces that may have crept in
        $value = preg_replace('/\s{2}/', " ", $value);
        return $value;
    }

    /**
     * @note EditableFormField is a DataObject
     */
    public static function updateFormField(EditableFormField $editableFormField, FormField $field = null, $fieldType = 'TextField')
    {
        // grab default value
        $default = $editableFormField->getField('Default');
        if (($field->Value() === null) && strpos($default, '$') !== false) {
            $default = self::processRegex($default);
        }

        $field->setValue($default);
        return $field;
    }
}
