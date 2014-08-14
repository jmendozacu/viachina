<?php

class MW_Dailydeal_Block_Adminhtml_System_Config_Messagequantity extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return '
            <button type="button" title="' . $this->__("Reset default message") . '" onclick="reset_message_deal_qty()">' . $this->__("Reset default message") . '</button>
        ';
    }

}