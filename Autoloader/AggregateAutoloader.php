<?php
namespace Poirot\Loader\Autoloader;

use Poirot\Loader\AggregateTrait;
use Poirot\Loader\Interfaces\iSplAutoloader;

if (class_exists('Poirot\\Loader\\AggregateAutoloader'))
    return;

require_once __DIR__ . '/NamespaceAutoloader.php';
require_once __DIR__ . '/../Interfaces/iSplAutoloader.php';

class AggregateAutoloader implements iSplAutoloader
{
    use AggregateTrait;

    /**
     * Tmp cache used to ignore recursion call for registered
     * autoloader objects
     *
     * @var array[hash=>iSplAutoloader]
     */
    protected $__tmp_registered_hash = [];

    /**
     * Construct
     *
     */
    function __construct()
    {
        // Attach Default Autoloaders:
        ## register, so we can access related autoloader classes
        $autoloader = new NamespaceAutoloader(['Poirot\\Loader' => __DIR__]);
        $autoloader->register();

        $this->attach($autoloader);
        $this->attach(new ClassMapAutoloader);
    }

    // Implement iSplAutoloader:

    /**
     * Register to spl autoloader
     *
     * <code>
     * spl_autoload_register(callable);
     * </code>
     *
     * @return void
     */
    function register()
    {
        foreach(clone $this->_getPrioQuee() as $i => $sa) {
            $objectHash = spl_object_hash($sa);

            if (array_key_exists($objectHash, $this->__tmp_registered_hash))
                // registered before
                return;

            /** @var iSplAutoloader $sa */
            $sa->register();
            $this->__tmp_registered_hash[$objectHash] = $sa;
        }
    }

    /**
     * Unregister from spl autoloader
     *
     * ! using same callable on register
     *
     * @return void
     */
    function unregister()
    {
        foreach($this->__tmp_registered_hash as $sa)
            /** @var iSplAutoloader $sa */
            $sa->unregister();
    }
}