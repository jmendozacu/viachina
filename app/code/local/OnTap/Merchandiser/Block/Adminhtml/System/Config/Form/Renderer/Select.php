<?php
class OnTap_Merchandiser_Block_Adminhtml_System_Config_Form_Renderer_Select extends Mage_Core_Block_Abstract {

    public function __construct($attributes = array()) {
        parent::__construct($attributes);
        $this->_prepareOptions();
    }

    protected function _toHtml() {
        return $this->getElementHtml();
    }

    protected function _escape($string) {
        return htmlspecialchars($string, ENT_COMPAT);
    }

    public function getElementHtml() {
        $elementHTML = '<select class="'.$this->getClass().'" name="' . $this->getInputName() . '" style="width:'. $this->getwidth().'px" >';
        foreach ($this->getValues() as $attr_value => $attr_name):
            $elementHTML.='<option value="'.$attr_value.'">'.$attr_name.'</option>';
        endforeach;
        $elementHTML.='</select>';
        return $elementHTML;
    }

    protected function _prepareOptions() {
        $option_value = $this->getValues();
        if (empty($option_value)):
            $options_available = $this->getOptions();
            if (is_array($options_available)):
                $option_value = array();
                foreach ($options_available as $opt_value => $opt_label):
                    $option_value[] = array('value' => $opt_value, 'label' => $opt_label);
                endforeach;
            elseif (is_string($options_available)):
                $option_value = array(array('value' => $options_available, 'label' => $options_available));
            endif;
            $this->setValues($option_value);
        endif;
    }

    public function getHtmlAttributes() {
        return array('title', 'class', 'style', 'onclick', 'onchange', 'disabled', 'readonly', 'tabindex');
    }

    public function addClass($class) {
        $oldClass = $this->getClass();
        $this->setClass($oldClass . ' ' . $class);
        return $this;
    }


}
