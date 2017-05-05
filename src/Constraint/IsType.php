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

namespace Constraint;

class IsType extends \PHPUnit_Framework_Constraint_IsType {
  const type TPredicate = (function(mixed):bool);

  public function __construct(string $type) {
    foreach (self::getExtraTypes() as $extra => $_pred) {
      /* HH_IGNORE_ERROR[4053] types prop not in HHI*/
      $this->types[$extra] = true;
    }
    parent::__construct($type);
  }

  public function matches(mixed $other): bool {
    $extra = self::getExtraTypes();
    if ($extra->containsKey($this->type)) {
      $predicate = $extra->at($this->type);
      return $predicate($other);
    }
    return parent::matches($other);
  }

  protected static function getExtraTypes(): ImmMap<string, self::TPredicate> {
    return ImmMap {
      'vec' => ($x ==> is_vec($x)),
      'dict' => ($x ==> is_dict($x)),
      'keyset' => ($x ==> is_keyset($x)),
    };
  }
}
