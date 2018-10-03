<?php
/**
 * Created by PhpStorm.
 * User: a.ferenets
 * Date: 2.10.18
 * Time: 16.16
 */

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

    protected function execute(InputInterface $input, OutputInterface $output){
        $path = $input->getArgument('path');


        if (($file = fopen($path, "r")) !== false) { // checking for existence and opening a file
            $count = 0;
            $isHeader = true;
            while ($data = fgetcsv($file)) {   // walk through file with data processing
                if ($isHeader){
                    if (!self::checkHeader($data)){
                        $output->writeln("The required columns were not found in the file! Check the file.");
                        break;
                    }else
                        $isHeader = false;
                }else {
                    $count += $this->checkAndSaveProduct($data) ? 1 : 0; // processing the row and increasing the number of rows processed
                }
            }
            $output->writeln($count.' records have been inserted!');
        }else{
            $output->writeln('No such file "'.$path.'"');
        }

    }

    protected function checkAndSaveProduct($data){
        if (empty($data[$this->columns[$this->requiredColumns['cost']]]) || empty($data[$this->columns[$this->requiredColumns['stock']]])){ // checking the conditions for insertion into db
            return false;
        }elseif ($data[$this->columns[$this->requiredColumns['cost']]] < 5 && $data[$this->columns[$this->requiredColumns['stock']]] < 10){
            return false;
        }elseif($data[$this->columns[$this->requiredColumns['cost']]] > 1000){
            return false;
        }

        /* @var $em EntityManager*/
        $em = $this->getContainer()->get('doctrine')->getManager('default'); // Load default entity manager

        if (!$em->isOpen()) { // check is manager closed and reload if need
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
        $product->setStmTimestamp(time());
        if ($data[$this->columns[$this->requiredColumns['disc']]] === 'yes') {
            $product->setDtmDiscontinued(new \DateTime("now"));
        }

        try { // trying saving into db
            $em->persist($product);
            $em->flush();
            return true;
        } catch (\Exception $e) {
            echo 'Error! ',  $e->getMessage(), "\n";
            return false;
        }
    }

    protected function checkHeader($data){ // checking the availability of necessary columns and creating an array of matches
        foreach ($this->requiredColumns as $code => $requiredColumn){
            $key = array_search($requiredColumn, $data);
            if ($key !== false){
                $this->columns[$requiredColumn] = $key;
            }else{
                return false;
            }
        }
        return true;
    }

}