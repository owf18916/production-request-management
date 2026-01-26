// Script untuk copy Alpine.js dan prepare assets untuk offline deployment
const fs = require('fs');
const path = require('path');

const srcAlpine = path.join(__dirname, 'node_modules/alpinejs/dist/cdn.min.js');
const destAlpine = path.join(__dirname, 'public/assets/js/alpine.js');

// Create directories if they don't exist
const jsDir = path.dirname(destAlpine);
if (!fs.existsSync(jsDir)) {
    fs.mkdirSync(jsDir, { recursive: true });
}

// Copy Alpine.js
if (fs.existsSync(srcAlpine)) {
    fs.copyFileSync(srcAlpine, destAlpine);
    console.log('✓ Alpine.js copied to', destAlpine);
} else {
    console.log('! Alpine.js not found in node_modules');
    console.log('  Run: npm install');
}

// Check if tailwind.css exists
const cssFile = path.join(__dirname, 'public/assets/css/tailwind.css');
if (fs.existsSync(cssFile)) {
    console.log('✓ Tailwind CSS found at', cssFile);
} else {
    console.log('! Tailwind CSS not found');
    console.log('  Run: npm run build');
}

console.log('\n✓ Assets prepared for offline deployment!');
