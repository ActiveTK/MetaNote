This directory contains files for external dependencies.

- `./pdfjs/`: Files downloaded from releases of
  [pdfjs](https://github.com/mozilla/pdf.js).

How to update: Go to the
[pdf.js download page](http://mozilla.github.io/pdf.js/) and copy-paste the
download URL into `/_tools/download.ts`. Then run `deno task download`.

Note that the license for pdf.js is Apache License 2.0.
