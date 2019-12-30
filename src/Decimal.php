<?php
declare(strict_types=1);

namespace AntonVlasenko\Decimal;

class Decimal
{
    public static $SCALE        = 200;
    public const REGEXP_FLOAT   = '/^(?<minus>-?)(?<leftPart>\d+)(\.(?<rightPart>\d+))?([eE](?<exponentSign>[-+])?(?<exponentPower>[\d]+))?$/';
    public const LESS_THAN      = -1;
    public const EQUALS         = 0;
    public const MORE_THAN      = 1;

    /** @var string */
    private $value;

    public function __construct($value)
    {
        $this->value = $this->parse($value);
    }

    private function parse($value): string
    {
        $value   = (string)$value;
        $matches = [];
        if (1 !== preg_match(static::REGEXP_FLOAT, $value, $matches)) {
            throw new \InvalidArgumentException;
        }
        $leftPart = ltrim($matches['leftPart'], '0');
        $leftPart = strlen($leftPart) ? $leftPart : '0';

        $hasRightPart = array_key_exists('rightPart', $matches);
        $minus        = !empty($matches['minus']);

        $rightPart = '';
        if ($hasRightPart) {
            $rightPart = rtrim($matches['rightPart'], '0');
            $rightPart = strlen($rightPart) ? $rightPart : '';
        }

        if (('0' == $leftPart) && ('' === $rightPart)) {
            $minus = false;
        }


        $result = ($minus ? '-' : '') . $leftPart . (strlen($rightPart) ? '.' : '') . $rightPart;

        $hasExponent = array_key_exists('exponentPower', $matches);
        if (!$hasExponent) {
            return $result;
        }

        $exponentSignIsPositive = true;
        if (array_key_exists('exponentSign', $matches) && ('-' == $matches['exponentSign'])) {
            $exponentSignIsPositive = false;
        }

        $exponentPower = (string)$matches['exponentPower'];
        $exponentPower = bcpow('10', $exponentPower);

        $method = $exponentSignIsPositive ? 'bcmul' : 'bcdiv';
        $result = $method($result, $exponentPower, static::$SCALE);
        return $this->parse($result);
    }

    public function add($value): Decimal
    {
        $value  = $this->parse($value);
        $result = bcadd($this->value, $value, static::$SCALE);
        return $this->construct($result);
    }

    public function substract($value): Decimal
    {
        $value  = $this->parse($value);
        $result = bcsub($this->value, $value, static::$SCALE);
        return $this->construct($result);
    }

    public function multiplyBy($value): Decimal
    {
        $value  = $this->parse($value);
        $result = bcmul($this->value, $value, static::$SCALE);
        return $this->construct($result);
    }

    public function divideBy($value): Decimal
    {
        $value = $this->parse($value);
        if ('0' === $value) {
            throw new \InvalidArgumentException('Cannot divide by zero.');
        }
        $result = bcdiv($this->value, $value, static::$SCALE);
        return $this->construct($result);
    }

    public function power($exponent): Decimal
    {
        $exponent = $this->parse($exponent);
        if ($this->isInt($exponent)) {
            $result = bcpow($this->value, $exponent, static::$SCALE);
        } else {
            $result = pow($this->toFloat(), (float)$exponent);
        }

        return $this->construct($result);
    }

    public function squareRoot(): Decimal
    {
        if ($this->lessThan(0)) {
            throw new \LogicException('Cannot square root negative numbers. Imaginary numbers aren\'t supported.');
        }

        $result = bcsqrt($this->value, static::$SCALE);
        return $this->construct($result);
    }

    public function moreThan($value): bool
    {
        return $this->compare($value, [1]);
    }

    public function moreThanOrEquals($value): bool
    {
        return $this->compare($value, [static::EQUALS, static::MORE_THAN]);
    }

    public function lessThan($value): bool
    {
        return $this->compare($value, [static::LESS_THAN]);
    }

    public function lessThanOrEquals($value): bool
    {
        return $this->compare($value, [static::LESS_THAN, static::EQUALS]);
    }

    public function equals($value): bool
    {
        return $this->compare($value, [static::EQUALS]);
    }

    public function notEquals($value): bool
    {
        return !$this->equals($value);
    }

    private function compare($value, array $validResults): bool
    {
        $value  = $this->parse($value);
        $result = bccomp($this->value, $value, static::$SCALE);
        return in_array($result, $validResults, true);
    }

    private function isInt($value): bool
    {
        return false === strpos($value, '.');
    }

    /**
     * Determines how we handle change of the object's value
     * We can either create a new object or modify the current one
     */
    protected function construct($value): Decimal
    {
        return new static($value);
    }

    public function toInt(): int
    {
        if (!$this->isInt($this->value)) {
            throw new \RuntimeException('Cannot convert non integer to integer.');
        }

        return (int)$this->value;
    }

    public function toFloat(): float
    {
        $value = (string)$this;
        return (float)$value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}