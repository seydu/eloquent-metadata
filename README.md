# eloquent-metadata

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

Manage metadata for Eloquent models to make automation easier.

## Install

Via Composer

``` bash
$ composer require seydu/eloquent-metadata
```

## Usage

``` php
$skeleton = new Seydu\EloquentMetadata();
echo $skeleton->echoPhrase('Hello, League!');
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email saidou.gueye@gmail.com instead of using the issue tracker.

## Credits

- [Saidou Gueye][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/seydu/eloquent-metadata.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/seydu/eloquent-metadata/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/seydu/eloquent-metadata.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/seydu/eloquent-metadata.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/seydu/eloquent-metadata.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/seydu/eloquent-metadata
[link-travis]: https://travis-ci.org/seydu/eloquent-metadata
[link-scrutinizer]: https://scrutinizer-ci.com/g/seydu/eloquent-metadata/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/seydu/eloquent-metadata
[link-downloads]: https://packagist.org/packages/seydu/eloquent-metadata
[link-author]: https://github.com/seydu
[link-contributors]: ../../contributors
