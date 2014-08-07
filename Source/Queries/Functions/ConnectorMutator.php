<?php

namespace Pinq\Queries\Functions;

use Pinq\Expressions as O;

/**
 * Structure of a function that recieves a outer and inner value and key
 * and mutates the outer value parameter.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ConnectorMutator extends MutatorBase
{
    public function getParameterStructure(array $parameterExpressions)
    {
        return new Parameters\OuterInnerValueKey($parameterExpressions);
    }

    protected function getValueParameter()
    {
        return $this->getParameters()->getOuterValue();
    }

    /**
     * @return Parameters\OuterInnerValueKey
     */
    public function getParameters()
    {
        return parent::getParameters();
    }
}