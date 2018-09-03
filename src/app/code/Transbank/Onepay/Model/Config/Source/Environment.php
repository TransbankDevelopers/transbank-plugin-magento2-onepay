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

class Environment implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 'INTEGRACION', 'label' => __('INTEGRACION')],
                ['value' => 'CERTIFICACION', 'label' => __('CERTIFICACION')],
                ['value' => 'PRODUCCION', 'label' => __('PRODUCCION')]];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return ['INTEGRACION' => __('INTEGRACION'), 'CERTIFICACION' => __('CERTIFICACION'), 'PRODUCCION' => __('PRODUCCION')];
    }
}
