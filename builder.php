<?php

namespace Builder;

/** @var array $argv */
require_once __DIR__ . '/vendor/autoload.php';

if (PHP_SAPI == 'cli') {
    $dest = realpath($argv[1]);
    if (!array_key_exists(1, $argv) || !is_dir($dest)) die('Please provide a valid project directory');
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $cmd = sprintf('SET BUILDER_DEST=%s && php -S 0.0.0.0:8000 "%s"', $dest, __FILE__);
        $browser = sprintf('start /b http://localhost:8000');
    } else {
        $cmd = sprintf('BUILDER_DEST="%s" php -S 0.0.0.0:8000 "%s"', $dest, __FILE__);
    }
    if (isset($browser)) exec($browser);
    printf("Starting server, press Ctrl+C to quit\n");
    exec($cmd);
} else {
    $dest = realpath(getenv('BUILDER_DEST'));
    $project = new Project();
    $success = false;
    if (!empty($_POST)) {
        foreach($_POST['system'] as $package => $checked) {
            if ((bool)$checked) $project->addPackage($package);
            else $project->removePackage($package);
        }
        foreach($_POST['php'] as $package => $checked) {
            if ((bool)$checked) $project->addPhpPackage($package);
            else $project->removePhpPackage($package);
        }
        foreach($_POST['pecl'] as $extension => $checked) {
            if ((bool)$checked) $project->addPhpExtension($extension);
            else $project->removePhpExtension($extension);
        }
        foreach($_POST['role'] as $role => $checked) {
            if ((bool)$checked) $project->addRole($role);
            else $project->removeRole($role);
        }
        $project->save();
        $success = true;
    }
    include(__DIR__ . '/src/template.php');
}