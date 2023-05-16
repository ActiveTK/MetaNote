import { serve } from "https://deno.land/std@0.177.0/http/mod.ts";
import { serveDir } from "https://deno.land/std@0.177.0/http/file_server.ts";
serve((req) => serveDir(req));
console.log("Plese access to http://localhost:8000/_tools/test.html");
