<?php

namespace Library\Logger;

use Library\Config\Contracts\ConfigurationInterface;
use Library\Config\Definitions\Builder\Definitions\ArrayNodeDefinition;
use Library\Config\Definitions\Builder\Definitions\NodeDefinition;
use Library\Config\Definitions\Builder\Processor;
use Library\Config\Definitions\Builder\TreeBuilder;
use Library\Logger\Contracts\LogLevel;
use Modules\Tree\Tree;

class Configuration implements ConfigurationInterface
{
    final public function processConfiguration(ConfigurationInterface $configuration, array $configs): array
    {
        $processor = new Processor();
        return $processor->processConfiguration($configuration, $configs);
    }
    
    public function getConfigTree(ArrayNodeDefinition $root): NodeDefinition
    {
        return $root
            ->children()
                ->arrayNode('config')
                    ->canBeUnset()
                    ->ignoreExtraKeys(false)
                    ->children()
                        ->scalarNode('log_path')
                            ->defaultValue('{path.log}/logger-{env.APP_MODE}')
                        ->end()
                        ->booleanNode('show_timestamps')
                            ->defaultTrue()
                            ->info('Show timestamps in the log output.')
                        ->end()
                        ->booleanNode('enable_channels')
                            ->defaultTrue()
                            ->info('Enable channels in the logger.')
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
    
    public function getDefaultsTree(ArrayNodeDefinition $root): NodeDefinition
    {
        return $root
            ->children()
                ->arrayNode('defaults')
                    ->canBeUnset()
                    ->ignoreExtraKeys(false)
                    ->children()
                        ->scalarNode('handler_class')->defaultValue('Library\Logger\Formatters\StdoutWriter')->end()
                        ->scalarNode('formatter_class')->defaultValue('Library\Logger\Formatters\LineFormatter')->end()
                        ->scalarNode('formatter_format')->defaultValue('[%timestamp%] %channel%.%level_name%: %message% %context%')->end()
                    ->end()
                ->end()
            ->end();
    }
    
    public function getLoggersTree(ArrayNodeDefinition $root): NodeDefinition
    {
        $root
            ->children()
                ->arrayNode('loggers')
                    ->canBeUnset()
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                    ->children()
                        ->arrayNode('handlers')->requiresAtLeastOneElement()->isRequired()->end()
                        ->arrayNode('processors')->canBeUnset()->end()
                        ->arrayNode('filters')->canBeUnset()->end()
                    ->end()
            ->end();
        return $root;
    }
    
    public function getSingleLoggerTree(ArrayNodeDefinition $root): NodeDefinition
    {
        $root
            ->children()
                ->arrayNode('logger')
                    ->ignoreExtraKeys(false)
                    ->children()
                        ->arrayNode('handlers')
                            ->beforeNormalization()->castToArray()->end()
                            ->scalarPrototype()->end()
                        ->end()
                        ->arrayNode('processors')
                            ->beforeNormalization()->castToArray()->end()
                            ->scalarPrototype()->end()
                        ->end()
                        ->arrayNode('filters')
                            ->beforeNormalization()->castToArray()->end()
                            ->scalarPrototype()->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
        return $root;
    }
    
    public function getHandlersTree(ArrayNodeDefinition $root): NodeDefinition
    {
        $root
            ->children()
                ->arrayNode('handlers')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->ignoreExtraKeys(false)
                        ->children()
                            ->scalarNode('class')->isRequired()->end()
                            ->scalarNode('formatter')
                                ->beforeNormalization()
                                    ->ifNull()->thenUnset()
                                ->end()
                            ->end()
                            ->arrayNode('handlers')
                                ->canBeUnset()
                                ->beforeNormalization()
                                    ->ifNull()->thenUnset()
                                    ->ifEmpty()->thenUnset()
                                    //->castToArray()
                                ->end()
                                ->scalarPrototype()->end()
                                //->variableNode()->end()
                            ->end()
                            ->arrayNode('processors')->canBeUnset()->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
        return $root;
    }
    
    public function getWritersTree(ArrayNodeDefinition $root): NodeDefinition
    {
        return $root
            ->children()
                ->arrayNode('writers')
                    ->info('A writer is almost like a handler, except instead of passing the LogItem to the next one, it will output it somewhere.')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->ignoreExtraKeys(false)
                        ->children()
                            ->scalarNode('class')->isRequired()->end()
                            ->scalarNode('formatter')->end()
                            ->arrayNode('handlers')
                                ->canBeUnset()
                                ->validate()
                                    ->ifString()->castToArray()
                                    ->ifEmpty()->thenUnset()
                                    ->ifNull()->thenUnset()
                                ->end()
                                ->scalarPrototype()->end()
                            ->end()
                            ->arrayNode('processors')
                                ->canBeUnset()
                                ->beforeNormalization()
                                    ->always(function ($v) {
                                        print("INSIDE PROCESSORS!!!\n");
                                        print_r($v);
                                        return $v;
                                    })
                                    ->ifString()->castToArray()
                                    ->ifEmpty()->thenUnset()
                                    ->ifNull()->thenUnset()
                                ->end()
                                ->treatNullLike([])
                                ->scalarPrototype()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
    
    public function getProcessorsTree(ArrayNodeDefinition $root): NodeDefinition
    {
        return $root
            ->children()
                ->arrayNode('processors')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->ignoreExtraKeys(false)
                        ->children()
                            ->scalarNode('class')->isRequired()->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
    
    public function getFormattersTree(ArrayNodeDefinition $root): NodeDefinition
    {
        return $root
            ->children()
                ->arrayNode('formatters')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->ignoreExtraKeys(false)
                        ->children()
                            ->scalarNode('class')->isRequired()->end()
                            ->scalarNode('format')
                                ->beforeNormalization()->ifNull()->thenUnset()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
    
    public function getFiltersTree(ArrayNodeDefinition $root): NodeDefinition
    {
        return $root
            ->children()
                ->arrayNode('filters')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('class')->isRequired()->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
    
    public function getCascadeTree(ArrayNodeDefinition $root): NodeDefinition
    {
        return $root
            ->children()
                ->arrayNode('cascade')
                    ->info("Define the logger cascade tree here.  Every 'logger' is the start of a chain, and the end should be a writer w/formatter.")
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('class')->isRequired()->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
    
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $tb = new TreeBuilder('logger');
        $root = $tb->getRootNode();
        
        $this->getConfigTree($root);
        $this->getDefaultsTree($root);
        //$this->getLoggersTree($root);
        $this->getSingleLoggerTree($root);
        $this->getHandlersTree($root);
        $this->getWritersTree($root);
        $this->getProcessorsTree($root);
        $this->getFormattersTree($root);
        $this->getFiltersTree($root);
        $this->getCascadeTree($root);

        return $tb;
    }
}
