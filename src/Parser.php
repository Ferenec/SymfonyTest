<?php

declare(strict_types=1);

namespace App;


use Symfony\Component\Console\Output\OutputInterface;

class Parser
{
    /* @var string */
    public $path;

    /* @var resource */
    private $file;

    /* @var array*/
    protected $columns;

    /* @var array*/
    protected $keys;

    /* @var OutputInterface*/
    public $output;

    /* @var array*/
    protected $required_columns = [
        'code'  => 'Product Code',
        'name'  => 'Product Name',
        'desc'  => 'Product Description',
        'stock' => 'Stock',
        'cost'  => 'Cost in GBP',
        'disc'  => 'Discontinued',
    ];

    /**
     * Parser constructor.
     * @param string $path
     * @param OutputInterface|null $output
     */
    public function __construct($path = '', OutputInterface $output = null)
    {
        $this->path = $path;
        $this->output = $output;
    }

    /**
     * @param string $path
     * @return array
     */
    public function getColumns()
    {
        if (!$this->openFile())
            return [];
        else {
            if (!$this->prepareColumns())
                return [];
            else
                return $this->columns;
        }
    }

    /**
     * @return null|OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @param $row
     */
    protected function fillColumns($row)
    {
        if ((empty($row[$this->keys[$this->required_columns['cost']]]) || empty($row[$this->keys[$this->required_columns['stock']]]))
            || ($row[$this->keys[$this->required_columns['cost']]] < 5 && $row[$this->keys[$this->required_columns['stock']]] < 10)
            || ($row[$this->keys[$this->required_columns['cost']]] > 1000))
            return;

        $this->columns[] = [
            'code' => $row[$this->keys[$this->required_columns['code']]],
            'name' => $row[$this->keys[$this->required_columns['name']]],
            'desc' => $row[$this->keys[$this->required_columns['desc']]],
            'disc' => $row[$this->keys[$this->required_columns['disc']]] === 'yes'
        ];
    }


    /**
     * @param $row
     * @return bool
     */
    protected function checkHeader($row)
    {
        foreach ($this->required_columns as $code => $required_column) {
            $key = array_search($required_column, $row);
            if ($key !== false) {
                $this->keys[$required_column] = $key;
            } else {
                return false;
            }
        }
        return true;
    }

    /**
     * @return bool
     */
    protected function openFile()
    {
        try {
            $this->file = fopen($this->path, 'r');
            return true;
        } catch (\Exception $e) {
            $this->output->writeln($e->getMessage());
            return false;
        }
    }

    /**
     * @return bool
     */
    protected function prepareColumns()
    {
        $isHeader = true;
        while ($row = fgetcsv($this->file)) {
            if ($isHeader){
                if (!$this->checkHeader($row))
                {
                    $this->output->writeln('The required columns were not found in the file! Check the file.');
                    return false;
                } else
                    $isHeader = false;
            } else {
                $this->fillColumns($row);
            }
        }
        return true;
    }
}