<?php


namespace Ruoc\ServiceProduct\Observer;

use Magento\Framework\Event\Observer;
use Magento\Quote\Api\CartItemRepositoryInterface;

class UpdateServiceProductInCart implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var CartItemRepositoryInterface 
     */
    protected $quoteItemRepository;
    private $isUpdating;
    public function __construct(
        CartItemRepositoryInterface $quoteItemRepository
    )
    {
        $this->quoteItemRepository = $quoteItemRepository;
    }

    public function execute(Observer $observer)
    {
        if($this->isUpdating){
            return;
        }
        /** @var \Magento\Quote\Model\Quote\Item $item */
        $item = $observer->getEvent()->getQuoteItem();
        if($item){
            $this->isUpdating = true;
            try {
                $serviceParentId = $item->getData('service_parent_id');
                $itemId = $item->getItemId();
                $qty = $item->getQty();
                $quote = $item->getQuote();
                if(empty($quote->getItems())){
                    $this->isUpdating = false;
                    return;
                }
                if (empty($serviceParentId)) {
                    foreach ($quote->getItems() as $quoteItem) {
                        $itemServiceId = $quoteItem->getData('service_parent_id');
                        $serviceQty = $quoteItem->getQty();
                        if ($itemId == $itemServiceId && $serviceQty > $qty) {
                            $quoteItem->setQty($qty);
                            $this->quoteItemRepository->save($quoteItem);
                        }
                    }
                }else{
                    $parent = $quote->getItemById($serviceParentId);
                    $parentQty = $parent->getQty();
                    if($serviceParentId == $parent->getId() && $qty > $parentQty){
                        $item->setQty($parentQty);
                        $this->quoteItemRepository->save($item);
                    }
                }
            }catch (\Exception $e){
                throw new \Exception($e->getMessage());
            } finally {
                $this->isUpdating = false;
            }
        }
    }
}