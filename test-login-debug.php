<?php
/**
 * Test script untuk debug login di PHP 7.4
 * Verifikasi: Database::row() returns null (bukan false) ketika tidak ada hasil
 */

require_once 'Autoloader.php';

use App\Database;

echo "=== Testing Database::row() return type ===\n";

// Test 1: Query yang returns nothing
$result1 = Database::row("SELECT * FROM users WHERE username = ?", ['nonexistent_user']);
echo "Test 1 - Non-existent user:\n";
echo "  Type: " . gettype($result1) . "\n";
echo "  Value: " . var_export($result1, true) . "\n";
echo "  === NULL check: " . ($result1 === null ? "TRUE ✓" : "FALSE ✗") . "\n";
echo "  === Empty check: " . (empty($result1) ? "TRUE" : "FALSE") . "\n\n";

// Test 2: Query yang returns row
$result2 = Database::row("SELECT * FROM users LIMIT 1");
echo "Test 2 - First user (if exists):\n";
echo "  Type: " . gettype($result2) . "\n";
if ($result2) {
    echo "  ID: " . $result2->id . "\n";
    echo "  Username: " . $result2->username . "\n";
} else {
    echo "  No user found\n";
}
echo "\n";

// Test 3: Test Auth flow
echo "=== Testing Auth::authenticate() ===\n";
$username = 'test'; // Ganti dengan username yang ada
$password = 'password'; // Ganti dengan password yang benar

$user = \App\Models\User::authenticate($username, $password);
echo "Result type: " . gettype($user) . "\n";
echo "Result: " . var_export($user, true) . "\n";

if ($user) {
    echo "✓ Authentication successful\n";
} else {
    echo "✗ Authentication failed\n";
}
?>
