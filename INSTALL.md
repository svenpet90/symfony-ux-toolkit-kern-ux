# Getting started

This kit provides ready-to-use and fully-customizable UI Twig components based on [KERN-UX 2.6](https://www.kern-ux.de/) components's **design**.

Please note that not every KERN-UX component is available in this kit, but we are working on it!

## Requirements

This kit requires KERN-UX 2.6 to work:

- Follow the [official KERN-UX documentation](https://www.kern-ux.de/design-system/erste-schritte/entwicklerinnen/einbinden-von-kern)
## Installation

Modify the file `assets/styles/app.css` with the following content:

```css
@import "@kern-ux/native/dist/kern.min.css";
@import "@kern-ux/native/dist/fonts/fira-sans.css";
```

If you prefer using CDN, add the following to your base template (e.g., `templates/base.html.twig`):

```twig
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@kern-ux/native/dist/kern.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@kern-ux/native/dist/fonts/fira-sans.css" />
```