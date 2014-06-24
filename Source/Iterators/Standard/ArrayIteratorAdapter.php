<?php

namespace Pinq\Iterators\Standard;

use Pinq\Iterators\Common;

/**
 * Implementation of the adapter iterator for \ArrayIterator using the fetch method
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ArrayIteratorAdapter extends Iterator implements \Pinq\Iterators\IAdapterIterator
{
    use Common\AdapterIterator;
    
    public function __construct(\ArrayIterator $arrayIterator)
    {
        parent::__construct();
        self::__constructIterator($arrayIterator);
    }
    
    protected function doRewind()
    {
        parent::doRewind();
        
        $this->iterator->rewind();
    }
    
    protected function doFetch()
    {
        $arrayIterator = $this->iterator;
        
        if($arrayIterator->valid()) {
            $key = $arrayIterator->key();
            
            //Get value by ref
            $element = [$key, &$arrayIterator[$key]];
            
            $arrayIterator->next();
            
            return $element;
        }
    }
}
