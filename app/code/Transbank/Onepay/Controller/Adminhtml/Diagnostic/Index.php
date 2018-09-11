<?php
namespace Transbank\Onepay\Controller\Adminhtml\Diagnostic;
 
use \Transbank\Onepay\Model\Config\DiagnosticPDF;

class Index extends \Magento\Backend\App\Action {

    public function __construct(\Magento\Backend\App\Action\Context $context,
                                \Transbank\Onepay\Model\Config\ConfigProvider $configProvider) {
        parent::__construct($context);
        $this->_configProvider = $configProvider;
    }

    /**
     * Render
     * 
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute() {

        $pdf = new DiagnosticPDF($this->_configProvider);

        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetFont('Times','',12);

        // Add a title for the section
        $pdf->Cell(60,15,utf8_decode('Server summary'),0,0,'L');
        $pdf->Ln(15);
        // Add php version
        $pdf->addPHPVersion();
        // Add server software
        $pdf->addServerApi();
        // Add addEcommerceInfo and plugin info
        $pdf->addEcommerceInfo();
        // Add merchant info
        $pdf->addMerchantInfo();
        //Add extension info
        $pdf->addExtensionsInfo();
        $pdf->addLogs();

        $pdf->Output();
    }
}

