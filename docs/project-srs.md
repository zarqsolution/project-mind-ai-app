# Portfolio AI
## Software Requirements Specification (SRS)

Version: 1.0
Status: MVP
Framework: Laravel
Frontend: React + Inertia.js
Database: PostgreSQL
Editor: TipTap (or another modern WYSIWYG editor)
AI: Laravel AI SDK

---

# 1. Project Overview

Portfolio AI is a personal developer portfolio management application.

The purpose of this application is to maintain all software development projects in one place and allow AI to search them semantically.

Instead of maintaining dozens of normalized tables (features, technologies, integrations, etc.), every project will contain a rich description written using a predefined template.

The description will be converted into embeddings using Laravel AI SDK so AI can quickly find the most relevant projects.

The second module is an AI Proposal Writer that uses the portfolio knowledge to generate personalized Upwork proposals.

The system is intentionally simple.

---

# 2. Goals

The application should allow me to:

- Store all software projects
- Upload screenshots
- Give every screenshot a title
- Search projects using AI
- Paste an Upwork job description
- Generate a proposal
- Reuse editable prompt templates
- Maintain prompt history

The application is only for personal use.

---

# 3. Design Principles

Always prefer simplicity over normalization.

Avoid unnecessary tables.

Project descriptions should contain all project information.

AI should understand the project through embeddings instead of relational data.

Adding a new project should take less than five minutes.

---

# 4. Tech Stack

Backend

- Laravel 12
- PHP 8.4+

Frontend

- React
- Inertia.js
- Tailwind CSS
- shadcn/ui

Database

- PostgreSQL

Storage

- Laravel Storage

Editor

- TipTap

AI

- Laravel AI SDK

Queues

- Laravel Queue

---

# 5. Modules

## Module 1

Portfolio

## Module 2

AI Search

## Module 3

Proposal Writer

## Module 4

Settings

---

# 6. Database

Only use these tables.

## users

Default Laravel users table.

---

## projects

Fields

id

title

slug

thumbnail

description (LONGTEXT)

embedding (nullable)

created_at

updated_at

---

## project_images

id

project_id

title

image

sort_order

created_at

updated_at

---

## prompt_templates

id

name

prompt

is_default

created_at

updated_at

---

## prompt_history

id

job_description

used_prompt

generated_proposal

created_at

---

No additional tables unless absolutely required.

---

# 7. Project Description Template

When creating a new project, preload the editor with this template.

# Overview

Describe the project.

---

# Client

---

# Problem

---

# Solution

---

# My Role

---

# Technologies

---

# Features

---

# Integrations

---

# Challenges

---

# Performance Optimizations

---

# Screens

Describe important screenshots.

---

# Proposal Notes

Mention what AI should highlight in proposals.

---

# Keywords

---

Users can freely modify this template.

No validation is required.

---

# 8. Portfolio Module

## Project List

Display

- Thumbnail
- Title
- Short Description
- Image Count
- Updated Date

Features

- Search by title
- Create
- Edit
- Delete

---

## Create Project

Fields

Title

Thumbnail

Rich Description

Save

---

## Project Details

Display

Project information

Images

Buttons

Edit

Delete

Generate Embedding

---

# 9. Images

Each project can contain unlimited images.

Each image contains

Title

Image

Sort Order

Features

Upload

Delete

Reorder

Preview

---

# 10. AI Embeddings

Whenever

Project Description changes

OR

Project Title changes

Regenerate embedding.

Embedding should contain

Project title

Description

Image titles

Store embedding in PostgreSQL.

Implementation details may change later, so keep embedding generation isolated in a service class.

---

# 11. AI Search

Create an AI Search page.

Search box

Example queries

Show me logistics projects.

Projects with OCR.

Laravel APIs.

Healthcare.

Stripe integration.

Marketplace.

Redis queues.

Search process

1. Generate embedding for query.

2. Compare against project embeddings.

3. Return best matching projects.

Each result should display

Project title

Similarity score

Reason

Thumbnail

Button

Open Project

---

# 12. Proposal Writer

Page Layout

Left

Job Description textarea

Right

Generated Proposal

---

Workflow

User pastes a job description.

↓

System searches portfolio.

↓

Top matching projects are selected.

↓

Prompt template is loaded.

↓

LLM receives

Prompt Template

Job Description

Relevant Projects

↓

Proposal generated.

---

# 13. Prompt Templates

CRUD

Fields

Name

Prompt

Default

Only one prompt may be default.

Prompt should be editable.

---

# 14. Prompt History

Store

Job Description

Prompt

Generated Proposal

Created Date

Allow

View

Reuse

Delete

---

# 15. Settings

Future AI configuration.

Fields

Model

Temperature

Max Tokens

Embedding Model

API Keys

These settings should be isolated for future implementation.

---

# 16. UI

Modern minimal design.

Use shadcn/ui.

Responsive.

Sidebar navigation.

Pages

Dashboard

Portfolio

AI Search

Proposal Writer

Prompt Templates

History

Settings

---

# 17. Dashboard

Cards

Projects

Images

Prompt Templates

Generated Proposals

Recent Projects

Recent Searches

---

# 18. Services

Keep business logic outside controllers.

Suggested services

ProjectService

EmbeddingService

AISearchService

ProposalService

PromptTemplateService

Controllers should remain thin.

---

# 19. AI Architecture

Do not place AI logic directly inside controllers.

Create dedicated services.

EmbeddingService

Generate embeddings.

AISearchService

Find relevant projects.

ProposalService

Generate proposal.

This architecture should allow replacing AI providers later without changing business logic.

---

# 20. Coding Standards

Follow Laravel best practices.

Use Form Requests.

Use Policies where appropriate.

Use Resource Controllers.

Use Service Classes.

Use Eloquent Relationships.

Keep controllers thin.

Write reusable code.

Avoid duplicate logic.

Follow PSR standards.

---

# 21. Future Features (Not Part of MVP)

Project categories

Project tags

ZIP download of screenshots

Automatic AI summaries

Multiple AI providers

Proposal export

Markdown editor

Resume generator

Portfolio website generation

Interview preparation

Proposal analytics

Project timeline

Image AI caption generation

Semantic image search

---

# 22. Development Strategy

Build the project in small milestones.

Milestone 1

- Authentication
- Layout
- Navigation

Milestone 2

- Portfolio CRUD

Milestone 3

- Image Management

Milestone 4

- WYSIWYG Editor

Milestone 5

- Embedding Generation

Milestone 6

- AI Search

Milestone 7

- Prompt Templates

Milestone 8

- Proposal Writer

Milestone 9

- Prompt History

Milestone 10

- Dashboard Polish

Never start the next milestone until the current one is fully completed and tested.