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
        $url = null;

        foreach ($routes as $route)
        {
            if ($route->getAttribute('module') === $module && $route->getAttribute('action') === $action) {
                $url = $route->getAttribute('uri');
                break;
            }
        }

        if(is_null($url))
            return null;

        foreach($vars as $key => $var){
            $url = str_replace($key, $var, $url);
        }


        return $url;
    }
}