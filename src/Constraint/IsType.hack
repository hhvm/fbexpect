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
      'integer' => ($x ==> ($x is int)),
      'int' => ($x ==> ($x is int)),
      'double' => ($x ==> ($x is float)),
      'float' => ($x ==> ($x is float)),
      'real' => ($x ==> ($x is float)),
      'string' => ($x ==> ($x is string)),
      'boolean' => ($x ==> ($x is bool)),
      'bool' => ($x ==> ($x is bool)),
      'null' => ($x ==> $x === null),
      'array' => ($x ==> \HH\is_php_array($x)),
      'object' => ($x ==> \is_object($x)),
      'resource' => (
        $x ==> {
          if ($x is resource) {
            return true;
          }
          $error_level = \error_reporting(0);
          $is_resource = \get_resource_type(
            /* HH_FIXME[4110] closed resources fail is resource */ $x,
          ) is string;
          \error_reporting($error_level);
          return $is_resource;
        }
      ),
      'scalar' => ($x ==> \is_scalar($x)),
      'callable' => ($x ==> \is_callable($x)),
      'iterable' => ($x ==> is_iterable($x)),
      'vec' => ($x ==> ($x is vec<_>)),
      'dict' => ($x ==> ($x is dict<_, _>)),
      'keyset' => ($x ==> ($x is keyset<_>)),
      'Container' => ($x ==> ($x is Container<_>)),
      'KeyedContainer' => ($x ==> ($x is KeyedContainer<_, _>)),
    };
  }

  public function matches(mixed $other): bool {
    $types = self::getTypes();
    $predicate = $types->at($this->expectedType);
    return $predicate($other);
  }
}
