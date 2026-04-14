# Car Showroom Instructions

## Workspace

- The main application lives in `src/`. Run Laravel, Composer, npm, Pint, PHPStan, and tests from `src/`.
- Files outside `src/` are mostly repo tooling, docs, and infrastructure. Do not move application code out of `src/` unless explicitly requested.
- Follow the existing project standards in `docs/coding-standards.md`, `src/pint.json`, `src/.editorconfig`, `.husky/pre-commit`, and `src/composer.json`.

## UI Reuse First

- Before creating or restyling any UI, inspect the existing BoxCar template source at `C:\Users\minhd\Desktop\tailieuCars\cars dealer`.
- Prefer reusing existing sections, elements, classes, JS hooks, and assets from that template instead of inventing new HTML, CSS, or JS.
- Search for reusable UI in this order:
  1. Existing project views and partials in `src/resources/views`
  2. Existing theme assets already imported in `src/public/boxcar`
  3. Template source files in `C:\Users\minhd\Desktop\tailieuCars\cars dealer`
- For small UI pieces, check `ui-elements.html` first.
- For page and section layouts, check the closest matching template pages first, especially `inventory-*.html`, `inventory-page-single*.html`, `contact.html`, `about.html`, `dashboard.html`, `dealer*.html`, `login.html`, and `team*.html`.
- Port only the needed section into Blade. Do not copy entire template pages with unrelated markup, dead sections, or unused scripts.
- Use the template directory as a reference source. Prefer assets that already exist under `src/public/boxcar` before copying anything new.
- Only add new CSS, JS, or markup when no existing project partial or BoxCar template element can cover the need with a small adaptation.
- If no suitable template element exists, make the smallest possible extension and keep it consistent with the current BoxCar look and structure.
- Do not switch client or admin pages to a new UI approach such as Tailwind-heavy markup, Alpine, React, or a different component library unless explicitly requested.

## Frontend Conventions

- Client-facing pages should stay aligned with the existing BoxCar theme loaded by `src/resources/views/client/layouts/*.blade.php`.
- Prefer existing Blade partials under `src/resources/views/client/partials` before creating new partials.
- Keep custom inline `<style>` and `<script>` blocks small and page-specific. If styling or behavior becomes shared or substantial, move it into the existing asset and partial structure.
- For admin pages, preserve the current admin shell and patterns built around `admin.layouts.app`, `admin.layouts.livewire`, `src/public/boxcar/css/admin.css`, and existing `admin-*` classes.
- For admin modules with heavy database interaction, dynamic filters, pagination, inline validation, modal workflows, or repeated CRUD actions, prefer Livewire over plain controller + Blade pages.
- Plain Blade admin pages are still acceptable for simple read-only screens, straightforward detail pages, or one-shot forms that do not need rich stateful interaction.
- Reuse existing admin table, toolbar, form, modal, and feedback patterns before introducing new structures.
- Keep asset paths consistent with `asset('boxcar/...')`.
- Reuse existing icons, placeholders, and BoxCar images before adding new assets.

## Architecture

- Keep routes thin. Do not place business logic in `src/routes/*.php`.
- Keep controllers thin. Use controllers for request entry, orchestration, and response shaping.
- Keep Livewire components focused on UI state, filters, validation, events, and calling services.
- Put non-trivial business rules, multi-step write flows, and transaction boundaries in `src/app/Services`.
- Prefer explicit, domain-oriented naming over generic helper names.
- Prefer typed properties, typed parameters, typed return values, and early returns.
- Avoid deep nesting and avoid spreading the same rule across multiple layers when one clear owner is enough.
- Do not introduce Repository, DTO, Action, Presenter, or custom abstraction layers unless the touched module already uses that pattern or the user explicitly asks for it.
- Preserve the current folder structure and naming in the touched module. Do not reorganize unrelated files.

## Laravel And Livewire Patterns

- Match the existing file pairing between `src/app/Livewire/...` and `src/resources/views/livewire/...`.
- In admin catalog modules, preserve the current pattern: `Page.php` as the workspace container and `*/Manager.php` for per-entity CRUD panels.
- In this project, "using Livewire" still means rendering through Blade view files under `src/resources/views/livewire/...`. Do not interpret "avoid Blade" as a reason to avoid Livewire.
- For new admin CRUD-heavy modules, default to Livewire unless the existing module clearly follows a simpler controller + Blade pattern and does not need rich interaction.
- For HTTP controller entry points, prefer Form Requests when validation is non-trivial or reused.
- For Livewire, follow the local module pattern already in place. Extract shared validation or normalization only when it is clearly reused across flows.
- Use Eloquent relationships and eager loading intentionally to avoid N+1 queries.
- Wrap multi-table writes and destructive multi-step updates in transactions.
- Prefer server-rendered Blade and Livewire behavior over custom frontend JS for core CRUD flows.
- Reuse existing events, route names, model conventions, and database naming unless explicitly asked to change them.

## Maintainability

- Keep diffs minimal and avoid formatting churn in untouched files.
- Reuse existing partials before creating new ones. Extract a new partial only when a section is reused or large enough to justify the split.
- Remove dead code, unused imports, stale comments, and obsolete branches when touching an area.
- Do not mass-rewrite copy, labels, or messages outside the requested scope.
- When adapting template markup, connect it to real routes, data, validation, and permissions used by the application.

## Verification

- After PHP, Blade, or Livewire changes, run `composer lint` from `src/`.
- After non-trivial PHP logic, query, validation, or service changes, also run `composer stan`.
- After behavioral changes that can be covered by tests, also run `composer test`.
- If a relevant check cannot be run, say so clearly in the final response.
