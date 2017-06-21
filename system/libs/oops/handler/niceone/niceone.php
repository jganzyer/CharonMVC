<?php
namespace oops;
class niceone
{
  private static $css_prefix = '';
  private static $custom_icons = [];
  public function __construct($errors, $range = 4)
  {
    ob_clean();
    $imain_html = file_get_contents(__DIR__.'\index.layout.html');
    $error_html = file_get_contents(__DIR__.'\error.layout.html');

    $error_render_html = '';
    $render_data = [];
    $i = 0;
    foreach ($errors as $error)
    {
      $sfile = str_replace(ROOT, '..\\', $error['file']);
      $type = self::type2string($error['type']);
      $types = slug($type);
      $line = $error['line'];

      $error['message'] .= ' in <b>'.$sfile.'</b> on line <b>'.$line.'</b>';
      $error['message'] = replace_between_all('**', '**', '<b>{data}</b>', $error['message']);

      $buffer = xss_clean(file_get_contents($error['file']));
      $buffer = explode(PHP_EOL, $buffer);
      $buffer[$line - 1] = '<span class="code-error type-'.$types.'">'.$buffer[$line - 1].'</span>';

      $output = range($line - $range - 1, $line + $range - 1);
      $output = array_combine($output, $output);
      $output = array_intersect_key($buffer, $output);
      $output = (empty(array_values($output)[0])) ? PHP_EOL.implode(PHP_EOL, $output) : implode(PHP_EOL, $output);

      if ($line - $range < 1)
      {
        $diff = $line - $range;
        if ($diff === 0)
        {
          $linenums = 1;
        }
        else
        {
          $linenums = $line - ($diff * -1 + 1);
        }
      } else {
        $linenums = $line - $range;
      }
      $class_prefix = '';
      if ($i === 0)
      {
        $render_data['first_code'] = $output;
        $render_data['first_detail'] = $error['message'];
        $render_data['first_file'] = $sfile;
        $render_data['first_line'] = $linenums;
        $class_prefix = 'active ';
      }
      $error_render_html .= self::render($error_html, [
        'class_prefix' => $class_prefix,
        'error_type' => $type,
        'error_type_class' => $types,
        'error_detail' => $error['message'],
        'error_file' => $sfile,
        'error_code' => $output,
        'error_linenums' => $linenums,
        'error_image' => self::string2image($types)
      ]);
      $i++;
    }
    $render_data['count'] = self::integer2digit($i);
    $render_data['html'] = $error_render_html;
    $render_data['report_link'] = 'https://github.com/CharonFW/CharonMVC/issues/new';
    if (empty(self::$css_prefix) === false)
    {
      self::$css_prefix = '<style>'.self::$css_prefix.'</style>';
    }
    $render_data['css_prefix'] = self::$css_prefix;
    echo self::render($imain_html, $render_data);
  }

  public static function add_type($type, $icon = null, $color = '#26C6DA')
  {
    $type = slug($type);
    self::$css_prefix .= '.type-'.$type.' .error-icon{background-color: '.$color.';}.type-'.$type.':hover .error-type:before{background-color: '.$color.';}.code-error.type-'.$type.':after{background-color: '.$color.';}.type-'.$type.'.active{background-color: '.$color.';}';
    self::$custom_icons[$type] = $icon;
  }

  private static function integer2digit($int)
  {
    if ($int < 9)
    {
      return '0'.$int;
    }
    return $int;
  }

  private static function type2string($type)
  {
    if ($type === 1 || $type === 16 || $type === 64 || $type === 256 || $type === 4096) {
      return "error";
    } else if ($type === 2 || $type === 32 || $type === 128 || $type === 512) {
      return "warning";
    } else if ($type === 8 || $type === 1024) {
      return "notice";
    } else {
      return $type;
    }
  }

  private static function string2image($str)
  {
    if (isset(self::$custom_icons[$str]) === true)
    {
      return self::$custom_icons[$str];
    }
    if ($str === "error")
    {
      return 'https://image.flaticon.com/icons/svg/149/149413.svg';
    }
    else if ($str === "warning")
    {
      return 'https://image.flaticon.com/icons/svg/78/78371.svg';
    }
    else if ($str === "notice")
    {
      return 'https://image.flaticon.com/icons/svg/3/3716.svg';
    }
    else
    {
      return 'https://image.flaticon.com/icons/svg/15/15934.svg';
    }
  }

  private static function render($html, $data)
  {
    foreach ($data as $key => $value)
    {
      $html = str_replace('{{'.$key.'}}', $value, $html);
    }
    return $html;
  }
}
