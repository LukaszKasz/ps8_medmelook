<?php
class Ps_LanguageselectorOverride extends Ps_Languageselector
{
    public function getNameSimple($name)
    {
        return preg_replace('/^\s+|\s+$/', '', $name);
    }
}
