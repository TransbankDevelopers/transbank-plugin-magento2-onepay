<?php

namespace Transbank\Onepay\Block\System\Config;

class DiagnosticButton extends \Magento\Config\Block\System\Config\Form\Field {

    const BUTTON_TEMPLATE = 'system/config/diagnostic_button.phtml';
 
    protected function _prepareLayout() {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate(static::BUTTON_TEMPLATE);
        }
        return $this;
    }

    /**
     * Render button
     *
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element) {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

     /**
     * Get the button and scripts contents
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element) {
        return $this->_toHtml();
    }

    /**
     * return the url for get diagnostic pdf (Implementation Controller: Transbank\Onepay\Controller\Adminhtml\Diagnostic\Index.php)
     */
    public function getUrlDiagnostic() {
        return $this->getUrl('admin_onepay/diagnostic');
    }
}