<?php

namespace Ruoc\ServiceProduct\Plugin;

use Magento\Quote\Model\Quote\Item\ToOrderItem;
use Magento\Sales\Model\Order\Item as OrderItem;

/**
 * Class for converting data from quote item to order item
 */
class CopyParentServiceId
{
    public function afterConvert(
        ToOrderItem $subject,
        $result,
        $item
    ): OrderItem {
        $result->setData('service_parent_id', $item->getData('service_parent_id'));
        return $result;
    }
}
