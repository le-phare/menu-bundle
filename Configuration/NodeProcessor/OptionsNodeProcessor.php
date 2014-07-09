<?php

namespace Lephare\Bundle\MenuBundle\Configuration\NodeProcessor;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Lephare\Bundle\MenuBundle\Configuration\ConfigurationPriorityList;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;

class OptionsNodeProcessor extends AbstractNodeProcessor implements NodeProcessorInterface
{
    public function getName()
    {
        return 'options';
    }

    public function process($configuration, ConfigurationPriorityList $processors, FactoryInterface $factory, ItemInterface &$node = null)
    {
        if (!is_array($configuration)) {
            return false;
        }

        $resolver = new OptionsResolver;
        $this->setDefaultOptions($resolver);

        $options = $resolver->resolve($configuration);
        $options = $factory->createItem('node', $options)->toArray();

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
