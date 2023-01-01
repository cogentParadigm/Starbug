<?php
namespace Starbug\Imports\Transform;

abstract class AbstractTransformer implements TransformerInterface {
  abstract public function transform($source, $dest, $options = []): array;
  public function getLabel($transformer): string {
    return $transformer["type"];
  }
}