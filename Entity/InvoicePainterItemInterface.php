<?php

namespace Realtyhub\InvoicePainterBundle\Entity;

interface InvoicePainterItemInterface
{
    public function getInvoicePainterUnitPriceEx();

    public function getInvoicePainterTaxRate();

    public function getInvoicePainterDate();

    public function getInvoicePainterDescription();

    public function getInvoicePainterUnits();
}