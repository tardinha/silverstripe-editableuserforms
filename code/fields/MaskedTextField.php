<?php

/**
 * A text field that supports masking
 *
 * @author Marcus Nyeholt <marcus@silverstripe.com.au>
 */
class MaskedTextField extends TextField
{

    private $default_classes = array('text');

    /**
     * A Mask used for describing how the form should appear
     *
     * a - Represents an alpha character (A-Z,a-z)
     * 9 - Represents a numeric character (0-9)
     * * - Represents an alphanumeric character (A-Z,a-z,0-9)
     *
     * @see http://digitalbush.com/projects/masked-input-plugin/
     *
     * @var String
     */
    protected $inputMask;

    protected $inCMS = FALSE;

    /**
     * @todo I'm sure there is a nicer way to do this, we want to stop the jQuery being implemented in the CMS
     */
    public function setInCms($in)
    {
        $this->inCMS = $in;
    }

    public function getInCms()
    {
        return $this->inCMS;
    }

    /**
     * Sets the input mask
     *
     * @param String $mask
     */
    public function setInputMask($mask)
    {
        $this->inputMask = $mask;
    }

    public function getInputMask()
    {
        return $this->inputMask;
    }

    public function Field($properties = array())
    {
        // ensure the text class is present
        $this->addExtraClass('text');

        $tag = parent::Field($properties);

        if(!$this->getInCms()) {
            // add in the logic for the masking
            Requirements::javascript('editableuserforms/javascript/jquery.maskedinput-1.4.1.min.js');
            $id = $this->id();
            $mask = addslashes($this->inputMask);
            $js = <<<JS
(function ($) {
	$().ready(function () {
		$('#$id').mask('$mask');
	});
})(jQuery);
JS;
            Requirements::customScript($js, $id . 'JS');
        }

        return $tag;
    }
}
