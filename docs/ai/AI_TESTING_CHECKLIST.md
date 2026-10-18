# AI Testing Standards & Verification Checklist for CodesBlock

> **MANDATORY FOR ALL AI AGENTS & CONTRIBUTORS**  
> "Untested code is broken code." Every modification, bug fix, or new feature requires test coverage.

---

## 1. Zero Untested Code Policy
- [ ] **Bug Fixes**: Every bug fix MUST include a regression test in `tests/unit/` or `tests/security/` reproducing the failing condition and verifying the fix.
- [ ] **New Features / Endpoints**: Every new helper function, REST route, or template filter must have corresponding unit test assertions.
- [ ] **No Mocking What Can Be Tested**: Test real logic, data structures, and edge cases (empty strings, null, invalid characters, negative numbers, extreme inputs).

---

## 2. Test Execution Workflow for AI Agents
Before claiming any task is complete or preparing a commit/PR, the AI agent must run the test suite locally:

```bash
# Run the complete test suite
php tests/run-tests.php
```

### ✅ Verification Requirements:
1. Total Failures must be **0**.
2. All unit suites must report **[PASS]**.
3. If new tests were added, verify the assertion count increases.
4. Record test execution summary in the PR or task report.

---

## 3. Writing Unit Tests with `CBTestCase`
Unit tests are placed in `tests/unit/test-<feature-name>.php`:

```php
<?php
declare(strict_types=1);

require_once dirname( __DIR__ ) . '/bootstrap.php';

class TestFeatureExample extends CBTestCase {

    public function testExpectedBehavior(): void {
        $result = codesblock_some_function( 'input' );
        $this->assertEquals( 'expected_output', $result );
    }

    public function testEdgeCaseEmptyInput(): void {
        $result = codesblock_some_function( '' );
        $this->assertFalse( $result );
    }
}
```

### Supported Assertions:
- `$this->assertTrue( $cond, $msg )`
- `$this->assertFalse( $cond, $msg )`
- `$this->assertEquals( $expected, $actual, $msg )`
- `$this->assertContains( $needle, $haystack, $msg )`
- `$this->assertFileExists( $filepath, $msg )`

---

## 4. Security Regression Tests
Security tests are placed in `tests/security/`:
- Path traversal tests
- Input sanitization tests
- Nonce and permission verification tests
- Dependency vulnerability scans
