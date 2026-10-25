# AI Code Quality Standards & Checklist for CodesBlock

> **MANDATORY FOR ALL AI AGENTS & CONTRIBUTORS**  
> Clean, readable, performant, and maintainable code adhering to enterprise PHP & WordPress standards.

---

## 1. Syntax, Types & PHP 8+ Modernization
- [ ] **Strict Syntax Verification**: Zero syntax errors (`php -l` must pass 100% cleanly).
- [ ] **Type Declarations**: Use typed parameters and return types on all new functions and test classes:
  ```php
  declare(strict_types=1);

  function cb_calculate_progress( int $completed_lessons, int $total_lessons ): float {
      if ( $total_lessons === 0 ) {
          return 0.0;
      }
      return round( ( $completed_lessons / $total_lessons ) * 100, 2 );
  }
  ```
- [ ] **Null Coalescing & Type Coercion**: Use safe null coalescing (`??`) and avoid undefined index notices (`isset()`, `empty()`).

---

## 2. Code Smells & Clean Code Principles
- [ ] **No Dead / Commented-Out Code**: Delete discarded implementations rather than leaving dead blocks commented out. Git history preserves previous versions.
- [ ] **No Debug Statements in Committed Code**:
  - Remove all `var_dump()`, `print_r()`, `die()`, `console.log()` before committing.
  - Never leave test logging statements enabled in production paths.
- [ ] **Single Responsibility Principle**: Functions should do one task with clarity. Long monolithic functions must be factored into discrete helper functions.
- [ ] **Prefixing & Namespace Safety**:
  - All theme functions, hooks, and global constants must be prefixed with `codesblock_` or `cb_` to avoid conflicts with core WordPress or third-party plugins.

---

## 3. Performance & Asset Management
- [ ] **Dynamic Cache Busting**: Always append `filemtime()` versioning to enqueued styles and scripts:
  ```php
  $css_file = get_template_directory() . '/assets/css/course-portal.css';
  wp_enqueue_style(
      'codesblock-course-portal',
      get_template_directory_uri() . '/assets/css/course-portal.css',
      array( 'codesblock-main' ),
      file_exists( $css_file ) ? (string) filemtime( $css_file ) : '1.0.0'
  );
  ```
- [ ] **Conditional Asset Loading**: Do not load heavy scripts or CSS on pages where they are not required (e.g. course portal scripts only on course pages).
- [ ] **Database Query Optimization**: Avoid queries in loops (N+1 query problems). Use `get_posts()` with explicit parameters or transients for heavy computations.

---

## 4. Documentation & Comments
- [ ] Every function, hook callback, and class must have a PHPDoc docblock explaining its purpose, parameters, and return value.
- [ ] Preserve all existing docstrings and license blocks across modified files.
