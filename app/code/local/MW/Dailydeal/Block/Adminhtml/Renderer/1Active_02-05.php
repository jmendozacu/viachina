<?php

/**
 * Version 1 :
 *  - View 4 color follow magento
 */
class MW_Dailydeal_Block_Adminhtml_Renderer_Active extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {

        $return = '';
        $active = $row->getData('active');
        $actives = MW_Dailydeal_Model_Status::getStatusTimeOptionArray();
        
        if ($active == MW_Dailydeal_Model_Status::STATUS_TIME_QUEUED) {
            $return = 
            '<span class="grid-severity-minor">
                <span>' . $actives[MW_Dailydeal_Model_Status::STATUS_TIME_QUEUED] . '</span>
            </span>';
        }
        elseif ($active == MW_Dailydeal_Model_Status::STATUS_TIME_RUNNING) {
            $return = 
            '<span class="grid-severity-notice">
                <span>' . $actives[MW_Dailydeal_Model_Status::STATUS_TIME_RUNNING] . '</span>
            </span>';
        }
        elseif ($active == MW_Dailydeal_Model_Status::STATUS_TIME_DISABLED) {
            $return = 
            '<span class="grid-severity-critical">
                <span>' . $actives[MW_Dailydeal_Model_Status::STATUS_TIME_DISABLED] . '</span>
            </span>';
        }
        elseif ($active == MW_Dailydeal_Model_Status::STATUS_TIME_ENDED) {
            $return = 
            '<span class="grid-severity-major">
                <span>' . $actives[MW_Dailydeal_Model_Status::STATUS_TIME_ENDED] . '</span>
            </span>';
        }

        return $return;
    }

}