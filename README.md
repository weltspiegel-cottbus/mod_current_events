# Cinetixx Events Module

Joomla 5/6 module to display current movies and events from the Cinetixx component.

## Description

This module integrates with the `com_cinetixx` component and displays current movies on your Joomla site. It reuses the component's model to fetch events from the Cinetixx API.

## Requirements

- Joomla 5.0 or higher (tested with Joomla 6)
- PHP 8.1 or higher
- `com_cinetixx` component installed and configured

## Installation

### Manual Installation

1. Build the package:
   ```bash
   npm run build
   ```

2. Upload the generated ZIP file via Joomla Administrator:
   - System → Extensions → Install
   - Upload `mod_cinetixx_events-*.zip`

3. Publish the module:
   - Content → Site Modules → New
   - Select "Cinetixx Events"
   - Assign to desired position
   - Publish it

### Via GitHub Releases

Download the latest release ZIP from the GitHub releases page and install via Joomla Administrator.

## Development

### Build Package

```bash
npm run build
```

This creates a `mod_cinetixx_events-{version}.zip` file ready for Joomla installation.

### Creating Releases

This project uses [changelogen](https://github.com/unjs/changelogen) for automated changelog generation and releases based on conventional commits.

#### Commit Message Format

Follow conventional commits format:

```
<type>: <description>

[optional body]
```

**Types:**
- `feat:` New feature (bumps minor version)
- `fix:` Bug fix (bumps patch version)
- `perf:` Performance improvement (bumps patch version)
- `refactor:` Code refactoring (bumps patch version)
- `docs:` Documentation changes
- `build:` Build system changes
- `chore:` Maintenance tasks
- `ci:` CI/CD changes
- `style:` Code style changes
- `test:` Test changes

**Examples:**
```bash
git commit -m "feat: add filter by date option"
git commit -m "fix: correct event title escaping"
git commit -m "docs: update installation instructions"
```

#### Release Commands

Install dependencies first:
```bash
npm install
```

Then create a release:

```bash
# Patch release (0.1.0 -> 0.1.1) - for bug fixes
npm run release:patch

# Minor release (0.1.0 -> 0.2.0) - for new features
npm run release:minor

# Major release (0.1.0 -> 1.0.0) - for breaking changes
npm run release:major

# Auto-detect version bump from commits
npm run release
```

This will:
1. Generate/update `CHANGELOG.md`
2. Bump version in `package.json`
3. Create a git commit with the changes
4. Create a git tag (e.g., `v0.2.0`)
5. Push to GitHub (which triggers the CI workflow)

#### Manual Changelog Generation

To generate changelog without releasing:

```bash
npm run changelog
```

### What Gets Packaged

The build script includes only the necessary files:
- `mod_cinetixx_events.xml` - Module manifest
- `services/` - Dependency injection provider
- `src/` - Module classes (Dispatcher, Helper)
- `tmpl/` - Layout files
- `language/` - Translation files

Excluded from package:
- `.idea/` - IDE files
- `.git/` - Git repository
- `node_modules/` - Dependencies
- `build.js`, `package.json` - Build files
- `*.zip` - Previous builds

## Continuous Integration

### GitHub Actions

The module includes a GitHub Actions workflow (`.github/workflows/release.yml`) that automatically builds and publishes releases.

#### Automated Workflow (Recommended)

Use changelogen to create releases (as described above):

```bash
npm run release:minor
```

This automatically creates the tag and pushes to GitHub, triggering the workflow.

#### Manual Workflow

Alternatively, create and push a tag manually:

```bash
git tag v0.2.0
git push origin v0.2.0
```

The workflow will:
1. Checkout the code with full git history
2. Install dependencies
3. Build the package
4. Create a GitHub release with:
   - The installable ZIP file
   - Changelog content (if CHANGELOG.md exists)

## License

MIT License - see LICENSE file for details

## Credits

Developed for Weltspiegel Cottbus cinema
