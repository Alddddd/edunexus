# EduNexUs - Master Context File

## Project Title
EduNexUs

## Project Description
EduNexUs is a programmable cooperative assistance infrastructure platform for Philippine teacher cooperatives. It manages assistance approvals, QR claim passes, merchant validation, settlement lifecycle operations, and Morph-backed audit verification in one institutional reimbursement workflow.

EduNexUs is positioned as:
- programmable cooperative assistance infrastructure
- merchant settlement workflow platform
- blockchain-backed audit verification system
- governance-focused reimbursement infrastructure

EduNexUs is NOT:
- a crypto wallet platform
- a DeFi application
- a token trading system
- a consumer crypto product

---

# 1. Hackathon Overview

## Event
Build In! Payments Hackathon

Organized by:
- Morph
- Blockchain4Youth
- DvCode

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

# 2. Chosen Tracks

## Primary
SME Payments

## Secondary Alignment
Payroll & B2B

---

# 3. Core Vision

EduNexUs modernizes cooperative financial assistance workflows through:
- assistance approvals
- QR claim passes
- programmable governance rule validation
- merchant claim processing
- settlement generation and release
- proof bundle and deterministic proof hash generation
- Morph-backed audit verification

using Laravel + Morph blockchain.

---

# 4. Pilot Sector

## Teacher Cooperatives

Chosen because:
- understandable
- realistic in Philippine context
- operationally believable
- emotionally relatable
- feasible within the hackathon timeline

---

# 5. Core Blockchain Principle

Blockchain must remain invisible to normal users.

Users:
- do NOT manage wallets
- do NOT require crypto knowledge
- do NOT interact directly with blockchain

Morph blockchain acts as:
- backend trust infrastructure
- settlement verification support
- tamper-resistant proof receipt layer
- audit visibility infrastructure

---

# 6. Main Users

## Cooperative Admin
Can:
- manage assistance programs
- approve/reject requests
- monitor claims and settlements
- manage merchants
- release merchant settlements
- review Morph proof records and proof bundles

## Cooperative Member
Can:
- request assistance
- receive QR/reference claim pass
- track claim status
- present QR/reference to merchants

## Partner Merchant
Can:
- validate QR/reference claims
- review programmable governance checks
- process eligible claims
- monitor reimbursement status

Examples:
- bookstores
- school supply stores

## Auditor / Coop Manager
Can:
- review Morph proof records
- inspect proof bundle summaries
- inspect validation rule outcomes
- monitor settlement transparency
- verify operational audit trails

---

# 7. Current Stable Workflow

```text
Member Request
-> Admin Approval
-> QR Claim Pass
-> Merchant Validation
-> Programmable Rule Validation
-> Claim Processing
-> Settlement Generation
-> Settlement Release
-> Morph Proof Recording
-> Verification Console Audit Visibility
```

This workflow must remain protected. Future enhancements should clarify or extend governance, reporting, or audit visibility without redesigning the core flow.

---

# 8. Grand-Prize Differentiator

## Programmable Governance Validation + Proof Infrastructure

Example:
Education assistance:
- usable only at approved school supply merchants
- limited by approved amount
- subject to claim validity period
- protected from duplicate redemption

This makes blockchain:
- operationally meaningful
- connected to validated workflow events
- more than simple record logging
- understandable through proof bundles, deterministic hashes, and audit-ready verification UI

Current structured governance checks:
- claim reference verification
- approval status confirmation
- expiration / claim validity validation
- merchant eligibility validation
- duplicate redemption prevention
- approved amount validation

If any governance rule fails:
- claim processing is blocked
- settlement generation is prevented
- Morph proof recording is not triggered for that failed claim
- the merchant UI shows failed checks in an institutional pass/fail review

---

# 9. Blockchain Integration Scope

## Morph Stores / Receipts
- proof receipts for validated workflow events
- transaction hashes
- timestamps
- confirmation status

## Laravel/MySQL Stores
- users and authentication
- assistance programs
- assistance requests
- QR claim payloads
- structured validation rule summaries
- settlements
- blockchain transaction payloads
- proof bundles
- proof hashes
- dashboards, reports, and audit views

Sensitive personal data should not be placed on-chain.

---

# 10. Proof Bundle Infrastructure

When a claim is successfully processed, Laravel prepares a proof bundle containing:
- reference code
- event type
- assistance request id
- program id
- merchant id
- member id
- approved amount
- merchant category
- validation rule summary
- workflow status
- timestamp

The proof bundle is hashed deterministically with SHA-256. The resulting proof hash is stored with the blockchain transaction payload and displayed in verification/audit surfaces.

Current proof presentation includes:
- verified proof hash
- validation summary
- rule pass/fail outcomes
- lifecycle timeline visibility
- settlement status context

---

# 11. Settlement Lifecycle Infrastructure

Settlements now read as an institutional reimbursement lifecycle rather than a simple status table.

Current lifecycle display:
- Ready for Release
- Released
- Settlement Generated
- Settlement Released
- Settlement audit-ready

Implementation note:
- stored database statuses remain simple and safe (`Pending`, `Settled`)
- UI labels translate those statuses into clearer reimbursement lifecycle language
- no real money transfer, stablecoin transfer, wallet, or DeFi behavior was added

---

# 12. Advanced Morph Verification Console

The Morph Verification Console has been upgraded from a transaction log into a governance and audit verification dashboard.

Current capabilities:
- verification status presentation
- proof integrity labels
- validation summary visualization
- lifecycle timeline chips
- expandable proof bundle viewer
- settlement status context
- readable member, merchant, amount, event, and timestamp metadata
- restrained explanation of why Morph matters: tamper-resistant proof receipts for assistance validation and settlement events

The console remains non-crypto in tone. It emphasizes institutional verification rather than wallet mechanics.

---

# 13. Smart Contract Scope

Smart contracts must remain simple.

Main conceptual functions:

```solidity
recordApproval()
recordClaim()
recordSettlement()
verifyTransaction()
```

The MVP may use demo-safe transaction handling where appropriate. The product story remains proof recording and auditability, not tokenized settlement.

---

# 14. Current Stabilization State

Current phase:
- Governance + Verification Infrastructure Stabilization
- Programmable Rule Engine Stabilization
- Settlement Lifecycle Infrastructure Polish
- Advanced Morph Verification Console Stabilization

Protected systems:
- assistance approval flow
- QR/reference generation
- merchant claim processing
- programmable validation rules
- proof bundle generation
- Morph proof recording flow
- settlement lifecycle workflow
- notifications system
- role middleware structure
- dashboard navigation structure

---

# 15. Stabilization Progress Summary

Completed foundation work:
- institutional shell/UI refinement
- responsive admin/member/merchant workflow stabilization
- shared page-header normalization
- premium restrained interaction polish
- searchable assistance program selector
- dynamic validation feedback
- notification/alert UX improvements
- reusable toast, confirmation modal, and loader systems
- QR claim pass enlargement
- icon standardization
- pagination standardization

Completed infrastructure upgrades:
- programmable governance rule engine
- merchant rule validation interface
- expired and duplicate claim protection
- proof bundle generation
- deterministic SHA-256 proof hash generation
- Morph proof payload enrichment
- settlement lifecycle infrastructure polish
- advanced Morph Verification Console

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
- governance/audit oriented
- operationally realistic
- clean and understandable

Avoid:
- crypto-trader aesthetics
- cyberpunk styling
- gaming UI
- wallet-first language
- excessive animation
- unnecessary dashboard redesign

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

---

# 17. Next Main Phase

The governance + verification infrastructure is now the stabilized MVP foundation.

Next recommended phase:
1. Configurable Program Governance Rules
2. Multi-sector Cooperative Scalability Readiness
3. Reporting and Export Infrastructure
4. Demo and Video Preparation
5. Full End-to-End QA and final workflow reliability pass

Priority order:

```text
workflow reliability > audit clarity > additional UI redesign
```
