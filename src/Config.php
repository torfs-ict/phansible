<?php

namespace Builder;

class Config {
    protected $config = [];
    public function __construct() {
        $this->config = json_decode(file_get_contents(__DIR__ . '/builder.json'), true);
    }
    public function MandatoryRoles() {
        return $this->config['roles']['mandatory'];
    }
    public function OptionalRoles() {
        return $this->config['roles']['optional'];
    }
    public function Pecl() {
        return $this->config['pecl'];
    }
    public function PhpPackages() {
        return $this->config['php'];
    }
    public function System() {
        return $this->config['system'];
    }
}