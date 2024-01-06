# Composer GravityWiz

This composer plugin enables installation of Gravity Perks WordPress plugins from Gravity Wiz. Base on Composer GravityForms (https://github.com/PiotrPress/composer-gravityforms).

## Example

```shell
$ composer require gravitywiz/gp-advanced-calculations:*
```

**NOTE:** Package name can be any GravityWiz Perk [slug](https://gravitywiz.com/gwapi/v2/?edd_action=get_products).

## Installation

1. Add the plugin as a composer requirement:

```shell
$ composer require blackbricksoftware/composer-gravitywiz
```

2. Allow the plugin execution:

```shell
$ composer config allow-plugins.blackbricksoftware/composer-gravitywiz true
```

3. Provide GravityWiz [license id & key](https://gravitywiz.com/documentation/can-i-download-perks-via-an-api/). There is no need to md5 hash the license key when setting this configuration.

> Navigating to Forms › Perks › Manage in the WordPress Dashboard. From the Account page, click Manage Sites. The license ID will appear in your URL. For example, this URL indicates the license ID is 123456.

```shell
$ composer config http-basic.gravitywiz.com <license id> <license_key>
```

4. Add `http://localhost` to the `Registered Sites` in your Gravity Wiz account.

## Usage

The Gravity Wiz Perks plugins have a type set to `wordpress-plugin` and can be installed in custom location using for example [Composer Installers](https://github.com/composer/installers): 

```json
{
  "require": {
    "gravitywiz/gp-advanced-calculations": "*",
    "gravitywiz/gwlimitchoices": "*",
    "composer/installers": "^2.0"
  },
  "config": {
    "allow-plugins": {
      "composer/installers": true
    }
  },
  "extra": {
    "installer-paths": {
      "wp-content/plugins/{$name}/": [
        "type:wordpress-plugin"
      ]
    }
  }
}
```

## Requirements

- PHP >= `7.4` version.
- Composer ^`2.0` version.

## License

[MIT](license.txt)