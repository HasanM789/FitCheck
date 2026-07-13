<?php 
require_once('db_config.php'); 
include('header.php'); 
?>

<section class="hero-section">
    <div class="hero-content">
        <h1>STYLE EACH MOMENT</h1>
        <p>Affordable, daily fashion essentials designed for your lifestyle.</p>
        <a href="catalog.php" class="hero-button">Shop New Arrivals</a>
    </div>
</section>

<div class="collection-showcase" style="text-align: center; padding: 100px 8%;">
    <h2 class="section-title" style="margin-bottom: 50px;">Explore Collections</h2>
    <div style="display: flex; justify-content: center; gap: 60px;">
        <a href="catalog.php?category=Tops" class="nav-text-link">Tops</a>
        <a href="catalog.php?category=Bottoms" class="nav-text-link">Bottoms</a>
        <a href="catalog.php?category=Hoodies" class="nav-text-link">Hoodies</a>
        <a href="catalog.php?category=Shoes" class="nav-text-link">Shoes</a>
    </div>
</div>

<?php include('footer.php'); ?>