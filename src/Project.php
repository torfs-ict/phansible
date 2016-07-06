<?php

namespace Builder;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class Project {
    private $dir;
    private $mirror = [];
    private $playbook;
    private $remove = [];
    private $vars;
    private function getPlayBook() {
        $yaml = new Yaml();
        $this->playbook = $yaml->parse(file_get_contents($this->dir . '/ansible/playbook.yml'));
    }
    private function getVars() {
        $yaml = new Yaml();
        $this->vars = $yaml->parse(file_get_contents($this->dir . '/ansible/vars/all.yml'));
    }
    public function __construct() {
        $this->dir = realpath(getenv('BUILDER_DEST'));
        $this->getPlayBook();
        $this->getVars();
    }

    public function addPackage($package) {
        if ($this->hasPackage($package)) return;
        if (!array_key_exists('server', $this->vars)) $this->vars['server'] = [];
        if (!array_key_exists('packages', $this->vars['server'])) $this->vars['server']['packages'] = [];
        $this->vars['server']['packages'][] = $package;
    }

    public function addPhpExtension($pecl) {
        if ($this->hasPhpExtension($pecl)) return;
        if (!array_key_exists('php7', $this->vars)) $this->vars['php7'] = [];
        if (!array_key_exists('pecl', $this->vars['php7'])) $this->vars['php7']['pecl'] = [];
        $this->vars['php7']['pecl'][] = $pecl;
    }

    public function addPhpPackage($package) {
        if ($this->hasPhpPackage($package)) return;
        if (!array_key_exists('php7', $this->vars)) $this->vars['php7'] = [];
        if (!array_key_exists('extensions', $this->vars['php7'])) $this->vars['php7']['extensions'] = [];
        $this->vars['php7']['extensions'][] = $package;
    }

    public function addRole($role) {
        if ($this->hasRole($role)) return;
        if (!array_key_exists(0, $this->playbook)) $this->playbook[0] = [];
        if (!array_key_exists('roles', $this->playbook[0])) $this->playbook[0]['roles'] = [];
        $this->playbook[0]['roles'][] = $role;
        $this->mirror[] = $role;
    }

    public function hasPackage($package) {
        if (!array_key_exists('server', $this->vars)) return false;
        if (!array_key_exists('packages', $this->vars['server'])) return false;
        $ret = array_search($package, $this->vars['server']['packages'], true);
        return $ret !== false;
    }

    public function hasPhpExtension($pecl) {
        if (!array_key_exists('php7', $this->vars)) return false;
        if (!array_key_exists('pecl', $this->vars['php7'])) return false;
        $ret = array_search($pecl, $this->vars['php7']['pecl'], true);
        return $ret !== false;
    }

    public function hasPhpPackage($package) {
        if (!array_key_exists('php7', $this->vars)) return false;
        if (!array_key_exists('extensions', $this->vars['php7'])) return false;
        $ret = array_search($package, $this->vars['php7']['extensions'], true);
        return $ret !== false;
    }

    public function hasRole($role) {
        if (!array_key_exists(0, $this->playbook)) return false;
        if (!array_key_exists('roles', $this->playbook[0])) return false;
        $ret = array_search($role, $this->playbook[0]['roles'], true);
        return $ret !== false;
    }

    public function removePackage($package) {
        if (!$this->hasPackage($package)) return;
        $array =& $this->vars['server']['packages'];
        $index = array_search($package, $array, true);
        unset($array[$index]);
        $this->vars['server']['packages'] = array_values($array);
    }

    public function removePhpExtension($pecl) {
        if (!$this->hasPhpExtension($pecl)) return;
        $array =& $this->vars['php7']['pecl'];
        $index = array_search($pecl, $array, true);
        unset($array[$index]);
        $this->vars['php7']['pecl'] = array_values($array);
    }

    public function removePhpPackage($package) {
        if (!$this->hasPhpPackage($package)) return;
        $array =& $this->vars['php7']['extensions'];
        $index = array_search($package, $array, true);
        unset($array[$index]);
        $this->vars['php7']['extensions'] = array_values($array);
    }

    public function removeRole($role) {
        if (!$this->hasRole($role)) return;
        $array =& $this->playbook[0]['roles'];
        $index = array_search($role, $array, true);
        unset($array[$index]);
        $this->playbook[0]['roles'] = array_values($array);
        $this->remove[] = $role;
    }

    public function save() {
        $yaml = new Yaml();
        file_put_contents($this->dir . '/ansible/playbook.yml', $yaml->dump($this->playbook));
        file_put_contents($this->dir . '/ansible/vars/all.yml', $yaml->dump($this->vars));

        $fs = new Filesystem();
        foreach($this->mirror as $role) {
            $fs->mirror(__DIR__ . '/../ansible/roles/' . $role, $this->dir . '/ansible/roles/' . $role);
        }
        foreach($this->remove as $role) {
            $fs->remove($this->dir . '/ansible/roles/' . $role);
        }
    }
}