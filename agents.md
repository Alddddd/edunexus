# AGENTS.md — EduNexUs Development Instructions

## Project Name
EduNexUs

## Project Overview
EduNexUs is a responsive web-based programmable cooperative assistance platform for the Philippines.

The platform manages:
- assistance request approvals
- QR-based claim validation
- merchant settlement workflows
- programmable validation rules
- blockchain proof recording on Morph
- operational dashboards and audit visibility

The system is:
- NOT a crypto trading platform
- NOT a DeFi application
- NOT a mobile app

The blockchain layer should remain:
- operationally meaningful
- visually understandable during demos
- mostly invisible to normal users

---

# Core MVP Workflow

The MVP flow MUST remain stable and understandable.

Primary workflow:

Admin approves assistance
→ QR/reference generated
→ Member presents claim pass
→ Merchant validates QR/reference
→ Programmable rules execute
→ Claim processed
→ Morph blockchain proof recorded
→ Settlement generated
→ Dashboard and audit timeline update

Do NOT redesign this workflow.

---

# Development Philosophy

Always prioritize:
- workflow clarity
- implementation safety
- polished UX
- realistic operational behavior
- institutional dashboard feel
- hackathon demo readiness

Avoid:
- unnecessary complexity
- overengineering
- massive rewrites
- experimental architecture
- complex blockchain mechanics
- crypto-trader aesthetics
- gaming UI
- cyberpunk styling

The goal is:
polished believable execution.

---

# UI / UX Direction

The UI should feel:
- premium
- modern
- institutional
- fintech-inspired
- operationally realistic
- clean and understandable

Use:
- Tailwind CSS
- Flowbite components when useful
- rounded-2xl cards
- slate-based neutral palette
- teal accents
- soft borders
- subtle shadows
- clean spacing

Avoid:
- visual clutter
- excessive animation
- neon colors
- dark hacker aesthetics

---

# Technical Stack

Framework:
- Laravel

Frontend:
- Blade
- Tailwind CSS
- Flowbite
- Vite

Backend:
- PHP
- MySQL

Blockchain:
- Morph blockchain integration
- service-based blockchain abstraction
- simulated/demo-safe transaction handling allowed

---

# Operational Rules

## IMPORTANT

Before making code changes:
1. inspect existing implementation first
2. explain risks if applicable
3. preserve stable logic

Always prefer:
- smallest safe implementation
- incremental changes
- compatibility with existing routes and controllers

Never:
- rewrite stabilized systems unnecessarily
- refactor large unrelated sections
- rename routes without reason
- break existing dashboard flows

---

# Protected Stable Systems

Do NOT break or redesign these systems unless explicitly requested:

- assistance approval flow
- QR/reference generation
- merchant claim processing
- programmable validation rules
- blockchain proof recording flow
- settlement workflow
- notifications system
- role middleware structure
- dashboard navigation structure

---

# Dashboard Standards

Admin dashboards should:
- feel operational
- display meaningful statuses
- use clean summary cards
- use readable tables/timelines
- support filters when appropriate
- feel believable for institutional use

Good examples:
- audit timelines
- settlement consoles
- verification logs
- approval monitoring
- operational feeds

---

# Blockchain Rules

Blockchain integration should:
- appear meaningful during demo
- record proof events
- support verification visibility
- avoid exposing crypto complexity to users

Do NOT:
- add wallets for normal users
- add token trading
- add DeFi mechanics
- add unnecessary smart contract complexity

---

# Coding Rules

When implementing:
- preserve formatting consistency
- keep Blade views clean
- keep controllers focused
- avoid giant methods
- preserve route naming conventions

Prefer:
- service classes for business logic
- readable Blade templates
- explicit status handling
- defensive validation

Avoid:
- hidden magic behavior
- unnecessary abstractions
- premature optimization

---

# Testing / Validation

After changes:
- run only necessary commands
- report changed files clearly
- explain what to manually test

Common commands:
```bash
php artisan route:list
php artisan view:clear
php artisan optimize:clear
npm run build