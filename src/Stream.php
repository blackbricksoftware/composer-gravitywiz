<?php declare( strict_types = 1 );

namespace BlackBrickSoftware\Composer\GravityWiz;

use PiotrPress\Streamer;

class Stream extends Streamer {

    public static array $product_lookup;

    static public function register(string $protocol, int $flags = 0) : bool
    {
        if (\in_array($protocol, \stream_get_wrappers())) {
            self::unregister($protocol);
        }
        return parent::register($protocol, $flags);
    }

    public function stream_open(string $path, string $mode, int $options, ?string &$opened_path) : bool
    {

        if (!isset(static::$product_lookup)) {
            $this->populate_product_ids();
        }

        $host = \parse_url($path, \PHP_URL_HOST);
        $license_id = \parse_url($path, \PHP_URL_USER);
        $license_key = \parse_url($path, \PHP_URL_PASS);
        $package_name = \basename($path);
        $scheme = \parse_url($path, \PHP_URL_SCHEME);

        if ('packages.json' === \substr($path, -strlen('packages.json')))  {
            self::$data[$path] = \json_encode([
                'metadata-url' => '/%package%',
                'available-package-patterns' => [
                    $scheme . '/*',
                ],
            ]);
            return parent::stream_open($path, $mode, $options, $opened_path);
        }

        // https://gravitywiz.com/documentation/can-i-download-perks-via-an-api/

        if (\array_key_exists($package_name, static::$product_lookup)) {

            $product = static::$product_lookup[$package_name];
            $package_url_format = $product['package'];

            // e.g. https://gravitywiz.com/gwapi/v2?edd_action=download_product&product_id=630292&url=%URL%&license_id=%LICENSE_ID%&license_hash=%LICENSE_HASH%&legacy=0
            $package_url = \str_replace(
                [
                    '%URL%',
                    '%LICENSE_ID%',
                    '%LICENSE_HASH%',
                ],
                [
                    \urlencode('http://localhost'),
                    $license_id,
                    md5($license_key),
                ],
                $package_url_format
            );

            $full_package_name = "$scheme/$package_name";
            $version = $product['version'];

            self::$data[$path] = \json_encode([
                'packages' => [
                    $full_package_name => [
                        $version => [
                            'name' => $full_package_name,
                            'version' => $version,
                            'type' => 'wordpress-plugin',
                            'dist' => [
                                'type' => 'zip',
                                'url' => $package_url
                            ]
                        ]
                    ]
                ]
            ]);
        } else {
            self::$data[$path] = \json_encode([]);
        }

        return parent::stream_open($path, $mode, $options, $opened_path);
    }

    protected function populate_product_ids(): void
    {
        $productsJson = \file_get_contents('https://gravitywiz.com/gwapi/v2/?edd_action=get_products');

        // Will throw on error
        $products = \json_decode($productsJson, true, 512, JSON_THROW_ON_ERROR);
        
        foreach ($products as $product) {
            static::$product_lookup[$product['slug']] = $product;
        }
    }
}