<?php
namespace Sb\Generator\PhpClass;

use Sb\Generator\PhpClass as AbstractClass;

class Property extends \Sb\Generator
{
    private $name;
    private $type;
    private $scope = 'public';
    private $isStatic;
    private $default;
    private $description;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function __toString()
    {
        $content = '';
        if ($this->description) {
            $content .= AbstractClass::tab().'/**'."\n";
            $tmp = explode("\n", $this->description);

            foreach ($tmp as $t) {
                $content .= AbstractClass::tab().' * '.$t."\n";
            }
            $content .= AbstractClass::tab().' */'."\n";
        }

        $content .= AbstractClass::tab().$this->scope;

        if ($this->isStatic) {
            $content .= ' static';
        }
        $content .= ' $'.$this->name;

        if ($this->default) {
            if (true === is_array($this->default)) {
                $defaults = $this->default;
                foreach ($defaults as $key => $default) {
                    $defaults[$key] = $default;
                }

                $content .= ' = ['."\n".AbstractClass::tab(2);
                $content .= implode(",\n".AbstractClass::tab(2), $defaults);
                $content .= "\n".AbstractClass::tab().']';
            } else {
                $content .= ' = '.$this->default;
            }
        }

        $content .= ";\n";

        return $content;
    }

    public function setScope($scope)
    {
        $this->scope = $scope;
    }
    public function addDescription($message)
    {
        $this->description = trim($message);
    }
    public function getDescription()
    {
        return $this->description;
    }
    public function getSetterName()
    {
        return 'set'.\Peanut\Text::camelize($this->name);
    }

    public function getGetterName()
    {
        return 'get'.\Peanut\Text::camelize($this->name);
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setDefault($default)
    {
        $this->default = $default;
    }

    public function setStatic()
    {
        $this->isStatic = true;
    }
}
