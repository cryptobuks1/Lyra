<?php

namespace SertxuDeveloper\Lyra\Fields;

class Boolean extends Field {

  protected $component = "boolean-field";

  protected function retrieveValue($model) {
    if(isset($model[$this->column])) {
      return ($model[$this->column] == 'ACTIVE') ? true : false;
    } else {
      return false;
    }
  }

  public function updateValue($value) {
    return $value ? 'ACTIVE' : 'BLOCKED';
  }

}