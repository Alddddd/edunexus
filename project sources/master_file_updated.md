# EduNexUs — Master Context File

## Project Title
EduNexUs

## Project Description
A blockchain-backed programmable assistance and merchant settlement platform for Philippine teacher cooperatives using Morph blockchain for transparent and tamper-resistant payment verification.

---

# 1. Hackathon Overview

## Event
Build In! Payments Hackathon

Organized by:
- Morph
- Blockchain4Youth
- DvCode

---

## Main Theme
Focus:
- real-world blockchain payment systems
- financial workflows
- merchant/payment infrastructure
- practical blockchain integration

The project must:
- solve a real-world payment problem
- use Morph blockchain meaningfully
- demonstrate strong technical execution
- provide a working demo/prototype
- remain understandable and deployable

---

# 2. Important Dates

| Event | Date |
|---|---|
| Kickoff | May 18 |
| Video Submission Deadline | May 22 |
| Build Period | May 18–29 |
| Judging | June 1–2 |
| Awarding | June 4 |

Important:
- May 22 is the real MVP/demo deadline
- core workflow must already work before May 22
- prioritize polished vertical slice execution

---

# 3. Chosen Tracks

## Primary
SME Payments

## Secondary Alignment
Payroll & B2B

---

# 4. Judging Priorities

Focus:
- meaningful blockchain integration
- smooth workflows
- polished UX
- practical usability
- realistic operational feel
- strong demo quality
- deployable system design

---

# 5. Core Vision

EduNexUs modernizes cooperative financial assistance workflows through:
- assistance approvals
- programmable claims
- merchant settlements
- blockchain-backed verification

using Laravel + Morph blockchain.

---

# 6. Pilot Sector

## Teacher Cooperatives

Chosen because:
- understandable
- realistic in Philippine context
- operationally believable
- emotionally relatable
- feasible within timeline

---

# 7. Core Blockchain Principle

Blockchain must remain:
# invisible to normal users

Users:
- do NOT manage wallets
- do NOT require crypto knowledge
- do NOT interact directly with blockchain

Morph blockchain acts as:
- backend trust infrastructure
- settlement verification layer
- tamper-resistant proof system

---

# 8. Main Users

## Cooperative Admin
Can:
- manage assistance programs
- approve/reject requests
- monitor claims and settlements
- manage merchants
- review blockchain logs

---

## Cooperative Member
Can:
- request assistance
- receive QR/reference
- track claim status
- present QR to merchants

---

## Partner Merchant
Can:
- scan QR claims
- validate assistance
- confirm claims
- submit settlement records

Examples:
- bookstores
- school supply stores

---

## Auditor / Coop Manager
Can:
- review blockchain logs
- audit transactions
- monitor settlement transparency

---

# 9. Core MVP Workflow

Admin creates assistance program
↓
Member submits request
↓
Admin approves request
↓
QR/reference generated
↓
Member presents QR to merchant
↓
Merchant validates QR
↓
Programmable rules validate claim
↓
Morph blockchain records proof
↓
Dashboard updates
↓
Settlement recorded

---

# 10. Grand-Prize Differentiator

## Programmable Assistance Validation

Example:
Education assistance:
- usable only at approved school supply merchants
- limited by claim amount
- subject to expiration

This makes blockchain:
- operationally meaningful
- more than simple record logging

---

# 11. Blockchain Integration Scope

## Morph Stores
- approval proofs
- claim proofs
- settlement proofs
- timestamps
- transaction hashes
- verification records

---

## Laravel/MySQL Stores
- users
- authentication
- dashboards
- reports
- workflow data
- QR generation
- assistance records

---
# 12. Smart Contract Scope

Smart contracts must remain simple.

Main functions:

```solidity
recordApproval()
recordClaim()
recordSettlement()
verifyTransaction()
```

# 13. Platform Architecture

EduNexUs is:
- a web-based application
- desktop-first dashboard system
- responsive web platform

---

# 14. Current Stabilization State

EduNexUs has completed a major UI/UX and workflow stabilization phase.

Current phase:
- UI/UX Foundation Stabilization
- Responsive Shared Layout Stabilization
- Pre-Full-QA Phase

The core MVP workflow remains stable:

```text
Member Request
-> Admin Approval
-> QR Generation
-> Merchant Validation
-> Programmable Rule Validation
-> Morph Blockchain Proof Recording
-> Settlement Tracking
-> Dashboard/Logs/Notifications Update
```

Important:
- This phase did not redesign the product.
- The assistance approval, QR generation, merchant claim processing, programmable validation rules, blockchain proof recording, settlement workflow, notifications, role middleware, and dashboard navigation structures must remain protected.
- Current priority is workflow reliability over additional UI redesign.

---

# 15. Stabilization Progress Summary

## Timezone Stabilization

The app timezone was migrated safely to Asia/Manila through configuration only.

Outcome:
- Philippine operational timestamps now align across dashboard activity, logs, approvals, claims, settlements, and notifications.
- No workflow logic was rewritten.
- No payment, approval, or blockchain flow was changed.

## Toast System

A reusable Sonner-style native Blade/Tailwind toast system was implemented.

Supports:
- success
- error
- warning
- info

Current behavior:
- global toast stack added to the dashboard layout
- auto-dismiss support
- close button support
- restrained premium styling

Primary file:
- `resources/views/components/toast.blade.php`

## Confirmation Modal System

Native browser confirm dialogs were replaced with a reusable Tailwind confirmation modal system.

Supports:
- title
- message
- confirm text
- tone states
- loading states

Applied to:
- logout
- approve request
- reject request
- settlement confirmation
- blockchain proof confirmation
- merchant claim processing

The modal system improves perceived quality without changing workflow rules.

## Premium Loader Overlay

A reusable global loader overlay was added after confirmation modal approval.

Supports:
- custom loader title
- custom loader message

The loader uses institutional fintech styling and keeps long-running actions visually understandable during demos.

## Validation Improvements

Assistance amount validation was corrected and made safer.

Current behavior:
- member request amount respects the program maximum
- admin approval amount respects the program maximum
- merchant validation safety rules remain preserved

## Pagination Standardization

Major operational tables now use standardized `paginate(5)` behavior.

Preserved:
- centered pagination layout
- query string filters
- dashboard/stat aggregation logic

## QR Claim Pass Enhancement

The claim pass now supports an enlarged QR modal.

Preserved:
- QR payload compatibility
- print layout

Added:
- modal close button behavior
- backdrop close behavior
- Escape key close behavior

## Icon Standardization

Emoji/text icons were replaced with a reusable SVG icon component.

Standardized areas:
- sidebar
- notifications
- activity logs
- blockchain logs
- settlement states
- dashboard indicators
- workflow arrows
- toast icons
- modal icons

Primary file:
- `resources/views/components/icon.blade.php`

---

# 16. Current Design System

Current visual direction:

```text
Institutional Sage Fintech
```

The UI should feel:
- premium
- modern
- institutional
- fintech-inspired
- operationally realistic
- clean and understandable

The current shell/layout should not be redesigned unless fixing a real UX issue.

Semantic Tailwind tokens:
- `ui-shell`
- `ui-canvas`
- `ui-surface`
- `ui-action`
- `ui-anchor`
- `ui-proof`
- `ui-success`
- `ui-warning`
- `ui-danger`
- `ui-border`
- `ui-subtext`
- `ui-muted`

Finalized palette:
- `ui-shell`: `#D4E2DC`
- `ui-canvas`: `#EEF4F1`
- `ui-surface`: `#F9FCFB`
- `ui-action`: `#0B5D56`
- `ui-anchor`: `#0F2F2C`

Typography:
- Inter font
- strengthened hierarchy
- institutional readable contrast
- premium restrained style

Visual hierarchy:
- shell = structure
- canvas = workspace
- cards = elevated content

Avoid:
- crypto-trader aesthetic
- cyberpunk styling
- gaming UI
- excessive animation
- unnecessary dashboard redesign

---

# 17. Responsive Foundation

The shared responsive shell is implemented in:
- `resources/views/layouts/dashboard.blade.php`

Current behavior:
- desktop persistent sidebar
- mobile/tablet collapsible sidebar drawer
- hamburger menu
- backdrop close
- Escape close
- responsive topbar controls
- responsive notification dropdown sizing
- Alpine-based responsive shell behavior

Animations:
- restrained sidebar slide/fade
- backdrop fade
- subtle transitions
- no heavy animation libraries

Primary dashboard reference:
- `resources/views/admin/dashboard.blade.php`

---

# 18. Known Minor Issue

There is a remaining perceived top-spacing issue on laptop widths in the Admin Dashboard shell/header area.

Status:
- not MVP-blocking
- should be revisited only if it becomes a real UX issue during QA

---

# 19. Next Main Phase

The UI foundation is considered stabilized for MVP.

Next phase:
1. Full End-to-End QA
2. Workflow reliability validation
3. Page-level responsive cleanup
4. Responsive tables/forms/cards
5. Final operational polish

Priority order:

```text
workflow reliability > additional UI redesign
```
