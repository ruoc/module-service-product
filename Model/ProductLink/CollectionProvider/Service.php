<?php
namespace Ruoc\ServiceProduct\Model\ProductLink\CollectionProvider;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductLink\CollectionProviderInterface;

class Service implements CollectionProviderInterface
{
    /** @var \Ruoc\ServiceProduct\Model\Service */
    protected $serviceModel;

    /**
     * Service constructor.
     * @param \Ruoc\ServiceProduct\Model\Service $serviceModel
     */
    public function __construct(
        \Ruoc\ServiceProduct\Model\Service $serviceModel
    ) {
        $this->serviceModel = $serviceModel;
    }

    /**
     * {@inheritdoc}
     */
    public function getLinkedProducts(Product $product)
    {
        return (array) $this->serviceModel->getServiceProducts($product);
    }
}
