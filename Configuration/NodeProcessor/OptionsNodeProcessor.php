<?php

namespace Lephare\Bundle\MenuBundle\Configuration\NodeProcessor;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Lephare\Bundle\MenuBundle\Configuration\ConfigurationPriorityList;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
        $this->configureItem($node, $options);
    }

    protected function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'uri' => null,
            'label' => null,
            'attributes' => array(),
            'linkAttributes' => array(),
            'childrenAttributes' => array(),
            'labelAttributes' => array(),
            'extras' => array(),
            'display' => true,
            'displayChildren' => true,
            'route' => null,
        ]);
    }

    /**
     * Configures the newly created item with the passed options
     *
     * @param ItemInterface $item
     * @param array         $options
     */
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
