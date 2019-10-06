<?php

namespace Realtyhub\InvoicePainterBundle\Entity;

class InvoicePainterItem
{

    protected $date = null;

    protected $description = null;

    protected $taxRate = null;

    protected $unitPriceEx = null;

    protected $unitPriceInc = null;

    protected $units = 1;


    static public function createFromParams($unitPriceEx, $units, $taxRate, $description, \DateTime $date)
    {
        $obj = new self;

        $obj->setUnitPriceEx($unitPriceEx);
        $obj->setUnits($units);
        $obj->setTaxRate($taxRate);
        $obj->setDescription($description);
        $obj->setDate($date);

        return $obj;
    }

    static public function createFromInterface(InvoicePainterItemInterface $invoicePainterItemInterface)
    {
        $obj = new self;

        $obj->setUnitPriceEx($invoicePainterItemInterface->getInvoicePainterUnitPriceEx());
        $obj->setTaxRate($invoicePainterItemInterface->getInvoicePainterTaxRate());
        $obj->setDescription($invoicePainterItemInterface->getInvoicePainterDescription());
        $obj->setDate($invoicePainterItemInterface->getInvoicePainterDate());
        $obj->setUnits($invoicePainterItemInterface->getInvoicePainterUnits());

        return $obj;
    }


    /**
     * @param mixed $unitPriceEx
     */
    public function setUnitPriceEx($unitPriceEx)
    {
        $this->unitPriceEx = $unitPriceEx;
    }

    /**
     * @return mixed
     */
    public function getUnitPriceEx()
    {
        if ($this->unitPriceEx === null)
        {   //amountEx has not been set, but lest see if we can calculate it
            if ($this->unitPriceInc === null || $this->taxRate === null)
            {
                throw new \Exception('Attempting to get amountEx, but it is null, and there is not enough information to calculate it');
            }

            $this->unitPriceEx = $this->unitPriceInc / (1 + $this->taxRate);
        }

        return $this->unitPriceEx;
    }

    /**
     * @param mixed $unitPriceInc
     */
    public function setUnitPriceInc($unitPriceInc)
    {
        $this->unitPriceInc = $unitPriceInc;
    }

    /**
     * @return mixed
     */
    public function getUnitPriceInc()
    {
        if ($this->unitPriceInc === null)
        {   //amountInc has not been set, but lest see if we can calculate it
            if ($this->unitPriceEx === null || $this->taxRate === null)
            {
                throw new \Exception('Attempting to get amountInc, but it is null, and there is not enough information to calculate it');
            }

            $this->unitPriceInc = $this->unitPriceEx * (1 + $this->taxRate);
        }

        return $this->unitPriceInc;
    }

    /**
     * @param mixed $date
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $taxRate
     */
    public function setTaxRate($taxRate)
    {
        $this->taxRate = $taxRate;
    }

    /**
     * @return mixed
     */
    public function getTaxRate()
    {
        return $this->taxRate;
    }

    /**
     * @param mixed $units
     */
    public function setUnits($units)
    {
        $this->units = $units;
    }

    /**
     * @return mixed
     */
    public function getUnits()
    {
        return $this->units;
    }

    /**
     * @return float|int
     * @throws \Exception
     */
    public function getAmountEx() {
        return $this->getUnits() * $this->getUnitPriceEx();
    }

    /**
     * @return float|int
     * @throws \Exception
     */
    public function getAmountInc() {
        return $this->getUnits() * $this->getUnitPriceInc();
    }
}