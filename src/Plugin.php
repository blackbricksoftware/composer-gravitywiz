<?php declare( strict_types = 1 );

namespace BlackBrickSoftware\Composer\GravityWiz;

use Composer\Plugin\PluginInterface;
use Composer\Composer;
use Composer\IO\IOInterface;

class Plugin implements PluginInterface {
    public function activate( Composer $composer, IOInterface $io ) : void {
        if ( ! $key = $composer->getConfig()->get( 'http-basic' )[ $host = 'gravitywiz.com' ][ 'password' ] ?? '' ) return;

        Stream::register( 'gravitywiz' );

        // https://gravitywiz.com/documentation/can-i-download-perks-via-an-api/
        // https://gravitywiz.com/gwapi/v2/?edd_action=get_products

        $composer->getRepositoryManager()->addRepository( $composer->getRepositoryManager()->createRepository(
            'composer',
            [
                'url' => "gravitywiz://:$key@$host",
                'options' => [
                    'gravityforms' => [
                        'api' => 'https://$host/wp-content/plugins/gravitymanager/api.php?op=get_plugin&slug=$slug&key=$key'
                    ]
                ]
            ],
            'gravitywiz'
        ) );
    }

    public function deactivate( Composer $composer, IOInterface $io ) : void {}
    public function uninstall( Composer $composer, IOInterface $io ) : void {}
}