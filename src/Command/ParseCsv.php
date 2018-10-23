<?php

declare(strict_types=1);

namespace App\Command;

use App\Parser;
use App\ProductsSave;
use Doctrine\Bundle\DoctrineBundle\Command\DoctrineCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ParseCsv extends DoctrineCommand
{

    protected function configure()
    {
        $this
            ->setName('app:parse-csv')
            ->setDescription('Parse csv file.')
            ->setHelp('This command parse csv file')
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
        $parser = new Parser($path, $output);
        $columns = $parser->getColumns();
        $output = $parser->getOutput();
        if (!empty($columns)) {
            $saver = new ProductsSave($this->getContainer()->get('doctrine')->getManager('default'), $columns, $output);
            $inserted_count = $saver->getInsertedCount();
            $output = $saver->getOutput();
            $output->writeln($inserted_count.' records have been inserted!');
        } else {
            $output->writeln('No data to process');
        }
    }

}