<?php
  $this->displays->render("RadioDisplay", [
    "value" => $value,
    "name" => $name,
    "model" => $from,
    "collection" => $query,
    "options" => $options,
    "other_option" => $other_option
  ] + $display_options);
