#!/usr/bin/env python3
"""
pre-commit hook: SCSSが変更されたとき、影響を受けるinline-cssが未更新なら警告してブロックする。

判定ロジック:
1. ステージされたSCSSの diff から「削除行（-）」に含まれるCSSクラス名を抽出
2. 純粋な追加のみ（- 行なし）の場合 → 既存ファイルへの影響なしとみなし通過
3. 削除・変更クラスがある場合 → 各inline-cssにそのクラスが含まれるか確認
4. 含まれる場合のみ「要更新」としてブロック

このファイルは scripts/pre-commit.py として管理されており、
.git/hooks/pre-commit からこのファイルを呼び出す形で使用する。

インストール: python3 scripts/install-hooks.py
スキップ: git commit --no-verify
"""
import re
import subprocess
import sys
from pathlib import Path


def main():
    root = Path(subprocess.check_output(
        ["git", "rev-parse", "--show-toplevel"], text=True
    ).strip())

    inline_dir = root / "output" / "inline-css"

    # ステージされたファイルを取得
    staged = subprocess.check_output(
        ["git", "diff", "--cached", "--name-only"], text=True
    ).splitlines()

    # 変更されたSCSSファイルを抽出（_c-*.scss / _p-*.scss）
    changed_scss = {
        Path(f).stem
        for f in staged
        if re.match(r"scss/(_c-|_p-).+\.scss$", f)
    }

    if not changed_scss:
        sys.exit(0)  # SCSS変更なし → 通過

    # -------------------------------------------------------
    # SCSSの削除行からクラス名を抽出（変更・削除されたセレクター）
    # 純粋な追加のみの場合は既存ファイルに影響しない
    # -------------------------------------------------------
    affected_selectors = set()
    for scss_stem in changed_scss:
        scss_path = f"scss/{scss_stem}.scss"
        try:
            diff = subprocess.check_output(
                ["git", "diff", "--cached", "--", scss_path],
                text=True, cwd=root
            )
        except subprocess.CalledProcessError:
            continue
        for line in diff.splitlines():
            if line.startswith('-') and not line.startswith('---'):
                for cls in re.findall(r'\.([a-zA-Z][\w-]*)', line):
                    affected_selectors.add(cls)

    if not affected_selectors:
        # 変更が純粋な追加のみ → 既存inline-cssへの影響なし
        print(f"[pre-commit] SCSS変更は純粋な追加のみ"
              f"（{', '.join(sorted(changed_scss))}）。既存inline-cssへの影響なし。")
        sys.exit(0)

    # -------------------------------------------------------
    # dp-deps を参照し、影響クラスを含むinline-cssを特定
    # -------------------------------------------------------
    stale_slugs = []
    staged_set = set(staged)

    for html_file in sorted(inline_dir.glob("*.html")):
        try:
            content = html_file.read_text(encoding="utf-8")
        except Exception:
            continue

        first_line = content.split("\n")[0]
        m = re.search(r"<!-- dp-deps:(.*?)-->", first_line)
        if not m:
            continue

        deps = m.group(1).strip().split()
        if not any(scss in deps for scss in changed_scss):
            continue

        slug = html_file.stem
        inline_rel = f"output/inline-css/{slug}.html"

        if inline_rel in staged_set:
            continue  # すでにステージ済み → OK

        # inline-css の内容に影響クラスが含まれるか確認
        # 部分一致を避けるため、クラス名の後に識別子文字（-_a-zA-Z0-9）が続かないことを確認
        # 例: .dp-solution-section が .dp-solution-section__img に誤マッチしないよう防止
        if any(re.search(r'\.' + re.escape(cls) + r'(?![a-zA-Z0-9_-])', content)
               for cls in affected_selectors):
            stale_slugs.append(slug)

    if not stale_slugs:
        sys.exit(0)  # 全て影響なしまたは更新済み → 通過

    # 警告表示
    print("\n" + "=" * 60)
    print("⚠️  pre-commit: inline-css 未更新を検出しました")
    print("=" * 60)
    print(f"\n変更されたSCSS: {', '.join(sorted(changed_scss))}")
    print(f"変更クラス: {', '.join(f'.{c}' for c in sorted(affected_selectors))}")
    print("\n以下のスラッグのinline-cssが影響を受けており、未更新です:\n")
    for slug in stale_slugs:
        print(f"  - {slug}")
    print(f"\n👉 /dp-regen {' '.join(sorted(changed_scss))} を実行してください")
    print("   スキップする場合: git commit --no-verify\n")
    print("=" * 60 + "\n")

    sys.exit(1)


if __name__ == "__main__":
    main()
