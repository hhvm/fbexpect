[![Build Status](https://travis-ci.org/hhvm/fbexpect.svg?branch=master)](https://travis-ci.org/hhvm/fbexpect)

# FBExpect

FBExpect is a unit testing utility built on top of PHPUnit. The primary goal of
this project is to allow Facebook to release our existing unit tests for other
projects, however it does have advantages for third-party Hack projects:

 - clarity
 - type refinment for some assertions
 - support for `vec`, `keyset`, and `dict` types

## Examples

### Clarity

It is linguistically clearer which value is the expected value and which is the
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
hhvm ~/composer require facebook/fbexpect
```

FBExpect supports HHVM's PHP7 mode, however as it is also supports
having PHP7 mode disabled, it currently requires PHPUnit 5.

## License

FBExpect is MIT-licensed.
