# Interview Session First Phase

## Intent

Turn the homepage's interview practice preview into a real, account-backed flow: a learner chooses a track, starts a guided workspace, and sees a clear next step when their one free session has been used. The same paid CodesBlock membership used for courses unlocks paid interview sessions.

## Goals

- Add a polished interview setup and workspace screen that fits the existing CodesBlock theme.
- Require a signed-in WordPress account to create a session.
- Grant one free session per account, enforced server-side and reserved atomically.
- Use the existing PMPro/CodesBlock membership entitlements and checkout URLs for paid access; do not create interview-only prices or subscriptions.
- Keep a clear integration seam for a future AI interviewer without implying it is already active.

## Non-goals

- Integrate or claim a working AI interviewer, scoring, or generated feedback.
- Change course prices, membership levels, checkout, or gateway configuration.
- Make session records available to other users.

## Approach

Add an interview route and a private session record in the theme. A WordPress REST endpoint checks authentication and paid membership; for free members it reserves the single free session under a per-user WordPress option before creating a private session record. The setup UI sends the selected track and difficulty to that endpoint. The workspace displays a starter prompt and an explicit AI-not-connected state. When access is exhausted, the existing course membership modal provides the same paid choices and checkout integration.

This approach reuses the existing site membership contract and keeps session creation on the server. A separate interview checkout would duplicate prices and billing behavior, while a client-only limit could be bypassed.

## Constraints and open implementation choices

- The theme is the deployable repository. The nested commerce plugin has uncommitted deletions in its separate checkout, so this feature must call its existing public helpers without modifying or restoring that plugin.
- Session metadata stores owner, track, difficulty, creation time, and starter prompt only. AI conversation storage and transport remain for phase two.
- Admins retain their existing paid-access bypass.
