<?php

namespace Lephare\Bundle\MenuBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Yaml\Parser;

class Builder extends ContainerAware
{
    protected $reservedRole = null;

    public function getMenu()
    {
        $factory = $this->container->get('knp_menu.factory');

        if (!file_exists($filename = $this->getConfig())) {
            throw new InvalidConfigurationException('"' . $filename . '" configuration file not found.');
        }
        $yaml = new Parser;
        $menuConfig = $yaml->parse(file_get_contents($filename));

        if (!is_array($menuConfig)) {
            throw new InvalidConfigurationException("Configuration must be an array.");
        }

        $this->sanitizeConfig($menuConfig);

        foreach ($menuConfig as $menuName => $menuDefinition) {
            if (count($menu = $menuDefinition['menu']) !== 1) {
                throw new InvalidConfigurationException('Invalid number of root node for menu "' . $menuName . '"');
            }

            $opts = $menuRootNode = current($menu);
            unset($opts['children'], $opts['name']);

            if (!isset($menuRootNode['name'])) {
                throw new InvalidConfigurationException('Node name for menu item must be defined.');
            }

            $node = $factory->createItem($menuRootNode['name'], $opts);

            if (isset($menuDefinition['options']['reserved_role'])) {
                $this->reservedRole = $menuDefinition['options']['reserved_role'];
            }

            if (isset($menuRootNode['children'])) {
                $this->addChildren($node, $menuRootNode['children']);
            }

            return $menuDefinition['options']['remove_empty'] ? $this->removeEmptyMenus($node) : $node;
        }
    }

    protected function sanitizeConfig(array &$config)
    {
        foreach ($config as &$item) {
            if (is_array($item)) {
                $this->sanitizeConfig($item);
            } else {
                if ('@' === $item[0]) {
                    $item = $this->container->get(substr($item, 1, strlen($item)));
                } elseif (!!preg_match('/%(\w+)%/', $item, $matches)) {
                    $item = $matches[1];
                } else {
                    $item = $item;
                }
            }
        }
    }

    protected function getConfig()
    {
        return $this->container->getParameter('kernel.root_dir') . '/Resources/' . 'menu.yml';
    }

    protected function addChildren(ItemInterface $menu, array $children)
    {
        foreach ($children as $menuItem) {
            if (is_callable($menuItem)) {

                $node = call_user_func_array($menuItem, [ $menu ]);

            } elseif (is_array($menuItem)) {

                $role = null;
                $opts = $menuItem;
                unset($opts['children'], $opts['name']);

                if (!isset($menuItem['name'])) {
                    throw new InvalidConfigurationException('Node name for menu item must be defined.');
                }

                if (isset($opts['role'])) {
                    $role = $opts['role'];
                    unset($opts['role']);
                }

                if (null !== $this->reservedRole && $this->reservedRole === $role) {
                    $opts['extra']['reserved'] = true;
                }

                $node = $menu->addChild($menuItem['name'], $opts);

                if (null !== $role) {
                    $node->setDisplay($this->isGranted($role));
                }

                if (isset($menuItem['children'])) {
                    $this->addChildren($node, $menuItem['children']);
                }

            }
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
