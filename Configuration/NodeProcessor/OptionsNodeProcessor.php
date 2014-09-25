<?php

namespace Lephare\Bundle\MenuBundle\Configuration\NodeProcessor;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\Util\MenuManipulator;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class OptionsNodeProcessor extends AbstractNodeProcessor implements NodeProcessorInterface
{
    public function getName()
    {
        return 'extras';
    }

    public function getAliases()
    {
        return [ 'options' ];
    }

    public function process($configuration, array &$processors, FactoryInterface $factory, ItemInterface &$node = null)
    {
        if (!is_array($configuration)) {
            return false;
        }

        $resolver = new OptionsResolver;
        $manipulator = new MenuManipulator;
        $this->setDefaultOptions($resolver);

        $configuration = array_merge($manipulator->toArray($node), $configuration);
        unset($configuration['name'], $configuration['label'], $configuration['children']);

        $options = $resolver->resolve($configuration);
        $options = $manipulator->toArray($factory->createItem($node->getName(), $options));

        $this->configureItem($node, $options);
    }

    protected function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'uri' => null,
                'label' => null,
                'attributes' => [],
                'linkAttributes' => [],
                'childrenAttributes' => [],
                'labelAttributes' => [],
                'display' => true,
                'displayChildren' => true,
                'route' => null,
                'routeParameters' => [],
                'extras' => [],
                'current' => null,
            ])

            ->setAllowedTypes([
                'uri' => [ 'string', 'null' ],
                'label' => [ 'string', 'null' ],
                'attributes' => 'array',
                'linkAttributes' => 'array',
                'childrenAttributes' => 'array',
                'labelAttributes' => 'array',
                'display' => 'bool',
                'displayChildren' => 'bool',
                'route' => [ 'null', 'string' ],
                'routeParameters' => 'array',
                'extras' => 'array',
                'current' => [ 'bool', 'null' ],
            ])
        ;
    }

    protected function configureItem(ItemInterface $item, array $options)
    {
        $item
            ->setUri($options['uri'])
            ->setLabel($options['label'])
            ->setAttributes($options['attributes'])
            ->setLinkAttributes($options['linkAttributes'])
            ->setChildrenAttributes($options['childrenAttributes'])
            ->setLabelAttributes($options['labelAttributes'])
            ->setExtras($options['extras'])
            ->setDisplay($options['display'])
            ->setDisplayChildren($options['displayChildren'])
        ;
    }

    public function getPriority()
    {
        return 20;
    }
}
