<?php

class EditableMultiFileField extends EditableFormField
{
    private static $singular_name = 'Multiple file upload field';

    private static $plural_name = 'Multiple file upload fields';

    private static $db = array(
        'AllowMultipleUploads' => 'Boolean',
    );

    private static $defaults = array(
        'AllowMultipleUploads' => 1,
    );

    private static $has_one = array(
        'Folder' => 'Folder'
    );

    public function getIcon()
    {
        return USERFORMS_DIR . '/images/editablefilefield.png';
    }

    public function getCmsFields()
    {
        $fields = parent::getCmsFields();

        $folder = $this->Folder();
        $treeField = UserformsTreeDropdownField::create(
            'FolderID',
            _t('EditableUploadField.SELECTUPLOADFOLDER', 'Select upload folder'),
            "Folder"
        );
        $treeField->setValue($folder);
        $fields->addFieldToTab('Root.Main', $treeField);

        $fields->addFieldToTab('Root.Main', CheckboxField::create($this->getSettingName("AllowMultipleUploads"), 'Allow multiple uploads'));

        return $fields;
    }

    public function getFormField()
    {
        $field = FileAttachmentField::create($this->Name, $this->Title);

        $folder = $this->Folder();
        if(!empty($folder->ID)) {
            $field->setFolderName(
                preg_replace("/^assets\//", "", $folder->Filename)
            );
        }

        if ($this->MultipleUploads == 1) {
            $field->setMultiple(true);
        }

        if ($this->Required) {
            // Required validation can conflict so add the Required validation messages
            // as input attributes
            $errorMessage = $this->getErrorMessage()->HTML();
            $field->setAttribute('data-rule-required', 'true');
            $field->setAttribute('data-msg-required', $errorMessage);
        }

        return $field;
    }

}
