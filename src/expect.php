<?hh // strict
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

/**
 * Alternate interface to PHPUnit assertions similar to Jasmine JS
 *
 * Example usage:
 *
 *   expect($foo)->toEqual('bar'); // Assert $foo == 'bar'
 *
 * When expecting on a mock object, expect() takes two args:
 *
 *   // Assert $foo->bar() was called once
 *   expect($foo, 'bar')->wasCalledOnce();
 *
 * See full API in ExpectObj. Sections:
 *
 *   - Basic Value Assertions
 *   - Negated Basic Value Assertions
 *   - Function Call Assertions
 *   - Function Exception Assertions
 *   - Mock Object Asserts
 */
function expect<T>(T $obj, mixed ...$args): ExpectObj<T> {
  return new ExpectObj(new ImmVector(func_get_args()));
}
