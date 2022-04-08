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
    expect(vec[1, 2, 3])->toBePHPEqual(vec[1, 2, 3]);

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
    expect(vec[1, 2, 3])->toContain(2);
    expect(vec[1, 2, 3])->toNotContain(7);
    expect('foo')->toContainSubstring('foo');
    expect('foo')->toContainSubstring('o');
    expect('foo')->toNotContainSubstring('a');
    expect(1)->toAlmostEqual(1);
    expect(null)->toAlmostEqual(null);

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
    expect(dict['k1' => 'v1', 'k2' => 'v2'])
      ->toInclude(dict['k1' => 'v1']);
    expect(dict['k1' => 'v1', 'k2' => 'v2'])->toInclude(dict['k1' => 'v1']);

    // same set is OK, even if order is different
    expect(dict['k2' => 'v2', 'k1' => 'v1'])
      ->toInclude(dict['k1' => 'v1', 'k2' => 'v2']);
    expect(dict['k1' => 'v1', 'k2' => 'v2'])
      ->toInclude(dict['k1' => 'v1', 'k2' => 'v2']);

    // regex
    expect('haystack')->toMatchRegExp('/stack$/');
    expect('haystack')->toNotMatchRegExp('/needle/');

    // sorting
    expect(vec[1, 2, 3])->toBeSortedBy(function(int $prev, int $curr): bool {
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
  public function provideFailureCases(): vec<vec<mixed>> {
    $o = new \stdClass();
    return vec[
      vec['toBePHPEqual', false, true],
      vec['toNotBePHPEqual', false, false],
      vec['toBeGreaterThan', 1, 1],
      vec['toBeLessThan', 1, 1],
      vec['toBeGreaterThanOrEqualTo', 1, 2],
      vec['toBeLessThanOrEqualTo', 2, 1],
      vec['toBeSame', $o, new \stdClass()],
      vec['toNotBeSame', $o, $o],
      vec['toBeTrue', false],
      vec['toBeFalse', true],
      vec['toBeNull', false],
      vec['toNotBeNull', null],
      vec['toBeEmpty', 1],
      vec['toNotBeEmpty', 0],
      vec['toNotBeInstanceOf', $o, 'stdClass'],
      vec['toBeType', 'a', 'int'],
      vec['toNotBeType', 1, 'int'],
      vec['toContain', vec[1, 2, 3], 7],
      vec['toNotContain', vec[1, 2, 3], 2],
      vec['toAlmostEqual', null, 0.0],

      // hack arrays
      vec['toContain', keyset[1, 2, 3], 7],
      vec['toNotContain', keyset[1, 2, 3], 2],
      vec['toContain', keyset[], 2],

      vec['toContain', vec[1, 2, 3], 7],
      vec['toNotContain', vec[1, 2, 3], 2],
      vec['toContain', vec[], 2],

      vec['toContain', dict['x' => 1, 'y' => 2, 'z' => 3], 7],
      vec['toNotContain', dict['x' => 1, 'y' => 2, 'z' => 3], 2],
      vec['toContain', dict[], 2],

      vec['toContainKey', dict['x' => 1, 'y' => 2, 'z' => 3], '1'],
      vec['toNotContainKey', dict['x' => 1, 'y' => 2, 'z' => 3], 'y'],
      vec['toContainKey', dict[], 'a'],

      // superset is not OK
      vec[
        'toInclude',
        dict['k1' => 'v1'],
        dict['k1' => 'v1', 'k2' => 'v2'],
      ],
      vec[
        'toInclude',
        dict['k1' => 'v1'],
        dict['k1' => 'v1', 'k2' => 'v2'],
      ],

      // values have to equal
      vec[
        'toInclude',
        dict['k1' => 'v1', 'k2' => 'v2'],
        dict['k1' => 'v2'],
      ],
      vec[
        'toInclude',
        dict['k1' => 'v1', 'k2' => 'v2'],
        dict['k1' => 'v2'],
      ],
    ];
  }

  <<DataProvider('provideFailureCases')>>
  public function testBasicFailure(
    string $func,
    mixed $actual,
    mixed $expected = self::EMPTY_VALUE,
  ): void {
    $obj = expect($actual);
    $rm = new \ReflectionMethod($obj, $func);

    if ($expected === self::EMPTY_VALUE) {
      expect(() ==> $rm->invoke($obj))->toThrow(
        ExpectationFailedException::class,
      );
    } else {
      expect(() ==> $rm->invokeArgs($obj, vec[$expected]))
        ->toThrow(ExpectationFailedException::class);
    }
  }

  <<DataProvider('provideFailureCases')>>
  public function testFailureWithCustomMsg(
    string $func,
    mixed $actual,
    mixed $expected = self::EMPTY_VALUE,
  ): void {
    $obj = expect($actual);
    $rm = new \ReflectionMethod($obj, $func);

    if ($expected === self::EMPTY_VALUE) {
      expect(() ==> $rm->invokeArgs($obj, vec['custom msg']))
        ->toThrow(ExpectationFailedException::class, 'custom msg');
    } else {
      expect(() ==> $rm->invokeArgs($obj, vec[$expected, 'custom msg']))
        ->toThrow(ExpectationFailedException::class, 'custom msg');
      ;
    }

    // And with funky sprintfification
    if ($expected === self::EMPTY_VALUE) {
      expect(
        () ==> $rm->invokeArgs($obj, vec['custom %s %d %f', 'msg', 1, 2.1]),
      )
        ->toThrow(ExpectationFailedException::class, 'custom msg 1 2.1');
    } else {
      expect(
        () ==> $rm->invokeArgs(
          $obj,
          vec[$expected, 'custom %s %d %f', 'msg', 1, 2.1],
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
      async () ==> {
        $class = ExpectObjTestException::class;
        await self::stopEagerExecution();
        if ($class === ExpectObjTestException::class) {
          throw new ExpectObjTestException();
        }
      },
    )->toThrow(ExpectObjTestException::class);
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

    // two arrays
    expect(
      () ==> {
        expect(
          dict[
            'a' => 5,
            'b' => 4,
            'c' => 3,
          ],
        )->toHaveSameShapeAs(
          dict[
            'b' => 4,
            'c' => 3,
            'a' => 5,
          ],
        );
      },
    )->notToThrow();

    // two dicts
    expect(
      () ==> {
        expect(
          dict[
            'a' => 5,
            'b' => 4,
            'c' => 3,
          ],
        )->toHaveSameShapeAs(
          dict[
            'b' => 4,
            'c' => 3,
            'a' => 5,
          ],
        );
      },
    )->notToThrow();
  }

  public function testToHaveSameContentAsSuccess(): void {
    expect(
      () ==> {
        expect(Set {1, 2})->toHaveSameContentAs(Vector {2, 1});
        expect(vec[3])->toHaveSameContentAs(Map {1 => 3});
      },
    )->notToThrow();
  }

  public function testToHaveSameContentAsFailure(): void {
    expect(() ==> expect(vec[1, 2])->toHaveSameContentAs(Vector {1}))
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

    expect(
      () ==> {
        expect(
          dict[
            'a' => 5,
            'b' => 4,
            'c' => 3,
          ],
        )->toHaveSameShapeAs(
          dict[
            'a' => 4,
            'b' => 3,
            'c' => 5,
          ],
        );
      },
    )->toThrow(ExpectationFailedException::class);

    expect(
      () ==> {
        expect(
          dict[
            'a' => 5,
            'b' => 4,
            'c' => 3,
          ],
        )->toHaveSameShapeAs(
          dict[
            'a' => 4,
            'b' => 3,
            'c' => 5,
          ],
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
      expect($e->getMessage())->toContainSubstring(" a\n b\n-c\n+d\n");
      return;
    }
    self::fail('Should have thrown an exception');
  }

  public function testToThrowReturnsException(): void {
    $e = expect(() ==> {
      throw new \Exception('Hello, world');
    })->toThrow(\Exception::class);
    expect($e->getMessage())->toContainSubstring('Hello, world');
  }

  public function testToTriggerAnError(): void {
    \set_error_handler('not_a_function');

    expect(
      () ==> expect(() ==> \trigger_error('Herp derp', \E_USER_WARNING))
        ->toTriggerAnError(),
    )->notToThrow();

    expect(() ==> expect(() ==> {})->toTriggerAnError())->toThrow(
      ExpectationFailedException::class,
      'Expected an error to be triggered, but got none.',
    );

    expect(
      () ==> expect(() ==> \trigger_error('Herp derp', \E_USER_WARNING))
        ->toTriggerAnError(\E_USER_WARNING, 'Herp derp'),
    )->notToThrow();

    // Incorrect error level
    expect(
      () ==> expect(() ==> \trigger_error('Herpi durp', \E_USER_NOTICE))
        ->toTriggerAnError(\E_USER_WARNING, 'Herpi durp'),
    )->toThrow(ExpectationFailedException::class);

    expect(
      () ==> expect(() ==> \trigger_error('Herpi derp doom', \E_USER_WARNING))
        ->toTriggerAnError(\E_USER_WARNING, 'Herpi derp'),
    )->notToThrow();

    // Incorrect error message
    expect(
      () ==> expect(() ==> \trigger_error('Giant squid', \E_USER_WARNING))
        ->toTriggerAnError(\E_USER_WARNING, 'Herpi durp'),
    )->toThrow(ExpectationFailedException::class);

    expect(
      () ==> expect(() ==> \trigger_error('', \E_USER_WARNING))
        ->toTriggerAnError(\E_USER_NOTICE, '', '%s, %d', 'ess', 6),
    )->toThrow(ExpectationFailedException::class);

    expect(
      () ==> expect(() ==> {
        \trigger_error('The first error');
        \trigger_error('The second error');
        \trigger_error('The third error');
        \trigger_error('The fourth error');
      })
        ->toTriggerAnError(\E_USER_NOTICE, 'The third error'),
    )->notToThrow();

    expect(
      () ==> expect(() ==> {
        \trigger_error('The first error');
        \trigger_error('The second error');
        \trigger_error('The third error');
        \trigger_error('The fourth error');
      })
        ->toTriggerAnError(\E_USER_NOTICE, 'The fifth error'),
    )->toThrow(ExpectationFailedException::class);

    expect(
      () ==> expect(async () ==> {
        await \HH\Asio\later();
        \trigger_error('asio!!', \E_USER_WARNING);
      })
        ->toTriggerAnError(\E_USER_NOTICE, 'asio!!'),
    )->toThrow(ExpectationFailedException::class);

    expect(
      () ==> expect(() ==> invariant_violation('something went wrong'))
        ->toTriggerAnError(),
    )
      ->toThrow(InvariantException::class, 'something went wrong');

    $previous = \set_error_handler('something_else');
    \restore_error_handler();
    \restore_error_handler();
    expect($previous)->toEqual('not_a_function', 'Error handler contaminated');
  }

  /**
   * Test that all reasonable ways of providing a (function(): mixed) work.
   */
  public function testCallables(): void {
    expect(() ==> self::exampleStaticCallable())
      ->toThrow(\Exception::class, 'Static method called!');
    expect(class_meth(self::class, 'exampleStaticCallable'))
      ->toThrow(\Exception::class, 'Static method called!');
    expect(inst_meth($this, 'exampleInstanceCallable'))
      ->toThrow(\Exception::class, 'Instance method called!');
    expect(fun('time'))->notToThrow();
  }

  public static function exampleStaticCallable(): void {
    throw new \Exception('Static method called!');
  }

  public function exampleInstanceCallable(): void {
    throw new \Exception('Instance method called!');
  }
}
