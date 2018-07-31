<?hh // strict
/*
 *  Copyright (c) 2004-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

namespace Facebook\FBExpect\Constraint;

use function Facebook\FBExpect\is_iterable;

class IsType {
  const type TPredicate = (function(mixed): bool);
  public function __construct(private string $expectedType) {}

  public static function getTypes(): ImmMap<string, self::TPredicate> {
    return ImmMap {
      'numeric' => ($x ==> \is_numeric($x)),
      'integer' => ($x ==> \is_int($x)),
      'int' => ($x ==> \is_int($x)),
      'double' => ($x ==> \is_float($x)),
      'float' => ($x ==> \is_float($x)),
      'real' => ($x ==> \is_float($x)),
      'string' => ($x ==> \is_string($x)),
      'boolean' => ($x ==> \is_bool($x)),
      'bool' => ($x ==> \is_bool($x)),
      'null' => ($x ==> $x === null),
      'array' => ($x ==> \is_array($x)),
      'object' => ($x ==> \is_object($x)),
      'resource' => (
        $x ==> \is_resource($x) || \is_string(@\get_resource_type($x))
      ),
      'scalar' => ($x ==> \is_scalar($x)),
      'callable' => ($x ==> \is_callable($x)),
      'iterable' => ($x ==> is_iterable($x)),
      'vec' => ($x ==> is_vec($x)),
      'dict' => ($x ==> is_dict($x)),
      'keyset' => ($x ==> is_keyset($x)),
    };
  }

  public function matches(mixed $other): bool {
    $types = self::getTypes();
    $predicate = $types->at($this->expectedType);
    return $predicate($other);
  }
}
