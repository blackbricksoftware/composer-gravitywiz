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

        $composer->getRepositoryManager()->addRepository( $composer->getRepositoryManager()->createRepository(
            'composer',
            [
                'url' => "gravitywiz://$licenseId:$key@$host",
             ],
            'gravitywiz'
        ) );
    }

    public function deactivate( Composer $composer, IOInterface $io ) : void {}
    public function uninstall( Composer $composer, IOInterface $io ) : void {}
}