[![Build Status](https://travis-ci.org/hhvm/fbexpect.svg?branch=master)](https://travis-ci.org/hhvm/fbexpect)

# FBExpect

FBExpect is a standalone unit testing utility based on the notion of assertions from PHPUnit. Starting with `2.x`, FBExpect no longer uses PHPUnit as a
dependency, and instead implements the assertions directly, and is intentended
for use with [HackTest].

## Examples

### Clarity

It is linguistically clear which value is the expected value and which is the
actual value:

```Hack
use function Facebook\FBExpect\expect;

// PHPUnit
$this->assertSame($a, $b);

// FBExpect
expect($b)->toBeSame($a);
```

### Type Refinement

```Hack
use function Facebook\FBExpect\expect;

// PHPUnit
$this->assertNotNull($x); // Actual test
assert($x !== null); // Tell the typechecker what's going on
$this->assertInstanceOf(Foo::class, $y);
assert($y instanceof Foo);

// FBExpect
$x = expect($x)->toNotBeNull();
$y = expect($y)->toBeInstanceOf(Foo::class);
```

## Installation

FBExpect is installed via composer:

```
composer require facebook/fbexpect
```

Composer must be executed with `php`, not HHVM.

## License

FBExpect is MIT-licensed.

[HackTest]: https://github.com/hhvm/hacktest/
