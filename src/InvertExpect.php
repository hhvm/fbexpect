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

final class InvertExpect {
  public function __construct(private ExpectObj $target) { }
  public function __call($method, $args) {
    try {
      call_user_func_array(array($this->target, $method), $args);
    } catch (Exception $e) {
      // We are intentionally swallowing a phpunit assertion
      // error. This is to prevent D1020690 from regurgitating it.
      if (PHPUnit_Framework_AssertionFailedError::$lastInstance === $e) {
        PHPUnit_Framework_AssertionFailedError::$lastInstance = null;
      }
      return;
    }
    BaseFacebookTestCase::fail("Expected $method to fail, but it did not");
  }
}
