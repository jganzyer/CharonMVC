<?php
class NoCSRFbad
{
    private static $doOriginCheck = true;
    protected static function randomString( $length )
    {
        $seed = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijqlmnopqrtsuvwxyz0123456789';
        $max = strlen($seed) - 1;
        $string = '';
        for ( $i = 0; $i < $length; ++$i )
            $string .= $seed{intval(mt_rand(0.0, $max))};
        return $string;
    }
    public static function generateToken($key)
    {
        $extra = self::$doOriginCheck ? sha1($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']) : '';
        $token = base64_encode(time() . $extra . self::randomString(32));
        Jession::set("csrf_".$key,$token);
        return $token;
    }
    public static function enabeOriginCheck(){
        self::$doOriginCheck = true;
    }
    public static function disableOriginCheck(){
        self::$doOriginCheck = false;
    }
    public static function check($key,$formtoken,$timespan=null){
        if (!isset($key)){
            return false;
        }
        if (!isset($formtoken)){
            return false;
        }
        $hash = Jession::get('csrf_'.$key);
        if (self::$doOriginCheck && sha1( $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] ) != substr( base64_decode( $hash ), 10, 40 ) )
        {
            return false;
        }
        if ($timespan != null && is_int($timespan) && intval(substr( base64_decode( $hash ), 0, 10 )) + $timespan < time() ){
            return false;
        }
        if ($hash == $formtoken){
            return true;
        } else {
            return false;
        }
    }
    public static function delete($key){
        Jession::delete('csrf_'.$key);
    }
    public static function deleteAll(){
        $ses = Jession::getAll();
        $seskeys =  array_keys($ses);
        foreach ($seskeys as $key){
            if (substr($key,0,5) == 'csrf_'){
                Jession::delete($key);
            }
        }
    }
}
