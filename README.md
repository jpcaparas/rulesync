# Rulesync

<p align="center">
    <img src="https://github.com/user-attachments/assets/307c2333-d2a0-449d-a5a8-50919e66746d" width="250" />
</p>

Synchronise AI assistant rules across multiple platforms with ease.

https://github.com/user-attachments/assets/98604814-c0aa-450a-83a4-be522c1e4d72

## Installation

### As a Development Dependency

```bash
composer require --dev jpcaparas/rulesync
./vendor/bin/rulesync --help
```

### As a Global Tool

```bash
composer global require jpcaparas/rulesync
rulesync --help
```

## Quick Start

1. Create a `rulesync.md` file with your rules
2. Run `rulesync generate` to create all AI assistant rule files
3. Your rules are now synced across Claude, Cursor, Windsurf, Gemini CLI, GitHub Copilot, and Junie!

## Commands

- `rulesync rules:list` - Show available AI assistants
- `rulesync generate` - Generate rule files from rulesync.md
- `rulesync config` - View current configuration
- `rulesync disable <name>` - Disable specific AI assistant
- `rulesync enable <name>` - Enable specific AI assistant
- `rulesync base <path>` - Set base rules (URL or file path)

## Supported AI Assistants

- **Claude** → `CLAUDE.md`
- **Cursor** → `.cursorrules`
- **Gemini CLI** → `GEMINI.md`
- **GitHub Copilot** → `.github/copilot-instructions.md`
- **Junie** → `.junie/guidelines.md`
- **Windsurf** → `.windsurfrules`

## FAQ

### Rule Augmentation

**Q: How does local and global rule augmentation work?**

A: Rulesync automatically detects whether you're in a local project (with `composer.json`) or global context. When both local (`./rulesync.md`) and global (`~/.config/rulesync/rulesync.md`) rule files exist, the system prompts you to:
- **Combine both files**: Local rules first, then global rules separated by `---`
- **Use local only**: Ignore global rules entirely

Your preference is saved for future `generate` calls, but can be changed anytime.

**Q: Can I disable certain AI assistants?**

A: Yes! Use these commands:
- `rulesync disable <name>` - Disable specific AI assistant (e.g., `rulesync disable claude`)
- `rulesync enable <name>` - Re-enable a disabled assistant
- `rulesync rules:list` - View all assistants and their enabled/disabled status

Disabled assistants won't have their rule files generated during `rulesync generate`.

**Q: How does overwrite protection work?**

A: By default, Rulesync protects existing rule files by:
- Comparing content using MD5 hashing
- Prompting for confirmation when files exist and differ
- Skipping files that already have identical content

You can override this behaviour with:
- `--overwrite` - Force overwrite existing files
- `--force` - Skip all prompts and force generation

**Q: Can I use base rules from external sources?**

A: Yes! The base rules system allows you to:
- Set base rules from URLs: `rulesync base https://example.com/rules.md`
- Set base rules from local files: `rulesync base ./shared-rules.md`
- Disable base rules: `rulesync base --disable`
- View current base rules: `rulesync base`

Base rules are appended to your final rule files after local/global augmentation.

## Building Standalone Executable

You can build Rulesync as a standalone PHAR executable that doesn't require PHP or Composer on the target system.

### Prerequisites

Install Box globally:
```bash
composer global require humbug/box
```

### Build Commands

**Basic build:**
```bash
php rulesync build
```

**Build with specific version:**
```bash
php rulesync build 1.0.0
```

**Create full release (with tests):**
```bash
php rulesync release 1.0.0
```

**Build with verbose output (for debugging):**
```bash
php rulesync build 1.0.0 --verbose
```

### Output

The standalone executable is created at `builds/rulesync` and includes all dependencies. You can distribute this single file without requiring users to install PHP, Composer, or any dependencies.

**Test the build:**
```bash
./builds/rulesync --version
./builds/rulesync --help
```

### Notes

- The version argument updates the version in `config/app.php` before building
- Use `--verbose` flag to see detailed Box compilation output for debugging
- The build process includes all dependencies (including dev dependencies) in the final PHAR
- The `release` command runs tests, builds the PHAR, but no longer creates a tarball

## License

MIT
