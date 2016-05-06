<?php
namespace OCFram;

class Direction {
    public static function askRoute($name,$module,$action,$vars = []){

        if (empty($name) || empty($module) || empty($action) || !is_string($name) || !is_string($module) || !is_string($action)){
            return "/home";
        }
        $xml = new \DOMDocument;
        $xml->load('../App/'.$name.'/Config/routes.xml');

        $routes = $xml->getElementsByTagName('route');
        $routes = iterator_to_array($routes);
        $url = null;

        $routes = array_filter($routes,function($v) use($module,$action){
            $v->getAttribute('module');
            return ($v->getAttribute('module') === $module && $v->getAttribute('action') === $action);
        });

        $url = array_pop($routes)->getAttribute('uri');

        if(is_null($url))
            return "/home";

        if(!is_null($vars)){
            $keys = array_keys($vars);
            array_walk($keys,function(&$v,$k){
                $v = '['.$v.']';
            });

            $url = str_replace($keys,$vars,$url);
        }
        
        return $url;
    }
}