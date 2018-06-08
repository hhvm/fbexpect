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

class ExpectObj<T> extends Assert {
  public function __construct(private T $var) {
  }

  /**************************************
   **************************************
   *********** Basic Assertions *********
   **************************************
   **************************************
   *
   * Example Usage: expect($actual)->toBeSame($expected)
   *
   */

  <<__Deprecated("Use toBeSame() or toBePHPEqual()")>>
  public function toEqual($expected, string $msg = '', ...$args): void {
    $this->toBePHPEqual($expected, $msg, ...$args);
  }

  /**
   * Asserts: Roughly $actual == $expected
   * Note:    Two objects are considered equal if
   *          (string)$o1 == (string)$o2.
   */
  public function toBePHPEqual($expected, string $msg = '', ...): void {
    $msg = \vsprintf($msg, \array_slice(\func_get_args(), 2));
    $this->assertEquals($expected, $this->var, $msg);
  }

  /**
   * Float comparison can give false positives - this will only error if $actual
   * and $expected are not within $delta of each other.
   */
  public function toEqualWithDelta($expected, float $delta, string $msg = '', ...): void {
    $msg = \vsprintf($msg, \array_slice(\func_get_args(), 3));
    $this->assertEquals($expected, $this->var, $msg, $delta);
  }

  public function toAlmostEqual($expected, string $msg = '', ...): void {
    $msg = \vsprintf($msg, \array_slice(\func_get_args(), 2));
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
    ...$args
  ): void {
    $this->toBePHPEqualWithNANEqual($expected, $msg, ...$args);
  }

  /**
   * Same as toEqual() except treats NAN as equal to itself.
   */
  public function toBePHPEqualWithNANEqual(
    $expected,
    string $msg = '',
    ...
  ): void {
    $msg = \vsprintf($msg, \array_slice(\func_get_args(), 2));

    $actual = $this->var;
    if (
      is_float($expected) &&
      is_float($actual) &&
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
  public function toBeSame($expected, string $msg = '', ...): void {
    $msg = \vsprintf($msg, \array_slice(\func_get_args(), 2));
    $this->assertSame($expected, $this->var, $msg);
  }

   // Asserts: $actual === true
  public function toBeTrue(string $msg = '', ...): void {
    $msg = \vsprintf($msg, \array_slice(\func_get_args(), 1));
    $this->assertTrue($this->var, $msg);
  }

  // Asserts: $actual === false
  public function toBeFalse(string $msg = '', ...): void {
    $msg = \vsprintf($msg, \array_slice(\func_get_args(), 1));
    $this->assertFalse($this->var, $msg);
  }

  // Asserts: $actual === null
  public function toBeNull(string $msg = '', ...): void {
    $msg = \vsprintf($msg, \array_slice(\func_get_args(), 1));
    $this->assertNull($this->var, $msg);
  }

  // Asserts: empty($actual) == true
  public function toBeEmpty(string $msg = '', ...): void {
    $msg = \vsprintf($msg, \array_slice(\func_get_args(), 1));
    $this->assertEmpty($this->var, $msg);
  }

  // Asserts: $actual > $expected
  public function toBeGreaterThan($expected, string $msg = '', ...): void {
    $msg = \vsprintf($msg, \array_slice(\func_get_args(), 2));
    $this->assertGreaterThan($expected, $this->var, $msg);
  }

  // Asserts: $actual < $expected
  public function toBeLessThan($expected, string $msg = '', ...): void {
    $msg = \vsprintf($msg, \array_slice(\func_get_args(), 2));
    $this->assertLessThan($expected, $this->var, $msg);
  }

  // Asserts: $actual <= $expected
  public function toBeLessThanOrEqualTo(
    $expected,
    string $msg = '',
    ...
  ): void {
    $msg = \vsprintf($msg, \array_slice(\func_get_args(), 2));
    $this->assertLessThanOrEqual($expected, $this->var, $msg);
  }

  // Asserts: $actual => $expected
  public function toBeGreaterThanOrEqualTo(
    $expected,
    string $msg = '',
    ...
  ): void {
    $msg = \vsprintf($msg, \array_slice(\func_get_args(), 2));
    $this->assertGreaterThanOrEqual($expected, $this->var, $msg);
  }

  // Asserts: $actual instanceof $type
  public function toBeInstanceOf<Tclass>(
    classname<Tclass> $class_or_interface,
    string $msg = '',
    ...
  ): Tclass {
    $msg = \vsprintf($msg, \array_slice(\func_get_args(), 2));
    $obj = $this->var;
    $this->assertInstanceOf(
      $class_or_interface,
      $obj,
      $msg,
    );
    return /* HH_IGNORE_ERROR[4110] */ $obj;
  }

  // Asserts: $actual matches $expected regular expression
  public function toMatchRegExp($expected, string $msg = '', ...): void {
    $msg = \vsprintf($msg, \array_slice(\func_get_args(), 2));
    $this->assertRegExp($expected, (string) $this->var, $msg);
  }

  /**
   * Asserts: is_{$type}($actual)
   *
   * $type should be one of the basic type strings like 'int' or 'string'
   *
   * Example: expect($actual)->toBeType('string') would assert is_string($actual)
   */
  public function toBeType($type,  string $msg = '', ...): void {
    $msg = \vsprintf($msg, \array_slice(\func_get_args(), 2));
    $this->assertType($type, $this->var, $msg);
  }

  /**
   * Assert: For strings, strpos($actual, $needle) !== false
   *         For containers (array, Map, Set, etc) or objects which implement
   *         Traversable, iterate through $actual and see if any element ==
   *         $needle.
   * Note:   If $needle is an object, === will be used.
   */
  public function toContain($needle, string $msg = '', ...): void {
    $msg = \vsprintf($msg, \array_slice(\func_get_args(), 2));
    $this->assertContains(
      $needle,
      not_hack_array($this->var),
      $msg,
    );
  }

  /**
   * Assert: That the KeyedTraversible $key has a key set.
   * Note:   If $key is a Set, use assertContains.
   */
  public function toContainKey($key, string $msg = '', mixed ...$args): void {
    $msg = \vsprintf($msg, $args);
    $obj = $this->var;
    invariant(
      $obj instanceof KeyedContainer,
      'ERROR: expect(...)->toContainKey only can be applied to '.
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
  public function toInclude($expected_subset, string $msg = '', ...): void {
    $msg = \vsprintf($msg, \array_slice(\func_get_args(), 2));
    $this->assertSubset($expected_subset, $this->var, $msg);
  }

  /**
   * Asserts: $actual has the same content as $expected, i.e. the same
   * key/values regardless of order.
   */
  public function toHaveSameShapeAs(
    $expected,
    string $msg = '',
    mixed ...$args
  ): void {
    $msg = \vsprintf($msg, $args);

    $value = $this->var;
    $this->assertKeyAndValueEquals(
      $expected,
      is_array($value) ? $value : [],
      $msg,
    );
  }

  /**
   * Asserts: $actual has the same content as $expected, i.e. the same items
   * regardless of order.
   */
  public function toHaveSameContentAs($expected, string $msg = '', ...): void {
    $msg = \vsprintf($msg, \array_slice(\func_get_args(), 2));
    $value = $this->var;
    $this->assertInstanceOf(Traversable::class, $value);
    assert($value instanceof Traversable);
    $this->assertContentsEqual($expected, $value, $msg);
  }

  /**
   * Asserts: That a traversable is sorted according to a given comparator.
   */
  public function toBeSortedBy($comparator, string $msg = '', ...): void {
    $msg = \vsprintf($msg, \array_slice(\func_get_args(), 2));

    $actual = $this->var;
    invariant(
      $actual instanceof Traversable,
      'ERROR: expect(...)->toBeSortedByKey only can be applied to '.
        'Traversables, not %s.',
      print_type($actual),
    );

    $this->assertIsSorted($actual, $comparator, $msg);
  }

  /**
   * Asserts: That a traversable is sorted according to a given key extraction
   * function.
   */
  public function toBeSortedByKey($key_extractor, string $msg = '', ...): void {
    $msg = \vsprintf($msg, \array_slice(\func_get_args(), 2));

    $actual = $this->var;
    invariant(
      $actual instanceof Traversable,
      'ERROR: expect(...)->toBeSortedByKey only can be applied to '.
        'Traversables, not %s.',
      print_type($actual),
    );

    $this->assertIsSortedByKey($actual, $key_extractor, $msg);
  }

  /**************************************
   **************************************
   ******* Negated Basic Assertions******
   **************************************
   **************************************/

  <<__Deprecated("Use toNotBeSame() or toNotBePHPEqual()")>>
  public function toNotEqual($expected, string $msg = '', ...$args): void {
    $this->toNotBePHPEqual($expected, $msg, ...$args);
  }

  public function toNotBePHPEqual($expected, string $msg = '', ...): void {
    $msg = \vsprintf($msg, \array_slice(\func_get_args(), 2));
    $this->assertNotEquals($expected, $this->var, $msg);
  }

  // Asserts: $actual !== null
  public function toNotBeNull<Tv>(string $msg = '', ...): Tv where T = ?Tv {
    $msg = \vsprintf($msg, \array_slice(\func_get_args(), 1));
    $val = $this->var;
    $this->assertNotNull($val, $msg);
    return /* HH_IGNORE_ERROR[4110] */ $val;
  }

  /**
   * Asserts: !is_{$type}($actual)
   *
   * $type should be one of the basic type strings like 'int' or 'string'
   *
   * Example: expect($actual)->toNotBeType('string') would assert
   *          !is_string($actual)
   */
  public function toNotBeType($type,  string $msg = '', ...): void {
    $msg = \vsprintf($msg, \array_slice(\func_get_args(), 2));
    $this->assertNotType($type, $this->var, $msg);
  }

  /**
   * Asserts: $actual !== $expected
   * Note:    Two objects are considered the same if they reference the same
   *          instance
   */
  public function toNotBeSame($expected, string $msg = '', ...): void {
    $msg = \vsprintf($msg, \array_slice(\func_get_args(), 2));
    $this->assertNotSame($expected, $this->var, $msg);
  }

  // Asserts: empty($actual) != true
  public function toNotBeEmpty(string $msg = '', ...): void {
    $msg = \vsprintf($msg, \array_slice(\func_get_args(), 1));
    $this->assertNotEmpty($this->var, $msg);
  }

  // Asserts: !($actual instanceof $class_or_interface)
  public function toNotBeInstanceOf(
    $class_or_interface,
    string $msg = '',
    ...): void {
    $msg = \vsprintf($msg, \array_slice(\func_get_args(), 2));
    $this->assertNotInstanceOf(
      $class_or_interface,
      $this->var,
      $msg
    );
  }

  /**
   * Assert: For strings, strpos($actual, $needle) === false
   *         For containers (array, Map, Set, etc) or objects which implement
   *         Traversable, iterate through $actual and make sure there is no
   *         element for which $element == $needle.
   * Note:   If $needle is an object, === will be used.
   */
  public function toNotContain($expected, string $msg = '', ...): void {
    $msg = \vsprintf($msg, \array_slice(\func_get_args(), 2));
    $this->assertNotContains(
      $expected,
      not_hack_array($this->var),
      $msg,
    );
  }

  /**
   * Assert: That the KeyedTraversible $key has a key set.
   * Note:   If $key is a Set, use assertContains.
   */
  public function toNotContainKey($key, string $msg = '', ...): void {
    $msg = \vsprintf($msg, \array_slice(\func_get_args(), 2));
    $obj = $this->var;
    invariant(
      $obj instanceof KeyedContainer,
      'ERROR: expect(...)->toNotContainKey only can be applied to '.
      'KeyedContainers, not %s.',
      print_type($obj),
    );
    $this->assertFalse(isset($obj[$key]), $msg);
  }


  // Asserts: $actual does not match $expected regular expression
  public function toNotMatchRegExp($expected, string $msg = '', ...): void {
    $msg = \vsprintf($msg, \array_slice(\func_get_args(), 2));
    $this->assertNotRegExp($expected, (string) $this->var, $msg);
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
  ): void {
    $msg = \vsprintf($msg, $args);
    $e = $this->tryCallWithArgsReturnException(array(), \Exception::class);
    if ($e !== null) {
      $msg = \sprintf(
        "%s was thrown: %s\n%s",
        \get_class($e),
        $msg,
        \implode("\n  ", \array_map(
          $t ==> \sprintf(  '%s: %s', idx($t, 'file'), idx($t, 'line')),
          $e->getTrace(),
        )),
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
   */
  public function toThrow<Tclass as \Exception>(
    classname<Tclass> $exception_class,
    ?string $expected_exception_message = null,
    ?string $msg = null,
    ...
  ): void {
    $msg = \vsprintf($msg, \array_slice(\func_get_args(), 3));
    $this->toThrowWhenCalledWith(
      array(),
      $exception_class,
      $expected_exception_message,
      $msg
    );
  }

  /**
   * Asserts: Function throws exception of given type and with the given
   *          exception message (optional)
   *
   * Example usage:
   *
   *   expect(
   *     function($a) { if ($a == 'foo') { invariant_violation('fail'); }}
   *   )->toThrowWhenCalledWith(array('foo'), 'InvariantViolationException');
   */
  public function toThrowWhenCalledWith<Tclass as \Exception>(
    array $args,
    classname<Tclass> $exception_class,
    ?string $expected_exception_message = null,
    ?string $desc = null
  ): void {
    $exception =
      $this->tryCallWithArgsReturnException($args, $exception_class);

    if (!$exception) {
      $this->fail(
        "$desc: Expected exception $exception_class wasn't thrown"
      );
    }

    if ($expected_exception_message !== null) {
      $message = $exception->getMessage();

      $this->assertContains(
        $expected_exception_message,
        $message,
        $desc ?? '',
      );
    }
  }

  /***************************************
   ***************************************
   **** Private implementation details ***
   ***************************************
   ***************************************/
  private function tryCallWithArgsReturnException<Tclass as \Exception>(
    array $args,
    classname<Tclass> $expected_exception_type,
  ) {
    try {
      $callable = $this->var;
      $returned = \call_user_func_array($callable, $args);

      if ($returned instanceof Awaitable) {
        $ret = \HH\Asio\join($returned);
      }
    } catch (\Exception $e) {
      expect($e)->toBeInstanceOf(
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
