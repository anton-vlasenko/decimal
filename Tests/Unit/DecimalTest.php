<?php
declare(strict_types=1);

namespace AntonVlasenko\Decimal\Tests\Unit;

use AntonVlasenko\Decimal\Decimal;
use PHPUnit\Framework\TestCase;

class DecimalTest extends TestCase
{
    /** @test */
    public function it_doesnt_accept_invalid_value_1(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Decimal('asfoq3t');
    }

    /** @test */
    public function it_doesnt_accept_invalid_value_2(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Decimal('123.');
    }

    /** @test */
    public function it_doesnt_accept_invalid_value_3(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Decimal('.12412');
    }

    /** @test */
    public function it_doesnt_accept_invalid_value_4(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Decimal('28230.kysdfl');
    }

    /** @test */
    public function it_doesnt_accept_invalid_value_5(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Decimal('2124asd');
    }

    /** @test */
    public function it_accepts_exponent_floats(): void
    {
        $this->assertSame('732.5', (string)new Decimal('7.325e2'));
        $this->assertSame('732.5', (string)new Decimal('7.325E+2'));
        $this->assertSame('0.0000000003445', (string)new Decimal('3.445e-10'));
        $this->assertSame('0.0000000003445', (string)new Decimal('3.445E-10'));
    }

    /** @test */
    public function it_accepts_valid_values(): void
    {
        new Decimal('0');
        $this->addToAssertionCount(1);

        new Decimal('0.123123');
        $this->addToAssertionCount(1);

        new Decimal('182619223523523.23792385012986');
        $this->addToAssertionCount(1);

        new Decimal('-235235.2379238');
        $this->addToAssertionCount(1);
    }

    /** @test */
    public function it_can_compare(): void
    {
        $decimal         = new Decimal('8734.23785');
        $negativeDecimal = new Decimal('-342.0001');

        $this->assertTrue($decimal->moreThan('1241'));
        $this->assertTrue($negativeDecimal->moreThan('-27365.12412'));

        $this->assertTrue($decimal->moreThanOrEquals('8734.23785'));
        $this->assertTrue($negativeDecimal->moreThanOrEquals('-30000'));

        $this->assertTrue($decimal->lessThan('8734.23785000000000000000001'));
        $this->assertTrue($negativeDecimal->lessThan('-199.235'));

        $this->assertTrue($decimal->lessThanOrEquals('8734.237850000000000000000000000000000000'));
        $this->assertTrue($negativeDecimal->lessThanOrEquals('-100.012312124124'));

        $this->assertTrue($decimal->equals($decimal));
        $this->assertFalse($decimal->equals($negativeDecimal));

        $this->assertFalse($decimal->notEquals($decimal));
        $this->assertTrue($decimal->notEquals($negativeDecimal));
    }

    /** @test */
    public function can_add(): void
    {
        $a      = new Decimal('587165.321478924');
        $result = $a->add('801.00000287463');
        $this->assertSame((string)$result, '587966.32148179863');

        $a      = new Decimal('854301501.36984');
        $result = $a->add('-4782938523.7145');
        $this->assertSame((string)$result, '-3928637022.34466');
    }

    /** @test */
    public function can_substract(): void
    {
        $a      = new Decimal('21436');
        $result = $a->substract('2341.286582738234324');
        $this->assertSame((string)$result, '19094.713417261765676');

        $a      = new Decimal('7236582.124');
        $result = $a->substract('-82029384.736273823');
        $this->assertSame((string)$result, '89265966.860273823');
    }

    /** @test */
    public function can_multiply(): void
    {
        $a      = new Decimal('124.20000351');
        $result = $a->multiplyBy('9.235');
        $this->assertSame((string)$result, '1146.98703241485');

        $a      = new Decimal('823756.2536');
        $result = $a->multiplyBy('-38.09764');
        $this->assertSame((string)$result, '-31383169.197401504');
    }

    /** @test */
    public function can_divide(): void
    {
        $a      = new Decimal('493961.5589674');
        $result = $a->divideBy('-653.14');
        $this->assertSame((string)$result, '-756.28741');

        $a      = new Decimal('-953102.1557129');
        $result = $a->divideBy('-952.15');
        $this->assertSame((string)$result, '1001.000006');
    }

    /** @test */
    public function throws_exception_when_trying_to_divide_by_zero(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new Decimal(1))->divideBy(0);
    }

    /** @test */
    public function throws_exception_when_casting_float_to_int(): void
    {
        $this->expectException(\RuntimeException::class);
        (new Decimal('6.5'))->toInt();
    }

    /** @test */
    public function can_cast_to_int(): void
    {
        $integer = new Decimal('2342352362');
        $this->assertSame(2342352362, $integer->toInt());
    }

    /** @test */
    public function can_square_root(): void
    {
        $decimal = new Decimal('34675105019099.11132516');
        $this->assertSame('5888557.1254', (string)$decimal->squareRoot());
    }

    /** @test */
    public function can_raise_to_power(): void
    {
        $decimal = new Decimal('5888557.1254');
        $this->assertSame('34675105019099.11132516', (string)$decimal->power(2));

        $decimal = new Decimal('2');
        $this->assertSame('2.8284271247462', (string)$decimal->power(1.5));
    }

    /** @test */
    public function can_cast_to_float(): void
    {
        $decimal = new Decimal('5888557.1254');
        $this->assertSame(5888557.1254, $decimal->toFloat());
    }

    /** @test */
    public function throws_exception_when_trying_to_square_root_negative_numbers(): void
    {
        $this->expectException(\LogicException::class);
        (new Decimal(-1))->squareRoot();
    }
}