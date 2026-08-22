<?php
include_once('database/conexao.php');
include_once('include/funcoes.php');
$_SESSION['id'] = 1; // Simulate login

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Test 1: All time
$parametros = ['admin', 'extrato'];
ob_start();
include('pages/adminextrato.php');
$out1 = ob_get_clean();
echo "Test 1 (All time) output length: " . strlen($out1) . "\n";

// Test 2: Year 2025
$parametros = ['admin', 'extrato', '2025'];
ob_start();
include('pages/adminextrato.php');
$out2 = ob_get_clean();
echo "Test 2 (Year 2025) output length: " . strlen($out2) . "\n";

// Test 3: Month 2025/08
$parametros = ['admin', 'extrato', '2025', '08'];
ob_start();
include('pages/adminextrato.php');
$out3 = ob_get_clean();
echo "Test 3 (Month 2025/08) output length: " . strlen($out3) . "\n";
