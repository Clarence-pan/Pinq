<?php 

namespace Pinq\Tests\Integration\Traversable;

class UnsupportedTest extends TraversableTest
{
    /**
     * @dataProvider Everything
     */
    public function testThatSetIndexThrowsAndException(\Pinq\ITraversable $traversable, array $data)
    {
        if (!$traversable instanceof \Pinq\ICollection) {
            $this->setExpectedException('\\Pinq\\PinqException');
            $traversable[0] = null;
        }
    }
    
    /**
     * @dataProvider Everything
     */
    public function testThatUnsetIndexThrowsAndException(\Pinq\ITraversable $traversable, array $data)
    {
        if (!$traversable instanceof \Pinq\ICollection) {
            $this->setExpectedException('\\Pinq\\PinqException');
            unset($traversable[0]);
        }
    }
}