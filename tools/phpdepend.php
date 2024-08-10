<?php

include __DIR__ . '/../vendor/autoload.php';
include '/var/www/projects/fdsdb/library/Filesystem/Finders/FileFinder.php';

use Library\Filesystem\Finders\FileFinder;

/** @var \Library\Filesystem\Finders\FileFinder $finder */
$finder = new \Library\Filesystem\Finders\FileFinder();
$files = $finder->wantDirs(false)->wantFiles(true)->wantExtension('php')->maxDepth(10)->includePath(__DIR__.'/../src')->find();

$phpdepend = new CzProject\PhpDepend\PhpDepend;

foreach ($files as $file) {
    $phpdepend->parseFile($file);
    // getting result
    $ac = $phpdepend->getClasses(); // returns list of defined classes, interfaces & traits
    $ad = $phpdepend->getDependencies(); // returns list of required classes, interfaces & traits

    $file = "<ROOT>/".trim(str_replace(dirname(__DIR__), '', realpath($file)), '/');

    print(str_pad("======[ $file ]=", 60, '=')."\n");
    print("Defined class-like objects:\n");
    foreach($ac as $classname) {
        print("   $classname\n");
    }

    print("Required class-like objects:\n");
    foreach($ad as $classname) {
        print("   $classname\n");
    }
    print("\n");
}
print("==============================================================================================\n");

// file parsing
//$phpdepend->parseFile('MyClass.php');

// code snippet parsing
//$source = file_get_contents('MyClass.php');
//$phpdepend->parse($source);
