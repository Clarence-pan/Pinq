<?php

namespace Pinq\Expressions\Operators;

/**
 * The enum containing PHP binary operators
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
final class Binary
{
    //Arthmetic
    const ADDITION       = '+';
    const SUBTRACTION    = '-';
    const MULTIPLICATION = '*';
    const DIVISION       = '/';
    const MODULUS        = '%';
    //Bitwise
    const BITWISE_AND = '&';
    const BITWISE_OR  = '|';
    const BITWISE_XOR = '^';
    const SHIFT_LEFT  = '<<';
    const SHIFT_RIGHT = '>>';
    //Logical
    const LOGICAL_AND              = '&&';
    const LOGICAL_OR               = '||';
    const EQUALITY                 = '==';
    const IDENTITY                 = '===';
    const INEQUALITY               = '!=';
    const NOT_IDENTICAL            = '!==';
    const LESS_THAN                = '<';
    const LESS_THAN_OR_EQUAL_TO    = '<=';
    const GREATER_THAN             = '>';
    const GREATER_THAN_OR_EQUAL_TO = '>=';
    //String
    const CONCATENATION = '.';
    //Type
    const IS_INSTANCE_OF = 'instanceof';

    private static $binaryOperations;

    public static function doBinaryOperation($left, $operator, $right)
    {
        if (self::$binaryOperations === null) {
            self::$binaryOperations = [
                    self::BITWISE_AND              => function ($l, $r) { return $l & $r; },
                    self::BITWISE_OR               => function ($l, $r) { return $l | $r; },
                    self::BITWISE_XOR              => function ($l, $r) { return $l ^ $r; },
                    self::SHIFT_LEFT               => function ($l, $r) { return $l << $r; },
                    self::SHIFT_RIGHT              => function ($l, $r) { return $l >> $r; },
                    self::LOGICAL_AND              => function ($l, $r) { return $l && $r; },
                    self::LOGICAL_OR               => function ($l, $r) { return $l || $r; },
                    self::ADDITION                 => function ($l, $r) { return $l + $r; },
                    self::SUBTRACTION              => function ($l, $r) { return $l - $r; },
                    self::MULTIPLICATION           => function ($l, $r) { return $l * $r; },
                    self::DIVISION                 => function ($l, $r) { return $l / $r; },
                    self::MODULUS                  => function ($l, $r) { return $l % $r; },
                    self::CONCATENATION            => function ($l, $r) { return $l . $r; },
                    self::IS_INSTANCE_OF           => function ($l, $r) { return $l instanceof $r; },
                    self::EQUALITY                 => function ($l, $r) { return $l == $r; },
                    self::IDENTITY                 => function ($l, $r) { return $l === $r; },
                    self::INEQUALITY               => function ($l, $r) { return $l != $r; },
                    self::NOT_IDENTICAL            => function ($l, $r) { return $l !== $r; },
                    self::LESS_THAN                => function ($l, $r) { return $l < $r; },
                    self::LESS_THAN_OR_EQUAL_TO    => function ($l, $r) { return $l <= $r; },
                    self::GREATER_THAN             => function ($l, $r) { return $l > $r; },
                    self::GREATER_THAN_OR_EQUAL_TO => function ($l, $r) { return $l >= $r; }
            ];
        }

        $operation = self::$binaryOperations[$operator];

        return $operation($left, $right);
    }
}
