#!/usr/bin/env python3
"""
git hooks インストールスクリプト
scripts/ 配下の hook ファイルを .git/hooks/ にコピーする。

使い方: python3 scripts/install-hooks.py
"""
import os
import shutil
import stat
from pathlib import Path

root = Path(__file__).parent.parent
hooks_dir = root / ".git" / "hooks"

HOOKS = {
    "pre-commit": root / "scripts" / "pre-commit.py",
}

for hook_name, src in HOOKS.items():
    dest = hooks_dir / hook_name
    shutil.copy2(src, dest)
    # 実行権限を付与
    current = stat.S_IMODE(os.stat(dest).st_mode)
    os.chmod(dest, current | stat.S_IXUSR | stat.S_IXGRP | stat.S_IXOTH)
    print(f"installed: {dest}")

print("hooks installed.")
