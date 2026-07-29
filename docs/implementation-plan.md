# Portfolio AI — Implementation Plan

Phased plan derived from `docs/project-srs.md`. Each phase must be fully completed and tested before starting the next. Update checkboxes as work lands.

## Status Legend

- [ ] Not started
- [x] Done

---

## Phase 0 — Project Foundation ✅

- [x] Fresh Laravel + React starter kit (Inertia v3, Fortify auth, Pest)
- [x] Landing page removed; `/` redirects to dashboard (guests → login)
- [x] System user seeder (`admin@portfolio-ai.test` / `password`)
- [x] Modern light/dark theme variables (violet + zinc palette)

## Phase 1 — Layout & Navigation ✅

- [x] Sidebar navigation: Dashboard, Portfolio, AI Search, Proposal Writer, Prompt Templates, History, Settings
- [x] Placeholder pages + named routes for each module (auth protected)
- [x] Feature tests for each page route

## Phase 2 — Portfolio CRUD

- [ ] `projects` migration (title, slug, thumbnail, description LONGTEXT, embedding nullable)
- [ ] `Project` model + factory + seeder
- [ ] `ProjectService`, `ProjectController` (resource), Form Requests
- [ ] Project list page (thumbnail, title, short description, image count, updated date, search by title)
- [ ] Create / Edit pages (title, thumbnail upload, description)
- [ ] Project details page (info, images, edit/delete/generate-embedding buttons)
- [ ] In Create , Edit and Detail pages the images should load big or thumnail with fear size so can quickly lookk whats going on
- [ ] Feature tests

## Phase 3 — Image Management

- [ ] `project_images` migration (project_id, title, image, sort_order)
- [ ] `ProjectImage` model + factory, relationship on `Project`
- [ ] Upload, delete, reorder, preview (unlimited images, each with title)
- [ ] Feature tests

## Phase 4 — WYSIWYG Editor

- [ ] TipTap editor integrated into create/edit pages
- [ ] Preload new projects with the SRS description template (section 7)
- [ ] Rendered rich description on details page

## Phase 5 — Embedding Generation

- [ ] Laravel AI SDK setup (isolated `EmbeddingService`)
- [ ] Queued embedding regeneration when title/description changes (includes image titles)
- [ ] Store embedding on `projects.embedding`
- [ ] "Generate Embedding" button on project details
- [ ] Tests (AI provider faked)

## Phase 6 — AI Search

- [ ] `AISearchService` (query embedding → cosine similarity → best matches)
- [ ] AI Search page (search box, example queries, results with title/score/reason/thumbnail/open button)
- [ ] Tests

## Phase 7 — Prompt Templates

- [ ] `prompt_templates` migration (name, prompt, is_default)
- [ ] CRUD + only one default enforced (`PromptTemplateService`)
- [ ] Tests

## Phase 8 — Proposal Writer

- [ ] `ProposalService` (portfolio search → top projects → template + job description → LLM)
- [ ] Page: job description textarea (left) / generated proposal (right)
- [ ] Tests (AI faked)

## Phase 9 — Prompt History

- [ ] `prompt_history` migration (job_description, used_prompt, generated_proposal)
- [ ] Auto-save on generation; view / reuse / delete
- [ ] Tests

## Phase 10 — Dashboard Polish

- [ ] Stat cards: projects, images, prompt templates, generated proposals
- [ ] Recent projects, recent searches
- [ ] Tests

---

## Architecture Notes

- Services hold business logic: `ProjectService`, `EmbeddingService`, `AISearchService`, `ProposalService`, `PromptTemplateService`. Controllers stay thin.
- AI providers must be swappable — no AI calls outside services.
- Only 5 tables: users, projects, project_images, prompt_templates, prompt_history.
- SRS targets PostgreSQL; local dev currently runs SQLite. Decide before Phase 5 (embeddings) whether to switch to Postgres (pgvector) or store embeddings as JSON with in-PHP cosine similarity.
