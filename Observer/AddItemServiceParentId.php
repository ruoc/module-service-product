<?php

namespace Ruoc\ServiceProduct\Observer;

use Magento\Checkout\Model\Cart;
use Magento\Framework\Event\Observer;
use Magento\Quote\Api\CartRepositoryInterface;

class AddItemServiceParentId implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * AddItemServiceParentId constructor.
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(
        CartRepositoryInterface $cartRepository
    ) {
        $this->cartRepository = $cartRepository;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        /** @var Cart $cart */
        $cart = $observer->getEvent()->getCart();
        $quote = $cart->getQuote();
        if(!empty($quote->getItems())){
            $idToItem = []; $changed = false;
            foreach($quote->getItemsCollection() as $item){
                $idToItem[$item->getProductId()] = $item;
            }
            
            foreach($quote->getItemsCollection() as &$item){
                $parentId = $item->getBuyRequest()->getServiceParentId();
                if(!empty($parentId) && isset($idToItem[$parentId])){
                    $item->setData('service_parent_id', $idToItem[$parentId]->getItemId());
                    $changed = true;
                }
            }
            
            if($changed){
                $this->cartRepository->save($quote);
            }
        }
    }
}
