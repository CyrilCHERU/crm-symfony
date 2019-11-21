<?php

namespace App\Controller\Operation;

use App\Entity\Invoice;
use Doctrine\ORM\EntityManagerInterface;

class InvoiceAmountIncrement
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(Invoice $data)
    {
        $data->setAmount($data->getAmount() + 100);
        $this->em->flush();
        return $data;
    }
}
