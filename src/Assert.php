<?hh
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

namespace Facebook\FBExpect;

use namespace HH\Lib\Str;
use type Facebook\DiffLib\StringDiff;

abstract class Assert {

  public function assertSame($expected, $actual, string $message = ''): void {
    if ($expected === $actual) {
      return;
    }

    if ($expected is string && $actual is string) {
      throw new ExpectationFailedException(
        Str\format(
          "%s\nFailed asserting that two strings are the same:\n%s\n",
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
    $expected,
    $actual,
    string $message = '',
  ): void {
    if ($expected !== $actual) {
      return;
    }
    throw new ExpectationFailedException(
      Str\format(
        "%s\nFailed asserting that %s is not the same as %s",
        $message,
        \var_export($actual, true),
        \var_export($expected, true),
      ),
    );
  }

  public function assertEquals($expected, $actual, string $message = ''): void {
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
    num $expected,
    $actual,
    float $delta,
    string $message = '',
  ): void {
    if ($actual >= $expected - $delta && $actual <= $expected + $delta) {
      return;
    }
    throw new ExpectationFailedException(
      Str\format(
        "%s\n%s does not equal %f with delta %f",
        $message,
        $actual,
        (float)$expected,
        $delta,
      ),
    );
  }

  public function assertNotEquals(
    $expected,
    $actual,
    string $message = '',
  ): void {
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

  public function assertTrue($condition, string $message = '') {
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

  public function assertFalse($condition, string $message = '') {
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

  public function assertNull($actual, string $message = '') {
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

  public function assertNotNull($actual, string $message = '') {
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

  public function assertEmpty($actual, string $message = '') {
    if (empty($actual) == true) {
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

  public function assertNotEmpty($actual, string $message = '') {
    if (empty($actual) != true) {
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
    $expected,
    $actual,
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
    $expected,
    $actual,
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
    $expected,
    $actual,
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
    $expected,
    $actual,
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
    $actual,
    string $message = '',
  ): void {
    if (!\class_exists($expected) && !\interface_exists($expected)) {
      throw new InvalidArgumentException('Invalid class or interface name');
    }
    if ($actual instanceof $expected) {
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
    $actual,
    string $message = '',
  ): void {
    if (!\class_exists($expected) && !\interface_exists($expected)) {
      throw new InvalidArgumentException('Invalid class or interface name');
    }
    if (!($actual instanceof $expected)) {
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
    $actual,
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
    $actual,
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
    $needle,
    $haystack,
    string $message = '',
    bool $ignoreCase = false,
  ): void {
    if (
      \is_array($haystack) ||
      (\is_object($haystack) && $haystack instanceof Traversable)
    ) {
      if ((new Constraint\TraversableContains($needle))->matches($haystack)) {
        return;
      }
    } elseif (($haystack is string)) {
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
    $needle,
    $haystack,
    string $message = '',
    bool $ignoreCase = false,
  ): void {
    if (
      \is_array($haystack) ||
      (\is_object($haystack) && $haystack instanceof Traversable)
    ) {
      if (!(new Constraint\TraversableContains($needle))->matches($haystack)) {
        return;
      }
    } elseif (($haystack is string)) {
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
  ) {
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
  ) {
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
    $expected,
    $actual,
    string $msg = '',
    string $path = '$actual',
  ): void {
    foreach ($expected as $key => $value) {
      if (is_any_array($actual)) {
        $actual_value = idx($actual, $key);
        $part = '['.\var_export($key, true).']';
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
        $main_message = $message ?: 'Collection is not sorted';
        $failure_detail = \sprintf(
          'at pos %d, %s and %s are in the wrong order',
          $index,
          \var_export($pair[0], true),
          \var_export($pair[1], true),
        );

        throw
          new ExpectationFailedException($main_message.': '.$failure_detail);
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
    \sort(&$copy);
    return $copy->toImmVector();
  }
}
