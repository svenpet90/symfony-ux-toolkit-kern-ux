# Component kit based on KERN-UX 2.6 for Symfony Toolkit

## Requirements

- [Symfony UX Toolkit](https://ux.symfony.com/toolkit)
- [Installation](INSTALL.md)

## Component installation

### Install the components you need...

eg. for Button:

```sh
php bin/console ux:install button --kit https://github.com/svenpet90/symfony-ux-toolkit-kern-ux
```

... and find them in your templates/components folder!

### Usage

```twig
{# Freshly installed components are ready to use! #}
<twig:Button as="a" variant="primary" href="https://symfony.com" target="_blank">
    Visit symfony.com
</twig:Button>
```

For component usage examples, see every components' examples folder.

> Note: The package is in progress, KERN-UX components might still be missing and bugs might happen.