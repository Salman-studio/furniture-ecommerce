<?php
// furniture/about.php
$pageTitle = 'About NoirLuxe';
require_once __DIR__ . '/includes/header.php';
?>
<style>
    .about-section {
        max-width: 800px;
        margin: 2rem auto;
        padding: 2rem;
        background-color: #f8f9fa;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .about-section h3 {
        font-size: 2.5rem;
        color: #2c3e50;
        margin-bottom: 1.5rem;
        text-align: center;
    }

    .about-section p {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #34495e;
        margin-bottom: 1rem;
    }

    .values-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin: 2rem 0;
    }

    .value-item {
        background-color: #ffffff;
        padding: 1.5rem;
        border-radius: 8px;
        text-align: center;
        box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
    }

    .value-item h4 {
        font-size: 1.3rem;
        color: #3498db;
        margin-bottom: 0.5rem;
    }

    .team-section {
        margin-top: 2rem;
        text-align: center;
    }

    .team-section h4 {
        font-size: 1.8rem;
        color: #2c3e50;
        margin-bottom: 1rem;
    }

    .team-member {
        display: inline-block;
        margin: 1rem;
    }

    .team-member img {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 0.5rem;
    }

    @media (max-width: 600px) {
        .about-section {
            padding: 1rem;
        }

        .about-section h3 {
            font-size: 2rem;
        }
    }
</style>

<div class="about-section">
    <h3>About NoirLuxe</h3>
    <p>At NoirLuxe, we believe furniture is more than just function—it's an expression of style, comfort, and craftsmanship. Founded in 2010, our passion for creating premium furniture blends modern aesthetics with time-honored techniques. Every sofa, chair, and table is handcrafted with precision, using sustainable materials to ensure lasting beauty and durability.</p>
    <p>Our mission is to transform living spaces into elegant, personalized havens. Whether it's a sleek modern sofa or a bespoke dining table, we design pieces that reflect your unique taste while maintaining unparalleled quality.</p>

    <div class="values-grid">
        <div class="value-item">
            <h4>Craftsmanship</h4>
            <p>Each piece is meticulously crafted by skilled artisans with decades of experience.</p>
        </div>
        <div class="value-item">
            <h4>Sustainability</h4>
            <p>We source eco-friendly materials to create furniture that’s kind to the planet.</p>
        </div>
        <div class="value-item">
            <h4>Innovation</h4>
            <p>Modern design meets functionality to elevate your living experience.</p>
        </div>
    </div>

    <div class="team-section">
        <h4>Our Team</h4>
        <p>Meet the passionate individuals behind NoirLuxe’s creations.</p>
        <div class="team-member">
            <img src="https://via.placeholder.com/100?text=Founder" alt="Founder">
            <p>Salman Ansari- Founder</p>
        </div>
        <div class="team-member">
            <img src="https://via.placeholder.com/100?text=Designer" alt="Designer">
            <p>Salman Ansari - Lead Designer</p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>