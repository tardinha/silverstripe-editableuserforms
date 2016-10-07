<?php

class EditableMultiFileField extends EditableFormField
{
    private static $singular_name = 'Multiple file upload field';

    private static $plural_name = 'Multiple file upload fields';

    private static $db = array(
        'AllowMultipleUploads' => 'Boolean',
        'ParallelUploads' => 'Int',
        'AllowedFileExtensions' => 'Varchar(255)',
        'ImagesOnly' => 'Boolean',
        'FilterByMimeTypeAsWell' => 'Boolean',
    );

    private static $defaults = array(
        'AllowMultipleUploads' => 1,
        'ParallelUploads' => 2,
        'AllowedFileExtensions' => 'jpg,gif,png,jpeg,webp',
        'ImagesOnly' => 0,
        'FilterByMimeTypeAsWell' => 0,
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
        $fields->addFieldToTab('Root.Main', CheckboxField::create("AllowMultipleUploads", "Allow multiple uploads"));
        $fields->addFieldToTab('Root.Main', NumericField::create("ParallelUploads", "Allow parallel uploads (if multiple uploads enabled)"));
        $fields->addFieldToTab('Root.Main', TextField::create('AllowedFileExtensions','Allowed File Extensions e.g ".gif, .jpg"'));
        $fields->addFieldToTab('Root.Main', CheckboxField::create("ImagesOnly", "Allow only images"));

        $fields->removeByName('FilterByMimeTypeAsWell');
        // $fields->addFieldToTab('Root.Main', CheckboxField::create("FilterByMimeTypeAsWell", "Additionally filter uploads by mime type"));
        return $fields;
    }

    /*
     * Event handler called before writing to the database.
     */
    public function onBeforeWrite()
    {
        parent::onBeforeWrite();

        // demung file extensions provided
        $extensions = $this->filterAllowedFileExtensions();
        // set whatever extensions we get back
        $this->AllowedFileExtensions = implode(",", $this->prefixExtensions($extensions));

        return TRUE;
    }

    /**
     * @returns array
     */
    protected function filterAllowedFileExtensions() {
        $allowed_image_extensions = array(".gif",".jpg",".jpeg",".png",".webp");
        $allowed_extensions = array(".gif",".jpg",".jpeg",".png");
        // you probably never want these uploaded to your server. If you do, extend this field and override the method
        $banned_extensions = array(".css",".php",".php5",".php4",".php3",".phtml",".js",".html",".xml",".xhtml",".exe");

        if($this->ImagesOnly == 1) {
            $extensions = $allowed_image_extensions;
        } else if ($this->AllowedFileExtensions != "") {
            // CMS editor saved the extensions allowed, probably wrong/munged formatting
            $extensions = explode(",", $this->AllowedFileExtensions);
            foreach($extensions as $k=>$extension) {
                // remove unacceptable characters and prefix with a . as the FileAttachmentField requires this
                $extensions[$k] = preg_replace("/[^a-zA-Z0-9]+/", "", $extension);
            }
        } else {
            // fallback just choose allowed_extensions
            $extensions = $allowed_extensions;
        }

        // remove anything the user did wrong that cannot be accepted
        $extensions = array_diff($extensions, $banned_extensions);

        return $extensions;
    }

    private function prefixExtensions(array $extensions) {
        foreach($extensions as $k=>$extension) {
            $extensions[$k] = "." . ltrim($extension, ".");
        }
        return $extensions;
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

        if ($this->AllowMultipleUploads == 1) {
            $field->setMultiple(TRUE);
        }

        $extensions = $this->filterAllowedFileExtensions();
        $field->setAcceptedFiles( $this->prefixExtensions($extensions) );

        // https://github.com/unclecheese/silverstripe-dropzone/issues/58 :(
        /**
        if($this->FilterByMimeTypeAsWell == 1) {
            $mimeTypes = Config::inst()->get('HTTP', 'MimeTypes');
            if(!empty($mimeTypes) && is_array($mimeTypes)) {
                // grab everything from mimeTypes that is present as a key in the extensions array
                $acceptedMimeTypes = array_intersect_key($mimeTypes, array_flip($extensions));
                $field->setAcceptedMimeTypes( $acceptedMimeTypes );
            }
        }
        */

        if($this->ParallelUploads > 0) {
            $field->setParallelUploads( $this->ParallelUploads );
        } else {
            // default 2, a good number
            $field->setParallelUploads( 2 );
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

    /**
     * Return the value for the database, link to the file is stored as a
     * relation so value for the field can be null.
     *
     * @return string
     */
    public function getValueFromData($data)
    {
        $val = isset($data[$this->Name]) ? $data[$this->Name] : null;
        return is_array($val) ? implode(',', $val) : $val;
    }
    public function getSubmittedFormField()
    {
        return new SubmittedMultiFileField();
    }

}
