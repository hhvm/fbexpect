/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

namespace Facebook\FBExpect;

use namespace Facebook\HackTest;
use namespace HH\Lib\{C, Str, Vec};
use type HH\Lib\Ref;
use type Facebook\HackTest\ExpectationFailedException;

class ExpectObj<T> extends Assert {
  public function __construct(private T $var) {}

  /**************************************
   **************************************
   *********** Basic Assertions *********
   **************************************
   **************************************
   *
   * Example Usage: expect($actual)->toBeSame($expected)
   *
   */

  /**
   * Asserts: $actual === $expected
   * Note:    Two objects are considered the same if they reference the same
   *          instance
   */
  public function toEqual(
    mixed $expected,
    string $msg = '',
    mixed ...$args
  ): void {
    $this->toBeSame($expected, $msg, ...$args);
  }

  /**
   * Asserts: Roughly $actual == $expected
   * Note:    Two objects are considered equal if
   *          (string)$o1 == (string)$o2.
   */
  public function toBePHPEqual(
    mixed $expected,
    string $msg = '',
    mixed ...$args
  ): void {
    $msg = \vsprintf($msg, $args);
    $this->assertEquals($expected, $this->var, $msg);
  }

  /**
   * Float comparison can give false positives - this will only error if $actual
   * and $expected are not within $delta of each other.
   */
  public function toEqualWithDelta(
    ?num $expected,
    float $delta,
    string $msg = '',
    mixed ...$args
  ): void where T as ?num {
    $msg = \vsprintf($msg, $args);
    $this->assertEqualsWithDelta($expected, $this->var, $delta, $msg);
  }

  public function toAlmostEqual(
    ?num $expected,
    string $msg = '',
    mixed ...$args
  ): void where T as ?num {
    $msg = \vsprintf($msg, $args);
    $this->toEqualWithDelta(
      $expected,
      1.19e-07 * 4, // roughly equivalent to gtest
      '%s',
      $msg,
    );
  }

  <<__Deprecated('use toBePHPEqualWithNANEqual')>>
  public function beEqualWithNANEqual(
    mixed $expected,
    string $msg = '',
    mixed ...$args
  ): void {
    $this->toBePHPEqualWithNANEqual($expected, $msg, ...$args);
  }

  /**
   * Same as toEqual() except treats NAN as equal to itself.
   */
  public function toBePHPEqualWithNANEqual(
    mixed $expected,
    string $msg = '',
    mixed ...$args
  ): void {
    $msg = \vsprintf($msg, $args);

    $actual = $this->var;
    if (
      ($expected is float) &&
      ($actual is float) &&
      \is_nan($expected) &&
      \is_nan($actual)
    ) {
      return;
    }

    $this->toBePHPEqual($expected, '%s', $msg);
  }

  /**
   * Asserts: $actual === $expected
   * Note:    Two objects are considered the same if they reference the same
   *          instance
   */
  public function toBeSame(
    mixed $expected,
    string $msg = '',
    mixed ...$args
  ): void {
    $msg = \vsprintf($msg, $args);
    $this->assertSame($expected, $this->var, $msg);
  }

  // Asserts: $actual === true
  public function toBeTrue(string $msg = '', mixed ...$args): void {
    $msg = \vsprintf($msg, $args);
    $this->assertTrue($this->var, $msg);
  }

  // Asserts: $actual === false
  public function toBeFalse(string $msg = '', mixed ...$args): void {
    $msg = \vsprintf($msg, $args);
    $this->assertFalse($this->var, $msg);
  }

  // Asserts: $actual === null
  public function toBeNull(string $msg = '', mixed ...$args): void {
    $msg = \vsprintf($msg, $args);
    $this->assertNull($this->var, $msg);
  }

  // Asserts: empty($actual) == true
  public function toBeEmpty(string $msg = '', mixed ...$args): void {
    $msg = \vsprintf($msg, $args);
    $this->assertEmpty($this->var, $msg);
  }

  // Asserts: $actual > $expected
  public function toBeGreaterThan(
    num $expected,
    string $msg = '',
    mixed ...$args
  ): void where T as num {
    $msg = \vsprintf($msg, $args);
    $this->assertGreaterThan($expected, $this->var, $msg);
  }

  // Asserts: $actual < $expected
  public function toBeLessThan(
    num $expected,
    string $msg = '',
    mixed ...$args
  ): void where T as num {
    $msg = \vsprintf($msg, $args);
    $this->assertLessThan($expected, $this->var, $msg);
  }

  // Asserts: $actual <= $expected
  public function toBeLessThanOrEqualTo(
    num $expected,
    string $msg = '',
    mixed ...$args
  ): void where T as num {
    $msg = \vsprintf($msg, $args);
    $this->assertLessThanOrEqual($expected, $this->var, $msg);
  }

  // Asserts: $actual => $expected
  public function toBeGreaterThanOrEqualTo(
    num $expected,
    string $msg = '',
    mixed ...$args
  ): void where T as num {
    $msg = \vsprintf($msg, $args);
    $this->assertGreaterThanOrEqual($expected, $this->var, $msg);
  }

  // Asserts: $actual instanceof $type
  public function toBeInstanceOf<Tclass>(
    classname<Tclass> $class_or_interface,
    string $msg = '',
    mixed ...$args
  ): Tclass {
    $msg = \vsprintf($msg, $args);
    $obj = $this->var;
    $this->assertInstanceOf($class_or_interface, $obj, $msg);
    return
      /* HH_IGNORE_ERROR[4110] Typechecker can't understand assertInstanceOf */
      $obj;
  }

  // Asserts: $actual matches $expected regular expression
  public function toMatchRegExp(
    string $expected,
    string $msg = '',
    mixed ...$args
  ): void {
    $msg = \vsprintf($msg, $args);
    $this->assertRegExp($expected, (string)$this->var, $msg);
  }

  /**
   * Asserts: is_{$type}($actual)
   *
   * $type should be one of the basic type strings like 'int' or 'string'
   *
   * Example: expect($actual)->toBeType('string') would assert is_string($actual)
   */
  public function toBeType(
    string $type,
    string $msg = '',
    mixed ...$args
  ): void {
    $msg = \vsprintf($msg, $args);
    $this->assertType($type, $this->var, $msg);
  }

  /**
   * Assert: iterate through $actual and see if any element == $needle.
   * Note:   If $needle is an object, === will be used.
   */
  public function toContain<TVal>(
    TVal $needle,
    string $msg = '',
    mixed ...$args
  ): void where T as Traversable<TVal> {
    $msg = \vsprintf($msg, $args);
    $this->assertContains($needle, $this->var, $msg);
  }

  /**
   * Assert: $actual contains the substring $expected
   */
  public function toContainSubstring(
    string $expected,
    string $msg = '',
    mixed ...$args
  ): void where T = string {
    $msg = \vsprintf($msg, $args);
    $this->assertContains($expected, $this->var, $msg);
  }

  /**
   * Assert: That the KeyedTraversible $key has a key set.
   * Note:   If $key is a Set, use assertContains.
   */
  public function toContainKey<TKey as arraykey, TVal>(
    TKey $key,
    string $msg = '',
    mixed ...$args
  ): void where T as KeyedContainer<TKey, TVal> {
    $msg = \vsprintf($msg, $args);
    $obj = $this->var;
    invariant(
      $obj is KeyedContainer<_, _>,
      'ERROR: expect(...$args)->toContainKey only can be applied to '.
      'KeyedContainers, not %s.',
      print_type($obj),
    );
    $this->assertTrue(\array_key_exists($key, $obj), $msg);
  }

  /**
   * Asserts: $actual contains $expected_subset
   * Notes:
   *
   *   - $actual can be an array of values (keys are optional) or an object
   *   - If $actual is an array, this checks that foreach $k, $v in
   *     $expected_subset, $actual[$k] == $v.
   *   - If $actual is an object, this checks that foreach $k, $v in
   *     $expected_subset, $actual->$k == $v.
   *
   *  TODO: typehint $expected_subset to array and fix tests
   */
  public function toInclude(
    mixed $expected_subset,
    string $msg = '',
    mixed ...$args
  ): void {
    $msg = \vsprintf($msg, $args);
    $this->assertSubset($expected_subset, $this->var, $msg);
  }

  /**
   * Asserts: $actual has the same content as $expected, i.e. the same
   * key/values regardless of order.
   */
  public function toHaveSameShapeAs(
    mixed $expected,
    string $msg = '',
    mixed ...$args
  ): void {
    $msg = \vsprintf($msg, $args);

    $value = $this->var;
    $this->assertKeyAndValueEquals(
      $expected as KeyedContainer<_, _>,
      $value as KeyedContainer<_, _>,
      $msg,
    );
  }

  /**
   * Asserts: $actual has the same content as $expected, i.e. the same items
   * regardless of order.
   */
  public function toHaveSameContentAs<Tk as arraykey>(
    KeyedContainer<Tk, mixed> $expected,
    string $msg = '',
    mixed ...$args
  ): void {
    $msg = \vsprintf($msg, $args);
    $value = $this->var;
    assert($value is Traversable<_>);
    $this->assertContentsEqual($expected, $value, $msg);
  }

  /**
   * Asserts: That a traversable is sorted according to a given comparator.
   */
  public function toBeSortedBy<Tv>(
    (function(Tv, Tv): bool) $comparator,
    string $msg = '',
    mixed ...$args
  ): void where T as Container<Tv> {
    $msg = \vsprintf($msg, $args);

    $actual = $this->var;
    invariant(
      $actual is Traversable<_>,
      'ERROR: expect(...$args)->toBeSortedByKey only can be applied to '.
      'Traversables, not %s.',
      print_type($actual),
    );

    $this->assertIsSorted($actual, $comparator, $msg);
  }

  /**
   * Asserts: That a traversable is sorted according to a given key extraction
   * function.
   */
  public function toBeSortedByKey<Tv>(
    (function(Tv): arraykey) $key_extractor,
    string $msg = '',
    mixed ...$args
  ): void where T as Traversable<Tv> {
    $msg = \vsprintf($msg, $args);

    $actual = $this->var;
    $this->assertIsSortedByKey($actual, $key_extractor, $msg);
  }

  /**************************************
   **************************************
   ******* Negated Basic Assertions******
   **************************************
   **************************************/

  /**
   * Asserts: $actual !== $expected
   * Note:    Two objects are considered the same if they reference the same
   *          instance
   */
  public function toNotEqual(
    mixed $expected,
    string $msg = '',
    mixed ...$args
  ): void {
    $this->toNotBeSame($expected, $msg, ...$args);
  }

  public function toNotBePHPEqual(
    mixed $expected,
    string $msg = '',
    mixed ...$args
  ): void {
    $msg = \vsprintf($msg, $args);
    $this->assertNotEquals($expected, $this->var, $msg);
  }

  // Asserts: $actual !== null
  public function toNotBeNull<Tv>(
    string $msg = '',
    mixed ...$args
  ): Tv where T = ?Tv {
    $msg = \vsprintf($msg, $args);
    $val = $this->var;
    $this->assertNotNull($val, $msg);
    return $val as nonnull;
  }

  /**
   * Asserts: !is_{$type}($actual)
   *
   * $type should be one of the basic type strings like 'int' or 'string'
   *
   * Example: expect($actual)->toNotBeType('string') would assert
   *          !is_string($actual)
   */
  public function toNotBeType(
    string $type,
    string $msg = '',
    mixed ...$args
  ): void {
    $msg = \vsprintf($msg, $args);
    $this->assertNotType($type, $this->var, $msg);
  }

  /**
   * Asserts: $actual !== $expected
   * Note:    Two objects are considered the same if they reference the same
   *          instance
   */
  public function toNotBeSame(
    mixed $expected,
    string $msg = '',
    mixed ...$args
  ): void {
    $msg = \vsprintf($msg, $args);
    $this->assertNotSame($expected, $this->var, $msg);
  }

  // Asserts: empty($actual) != true
  public function toNotBeEmpty(string $msg = '', mixed ...$args): void {
    $msg = \vsprintf($msg, $args);
    $this->assertNotEmpty($this->var, $msg);
  }

  // Asserts: !($actual instanceof $class_or_interface)
  public function toNotBeInstanceOf<Tv>(
    classname<Tv> $class_or_interface,
    string $msg = '',
    mixed ...$args
  ): void {
    $msg = \vsprintf($msg, $args);
    $this->assertNotInstanceOf($class_or_interface, $this->var, $msg);
  }

  /**
   * Assert: iterate through $actual and make sure there is no
   *         element for which $element == $needle.
   * Note:   If $needle is an object, === will be used.
   */
  public function toNotContain<TVal>(
    TVal $expected,
    string $msg = '',
    mixed ...$args
  ): void where T as Traversable<TVal> {
    $msg = \vsprintf($msg, $args);
    $this->assertNotContains($expected, $this->var, $msg);
  }

  /**
   * Assert: $actual does not contain the substring $expected
   */
  public function toNotContainSubstring(
    string $expected,
    string $msg = '',
    mixed ...$args
  ): void where T = string {
    $msg = \vsprintf($msg, $args);
    $this->assertNotContains($expected, $this->var, $msg);
  }

  /**
   * Assert: That the KeyedTraversible $key has a key set.
   * Note:   If $key is a Set, use assertContains.
   */
  public function toNotContainKey<TKey as arraykey, TVal>(
    TKey $key,
    string $msg = '',
    mixed ...$args
  ): void where T as KeyedContainer<TKey, TVal> {
    $msg = \vsprintf($msg, $args);
    $obj = $this->var;
    $this->assertFalse(\array_key_exists($key, $obj), $msg);
  }


  // Asserts: $actual does not match $expected regular expression
  public function toNotMatchRegExp(
    string $expected,
    string $msg = '',
    mixed ...$args
  ): void {
    $msg = \vsprintf($msg, $args);
    $this->assertNotRegExp($expected, (string)$this->var, $msg);
  }

  /***************************************
   ***************************************
   **** Function Exception Assertions ****
   ***************************************
   ***************************************
   *
   * Note: function can be any of the normal php callable types - closure,
   * function/method name as string or array($instance, 'method'). See
   * http://www.php.net/manual/en/language.types.callable.php for more info.
   */

  /**
   * Asserts: That a given function DOESN'T throw an exception.
   *
   * Example usage:
   *
   *   expect( () ==> 1 )->notToThrow(); // would pass
   *
   *   expect( () ==> invariant_violation('...') )->notToThrow(); // would fail
   */
  public function notToThrow(
    ?string $msg = null,
    mixed ...$args
  ): void where T as (function(): mixed) {
    $msg = \vsprintf($msg, $args);
    $e = $this->tryCallReturnException(\Exception::class);
    if ($e !== null) {
      $msg = Str\format(
        "%s was thrown: %s\n%s",
        \get_class($e),
        $msg,
        Vec\map(
          $e->getTrace(),
          $t ==> {
            $t = $t as KeyedContainer<_, _>;
            return Str\format(
              '%s: %d',
              idx($t, 'file') as ?string ?? '<no file>',
              idx($t, 'line') as ?int ?? -1,
            );
          },
        )
          |> Str\join($$, "\n  "),
      );
    }
    $this->assertNull($e, $msg);
  }

  /**
   * Asserts: Function throws exception of given type and with the given
   *          exception message (optional)
   *
   * Example usage:
   *
   *   expect(function() { invariant_violation('fail'); })
   *     ->toThrow(InvariantViolationException::class);
   *
   *   expect(function() { invariant_violation('fail'); })
   *     ->toThrow(InvariantViolationException::class, 'fail');
   *
   * If `TRet` is an `Awaitable<_>` (e.g. if an async function is provided),
   * the awaitable will be awaited.
   */
  public function toThrow<TException as \Throwable, TRet>(
    classname<TException> $exception_class,
    ?string $expected_exception_message = null,
    ?string $msg = null,
    mixed ...$args
  ): TException where T = (function(): TRet) {
    $msg = \vsprintf($msg ?? '', $args);
    $exception = $this->tryCallReturnException($exception_class);

    if (!$exception) {
      throw new \Exception(
        $msg.': Expected exception '.$exception_class." wasn't thrown",
      );
    }

    if ($expected_exception_message !== null) {
      $message = $exception->getMessage();

      $this->assertContains($expected_exception_message, $message, $msg);
    }

    return $exception;
  }

  public function toTriggerAnError(
    ?int $level = null,
    ?string $expected_error_message = null,
    ?Str\SprintfFormatString $msg = null,
    mixed ...$args
  ): void where T = (function(): mixed) {
    $captured_errors = new Ref<vec<(int, string)>>(vec[]);
    $error_level = \error_reporting(\E_ALL);
    \set_error_handler((int $level, string $message) ==> {
      $captured_errors->value[] = tuple($level, $message);
    });

    try {
      $return = ($this->var)();
      if ($return is Awaitable<_>) {
        /*HHAST_FIXME[DontUseAsioJoin]
          We cannot safely use await here,
          since we are messing with the error handler.*/
        \HH\Asio\join($return);
      }
    } finally {
      \error_reporting($error_level);
      \restore_error_handler();
    }

    $message = $msg is null ? '' : \vsprintf($msg, $args);
    if (C\is_empty($captured_errors->value)) {
      throw new HackTest\ExpectationFailedException(
        'Expected an error to be triggered, but got none.',
      );
    }

    $errors = $captured_errors->value;

    $passes = C\any(
      $errors,
      $e ==> ($e[0] === $level || $level is null) &&
        (
          $expected_error_message is null ||
          Str\contains($e[1], $expected_error_message)
        ),
    );

    if ($passes) {
      return;
    }

    throw new ExpectationFailedException(
      Str\format(
        "%sFailed asserting that the correct kind of error was triggered:\n Expected level: %s\n Expected message: %s\n Got: %s",
        $message,
        (string)($level ?? '<any>'),
        (string)($expected_error_message ?? '<any>'),
        \var_export($errors, true),
      ),
    );
  }

  /***************************************
   ***************************************
   **** Private implementation details ***
   ***************************************
   ***************************************/
  private function tryCallReturnException<Tclass as \Throwable>(
    classname<Tclass> $expected_exception_type,
  ): ?Tclass where T as (function(): mixed) {
    try {
      $callable = $this->var;
      $returned = $callable();

      if ($returned is Awaitable<_>) {
        /* HHAST_IGNORE_ERROR[DontUseAsioJoin] */
        \HH\Asio\join($returned);
      }
    } catch (\Exception $e) {
      $e = expect($e)->toBeInstanceOf(
        $expected_exception_type,
        'Expected to throw "%s", but instead got <%s> with message "%s"',
        $expected_exception_type,
        \get_class($e),
        $e->getMessage(),
      );
      return $e;
    }

    return null;
  }
}
