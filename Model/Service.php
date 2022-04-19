<?php
namespace Ruoc\ServiceProduct\Model;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Link\Collection;
use Magento\Framework\DataObject;
use Ruoc\ServiceProduct\Model\Product\Link;

class Service extends DataObject
{
    /**
     * Product link instance
     *
     * @var Product\Link
     */
    protected $linkInstance;

    /**
     * Service constructor.
     * @param Link $productLink
     * @param $data
     */
    public function __construct(
        Link $productLink,
        array $data = []
    ) {
        parent::__construct($data);
        $this->linkInstance = $productLink;
    }

    /**
     * Retrieve link instance
     *
     * @return  Product\Link
     */
    public function getLinkInstance()
    {
        return $this->linkInstance;
    }

    /**
     * Retrieve array of Service products
     *
     * @param Product $currentProduct
     * @return array
     */
    public function getServiceProducts(Product $currentProduct)
    {
        if (!$this->hasServiceProducts()) {
            $products = [];
            $collection = $this->getServiceProductCollection($currentProduct);
            foreach ($collection as $product) {
                $products[] = $product;
            }
            $this->setServiceProducts($products);
        }
        return $this->getData('service_products');
    }

    /**
     * Retrieve service products identifiers
     *
     * @param Product $currentProduct
     * @return array
     */
    public function getServiceProductIds(Product $currentProduct)
    {
        if (!$this->hasServiceProductIds()) {
            $ids = [];
            foreach ($this->getServiceProducts($currentProduct) as $product) {
                $ids[] = $product->getId();
            }
            $this->setServiceProductIds($ids);
        }
        return $this->getData('service_product_ids');
    }

    /**
     * Retrieve collection service product
     *
     * @param Product $currentProduct
     * @return \Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection
     */
    public function getServiceProductCollection(Product $currentProduct)
    {
        $collection = $this->getLinkInstance()->useServiceLinks()->getProductCollection()->setIsStrongMode();
        $collection->addAttributeToSelect('name')
            ->addAttributeToSelect('thumbnail')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('special_price')
            ->addAttributeToSelect('status');
        $collection->setProduct($currentProduct)->setPositionOrder();
        return $collection;
    }

    /**
     * Retrieve collection service link
     *
     * @param Product $currentProduct
     * @return Collection
     */
    public function getServiceLinkCollection(Product $currentProduct)
    {
        $collection = $this->getLinkInstance()->useServiceLinks()->getLinkCollection();
        $collection->setProduct($currentProduct);
        $collection->addLinkTypeIdFilter();
        $collection->addProductIdFilter();
        $collection->joinAttributes();
        return $collection;
    }
}
