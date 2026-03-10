---
name: releasing-with-changelogger
description: "Creates a release with Changelogger by collecting unreleased changelog entries into CHANGELOG.md. Use when asked to create a release, publish a version, or generate a changelog for a new version."
---

# Releasing with Changelogger

Generates a new release section in `CHANGELOG.md` by collecting all unreleased changelog entries from `changelogs/unreleased/`.

## When to Act

- When explicitly asked to create a release or generate a changelog for a new version.
- When preparing a version bump or tag.

## Workflow

### 1. Review Unreleased Changes

Before releasing, check what unreleased entries exist:

```bash
changelogger show
```

This displays all pending changelog entries in a table. If there are no entries, there is nothing to release.

### 2. Verify CHANGELOG.md Contains the Marker

The `CHANGELOG.md` file **must** contain the marker comment where new releases are inserted:

```markdown
<!-- CHANGELOGGER -->
```

If the file or marker does not exist, inform the user — the release command needs it to insert the new section at the correct position.

### 3. Run the Release

```bash
changelogger release <tag>
```

The `<tag>` is the version number (e.g., `v1.2.0`, `2024.03`, or any version scheme the project uses). Check existing entries in `CHANGELOG.md` to match the project's versioning convention.

This command will:
1. Collect all YAML files from `changelogs/unreleased/`.
2. Group and sort them by type (and by group, if configured).
3. Insert a new section into `CHANGELOG.md` at the `<!-- CHANGELOGGER -->` marker.
4. Delete all files in `changelogs/unreleased/`.

If only "ignore" entries exist, no changelog section is added, but the files are still cleaned up.

### 4. Verify the Result

After the release, review the generated output:

```bash
head -50 CHANGELOG.md
```

Confirm the new section looks correct and the `changelogs/unreleased/` directory is empty.

### 5. Commit the Changes

Stage both the updated `CHANGELOG.md` and the removed unreleased files:

```bash
git add CHANGELOG.md changelogs/unreleased/
git commit -m "Release <tag>"
```

Only commit if the user has asked you to. Do **not** push without explicit consent.

## Other Useful Commands

| Command                    | Description                              |
|----------------------------|------------------------------------------|
| `changelogger show`        | List all unreleased changelog entries    |
| `changelogger clean`       | Remove all unreleased entries            |
| `changelogger clean --force` | Remove without confirmation prompt     |
| `changelogger info`        | Show Changelogger version and config     |

## Important Rules

- **Never** edit `CHANGELOG.md` manually. Always use `changelogger release`.
- **Always** check `changelogger show` before releasing so you can confirm the entries with the user.
- **Match the versioning scheme** used in existing `CHANGELOG.md` entries (e.g., `v1.0.0` vs `1.0.0`).
- After a release, the `changelogs/unreleased/` directory should be empty (except `.gitkeep` if present).
