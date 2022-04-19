<?php


namespace Ruoc\ServiceProduct\Observer;

use Magento\Framework\Event\Observer;
use Magento\Quote\Api\CartItemRepositoryInterface;

class RemoveServiceProductFromCart implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var CartItemRepositoryInterface 
     */
    protected $quoteItemRepository;
    private $isDeleting;
    public function __construct(
        CartItemRepositoryInterface $quoteItemRepository
    )
    {
        $this->quoteItemRepository = $quoteItemRepository;
    }

    public function execute(Observer $observer)
    {
        if($this->isDeleting){
            return;
        }
        /** @var \Magento\Quote\Model\Quote\Item $item */
        $item = $observer->getEvent()->getQuoteItem();
        if($item){
            $this->isDeleting = true;
            try {
                $serviceParentId = $item->getData('service_parent_id');
                if (empty($serviceParentId)) {
                    $itemId = $item->getItemId();
                    $quote = $item->getQuote();
                    if(empty($quote->getItems())){
                        $this->isDeleting = false;
                        return;
                    }
                    foreach ($quote->getItems() as $quoteItem) {
                        $itemServiceId = $quoteItem->getData('service_parent_id');
                        if ($itemId == $itemServiceId) {
                            $this->quoteItemRepository->deleteById($quote->getId(), $quoteItem->getItemId());
                        }
                    }
                }
            }catch (\Exception $e){
                throw new \Exception($e->getMessage());
            } finally {
                $this->isDeleting = false;
            }
        }
    }
}