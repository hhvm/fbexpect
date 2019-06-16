/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

namespace Facebook\FBExpect;

function is_any_array(mixed $value): bool {
  return (
    \is_array($value) ||
    ($value is dict<_, _>) ||
    ($value is vec<_>) ||
    ($value is keyset<_>)
  );
}

function not_hack_array(mixed $value): mixed {
  if (is_any_array($value) && !\is_array($value)) {
    /* HH_IGNORE_ERROR[4007] sketchy array cast */
    return (array) $value;
  }
  return $value;
}

function print_type(mixed $value): string {
  if (\is_object($value)) {
    return \get_class($value);
  }
  return \gettype($value);
}

function is_iterable(mixed $value): bool {
  return \is_array($value) ||
    (\is_object($value) && ($value is Traversable<_>));
}

function is_type(mixed $value): bool {
  return Constraint\IsType::getTypes()->containsKey($value);
}
