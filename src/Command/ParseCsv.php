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
            while (($data = fgetcsv($file)) !== false) {   // walk through file with data processing
                $count += $this->checkAndSaveProduct($data) ? 1 : 0; // processing the row and increasing the number of rows processed
            }
            $output->writeln($count.' records have been inserted!');
        }else{
            $output->writeln('No such file "'.$path.'"');
        }

    }

    protected function checkAndSaveProduct($data){
        if (empty($data[4]) || empty($data[3])){ // checking the conditions for insertion into db
            return false;
        }elseif ($data[4] < 5 && $data[3] < 10){
            return false;
        }elseif($data[4] > 1000){
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
        $product->setStrProductCode($data[0]);
        $product->setStrProductName($data[1]);
        $product->setStrProductDesc($data[2]);
        $product->setDtmAdded( new \DateTime("now"));
        $product->setStmTimestamp(time());
        if ($data[5] === 'yes') {
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

}