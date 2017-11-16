<?php
namespace Sb\Generator\PhpClass;

use Sb\Generator\PhpClass as AbstractClass;
use Sb\Generator\PhpClass\Method\Param as AbstractClassMethodParam;

class Method extends \Sb\Generator
{
    /**
     * @var AbstractClassMethodParam[]
     */
    private $params = [];

    private $content;

    private $scope = 'public';

    private $isStatic = false;

    private $name;

    private $return;

    private $addReturnNull;

    private $description = '';

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function __toString()
    {
        $content = '';

        if ($this->description || $this->params || $this->return) {
            $content .= AbstractClass::tab()."/**\n";
            if ($this->description) {
                $tmp = explode("\n", $this->description);
                foreach ($tmp as $t) {
                    $content .= AbstractClass::tab().' * '.$t."\n";
                }
            }

            if ($this->params) {
                foreach ($this->params as $param) {
                    $content .= AbstractClass::tab().' * @param';
                    if ($param->getType()) {
                        $content .= ' '.$param->getType();
                    }
                    $content .= ' $'.$param->getName()."\n";
                }
            }

            if ($this->return) {
                if ($this->addReturnNull) {
                    $content .= AbstractClass::tab().' * @return '.$this->return." | null\n";
                } else {
                    $content .= AbstractClass::tab().' * @return '.$this->return."\n";
                }
            }
            $content .= AbstractClass::tab()." */\n";
        }

        $params = implode(', ', $this->params);

        $content .= AbstractClass::tab().$this->scope.$this->getStatic().'function '.$this->name;
        if ($this->return) {
            if ($this->addReturnNull) {
                if (version_compare(PHP_VERSION, '7.1') > 0) {
                    $content .= '('.$params.')'.(' : ?'.$this->getReturn())."\n";
                } else {
                    $content .= '('.$params.')'.(' /* : ?'.$this->getReturn())." */\n";
                }
            } else {
                $content .= '('.$params.')'.(' : '.$this->getReturn())."\n";
            }
        } else {
            $content .= '('.$params.')'."\n";
        }
        $content .= AbstractClass::tab().'{'."\n";
        $content .= $this->content;
        $content .= AbstractClass::tab().'}'."\n";

        return $content;
    }
    public function getReturn()
    {
        if ($this->return == '$this') {
            return 'self';
        } elseif (1 === preg_match('#\[\]$#', $this->return)) {
            return 'array';
        }

        return $this->return;
    }
    public function addParam(AbstractClassMethodParam $param)
    {
        $this->params[] = $param;
    }

    public function addDescription($message)
    {
        $this->description = trim($message);
    }
    public function getDescription()
    {
        return $this->description;
    }
    public function setScope($scope)
    {
        $this->scope = $scope;
    }

    public function setContent($content)
    {
        $this->content = $content."\n";
    }

    public function addContentLine($line='')
    {
        if ($line) {
            $this->content .= AbstractClass::tab(2).$line."\n";
        } else {
            $this->content .= "\n";
        }
    }

    public function setStatic()
    {
        $this->isStatic = true;
    }

    public function getStatic()
    {
        if ($this->isStatic) {
            return ' static ';
        }

        return ' ';
    }

    /**
     * @param string $return
     * @return string
     */
     public function setReturn($return)
     {
         $this->return = $return;
     }
    public function addReturnNull()
    {
        $this->addReturnNull = true;
    }
}
