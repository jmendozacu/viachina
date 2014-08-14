<?php


class OnTap_Merchandiser_Model_Resource_Enterprise_Search_Engine extends Enterprise_Search_Model_Resource_Engine
{
    
    /**
     * Adds support for Smart Merchandiser with Magento Enterprise
     *
     * @param string $adapterName
     * @return object
     */
    protected function _getAdapterModel($adapterName)
    {
        
        /**
        *   Lets figure out is Solr is in use. if so, return the Enterprise core Singleton.
        *   Otherwise, jump in with our search model singleton.
        */
        
        $modelToUse = '';
        switch ($adapterName):
            case 'solr':
            default:
                if (extension_loaded('solr')):
                    $modelToUse'enterprise_search/adapter_phpExtension';
                else:
                    $modelToUse='merchandiser/enterprise_search_adapter_httpStream';
                endif;
                break;
        endswitch;

        return Mage::getSingleton($modelToUse);
    }

    
}
