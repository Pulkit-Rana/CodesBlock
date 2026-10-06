# Security remediation — 7 October 2026

## Repository fixes

- Dependabot #27: updated Knit Pay's bundled Flysystem from 3.35.2 to the official 3.35.3 release. Updated the actual PHP implementation, lockfile, and installed Composer metadata together. The malformed UTF-8 regression reproduced three failures before the update; all 11 checks passed after it.
- Dependabot #25: restricted the bundled Sodium library's development-only PHPUnit requirement to `^8.5.52 || ^9.6.33`, the patched versions of its existing supported major versions. This changes test-tool requirements, not WordPress's cryptographic implementation. PHP older than 7.2 cannot install these patched test tools.
- Removed database exports, migration snapshots, deployment copies, and site archives from the Git index. Local copies are preserved. Added ignore rules for these files, private environment files, and private keys.
- Secret-scanning #3: resolved as a false positive after confirming that the detected key is exactly the public example in Stripe's official PHP SDK README. It is not a CodesBlock Stripe credential.
- Secret-scanning #1, #2, and #4: the Google OAuth client ID, client secret, and access token were found in SQL exports. These require provider-side remediation; deleting files does not revoke credentials or erase older commits.

## Required manual actions

1. **Rotate the Google OAuth client secret.** Open [Google Auth Platform → Clients](https://console.cloud.google.com/auth/clients), select the client identified in [alert #2](https://github.com/Pulkit-Rana/CodesBlock/security/secret-scanning/2), add a new secret, update the WordPress integration using that client, and disable/delete the exposed old secret. Test Google sign-in or the affected integration. A client ID is a public identifier; it does not need replacement solely because it was exposed alongside the secret.
2. **Revoke the exposed Google authorization.** Use [Google Account connections](https://myaccount.google.com/connections) to remove the affected app's access, then reconnect it in WordPress. Review [alert #4](https://github.com/Pulkit-Rana/CodesBlock/security/secret-scanning/4) to identify the token. Also revoke any associated refresh token; an expired access token alone does not establish that the authorization is safe.
3. **Check the other sensitive contents of the published backups.** SQL exports can include WordPress password hashes, application tokens, and customer information. Reset administrator passwords, revoke active sessions, and review the old migration archives for hosting/database passwords and other API credentials; replace anything exposed. Do not paste those values into GitHub or chat.
4. **Apply the Knit Pay patch to production.** The deployment documented in `DEPLOYMENT.md` updates `public_html/wp-content/themes/codesblock`; it does not update the live plugin. Back up the live plugin, then extract the provided `knit-pay-flysystem-3.35.3-patch.zip` into `public_html/wp-content/plugins/`, replacing the four matching files. The archive contains only the normalizer and its three Composer metadata files. Keep the other Knit Pay files. Verify payment checkout in test mode. Alternatively, update Knit Pay to an official release after verifying that its bundled Flysystem is at least 3.35.3. Future plugin updates can overwrite this patch, so recheck the bundled version.
5. **Close the remaining Google alerts after remediation.** Close #2 and #4 as revoked only after disabling the credentials at Google. Review #1 as the associated public client identifier after the secret remediation. The alerts remain open until those actions are verified.
6. **Remove the historical private data.** The new commit removes files from the current branch, but older commits still contain the exports and archives. After credential rotation, coordinate a repository-history cleanup using GitHub's [sensitive-data removal guide](https://docs.github.com/en/authentication/keeping-your-account-and-data-secure/removing-sensitive-data-from-a-repository). Include `.codex-migration/`, `migration-staging/`, `deploy-packages/`, `app/sql/`, historical `app/public/wp-config.php`, and `output/security-20260912/custom-plugins.tar.gz`. Include any additional private paths identified during review. History cleanup requires coordinated force-pushes and fresh clones, and GitHub Support may need to remove cached commit views. It does not invalidate copies already downloaded by others. No history rewrite or force-push was performed by this remediation.

## Verification and boundaries

Run the regression check from the repository root:

```sh
php tests/security/flysystem-path-normalization.php
```

Expected result: `11 checks, 0 failures`.

The repository has no root test runner and does not ship the upstream Flysystem/PHPUnit test suites. Verification covers the real bundled autoloader, filename normalization, traversal behavior, changed PHP syntax, and package metadata. Live payment checkout and provider credential revocation require the manual actions above. Unrelated existing course and mobile UI changes were excluded from the security commit.

References: [Flysystem advisory](https://github.com/advisories/GHSA-cxf4-7mrp-vvpr), [PHPUnit advisory](https://github.com/advisories/GHSA-vvj3-c3rp-c85p), [Google secret rotation](https://support.google.com/cloud/answer/15549257), [Google OAuth security practices](https://developers.google.com/identity/protocols/oauth2/resources/best-practices), [GitHub secret alert resolution](https://docs.github.com/en/code-security/how-tos/manage-security-alerts/manage-secret-scanning-alerts/resolving-alerts).
