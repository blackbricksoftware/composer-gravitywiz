<?php declare( strict_types = 1 );

namespace BlackBrickSoftware\Composer\GravityWiz;

use Composer\Plugin\PluginInterface;
use Composer\Composer;
use Composer\IO\IOInterface;

class Plugin implements PluginInterface {
    public function activate( Composer $composer, IOInterface $io ) : void
    {
        $host = 'gravitywiz.com';

        $licenseId = $composer->getConfig()->get('http-basic')[$host]['username'] ?? '';
        $key = $composer->getConfig()->get('http-basic')[$host]['password'] ?? '';
        if (!$licenseId || !$key) {
            return;
        }

        Stream::register('gravitywiz');

        // https://gravitywiz.com/documentation/can-i-download-perks-via-an-api/
        // https://gravitywiz.com/gwapi/v2/?edd_action=get_products
        // e.g. https://gravitywiz.com/gwapi/v2?edd_action=download_product&product_id=736472&url=%URL%&license_id=%LICENSE_ID%&license_hash=%LICENSE_HASH%

        $composer->getRepositoryManager()->addRepository( $composer->getRepositoryManager()->createRepository(
            'composer',
            [
                'url' => "gravitywiz://:$key@$host",
                'options' => [
                    'gravitywiz' => [
                        // 'api' => 'https://$host/wp-content/plugins/gravitymanager/api.php?op=get_plugin&slug=$slug&key=$key'
                        'api' => 'https://gravitywiz.com/gwapi/v2?edd_action=download_product&product_id=$product_id&url=http%3A%2F%2Flocalhost&license_id=$license_id&license_hash=$license_hash'
                    ]
                ]
            ],
            'gravitywiz'
        ) );
    }

    public function deactivate( Composer $composer, IOInterface $io ) : void {}
    public function uninstall( Composer $composer, IOInterface $io ) : void {}
}