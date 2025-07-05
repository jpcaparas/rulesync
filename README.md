# Rulesync

Synchronize AI assistant rules across multiple platforms with ease.

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
3. Your rules are now synced across Claude, Cursor, Windsurf, Gemini, and GitHub Copilot!

## Commands

- `rulesync list` - Show available AI assistants
- `rulesync generate` - Generate rule files from rulesync.md
- `rulesync config` - View current configuration
- `rulesync disable <name>` - Disable specific AI assistant
- `rulesync enable <name>` - Enable specific AI assistant
- `rulesync base <path>` - Set base rules (URL or file path)

## Supported AI Assistants

- **Claude** → `CLAUDE.md`
- **Cursor** → `.cursorrules`
- **Windsurf** → `.windsurf/rules.md`
- **Gemini CLI** → `.gemini/instructions.md`
- **GitHub Copilot** → `.github/copilot-instructions.md`

## License

MIT