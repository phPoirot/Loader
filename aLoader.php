<?php
namespace Poirot\Loader;

!interface_exists('Poirot\Loader\Interfaces\iLoader', false)
    and require_once __DIR__.'/Interfaces/iLoader.php';

use Poirot\Loader\Interfaces\iLoader;


abstract class aLoader
    implements iLoader
    # , ipConfigurable // removed in case of dependency reduction
{
    /**
     * Construct
     *
     * $options:
     * ! given string is filepath to a file that
     *   return array when included.
     *
     * @param array|string $options
     */
    function __construct($options = null)
    {
        if ($options !== null)
            $this->with(static::parseWith($options));
    }


    // Implement ipConfigurable:

    abstract function with(array $options, $throwException = false);

    /**
     * Load Build Options From Given Resource
     *
     * - usually it used in cases that we have to support
     *   more than once configure situation
     *   [code:]
     *     Configurable->with(Configurable::withOf(path\to\file.conf))
     *   [code]
     *
     *
     * @param array|string $optionsRes Array Options Or Path To Array Included File
     *
     * @throws \InvalidArgumentException if resource not supported
     * @return array
     */
    final static function parseWith($optionsRes, array $_ = null)
    {
        if (is_string($optionsRes)) {
            if (! file_exists($optionsRes) )
                throw new \InvalidArgumentException(sprintf(
                    'Map file "%s" provided does not exist.',
                    $optionsRes
                ));

            $optionsRes = include $optionsRes;
        }

        if (! is_array($optionsRes) )
            throw new \InvalidArgumentException(sprintf(
                'Resource must be an array, given: (%s).'
                , var_export($optionsRes, true)
            ));

        return $optionsRes;
    }
}
