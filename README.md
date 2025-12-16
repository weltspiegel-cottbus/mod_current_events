# Current Events Module

Joomla 5/6 module to display current movies and events from the Weltspiegel component.

## Description

This module integrates with the `com_weltspiegel` component and displays current movies on your Joomla site. It reuses the component's model to fetch events from the Cinetixx API.

## Requirements

- Joomla 5.0 or higher (tested with Joomla 6)
- PHP 8.1 or higher
- `com_weltspiegel` component installed and configured

## Installation

### Manual Installation

1. Build the package:
   ```bash
   pnpm build
   ```

2. Upload the generated ZIP file via Joomla Administrator:
   - System → Extensions → Install
   - Upload `mod_current_events-*.zip`

3. Publish the module:
   - Content → Site Modules → New
   - Select "Current Events"
   - Assign to desired position
   - Publish it

### Via GitHub Releases

Download the latest release ZIP from the GitHub releases page and install via Joomla Administrator.

### Automatic Updates

Once installed, the module can be updated automatically through Joomla's update system:
- System → Update → Extensions
- Joomla checks the update server configured in the module manifest
- New versions are automatically detected and can be installed with one click

The update manifest is hosted at:
`https://raw.githubusercontent.com/weltspiegel-cottbus/mod_current_events/main/update-manifest.xml`

## Development

### Build Package

```bash
pnpm build
```

This creates a `mod_current_events-{version}.zip` file ready for Joomla installation.

### Creating Releases

This project uses [changelogen](https://github.com/unjs/changelogen) for automated changelog generation and releases based on conventional commits.

**Prerequisites:** This project uses pnpm for package management. Install it with `npm install -g pnpm` if you don't have it.

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
pnpm install
```

**Before releasing**, update version numbers in two files:

1. **`mod_current_events.xml`**:
   - Update the `<version>` tag to match the new version

2. **`update-manifest.xml`**:
   - Update the `<version>` tag to match the new version
   - Update the download URL to match the new version tag and filename

3. Commit these changes

Then create a release:

```bash
# Patch release (0.1.0 -> 0.1.1) - for bug fixes
pnpm release:patch

# Minor release (0.1.0 -> 0.2.0) - for new features
pnpm release:minor

# Major release (0.1.0 -> 1.0.0) - for breaking changes
pnpm release:major

# Auto-detect version bump from commits
pnpm release
```

This will:
1. Generate/update `CHANGELOG.md`
2. Bump version in `package.json`
3. Create a git commit with the changes
4. Create a git tag (e.g., `v0.2.0`)
5. Push to GitHub
6. Create a GitHub release with changelog
7. Trigger GitHub Actions to build and attach the ZIP file

#### Manual Changelog Generation

To generate changelog without releasing:

```bash
pnpm changelog
```

### What Gets Packaged

The build script includes only the necessary files:
- `mod_current_events.xml` - Module manifest
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

The module includes a GitHub Actions workflow (`.github/workflows/release.yml`) that automatically builds and attaches the ZIP file to releases.

#### How It Works

When you run `pnpm release:minor` (or patch/major):
1. **Changelogen** creates the GitHub release with changelog
2. **GitHub Actions** triggers automatically on release creation
3. **Workflow** builds the ZIP and attaches it to the release

The workflow:
1. Checks out the code
2. Installs dependencies with pnpm
3. Builds the installable ZIP package
4. Uploads the ZIP to the release created by changelogen

No manual intervention needed - just run the release command!

## License

MIT License - see LICENSE file for details

## Credits

Developed for Weltspiegel Cottbus cinema
