<?php

namespace Realtyhub\InvoicePainterBundle\Twig;

class RealtyhubInvoicePainterExtension extends \Twig_Extension
{

    protected $currencyHtmlEntity;
    protected $currencyHtmlEntitySprintfFormat;

    public function __construct($currencySymbol)
    {
        $lookup['dollar'] = '&#36;|%2$s%1$s';
        $lookup['pound'] = '&pound;|%2$s%1$s';
        $lookup['euro'] = '&euro;|%1$s %2$s';
        $lookup['yen'] = '&yen;|%2$s%1$s';
        $lookup['pln'] = 'z&#322;|%1$s %2$s';

        if (array_key_exists(strtolower($currencySymbol), $lookup) )
        {
            $config = explode('|', $lookup[ $currencySymbol ]);
            $this->currencyHtmlEntity = $config[0];
            $this->currencyHtmlEntitySprintfFormat = $config[1];
        }
        else
        {
            throw new \Exception('Currency symbol not supported, possible options are '.join(',', array_keys($lookup) ) );
        }

    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('money', [$this, 'moneyFilter'], ['is_safe' => ['html'] ]),
            new \Twig_SimpleFilter('percentage', array($this, 'percentageFilter')),
        );
    }

    public function percentageFilter($amount, $decimals = 0)
    {
        $str = round($amount * 100, $decimals);
        return $str.'%';
    }

    public function moneyFilter($amount, $decimals = 2)
    {


        if ($amount < 0)
        {
            $negative = true;
            $str = $amount *  -1;
        }
        else
        {
            $negative = false;
            $str = $amount;
        }

        $str = number_format($str, $decimals, '.', ','); //Note: deliberately not using money_format() as it is lame
        $str = sprintf($this->currencyHtmlEntitySprintfFormat, $str, $this->currencyHtmlEntity); //add currency symbol. Note: use of "is_safe" option for this filter to prevent the already escaped html been escaped again

        if ($negative)
        {
            $str= "({$str})";   //negative numbers shown with brackets around them (Accounting standard)
        }

        return $str;
    }

    public function getName()
    {
        return 'realtyhub_invoice_painter_extension';
    }
}