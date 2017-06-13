<?php
namespace Charon;
class Validator
{
  private $rules;
  private $fields;
  private $errors;
  private $rule_messages;

  public function __construct()
  {
    $this->fields = [];
    $this->rules = [
      'required' => function($data){
        return (!empty(trim($data)));
      },
      'alpha' => function($data){
        return (bool)preg_match('/^[\pL\pM]+$/u', $data);
      },
      'alnum' => function($data){
        return (bool)preg_match('/^[\pL\pM\pN]+$/u', $data);
      },
      'alnumDash' => function($data){
        return (bool) preg_match('/^[\pL\pM\pN_-]+$/u', $data);
      },
      'email' => function($data){
        return filter_var($data, FILTER_VALIDATE_EMAIL);
      },
      'ip' => function($data){
        return filter_var($data,FILTER_VALIDATE_IP);
      },
      'between' => function($data,$min, $max){
        return ($data >= $min && $data <= $max);
      },
      'min' => function($data,$min){
        return ($data >= $min);
      },
      'max' => function($data,$max){
        return ($data <= $max);
      },
      'minLenght' => function($data, $lenght){
        return (strlen($data) >= $lenght);
      },
      'maxLenght' => function($data,$lenght){
        return (strlen($data) <= $lenght);
      },
      'matches' => function($data, $field){
        if (empty($this->fields[$field])) {
          return false;
        }
        if ($data == $this->fields[$field]){
          return true;
        }
        else
        {
          return false;
        }
      },
      'unmatches' => function($data, $field){
        if (empty($this->fields[$field])) {
          return false;
        }
        if ($data == $this->fields[$field]){
          return false;
        }
        else
        {
          return true;
        }
      }
    ];
    $this->rule_messages = [
      'between' => '{field} alanına girdiğiniz {value} değeri hatalıdır {rule} fonksiyonu hata verdi'
    ];
    $this->errors = [];
  }
  public function validate($data, $rule = null)
  {
    if ($rule === null)
    {
      foreach($data as $field => $value)
      {
        $enter = array_shift($value);
        $this->fields[$field] = $enter;
        foreach ($value as $name => $params)
        {
          if (gettype($name) === 'integer')
          {
            $name = $params;
            $params = [];
          }
          array_unshift($params, $enter);
          if (call_user_func_array($this->rules[$name], $params) == false)
          {
            $this->errors[] = [$field, $enter, isset($this->rule_messages[$name]) ? str_replace(['{field}','{rule}','{value}'],[$field,$name, $enter],$this->rule_messages[$name]) : ''];
          }
        }
      }
    }
  }
  public function passed()
  {
    return empty($this->errors);
  }
  public function addRule($name, $fn, $error_message = null)
  {
    $this->rules[$name] = $fn;
    if ($error_message !== null)
    {
      $this->rule_messages[$name] = $fn;
    }
  }
  public function addRuleMessage($name, $error_message)
  {
    $this->rule_messages[$name] = $error_message;
  }
  public function errors()
  {
    return $this->errors;
  }
}
