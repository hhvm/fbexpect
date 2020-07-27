/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */


namespace Facebook\FBExpect\Constraint;

class TraversableContains {

  public function __construct(private mixed $value) {}

  public function matches(Traversable<mixed> $other): bool {
    if (\is_object($this->value)) {
      foreach ($other as $element) {
        if ($element === $this->value) {
          return true;
        }
      }
      return false;
    }
    foreach ($other as $element) {
      /* HHAST_IGNORE_ERROR[NoPHPEquality] */
      if ($element == $this->value) {
        return true;
      }
    }
    return false;
  }
}
