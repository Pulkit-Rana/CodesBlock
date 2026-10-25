# AI Pull Request & Git Review Checklist for CodesBlock

> **MANDATORY PROTOCOL FOR AI GENERATED PRS AND COMMITS**  
> Every commit and PR authored or assisted by AI must undergo this strict self-audit.

---

## 1. Commit Message Standards (Conventional Commits)
All commit messages must adhere to the Conventional Commits specification:
`<type>(<optional scope>): <imperative description>`

### Approved Types:
- `feat`: A new user-facing feature or capability
- `fix`: A bug fix or patch
- `security`: A security enhancement, vulnerability patch, or hardening measure
- `refactor`: Code change that neither fixes a bug nor adds a feature
- `test`: Adding missing tests or correcting existing tests
- `ci`: Changes to CI/CD workflows and configuration scripts
- `docs`: Documentation updates or AI guideline additions
- `perf`: A code change that improves execution speed or memory footprint
- `chore`: Maintenance tasks, dependency updates, or internal build tooling

### ❌ Rejected Commit Messages:
- `fixed stuff`
- `update`
- `wip`
- `fixes bug in functions`

### ✅ Approved Commit Messages:
- `feat(course): add interactive progress tracker to lesson view`
- `fix(navigation): resolve active state highlight for /articles URL`
- `security(auth): enforce strict nonce verification on enrollment endpoint`
- `ci(actions): add automated unit test runner on PHP matrix`

---

## 2. Branch Naming Conventions
Always use structured, lowercase branch names:
- Feature: `feat/<feature-slug>`
- Bugfix: `fix/<issue-slug>`
- Security: `security/<cve-or-patch-slug>`
- Production / Infrastructure: `prod_setup`, `infra/<task-slug>`

---

## 3. Pull Request Self-Audit Protocol for AI
Before opening a PR or finalizing work, the AI must verify:
- [ ] **Diff Hygiene**: Run `git diff` and verify no unintended changes, whitespace regressions, or leftover debug lines exist.
- [ ] **No Untracked Junk**: Run `git status` to verify no scratch files or temporary logs are staged.
- [ ] **All CI Workflows Pass Locally**:
  - `php -l` on all PHP files.
  - `php tests/run-tests.php` passes with 0 failures.
  - No merge conflicts or conflict markers.
- [ ] **PR Description**: Fill out all sections of `.github/PULL_REQUEST_TEMPLATE.md`:
  - Clear summary of changes
  - Security checklist checked off
  - Local test output included
  - AI compliance verified
