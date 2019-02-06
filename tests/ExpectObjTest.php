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

use type Facebook\HackTest\{DataProvider, ExpectationFailedException, HackTest};

final class ExpectObjTestException extends \Exception {}

/**
 * Test expect() assertions
 */
final class ExpectObjTest extends HackTest {

  const EMPTY_VALUE = 'do not use this as a test value';

  public function testBasicSuccesses(): void {
    $o = new \stdClass();
    $o2 = new \stdClass();

    expect(1)->toBePHPEqual(1, 'custom msg');
    expect(true)->toNotBePHPEqual(false);
    expect(array(1, 2, 3))->toBePHPEqual(array(1, 2, 3));

    expect(1)->toBeGreaterThan(0);
    expect(1)->toBeGreaterThanOrEqualTo(0);
    expect(1)->toBeGreaterThanOrEqualTo(1);

    expect(0)->toBeLessThan(1);
    expect(0)->toBeLessThanOrEqualTo(0);
    expect(0)->toBeLessThanOrEqualTo(1);

    expect($o)->toBeSame($o);
    expect($o)->toNotBeSame($o2);

    expect(true)->toBeTrue();
    expect(false)->toBeFalse();
    expect(null)->toBeNull();
    expect(1)->toNotBeNull();
    expect(0)->toBeEmpty();
    expect(1)->toNotBeEmpty();
    expect($o)->toBeInstanceOf(\stdClass::class);
    expect(1)->toBeType('int');
    expect('a')->toNotBeType('int');
    expect(vec[])->toNotBeType('array');
    expect(dict[])->toBeType('dict');
    expect(keyset[])->toBeType('keyset');
    expect(Set {})->toBeType('Container');
    // vec[] is keyed by int type.
    expect(vec[])->toBeType('KeyedContainer');
    expect(dict[])->toBeType('KeyedContainer');
    expect(array(1, 2, 3))->toContain(2);
    expect(array(1, 2, 3))->toNotContain(7);

    // hack arrays
    expect(keyset[1])->toContain(1);
    expect(keyset[1, 2, 3])->toContain(2);
    expect(keyset[1, 2, 3])->toNotContain(7);
    expect(keyset[])->toNotContain(1);

    expect(vec[1])->toContain(1);
    expect(vec[1, 2, 3])->toContain(2);
    expect(vec[1, 2, 3])->toNotContain(7);
    expect(vec[])->toNotContain(1);

    expect(dict['x' => 1])->toContain(1);
    expect(dict['x' => 1, 'y' => 2, 'z' => 3])->toContain(2);
    expect(dict['x' => 1, 'y' => 2, 'z' => 3])->toNotContain(7);
    expect(dict[])->toNotContain(1);

    expect(dict['x' => 1])->toContainKey('x');
    expect(dict['x' => 1, 'y' => 2, 'z' => 3])->toContainKey('y');
    expect(dict['x' => 1, 'y' => 2, 'z' => 3])->toNotContainKey('1');
    expect(dict[])->toNotContainKey('x');

    // subsets are OK
    expect(array('k1' => 'v1', 'k2' => 'v2'))
      ->toInclude(array('k1' => 'v1'));
    expect(dict['k1' => 'v1', 'k2' => 'v2'])->toInclude(dict['k1' => 'v1']);

    // same set is OK, even if order is different
    expect(array('k2' => 'v2', 'k1' => 'v1'))
      ->toInclude(array('k1' => 'v1', 'k2' => 'v2'));
    expect(dict['k1' => 'v1', 'k2' => 'v2'])
      ->toInclude(dict['k1' => 'v1', 'k2' => 'v2']);

    // regex
    expect('haystack')->toMatchRegExp('/stack$/');
    expect('haystack')->toNotMatchRegExp('/needle/');

    // sorting
    expect(array(1, 2, 3))->toBeSortedBy(function(int $prev, int $curr): bool {
      if ($prev <= $curr) {
        return true;
      }
      return false;
    });

    expect(2)->toEqualWithDelta(1.99, 0.01);
  }

  /**
   * It's really important that test helpers fail correctly. Add custom expect
   * methods here in the form:
   *
   *   [method_name, test value, expected value (if necessary)]
   *
   */
  public function provideFailureCases() {
    $o = new \stdClass();
    return array(
      array('toBePHPEqual', false, true),
      array('toNotBePHPEqual', false, false),
      array('toBeGreaterThan', 1, 1),
      array('toBeLessThan', 1, 1),
      array('toBeGreaterThanOrEqualTo', 1, 2),
      array('toBeLessThanOrEqualTo', 2, 1),
      array('toBeSame', $o, new \stdClass()),
      array('toNotBeSame', $o, $o),
      array('toBeTrue', false),
      array('toBeFalse', true),
      array('toBeNull', false),
      array('toNotBeNull', null),
      array('toBeEmpty', 1),
      array('toNotBeEmpty', 0),
      array('toNotBeInstanceOf', $o, 'stdClass'),
      array('toBeType', 'a', 'int'),
      array('toNotBeType', 1, 'int'),
      array('toContain', array(1, 2, 3), 7),
      array('toNotContain', array(1, 2, 3), 2),

      // hack arrays
      array('toContain', keyset[1, 2, 3], 7),
      array('toNotContain', keyset[1, 2, 3], 2),
      array('toContain', keyset[], 2),

      array('toContain', vec[1, 2, 3], 7),
      array('toNotContain', vec[1, 2, 3], 2),
      array('toContain', vec[], 2),

      array('toContain', dict['x' => 1, 'y' => 2, 'z' => 3], 7),
      array('toNotContain', dict['x' => 1, 'y' => 2, 'z' => 3], 2),
      array('toContain', dict[], 2),

      array('toContainKey', dict['x' => 1, 'y' => 2, 'z' => 3], '1'),
      array('toNotContainKey', dict['x' => 1, 'y' => 2, 'z' => 3], 'y'),
      array('toContainKey', dict[], 'a'),

      // superset is not OK
      array(
        'toInclude',
        array('k1' => 'v1'),
        array('k1' => 'v1', 'k2' => 'v2'),
      ),
      array(
        'toInclude',
        dict['k1' => 'v1'],
        dict['k1' => 'v1', 'k2' => 'v2'],
      ),

      // values have to equal
      array(
        'toInclude',
        array('k1' => 'v1', 'k2' => 'v2'),
        array('k1' => 'v2'),
      ),
      array(
        'toInclude',
        dict['k1' => 'v1', 'k2' => 'v2'],
        dict['k1' => 'v2'],
      ),
    );
  }

  <<DataProvider('provideFailureCases')>>

  public function testBasicFailure(
    $func,
    $values,
    $expected = self::EMPTY_VALUE,
  ): void {
    if ($expected === self::EMPTY_VALUE) {
      expect(() ==> call_user_func(array(expect($values), $func)))->toThrow(
        ExpectationFailedException::class,
      );
    } else {
      expect(() ==> call_user_func(array(expect($values), $func), $expected))
        ->toThrow(ExpectationFailedException::class);
    }
  }

  <<DataProvider('provideFailureCases')>>

  public function testFailureWithCustomMsg(
    $func,
    $value,
    $expected = self::EMPTY_VALUE,
  ): void {

    if ($expected === self::EMPTY_VALUE) {
      expect(() ==> call_user_func(array(expect($value), $func), 'custom msg'))
        ->toThrow(ExpectationFailedException::class, 'custom msg');
    } else {
      expect(
        () ==>
          call_user_func(array(expect($value), $func), $expected, 'custom msg'),
      )->toThrow(ExpectationFailedException::class, 'custom msg');
      ;
    }

    // And with funky sprintfification
    if ($expected === self::EMPTY_VALUE) {
      expect(
        () ==> \call_user_func_array(
          array(expect($value), $func),
          array('custom %s %d %f', 'msg', 1, 2.1),
        ),
      )->toThrow(ExpectationFailedException::class, 'custom msg 1 2.1');
    } else {
      expect(
        () ==> \call_user_func_array(
          array(expect($value), $func),
          array($expected, 'custom %s %d %f', 'msg', 1, 2.1),
        ),
      )->toThrow(ExpectationFailedException::class, 'custom msg 1 2.1');
    }
  }

  public function testAssertEqualsWithDeltaFailure(): void {
    expect(() ==> expect(3.14)->toEqualWithDelta(3.1, 0.0001))->toThrow(
      ExpectationFailedException::class,
    );
  }

  //
  // Tests for toThrow methods
  //
  public function testToThrowWhenCalledWithSuccess(): void {
    expect(
      function() {
        throw new \Exception();
      },
    )->toThrow(\Exception::class);

    expect(
      function($class) {
        throw new $class();
      },
    )->toThrowWhenCalledWith(
      array(ExpectObjTestException::class),
      ExpectObjTestException::class,
    );

    expect(
      function() {
        throw new ExpectObjTestException('test msg');
      },
    )->toThrow(ExpectObjTestException::class, 'test msg');
  }

  public function testToThrowWithMessage(): void {
    expect(
      function() {
        expect(
          () ==> {
            throw new ExpectObjTestException('test 2');
          },
        )->toThrow(
          ExpectObjTestException::class,
          'test error',
          'test message does not match',
        );
      },
    )->toThrow(
      ExpectationFailedException::class,
      'test message does not match',
    );
  }

  private static function stopEagerExecution(): RescheduleWaitHandle {
    return RescheduleWaitHandle::create(RescheduleWaitHandle::QUEUE_DEFAULT, 0);
  }

  public function testAwaitableFunctionGetsPrepped(): void {
    expect(
      async function($class) {
        await self::stopEagerExecution();
        throw new $class();
      },
    )->toThrowWhenCalledWith(
      array(ExpectObjTestException::class),
      ExpectObjTestException::class,
    );
  }

  public function testToHaveSameShapeAsSuccess(): void {
    expect(
      () ==> {
        expect(
          shape(
            'a' => 5,
            'b' => 4,
            'c' => 3,
          ),
        )->toHaveSameShapeAs(
          shape(
            'b' => 4,
            'c' => 3,
            'a' => 5,
          ),
        );
      },
    )->notToThrow();
  }

  public function testToHaveSameContentAsSuccess(): void {
    expect(
      () ==> {
        expect(Set { 1, 2 })->toHaveSameContentAs(Vector { 2, 1 });
        expect(array(3))->toHaveSameContentAs(Map { 1 => 3 });
      },
    )->notToThrow();
  }

  public function testToHaveSameContentAsFailure(): void {
    expect(() ==> expect(array(1, 2))->toHaveSameContentAs(Vector { 1 }))
      ->toThrow(ExpectationFailedException::class);
  }

  public function testToHaveSameShapeAsFailure(): void {
    expect(
      () ==> {
        expect(
          shape(
            'a' => 5,
            'b' => 4,
            'c' => 3,
          ),
        )->toHaveSameShapeAs(
          shape(
            'b' => 4,
            'c' => 3,
            'a' => 5,
          ),
        );
      },
    )->notToThrow();
    expect(
      () ==> {
        expect(
          shape(
            'a' => 5,
            'b' => 4,
            'c' => 3,
          ),
        )->toHaveSameShapeAs(
          shape(
            'a' => 4,
            'b' => 3,
            'c' => 5,
          ),
        );
      },
    )->toThrow(ExpectationFailedException::class);
  }

  public function testInstanceOfTyping(): void {
    // This test is primarily for the typechecker, not the runtime
    $x = ((): mixed ==> new \Exception('foo'))();
    $x = expect($x)->toBeInstanceOf(\Exception::class);
    expect($x->getMessage())->toBeSame('foo');
  }

  public function testNotNull(): void {
    $x = ((): ?\Exception ==> new \Exception('foo'))();
    $x = expect($x)->toNotBeNull();
    expect($x->getMessage())->toBeSame('foo');
  }

  public function testDifferingStringOutput(): void {
    try {
      expect("a\nb\nd\n")->toBeSame("a\nb\nc\n");
    } catch (ExpectationFailedException $e) {
      expect($e->getMessage())->toContain(" a\n b\n-c\n+d\n");
      return;
    }
    self::fail("Should have thrown an exception");
  }
}
