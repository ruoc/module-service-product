<?php
namespace Ruoc\ServiceProduct\Model\Product;

use Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection as ProductCollection;

class Link extends \Magento\Catalog\Model\Product\Link
{
    const LINK_TYPE_SERVICE = 7;

    /**
     * @return $this
     */
    public function useServiceLinks()
    {
        $this->setLinkTypeId(self::LINK_TYPE_SERVICE);
        return $this;
    }
    
}
