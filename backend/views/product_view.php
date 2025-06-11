<?php
function isProductAvailable(array $product): bool {
    return isset($product['stock']) && (int)$product['stock'] > 0;
}

function renderAddToCartControls(array $product): string {
    if (!isProductAvailable($product)) {
        return <<<HTML
            <div class="d-grid gap-2 mb-4 w-100 max-w-200">
              <button class="btn btn-secondary btn-lg" disabled>Немає в наявності</button>
              <a href="/public/index.php" class="btn btn-outline-secondary">Повернутися до списку</a>
            </div>
        HTML;
    }

    $stock = (int)$product['stock'];
    $id    = (int)($product['id'] ?? 0);

    return <<<HTML
        <form action="/public/orders/cart.php" method="get" class="mb-4">
          <input type="hidden" name="action" value="add">
          <input type="hidden" name="id" value="{$id}">
          <div class="input-group mb-3 max-w-140">
            <button type="button" class="btn btn-outline-secondary" onclick="changeQuantity(-1)">−</button>
            <input type="number" id="quantity" name="quantity"
                   class="form-control text-center"
                   min="1" max="{$stock}" value="1">
            <button type="button" class="btn btn-outline-secondary" onclick="changeQuantity(1)">＋</button>
          </div>
          <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary btn-lg">Додати в кошик</button>
            <a href="/public/index.php" class="btn btn-outline-secondary">Повернутися до списку</a>
          </div>
        </form>
    HTML;
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($product['name'] ?? 'Товар') ?> – Ноутбук-Маркет</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="/public/assets/css/style.css" rel="stylesheet">
  <link href="/public/assets/css/gallery.css" rel="stylesheet">
  <link href="/public/assets/css/product.css" rel="stylesheet">
  <script type="module" src="/public/assets/js/ui/imageHandlers.js" defer></script>
  <style>
    .max-w-140 { max-width: 140px; }
    .max-w-80  { max-width: 80px; }
    .max-w-200 { max-width: 200px; }
  </style>
</head>
<body>
<?php if (!empty($_SESSION['error_message'])): ?>
  <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
    <?= htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="/public/index.php">Ноутбук-Маркет</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="/public/index.php">Головна</a></li>
      </ul>
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="/public/orders/cart.php">
            Кошик <span class="badge bg-primary"><?= count($_SESSION['cart'] ?? []) ?></span>
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container my-5">
  <div class="row gy-4">
    <div class="col-md-6">
      <img src="<?= htmlspecialchars($mainImage ?? '/public/assets/img/default.jpg') ?>"
           class="img-fluid rounded main-product-image"
           alt="<?= htmlspecialchars($product['name'] ?? '') ?>">

      <?php if (!empty($images) && count($images) > 1): ?>
        <div class="image-thumbnails mt-3 d-flex flex-wrap">
          <?php foreach ($images as $img): ?>
            <img src="<?= htmlspecialchars($img) ?>" class="img-thumbnail me-2 mb-2 max-w-80" alt="thumbnail">
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <div class="col-md-6">
      <div class="mb-3"><?= renderProductBadges($product) ?></div>
      <h1 class="h2"><?= htmlspecialchars($product['name'] ?? '') ?></h1>
      <p class="fs-3 fw-bold text-primary">
        <?= formatProductPrice($product['price'] ?? 0) ?> грн
      </p>
      <p class="<?= getProductAvailabilityClass((int)($product['stock'] ?? 0)) ?>">
        <?= isProductAvailable($product)
              ? "В наявності: " . (int)$product['stock'] . " шт."
              : "Немає в наявності" ?>
      </p>

      <?= renderAddToCartControls($product) ?>
    </div>
  </div>

  <div class="row mt-5">
    <div class="col">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">Детальний опис</div>
        <div class="card-body">
          <p class="mb-0">
            <?= nl2br(htmlspecialchars($product['full_description'] ?? 'Детальний опис відсутній')) ?>
          </p>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="image-zoom-container"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
<script src="/public/assets/js/product.js" defer></script>
<script type="module" defer>
  import { initImageZoom, changeMainImage } from '/public/assets/js/ui/imageHandlers.js';

  document.addEventListener('DOMContentLoaded', () => {
    initImageZoom();
    document.querySelectorAll('.image-thumbnails img').forEach(img => {
      img.onclick = () => changeMainImage(img);
    });
  });
</script>
</body>
</html>
