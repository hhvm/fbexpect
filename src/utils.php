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

function is_any_array(mixed $value): bool {
  return (
    is_array($value) ||
    is_dict($value) ||
    is_vec($value) ||
    is_keyset($value)
  );
}

function print_type(mixed $value): string {
  if (is_object($value) || $value instanceof \__PHP_Incomplete_Class) {
    return get_class($value);
  }
  return gettype($value);
}

function is_type(string $value): bool {
  switch ($value) {
    case 'vec':
    case 'dict':
    case 'keyset':
      return true;
    default:
      /* HH_FIXME[2049] unbound name */
      if (class_exists(\PHPUnit_Util_Type::class)) {
        // PHPUnit 5
      /* HH_FIXME[2049] unbound name */
        return \PHPUnit_Util_Type::isType($value);
      } else {
        // PHPUnit 6
      /* HH_FIXME[2049] unbound name */
      /* HH_FIXME[4107] unbound name */
        return \PHPUnit\Util\Type\isType($value);
      }
  }
}
