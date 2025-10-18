<?php
// furniture/custom-work.php
$pageTitle = 'Custom Work';
require_once __DIR__ . '/includes/header.php';
?>
<style>
    .custom-work-section {
        max-width: 1000px;
        margin: 2rem auto;
        padding: 2rem;
        background-color: #f8f9fa;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .custom-work-section h3 {
        font-size: 2.5rem;
        color: #2c3e50;
        margin-bottom: 1.5rem;
        text-align: center;
    }

    .custom-work-section p {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #34495e;
        margin-bottom: 1rem;
    }

    .gallery {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        padding: 1rem 0;
    }

    .gallery-item {
        position: relative;
        overflow: hidden;
        border-radius: 8px;
        cursor: pointer;
    }

    .gallery-item img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .gallery-item:hover img {
        transform: scale(1.1);
    }

    .gallery-item .overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .gallery-item:hover .overlay {
        opacity: 1;
    }

    .process-steps {
        margin: 2rem 0;
    }

    .process-step {
        display: flex;
        align-items: center;
        margin-bottom: 1.5rem;
        gap: 1rem;
    }

    .process-step h4 {
        font-size: 1.3rem;
        color: #3498db;
        margin-bottom: 0.5rem;
    }

    .cta-section {
        text-align: center;
        margin-top: 2rem;
    }

    .cta-button {
        background-color: #3498db;
        color: white;
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 4px;
        font-size: 1.1rem;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
    }

    .cta-button:hover {
        background-color: #2980b9;
    }

    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.8);
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .modal img {
        max-width: 90%;
        max-height: 90%;
        border-radius: 8px;
    }

    .modal-close {
        position: absolute;
        top: 20px;
        right: 20px;
        color: white;
        font-size: 2rem;
        cursor: pointer;
    }

    @media (max-width: 600px) {
        .custom-work-section {
            padding: 1rem;
        }

        .custom-work-section h3 {
            font-size: 2rem;
        }

        .process-step {
            flex-direction: column;
            text-align: center;
        }
    }
</style>

<div class="custom-work-section">
    <h3>Custom Furniture Creations</h3>
    <p>At NoirLuxe, we specialize in crafting bespoke furniture tailored to your vision. From luxurious sofas to elegant dining tables, our custom work process ensures every piece is a unique masterpiece, designed to complement your space and style.</p>

    <div class="gallery">
        <?php
        $galleryItems = [
            ['src' => 'https://via.placeholder.com/300x200?text=Custom+Sofa', 'alt' => 'Custom Sofa', 'title' => 'Bespoke Velvet Sofa'],
            ['src' => 'https://via.placeholder.com/300x200?text=Custom+Chair', 'alt' => 'Custom Chair', 'title' => 'Modern Leather Chair'],
            ['src' => 'https://via.placeholder.com/300x200?text=Custom+Table', 'alt' => 'Custom Table', 'title' => 'Handcrafted Oak Table'],
            ['src' => 'https://via.placeholder.com/300x200?text=Custom+Bookshelf', 'alt' => 'Custom Bookshelf', 'title' => 'Minimalist Bookshelf']
        ];
        foreach ($galleryItems as $item) {
            echo "
                <div class='gallery-item'>
                    <img src='{$item['src']}' alt='{$item['alt']}' onclick='openModal(this.src)'>
                    <div class='overlay'>{$item['title']}</div>
                </div>";
        }
        ?>
    </div>

    <div class="process-steps">
        <h3>Our Custom Work Process</h3>
        <div class="process-step">
            <div>
                <h4>1. Consultation</h4>
                <p>We discuss your ideas, preferences, and requirements to create a personalized design plan.</p>
            </div>
        </div>
        <div class="process-step">
            <div>
                <h4>2. Design & Material Selection</h4>
                <p>Choose from premium materials and collaborate on a design that reflects your style.</p>
            </div>
        </div>
        <div class="process-step">
            <div>
                <h4>3. Craftsmanship</h4>
                <p>Our skilled artisans bring your vision to life with precision and care.</p>
            </div>
        </div>
        <div class="process-step">
            <div>
                <h4>4. Delivery & Installation</h4>
                <p>We deliver and install your custom piece, ensuring it fits perfectly in your space.</p>
            </div>
        </div>
    </div>

    <div class="cta-section">
        <p>Ready to create your dream furniture? Let’s bring your vision to life.</p>
        <a href="contact.php" class="btn btn-primary-custom mt-3">Get Started</a>
    </div>
</div>

<div class="modal" id="imageModal">
    <span class="modal-close" onclick="closeModal()">&times;</span>
    <img id="modalImage" src="" alt="Modal Image">
</div>

<script>
    function openModal(src) {
        const modal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');
        modalImage.src = src;
        modal.style.display = 'flex';
    }

    function closeModal() {
        const modal = document.getElementById('imageModal');
        modal.style.display = 'none';
    }
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>