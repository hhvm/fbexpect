<?hh

namespace Facebook\FBExpect\Constraint;

class TraversableContains {

  public function __construct(private $value) {}

  public function matches($other): bool {
    if ($other instanceof \SplObjectStorage) {
      return $other->contains($this->value);
    }
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
