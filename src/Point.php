<?php

namespace Geometry;

use Geometry\Rectangle;
use Geometry\NotFoundException;
use Geometry\ImmutableException;

/**
 * Defines a rectangle in Cartesian coordinates.
 */
class Point extends Shape {

  private $pX;

  private $pY;

  // Enables getting of private properties
  public function __get($name) {
    if (!property_exists($this, $name)) {
      throw new NotFoundException("Point has no property named `$name`.");
    }

    return $this->$name;
  }

  // Prevent setting of private properties
  public function __set($name, $value) {
    throw new ImmutableException("Cannot set `$name` directly because Points are immutable.");
  }

  // Enables OOP-style method calling
  public function __call($name, $arguments) {
    if (!method_exists($this, $name)) {
      throw new NotFoundException("Point has no method named `$name`.");
    }

    return call_user_func_array(
      [$this, $name],
      array_merge([$this], $arguments)
    );
  }

  // Enabled static-style method calling
  public static function __callStatic($name, $arguments) {
    if (!method_exists(self::class, $name)) {
      throw new NotFoundException("Point has no method named `$name`.");
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
   * @param float $pX
   * @param float $pY
   */
  public function __construct($pX, $pY) {
    $this->pX = $pX;
    $this->pY = $pY;
  }

  /**
   * Make a rectangle by combining this point and another point.
   *
   * @param Point $other
   *
   * @return Rectangle
   */
  public function pointToRect(Point $other) {
    return new Rectangle($this->pX, $this->pY, $other->pX, $other->pY);
  }

  /**
   * Make a rectangle by combining this point and another point.
   *
   * @param Point $mine
   * @param Point $other
   *
   * @return Rectangle
   */
  static public function pointsToRect(Point $mine, Point $other) {
    return new Rectangle($mine->pX, $mine->pY, $other->pX, $other->pY);
  }

  /**
   * Make a rectangle by combining this point and another point.
   *
   * @param $width
   * @param $height
   *
   * @return Rectangle
   */
  public function makeRect($width, $height) {
    return new Rectangle($this->pX, $this->pY, $this->pX + $width, $this->pY + $height);
  }

  /**
   * Return Points from a rectangle.
   *
   * @param Rectangle $rect
   *
   * @return Point
   */
  static public function topLeft(Rectangle $rect) {
    return new Point($rect->minX, $rect->maxY);
  }

  /**
   * Return Points from a rectangle.
   *
   * @param Rectangle $rect
   *
   * @return Point
   */
  static public function bottomLeft(Rectangle $rect) {
    return new Point($rect->minX, $rect->minY);
  }

  /**
   * Return Points from a rectangle.
   *
   * @param Rectangle $rect
   *
   * @return Point
   */
  static public function topRight(Rectangle $rect) {
    return new Point($rect->maxX, $rect->maxY);
  }

  /**
   * Return Points from a rectangle.
   *
   * @param Rectangle $rect
   *
   * @return Point
   */
  static public function bottomRight(Rectangle $rect) {
    return new Point($rect->maxX, $rect->minY);
  }

  /**
   * Return Points from a rectangle.
   *
   * @param Rectangle $rect
   *
   * @return Point
   */
  static public function centre(Rectangle $rect) {
    return new Point(($rect->maxX - $rect->minX) / 2, ($rect->maxY - $rect->minY) / 2);
  }

  /**
   * Return True if this point is inside the rectangle.
   *
   * NB: Includes rectangle edges!
   *
   * @param Rectangle $rect
   *
   * @return bool
   */
  public function inside(Rectangle $rect) {
    return $this->pX <= $rect->maxX
      && $this->pX >= $rect->minX
      && $this->pY <= $rect->maxY
      && $this->pY >= $rect->minY;
  }

  /**
   * Return True if this point lies on the edge of the rectangle.
   *
   * Be careful when using floats!
   *
   * @param Rectangle $rect
   *
   * @return bool
   */
  public function borders(Rectangle $rect) {
    return
      // Lies on left or right edge and within top-bottom range.
      ($this->pX === $rect->maxX || $this->pX === $rect->minX) && ($this->pY <= $rect->maxY || $this->pY >= $rect->minY)
      ||
      // Lies on top or bottom edge and within left-right range.
      ($this->pX <= $rect->maxX && $this->pX >= $rect->minX) && ($this->pY === $rect->maxY || $this->pY === $rect->minY);
  }

  /**
   * Return a new point translated by an xy amount.
   *
   * @param $x
   * @param $y
   *
   * @return Point
   */
  public function translateXY($x = 0, $y = 0) {
    return new Point($this->pX + $x, $this->pY + $y);
  }

}
