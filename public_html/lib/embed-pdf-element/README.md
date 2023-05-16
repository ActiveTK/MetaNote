# `<embed-pdf>`

`embed-pdf` is a WebComponent for embedding PDF files in web pages using pdf.js.

- [library](https://deno.land/x/embed_pdf)
- [document](https://deno.land/x/embed_pdf/mod.js?s=EmbedPdf)
- [demo](https://embed-pdf-element-demo.deno.dev/_tools/test.html)

## why did you make this?

Embedding PDFs on mobile devices is a bit of a pain, as indicated in the
[Stack Overflow answer](https://stackoverflow.com/questions/36382249/embed-pdf-in-mobile-browsers).

- If iframe is used, it embeds fine on PC, but not on mobile.
- If you use pdf.js it works on mobile devices, but you have to download the zip
  file, unzip it, host it on your own server...manual versioning is a pain.
- I tried using Google Docs viewer, but it becomes unstable when multiple PDFs
  are embedded in one page.
- I've tried looking for existing web components that can use pdf.js, but they
  don't seem to be very actively maintained. They didn't seem to be an option
  because they required you to download the files yourself or depended on the
  old WebComponent specification.

**This library provides a WebComponent that is delivered by a CDN and can be
activated by inserting a single line of script tag. It also uses pdf.js
internally to allow embedding of PDFs on both PC and mobile devices.** This
library greatly simplified the process of embedding PDFs in HTML.

## Usage

First, insert a script tag into your HTML. Next, place the `<embed-pdf>` tag.
Finally, set the file path in the `src` attribute of the `<embed-pdf>` tag.

```html
<script src="https://deno.land/x/embed_pdf@$MODULE_VERSION/mod.js" type="module"></script>
<embed-pdf src="./path/to/file.pdf"></embed-pdf>
```

By default, this library uses the browser's built-in PDF embedding support to
display PDFs. Switch to rendering using pdf.js only if the built-in PDF
rendering is not available. This will keep the download size to a minimum.

If you want to see how this works, check out the
[demo page](https://embed-pdf-element-demo.deno.dev/_tools/test.html)!

![screenshot](./_tools/screenshot.png)

By default, this library uses pdf.js in the [vendor directory](./vendor/) to
render PDFs. Alternatively, to use the latest version of pdf.js directly from
the official site, set it using JavaScript as follows:

```js
import { EmbedPdf } from "https://deno.land/x/embed_pdf@$MODULE_VERSION/mod.js";

// Specify the path to viewer.html. The default URL is https://deno.land/x/embed_pdf@$MODULE_VERSION/vendor/pdfjs/web/viewer.html .
EmbedPdf.viewerUrl = "https://mozilla.github.io/pdf.js/web/viewer.html";
```

## styling

You can specify width and height using CSS.

```css
embed-pdf {
  width: 500px; /* default is 300px. */
  height: 250px; /* default is 150px. */
  margin: 0 auto;
}
```
