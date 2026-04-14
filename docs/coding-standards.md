# Coding Standards

This repository uses tooling-first conventions for all PHP code inside `src/`.

## Source of truth

- `src/pint.json`: automatic formatter for PHP / Blade / Livewire files.
- `src/.editorconfig`: whitespace, indentation, line endings, and final newline policy.
- `.husky/pre-commit` + `.lintstagedrc.json`: blocks commits until staged PHP files are formatted.

## Auto-enforced rules

These rules are enforced before commit for staged files matching `src/**/*.php`:

- Laravel code style via the `laravel` Pint preset.
- Four spaces for indentation.
- UTF-8 files with LF line endings.
- Final newline at end of file.
- No trailing whitespace.
- No unused imports.
- Alphabetically ordered imports.
- Single quotes when escaping is not required.
- Trailing commas in multiline arrays, argument lists, and similar multiline structures.
- Single spaces around string concatenation.

Generated PHP should not be formatted. `src/pint.json` excludes:

- `bootstrap/cache`
- `storage`

## Team conventions

These conventions are expected during review even when they are not fully auto-fixed:

- Keep controllers and Livewire components thin; move business logic into services or dedicated classes.
- Prefer typed properties, typed parameters, and typed return values.
- Prefer early returns over deep nesting.
- Prefer Form Requests or centralized validation rules for HTTP entry points.
- Remove dead code, unused branches, and outdated comments.
- Keep naming explicit and domain-oriented.

## Commands

Run these from `src/`:

```bash
composer lint
composer format
npm run lint
npm run format
```

## Commit gate

After installing dependencies in `src/`, run:

```bash
npm run prepare
```

This installs the Git hook in the repository root and ensures every `git commit` formats staged PHP files with Pint before the commit is created.
