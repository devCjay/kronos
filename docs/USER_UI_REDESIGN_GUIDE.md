# User Interface Redesign Development Guide

## 1. Purpose

This guide governs the phased redesign of every user-facing page in the platform. The objective is to create a polished crypto-exchange experience that feels modern, fast, secure, and consistent without changing business logic, routes, validation, permissions, financial calculations, or user workflows.

The redesign starts with authentication. One page is redesigned, tested, presented, and approved before work begins on the next page.

## 2. Non-Negotiable Rules

1. This is a UI redesign, not a functional rewrite.
2. Existing controllers, models, services, route names, middleware, request fields, and response formats must remain compatible.
3. Existing form actions, HTTP methods, CSRF protection, AJAX behavior, validation errors, redirects, notifications, pagination, filters, exports, and downloads must remain functional.
4. Financial values, balances, fees, PnL, trading-bot calculations, blockchain addresses, and transaction statuses must never be altered by presentation code.
5. Existing authorization, OTP, KYC, account-status, and sandbox restrictions must remain intact.
6. No page may be removed, merged, or functionally simplified without explicit approval.
7. Mobile navigation and responsive behavior must be verified for every phase.
8. Each page requires approval before the next page is started.
9. Changes must remain reviewable. Shared components may be extracted only after their first approved use.
10. Do not imitate a specific exchange exactly. Use familiar crypto-exchange interaction patterns while preserving this platform's identity.

## 3. Brand Direction

### Visual identity

- Primary canvas: near-black charcoal (`#0b0e11`).
- Elevated surface: exchange charcoal (`#151a21`).
- Primary brand accent: warm gold (`#f7a600`).
- Positive state: emerald (`#10b981`).
- Negative state: red (`#ef4444`).
- Primary text: cool white (`#f8fafc`).
- Secondary text: slate (`#94a3b8`).
- Borders: subtle white at approximately 6–10% opacity.
- Glow: restrained amber illumination around important branded actions, never around every component.

### Brand principles

- Gold communicates primary actions, active states, focus, and premium identity.
- Green and red are reserved for financial direction and success/error states.
- Avoid the former cyan/dark-blue visual language.
- Maintain generous negative space and clear information hierarchy.
- Prefer flat exchange panels with subtle depth over oversized glass effects.
- Use rounded corners consistently: 8–12px for controls, 12–16px for panels.
- Keep the platform logo, name, favicon, and configured assets dynamic through existing settings.

### Typography

- Headings: Plus Jakarta Sans.
- Interface copy: Inter.
- Addresses, references, codes, and market figures: JetBrains Mono.
- Use tabular numerals for balances and financial tables where possible.
- Avoid excessive uppercase text; reserve it for compact labels and status metadata.

## 4. Experience Standards

### Layout

- Desktop target: 1280–1536px.
- Tablet target: 768–1024px.
- Mobile target: 320–430px.
- No horizontal page overflow at any supported width.
- Financial tables may scroll inside their own container on narrow screens.
- Primary action must remain visible without obscuring content.
- Important forms should use a readable maximum width rather than stretching across large screens.

### Motion

- Standard transition: 160–220ms.
- Panel entrance: subtle opacity and 6–10px translation only.
- Hover motion: maximum 1–2px translation or a restrained border/glow change.
- Loading indicators must communicate actual work and must not block indefinitely.
- Respect `prefers-reduced-motion`.
- Avoid continuous decorative animation except small live-status indicators.

### Accessibility

- Meet WCAG AA contrast for body text and controls.
- Every input requires an associated label.
- Focus states must be visible and use the gold brand accent.
- Icon-only buttons require accessible labels and tooltips.
- Keyboard navigation must work for forms, menus, dialogs, tabs, and filters.
- Validation errors must be announced and displayed beside the relevant field.
- Do not rely on color alone to communicate status.

### Content style

- Use direct, professional language.
- Explain security-sensitive actions before submission.
- Clearly distinguish pending, processing, completed, failed, and rejected states.
- Never imply guaranteed profit or risk-free trading.
- Shorten supporting text on mobile without removing required disclosures.

## 5. Shared Component System

Build or standardize these components progressively as approved pages require them:

- Brand logo lockup
- Authentication shell
- User application shell
- Page heading and breadcrumb
- Primary, secondary, ghost, danger, and icon buttons
- Text, email, password, number, select, OTP, file, and search fields
- Password visibility and strength controls
- Form error and help text
- Status badges
- Metric cards
- Exchange-style data tables
- Empty, loading, error, and success states
- Confirmation dialog
- Toast notification
- Tabs and segmented filters
- Pagination
- Transaction reference display
- Copy-to-clipboard control
- Asset/network selector
- Responsive drawer

Components must accept existing dynamic values and translations. Do not hard-code the platform name, currency, blockchain, user, or asset data.

## 6. Functional Preservation Checklist

Before editing a page, record its current contract:

- Route name and URL
- Controller action
- Middleware
- Form action and method
- Input names and expected value types
- Conditional fields
- Validation and server error output
- AJAX selectors and JavaScript events
- Success behavior and redirect
- Failure behavior
- Dynamic settings and module checks
- Empty/loading states
- Mobile-specific behavior
- Related downloads, receipts, filters, or pagination

After redesign, compare the implementation against this contract. Visual changes must not rename or remove any functional hook.

## 7. Per-Page Workflow and Approval Gate

Every page follows this sequence:

1. **Audit:** Document the existing functional contract and identify reusable components.
2. **Design:** Implement the responsive visual redesign only for the current page.
3. **Self-review:** Check content hierarchy, brand alignment, mobile layout, accessibility, and all states.
4. **Technical verification:** Compile Blade, build assets, validate routes, and run relevant tests.
5. **Functional verification:** Test each form, link, button, dialog, filter, and conditional state.
6. **Presentation:** Provide the changed files, screenshots when available, test results, and any known limitations.
7. **Approval:** Stop and request approval for the completed page.
8. **Revision or advance:** Apply requested revisions. Start the next page only after explicit approval.

Approval applies only to the current page. It does not authorize redesigning later phases automatically.

## 8. Definition of Done for Each Page

A page is complete only when:

- Its original functionality remains available.
- Desktop, tablet, and mobile layouts are usable.
- There are no clipped controls or unintended horizontal overflow.
- Default, hover, focus, disabled, loading, empty, success, and error states are addressed where applicable.
- Text and controls meet contrast requirements.
- Keyboard navigation is usable.
- Dynamic translations and configured settings still render.
- Blade compilation succeeds.
- Production assets build successfully.
- Relevant automated tests pass, or any unavailable test dependency is reported.
- The user explicitly approves the page.

## 9. Phased Page Roadmap

### Foundation — approval required before Page 1

- Confirm brand tokens, spacing, typography, control sizes, and responsive breakpoints.
- Prepare the reusable authentication shell without altering authenticated pages.
- Establish component naming and CSS organization.
- Capture a functional baseline for authentication routes.

### Authentication sequence

Each item is an independent approval phase:

1. **Login** — `user/auth/login.blade.php`
   - Email/username and password flow
   - Password visibility
   - Remember/recovery links where currently available
   - Social authentication providers
   - Validation, loading, and error states

2. **Registration** — `user/auth/register.blade.php`
   - All existing identity, referral, consent, and verification fields
   - Password requirements and confirmation
   - Email verification and resend/cancel behavior

3. **Login OTP** — `user/auth/otp.blade.php`
   - OTP entry, submission, resend, timing, and error states

4. **Forgot Password** — `user/auth/passwords/email.blade.php`
   - Recovery identifier submission and confirmation state

5. **Password Recovery OTP** — `user/auth/passwords/otp.blade.php`
   - Verification and resend behavior

6. **Reset Password** — `user/auth/passwords/reset.blade.php`
   - New password, confirmation, strength guidance, submission, and errors

### Onboarding and identity

7. **KYC** — `user/kyc.blade.php`
   - Existing fields, file uploads, requirements, status, validation, and submission

8. **Onboarding dialog/flow** — shared onboarding partial
   - Existing risk-profile selection and completion behavior

### Core account experience

9. **Dashboard review and refinement** — `user/dashboard-exchange.blade.php`
   - Preserve the current exchange dashboard functionality
   - Align final components with approved authentication and shell standards

10. **Transactions** — `user/transactions.blade.php`
    - Ledger, statuses, pagination, references, values, and empty states

### Deposits

11. **Deposit history** — `user/deposits/index.blade.php`
12. **Deposit status scopes** — `user/deposits/scope.blade.php`
13. **New deposit** — `user/deposits/new-d.blade.php` and required shared partials
14. **Deposit details and receipt** — `user/deposits/view.blade.php`
15. **Deposit shared/general states** — `user/deposits/general.blade.php`

Network selection, wallet generation, QR/address display, copy actions, expiry, status, and receipt download must remain intact.

### Withdrawals

16. **Withdrawal history** — `user/withdrawals/index.blade.php`
17. **Withdrawal status scopes** — `user/withdrawals/scope.blade.php`
18. **New withdrawal** — `user/withdrawals/new.blade.php`
19. **Withdrawal details** — `user/withdrawals/view.blade.php`

Balance checks, rates, limits, fees, blockchain/network fields, validation, and submission safeguards must remain intact.

### Trading bots

20. **Trading bot marketplace** — `user/trading/bots/index.blade.php`
21. **Bot activations** — `user/trading/bots/activations.blade.php`
22. **Bot logs** — `user/trading/bots/logs.blade.php` and `partials/logs_table.blade.php`
23. **Daily summary** — `user/trading/bots/daily_summary.blade.php` and `partials/daily_cards.blade.php`

Bot activation inputs, plan terms, available balance, histories, filters, ROI/PnL presentation, and statuses must remain unchanged functionally.

### Growth and account management

24. **Referrals** — `user/referrals.blade.php`
    - Referral link/code, statistics, network, commissions, and copy actions

25. **Profile** — `user/account/profile.blade.php`
26. **Security** — `user/account/security.blade.php`
27. **Account sidebar/shared navigation** — `user/account/partials/sidebar.blade.php`

Password updates, session termination, profile validation, sandbox restrictions, and account-security controls must remain intact.

### Final consistency phase

28. Audit every user page for shared spacing, typography, status colors, tables, forms, mobile behavior, and translations.
29. Remove superseded UI-only styles and components after confirming they are unused.
30. Run the complete applicable test suite and production build.
31. Conduct a final desktop/tablet/mobile regression review.

## 10. Testing Matrix

For every applicable page, verify:

| Area | Required checks |
|---|---|
| Routing | Page loads through the existing named route and middleware |
| Forms | Correct action, method, CSRF, fields, validation, loading, success, and failure |
| Data | Real dynamic values, formatting, statuses, pagination, and empty states |
| Security | Authentication, OTP, KYC, sandbox, and authorization behavior |
| Responsive | 320px, 375px, 768px, 1024px, and desktop widths |
| Accessibility | Keyboard, labels, focus, contrast, dialogs, and icon names |
| Browser | Current Chrome, Firefox, Safari, and mobile WebKit behavior where available |
| Build | Blade cache and Vite production build |
| Regression | No changes to unrelated pages or backend behavior |

## 11. Change Handoff Format

At the end of every page phase, report:

- Page and route completed
- Files changed
- Functional contract preserved
- Components introduced or reused
- Responsive states checked
- Tests and build results
- Known limitations
- Exact approval question: **Approve this page and proceed to the next phase?**

## 12. First Execution Phase

The first implementation phase is the Login page only. Before changing it:

1. Audit `user/auth/login.blade.php`, its layout, controller action, route, social-login conditions, validation response, and JavaScript hooks.
2. Present the proposed login-page information hierarchy and component reuse plan.
3. Implement the approved login redesign.
4. Verify every login path and responsive state.
5. Stop for approval before editing Registration.

