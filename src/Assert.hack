/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

namespace Facebook\FBExpect;

use namespace HH\Lib\{C, Str, Vec};
use type Facebook\DiffLib\StringDiff;
use type Facebook\HackTest\ExpectationFailedException;

abstract class Assert {

  private static function isDiffable(mixed $x): bool {
    if ($x is bool || $x is arraykey) {
      return true;
    }
    if ($x is Container<_>) {
      foreach ($x as $elem) {
        if (!self::isDiffable($elem)) {
          return false;
        }
      }
      return true;
    }
    return false;
  }

  public function assertSame(
    mixed $expected,
    mixed $actual,
    string $message = '',
  ): void {
    if ($expected === $actual) {
      return;
    }

    if (self::isDiffable($expected) && self::isDiffable($actual)) {
      if (!$expected is string) {
        $expected = \var_export($expected, true);
      }
      if (!$actual is string) {
        $actual = \var_export($actual, true);
      }
      throw new ExpectationFailedException(
        Str\format(
          "%s\nFailed asserting that two values are the same:\n%s\n",
          $message,
          StringDiff::lines($expected, $actual)->getUnifiedDiff(),
        ),
      );
    }

    throw new ExpectationFailedException(
      Str\format(
        "%s\nFailed asserting that %s is the same as %s",
        $message,
        \var_export($actual, true),
        \var_export($expected, true),
      ),
    );
  }

  public function assertNotSame(
    mixed $expected,
    mixed $actual,
    string $message = '',
  ): void {
    if ($expected !== $actual) {
      return;
    }
    throw new ExpectationFailedException(
      Str\format(
        "%s\nFailed asserting that values differed; both are %s",
        $message,
        \var_export($actual, true),
      ),
    );
  }

  public function assertEquals(
    mixed $expected,
    mixed $actual,
    string $message = '',
  ): void {
    /* HHAST_IGNORE_ERROR[NoPHPEquality] */
    if ($actual == $expected) {
      return;
    }
    throw new ExpectationFailedException(
      Str\format(
        "%s\nFailed asserting that %s is equal to %s",
        $message,
        \var_export($actual, true),
        \var_export($expected, true),
      ),
    );
  }

  public function assertEqualsWithDelta(
    ?num $expected,
    ?num $actual,
    float $delta,
    string $message = '',
  ): void {
    if (($actual === null) && ($expected === null)) {
      return;
    }
    if ($actual === null) {
      throw new ExpectationFailedException(
        Str\format(
          "%s\nnull is not equal to %f (with delta %f)",
          $message,
          (float)$expected,
          $delta,
        ),
      );
    }
    if ($expected === null) {
      throw new ExpectationFailedException(
        Str\format(
          "%s\n%f is not equal to null (with delta %f)",
          $message,
          (float)$actual,
          $delta,
        ),
      );
    }

    if ($actual >= $expected - $delta && $actual <= $expected + $delta) {
      return;
    }
    throw new ExpectationFailedException(
      Str\format(
        "%s\n%s does not equal %f with delta %f",
        $message,
        (string)$actual,
        (float)$expected,
        $delta,
      ),
    );
  }

  public function assertNotEquals(
    mixed $expected,
    mixed $actual,
    string $message = '',
  ): void {
    /* HHAST_IGNORE_ERROR[NoPHPEquality] */
    if ($actual != $expected) {
      return;
    }
    throw new ExpectationFailedException(
      Str\format(
        "%s\nFailed asserting that %s is not equal to %s",
        $message,
        \var_export($actual, true),
        \var_export($expected, true),
      ),
    );
  }

  public function assertTrue(mixed $condition, string $message = ''): void {
    if ($condition === true) {
      return;
    }
    throw new ExpectationFailedException(
      Str\format(
        "%s\nFailed asserting that %s is true",
        $message,
        \var_export($condition, true),
      ),
    );
  }

  public function assertFalse(mixed $condition, string $message = ''): void {
    if ($condition === false) {
      return;
    }
    throw new ExpectationFailedException(
      Str\format(
        "%s\nFailed asserting that %s is false",
        $message,
        \var_export($condition, true),
      ),
    );
  }

  public function assertNull(mixed $actual, string $message = ''): void {
    if ($actual === null) {
      return;
    }
    throw new ExpectationFailedException(
      Str\format(
        "%s\nFailed asserting that %s is null",
        $message,
        \var_export($actual, true),
      ),
    );
  }

  public function assertNotNull(mixed $actual, string $message = ''): void {
    if ($actual !== null) {
      return;
    }
    throw new ExpectationFailedException(
      Str\format(
        "%s\nFailed asserting that %s is not null",
        $message,
        \var_export($actual, true),
      ),
    );
  }

  private function isPHPEmpty(mixed $v): bool {
    return $v === '' ||
      $v === 0 ||
      $v === 0.0 ||
      $v === '0' ||
      $v === null ||
      $v === false ||
      ($v is Container<_> && C\is_empty($v));
  }

  public function assertEmpty(mixed $actual, string $message = ''): void {
    if ($this->isPHPEmpty($actual)) {
      return;
    }
    throw new ExpectationFailedException(
      Str\format(
        "%s\nFailed asserting that %s is empty",
        $message,
        \var_export($actual, true),
      ),
    );
  }

  public function assertNotEmpty(mixed $actual, string $message = ''): void {
    if (!$this->isPHPEmpty($actual)) {
      return;
    }
    throw new ExpectationFailedException(
      Str\format(
        "%s\nFailed asserting that %s is not empty",
        $message,
        \var_export($actual, true),
      ),
    );
  }

  public function assertGreaterThan(
    num $expected,
    num $actual,
    string $message = '',
  ): void {
    if ($actual > $expected) {
      return;
    }
    throw new ExpectationFailedException(
      Str\format(
        "%s\nFailed asserting that %s is greater than %s",
        $message,
        \var_export($actual, true),
        \var_export($expected, true),
      ),
    );
  }

  public function assertLessThan(
    num $expected,
    num $actual,
    string $message = '',
  ): void {
    if ($actual < $expected) {
      return;
    }
    throw new ExpectationFailedException(
      Str\format(
        "%s\nFailed asserting that %s is less than %s",
        $message,
        \var_export($actual, true),
        \var_export($expected, true),
      ),
    );
  }

  public function assertGreaterThanOrEqual(
    num $expected,
    num $actual,
    string $message = '',
  ): void {
    if ($actual >= $expected) {
      return;
    }
    throw new ExpectationFailedException(
      Str\format(
        "%s\nFailed asserting that %s is greater than or equal to %s",
        $message,
        \var_export($actual, true),
        \var_export($expected, true),
      ),
    );
  }

  public function assertLessThanOrEqual(
    num $expected,
    num $actual,
    string $message = '',
  ): void {
    if ($actual <= $expected) {
      return;
    }
    throw new ExpectationFailedException(
      Str\format(
        "%s\nFailed asserting that %s is less than or equal to %s",
        $message,
        \var_export($actual, true),
        \var_export($expected, true),
      ),
    );
  }

  public function assertInstanceOf(
    string $expected,
    mixed $actual,
    string $message = '',
  ): void {
    if (!\class_exists($expected) && !\interface_exists($expected)) {
      throw new InvalidArgumentException('Invalid class or interface name');
    }
    if (\is_a($actual, $expected)) {
      return;
    }
    throw new ExpectationFailedException(
      Str\format(
        "%s\nFailed asserting that %s is an instance of %s",
        $message,
        \var_export($actual, true),
        $expected,
      ),
    );
  }

  public function assertNotInstanceOf(
    string $expected,
    mixed $actual,
    string $message = '',
  ): void {
    if (!\class_exists($expected) && !\interface_exists($expected)) {
      throw new InvalidArgumentException('Invalid class or interface name');
    }
    if (!\is_a($actual, $expected)) {
      return;
    }
    throw new ExpectationFailedException(
      Str\format(
        "%s\nFailed asserting that %s is not an instance of %s",
        $message,
        \var_export($actual, true),
        $expected,
      ),
    );
  }

  /**
   * Asserts that a variable is of a given type
   */
  public function assertType(
    string $expected,
    mixed $actual,
    string $message = '',
  ): void {
    if (is_type($expected)) {
      if ((new Constraint\IsType($expected))->matches($actual)) {
        return;
      }
      throw new ExpectationFailedException(
        Str\format(
          "%s\nFailed asserting that %s is type %s",
          $message,
          \var_export($actual, true),
          $expected,
        ),
      );
    } else if (\class_exists($expected) || \interface_exists($expected)) {
      $this->assertInstanceOf($expected, $actual, $message);
    }
    throw new InvalidArgumentException('Invalid type');
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
      if (!(new Constraint\IsType($expected))->matches($actual)) {
        return;
      }
      throw new ExpectationFailedException(
        Str\format(
          "%s\nFailed asserting that %s is not type %s",
          $message,
          \var_export($actual, true),
          $expected,
        ),
      );
    } else if (\class_exists($expected) || \interface_exists($expected)) {
      $this->assertNotInstanceOf($expected, $actual, $message);
    }
    throw new InvalidArgumentException('Invalid type');
  }

  public function assertContains(
    mixed $needle,
    mixed $haystack,
    string $message = '',
    bool $ignoreCase = false,
  ): void {
    if ($haystack is Traversable<_>) {
      if ((new Constraint\TraversableContains($needle))->matches($haystack)) {
        return;
      }
    } else if (($haystack is string)) {
      if (!($needle is string)) {
        throw new InvalidArgumentException(
          'If haystack is string, needle must be string',
        );
      }
      if ($ignoreCase) {
        if (Str\contains_ci($haystack, $needle)) {
          return;
        }
      } else if (Str\contains($haystack, $needle)) {
        return;
      }
    } else {
      throw new InvalidArgumentException(
        'Haystack must be an array, traversable or string',
      );
    }
    throw new ExpectationFailedException(
      Str\format(
        "%s\nFailed asserting that %s contains %s",
        $message,
        \var_export($haystack, true),
        \var_export($needle, true),
      ),
    );
  }

  public function assertNotContains(
    mixed $needle,
    mixed $haystack,
    string $message = '',
    bool $ignoreCase = false,
  ): void {
    if ($haystack is Traversable<_>) {
      if (!(new Constraint\TraversableContains($needle))->matches($haystack)) {
        return;
      }
    } else if (($haystack is string)) {
      if (!($needle is string)) {
        throw new InvalidArgumentException(
          'If haystack is string, needle must be string',
        );
      }
      if ($ignoreCase) {
        if (!Str\contains_ci($haystack, $needle)) {
          return;
        }
      } else if (!Str\contains($haystack, $needle)) {
        return;
      }
    } else {
      throw new InvalidArgumentException(
        'Haystack must be an array, traversable or string',
      );
    }
    throw new ExpectationFailedException(
      Str\format(
        "%s\nFailed asserting that %s does not contain %s",
        $message,
        \var_export($haystack, true),
        \var_export($needle, true),
      ),
    );
  }

  public function assertRegExp(
    string $expected,
    string $actual,
    string $message = '',
  ): void {
    if (\preg_match($expected, $actual) === 1) {
      return;
    }
    throw new ExpectationFailedException(
      Str\format(
        "%s\nFailed asserting that %s matches PCRE pattern %s",
        $message,
        $actual,
        $expected,
      ),
    );
  }

  public function assertNotRegExp(
    string $expected,
    string $actual,
    string $message = '',
  ): void {
    if (\preg_match($expected, $actual) === 0) {
      return;
    }
    throw new ExpectationFailedException(
      Str\format(
        "%s\nFailed asserting that %s does not match PCRE pattern %s",
        $message,
        $actual,
        $expected,
      ),
    );
  }

  public function assertSubset(
    dynamic $expected,
    dynamic $actual,
    string $msg = '',
    string $path = '$actual',
  ): void {
    foreach ($expected as $key => $value) {
      if ($actual is KeyedContainer<_, _>) {
        $actual_value = idx($actual, $key ?as arraykey);
        $part = '['.\var_export($key, true).']';
      } else if (\is_object($actual)) {
        $actual_value = /* HH_FIXME[2011] Dynamic property access */ $actual->$key;
        $part = '->'.$key;
      } else {
        $actual_value = null;
        $part = null;
      }

      if (is_any_array($value) || \is_object($value)) {
        $this->assertSubset($value, $actual_value, $msg, $path.$part);
      } else {
        $this->assertEquals($value, $actual_value, $msg."\nKey: ".$path.$part);
      }
    }
  }

  /**
   * Recursively sorts the two arbitrarily depth nested arrays and then checks
   * that the contents of the two arrays are equal.
   */
  public function assertKeyAndValueEquals<Tk as arraykey>(
    KeyedContainer<Tk, mixed> $expected,
    KeyedContainer<Tk, mixed> $actual,
    string $msg = '',
  ): void {
    $expected = self::sortArrayRecursive($expected);
    $actual = self::sortArrayRecursive($actual);
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
    $this->assertEquals(Vec\sort($expected), Vec\sort($actual), $msg);
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
    (function(Tv, Tv): bool) $comparator,
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
        $main_message = $message === '' ? 'Collection is not sorted' : $message;
        $failure_detail = \sprintf(
          'at pos %d, %s and %s are in the wrong order',
          $index,
          \var_export($pair[0], true),
          \var_export($pair[1], true),
        );

        throw new ExpectationFailedException(
          $main_message.': '.$failure_detail,
        );
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
    (function(Tv): mixed) $key_extractor,
    string $message = '',
  ): void {
    $this->assertIsSorted(
      $collection,
      /* HH_FIXME[4240] unsafe comparison (PHPism) */
      ($a, $b) ==> $key_extractor($a) <= $key_extractor($b),
      $message,
    );
  }

  private static function sortArrayRecursive<Tk as arraykey>(
    KeyedContainer<Tk, mixed> $arr,
  ): dict<arraykey, mixed> {
    $out = dict[];
    foreach ($arr as $k => $v) {
      if ($v is KeyedContainer<_, _>) {
        $v = self::sortArrayRecursive($v);
      }
      $out[$k as arraykey] = $v;
    }
    return $out;
  }

}
