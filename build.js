#!/usr/bin/env node

/**
 * Build script for mod_cinetixx_events
 * Creates a ZIP file ready for Joomla installation
 */

const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

const moduleName = 'mod_cinetixx_events';
const version = require('./package.json').version;
const outputFile = `${moduleName}-${version}.zip`;

// Files and folders to exclude from the package
const excludePatterns = [
    '.idea',
    '.git',
    '.gitignore',
    '.github',
    '.changelogrc',
    '.env',
    'node_modules',
    'build.js',
    'package.json',
    'pnpm-lock.yaml',
    'CHANGELOG.md',
    'README.md',
    '*.zip'
];

// Build the exclude arguments for zip command
const excludeArgs = excludePatterns.map(pattern => `-x "*/${pattern}/*" "*${pattern}*"`).join(' ');

console.log(`Building ${moduleName} v${version}...`);

try {
    // Remove old zip file if it exists
    if (fs.existsSync(outputFile)) {
        fs.unlinkSync(outputFile);
        console.log(`Removed old ${outputFile}`);
    }

    // Create the zip file
    const zipCommand = `zip -r ${outputFile} . ${excludeArgs}`;
    console.log('Creating ZIP archive...');
    execSync(zipCommand, { stdio: 'inherit' });

    console.log(`\n✓ Package created: ${outputFile}`);
    console.log(`✓ Ready for Joomla installation`);

} catch (error) {
    console.error('Error creating package:', error.message);
    process.exit(1);
}
