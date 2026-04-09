---
description: "Poranam project guidelines for AI agents. PHP monolithic web application with AJAX handlers, database abstraction layer, and admin/editor interface."
---

# Poranam Project Instructions

## Project Overview

**Poranam** is a PHP web application providing a collaborative platform with user management, initiatives, meetings, voting, and administrative capabilities. The codebase uses a traditional monolithic architecture with server-side rendering, AJAX handlers, and a custom database abstraction layer.

- **Language**: PHP 7+
- **Architecture**: Monolithic MVC-like pattern
- **Database**: MySQL (configured in `Connections/dbconn.php`)
- **Frontend**: jQuery + custom JavaScript + AJAX
- **Current Branch**: `fix/admin-votes`

## Directory Structure & Purpose

```
/Connections/          Database layer, core utilities, ORM
  └─ db.php            Custom database abstraction/query builder
  └─ functions.php     Shared utility functions (frequently modified)
  └─ gui.php           GUI/HTML generation helpers
  └─ rbp.php           RedBeanPHP ORM (large file - use for complex queries)
  
/editor/               Admin/management interface
  └─ index.php         Admin dashboard entry point
  └─ Main.php          Core admin functionality
  └─ *.php             Admin modules (users, sites, modules, etc.)
  
/modules/              Business logic and AJAX handlers
  └─ ajaxHandlers/     AJAX endpoint handlers (linked from index.php)
  └─ vendor/           Composer dependencies (PHPMailer, Dadata)
  
/tmpl/                 Page templates and dialogs
  └─ page/             Page templates
  └─ dialogs/          Modal/dialog templates
  
/css, /js, /fonts, /images/  Static assets

/index.php             Main application entry point (router)
/p84699_db.sql         Database schema dump
```

## Key Patterns & Conventions

### 1. AJAX Request Handling
All AJAX requests go through `index.php` which routes based on POST data:

```php
// AJAX detection
if ($_POST['ajax'] == 1) {
    switch($_POST['mode']) {
        case 'popup':
            // Load dialog from /tmpl/page/dialogs/{url}.php
            include $dialogUrl;
            break;
        default:
            // Load handler from /modules/ajaxHandlers/{action}.php
            include $ajaxHandler;
            break;
    }
}
```

**Pattern**: When adding AJAX endpoints, create a new PHP file in `/modules/ajaxHandlers/{action}.php`

### 2. Database Connection & Queries
Database connections are initialized in `/Connections/dbconn.php`. For queries:

- **Simple queries**: Use `$db->query()` (in `db.php`)
- **Complex queries**: Consider `RedBeanPHP` via `rb.php`
- **Custom utilities**: Check `functions.php` before writing new ones

### 3. Database Constraints
- Character encoding: UTF-8 (strings use Cyrillic)
- Timestamps: Use consistent datetime formats
- Foreign keys: Follow patterns in existing migrations

### 4. File Naming & Organization
- **Handler files**: camelCase with lowercase first letter (`editProfile.php`, `addInitiative.php`)
- **Class files**: PascalCase (`DataBase.php`, `PageManager.php`)
- **Utility files**: Descriptive lowercase or PascalCase (`functions.php`, `Tools.php`)
- **Session & Auth**: Handled at request entry in `/editor/` (admin panel)

### 5. Code Style
- **PHP**: ~140KB+ files (e.g., `functions.php`, `gui.php`) - highly modular within themselves
- **Comments**: Mostly Cyrillic; preserve language in commits
- **Error handling**: Typically silent with fallbacks (check for file existence before include)
- **Security**: Input sanitization in `functions.php` (search `el_strongcleanvars`)

## Common Development Tasks

### Adding a New AJAX Handler
1. Create `/modules/ajaxHandlers/{actionName}.php`
2. Handler runs with global scope (has access to `$_POST`, `$_SESSION`, database)
3. Output JSON or HTML; use `json_encode()` for API responses
4. Include `dbconn.php` if not auto-loaded

**Example structure**:
```php
<?php
// Handler automatically included with $db available
// Use: $_POST['field'] for data
// Return JSON: echo json_encode(['status' => 'ok']);
```

### Editing Admin Interface
1. Files are in `/editor/` 
2. Linked from `menuadmin.php` (menu navigation)
3. Use session to check permissions (check `$_SESSION` for user role)

### Modifying Database Schema
1. Update `/p84699_db.sql` with schema changes
2. Document the change in a comment with date/reason
3. Test queries in both `db.php` and RedBeanPHP patterns

### Adding Composer Dependencies
1. Update `/modules/composer.json`
2. Run `composer install` in `/modules/`
3. Include via `require_once($_SERVER['DOCUMENT_ROOT'].'/modules/vendor/autoload.php');`

## Important Files & Their Purpose

| File | Purpose | Size/Complexity |
|------|---------|----------------|
| `functions.php` | Centralized utility functions (140KB+) | Very large - search before adding |
| `gui.php` | HTML/form generation helpers (145KB+) | Large - check existing patterns |
| `db.php` | Database query builder (13KB) | Medium - primary DB interface |
| `rb.php` | RedBeanPHP ORM (484KB) | Monolithic - for complex queries |
| `index.php` | Main router/entry point | Small - core logic |
| `dbconn.php` | Database initialization | Small - connection string here |

## Best Practices

### Do
✅ Search `functions.php` and `gui.php` before writing utilities  
✅ Use `str_replace()` for path sanitization (existing pattern in `index.php`)  
✅ Check file existence before `include` (prevents silent errors from becoming confusing)  
✅ Preserve Cyrillic comments and variable names (important for team context)  
✅ Test AJAX handlers with real POST requests (browser DevTools Network tab)  
✅ Keep handlers thin - delegate logic to `functions.php`  

### Avoid
❌ Adding massive amounts of inline SQL - use existing query helpers  
❌ Modifying `rb.php` (external library - update via Composer)  
❌ Global POST/GET without sanitization (use utilities in `functions.php`)  
❌ Creating new database connection files (use `dbconn.php`)  
❌ Breaking existing AJAX routing in `index.php`  

## Branch & Development Context

- **Current branch**: `fix/admin-votes` (voting system modifications)
- **Team conventions**: Follow existing naming patterns in AJAX handlers
- **Code reviews**: Check for SQL injection / XSS in user input handling

## Troubleshooting

**AJAX handler not executing?**
- Verify file exists at `/modules/ajaxHandlers/{action}.php`
- Check `$_POST['action']` matches filename (no dots/slashes)
- Look for `exit;` or early `return` blocking execution

**Database query failing?**
- Check connection in `/Connections/dbconn.php`
- Verify table/column names match schema in `p84699_db.sql`
- Test query syntax with both `db.php` and raw SQL first

**Template/dialog not loading?**
- For popups: check file exists at `/tmpl/page/dialogs/{url}.php`
- For pages: check `/tmpl/page/{template}.php`
- Verify modal mode in POST: `$_POST['mode'] == 'popup'`

## Additional Resources

- Database schema: [p84699_db.sql](/workspaces/Poranam/p84699_db.sql)
- Composer dependencies: [modules/composer.json](modules/composer.json)
- Web server config: [.htaccess](.htaccess) (Apache mod_expires caching rules)
