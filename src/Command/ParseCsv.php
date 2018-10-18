<?php

namespace App\Command;

use App\Entity\ProductData;
use Doctrine\Bundle\DoctrineBundle\Command\DoctrineCommand;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ParseCsv extends DoctrineCommand {

    protected $requiredColumns = [
        'code'  =>'Product Code',
        'name'  => 'Product Name',
        'desc'  => 'Product Description',
        'stock' => 'Stock',
        'cost'  => 'Cost in GBP',
        'disc'  => 'Discontinued',
    ];

    protected $columns;

    protected function configure()
    {
        $this
            ->setName('app:parse-csv')
            ->setDescription('Parse csv file.')
            ->setHelp('This command paese csv file')
            ->addArgument('path', InputArgument::REQUIRED, 'Path to csv file')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     * @throws \Doctrine\ORM\ORMException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getArgument('path');

        try {
            $file = fopen($path, "r");  // checking for existence and opening a file
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
            return;
        }
        $inserted = 0;
        $isHeader = true;
        while ($data = fgetcsv($file)) {   // walk through file with data processing
            if ($isHeader){
                if (!self::checkHeader($data))
                {
                    $output->writeln("The required columns were not found in the file! Check the file.");
                    return;
                } else
                    $isHeader = false;
            } else {
                $inserted += $this->checkAndSaveProduct($data, $output) ? 1 : 0;
            }
        }
        $output->writeln($inserted.' records have been inserted!');
    }

    /**
     * @param $data
     * @param OutputInterface $output
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     */
    protected function checkAndSaveProduct($data,OutputInterface $output)
    {
        if (empty($data[$this->columns[$this->requiredColumns['cost']]])
            || empty($data[$this->columns[$this->requiredColumns['stock']]])){
            return false;
        } elseif ($data[$this->columns[$this->requiredColumns['cost']]] < 5
            && $data[$this->columns[$this->requiredColumns['stock']]] < 10){
            return false;
        } elseif ($data[$this->columns[$this->requiredColumns['cost']]] > 1000){
            return false;
        }

        /* @var $em EntityManager*/
        $em = $this->getContainer()->get('doctrine')->getManager('default');

        if (!$em->isOpen()) {
            $em = $em->create(
                $em->getConnection(),
                $em->getConfiguration()
            );
        }

        $product = new ProductData();
        $product->setStrProductCode($data[$this->columns[$this->requiredColumns['code']]]);
        $product->setStrProductName($data[$this->columns[$this->requiredColumns['name']]]);
        $product->setStrProductDesc($data[$this->columns[$this->requiredColumns['desc']]]);
        $product->setDtmAdded( new \DateTime("now"));
        $product->setStmTimestamp(new \DateTime("now"));
        if ($data[$this->columns[$this->requiredColumns['disc']]] === 'yes') {
            $product->setDtmDiscontinued(new \DateTime("now"));
        }

        try { // trying saving into db
            $em->persist($product);
            $em->flush();
            return true;
        } catch (\Exception $e) {
            $output->writeln('Error! '.  $e->getMessage());
            return false;
        }
    }

    /**
     * @param $data
     * @return bool
     */
    protected function checkHeader($data)
    {
        foreach ($this->requiredColumns as $code => $requiredColumn){
            $key = array_search($requiredColumn, $data);
            if ($key !== false){
                $this->columns[$requiredColumn] = $key;
            } else {
                return false;
            }
        }
        return true;
    }

}