<?php

namespace Keboola\DockerDemo\Tests\Splitter;

use Keboola\DockerDemo\Splitter\MbSplit;
use PHPUnit\Framework\TestCase;

class MbSplitTest extends TestCase
{

    public function testSimpleSplit()
    {
        $this->assertEquals(["a", "b"], MbSplit::split("ab", 1));
    }

    public function testMbSplit()
    {
        $this->assertEquals(["a", "č", "ď", "e"], MbSplit::split("ačďe", 1));
        $this->assertEquals(["漢","字"], MbSplit::split("漢字", 1));
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage maxLength must be greater than 0
     */
    public function testMbSplitException()
    {
        $this->assertEquals(["a", "č", "ď", "e"], MbSplit::split("ačďe", 0));
    }
}
