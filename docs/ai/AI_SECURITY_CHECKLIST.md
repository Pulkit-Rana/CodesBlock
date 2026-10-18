# AI Security Checklist & Guardrails for CodesBlock

> **MANDATORY FOR ALL AI AGENTS & CONTRIBUTORS**  
> Every code change in CodesBlock must pass every item on this checklist before merging or committing.

---

## 1. Direct Access & File Execution Guard
- [ ] **Direct Script Access**: Every PHP template or include file must prevent direct URL execution.
  ```php
  if ( ! defined( 'ABSPATH' ) ) {
      exit;
  }
  ```
- [ ] **No Direct File Disclosure**: Prevent directory indexing and direct access to logs or sensitive backups.

---

## 2. Cross-Site Scripting (XSS) Prevention & Output Escaping
Never output raw variables directly with `echo $var;` or `<?= $var ?>`. Always use context-specific escaping:

| Context | Escaping Function | Example |
|---|---|---|
| HTML Body | `esc_html()` | `<p><?php echo esc_html( $user_name ); ?></p>` |
| HTML Attribute | `esc_attr()` | `<input value="<?php echo esc_attr( $val ); ?>" />` |
| URL / Href / Src | `esc_url()` | `<a href="<?php echo esc_url( $link ); ?>">` |
| Textarea Content | `esc_textarea()` | `<textarea><?php echo esc_textarea( $bio ); ?></textarea>` |
| Trusted HTML Rich Text | `wp_kses_post()` | `<div><?php echo wp_kses_post( $content ); ?></div>` |
| JavaScript Variables | `wp_json_encode()` | `const data = <?php echo wp_json_encode( $obj ); ?>;` |

### ❌ Strictly Forbidden:
```php
// DANGEROUS: Direct XSS vulnerability
echo $_GET['search'];
echo "<a href='$url'>$title</a>";
```

### ✅ Mandatory Standard:
```php
// SECURE
echo esc_html( sanitize_text_field( $_GET['search'] ?? '' ) );
echo '<a href="' . esc_url( $url ) . '">' . esc_html( $title ) . '</a>';
```

---

## 3. SQL Injection (SQLi) Prevention
- [ ] Never concatenate user-supplied input directly into SQL strings.
- [ ] Always use `$wpdb->prepare()` with explicit placeholders (`%s`, `%d`, `%f`).
```php
// ❌ WRONG
$wpdb->query( "SELECT * FROM {$wpdb->posts} WHERE post_title = '$title'" );

// ✅ CORRECT
$safe_query = $wpdb->prepare(
    "SELECT * FROM {$wpdb->posts} WHERE post_title = %s",
    $title
);
$results = $wpdb->get_results( $safe_query );
```

---

## 4. Cross-Site Request Forgery (CSRF) & Nonces
- [ ] **All State-Changing Requests**: Every POST, PUT, DELETE, or AJAX action must generate and verify nonces.
  - Form generation: `wp_nonce_field( 'action_name', 'cb_nonce' );`
  - AJAX verification: `check_ajax_referer( 'cb_nonce_action', 'nonce' );`
  - REST API / custom verification:
    ```php
    if ( ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( $_REQUEST['nonce'], 'cb_action' ) ) {
        wp_send_json_error( array( 'message' => 'Invalid security token' ), 403 );
        exit;
    }
    ```

---

## 5. Authorization & Capability Checks
- [ ] Never rely solely on user authentication (logged-in state). Verify granular capabilities.
```php
if ( ! current_user_can( 'edit_posts' ) ) {
    wp_die( esc_html__( 'Unauthorized user action.', 'codesblock' ), 403 );
}
```
- [ ] For REST API routes, always define a strict `'permission_callback'`. Never leave it undefined or default to `__return_true` for protected endpoints.

---

## 6. Path Traversal & File Uploads
- [ ] Sanitize file paths using `sanitize_file_name()`.
- [ ] Verify paths resolve inside authorized directories using `realpath()` and whitelist checks.
- [ ] Reject null bytes (`\0`), control characters, and directory traversal sequences (`..`).

---

## 7. Secrets & Credential Isolation
- [ ] **Zero Hardcoded Secrets**: Never commit passwords, database tokens, API keys, private certificates (`.pem`, `.key`), or `.env` files.
- [ ] Check `.gitignore` before committing new file types.
- [ ] All sensitive credentials must be pulled from environment variables or secure WordPress options.
