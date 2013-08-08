<?php

namespace Yunait\Apigator\Mondator\Extension;

use Mandango\Mondator\Definition\Definition;
use Mandango\Mondator\Definition\Method;
use Mandango\Mondator\Definition\Property;
use Mandango\Mondator\Extension;

class ResourceExtension extends ApigatorExtension
{
    const CLASSES_NAMESPACE = 'Resources\\Base';
    const CLASSES_PREFIX = '';
    const CLASSES_SUFFIX = '';
    private $repositoryNamePrefix;

    public function __construct($options = array())
    {
        parent::__construct($options);

        $this->classesNamespace = self::CLASSES_NAMESPACE;
        $this->classesPrefix = self::CLASSES_PREFIX;
        $this->classesSuffix = self::CLASSES_SUFFIX;
    }

    protected function generateClass()
    {
        $output = $this->outputFactory->create($this->getOption('output'), true);
        $targetClassName = $this->getTargetClass($this->getClassName());
        $definition = $this->definitionFactory->create($targetClassName, $output);

        $this->definitions['resource'] = $definition;
        $definition->setAbstract(true);
        $definition->setParentClass(BaseResourceExtension::CLASSES_PREFIX . BaseResourceExtension::CLASSES_SUFFIX);
        $this->addConstructorToDefinition($definition);
        $this->addGetDocumentAsResourceToDefinition($definition);
    }

    private function addConstructorToDefinition(Definition $definition)
    {
        $collectionName = $this->configClass['collection'];
        $method = new Method('public', '_construct', 'Level3\Hal\ResourceBuilderFactory $resourceBuilderFactory',
<<<EOF
        parent::__construct(\$resourceBuilderFactory);
        \$this->collectionName = '$collectionName';
EOF
        );
        $definition->addMethod($method);
    }

    private function addGetDocumentAsResourceToDefinition(Definition $definition)
    {
        $builderClass = '\\' . $this->getOption('namespace') . '\\' .
            EmptyResourceBuilderExtension::CLASSES_NAMESPACE . '\\' .
            EmptyResourceBuilderExtension::CLASSES_PREFIX .
            $this->getClassName() .
            EmptyResourceBuilderExtension::CLASSES_SUFFIX;

        $method = new Method('protected', 'getDocumentAsResource', '\Mongator\Document\Document $document',
<<<EOF
        \$builder = new $builderClass(\$this->createResourceBuilder(), \$document);
        return \$builder->build();
EOF
        );
        $definition->addMethod($method);
    }
}