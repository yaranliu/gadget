<?php
/**
 * Created by PhpStorm.
 * User: ufukyaranli
 * Date: 7.11.2018
 * Time: 12:23
 */

namespace Yaranliu\Gadget\Tests;


use Yaranliu\Gadget\Facades\Gadget;
use Yaranliu\Gadget\GadgetServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{

    protected function getPackageProviders($app)
    {
        return [GadgetServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Gadget' => Gadget::class,
        ];
    }

}