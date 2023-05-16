// @ts-check

/// <reference no-default-lib="true"/>
/// <reference lib="esnext" />
/// <reference lib="dom" />
/// <reference lib="dom.iterable" />

const style = `
:host {
  display: block;
  width: 300px;
  height: 150px;
  background-color: #ddd;
}
iframe {
  width: 100%;
  height: 100%;
  border: none;
}
.download-link {
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
}
.download-link a {
  background-color: royalblue;
  color: white;
  border-radius: 0.5em;
  padding: 1em;
  text-decoration: none;
  line-height: 100%;
}
.download-link a:hover {
  background-color: dodgerblue;
}
.download-link a::after {
  content: "💾";
  font-size: 1.2em;
  mergin: 0.2em;
  filter: brightness(2);
}
`;

/** @type {Record<string, Promise<string>>} */
const viewerHtmlCache = {};

/** @param {{ src: string | null }} options */
async function render({ src: fileSrc }) {
  const iframe = document.createElement("iframe");

  if (!fileSrc) {
    throw new Error("plese set `src` attribute to <embed-pdf> element.");
  }

  if (globalThis.navigator?.pdfViewerEnabled) {
    // use native iframe support
    iframe.src = fileSrc;
    return iframe;
  }

  try {
    const fileUrl = new URL(fileSrc, location.href);

    // cache pdfjs content
    viewerHtmlCache[EmbedPdf.viewerUrl] ??= (async () => {
      const res = await fetch(EmbedPdf.viewerUrl);
      return await res.text();
    })();
    const text = await viewerHtmlCache[EmbedPdf.viewerUrl];

    // inject script tag
    const html = text
      .replace(
        '<meta charset="utf-8">',
        // Sets the base path for assets loaded with relative paths from within viewer.html.
        `<meta charset="utf-8"><base href="${EmbedPdf.viewerUrl}">`,
      )
      .replace(
        '<script src="viewer.js"></script>',
        // Tells pdf.js which file to load. See also https://github.com/ayame113/embed-pdf-element/issues/1 .
        `<script src="viewer.js"></script>
        <script>PDFViewerApplicationOptions.set("defaultUrl", "${fileUrl}");</script>`,
      );

    const blob = new Blob([html], { type: "text/html" });
    iframe.src = URL.createObjectURL(blob);

    // show download link when loading error occurs
    iframe.addEventListener("load", () => {
      iframe.contentWindow?.addEventListener("unhandledrejection", () => {
        iframe.parentNode?.append(renderDownloadLink(fileSrc));
        iframe.remove();
      });
    });

    return iframe;
  } catch (error) {
    console.error(error);

    return renderDownloadLink(fileSrc);
  }
}

/** @param {string} fileSrc */
function renderDownloadLink(fileSrc) {
  // if error, show download link.
  const wrapper = document.createElement("div");
  const downloadLink = document.createElement("a");
  downloadLink.href = fileSrc;
  downloadLink.target = "_brank";
  downloadLink.textContent = "Download PDF ";
  wrapper.append(downloadLink);
  wrapper.classList.add("download-link");
  return wrapper;
}

/**
 * An HTML element that can embed a pdf file.
 *
 * First, insert a script tag into your HTML. Next, place the `<embed-pdf>` tag.
 * Finally, set the file path in the src attribute of the `<embed-pdf>` tag.
 *
 * ```html
 * <script src="https://deno.land/x/embed_pdf@$MODULE_VERSION/mod.js" type="module"></script>
 * <embed-pdf src="./path/to/file.pdf"></embed-pdf>
 * ```
 *
 * ![screenshot](./_tools/screenshot.png)
 *
 * By default, this library uses pdf.js in the vendor directory to render PDFs.
 * Alternatively, to use the latest version of pdf.js directly from the official
 * site, set it using JavaScript as follows:
 *
 * ```js
 * import { EmbedPdf } from "https://deno.land/x/embed_pdf@$MODULE_VERSION/mod.js";
 *
 * // Specify the path to viewer.html. The default URL is https://deno.land/x/embed_pdf@$MODULE_VERSION/vendor/pdfjs/web/viewer.html .
EmbedPdf.viewerUrl = "https://mozilla.github.io/pdf.js/web/viewer.html";
 * ```
 */
export class EmbedPdf extends HTMLElement {
  static viewerUrl = new URL("./vendor/pdfjs/web/viewer.html", import.meta.url)
    .toString();
  static observedAttributes = ["src"];
  #shadowRoot;
  #isConnected = false;
  constructor() {
    super();
    this.#shadowRoot = this.attachShadow({ mode: "closed" });
    const styleSheet = new CSSStyleSheet();
    styleSheet.replace(style);
    this.#shadowRoot.adoptedStyleSheets = [styleSheet];
  }
  async connectedCallback() {
    this.#shadowRoot.replaceChildren(
      await render({ src: this.getAttribute("src") }),
    );
    this.#isConnected = true;
  }
  disconnectedCallback() {
    this.#isConnected = false;
    this.#shadowRoot.replaceChildren();
  }
  /**
   * @param {string} name
   * @param {string | null} _oldValue
   * @param {string | null} newValue
   */
  async attributeChangedCallback(name, _oldValue, newValue) {
    if (!this.#isConnected) {
      return;
    }
    if (name === "src") {
      this.#shadowRoot.replaceChildren(await render({ src: newValue }));
    }
  }
}
customElements.define("embed-pdf", EmbedPdf);
