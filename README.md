> Note: The package is in progress. It tracks KERN-UX 2.8; components may still be added and bugs might happen.

# Component kit based on KERN-UX 2.8 for Symfony Toolkit

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

## Available components

### Layout
`Container` · `Row` · `Col` · `Divider`

### Typography
`Heading` · `Title` · `Preline` · `Subline` · `Label` · `Body` · `List` · `Link` · `Hgroup`

### Content & feedback
`Alert` · `Badge` · `Card` · `Accordion` · `Details` · `Dialog` · `Dropdown` · `Loader` · `Progress` · `Icon`

### Data
`Table` (`Table:Head`, `Table:Body`, `Table:Footer`, `Table:Row`, `Table:Header`, `Table:Cell`) · `DescriptionList` (`DescriptionList:Item`) · `Summary` (`Summary:Group`) · `TaskList` (`TaskList:Item`)

### Forms
`Text` · `Email` · `Tel` · `Url` · `Number` · `Password` · `Date` · `Textarea` · `Select` · `Checkbox` · `Radio` · `File` · `Fieldset` · `InputGroup` (`InputGroup:Text`)

### Government header
`Kopfzeile` (rendered via the KERN `kern-kopfzeile` web component — include its script once per page; see the component's example)

## Not (yet) available

These KERN-UX components are marked "in Bearbeitung" (under development) upstream or are not published as open source, so they are intentionally not part of this kit yet:

- **Tabs**, **Header**, **Notification Banner**, **Search** — under development in KERN-UX 2.8.
- **Bildwortmarke** — a sovereign emblem that KERN does not publish under an open-source license or as source code.