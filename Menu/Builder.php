<?php

namespace Horrorin\Bundle\MenuBuilderBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware
{
    protected $reservedRole = null;

    public function __call($name, $args)
    {
        $factory = $this->container->get('knp_menu.factory');

        $menuConfig = $this->container->getParameter('horrorin_menubuilder.menus');
        foreach ($menuConfig as $menuName => $definition) {
            if ($name === lcfirst($this->container->camelize($menuName)) . 'Menu') {
                if (count($menu = $definition['definition']) !== 1) {
                    throw new InvalidConfigurationException('Invalid number of root node for menu "' . $menuName . '"');
                }

                $opts = $menuRootNode = current($menu);
                unset($opts['children']);

                $node = $factory->createItem(key($menu), $opts);

                if (isset($definition['options']['reserved_role'])) $this->reservedRole = $definition['options']['reserved_role'];

                if (isset($menuRootNode['children'])) $this->addChildren($node, $menuRootNode['children']);

                return $definition['options']['remove_empty'] ? $this->removeEmptyMenus($node) : $node;
            }
        }
    }

    protected function addChildren(ItemInterface $menu, array $children)
    {
        foreach ($children as $name => $child) {
            $role = null;
            $opts = $child;
            unset($opts['children']);

            if (isset($opts['role'])) {
                $role = $opts['role'];
                unset($opts['role']);
            }

            if (null !== $this->reservedRole && $this->reservedRole === $role) $opts['extra']['reserved'] = true;

            $node = $menu->addChild($name, $opts);

            if (null !== $role) $node->setDisplay($this->isGranted($role));

            if (isset($child['children'])) $this->addChildren($node, $child['children']);
        }
    }

    protected function removeEmptyMenus(ItemInterface $menu)
    {
        foreach ($menu as $node) {
            if ($node->hasChildren()) {
                $this->removeEmptyMenus($node);
            } elseif (strpos($node->getUri(), '/') !== 0) {
                $node->setDisplay(false);
            }
        }

        return $menu;
    }

    protected function isGranted($attributes, $object = null)
    {
        return $this->container->get('security.context')->isGranted($attributes, $object = null);
    }
}
