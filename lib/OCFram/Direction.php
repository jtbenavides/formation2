<?php
namespace OCFram;

class Direction {

    public static $routesAppli = [];

    public static $appliRoutes = [];

    public static function askRoute($name,$module,$action,$vars = []){

        if (empty($name) || empty($module) || empty($action) || !is_string($name) || !is_string($module) || !is_string($action)){
            return "/home";
        }

        $url = null;
        $routes = null;
        if(!array_key_exists($name,Direction::$appliRoutes)){
            $xml = new \DOMDocument;
            $xml->load('../App/'.$name.'/Config/routes.xml');

            $routes = $xml->getElementsByTagName('route');
            $routes = iterator_to_array($routes);

            $assocRoute = [];
            foreach($routes as $route){
                $assocRoute[$route->getAttribute('module').$route->getAttribute('action')] = $route->getAttribute('uri');
            }
            Direction::$appliRoutes[$name] = $assocRoute;
        }

        if(!array_key_exists($module.$action,Direction::$appliRoutes[$name]))
            return "/home";

        $url = Direction::$appliRoutes[$name][$module.$action];

        if(!is_null($vars)){

            foreach($vars as $key=>$var) {
                $vars['['.$key.']'] = $var;
                unset($vars[$key]);
            }

            $url = strtr($url,$vars);
        }
        return $url;
    }
}