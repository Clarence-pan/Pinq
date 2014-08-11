<?php

namespace Pinq\Queries\Functions\Parameters;

use Pinq\Expressions as O;

/**
 * Base class for the structure of parameters of a function.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class Base implements \Countable
{
    /**
     * @var O\ParameterExpression[]
     */
    protected $expressions;

    /**
     * @var O\ParameterExpression[]
     */
    protected $unusedExpressions;

    /**
     * @var O\ParameterExpression[]
     */
    protected $requiredUnusedExpressions = [];

    /**
     * @var boolean
     */
    protected $hasRequiredUnusedParameters = false;

    /**
     * @var array<string, O\Expression>
     */
    protected $unusedParameterDefaultMap = [];

    protected function __construct(array $parameterExpressions, $usedParameters)
    {
        $this->expressions       = $parameterExpressions;
        $this->unusedExpressions = array_slice($parameterExpressions, $usedParameters);

        foreach ($this->unusedExpressions as $parameter) {
            if ($parameter->hasDefaultValue()) {
                $defaultExpression = $parameter->getDefaultValue();
            } else {
                $defaultExpression                 = null;
                $this->hasRequiredUnusedParameters = true;
                $this->requiredUnusedExpressions[] = $parameter;
            }

            $this->unusedParameterDefaultMap[$parameter->getName()] = $defaultExpression;
        }
    }

    public function count()
    {
        return count($this->expressions);
    }

    /**
     * Gets an array of all parameters.
     *
     * @return O\ParameterExpression[]
     */
    final public function getAll()
    {
        return $this->expressions;
    }

    /**
     * Whether there are any unused parameters.
     *
     * @return boolean
     */
    public function hasUnused()
    {
        return !empty($this->unusedExpressions);
    }

    /**
     * Whether there is an unused parameters without a default value.
     *
     * @return boolean
     */
    public function hasRequiredUnusedParameters()
    {
        return $this->hasRequiredUnusedParameters;
    }

    /**
     * Gets any unused parameters without a default value.
     *
     * @return boolean
     */
    public function getRequiredUnusedParameters()
    {
        return $this->requiredUnusedExpressions;
    }

    /**
     * Gets an array of unused parameters.
     *
     * @return O\ParameterExpression[]
     */
    public function getUnused()
    {
        return $this->unusedExpressions;
    }

    /**
     * Returns an array containing default value expressions indexed by their
     * respective unused parameter name.
     * This is useful as it will introduce variables in the scope of the
     * function that may be validly used.
     *
     * @return array<string, O\Expression>
     */
    public function getUnusedParameterDefaultMap()
    {
        return $this->unusedParameterDefaultMap;
    }
}
