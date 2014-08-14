<?php
/**
 * Backend for serialized array data
 *
 */
class Mymonki_Ship2pay_Model_System_Config_Backend_Serialized_Ship2pay extends Mage_Adminhtml_Model_System_Config_Backend_Serialized
{
    /**
     * Unset array element with '__empty' key
     *
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        
        $rows = count($value['shipping_method']);
        
        for($i=0; $i<$rows; $i++)
        {
        	if($value['shipping_method'][$i] == '__empty' || $value['payment_method'][$i] == '__empty')	
        	{
        		unset($value['shipping_method'][$i]);
        		unset($value['payment_method'][$i]);
        	}
        }
        
        $this->setValue($value);
        parent::_beforeSave();
    }
}
