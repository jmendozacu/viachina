<?php

class MW_Dailydeal_TestController extends Mage_Core_Controller_Front_Action
{

    public function indexAction()
    {
        $methods = get_class_methods(get_class($this));
        $result = array();
        $action = array();
        $home_url = Mage::helper('core/url')->getHomeUrl();
        $current_url = Mage::helper('core/url')->getCurrentUrl();


        foreach ($methods as $method) {
            if (MW_Dailydeal_Helper_Toolasiaconnect::isValidPattern("/_Action+$/i", $method)) {
                $temp = rtrim($method, "Action");
                $result[] = $current_url . '/' . $temp;
                $action[] = $temp;
            }
        }
        for($i = 0 ; $i< count($action); $i++){
            echo "<a href=\"{$result[$i]}\">{$action[$i]}</a>";
            echo '<br />';
        }
    }

    public function autoSendMail_Action(){
		try {
			MW_Dailydeal_Helper_Data::setConfigSendMailAdminNotification(MW_Dailydeal_Model_Status::SEND_MAIL_NEVER);
			MW_Dailydeal_Model_Business::autoSendMail();
            MW_Dailydeal_Helper_Data::setConfigSendMailAdminNotification(MW_Dailydeal_Model_Status::SEND_MAIL_NEVER);
		} catch (Exception $ex) {
			die ('Email can not send!');
        }
    }

    public function cronjob_sendMail_Action(){
        // Check system will have deal running tomorrow
        $result = MW_Dailydeal_Model_Business::autoSendMail();
        
        if($result == 1){
            echo 'Send mail success';
        }
    }
    
   public function cronjob_autoGenerateDeal_Action(){
        // Generate deal
        MW_Dailydeal_Model_Business::autoGenerateDeal();

        // Check system will have deal running tomorrow
        MW_Dailydeal_Model_Business::autoSendMail();
   }
   
   public function cronjob_autoDisableProduct_Action(){
        // disable Product if deal expire time
        MW_Dailydeal_Model_Business::autoDisableProduct();
   }
   
   
    
	public function remove_module_Action(){
		$installer = Mage::getSingleton('core/resource_setup');
        $resource = Mage::getSingleton('core/resource');
        $conn = Mage::getModel('core/resource')->getConnection('core_write');
        
        // Module
        $sql = "DELETE FROM `".$resource->getTableName('core/resource')."` WHERE `code` LIKE 'dailydeal_setup'";
        $conn->query($sql);
        
        // Table
        $sql = "
        DROP TABLE IF EXISTS {$resource->getTableName('mw_dailydeal')};
        DROP TABLE IF EXISTS {$resource->getTableName('mw_deal_scheduler_product')};
        DROP TABLE IF EXISTS {$resource->getTableName('mw_deal_scheduler')};
        ";
        $conn->query($sql);
        
        // Attribute
        $setup = new Mage_Eav_Model_Entity_Setup();
        $setup->removeAttribute ('catalog_product', 'activedeal');
	} 
 /* */
}
