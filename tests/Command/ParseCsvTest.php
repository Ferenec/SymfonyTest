<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\ParseCsv;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ParseCsvTest extends KernelTestCase
{

    public function testExecute()
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->add(new ParseCsv());
        $dirPath = dirname(__DIR__);

        $command = $application->find('app:parse-csv');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => $command->getName(),
            'path' => $dirPath.'/files/stock.csv',
        ));

        $output = $commandTester->getDisplay();
        $this->assertContains('0 records have been inserted!', $output);

        $commandTester->execute(array(
            'command'  => $command->getName(),
            'path' => '/file',
        ));

        $output = $commandTester->getDisplay();
        $this->assertContains('failed to open stream: No such file or directory', $output);

        $commandTester->execute(array(
            'command'  => $command->getName(),
            'path' => $dirPath.'/files/invalid_file',
        ));

        $output = $commandTester->getDisplay();
        $this->assertContains('The required columns were not found in the file! Check the file.', $output);

        $commandTester->execute(array(
            'command'  => $command->getName(),
            'path' => '/',
        ));

        $output = $commandTester->getDisplay();
        $this->assertContains('No data to process', $output);
    }

}
