/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
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
 * See full API in ExpectObj. Sections:
 *
 *   - Basic Value Assertions
 *   - Negated Basic Value Assertions
 *   - Function Call Assertions
 *   - Function Exception Assertions
 */
function expect<T>(T $obj): ExpectObj<T> {
  return new ExpectObj($obj);
}
