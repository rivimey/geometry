<?php

namespace GeometryTests;

use Geometry\ImmutableException;
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
        $this->assertTrue($actual->identicalTo($expected));
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

    public function testCannotSetProperties()
    {
        $this->expectException(ImmutableException::class);

        $rect = new Rectangle(0, 0, 4, 4);
        $rect->x1 = 3;
    }

    public function testWidth()
    {
        $rect1 = new Rectangle(-1, -1, 4, 4);
        $this->assertSame(5, $rect1->width());
    }

    public function testHeight()
    {
        $rect1 = new Rectangle(-1, -1, 4, 4);
        $this->assertSame(5, $rect1->height());
    }

    public function testArea()
    {
        $rect1 = new Rectangle(0, 0, 4, 4);
        $this->assertSame(16, $rect1->area());

        $rect2 = new Rectangle(0, -4, 4, 0);
        $this->assertSame(16, $rect2->area());
    }

    public function testCenter()
    {
        $rect = new Rectangle(0, 0, 4, 6);
        list($centerX, $centerY) = $rect->center();
        $this->assertSame(2, $centerX);
        $this->assertSame(3, $centerY);
    }

    public function testIdentical()
    {
        $rect1 = new Rectangle(0, 0, 4, 4);
        $rect2 = new Rectangle(2, 2, 6, 6);
        $this->assertFalse($rect1->identicalTo($rect2));

        $rect1 = new Rectangle(0, 0, 4, 4);
        $rect2 = new Rectangle(0, 0, 4, 4);
        $this->assertTrue($rect1->identicalTo($rect2));
    }

    public function testIntersects()
    {
        // No overlap
        $rect1 = new Rectangle(0, 0, 4, 4);
        $rect2 = new Rectangle(5, 0, 9, 4);
        $this->assertFalse($rect1->intersects($rect2));

        // Edges touch
        $rect1 = new Rectangle(0, 0, 4, 4);
        $rect2 = new Rectangle(4, 0, 8, 4);
        $this->assertFalse($rect1->intersects($rect2));

        // Normal case
        $rect1 = new Rectangle(0, 0, 4, 4);
        $rect2 = new Rectangle(2, 2, 6, 6);
        $this->assertTrue($rect1->intersects($rect2));
    }

    public function testContains()
    {
        // No overlap
        $rect1 = new Rectangle(0, 0, 4, 4);
        $rect2 = new Rectangle(5, 0, 9, 4);
        $this->assertFalse($rect1->contains($rect2));

        // Partial overlap
        $rect1 = new Rectangle(0, 0, 4, 4);
        $rect2 = new Rectangle(2, 2, 6, 6);
        $this->assertFalse($rect1->contains($rect2));

        // Fully contained
        $rect1 = new Rectangle(0, 0, 4, 4);
        $rect2 = new Rectangle(1, 1, 3, 3);
        $this->assertTrue($rect1->contains($rect2));

        // Identical rectangles
        $rect1 = new Rectangle(0, 0, 4, 4);
        $rect2 = new Rectangle(0, 0, 4, 4);
        $this->assertTrue($rect1->contains($rect2));
    }

    public function testTranslated()
    {
        $rect = new Rectangle(0, 0, 4, 4);
        $rectTranslated = $rect->translated(2, 2);
        $this->assertIdentical(new Rectangle(2, 2, 6, 6), $rectTranslated);
        $this->assertSame(2, $rectTranslated->minX);
        $this->assertNotSame($rectTranslated, $rect);
    }

    public function testScaledOrigin()
    {
        $rect = new Rectangle(1, 1, 2, 2);
        $rectScaled = $rect->scaledOrigin(2, 3);
        $this->assertIdentical(new Rectangle(2, 3, 4, 6), $rectScaled);
        $this->assertNotSame($rectScaled, $rect);
    }

    public function testScaledCenter()
    {
        $rect = new Rectangle(1, 1, 3, 3);
        $rectScaled = $rect->scaledCenter(2, 3);
        $this->assertIdentical(new Rectangle(0, -1, 4, 5), $rectScaled);
        $this->assertNotSame($rectScaled, $rect);
    }

    public function testInflated()
    {
        $rect = new Rectangle(1, 1, 3, 3);
        $rectInflated = $rect->inflated(1, 2);
        $this->assertIdentical(new Rectangle(0, -1, 4, 5), $rectInflated);
        $this->assertNotSame($rectInflated, $rect);
    }
}
