import fs from "node:fs";
import path from "node:path";
import process from "node:process";

const themeDir = process.cwd();
const inputScss = path.join(themeDir, "scss", "style.scss");
const outputCss = path.join(themeDir, "style.css");
const functionsPhp = path.join(themeDir, "functions.php");

function exists(p) {
  try {
    fs.accessSync(p, fs.constants.F_OK);
    return true;
  } catch {
    return false;
  }
}

function mtime(p) {
  try {
    return fs.statSync(p).mtime.toISOString();
  } catch {
    return null;
  }
}

function log(title, value) {
  // eslint-disable-next-line no-console
  console.log(`${title}: ${value}`);
}

function warn(msg) {
  // eslint-disable-next-line no-console
  console.warn(`WARN: ${msg}`);
}

function fail(msg) {
  // eslint-disable-next-line no-console
  console.error(`ERROR: ${msg}`);
  process.exitCode = 1;
}

log("Theme dir", themeDir);
log("Input SCSS", inputScss);
log("Output CSS", outputCss);

if (!exists(inputScss)) fail("Missing scss/style.scss");
if (!exists(functionsPhp)) warn("functions.php not found (skip enqueue check)");

if (exists(functionsPhp)) {
  const php = fs.readFileSync(functionsPhp, "utf8");
  const isWrongEnqueue =
    php.includes("'/scss/style.css'") || php.includes('"/scss/style.css"');
  const isRightEnqueue =
    php.includes("'/style.css'") || php.includes('"/style.css"');

  if (isWrongEnqueue) {
    fail("functions.php enqueues /scss/style.css (should enqueue /style.css)");
  } else if (!isRightEnqueue) {
    warn("Could not confirm /style.css enqueue in functions.php");
  } else {
    log("Enqueue", "functions.php looks OK (/style.css)");
  }
}

let sass;
try {
  sass = await import("sass");
  log("Sass", sass.info ?? "OK");
} catch (e) {
  fail(
    "Cannot import 'sass'. Run: npm install (in this theme directory) to install dependencies."
  );
  // eslint-disable-next-line no-console
  console.error(e);
  process.exit(1);
}

try {
  const result = sass.compile(inputScss, { sourceMap: false });
  fs.writeFileSync(outputCss, result.css);
  log("Compile", "OK (wrote style.css)");
} catch (e) {
  fail("Compile failed. Fix the SCSS error shown below.");
  // eslint-disable-next-line no-console
  console.error(e);
}

log("mtime scss/style.scss", mtime(inputScss) ?? "N/A");
log("mtime style.css", mtime(outputCss) ?? "N/A");

