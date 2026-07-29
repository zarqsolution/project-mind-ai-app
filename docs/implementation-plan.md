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

## Phase 2 — Portfolio CRUD ✅

- [x] `projects` migration (title, slug, thumbnail, description LONGTEXT, embedding nullable)
- [x] `Project` model + factory + seeder
- [x] `ProjectService`, `ProjectController` (resource), Form Requests
- [x] Project list page (thumbnail, title, short description, updated date, search by title) — image count arrives with Phase 3
- [x] Create / Edit pages (title, thumbnail upload with live preview, description preloaded with template)
- [x] Project details page (info, edit/delete buttons; images + generate-embedding buttons arrive in Phases 3/5)
- [x] In Create, Edit and Detail pages the images load big (aspect-video previews, large detail hero) for a quick look
- [x] Feature tests (`ProjectCrudTest` — 12 tests covering list, search, create, slug uniqueness, update, thumbnail replacement, delete, guests)
- [x] Consistent page padding moved into the app sidebar layout so every module renders correctly

## Phase 3 — Image Management ✅

- [x] `project_images` migration (project_id, title, image, sort_order, cascade delete)
- [x] `ProjectImage` model + factory, `images()` relationship on `Project` (ordered by sort_order)
- [x] Upload (dialog with title + live preview), delete (confirm dialog), reorder (up/down), big aspect-video previews on the detail page
- [x] Image count badge on the project list cards
- [x] Deleting a project also removes its image files from storage
- [x] Feature tests (`ProjectImageTest` — 10 tests covering upload, append order, validation, ordered display, reorder, delete, scoped bindings, cascade, count, guests)
- [x] Larastan now passes clean (fixed factory faker method, typed service payloads, storage return types, starter-kit `withTwoFactor` stub)

## Phase 4 — WYSIWYG Editor ✅

- [x] TipTap editor integrated into create/edit pages (`RichTextEditor` component: toolbar with bold/italic/strike/code, H1–H3, lists, blockquote, code block, hr, undo/redo; hidden input keeps Inertia `<Form>` serialization working)
- [x] Descriptions now stored as HTML; SRS template (section 7) converted to HTML and preloaded on create
- [x] Rendered rich description on details page via shared `.rich-text` styles (app.css); list excerpt strips HTML tags
- [x] Factory generates HTML descriptions; template test updated; full suite + Larastan + tsc + build all green

## Phase 5 — Embedding Generation ✅

- [x] Laravel AI SDK (`laravel/ai`) installed; provider config-driven (`ai.default_for_embeddings`, defaults to OpenAI — set `OPENAI_API_KEY`; Anthropic does not support embeddings)
- [x] Isolated `EmbeddingService` (builds input from title + plain-text description + image titles, stores vector)
- [x] Queued `GenerateProjectEmbedding` job dispatched on create, title/description change, and image add/delete
- [x] Embedding stored on `projects.embedding` (JSON, hidden from serialization; `has_embedding` appended)
- [x] "Generate Embedding" button + status badge on project details
- [x] Feature tests (`ProjectEmbeddingTest` — 8 tests, `Embeddings::fake()` / `Queue::fake()`)

## Phase 6 — AI Search ✅

- [x] `AISearchService` (query embedding → cosine similarity → top 5 matches with rounded score + keyword-based reason, semantic fallback message)
- [x] `AISearchController@index` (thin controller; `query` request param, `results` null until a query is submitted)
- [x] AI Search page (search box with spinner, SRS example query chips, result cards with title / % match badge / reason / thumbnail / Open Project button)
- [x] Feature tests (`AISearchTest` — 8 tests using deterministic `Embeddings::fake()` vectors: ranking, exclusion of unembedded projects, top-5 limit, reasons, hidden embeddings, guests)

## Phase 7 — Prompt Templates ✅

- [x] `prompt_templates` migration (name, prompt, is_default) + `PromptTemplate` model/factory (`default()` state)
- [x] `PromptTemplateService`: single default enforced — first template auto-defaults, marking default unsets others, deleting the default promotes the most recently updated template
- [x] Resource controller (index/store/update/destroy) + Form Requests, thin controller with toasts
- [x] Prompt Templates page: card list (default first, Default badge), create/edit dialogs (name, prompt, set-as-default checkbox), Make Default quick action, delete confirmation
- [x] Feature tests (`PromptTemplateTest` — 10 tests covering listing order, CRUD, default rules, validation, guests)

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
