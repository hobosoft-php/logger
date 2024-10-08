<?php

namespace Hobosoft\Logger;

use Hobosoft\Config\Config;
use Hobosoft\Config\Schema\Processor;

use Hobosoft\FileLoaders\Loader\DelegatingLoader;
use Hobosoft\FileLoaders\Loader\FileLocator;
use Hobosoft\FileLoaders\Loader\Types\IniLoader;
use Hobosoft\FileLoaders\Loader\Types\PhpLoader;
use Hobosoft\FileLoaders\Loader\Types\YamlLoader;
use Hobosoft\Logger\Registry\Registry;
use Hobosoft\Logger\Writers\BufferWriter;
use Hobosoft\Logger\Writers\PrintWriter;
use Symfony\Component\Yaml\Yaml;

define('ROOTPATH',dirname(__DIR__,1));

print("===============================================\n");
print("ROOTPATH = " . ROOTPATH . "\n");
print("===============================================\n");

include     ROOTPATH . '/vendor/autoload.php';

function include_path(array|string $path): void
{
    if (is_string($path)) {
        $path = [$path];
    }

    $files = [];
    foreach ($path as $file) {
        //if(strcspn($file, "*?{[") !== 0)
        $ret = glob($file, GLOB_BRACE);
        if($ret === false) {
            debug_print_backtrace();
            die("glob() error.\n");
        }
        $files = array_merge($files, $ret);
    }
    foreach ($files as $file) {
        print("Including $file...\n");
        include_once($file);
    }
}

spl_autoload_register(function ($class): void {
    print(" -- autoload: $class\n");
});

include_path([
    ROOTPATH . '/library/Bootloader/TinyLogger.php',
    ROOTPATH . '/library/Logger/Contracts/HandlerInterface.php',
    ROOTPATH . '/library/Logger/Contracts/*.php',
    ROOTPATH . '/library/Logger/Exceptions/*.php',
    ROOTPATH . '/library/Logger/Loaders/*.php',
    ROOTPATH . '/library/Logger/Abstract*.php',
    ROOTPATH . '/library/Logger/Logger.php',
    //__DIR__ . '/Configuration.php',
]);

//include_once(__DIR__.'/Configuration.php');

/*
$config = new Config();
$config->load(ROOTPATH.'/config');
$arr = $config->toArray();
print_r($arr);

$flat = [];
$context = [];
$flat = Utils::flatten($arr);
print_r($flat);

die();
*/

//$c = new \Hobosoft\Logger\Configuration();
//$t = $c->getConfigTreeBuilder();
//$n = $t->buildTree();
//print_r($n);
//$d = new YamlReferenceDumper();
//file_put_contents(__DIR__.'/refconfig.yaml', $d->dump($c));
//print_r($d->dump($c));

//$loader = new YamlLoader(new FileLocator(__DIR__));
//$oldcfg = $loader->load('test.yaml');
//print_r($oldcfg);


$loader = $loader ?? new DelegatingLoader([
    YamlLoader::class,
    PhpLoader::class,
    IniLoader::class,
], new FileLocator(\Hobosoft\MegaLoader\Utils::getDefinedPath('config')));


//$processor = new Processor();
//$configuration = new Configuration();
//$config = $configuration->processConfiguration($configuration, $oldcfg);
//$config = $processor->processConfiguration($configuration, $oldcfg);

$config = [];
$yaml = Yaml::dump($config, 10);

//$logger = new Logger([], ($writer = new BufferWriter()));
//$logger = new Logger([], ($writer = new FileWriter(ROOTPATH.'/var/log/test.log')));
//$logger = new Logger(($writer = new BufferWriterWrapper(new FileWriter(ROOTPATH.'/var/log/test.log'))));
$stdoutWriter = new PrintWriter();

/*
$writer = new GroupWriter([
    new FilterWriterWrapper(new FileWriter(ROOTPATH.'/var/log/test-debug.log'), new LevelFilter(LogLevel::Debug, LogLevel::Debug)),
    new FilterWriterWrapper(new FileWriter(ROOTPATH.'/var/log/test-info.log'), new LevelFilter(LogLevel::Info, LogLevel::Info)),
    new FilterWriterWrapper(new FileWriter(ROOTPATH.'/var/log/test-test1.log'), new ChannelFilter('test1')),
    new FilterWriterWrapper(new FileWriter(ROOTPATH.'/var/log/test-test2.log'), new ChannelFilter('test2')),
    new FilterWriterWrapper(new FileWriter(ROOTPATH.'/var/log/test-all.log'), new NoFilter()),

    new BufferWriterWrapper($stdoutWriter),
]);*/

$config = new Config();
$config->load(ROOTPATH.'/config');

$logger = new Logger($config, fn(Logger $logger) => new CascadeBuilder($logger));
$logger->info('Log Message.');

$writer = $logger->getWriter();
$writer->setOutputDestination(($bw = new BufferWriter('BufferWriter')));

$bw->setOutputDestination(($pw = new PrintWriter()));

/** @var Registry $reg */
//$reg = Registry::getInstance();
//$reg->addHandler(PassthruHandler::class);

print_r($writer);
$writer->traceHandlers();

try {
    $l_test1 = $logger->createChannel('test1');
    $l_test2 = $logger->createChannel('test2');
} catch (Exceptions\ChannelAlreadyCreatedException $e) {
    print("caught: ".$e->getMessage());
}

$l_test1->debug('Message 1.');
$l_test1->info('Message 2.');
$l_test1->info('Message 3.');
$l_test1->info('Message 4.');

print("done!\n");