<?php

namespace Deck36\Bundle\Plan9Bundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class Deck36Plan9Extension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        if (!isset($config['overview']) || !isset($config['playground'])) {
            throw new \InvalidArgumentException(
              'The "overview" and "playground" options must be set'
            );
        }

        $container->setParameter(
          'deck36_plan9.parameter.list_pixel_free',
          $config['list_pixel_free']
        );

        $container->setParameter(
          'deck36_plan9.parameter.overview.size.rows',
          $config['overview']['size']['rows']
        );

        $container->setParameter(
          'deck36_plan9.parameter.overview.size.cols',
          $config['overview']['size']['cols']
        );

        $container->setParameter(
          'deck36_plan9.parameter.overview.solutionimage',
          $config['overview']['solutionimage']
        );

        $container->setParameter(
          'deck36_plan9.parameter.playground.desktopsize',
          $config['playground']['desktopsize']
        );

        $container->setParameter(
          'deck36_plan9.parameter.playground.mobilesize',
          $config['playground']['mobilesize']
        );
    }
}
