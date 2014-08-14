<?php
/**
 * Iceberg Commerce
 *
 * @author     IcebergCommerce
 * @package    IcebergCommerce_VideoGallery
 * @copyright  Copyright (c) 2011 Iceberg Commerce
 */

/**
 * Helper
 * This helper class contains methods not used by video gallery core
 * The methods defined here have been developed for customizations
 */
class IcebergCommerce_VideoGallery_Helper_Extras extends Mage_Core_Helper_Abstract
{	
	
	/**
     * Load Video Gallery info into product
     * useful for product list page
     */
    public function loadGalleryIntoProduct($product)
    {
    	$attrCode = 'video_gallery';
        $value = array();
        $value['videos'] = array();
        $localAttributes = array('label', 'position', 'disabled','description');

        $videoGallery = Mage::getResourceSingleton('videogallery/backend_video')->loadGallery($product);
        
        foreach ($videoGallery as $video) 
        {
            foreach ($localAttributes as $localAttribute) 
            {
                if (is_null($video[$localAttribute])) 
                {
                    $video[$localAttribute] = isset($video[$localAttribute . '_default']) ? $video[$localAttribute . '_default'] : '';
                }
            }
            $value['videos'][] = $video;
        }
        $product->setData($attrCode, $value);
    }
    
    
    /**
     * Get a product's first video
     */
    public function getFirstProductVideo($product)
    {
    	$data = $product->getData();
    	if (!isset($data['video_gallery']))
    	{
			Mage::helper('videogallery/extras')->loadGalleryIntoProduct($product);
    	}
    	
    	$videos = $product->getData('video_gallery');
    	$videoCount = isset($videos['videos']) ? count($videos['videos']) : 0;
		
		if ($videoCount > 0)
		{
			foreach ($videos['videos'] as $v)
			{
				if (!$v['disabled'])
				{
					return $v;
				}
			}
		}
		
		return array();
	}
	
	
	/**
	 * Get a video url
     */
	public function getVideoUrl($product, $video)
	{
		if (isset($video['value']) && isset($video['value_id']) && isset($video['provider']))
		{
			$obj = Mage::helper('videogallery/video')->getVideoByValue($video['value'] , $video['provider']);
			if ($obj)
			{
				$params = array('product_id'=>$product->getId(), 'media_video_id' => $video['value_id']);
				return Mage::getUrl('videogallery/gallery/single/', $params);
			}
		}
		return null;
	}
	
	/**
	 * Get all embed codes
	 * returns array of html
	 */
	public function getAllVideoEmbeds($product)
	{
		$videos = $product->getData('video_gallery'); 
		$videoCount = isset($videos['videos']) ? count($videos['videos']) : 0;
		
		$arr = array();
		
		if ($videoCount > 0)
		{
			foreach ($videos['videos'] as $v)
			{
				if (!$v['disabled'])
				{
					$obj = Mage::helper('videogallery/video')->getVideoByValue($v['value'] , $v['provider']);
					if ($obj)
					{
						$arr[] = $obj->getEmbedCode(600, 400, $autoplay=false);
					}
				}
			}
		}
		return $arr;
	}
}