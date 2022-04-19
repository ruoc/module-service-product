<?php

namespace Ruoc\ServiceProduct\Observer;

use Magento\Checkout\Model\Cart;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;

class AddServiceProductToCart implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;
    
    /**
     * @var RequestInterface
     */
    protected $request;

    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        RequestInterface $request
    ) {
        $this->messageManager = $messageManager;
        $this->request = $request;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        /** @var Cart $cart */
        $cart = $observer->getEvent()->getCart();
        $productId = $this->request->getParam('product', false);
        $qty = $this->request->getParam('qty', 1);
        if($productId === false){
            return $this;
        }
        $serviceProducts = $this->request->getParam('service_product');
        $selected = $this->request->getParam('selected');
        if (!empty($serviceProducts)) {
            $serviceProducts = array_filter($serviceProducts);
            $totalServiceQty = 0;
            foreach($serviceProducts as $id => $qty){
                if(!isset($selected[$id])){
                    unset($serviceProducts[$id]);
                }else{
                    $totalServiceQty += $qty;
                }
            }
            $serviceParent = NULL;
            foreach($cart->getItems() as $item){
                if($item->getProductId() == $productId){
                    $serviceParent = $item;
                    break;
                }
            }
            if(!empty($serviceParent)){
                foreach($cart->getItems() as &$item){
                    if(isset($serviceProducts[$item->getProductId()])){
                        $totalServiceQty += $item->getQty();
                    }
                }
                if($totalServiceQty > $serviceParent->getQty() + $qty){
                    $this->messageManager->addErrorMessage(__('You are not allowed to purchase more service items than main products'));
                    return $this;
                }
            }
            foreach ($serviceProducts as $id => $qty){
                $cart->addProduct($id, ['qty' => $qty, 'service_parent_id' => $productId]);
            }
        }
    }
}
