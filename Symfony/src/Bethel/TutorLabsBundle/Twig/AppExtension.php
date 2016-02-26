<?php

namespace Bethel\TutorLabsBundle\Twig;

class AppExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('jsonDecode', array($this, 'jsonDecodeFunc')),
        );
    }

    public function jsonDecodeFunc($string)
    {
        return json_decode($string);
    }

    public function getName()
    {
        return 'app_extension';
    }
}