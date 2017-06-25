<?php
namespace Sb\Generator;

use Sb\Generator\Object\Property as AbstractClassProperty;
use Sb\Generator\Object\Method as AbstractClassMethod;
use Sb\Generator\Object\Method\Param as AbstractMethodParam;

class Object extends \Sb\Generator
{
    private $name;
    private $namespace;
    private $extend;
    private $implements = [];
    private $isAbstract;
    private $use       = [];
    private $docBlocks = [];

    /**
     * @var AbstractClassProperty[]
     */
    private $fields = [];
    /**
     * @var AbstractClassMethod[]
     */
    private $methods = [];

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function __toString()
    {
        $content = '<?php'."\n";

        if ($this->namespace) {
            $content .= 'namespace '.$this->namespace.";\n\n";
        }

        if ($this->use) {
            foreach ($this->use as $use) {
                $content .= 'use '.$use['use'];
                if ($use['as']) {
                    $content .= ' as '.$use['as'];
                }
                $content .= ";\n";
            }
            $content .= "\n";
        }

        if ($this->docBlocks) {
            $content .= "/**\n";
            foreach ($this->docBlocks as $docBlock) {
                $content .= " * {$docBlock}\n";
            }
            $content .= " */\n";
        }

        if ($this->isAbstract) {
            $content .= 'abstract ';
        }

        $content .= 'class '.$this->name;

        if ($this->extend) {
            $content .= ' extends '.$this->extend;
        }

        if ($this->implements) {
            $content .= ' implements '.implode(',', $this->implements);
        }

        $content .= "\n".'{'."\n";

        $innerContent = '';
        foreach ($this->fields as $field) {
            $innerContent .= $field->__toString()."\n";
        }

        foreach ($this->methods as $method) {
            $innerContent .= $method->__toString()."\n";
        }
        if ($innerContent) {
            $content .= rtrim($innerContent)."\n";
        }

        $content .= '}';

        $content .= "\n";

        return $content;
    }

    public function addMethod(AbstractClassMethod $method)
    {
        $this->methods[] = $method;
    }

    public function addField(AbstractClassProperty $field)
    {
        $this->fields[] = $field;
    }

    public function addDocBlock($block)
    {
        $this->docBlocks[] = $block;
    }

    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    public function setExtends($extends)
    {
        $this->extend = $extends;
    }

    public function addImplements($implements)
    {
        $this->implements[] = $implements;
    }

    public function sortMethods()
    {
    }

    public function sortFields()
    {
    }

    public function addUse($use, $as = '')
    {
        $this->use[] = [
            'use' => $use,
            'as'  => $as,
        ];
    }

    public function setAbstract()
    {
        $this->isAbstract = true;
    }

    public function generateSettersAndGetters()
    {
        foreach ($this->fields as $field) {
            $fieldName      = $field->getName();
            $param          = new AbstractMethodParam(\Peanut\Text::lcfirstCamelize($fieldName));

            $setFieldMethod = new AbstractClassMethod($field->getSetterName());
            $setFieldMethod->addParam($param);
            $setFieldMethod->addDescription($field->getDescription());
            $setFieldMethod->addContentLine('$this->'.$fieldName.' = $'.\Peanut\Text::lcfirstCamelize($fieldName).';');

            $setFieldMethod->addContentLine('');
            $setFieldMethod->addContentLine('return $this;');
            $setFieldMethod->setReturn('$this');

            $this->addMethod($setFieldMethod);

            $getFieldMethod = new AbstractClassMethod($field->getGetterName());
            $getFieldMethod->addDescription($field->getDescription());
            $getFieldMethod->addContentLine("return \$this->{$fieldName};");

            $this->addMethod($getFieldMethod);
        }
    }

    public static function tab($amount = 1)
    {
        return str_repeat(' ', 4 * $amount);
    }
}
