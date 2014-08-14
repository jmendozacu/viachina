<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_CatalogSearch
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog search query model
 *
 * @method Mage_CatalogSearch_Model_Resource_Query _getResource()
 * @method Mage_CatalogSearch_Model_Resource_Query getResource()
 * @method string getQueryText()
 * @method Mage_CatalogSearch_Model_Query setQueryText(string $value)
 * @method int getNumResults()
 * @method Mage_CatalogSearch_Model_Query setNumResults(int $value)
 * @method int getPopularity()
 * @method Mage_CatalogSearch_Model_Query setPopularity(int $value)
 * @method string getRedirect()
 * @method Mage_CatalogSearch_Model_Query setRedirect(string $value)
 * @method string getSynonymFor()
 * @method Mage_CatalogSearch_Model_Query setSynonymFor(string $value)
 * @method int getDisplayInTerms()
 * @method Mage_CatalogSearch_Model_Query setDisplayInTerms(int $value)
 * @method int getIsActive()
 * @method Mage_CatalogSearch_Model_Query setIsActive(int $value)
 * @method int getIsProcessed()
 * @method Mage_CatalogSearch_Model_Query setIsProcessed(int $value)
 * @method string getUpdatedAt()
 * @method Mage_CatalogSearch_Model_Query setUpdatedAt(string $value)
 *
 * @category    Mage
 * @package     Mage_CatalogSearch
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Custom_SearchByName_Model_Query extends Mage_CatalogSearch_Model_Query
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'searchbyname_query';

    /**
     * Event object key name
     *
     * @var string
     */
    protected $_eventObject = 'searchbyname_query';

    const CACHE_TAG                     = 'SEARCH_QUERY';
    const XML_PATH_MIN_QUERY_LENGTH     = 'catalog/search/min_query_length';
    const XML_PATH_MAX_QUERY_LENGTH     = 'catalog/search/max_query_length';
    const XML_PATH_MAX_QUERY_WORDS      = 'catalog/search/max_query_words';

    /**
     * Retrieve search collection
     *
     * @return Mage_CatalogSearch_Model_Resource_Search_Collection
     */
    public function getSearchCollection()
    {
        return Mage::getResourceModel('searchbyname/search_collection');
    }

    /**
     * Init resource model
     *
     */
    protected function _construct()
    {
        $this->_init('searchbyname/query');
    }

    /**
     * Retrieve collection of suggest queries
     *
     * @return Mage_CatalogSearch_Model_Resource_Query_Collection
     */
    public function getSuggestCollection()
    {
        $collection = $this->getData('suggest_collection');
                if (is_null($collection)) {
            $collection = Mage::getModel('catalog/category')
                ->loadByAttribute('name', 'Brands Category')
                ->setStoreId('6')
                ->getProductCollection()
                ->addAttributeToSelect('name')
                ->addAttributeToFilter('name', array('like' => '%'.$this->getQueryText().'%'))
                ->setOrder('ASC');

            $this->setData('suggest_collection', $collection);
        }
        return $collection;
    }

    public function setAttribute($attribute)
    {
        return $this->setData('attributes', $attribute);
    }

    public function getAttribute()
    {
        return $this->getData('attributes');
    }
}
