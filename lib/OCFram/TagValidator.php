<?php
namespace OCFram;

class TagValidator extends Validator
{

    public function isValid($value)
    {
        $tableau = explode(" ",$value);
        $new = [];

        foreach ($tableau as $tag):
            if(substr($tag,0,1) == "#"):
                $new[] = $tag;
            endif;
        endforeach;

        $value = implode(" ",$new);

        return true;
    }
}