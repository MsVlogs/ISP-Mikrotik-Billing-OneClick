# Prioritized Feature Plan

This branch should focus on the five features that provide the highest operational value for an ISP while preserving the existing Laravel 12, Filament 5, Livewire 4, MikroTik RouterOS, payment webhook, reseller, and one-click deployment foundations.

## Priority 1 — Payment reconciliation and automatic activation

**Why first:** Payment processing is the shortest path from revenue collection to customer service activation.

### Scope
- Normalize bKash, Nagad, Rocket, and SMS-forwarder transactions into one payment workflow.
- Make webhook processing idempotent using provider plus transaction ID.
- Add pending, approved, failed, refunded, and reconciled states.
- Match payments by subscriber code, phone number, invoice, or reference.
- Activate or renew PPPoE/Hotspot service only after a validated payment.
- Record every gateway response and provisioning attempt.
- Add failed-payment retry and manual reconciliation actions.

### Acceptance criteria
- A duplicate webhook cannot create a second payment or second activation.
- A successful payment creates an auditable payment record and one provisioning job.
- Failed RouterOS provisioning is visible and retryable without charging the customer again.
- Admins can reconcile unmatched payments from Filament.

## Priority 2 — Customer lifecycle and automated billing

**Why second:** It connects customers, packages, invoices, expiry, reminders, and RouterOS service status.

### Scope
- Customer states: lead, pending, active, suspended, expired, churned, and reactivated.
- Subscription and invoice timeline per customer.
- Monthly invoice generation and configurable grace period.
- Expiry reminders through the existing SMS integration.
- Queue-based suspend, renew, and re-provision workflows.
- Customer service-health indicators based on payment and connection status.

### Acceptance criteria
- Billing generation is safe to rerun for the same billing period.
- Expired accounts are suspended according to policy and can be restored after payment.
- Customer history shows invoice, payment, notification, and RouterOS actions together.

## Priority 3 — Financial reporting and expense control

**Why third:** It turns existing payment data into management information without introducing a second accounting system.

### Scope
- Income, expense, refund, commission, and net-profit summaries.
- Monthly and date-range reports.
- Payment-method and package-level breakdowns.
- Expense categories and approval/audit history.
- PDF, Excel, and CSV export using already-installed reporting dependencies.
- Dashboard KPIs with cached aggregates for large datasets.

### Acceptance criteria
- Report totals are derived from immutable payment/expense records.
- Every total can be drilled down to source records.
- Reports respect role and reseller scope.

## Priority 4 — Router observability and operational safety

**Why fourth:** Better visibility reduces outages and makes MikroTik automation safer.

### Scope
- Router health, uptime, API latency, interface usage, and active-session counts.
- Scheduled log polling with retention controls.
- Connection test and read-only health checks before provisioning.
- Router backup metadata and restore-point tracking.
- Audit trail for every write operation sent to RouterOS.
- Alerting for offline routers, failed syncs, and repeated provisioning failures.

### Acceptance criteria
- A failed health check prevents unsafe provisioning and explains why.
- Router status is visible per device and on the dashboard.
- Operational logs contain actor, router, command category, result, and correlation ID.

## Priority 5 — Reseller wallet and commission controls

**Why fifth:** It protects reseller revenue flows and formalizes the commission behavior already described by the product.

### Scope
- Wallet ledger with immutable credit/debit entries.
- Package and voucher purchases through a transaction boundary.
- Configurable upfront or deferred commission rules.
- Commission reversal for refunds and failed activation.
- Reseller-level reporting and permission boundaries.
- Low-wallet alerts and reconciliation tools.

### Acceptance criteria
- Wallet balance is calculated from ledger entries, not overwritten counters.
- A purchase and its commission are atomic.
- Refunds reverse the original financial entries instead of deleting history.

## Deferred features

Defer Live TV, employee payroll/attendance, and full OLT/PON automation until the five priorities are stable. OLT/PON inventory can be added later as a separate bounded module; it should not be mixed into the initial payment and RouterOS provisioning transaction flow.

## Delivery sequence

1. Payment domain and idempotent webhooks.
2. Customer subscriptions, invoices, and billing scheduler.
3. Financial reports and expenses.
4. Router health, logs, and safe provisioning.
5. Reseller ledger and commissions.
6. Hardening: permissions, exports, tests, queues, backups, and documentation.
