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

    public function __get($name)
    {
        return $this->$name;
    }

    public function __set($name, $value)
    {
        throw new ImmutableException("Cannot set `$name` directly because Rectangles are immutable.");
    }

    /**
     * Constructs a new rectangle where x1 and y1 define one corner, and
     * x2 and y2 define the opposite corner.
     *
     * @param float $x1
     * @param float $y1
     * @param float $x2
     * @param float $y2
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
     * @return float
     */
    public function width()
    {
        return $this->maxX - $this->minX;
    }

    /**
     * Gets the height of this rectangle.
     *
     * @return float
     */
    public function height()
    {
        return $this->maxY - $this->minY;
    }

    /**
     * Gets the area of this rectangle.
     *
     * @return float
     */
    public function area()
    {
        return $this->width() * $this->height();
    }

    /**
     * Gets the center x and y of this rectangle.
     *
     * @return float[] A two-element array, where the first element is the
     *                   center x and the second element is the center y.
     */
    public function center()
    {
        return [
            ($this->minX + $this->maxX) / 2,
            ($this->minY + $this->maxY) / 2
        ];
    }

    /**
     * Checks to see if the rectangle is identical to another rectangle.
     *
     * @param  Rectangle $rect
     * @return bool
     */
    public function identicalTo(Rectangle $rect)
    {
        return $this->minX === $rect->minX
            && $this->minY === $rect->minY
            && $this->maxX === $rect->maxX
            && $this->maxY === $rect->maxY;
    }

    /**
     * Checks to see if the rectangle is intersecting another rectangle.
     *
     * @param  Rectangle $rect
     * @return bool
     */
    public function intersects(Rectangle $rect)
    {
        return $this->minX < $rect->maxX
            && $this->maxX > $rect->minX
            && $this->minY < $rect->maxY
            && $this->maxY > $rect->minY;
    }

    /**
     * Checks to see if the rectangle fully contains another rectangle.
     *
     * @param  Rectangle $other
     * @return bool
     */
    public function contains(Rectangle $rect)
    {
        return $this->minX <= $rect->minX
            && $this->maxX >= $rect->maxX
            && $this->minY <= $rect->minY
            && $this->maxY >= $rect->maxY;
    }

    /**
     * Returns a non-normalized copy of this rectangle which has been translated
     * by the given x and y coordinates.
     *
     * @param  float $x
     * @param  float $y
     * @return Rectangle
     */
    public function translated($x, $y)
    {
        return new Rectangle(
            $this->minX + $x,
            $this->minY + $y,
            $this->maxX + $x,
            $this->maxY + $y
        );
    }

    /**
     * Returns a non-normalized copy of this rectangle which has been scaled
     * about the origin by the given x and y values.
     *
     * @param  float $x
     * @param  float $y
     * @return Rectangle
     */
    public function scaledOrigin($x, $y)
    {
        return new Rectangle(
            $this->minX * $x,
            $this->minY * $y,
            $this->maxX * $x,
            $this->maxY * $y
        );
    }

    /**
     * Returns a non-normalized copy of this rectangle which has been scaled
     * about the rectangle's center point by the given x and y values.
     *
     * @param  float $x
     * @param  float $y
     * @return Rectangle
     */
    public function scaledCenter($x, $y)
    {
        list($centerX, $centerY) = $this->center();

        return $this
            ->translated(-$centerX, -$centerY)
            ->scaledOrigin($x, $y)
            ->translated($centerX, $centerY);
    }

    /**
     * Returns a normalized copy of this rectangle whose dimensions have been
     * extended on all sides by the given x and y amounts.
     *
     * @param  float $x  The amount by which to expand the left and right sides.
     * @param  float $y  The amount by which to expand the top and bottom sides.
     * @return Rectangle
     */
    public function inflated($x, $y)
    {
        return new Rectangle(
            $this->minX - $x,
            $this->minY - $y,
            $this->maxX + $x,
            $this->maxY + $y
        );
    }
}
