#!/usr/bin/env python3
"""
dp-merge.py  --  inline-css を WP post_content に マージする
Usage: python3 scripts/dp-merge.py {slug}

入力:  /tmp/dp-current-content.txt  (wp-cli で取得した現在のpost_content)
       output/inline-css/{slug}.html
出力:  /tmp/dp-new-content.txt       (wp-cli で更新するpost_content)

注意:
  - inline-css ファイル先頭の dp-deps コメントはWP側に含めない
  - 既存の wp:html ブロック（残骸dp-depsを含む）を検出して置換する
"""
import re, sys

slug = sys.argv[1]
inline_css_path = f"output/inline-css/{slug}.html"

with open("/tmp/dp-current-content.txt", "r") as f:
    current = f.read()

with open(inline_css_path, "r") as f:
    new_block = f.read().strip()

# dp-depsコメントを除去（WP post_contentには含めない）
new_block = re.sub(r'^<!-- dp-deps:.*?-->\n?', '', new_block, flags=re.MULTILINE).strip()

# 末尾の wp:html ブロック（前の残骸 dp-deps も含む）を置換または追加
pattern = r'\n*(<!-- dp-deps:.*?-->\n*)*<!-- wp:html -->.*?<!-- /wp:html -->\s*$'
if re.search(pattern, current, re.DOTALL):
    updated = re.sub(pattern, lambda m: "\n\n" + new_block, current, flags=re.DOTALL)
else:
    updated = current.rstrip() + "\n\n" + new_block + "\n"

with open("/tmp/dp-new-content.txt", "w") as f:
    f.write(updated)

print("merge complete")
