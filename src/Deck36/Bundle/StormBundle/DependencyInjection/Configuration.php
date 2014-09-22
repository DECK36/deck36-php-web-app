<?php

namespace Deck36\Bundle\StormBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('deck36_storm');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        $rootNode
            ->children()
                ->arrayNode('php')
                    ->children()
                        ->scalarNode('executor')
                            ->defaultValue('/usr/bin/php')
                        ->end()
                        ->scalarNode('main')
                            ->defaultValue('app/console')
                        ->end()
                        ->variableNode('executor_params')                                         
                        ->end()
                        ->variableNode('main_params')                            
                        ->end()                        
                    ->end()
                ->end()
                ->booleanNode('debug')
                    ->defaultFalse()
                ->end()
                ->integerNode('default_parallelism_hint')
                    ->defaultValue(1)
                ->end()
                ->integerNode('default_num_tasks')
                    ->defaultValue(1)
                ->end()
                ->arrayNode('rabbitmq')
                    ->children()
                        ->scalarNode('host')
                            ->defaultValue('localhost')
                        ->end()
                        ->integerNode('port')
                            ->defaultValue(5672)
                        ->end()
                        ->scalarNode('user')
                            ->defaultValue('guest')
                        ->end()
                        ->scalarNode('pass')
                            ->defaultValue('guest')
                        ->end()
                        ->scalarNode('vhost')
                            ->defaultValue('/')
                        ->end()
                        ->integerNode('heartbeat')
                            ->defaultValue(10)
                        ->end()
                        ->integerNode('prefetch')
                            ->defaultValue(1)
                        ->end()
                        ->integerNode('spout_tasks')
                            ->defaultValue(4)
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('HighFiveBolt')
                    ->children()
                        ->scalarNode('main')
                            ->defaultValue('storm:bolt:HighFive')
                        ->end()
                        ->variableNode('params')                            
                        ->end()
                        ->arrayNode('rabbitmq')
                            ->children()
                                ->scalarNode('exchange')
                                    ->defaultValue('plan9')
                                ->end()
                                ->scalarNode('routing_key')
                                    ->defaultValue('cbt.#')
                                ->end()
                                ->scalarNode('target_exchange')
                                    ->defaultValue('plan9_target')
                                ->end()                                
                            ->end()
                        ->end()
                        ->integerNode('badge_timewindow')
                            ->min(1)
                        ->end()
                        ->arrayNode('badge')
                            ->children()
                                ->scalarNode('name')
                                ->end()
                                ->scalarNode('text')
                                ->end()
                                ->scalarNode('color')
                                ->end()
                                ->scalarNode('size')
                                ->end()
                                ->scalarNode('effect')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('EmptyTickTupleBolt')
                    ->children()
                        ->scalarNode('main')
                            ->defaultValue('storm:bolt:EmptyTickTuple')
                        ->end()
                        ->variableNode('params')                            
                        ->end()
                        ->arrayNode('rabbitmq')
                            ->children()
                                ->scalarNode('exchange')
                                    ->defaultValue('plan9')
                                ->end()
                                ->scalarNode('routing_key')
                                    ->defaultValue('#')
                                ->end()
                                ->scalarNode('target_exchange')
                                    ->defaultValue('plan9_target')
                                ->end()                                
                            ->end()
                        ->end()
                        ->integerNode('tick_frequency')
                            ->min(0)
                        ->end()
                        ->arrayNode('badge')
                            ->children()
                                ->scalarNode('name')
                                ->end()
                                ->scalarNode('text')
                                ->end()
                                ->scalarNode('color')
                                ->end()
                                ->scalarNode('size')
                                ->end()
                                ->scalarNode('effect')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('DeludedKittenRobbersBolt')
                    ->children()
                        ->scalarNode('main')
                            ->defaultValue('storm:bolt:DeludedKittenRobbers')
                        ->end()
                        ->variableNode('params')                            
                        ->end()
                        ->arrayNode('rabbitmq')
                            ->children()
                                ->scalarNode('exchange')
                                    ->defaultValue('plan9')
                                ->end()
                                ->scalarNode('routing_key')
                                    ->defaultValue('#')
                                ->end()
                                ->scalarNode('target_exchange')
                                    ->defaultValue('plan9_target')
                                ->end()                                
                            ->end()
                        ->end()
                        ->integerNode('attack_frequency_secs')
                            ->min(0)
                        ->end()
                        ->arrayNode('badge')
                            ->children()
                                ->scalarNode('name')
                                ->end()
                                ->scalarNode('text')
                                ->end()
                                ->scalarNode('color')
                                ->end()
                                ->scalarNode('size')
                                ->end()
                                ->scalarNode('effect')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('RecordBreakerBolt')
                    ->children()
                        ->scalarNode('main')
                            ->defaultValue('storm:bolt:RecordBreaker')
                        ->end()
                        ->variableNode('params')                            
                        ->end()
                        ->arrayNode('rabbitmq')
                            ->children()
                                ->scalarNode('exchange')
                                    ->defaultValue('plan9')
                                ->end()
                                ->scalarNode('routing_key')
                                    ->defaultValue('#')
                                ->end()                                
                                ->scalarNode('target_exchange')
                                    ->defaultValue('plan9_target')
                                ->end()                                
                            ->end()
                        ->end()
                        ->arrayNode('badge')
                            ->children()
                                ->scalarNode('name')
                                ->end()
                                ->scalarNode('text')
                                ->end()
                                ->scalarNode('color')
                                ->end()
                                ->scalarNode('size')
                                ->end()
                                ->scalarNode('effect')
                                ->end()
                            ->end()
                        ->end()                        
                    ->end()
                ->end()
                ->arrayNode('RecordMasterBolt')
                    ->children()
                        ->scalarNode('main')
                            ->defaultValue('storm:bolt:RecordMaster')
                        ->end()
                        ->variableNode('params')
                        ->end()
                        ->arrayNode('rabbitmq')
                            ->children()
                                ->scalarNode('exchange')
                                    ->defaultValue('plan9')
                                ->end()
                                ->scalarNode('routing_key')
                                    ->defaultValue('#')
                                ->end()                                
                                ->scalarNode('target_exchange')
                                    ->defaultValue('plan9_target')
                                ->end()                                
                            ->end()
                        ->end()
                        ->arrayNode('badge')
                            ->children()
                                ->scalarNode('name')
                                ->end()
                                ->scalarNode('text')
                                ->end()
                                ->scalarNode('color')
                                ->end()
                                ->scalarNode('size')
                                ->end()
                                ->scalarNode('effect')
                                ->end()
                            ->end()
                        ->end()                        
                    ->end()
                ->end()
                ->arrayNode('StatusLevelBolt')
                    ->children()
                        ->scalarNode('main')
                            ->defaultValue('storm:bolt:StatusLevel')
                        ->end()
                        ->variableNode('params')                            
                        ->end()
                        ->arrayNode('rabbitmq')
                            ->children()
                                ->scalarNode('exchange')
                                    ->defaultValue('plan9')
                                ->end()
                                ->scalarNode('routing_key')
                                    ->defaultValue('#')
                                ->end()                                
                                ->scalarNode('target_exchange')
                                    ->defaultValue('plan9_target')
                                ->end()                                
                            ->end()
                        ->end()    
                        ->variableNode('levels')
                        ->end()
                        ->arrayNode('badge')
                            ->children()
                                ->scalarNode('name')
                                ->end()
                                ->scalarNode('text')
                                ->end()
                                ->scalarNode('color')
                                ->end()
                                ->scalarNode('size')
                                ->end()
                                ->scalarNode('effect')
                                ->end()
                            ->end()
                        ->end()                    
                    ->end()
                ->end() 
                ->arrayNode('PrimeCatBolt')
                    ->children()
                        ->scalarNode('main')
                            ->defaultValue('storm:bolt:PrimeCat')
                        ->end()
                        ->variableNode('params')                            
                        ->end()
                        ->arrayNode('rabbitmq')
                            ->children()
                                ->scalarNode('exchange')
                                    ->defaultValue('plan9')
                                ->end()
                                ->scalarNode('routing_key')
                                    ->defaultValue('#')
                                ->end()                                
                                ->scalarNode('target_exchange')
                                    ->defaultValue('plan9_target')
                                ->end()                                
                            ->end()
                        ->end()    
                        ->integerNode('badge_timewindow')
                            ->min(1)
                        ->end()                  
                        ->integerNode('primecat_frequency')
                            ->min(1)
                        ->end()
                        ->arrayNode('badge')
                            ->children()
                                ->scalarNode('name')
                                ->end()
                                ->scalarNode('text')
                                ->end()
                                ->scalarNode('color')
                                ->end()
                                ->scalarNode('size')
                                ->end()
                                ->scalarNode('effect')
                                ->end()
                            ->end()
                        ->end()    
                    ->end()
                ->end()
                ->arrayNode('KittenRobbersFromOuterSpaceBolt')
                    ->children()
                        ->scalarNode('main')
                            ->defaultValue('storm:bolt:KittenRobbers')
                        ->end()
                        ->variableNode('params')                            
                        ->end()
                        ->arrayNode('rabbitmq')
                            ->children()
                                ->scalarNode('exchange')
                                    ->defaultValue('plan9')
                                ->end()
                                ->scalarNode('routing_key')
                                    ->defaultValue('#')
                                ->end()                                
                                ->scalarNode('target_exchange')
                                    ->defaultValue('plan9_target')
                                ->end()                                
                            ->end()
                        ->end()    
                        ->integerNode('robber_frequency')
                            ->min(1)
                        ->end()
                        ->arrayNode('badge')
                            ->children()
                                ->scalarNode('name')
                                ->end()
                                ->scalarNode('text')
                                ->end()
                                ->scalarNode('color')
                                ->end()
                                ->scalarNode('size')
                                ->end()
                                ->scalarNode('effect')
                                ->end()
                            ->end()
                        ->end()                  
                    ->end()
                ->end()
                ->arrayNode('RaiderOfTheKittenRobbersBolt')
                    ->children()
                        ->scalarNode('main')
                            ->defaultValue('storm:bolt:RaiderOfTheKittenRobbers')
                        ->end()
                        ->variableNode('params')                            
                        ->end()
                        ->arrayNode('rabbitmq')
                            ->children()
                                ->scalarNode('exchange')
                                    ->defaultValue('plan9')
                                ->end()
                                ->scalarNode('routing_key')
                                    ->defaultValue('#')
                                ->end()                                
                                ->scalarNode('target_exchange')
                                    ->defaultValue('plan9_target')
                                ->end()                                
                            ->end()
                        ->end()    
                        ->integerNode('badge_timewindow')
                            ->min(1)
                        ->end()
                        ->integerNode('the_chosen_one_timewindow')
                            ->min(1)
                        ->end()
                        ->arrayNode('badge')
                            ->children()
                                ->scalarNode('name')
                                ->end()
                                ->scalarNode('text')
                                ->end()
                                ->scalarNode('color')
                                ->end()
                                ->scalarNode('size')
                                ->end()
                                ->scalarNode('effect')
                                ->end()
                            ->end()
                        ->end()                                 
                    ->end()
                ->end()
                ->arrayNode('StumbleBlunderBolt')
                    ->children()
                        ->scalarNode('main')
                            ->defaultValue('storm:bolt:StumbleBlunder')
                        ->end()
                        ->variableNode('params')                            
                        ->end()
                        ->arrayNode('rabbitmq')
                            ->children()
                                ->scalarNode('exchange')
                                    ->defaultValue('plan9')
                                ->end()
                                ->scalarNode('routing_key')
                                    ->defaultValue('#')
                                ->end()                                
                                ->scalarNode('target_exchange')
                                    ->defaultValue('plan9_target')
                                ->end()                                
                            ->end()
                        ->end()    
                        ->integerNode('badge_timewindow')
                            ->min(1)
                        ->end()
                        ->integerNode('max_tolerance')
                            ->min(1)
                        ->end()
                        ->arrayNode('badge')
                            ->children()
                                ->scalarNode('name')
                                ->end()
                                ->scalarNode('text')
                                ->end()
                                ->scalarNode('color')
                                ->end()
                                ->scalarNode('size')
                                ->end()
                                ->scalarNode('effect')
                                ->end()
                            ->end()
                        ->end()   
                    ->end()
                ->end()                                                              
            ->end()
        ;

        return $treeBuilder;
    }
}

























