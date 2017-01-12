<?php

namespace Geometry;

/**
 * Defines a rectangle in Cartesian coordinates.
 */
class Rectangle
{
    private $minX;
    private $minY;
    private $maxX;
    private $maxY;

    // Enables getting of private properties
    public function __get($name)
    {
        if (!property_exists($this, $name)) {
            throw new NotFoundException("Rectangle has no property named `$name`.");
        }

        return $this->$name;
    }

    // Prevent setting of private properties
    public function __set($name, $value)
    {
        throw new ImmutableException("Cannot set `$name` directly because Rectangles are immutable.");
    }

    // Enables OOP-style method calling
    public function __call($name, $arguments)
    {
        if (!method_exists($this, $name)) {
            throw new NotFoundException("Rectangle has no method named `$name`.");
        }

        return call_user_func_array(
            [$this, $name],
            array_merge([$this], $arguments)
        );
    }

    // Enabled static-style method calling
    public static function __callStatic($name, $arguments)
    {
        if (!method_exists(self::class, $name)) {
            throw new NotFoundException("Rectangle has no method named `$name`.");
        }

        return call_user_func_array(
            [self::class, $name],
            $arguments
        );
    }

    /**
     * Constructs a new rectangle where minX and minY define the lower left
     * corner, and maxX and maxY define the upper right corner.
     *
     * This constructor will ensure that the resulting rectangle is normalized.
     *
     * @param float $minX
     * @param float $minY
     * @param float $maxX
     * @param float $maxY
     */
    public function __construct($minX, $minY, $maxX, $maxY)
    {
        $this->minX = min($minX, $maxX);
        $this->minY = min($minY, $maxY);
        $this->maxX = max($minX, $maxX);
        $this->maxY = max($minY, $maxY);
    }

    /**
     * Gets the width of this rectangle.
     *
     * @param  Rectangle $rect
     * @return float
     */
    private static function width(Rectangle $rect)
    {
        return $rect->maxX - $rect->minX;
    }

    /**
     * Gets the height of this rectangle.
     *
     * @param  Rectangle $rect
     * @return float
     */
    private static function height(Rectangle $rect)
    {
        return $rect->maxY - $rect->minY;
    }

    /**
     * Gets the area of this rectangle.
     *
     * @param  Rectangle $rect
     * @return float
     */
    private static function area(Rectangle $rect)
    {
        return self::width($rect) * self::height($rect);
    }

    /**
     * Gets the center x and y of this rectangle.
     *
     * @param  Rectangle $rect
     * @return float[] A two-element array, where the first element is the
     *                   center x and the second element is the center y.
     */
    private static function center(Rectangle $rect)
    {
        return [
            ($rect->minX + $rect->maxX) / 2,
            ($rect->minY + $rect->maxY) / 2
        ];
    }

    /**
     * Checks to see if the rectangle is identical to another rectangle.
     *
     * @param  Rectangle $rect1
     * @param  Rectangle $rect2
     * @return bool
     */
    private static function identicalTo(Rectangle $rect1, Rectangle $rect2)
    {
        return $rect1->minX === $rect2->minX
            && $rect1->minY === $rect2->minY
            && $rect1->maxX === $rect2->maxX
            && $rect1->maxY === $rect2->maxY;
    }

    /**
     * Checks to see if the rectangle is intersecting another rectangle.
     *
     * @param  Rectangle $rect1
     * @param  Rectangle $rect2
     * @return bool
     */
    private static function intersects(Rectangle $rect1, Rectangle $rect2)
    {
        return $rect1->minX < $rect2->maxX
            && $rect1->maxX > $rect2->minX
            && $rect1->minY < $rect2->maxY
            && $rect1->maxY > $rect2->minY;
    }

    /**
     * Checks to see if the rectangle fully contains another rectangle.
     *
     * @param  Rectangle $rect1
     * @param  Rectangle $rect2
     * @return bool
     */
    private static function contains(Rectangle $rect1, Rectangle $rect2)
    {
        return $rect1->minX <= $rect2->minX
            && $rect1->maxX >= $rect2->maxX
            && $rect1->minY <= $rect2->minY
            && $rect1->maxY >= $rect2->maxY;
    }

    /**
     * Returns a non-normalized copy of this rectangle which has been translated
     * by the given x and y coordinates.
     *
     * @param  Rectangle $rect
     * @param  float $x
     * @param  float $y
     * @return Rectangle
     */
    private static function translated(Rectangle $rect, $x, $y)
    {
        return new Rectangle(
            $rect->minX + $x,
            $rect->minY + $y,
            $rect->maxX + $x,
            $rect->maxY + $y
        );
    }

    /**
     * Returns a non-normalized copy of this rectangle which has been scaled
     * about the origin by the given x and y values.
     *
     * @param  Rectangle $rect
     * @param  float $x
     * @param  float $y
     * @return Rectangle
     */
    private static function scaledOrigin(Rectangle $rect, $x, $y)
    {
        return new Rectangle(
            $rect->minX * $x,
            $rect->minY * $y,
            $rect->maxX * $x,
            $rect->maxY * $y
        );
    }

    /**
     * Returns a non-normalized copy of this rectangle which has been scaled
     * about the rectangle's center point by the given x and y values.
     *
     * @param  Rectangle $rect
     * @param  float $x
     * @param  float $y
     * @return Rectangle
     */
    private static function scaledCenter(Rectangle $rect, $x, $y)
    {
        list($centerX, $centerY) = self::center($rect);

        $atOrigin = self::translated($rect, -$centerX, -$centerY);
        $scaled = self::scaledOrigin($atOrigin, $x, $y);
        $recentered = self::translated($scaled, $centerX, $centerY);

        return $recentered;
    }

    /**
     * Returns a normalized copy of this rectangle whose dimensions have been
     * extended on all sides by the given x and y amounts.
     *
     * @param  Rectangle $rect
     * @param  float $x  The amount by which to expand the left and right sides.
     * @param  float $y  The amount by which to expand the top and bottom sides.
     * @return Rectangle
     */
    private static function inflated(Rectangle $rect, $x, $y)
    {
        return new Rectangle(
            $rect->minX - $x,
            $rect->minY - $y,
            $rect->maxX + $x,
            $rect->maxY + $y
        );
    }
}
