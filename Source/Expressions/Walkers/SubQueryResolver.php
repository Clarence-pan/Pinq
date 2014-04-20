<?php

namespace Pinq\Expressions\Walkers;

use \Pinq\Queries;
use \Pinq\Expressions as O;

class FunctionToExpressionTreeConverter implements \Pinq\Parsing\IFunctionToExpressionTreeConverter
{
    public function Convert(callable $Function)
    {
        return $Function;
    }
}

class QueryProvider implements \Pinq\Providers\IQueryProvider 
{
    private $Query;
    
    public function CreateQueryable(\Pinq\Queries\IScope $Scope = null)
    {
        return new \Pinq\Queryable($this, $Scope);
    }

    public function GetFunctionToExpressionTreeConverter()
    {
        return new FunctionToExpressionTreeConverter();
    }

    public function Load(Queries\IRequestQuery $Query)
    {
        $this->Query = $Query;
        if($Query->GetRequest() instanceof Queries\Requests\Values) {
            return new \ArrayIterator();
        }
        else {
            return null;
        }
    }
    
    /**
     * @return Queries\IRequestQuery|mill
     */
    public function GetAndResetQuery() 
    {
        $Query = $this->Query;
        $this->Query = null;
        
        return $Query;
    }
}

/**
 * Resolves all method call expression to their equivalent sub query expression. 
 */
class SubQueryResolver extends O\ExpressionWalker
{
    /**
     * @var QueryProvider
     */
    private $QueryProvider;

    /**
     * @var callable|null
     */
    private $Filter;

    public function __construct()
    {
        $this->QueryProvider = new QueryProvider();
    }
    
    public function SetFilter(callable $Function = null)
    {
        $this->Filter = $Function;
    }

    public function WalkMethodCall(O\MethodCallExpression $MethodCallExpression)
    {
        $Expression = $MethodCallExpression;
        $MethodCallDepth = 0;
        $Filter = $this->Filter;
        while($Expression instanceof O\MethodCallExpression) {
            
            if($Filter === null || $Filter($Expression)) {
                $SubQueryExpression = $this->ResolveSubQuery($MethodCallExpression, $Expression->GetValueExpression(), $MethodCallDepth);
                
                if($SubQueryExpression !== null) {
                    return $SubQueryExpression;
                }
            }
            
            $MethodCallDepth++;
            $Expression = $Expression->GetValueExpression();
        }
        
        return parent::WalkMethodCall($MethodCallExpression);
    }
    
    /**
     * @param \Pinq\Expressions\MethodCallExpression $MethodCallExpression
     * @param int $MethodCallDepth
     * @return O\SubQueryExpression|null
     */
    private function ResolveSubQuery(O\MethodCallExpression $MethodCallExpression, O\Expression $QueryableOriginExpression, $MethodCallDepth)
    {
        $Queryable = $this->QueryProvider->CreateQueryable(new \Pinq\Queries\Scope([]));
        
        /*
         * Update the expression with the blank queryable as the value and resolve closure arguments
         * into fucntion expression trees
         */
        $QueryMethodCallExpression = $this->ResolveMethodCallExpression(
                $MethodCallExpression, 
                $MethodCallDepth,
                O\Expression::Value($Queryable));
        
        // Attempt execute the methods agains the queryable
        $ResultExpression = $QueryMethodCallExpression->Simplify();
        
        if($ResultExpression instanceof O\ValueExpression) {
            //Methods successfully executed upon the queryable, the value should contain the correct scope
            $Query = $this->QueryProvider->GetAndResetQuery();
            if($Query === null) {
                $Query = new Queries\RequestQuery($Queryable->GetScope(), new Queries\Requests\Values());
            }
            return O\Expression::SubQuery($QueryableOriginExpression, $Query, $MethodCallExpression);
        }
    }

    private function ResolveMethodCallExpression(O\MethodCallExpression $Expression, $MethodCallDepth, O\Expression $ReplacementExpression) 
    {
        $Expression = $Expression->Update(
                $Expression->GetValueExpression(), 
                $Expression->GetNameExpression(), 
                $this->ResolveClosureArguments($Expression->GetArgumentExpressions()));
        
        if($MethodCallDepth === 0) {
            return $Expression->UpdateValue($ReplacementExpression);
        }
        
        return $Expression->UpdateValue(
                $this->ResolveMethodCallExpression(
                        $Expression->GetValueExpression(),
                        --$MethodCallDepth, 
                        $ReplacementExpression));
    }
    
    private function ResolveClosureArguments(array $ArgumentExpressions)
    {
        foreach ($ArgumentExpressions as $Key => $ArgumentExpression) {
            if ($ArgumentExpression instanceof O\ClosureExpression) {
                $FunctionExpressionTree = \Pinq\FunctionExpressionTree::FromClosureExpression($ArgumentExpression);
                $ArgumentExpressions[$Key] = O\Expression::Value($FunctionExpressionTree);
            }
        }

        return $ArgumentExpressions;
    }
}
