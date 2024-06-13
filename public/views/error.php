<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Error</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      margin: 0;
      padding: 0;
      background-color: #0e130e;
      color:#f4f4f4;
    }

    .container {
      max-width: 800px;
      margin: 100px auto;
      padding: 20px;
      background-color: #253125;
      border-radius: 5px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    h1 {
      color: #f4f4f4;
    }

    p {
      margin: 20px 0;
    }
  </style>
</head>

<body>
  <div class="container">
    <h1>
      Error
      <?= $code; ?>
    </h1>
    <?php foreach ($messages as $message): ?>
      <p><?= $message; ?></p>
    <?php endforeach; ?>
  </div>
</body>

</html>