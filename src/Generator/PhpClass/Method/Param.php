<?php
namespace Sb\Generator\PhpClass\Method;

class Param extends \Sb\Generator
{
    private $name;
    private $default;
    private $type;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function __toString()
    {
        $content = '';
        if ($this->type) {
            $content .= $this->type.' ';
        }
        $content .= '$'.$this->name;
        if ($this->default) {
            $content .= ' = '.$this->default;
        }

        return $content;
    }

    public function setDefaultValue($default)
    {
        $this->default = $default;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getName()
    {
        return $this->name;
    }
}
