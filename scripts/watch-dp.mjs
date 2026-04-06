#!/usr/bin/env node
/**
 * watch-dp.mjs
 * sass --watch を起動しつつ、dp-style.css が更新されるたびに
 * plugins/swell-dp/assets/css/dp-style.css にコピーする
 */

import { spawn } from 'child_process';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const root = path.resolve(__dirname, '..');

const src  = path.join(root, 'dp-style.css');
const dest = path.resolve(root, '../../plugins/swell-dp/assets/css/dp-style.css');

// sass --watch を起動
const sass = spawn(
  'npx',
  ['sass', '--watch', '--poll', '--no-source-map',
    'scss/style.scss:style.css',
    'scss/dp-style.scss:dp-style.css'],
  { cwd: root, stdio: 'inherit', shell: true }
);

// dp-style.css を 500ms 間隔でポーリング監視
fs.watchFile(src, { interval: 500 }, (curr, prev) => {
  if (curr.mtime > prev.mtime) {
    try {
      fs.copyFileSync(src, dest);
      console.log(`[sync] dp-style.css → plugin  (${new Date().toLocaleTimeString()})`);
    } catch (e) {
      console.error('[sync] コピー失敗:', e.message);
    }
  }
});

process.on('SIGINT', () => {
  fs.unwatchFile(src);
  sass.kill('SIGINT');
  process.exit(0);
});
