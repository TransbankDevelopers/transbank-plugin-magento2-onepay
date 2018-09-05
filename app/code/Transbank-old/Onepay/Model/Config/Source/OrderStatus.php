<?php

/**
* @author     Allware Ltda. (http://www.allware.cl)
* @copyright  2017 Transbank S.A. (http://www.transbank.cl)
* @license    GNU LGPL
* @version    1.0
*/

/**
 * Used in creating options for value selection
 *
 */
namespace Transbank\Onepay\Model\Config\Source;

class OrderStatus implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 'processing', 'label' => __('processing')],
                ['value' => 'pending_payment', 'label' => __('pending_payment')],
                ['value' => 'payment_review', 'label' => __('payment_review')],
                ['value' => 'complete', 'label' => __('complete')],
                ['value' => 'canceled', 'label' => __('canceled')]];

    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return ['processing' => __('processing'),'pending_payment' => __('pending_payment'),'payment_review' => __('payment_review'),'complete' => __('complete'),'canceled' => __('canceled')];
    }
    
}
