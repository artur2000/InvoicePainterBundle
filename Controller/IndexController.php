<?php


namespace Realtyhub\InvoicePainterBundle\Controller;

use Realtyhub\InvoicePainterBundle\Entity\InvoicePainterDataContainer;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndexController extends AbstractController
{

    protected $templating;
    protected $dompdf;
    protected $defaultTaxShortName;
    protected $invoiceTemplate;

    public function __construct(EngineInterface $templating, Pdf $dompdf, $defaultTaxShortName, $invoiceViewTemplate)
    {
        $this->templating = $templating;
        $this->dompdf = $dompdf;
        $this->defaultTaxShortName = $defaultTaxShortName;
        $this->invoiceViewTemplate = $invoiceViewTemplate;
    }

    public function paintAction(InvoicePainterDataContainer $invoiceData)
    {

        if ($invoiceData->getTaxShortName() === null)
        {
            $invoiceData->setTaxShortName($this->defaultTaxShortName);
        }

        $html = $this->renderView(
            $this->invoiceViewTemplate,
            array(  'invoiceData' => $invoiceData )
        );

        // Generate the pdf
        if (is_file($this->get('kernel')->getProjectDir() . '/vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64')) {
            $this->dompdf->setBinary($this->get('kernel')->getProjectDir() . '/vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64');
        } else if ('/usr/local/bin/wkhtmltopdf') {
            $this->dompdf->setBinary('/usr/local/bin/wkhtmltopdf');
        } else {
            throw new \Exception('wkhtmltopdf binary is missing, see https://packagist.org/packages/knplabs/knp-snappy');
        }

        $pdfContents = $this->dompdf->getOutputFromHtml($html, []);

        return new Response(
            $pdfContents,
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'attachment; filename="'.$this->generateDownloadFileName($invoiceData).'"'
            )
        );

    }

    /**
     * adapted from Symfony/Bundle/FrameworkBundle/Controller/Controller
     */
    public function renderView($view, array $parameters = array()): string
    {
        return $this->templating->render($view, $parameters);
    }

    protected function generateDownloadFileName(InvoicePainterDataContainer $invoiceData)
    {
        return 'invoice_'.$invoiceData->getInvoiceNumber().'_'.$invoiceData->getInvoiceDate()->format('Y-m-d').'.pdf';
    }
}