<?php

namespace Deck36\Bundle\StormBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class Deck36StormExtension extends Extension
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

        // HighFiveBolt
        $container->setParameter('highfive_badge_timewindow', $config['HighFiveBolt']['badge_timewindow']);
        
        $container->setParameter('highfive__badge_name',   $config['HighFiveBolt']['badge']['name']);
        $container->setParameter('highfive__badge_text',   $config['HighFiveBolt']['badge']['text']);
        $container->setParameter('highfive__badge_size',   $config['HighFiveBolt']['badge']['size']);
        $container->setParameter('highfive__badge_color',  $config['HighFiveBolt']['badge']['color']);
        $container->setParameter('highfive__badge_effect', $config['HighFiveBolt']['badge']['effect']);
        
        // EmptyTickTupleBolt        
        $container->setParameter('emptyticktuple__badge_name',   $config['EmptyTickTupleBolt']['badge']['name']);
        $container->setParameter('emptyticktuple__badge_text',   $config['EmptyTickTupleBolt']['badge']['text']);
        $container->setParameter('emptyticktuple__badge_size',   $config['EmptyTickTupleBolt']['badge']['size']);
        $container->setParameter('emptyticktuple__badge_color',  $config['EmptyTickTupleBolt']['badge']['color']);
        $container->setParameter('emptyticktuple__badge_effect', $config['EmptyTickTupleBolt']['badge']['effect']);
        

        // DeludedKittenRobbersBolt        
        $container->setParameter('deludedkittenrobbers__badge_name',   $config['DeludedKittenRobbersBolt']['badge']['name']);
        $container->setParameter('deludedkittenrobbers__badge_text',   $config['DeludedKittenRobbersBolt']['badge']['text']);
        $container->setParameter('deludedkittenrobbers__badge_size',   $config['DeludedKittenRobbersBolt']['badge']['size']);
        $container->setParameter('deludedkittenrobbers__badge_color',  $config['DeludedKittenRobbersBolt']['badge']['color']);
        $container->setParameter('deludedkittenrobbers__badge_effect', $config['DeludedKittenRobbersBolt']['badge']['effect']);
        



        // PrimeCatBolt
        $container->setParameter('primecat_badge_timewindow', $config['PrimeCatBolt']['badge_timewindow']);

        $container->setParameter('primecat__badge_name',   $config['PrimeCatBolt']['badge']['name']);
        $container->setParameter('primecat__badge_text',   $config['PrimeCatBolt']['badge']['text']);
        $container->setParameter('primecat__badge_size',   $config['PrimeCatBolt']['badge']['size']);
        $container->setParameter('primecat__badge_color',  $config['PrimeCatBolt']['badge']['color']);
        $container->setParameter('primecat__badge_effect', $config['PrimeCatBolt']['badge']['effect']);

        // KittenRobbersFromOuterSpaceBolt

        $container->setParameter('kittenrobbers__badge_name',   $config['KittenRobbersFromOuterSpaceBolt']['badge']['name']);
        $container->setParameter('kittenrobbers__badge_text',   $config['KittenRobbersFromOuterSpaceBolt']['badge']['text']);
        $container->setParameter('kittenrobbers__badge_size',   $config['KittenRobbersFromOuterSpaceBolt']['badge']['size']);
        $container->setParameter('kittenrobbers__badge_color',  $config['KittenRobbersFromOuterSpaceBolt']['badge']['color']);
        $container->setParameter('kittenrobbers__badge_effect', $config['KittenRobbersFromOuterSpaceBolt']['badge']['effect']);
        
        
        // RaiderOfTheKittenRobbersBolt
        $container->setParameter('raider_of_the_robbers_badge_timewindow',          $config['RaiderOfTheKittenRobbersBolt']['badge_timewindow']);
        $container->setParameter('raider_of_the_robbers_the_chosen_one_timewindow', $config['RaiderOfTheKittenRobbersBolt']['the_chosen_one_timewindow']);

        $container->setParameter('raider_of_the_robbers__badge_name',   $config['RaiderOfTheKittenRobbersBolt']['badge']['name']);
        $container->setParameter('raider_of_the_robbers__badge_text',   $config['RaiderOfTheKittenRobbersBolt']['badge']['text']);
        $container->setParameter('raider_of_the_robbers__badge_size',   $config['RaiderOfTheKittenRobbersBolt']['badge']['size']);
        $container->setParameter('raider_of_the_robbers__badge_color',  $config['RaiderOfTheKittenRobbersBolt']['badge']['color']);
        $container->setParameter('raider_of_the_robbers__badge_effect', $config['RaiderOfTheKittenRobbersBolt']['badge']['effect']);
        
        // RecordBreakerBolt
        $container->setParameter('recordbreaker__badge_name',   $config['RecordBreakerBolt']['badge']['name']);
        $container->setParameter('recordbreaker__badge_text',   $config['RecordBreakerBolt']['badge']['text']);
        $container->setParameter('recordbreaker__badge_size',   $config['RecordBreakerBolt']['badge']['size']);
        $container->setParameter('recordbreaker__badge_color',  $config['RecordBreakerBolt']['badge']['color']);
        $container->setParameter('recordbreaker__badge_effect', $config['RecordBreakerBolt']['badge']['effect']);


        // RecordMasterBolt
        $container->setParameter('recordmaster__badge_name',   $config['RecordMasterBolt']['badge']['name']);
        $container->setParameter('recordmaster__badge_text',   $config['RecordMasterBolt']['badge']['text']);
        $container->setParameter('recordmaster__badge_size',   $config['RecordMasterBolt']['badge']['size']);
        $container->setParameter('recordmaster__badge_color',  $config['RecordMasterBolt']['badge']['color']);
        $container->setParameter('recordmaster__badge_effect', $config['RecordMasterBolt']['badge']['effect']);


        // StumbleBlunderBolt
        $container->setParameter('stumble_blunder_badge_timewindow', $config['StumbleBlunderBolt']['badge_timewindow']);
        $container->setParameter('stumble_blunder_badge_max_tolerance', $config['StumbleBlunderBolt']['max_tolerance']);

        $container->setParameter('stumble_blunder__badge_name',   $config['StumbleBlunderBolt']['badge']['name']);
        $container->setParameter('stumble_blunder__badge_text',   $config['StumbleBlunderBolt']['badge']['text']);
        $container->setParameter('stumble_blunder__badge_size',   $config['StumbleBlunderBolt']['badge']['size']);
        $container->setParameter('stumble_blunder__badge_color',  $config['StumbleBlunderBolt']['badge']['color']);
        $container->setParameter('stumble_blunder__badge_effect', $config['StumbleBlunderBolt']['badge']['effect']);
        

        // StatusLevelBolt
        $container->setParameter('status_level_config', $config['StatusLevelBolt']['levels']);

        $container->setParameter('status_level__badge_name',   $config['StatusLevelBolt']['badge']['name']);
        $container->setParameter('status_level__badge_text',   $config['StatusLevelBolt']['badge']['text']);
        $container->setParameter('status_level__badge_size',   $config['StatusLevelBolt']['badge']['size']);
        $container->setParameter('status_level__badge_color',  $config['StatusLevelBolt']['badge']['color']);
        $container->setParameter('status_level__badge_effect', $config['StatusLevelBolt']['badge']['effect']);
        


    }
}
