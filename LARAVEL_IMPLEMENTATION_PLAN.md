# Laravel Implementation Plan

Target branch: `feature/enhanced-billing-features`

Target stack: Laravel 12, PHP 8.3+, Filament 5, Livewire 4, Sanctum, Spatie Permission, Spatie Activity Log, RouterOS API PHP wrapper, Laravel queues, and the existing Vite frontend.

This is an implementation plan, not a claim that these modules are already implemented. Build each vertical slice with migration, model, policy, service, Filament resource, queue/job behavior, tests, and documentation.

## 1. Establish the domain map before migrations

Inspect the existing models, migrations, Filament resources, webhook controllers, and scheduled tasks first. Reuse existing customer, package, router, payment, reseller, wallet, and invoice concepts instead of creating duplicate tables.

Suggested commands:

```bash
php artisan about
php artisan route:list
php artisan schedule:list
php artisan migrate:status
```

Document the existing table and model relationships in `docs/domain-map.md` before adding foreign keys.

## 2. Payment and reconciliation vertical slice

### Suggested files

```text
app/Services/Payments/PaymentReconciliationService.php
app/Services/Payments/PaymentActivationService.php
app/Jobs/Payments/ProcessPaymentWebhook.php
app/Jobs/Payments/ProvisionPaidSubscription.php
app/Models/PaymentAttempt.php
app/Filament/Resources/PaymentAttemptResource.php
database/migrations/*_create_payment_attempts_table.php
tests/Feature/Payments/PaymentWebhookTest.php
tests/Unit/Services/PaymentReconciliationServiceTest.php
```

### Data rules

- Add a unique key for `provider` plus `provider_transaction_id`.
- Store raw payloads encrypted or in a protected JSON column where appropriate.
- Use database transactions for payment state changes.
- Dispatch provisioning after commit, never before the payment transaction commits.
- Use a correlation ID across webhook, payment, notification, and RouterOS logs.

### Webhook behavior

1. Authenticate and validate the provider request.
2. Normalize the payload into a provider-neutral DTO.
3. Find or create the idempotent payment attempt.
4. Reconcile against invoice/customer/subscriber reference.
5. Mark the payment state.
6. Dispatch `ProvisionPaidSubscription` once.
7. Return a provider-appropriate response without exposing secrets.

## 3. Customer lifecycle and billing

### Suggested files

```text
app/Models/Subscription.php
app/Models/Invoice.php
app/Services/Billing/BillingCycleService.php
app/Services/Customers/CustomerLifecycleService.php
app/Jobs/Billing/GenerateMonthlyInvoices.php
app/Jobs/Billing/SuspendExpiredSubscriptions.php
app/Jobs/Billing/SendExpiryReminder.php
app/Filament/Resources/SubscriptionResource.php
app/Filament/Resources/InvoiceResource.php
database/migrations/*_create_subscriptions_table.php
database/migrations/*_create_invoices_table.php
tests/Feature/Billing/BillingCycleTest.php
```

### Required invariants

- One invoice per customer/subscription/billing period.
- Invoice totals are stored with explicit currency and tax/discount values.
- A renewal extends service only after payment reconciliation succeeds.
- Suspension is reversible and must retain the reason and actor/job that caused it.

Register scheduled jobs in the existing scheduler and make each job safe to retry.

## 4. Finance and reporting

### Suggested files

```text
app/Models/Expense.php
app/Models/FinancialSnapshot.php
app/Services/Reporting/FinancialReportService.php
app/Filament/Resources/ExpenseResource.php
app/Filament/Pages/FinancialReports.php
database/migrations/*_create_expenses_table.php
database/migrations/*_create_financial_snapshots_table.php
tests/Feature/Reporting/FinancialReportTest.php
```

Use the existing PDF and spreadsheet packages where possible. Do not add duplicate export libraries until dependency compatibility is checked with `composer why-not` and the lockfile.

Reports should query approved/settled records, apply reseller scope, and expose drill-down links to source payments and expenses.

## 5. Router observability and safe provisioning

### Suggested files

```text
app/Services/Router/RouterHealthService.php
app/Services/Router/RouterProvisioningService.php
app/Jobs/Router/PollRouterHealth.php
app/Jobs/Router/SyncRouterUsers.php
app/Models/RouterHealthSnapshot.php
app/Filament/Widgets/RouterHealthWidget.php
database/migrations/*_create_router_health_snapshots_table.php
tests/Unit/Services/RouterHealthServiceTest.php
```

Keep RouterOS writes behind one service boundary. Add timeouts, bounded retries, structured logging, and a dry-run/read-only health check. Never make a payment webhook wait for a long RouterOS call; queue provisioning and expose its status.

## 6. Reseller wallet and commissions

### Suggested files

```text
app/Models/WalletLedgerEntry.php
app/Models/Commission.php
app/Services/Resellers/WalletService.php
app/Services/Resellers/CommissionService.php
app/Filament/Resources/WalletLedgerEntryResource.php
app/Filament/Resources/CommissionResource.php
database/migrations/*_create_wallet_ledger_entries_table.php
database/migrations/*_create_commissions_table.php
tests/Unit/Services/WalletServiceTest.php
```

Use ledger entries as the source of truth. Wrap purchase, debit, commission, and failure reversal in one database transaction. Add authorization policies so resellers can only see their own ledger and customers.

## 7. Permissions and audit coverage

Add explicit permissions for each module, for example:

```text
payments.view, payments.reconcile, payments.refund
billing.generate, billing.suspend
finance.view, finance.export, expenses.manage
routers.view, routers.health, routers.provision
wallet.view, wallet.adjust, commissions.manage
```

Use Spatie Permission for authorization and Spatie Activity Log for state-changing actions. Add feature tests for admin, staff, reseller, and unauthorized users.

## 8. Testing and quality gates

```bash
composer install
npm ci
php artisan migrate:fresh --seed
php artisan test
npm run build
php artisan route:list
php artisan schedule:list
```

Minimum tests before merging:

- Duplicate webhook idempotency.
- Unmatched payment remains pending and does not activate service.
- Successful payment dispatches one provisioning job.
- Invoice generation is idempotent.
- Expired subscription suspension is retry-safe.
- Financial totals reconcile to source records.
- Wallet debit and commission are atomic.
- Router timeout is recorded and retryable.
- Permission boundaries prevent reseller data leakage.

## 9. Deployment and rollback

Run migrations in a maintenance-safe release, deploy code before enabling scheduled jobs, and verify queue workers after deployment. Take a database backup before schema changes. Every migration must have a safe rollback strategy or an explicit documented reason why it cannot be reversed.

Update `README.md` and `/docs` only after the feature is implemented and tested; the planning documents must not be presented as completed functionality.
