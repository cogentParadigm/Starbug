<?php
namespace Starbug\Imports\Transform;

interface TransformerInterface {
  public function transform($source, $dest, $ops = []): array;
  public function getLabel($transformer): string;
}
