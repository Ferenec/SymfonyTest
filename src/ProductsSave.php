<?php

declare(strict_types=1);

namespace App;


use App\Entity\ProductData;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Output\OutputInterface;

class ProductsSave
{

    /* @var array*/
    public $products;

    /* @var OutputInterface*/
    public $output;

    /* @var EntityManager*/
    protected $em;

    /* @var integer*/
    protected $total_inserted = 0;

    /**
     * ProductsSave constructor.
     * @param array $products
     * @param OutputInterface|null $output
     */
    public function __construct(EntityManager $em, $products = [], OutputInterface $output = null)
    {
        $this->em = $em;
        $this->products = $products;
        $this->output = $output;
    }

    /**
     * @return int
     * @throws \Doctrine\ORM\ORMException
     */
    public function getInsertedCount()
    {
        $this->saveProducts();
        return $this->total_inserted;
    }

    /**
     * @return null|OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     */
    protected function saveProducts()
    {
        foreach ($this->products as $product) {
            if (!$this->em->isOpen()) {
                $this->em = $this->em->create(
                    $this->em->getConnection(),
                    $this->em->getConfiguration()
                );
            }

            $product = new ProductData($product);

            try {
                $this->em->persist($product);
                $this->em->flush();
                $this->total_inserted++;
            } catch (\Exception $e) {
                $this->output->writeln('Error! ' . $e->getMessage());
            }
        }
    }

}