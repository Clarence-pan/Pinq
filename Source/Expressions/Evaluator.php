<?php

namespace Pinq\Expressions;

use Pinq\PinqException;

/**
 * Implementation of the expression evaluator using compiled closures.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class Evaluator implements IEvaluator
{
    /**
     * @var IEvaluationContext
     */
    protected $context;

    /**
     * @var string[]
     */
    protected $requiredVariables;

    protected function __construct(IEvaluationContext $context = null)
    {
        $this->context = $context ? : EvaluationContext::globalScope();
        $this->requiredVariables = array_keys($this->context->getVariableTable());
    }

    final public function getContext()
    {
        return $this->context;
    }

    final public function getRequiredVariables()
    {
        return $this->requiredVariables;
    }

    public function evaluate(array $variableTable = null)
    {
        //Loose equality: order is irrelevant
        if ($variableTable !== null && array_keys($variableTable) != $this->requiredVariables) {
            throw new PinqException(
                    'Cannot evaluate expression: supplied variable table is invalid, variable names do not match the required variable names');
        }

        $contextVariableTable = $this->context->getVariableTable() ? : [];
        return $this->doEvaluation($variableTable === null ? $contextVariableTable : $variableTable);
    }

    protected abstract function doEvaluation(array $variableTable);
}