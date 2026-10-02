<?php

namespace Config;

use CodeIgniter\Config\View as BaseView;
use CodeIgniter\View\ViewDecoratorInterface;

class View extends BaseView
{
    public $saveData = false;

    public $filters = [];

    public $plugins = [];

    public array $decorators = [];

    public string $appOverridesFolder = 'overrides';
}
