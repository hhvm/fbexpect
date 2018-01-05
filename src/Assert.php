<?hh
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the BSD-style license found in the
 *  LICENSE file in the root directory of this source tree. An additional grant
 *  of patent rights can be found in the PATENTS file in the same directory.
 *
 */

namespace Facebook\FBExpect;

abstract class Assert extends \PHPUnit\Framework\Assert {
  /**
   * Asserts that a variable is of a given type
   */
  public function assertType(
    string $expected,
    mixed $actual,
    string $message = '',
  ): void {
    if (is_type($expected)) {
      $constraint = new Constraint\IsType($expected);
    } else if (class_exists($expected) || interface_exists($expected)) {
      $constraint = new \PHPUnit_Framework_Constraint_IsInstanceOf(
        /* HH_IGNORE_ERROR[4110] is really a classname */ $expected,
      );
    } else {
      /* HH_FIXME[2049] unbound name */
      throw \PHPUnit\Util\InvalidArgumentHelper::factory(
        1,
        'class or interface name',
      );
    }
    $this->assertThat($actual, $constraint, $message);
  }

  /**
   * Asserts that a variable is not of a given type
   */
  public function assertNotType(
    string $expected,
    mixed $actual,
    string $message = '',
  ): void {
    if (is_type($expected)) {
      $constraint = new Constraint\IsType($expected);
    } else if (class_exists($expected) || interface_exists($expected)) {
      $constraint = new \PHPUnit_Framework_Constraint_IsInstanceOf(
        /* HH_IGNORE_ERROR[4110] is really a classname */ $expected,
      );
    } else {
      /* HH_FIXME[2049] unbound name */
      throw \PHPUnit\Util\InvalidArgumentHelper::factory(
        1,
        'class or interface name',
      );
    }
    $constraint = new \PHPUnit_Framework_Constraint_Not($constraint);
    $this->assertThat($actual, $constraint, $message);
  }

  public function assertSubset(
    $expected,
    $actual,
    string $msg = '',
    string $path = '$actual',
  ): void {
    foreach ($expected as $key => $value) {
      if (is_any_array($actual)) {
        $actual_value = idx($actual, $key);
        $part = '['.var_export($key, true).']';
      } else if (is_object($actual)) {
        $actual_value = /* UNSAFE_EXPR */ $actual->$key;
        $part = "->$key";
      } else {
        $actual_value = null;
        $part = null;
      }

      if (is_any_array($value) || is_object($value)) {
        $this->assertSubset($value, $actual_value, $msg, $path.$part);
        } else {
        $this->assertEquals($value, $actual_value, $msg."\nKey: $path$part");
      }
    }
  }

  /**
   * Recursively sorts the two arbitrarily depth nested arrays and then checks
   * that the contents of the two arrays are equal.
   */
  public function assertKeyAndValueEquals(
    array $expected,
    array $actual,
    string $msg = '',
  ): void {
    self::sortArrayRecursive(&$expected);
    self::sortArrayRecursive(&$actual);
    $this->assertEquals($expected, $actual, $msg);
  }

  /**
   * Checks that the contents of the two arrays are equal,
   * irrespective of element order.
   */
  public function assertContentsEqual<T>(
    Traversable<T> $expected,
    Traversable<T> $actual,
    string $msg = '',
  ): void {
    $expected = self::sorted($expected)->toArray();
    $actual = self::sorted($actual)->toArray();

    $this->assertEquals($expected, $actual, $msg);
  }
  /**
   * Checks that a collection is sorted according to some criterion.
   *
   * @param Traversable<Tv> $collection Any collection
   * @param (function(Tv,Tv) : bool) $comparator A function that compares two
   *   items in the collection and returns true if they are in order (i.e. the
   *   first precedes or is equal to the second).
   * @param ?string $message A message to display if the collection is not
   *                         sorted correctly
   *
   * The collection is considered sorted iff $comparator returns true for all
   * consecutive pairs in the collection.
   *
   * A collection with 0 or 1 items is always considered sorted.
   */
  public function assertIsSorted<Tv>(
    Traversable<Tv> $collection,
    (function(Tv,Tv) : bool) $comparator,
    string $message = '',
  ): void {
    // Note: the way we maintain the pair of values to be compared may seem
    // weird and convoluted. However, there is a reason for this weirdness.
    // Because $collection is a Traversable (we're trying to be general here),
    // we can't index directly into it, so at some point we'd have to do a
    // comparison like $comparator($prev_item, $current_item). Unfortunately
    // there seems to be no way to initialize $prev_item so as to satisfy the
    // Hack type checker. If we init it to null, Hack complains that we are
    // passing a nullable into $comparator(Tv,Tv). If we omit it, Hack complains
    // of an undefined variable. And we can't init it to the first item of the
    // collection either, because we can only foreach into it and by then it's
    // too late.
    $pair = Vector {};

    $index = 0;
    foreach ($collection as $item) {
      if ($pair->count() < 2) {
        $pair->add($item);
      } else {
        $pair[0] = $pair[1];
        $pair[1] = $item;
      }

      if (($pair->count() === 2) && !$comparator($pair[0], $pair[1])) {
        $main_message = $message ?: 'Collection is not sorted';
        $failure_detail = sprintf(
          'at pos %d, %s and %s are in the wrong order',
          $index,
          /* HH_IGNORE_ERROR[2049] unbound name */
          \PHPUnit\Util\Type::toString($pair[0]),
          /* HH_IGNORE_ERROR[2049] unbound name */
          \PHPUnit\Util\Type::toString($pair[1]),
        );

        $this->fail($main_message . ': ' . $failure_detail);
      }

      $index++;
    }
  }

  /**
   * Checks that a collection is sorted according to some given key in the
   * elements.
   *
   * @param Traversable<Tv> $collection Any collection
   * @param (function(Tv) : mixed) $key_extractor A function that extracts a
   *   sorting key from every item
   * @param ?string $message A message to display if the collection is not
   *                         sorted correctly
   *
   * The collection is considered sorted iff the keys extracted from every
   * element form a vector that is sorted in natural order.
   *
   * A collection with 0 or 1 items is always considered sorted.
   */
  public function assertIsSortedByKey<Tv>(
    Traversable<Tv> $collection,
    (function(Tv) : mixed) $key_extractor,
    string $message = '',
  ): void {
    $this->assertIsSorted(
      $collection,
      ($a,$b) ==> $key_extractor($a) <= $key_extractor($b),
      $message,
    );
  }

  private static function sortArrayRecursive(array &$arr): void {
    foreach ($arr as $codemod_inserted_key => $i) {
      if (is_array($i)) {
        self::sortArrayRecursive(&$i);
      }
      $arr[$codemod_inserted_key] = $i;
    }
  }

  private static function sorted<T>(Traversable<T> $x): ImmVector<T> {
    $copy = Vector::fromItems($x);
    sort(&$copy);
    return $copy->toImmVector();
  }
}
