<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Erreur') ?></title>
    <style>
        body {
            font-family: sans-serif;
            padding: 40px;
            background: #fafafa;
        }
        .error-box {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        pre {
            background: #eee;
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="error-box">
        <?php require $viewFile; ?>
    </div>
</body>
</html>
