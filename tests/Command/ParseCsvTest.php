<?php
namespace App\Tests\Command;

use App\Command\ParseCsv;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ParseCsvTest extends KernelTestCase
{

    public function testExecute(){
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $application->add(new ParseCsv());

        $command = $application->find('app:parse-csv');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => $command->getName(),
            'path' => '/home/ITRANSITION.CORP/a.ferenets/Downloads/test/stock.csv',
        ));

        $output = $commandTester->getDisplay();
        $this->assertContains('0 records have been inserted!', $output);
    }

}
