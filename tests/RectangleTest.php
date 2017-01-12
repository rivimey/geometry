<?php

namespace GeometryTests;

use Geometry\ImmutableException;
use Geometry\NotFoundException;
use Geometry\Rectangle;

class RectangleTest extends \PHPUnit_Framework_TestCase
{
    private function assertNormalized(Rectangle $rect)
    {
        $this->assertTrue($rect->minX <= $rect->maxX);
        $this->assertTrue($rect->minY <= $rect->maxY);
    }

    private function assertIdentical(Rectangle $expected, Rectangle $actual)
    {
        $this->assertTrue(Rectangle::identicalTo($expected, $actual));
    }

    public function testConstructor()
    {
        $this->assertNormalized(new Rectangle(0, 0, 4, 4));
        $this->assertNormalized(new Rectangle(4, 4, 0, 0));
    }

    public function testGetValidProperties()
    {
        $rect = new Rectangle(1, 2, 3, 4);

        $this->assertSame(1, $rect->minX);
        $this->assertSame(2, $rect->minY);
        $this->assertSame(3, $rect->maxX);
        $this->assertSame(4, $rect->maxY);
    }

    public function testGetInvalidProperty()
    {
        $this->expectException(NotFoundException::class);

        $rect = new Rectangle(1, 2, 3, 4);
        $rect->foo;
    }

    public function testSetProperty()
    {
        $this->expectException(ImmutableException::class);

        $rect = new Rectangle(1, 2, 3, 4);
        $rect->x1 = 3;
    }

    public function testCallBadFunctionOOP()
    {
        $this->expectException(NotFoundException::class);

        $rect = new Rectangle(1, 2, 3, 4);
        $rect->foo();
    }

    public function testCallBadFunctionProcedural()
    {
        $this->expectException(NotFoundException::class);

        $rect = new Rectangle(1, 2, 3, 4);
        Rectangle::foo($rect);
    }

    public function testWidth()
    {
        $rect1 = new Rectangle(-1, -1, 4, 4);
        $this->assertSame(5, $rect1->width());
        $this->assertSame(5, Rectangle::width($rect1));
    }

    public function testHeight()
    {
        $rect1 = new Rectangle(-1, -1, 4, 4);
        $this->assertSame(5, $rect1->height());
        $this->assertSame(5, Rectangle::height($rect1));
    }

    public function testArea()
    {
        $rect1 = new Rectangle(0, 0, 4, 4);
        $this->assertSame(16, $rect1->area());
        $this->assertSame(16, Rectangle::area($rect1));

        $rect2 = new Rectangle(0, -4, 4, 0);
        $this->assertSame(16, $rect2->area());
        $this->assertSame(16, Rectangle::area($rect2));
    }

    public function testCenter()
    {
        $rect = new Rectangle(0, 0, 4, 6);

        list($centerX, $centerY) = $rect->center();
        $this->assertSame(2, $centerX);
        $this->assertSame(3, $centerY);

        list($centerX, $centerY) = Rectangle::center($rect);
        $this->assertSame(2, $centerX);
        $this->assertSame(3, $centerY);
    }

    public function testIdentical()
    {
        $rect1 = new Rectangle(0, 0, 4, 4);
        $rect2 = new Rectangle(2, 2, 6, 6);
        $this->assertFalse($rect1->identicalTo($rect2));
        $this->assertFalse(Rectangle::identicalTo($rect1, $rect2));

        $rect1 = new Rectangle(0, 0, 4, 4);
        $rect2 = new Rectangle(0, 0, 4, 4);
        $this->assertTrue($rect1->identicalTo($rect2));
        $this->assertTrue(Rectangle::identicalTo($rect1, $rect2));
    }

    public function testIntersects()
    {
        // No overlap
        $rect1 = new Rectangle(0, 0, 4, 4);
        $rect2 = new Rectangle(5, 0, 9, 4);
        $this->assertFalse($rect1->intersects($rect2));
        $this->assertFalse(Rectangle::intersects($rect1, $rect2));

        // Edges touch
        $rect1 = new Rectangle(0, 0, 4, 4);
        $rect2 = new Rectangle(4, 0, 8, 4);
        $this->assertFalse($rect1->intersects($rect2));
        $this->assertFalse(Rectangle::intersects($rect1, $rect2));

        // Normal case
        $rect1 = new Rectangle(0, 0, 4, 4);
        $rect2 = new Rectangle(2, 2, 6, 6);
        $this->assertTrue($rect1->intersects($rect2));
        $this->assertTrue(Rectangle::intersects($rect1, $rect2));
    }

    public function testContains()
    {
        // No overlap
        $rect1 = new Rectangle(0, 0, 4, 4);
        $rect2 = new Rectangle(5, 0, 9, 4);
        $this->assertFalse($rect1->contains($rect2));
        $this->assertFalse(Rectangle::contains($rect1, $rect2));

        // Partial overlap
        $rect1 = new Rectangle(0, 0, 4, 4);
        $rect2 = new Rectangle(2, 2, 6, 6);
        $this->assertFalse($rect1->contains($rect2));
        $this->assertFalse(Rectangle::contains($rect1, $rect2));

        // Fully contained
        $rect1 = new Rectangle(0, 0, 4, 4);
        $rect2 = new Rectangle(1, 1, 3, 3);
        $this->assertTrue($rect1->contains($rect2));
        $this->assertTrue(Rectangle::contains($rect1, $rect2));

        // Identical rectangles
        $rect1 = new Rectangle(0, 0, 4, 4);
        $rect2 = new Rectangle(0, 0, 4, 4);
        $this->assertTrue($rect1->contains($rect2));
        $this->assertTrue(Rectangle::contains($rect1, $rect2));
    }

    public function testTranslated()
    {
        // OOP
        $rect = new Rectangle(0, 0, 4, 4);
        $rectTranslated = $rect->translated(2, 2);
        $this->assertIdentical(new Rectangle(2, 2, 6, 6), $rectTranslated);
        $this->assertNotSame($rectTranslated, $rect);

        // Procedural
        $rect = new Rectangle(0, 0, 4, 4);
        $rectTranslated = Rectangle::translated($rect, 2, 2);
        $this->assertIdentical(new Rectangle(2, 2, 6, 6), $rectTranslated);
        $this->assertNotSame($rectTranslated, $rect);
    }

    public function testScaledOrigin()
    {
        // OOP
        $rect = new Rectangle(1, 1, 2, 2);
        $rectScaled = $rect->scaledOrigin(2, 3);
        $this->assertIdentical(new Rectangle(2, 3, 4, 6), $rectScaled);
        $this->assertNotSame($rectScaled, $rect);

        // Procedural
        $rect = new Rectangle(1, 1, 2, 2);
        $rectScaled = Rectangle::scaledOrigin($rect, 2, 3);
        $this->assertIdentical(new Rectangle(2, 3, 4, 6), $rectScaled);
        $this->assertNotSame($rectScaled, $rect);
    }

    public function testScaledCenter()
    {
        // OOP
        $rect = new Rectangle(1, 1, 3, 3);
        $rectScaled = $rect->scaledCenter(2, 3);
        $this->assertIdentical(new Rectangle(0, -1, 4, 5), $rectScaled);
        $this->assertNotSame($rectScaled, $rect);

        // Procedural
        $rect = new Rectangle(1, 1, 3, 3);
        $rectScaled = Rectangle::scaledCenter($rect, 2, 3);
        $this->assertIdentical(new Rectangle(0, -1, 4, 5), $rectScaled);
        $this->assertNotSame($rectScaled, $rect);
    }

    public function testInflated()
    {
        // OOP
        $rect = new Rectangle(1, 1, 3, 3);
        $rectInflated = $rect->inflated(1, 2);
        $this->assertIdentical(new Rectangle(0, -1, 4, 5), $rectInflated);
        $this->assertNotSame($rectInflated, $rect);

        // Procedural
        $rect = new Rectangle(1, 1, 3, 3);
        $rectInflated = Rectangle::inflated($rect, 1, 2);
        $this->assertIdentical(new Rectangle(0, -1, 4, 5), $rectInflated);
        $this->assertNotSame($rectInflated, $rect);
    }
}
