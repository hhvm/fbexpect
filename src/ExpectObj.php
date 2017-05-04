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

final class ExpectObj extends \PHPUnit\Framework\Assert {
  public function __construct(private ImmVector<mixed> $vars) { }

  /**************************************
   **************************************
   *********** Basic Assertions *********
   **************************************
   **************************************
   *
   * Example Usage: expect($actual)->toEqual($expected)
   *
   */

  /**
   * Asserts: Roughly $actual == $expected
   * Note:    Two objects are considered equal if
   *          (string)$o1 == (string)$o2.
   */
  public function toEqual($expected, string $msg = '', ...): void {
    $msg = vsprintf($msg, array_slice(func_get_args(), 2));
    $this->assertSingleArg(__FUNCTION__);
    $this->assertEquals($expected, $this->vars->firstValue(), $msg);
  }

  /**
   * Float comparison can give false positives - this will only error if $actual
   * and $expected are not within $delta of each other.
   */
  public function toEqualWithDelta($expected, float $delta, string $msg = '', ...): void {
    $msg = vsprintf($msg, array_slice(func_get_args(), 3));
    $this->assertSingleArg(__FUNCTION__);
    $this->assertEquals($expected, $this->vars->firstValue(), $msg, $delta);
  }

  public function toAlmostEqual($expected, string $msg = '', ...): void {
    $msg = vsprintf($msg, array_slice(func_get_args(), 2));
    $this->assertSingleArg(__FUNCTION__);
    $this->assertAlmostEquals($expected, $this->vars->firstValue(), $msg);
  }

  /**
   * Same as toEqual() except treats NAN as equal to itself.
   */
  public function toEqualWithNANEqual($expected, string $msg = '', ...): void {
    $msg = vsprintf($msg, array_slice(func_get_args(), 2));
    $this->assertSingleArg(__FUNCTION__);
    $this->assertEqualsWithNANEqual(
      $expected, $this->vars->firstValue(), $msg
    );
  }

  /**
   * Asserts: $actual === $expected
   * Note:    Two objects are considered the same if they reference the same
   *          instance
   */
  public function toBeSame($expected, string $msg = '', ...): void {
    $msg = vsprintf($msg, array_slice(func_get_args(), 2));
    $this->assertSingleArg(__FUNCTION__);
    $this->assertSame($expected, $this->vars->firstValue(), $msg);
  }

   // Asserts: $actual === true
  public function toBeTrue(string $msg = '', ...): void {
    $msg = vsprintf($msg, array_slice(func_get_args(), 1));
    $this->assertSingleArg(__FUNCTION__);
    $this->assertTrue($this->vars->firstValue(), $msg);
  }

  // Asserts: $actual === false
  public function toBeFalse(string $msg = '', ...): void {
    $msg = vsprintf($msg, array_slice(func_get_args(), 1));
    $this->assertSingleArg(__FUNCTION__);
    $this->assertFalse($this->vars->firstValue(), $msg);
  }

  // Asserts: $actual === null
  public function toBeNull(string $msg = '', ...): void {
    $msg = vsprintf($msg, array_slice(func_get_args(), 1));
    $this->assertSingleArg(__FUNCTION__);
    $this->assertNull($this->vars->firstValue(), $msg);
  }

  // Asserts: empty($actual) == true
  public function toBeEmpty(string $msg = '', ...): void {
    $msg = vsprintf($msg, array_slice(func_get_args(), 1));
    $this->assertSingleArg(__FUNCTION__);
    $this->assertEmpty($this->vars->firstValue(), $msg);
  }

  // Asserts: $actual > $expected
  public function toBeGreaterThan($expected, string $msg = '', ...): void {
    $msg = vsprintf($msg, array_slice(func_get_args(), 2));
    $this->assertSingleArg(__FUNCTION__);
    $this->assertGreaterThan($expected, $this->vars->firstValue(), $msg);
  }

  // Asserts: $actual < $expected
  public function toBeLessThan($expected, string $msg = '', ...): void {
    $msg = vsprintf($msg, array_slice(func_get_args(), 2));
    $this->assertSingleArg(__FUNCTION__);
    $this->assertLessThan($expected, $this->vars->firstValue(), $msg);
  }

  // Asserts: $actual <= $expected
  public function toBeLessThanOrEqualTo(
    $expected,
    string $msg = '',
    ...
  ): void {
    $msg = vsprintf($msg, array_slice(func_get_args(), 2));
    $this->assertSingleArg(__FUNCTION__);
    $this->assertLessThanOrEqual($expected, $this->vars->firstValue(), $msg);
  }

  // Asserts: $actual => $expected
  public function toBeGreaterThanOrEqualTo(
    $expected,
    string $msg = '',
    ...
  ): void {
    $msg = vsprintf($msg, array_slice(func_get_args(), 2));
    $this->assertSingleArg(__FUNCTION__);
    $this->assertGreaterThanOrEqual($expected, $this->vars->firstValue(), $msg);
  }

  // Asserts: $actual instanceof $type
  public function toBeInstanceOf(
    $class_or_interface,
    string $msg = '',
    ...
  ): void {
    $msg = vsprintf($msg, array_slice(func_get_args(), 2));
    $this->assertSingleArg(__FUNCTION__);
    $this->assertInstanceOf(
      $class_or_interface,
      $this->vars->firstValue(),
      $msg
    );
  }

  // Asserts: $actual matches $expected regular expression
  public function toMatchRegExp($expected, string $msg = '', ...): void {
    $msg = vsprintf($msg, array_slice(func_get_args(), 2));
    $this->assertSingleArg(__FUNCTION__);
    $this->assertRegExp($expected, $this->vars->firstValue(), $msg);
  }

  /**
   * Asserts: is_{$type}($actual)
   *
   * $type should be one of the basic type strings like 'int' or 'string'
   *
   * Example: expect($actual)->toBeType('string') would assert is_string($actual)
   */
  public function toBeType($type,  string $msg = '', ...): void {
    $msg = vsprintf($msg, array_slice(func_get_args(), 2));
    $this->assertSingleArg(__FUNCTION__);
    $this->assertType($type, $this->vars->firstValue(), $msg);
  }

  /**
   * Assert: For strings, strpos($actual, $needle) !== false
   *         For containers (array, Map, Set, etc) or objects which implement
   *         Traversable, iterate through $actual and see if any element ==
   *         $needle.
   * Note:   If $needle is an object, === will be used.
   */
  public function toContain($needle, string $msg = '', ...): void {
    $msg = vsprintf($msg, array_slice(func_get_args(), 2));
    $this->assertSingleArg(__FUNCTION__);
    $this->assertContains($needle, $this->vars->firstValue(), $msg);
  }

  /**
   * Assert: That the KeyedTraversible $key has a key set.
   * Note:   If $key is a Set, use assertContains.
   */
  public function toContainKey($key, string $msg = '', ...): void {
    $msg = vsprintf($msg, array_slice(func_get_args(), 2));
    $this->assertSingleArg(__FUNCTION__);
    $obj = $this->vars->firstValue();
    invariant(
      $obj instanceof KeyedTraversable,
      'ERROR: expect(...)->toContainKey only can be applied to '.
      'KeyedTraversables, not %s.',
      print_type($obj),
    );
    $this->assertTrue(array_key_exists($key, $obj), $msg);
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
    $msg = vsprintf($msg, array_slice(func_get_args(), 2));
    $this->assertSingleArg(__FUNCTION__);
    $this->assertSubset($expected_subset, $this->vars->firstValue(), $msg);
  }

  /**
   * Asserts: $actual has the same content as $expected, i.e. the same
   * key/values regardless of order.
   */
  public function toHaveSameShapeAs($expected, string $msg = '', ...): void {
    $msg = vsprintf($msg, array_slice(func_get_args(), 2));
    $this->assertSingleArg(__FUNCTION__);
    $this->assertKeyAndValueEquals(
      $expected, $this->vars->firstValue(), $msg
    );
  }

  /**
   * Asserts: $actual has the same content as $expected, i.e. the same items
   * regardless of order.
   */
  public function toHaveSameContentAs($expected, string $msg = '', ...): void {
    $msg = vsprintf($msg, array_slice(func_get_args(), 2));
    $this->assertSingleArg(__FUNCTION__);
    $this->assertContentsEqual(
      $expected, $this->vars->firstValue(), $msg
    );
  }

  /**
   * Asserts: That a traversable is sorted according to a given comparator.
   */
  public function toBeSortedBy($comparator, string $msg = '', ...): void {
    $msg = vsprintf($msg, array_slice(func_get_args(), 2));
    $this->assertSingleArg(__FUNCTION__);

    $actual = $this->vars->firstValue();
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
    $msg = vsprintf($msg, array_slice(func_get_args(), 2));
    $this->assertSingleArg(__FUNCTION__);

    $actual = $this->vars->firstValue();
    invariant(
      $actual instanceof Traversable,
      'ERROR: expect(...)->toBeSortedByKey only can be applied to '.
        'Traversables, not %s.',
      print_type($actual),
    );

    $this->assertIsSortedByKey($actual, $key_extractor, $msg);
  }

  /**
   * Asserts: $actual is roughly equal to the expected URI
   * Notes:
   *
   *   - Asserts that protocol (ex: "http"), domain, port are exactly the same
   *   - Paths are "cleaned up" before being compared (ex: '/' and no path are
   *     equal)
   *   - Both URIs must have same GET params but order doesn't matter. If a URI
   *     has a duplicate param, the last one will be considered
   */
  public function toEqualURI($expected_uri, string $msg = '', ...): void {
    $msg = vsprintf($msg, array_slice(func_get_args(), 2));
    $this->assertSingleArg(__FUNCTION__);
    $this->assertURIsEquivalent(
      $expected_uri,
      $this->vars->firstValue(),
      array(),
      $msg
    );
  }

  /**************************************
   **************************************
   ******* Negated Basic Assertions******
   **************************************
   **************************************/

  // Asserts: $actual != $expected
  public function toNotEqual($expected, string $msg = '', ...): void {
    $msg = vsprintf($msg, array_slice(func_get_args(), 2));
    $this->assertSingleArg(__FUNCTION__);
    $this->assertNotEquals($expected, $this->vars->firstValue(), $msg);
  }

  // Asserts: $actual !== null
  public function toNotBeNull(string $msg = '', ...) {
    $msg = vsprintf($msg, array_slice(func_get_args(), 1));
    $this->assertSingleArg(__FUNCTION__);
    $this->assertNotNull($this->vars->firstValue(), $msg);
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
    $msg = vsprintf($msg, array_slice(func_get_args(), 2));
    $this->assertSingleArg(__FUNCTION__);
    $this->assertNotType($type, $this->vars->firstValue(), $msg);
  }

  /**
   * Asserts: $actual !== $expected
   * Note:    Two objects are considered the same if they reference the same
   *          instance
   */
  public function toNotBeSame($expected, string $msg = '', ...): void {
    $msg = vsprintf($msg, array_slice(func_get_args(), 2));
    $this->assertSingleArg(__FUNCTION__);
    $this->assertNotSame($expected, $this->vars->firstValue(), $msg);
  }

  // Asserts: empty($actual) != true
  public function toNotBeEmpty(string $msg = '', ...): void {
    $msg = vsprintf($msg, array_slice(func_get_args(), 1));
    $this->assertSingleArg(__FUNCTION__);
    $this->assertNotEmpty($this->vars->firstValue(), $msg);
  }

  // Asserts: !($actual instanceof $class_or_interface)
  public function toNotBeInstanceOf(
    $class_or_interface,
    string $msg = '',
    ...): void {
    $msg = vsprintf($msg, array_slice(func_get_args(), 2));
    $this->assertSingleArg(__FUNCTION__);
    $this->assertNotInstanceOf(
      $class_or_interface,
      $this->vars->firstValue(),
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
    $msg = vsprintf($msg, array_slice(func_get_args(), 2));
    $this->assertSingleArg(__FUNCTION__);
    $this->assertNotContains($expected, $this->vars->firstValue(), $msg);
  }

  /**
   * Assert: That the KeyedTraversible $key has a key set.
   * Note:   If $key is a Set, use assertContains.
   */
  public function toNotContainKey($key, string $msg = '', ...): void {
    $msg = vsprintf($msg, array_slice(func_get_args(), 2));
    $this->assertSingleArg(__FUNCTION__);
    $obj = $this->vars->firstValue();
    invariant(
      $obj instanceof KeyedTraversable,
      'ERROR: expect(...)->toNotContainKey only can be applied to '.
      'KeyedTraversables, not %s.',
      print_type($obj),
    );
    $this->assertFalse(isset($obj[$key]), $msg);
  }


  // Asserts: $actual does not match $expected regular expression
  public function toNotMatchRegExp($expected, string $msg = '', ...): void {
    $msg = vsprintf($msg, array_slice(func_get_args(), 2));
    $this->assertSingleArg(__FUNCTION__);
    $this->assertNotRegExp($expected, $this->vars->firstValue(), $msg);
  }

  public function toMatchSnapshot(string $message = '', ...): void {
    $message = vsprintf($message, array_slice(func_get_args(), 1));
    $this->assertSingleArg(__FUNCTION__);
    $snapshot = TestSnapshotListener::getCurrentSnapshot();
    invariant(
      $snapshot !== null,
      'toMatchSnapshot(): Expected a snapshot to have been instantiated for '.
      'the currently running test. Does your test case implement the '.
      '`ISnapshotTest` interface?',
    );
    $data = first($this->vars);
    $this->assertMatchesSnapshot($snapshot, $data, $message);
  }

  /***************************************
   ***************************************
   ****** Function Call Assertions *******
   ***************************************
   ***************************************
   *
   * Assert that functions or methods were/weren't called.
   *
   *
   * Example usage:
   *
   *   expect($mock_foo, 'bar')->wasCalledOnce();
   *   expect('Foo::bar')->wasCalledOnce(); // Autobahn unit tests only
   *   expect('foo')->wasCalledOnce(); // Autobahn unit tests only
   */

  // Asserts: Function/method was called exactly once with any arguments
  public function wasCalledOnce(string $msg = '', ...): void {
    $msg = vsprintf($msg, array_slice(func_get_args(), 1));
    $this->assertCalledOnce(
      ...array_concat(make_array($this->vars), array(null), array($msg)),
    );
  }

  // Asserts: Function/method was called exactly twice with any arguments
  public function wasCalledTwice(string $msg = '', ...): void {
    $msg = vsprintf($msg, array_slice(func_get_args(), 1));
    $this->wasCalledNTimes(2, $msg);
  }

  // Asserts: Function/method was called exactly N times with any arguments
  public function wasCalledNTimes(int $times, string $msg = '', ...): void {
    $msg = vsprintf($msg, array_slice(func_get_args(), 2));
    $this->assertNumCalls(
      ...array_concat(make_array($this->vars), array($times, $msg)),
    );
  }

  /**
   * Asserts: Function/method was called exactly once and that call had the
   *          given arguments
   *
   * Example usage:
   *
   *   foo(1, 2, 3); // code under test
   *
   *   expect('foo')->wasCalledOnceWith(1, 2, 3); // passes
   *   expect('foo')->wasCalledOnceWith(4, 5, 6); // fails
   */
  public function wasCalledOnceWith(...): void {
    $this->assertCalledOnce(
      ...array_concat(make_array($this->vars), array(func_get_args())),
    );
  }

  /**
   * Asserts: Function/method was called at least once, and that the final call
   *    had the given arguments
   *
   * Example usage:
   *
   *   foo('aardvark'); // Code under test
   *   foo('badger');
   *
   *   expect('foo')->wasCalledLastWith('aardvark'; // Fail; not last call
   *   expect('foo')->wasCalledLastWith('badger'); // Pass
   */
  public function wasCalledLastWith(...): void {
    $args = array_concat(make_array($this->vars), array(func_get_args()));
    $this->assertCalledLastWith(...$args);
  }

  /**
   * Asserts: Calls to function/method match the given arrays of calls
   * Note:    order of the calls matters
   *
   * Example usage:
   *
   *   foo(1, 2, 3);
   *   foo(4, 5, 6);
   *
   *   expect('foo')->wasCalledWith(array(1,2,3), array(4,5,6)); // passes
   *   expect('foo')->wasCalledWith(array(4,5,6), array(1,2,3)); // fails
   */
  public function wasCalledWith(...): void {
    $this->assertCalls(
      ...array_concat(make_array($this->vars), func_get_args()),
    );
  }

  /**
   * Asserts: Calls to function/method match the given array of array of calls
   * Note:    This is the same as wasCalledWith() but can be called with an
   *          array instead of a variable number of arguments.
   *
   * Example usage:
   *
   *   foo(1, 2, 3);
   *   foo(4, 5, 6);
   *
   *   expect('foo')->wasCalledWithArray(array(array(1,2,3), array(4,5,6)));
   */
  public function wasCalledWithArray(array $calls, string $msg = '', ...): void {
    $msg = vsprintf($msg, array_slice(func_get_args(), 2));
    if ($msg) {
     $calls[] = $msg;
    }
    $this->assertCalls(
      ...array_concat(make_array($this->vars), $calls)
    );
  }

  /**
   * Asserts: All calls to function/method pass the given predicate
   *
   * Example usage:
   *
   *  foo(1, 2, 3);
   *
   *  expect('foo')->wasCalledWithArgumentsPassing(
   *    ($a, $b, $c) ==> return $b == 2
   *  );
   */
  public function wasCalledWithArgumentsPassing($f, $msg='') : void {
    $this->assertCallsPass(
      ...array_concat(make_array($this->vars), array($f, $msg)),
    );
  }

  // Asserts: Function/method was not called
  public function wasNotCalled(string $msg = '', ...): void {
    $msg = vsprintf($msg, array_slice(func_get_args(), 1));
    $this->assertNotCalled(
      ...array_append(make_array($this->vars), $msg)
    );
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
    ...
  ): void {
    $e = $this->tryCallWithArgsReturnException(array(), Exception::class);
    if ($e !== null) {
      $msg = sprintf(
        "%s was thrown: %s\n%s",
        get_class($e),
        vsprintf($msg, array_slice(func_get_args(), 1)),
        implode("\n  ", array_map(
          $t ==> sprintf(  '%s: %s', idx($t, 'file'), idx($t, 'line')),
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
  public function toThrow(
    string $exception_class,
    ?string $expected_exception_message = null,
    ?string $msg = null,
    ...
  ): void {
    $msg = vsprintf($msg, array_slice(func_get_args(), 3));
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
  public function toThrowWhenCalledWith(
    array $args,
    string $exception_class,
    ?string $expected_exception_message = null,
    ?string $desc = null
  ): void {
    $exception =
      $this->tryCallWithArgsReturnException($args, $exception_class);

    if (!$exception) {
      self::fail(
        "$desc: Expected exception $exception_class wasn't thrown"
      );
    }

    if ($expected_exception_message !== null) {
      $message = $exception->getMessage();
      // AliteRedirectExceptions implement getMessage, but return a URI instead
      // of a string.
      if ($message instanceof URI) {
        $message = $message->toString();
      }

      $this->assertContains(
        $expected_exception_message,
        $message,
        $desc
      );
    }
  }

  /**
   * Asserts: Function throws CodedException with given code.
   *          If API code and error data are given (optional), assert the
   *          CodedException has that API code and error data.
   */
  public function toThrowCodedException(
    int $code,
    ?int $api_code = null,
    $error_data = null,
    ?string $desc = null,
  ): void {
    $this->toThrowCodedExceptionWhenCalledWith(
      array(),
      $code,
      $api_code,
      $error_data,
      $desc
    );
  }

  /**
   * Asserts: Function throws CodedException with given code when called with
   *          the given args. If API code and error data are given (optional)
   *          assert the CodedException has that API code and error data.
   */
  public function toThrowCodedExceptionWhenCalledWith(
    array $args,
    int $code,
    ?int $api_code = null,
    $error_data = null,
    ?string $desc = null,
  ): void {
    $exception = $this->tryCallWithArgsReturnException($args, 'CodedException');

    if (!$exception) {
      self::fail($desc ?: "CodedException $code wasn't thrown");
    }

    expect($exception->getErrorCode())->toEqual(
      $code,
      $desc ?: 'A CodedException was thrown, but it didn\'t have '.
        'the expected error code',
    );

    if ($api_code !== null) {
      expect($exception->getApiErrorCode())->toEqual(
        $api_code,
        $desc ?: 'The CodedException didn\'t have the expected api code',
      );
    }

    if ($error_data !== null) {
      expect($exception->getErrorData())->toEqual(
        $error_data,
        $desc ?: 'The CodedException didn\'t have the expected error data',
      );
    }
  }

  /***************************************
   ***************************************
   **** Private implementation details ***
   ***************************************
   ***************************************/
  private function assertSingleArg(string $method) {
    invariant(
      count($this->vars) === 1,
      'Single arg expected for expect()->%s()',
      $method,
    );
  }

  private function tryCallWithArgsReturnException(
    array $args,
    string $expected_exception_type,
  ) {
    try {
      $callable = count($this->vars) == 1 ? $this->vars->firstValue() : $this->vars;
      $returned = call_user_func_array($callable, $args);

      if ($returned instanceof Awaitable) {
        $ret = Asio::awaitSynchronously($returned);
      }
    } catch (Exception $e) {
      expect($e)->toBeInstanceOf(
        $expected_exception_type,
        'Expected to throw "%s", but instead got <%s> with message "%s"',
        $expected_exception_type,
        get_class($e),
        $e->getMessage(),
      );
      return $e;
    }

    return null;
  }

  /**
   * Conditionally invert the expectation.
   *
   * expect($foo)->iff(foo === $bar)->toBeSame($bar) should always pass
   *
   * The return type is a lie for the benefit of Hack.
   */
  public function iff($condition) {
    if ($condition) {
      return $this;
    } else {
      // UNSAFE_BLOCK
      return new InvertExpect($this);
    }
    // UNSAFE_BLOCK (bonus)
  }
}
