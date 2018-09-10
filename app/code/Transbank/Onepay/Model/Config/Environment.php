<?php

namespace Transbank\Onepay\Model\Config;

class Environment implements \Magento\Framework\Option\ArrayInterface {

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
        return [['value' => 'TEST', 'label' => __('INTEGRACION')],
                ['value' => 'LIVE', 'label' => __('PRODUCCION')]];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray() {
        return ['INTEGRACION' => __('TEST'), 'PRODUCCION' => __('LIVE')];
    }
}
