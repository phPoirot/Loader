<?php
namespace Poirot\Loader;

!class_exists('Poirot/Loader/aLoader', false)
    and require_once __DIR__.'/aLoader.php';
!trait_exists('Poirot\Loader\Traits\tLoaderAggregate', false)
    and require_once __DIR__.'/Traits/tLoaderAggregate.php';

use Poirot\Loader\Traits\tLoaderAggregate;

class LoaderAggregate
    extends aLoader
{
    use tLoaderAggregate;

    ## @see fixes/LoaderAggregate;
    ## Code Clone <begin> =================================================================
    /**
     * Build Object With Provided Options
     * > Setup Aggregate Loader
     *   Options:
     *  [
     *    'attach' => [new Loader(), $priority => new OtherLoader(), ['loader' => iLoader, 'priority' => $pr] ],
     *    Loader::class => [
     *       // Options
     *       'Poirot\AaResponder'  => [APP_DIR_VENDOR.'/poirot/action-responder/Poirot/AaResponder'],
     *       'Poirot\Application'  => [APP_DIR_VENDOR.'/poirot/application/Poirot/Application'],
     *    ],
     *    OtherLoader::class => [options..]
     *  ]
     *
     * @param array $options       Associated Array
     * @param bool $throwException Throw Exception On Wrong Option
     *
     * @throws \Exception
     * @return $this
     */
    function with(array $options, $throwException = false)
    {
        # Attach Loader:
        if (isset($options['attach'])) {
            $attach = $options['attach'];
            if(! is_array($attach) )
                $attach = array($attach);

            foreach($attach as $pr => $loader) {
                if (is_array($loader)) {
                    if ( !isset($loader['priority']) || !isset($loader['loader']) )
                        throw new \InvalidArgumentException(sprintf(
                            'Invalid Option Provided (%s).'
                            , var_export($loader, true)
                        ));

                    $pr     = $loader['priority'];
                    $loader = $loader['loader'];
                }

                $this->attach($loader, $pr);
            }

            unset($options['attach']);
        }

        # Set Loader Specific Config:
        foreach($options as $loader => $loaderOptions)
        {
            try{
                $loader = $this->loader($loader);
            } catch (\Exception $e) {
                if ($throwException) {
                    throw new \InvalidArgumentException(sprintf(
                        'Loader (%s) not attached.'
                        , $loader
                    ));
                }
            }

            if (! is_object($loader) )
                // Exception Rise And Catch!!
                continue;

            if (method_exists($loader, 'with')) {
                /** @var \Poirot\Std\Interfaces\Pact\ipConfigurable $loader */
                $loader->with( $loader::parseWith($loaderOptions) );
            }
        }

        return $this;
    }
    ## Code Clone <end> ===================================================================
}
