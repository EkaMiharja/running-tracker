<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Run Tracker' ?></title>
<?php 
$docRoot = rtrim($_SERVER['DOCUMENT_ROOT'], '\\/');
$baseUrl = substr(dirname(__DIR__), strlen($docRoot));
?>
    <link rel="apple-touch-icon" sizes="180x180" href="<?= $baseUrl ?>/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= $baseUrl ?>/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= $baseUrl ?>/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="192x192" href="<?= $baseUrl ?>/android-chrome-192x192.png">
    <link rel="icon" type="image/png" sizes="512x512" href="<?= $baseUrl ?>/android-chrome-512x512.png">
    <link rel="icon" href="<?= $baseUrl ?>/favicon.ico">
    <link rel="manifest" href="<?= $baseUrl ?>/site.webmanifest">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            500: '#fc5200',
                            600: '#e04700',
                        }
                    },
                    fontFamily: {
                        inter: ['Inter', 'system-ui', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-white text-[#1F2937] font-inter">
