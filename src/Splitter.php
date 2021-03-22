<?php

namespace Keboola\DockerDemo;

use Keboola\DockerDemo\Splitter\Exception;
use Keboola\DockerDemo\Splitter\MbSplit;

class Splitter
{

    protected $rowNumberColumn = 'row_number';

    /**
     * @return string
     */
    public function getRowNumberColumn()
    {
        return $this->rowNumberColumn;
    }

    /**
     * @param string $rowNumberColumn
     * @return $this
     */
    public function setRowNumberColumn($rowNumberColumn)
    {
        $this->rowNumberColumn = $rowNumberColumn;

        return $this;
    }

    /**
     * @param $source
     * @param $destination
     * @param $primaryKeyColumn
     * @param $textColumn
     * @param $length
     * @return int
     * @throws Exception
     */
    public function processFile($source, $destination, $primaryKeyColumn, $textColumn, $length)
    {
        if (!file_exists($source)) {
            throw new Exception("File '{$source}' not found.");
        }

        $fhIn = fopen($source, "r");
        $fhOut = fopen($destination, "w");

        $header = fgetcsv($fhIn);

        if (!in_array($primaryKeyColumn, $header)) {
            throw new Exception("Primary key column '{$primaryKeyColumn}' not found.");
        }

        if (!in_array($textColumn, $header)) {
            throw new Exception("Column '{$textColumn}' not found.");
        }

        $primaryKeyColumnIndex = array_search($primaryKeyColumn, $header);
        $dataColumnIndex = array_search($textColumn, $header);

        fputcsv($fhOut, array($primaryKeyColumn, $textColumn, $this->getRowNumberColumn()));

        $rows = 0;
        while ($row = fgetcsv($fhIn)) {
            $rows++;
            $parts = MbSplit::split($row[$dataColumnIndex], $length);
            foreach ($parts as $key => $part) {
                fputcsv($fhOut, array($row[$primaryKeyColumnIndex], $part, $key));
            }
        }
        fclose($fhIn);
        fclose($fhOut);
        return $rows;
    }
}
