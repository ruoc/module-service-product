<?php
namespace Ruoc\ServiceProduct\Block\Product\View;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\View\Element\Template;
use \Ruoc\ServiceProduct\Model\Service as ServiceModel;
class Service extends \Magento\Catalog\Block\Product\View{
    
    protected $service;
    protected $registry;
    
    public function __construct(
        ServiceModel $service,
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Customer\Model\Session $customerSession,
        ProductRepositoryInterface $productRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        array $data = []
    )
    {
        $this->service = $service;
        parent::__construct($context, $urlEncoder, $jsonEncoder, $string, $productHelper, $productTypeConfig, $localeFormat, $customerSession, $productRepository, $priceCurrency, $data);
    }
    
    public function getServiceProducts(): array
    {
        $product = $this->getProduct();
        $services = $this->service->getServiceProducts($product);
        foreach ($services as $k => $service){
            if($service->getStatus() == Status::STATUS_DISABLED){
                unset($services[$k]);
            }
        }
        return $services;
    }
}