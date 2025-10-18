<?php
// furniture/shipping.php
$pageTitle = 'Shipping & Delivery';
require_once __DIR__ . '/includes/header.php';
?>
<style>
    .shipping-section {
        max-width: 800px;
        margin: 2rem auto;
        padding: 2rem;
        background-color: #f8f9fa;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .shipping-section h3 {
        font-size: 2.5rem;
        color: #2c3e50;
        margin-bottom: 1.5rem;
        text-align: center;
    }

    .shipping-section h4 {
        font-size: 1.6rem;
        color: #3498db;
        margin: 1.5rem 0 1rem;
    }

    .shipping-section p {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #34495e;
        margin-bottom: 1rem;
    }

    .shipping-details {
        margin: 2rem 0;
    }

    .shipping-details ul {
        list-style-type: disc;
        padding-left: 2rem;
        margin-bottom: 1rem;
    }

    .shipping-details ul li {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #34495e;
    }

    .faq-section {
        margin-top: 2rem;
    }

    .faq-item {
        margin-bottom: 1.5rem;
    }

    .faq-item h5 {
        font-size: 1.2rem;
        color: #2c3e50;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem;
        background-color: #ffffff;
        border-radius: 4px;
        box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
    }

    .faq-item h5:hover {
        background-color: #e9ecef;
    }

    .faq-item p {
        display: none;
        padding: 1rem;
        background-color: #ffffff;
        border-radius: 4px;
        margin-top: 0.5rem;
    }

    .faq-item.active p {
        display: block;
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

    @media (max-width: 600px) {
        .shipping-section {
            padding: 1rem;
        }

        .shipping-section h3 {
            font-size: 2rem;
        }

        .shipping-section h4 {
            font-size: 1.4rem;
        }
    }
</style>

<div class="shipping-section">
    <h3>Shipping & Delivery</h3>
    <p>At NoirLuxe, we are committed to delivering your custom furniture with care and efficiency. Our shipping process ensures your handcrafted pieces arrive safely and on time, wherever you are.</p>

    <div class="shipping-details">
        <h4>Shipping Options</h4>
        <ul>
            <li><strong>Standard Shipping:</strong> Available for all furniture items within the continental U.S., with delivery in 4-6 weeks for custom orders.</li>
            <li><strong>White Glove Delivery:</strong> Includes in-home setup and debris removal, available in select regions, with delivery in 5-7 weeks.</li>
            <li><strong>International Shipping:</strong> Available to select countries. Contact us for details and timelines.</li>
        </ul>

        <h4>Shipping Costs</h4>
        <p>Shipping costs are calculated based on the size, weight, and destination of your order. You’ll receive a detailed quote during the checkout process. Free standard shipping is available on select items for orders over $2,000 within the U.S.</p>

        <h4>Tracking & Updates</h4>
        <p>Once your order ships, you’ll receive a tracking number via email. Our team is available to provide updates and assist with any delivery concerns.</p>
    </div>

    <div class="faq-section">
        <h4>Frequently Asked Questions</h4>
        <div class="faq-item">
            <h5 onclick="toggleFAQ(this)">How long does delivery take for custom furniture? <span>▼</span></h5>
            <p>Custom furniture typically takes 4-6 weeks for standard shipping and 5-7 weeks for white glove delivery, depending on your location and order specifications.</p>
        </div>
        <div class="faq-item">
            <h5 onclick="toggleFAQ(this)">Can I track my order? <span>▼</span></h5>
            <p>Yes, you’ll receive a tracking number via email once your order ships. You can also contact our support team for real-time updates.</p>
        </div>
        <div class="faq-item">
            <h5 onclick="toggleFAQ(this)">Do you offer international shipping? <span>▼</span></h5>
            <p>We offer international shipping to select countries. Please reach out to our team for availability and estimated costs.</p>
        </div>
        <div class="faq-item">
            <h5 onclick="toggleFAQ(this)">What if my furniture arrives damaged? <span>▼</span></h5>
            <p>In the rare event of damage, contact us within 48 hours of delivery. We’ll arrange a replacement or repair at no additional cost.</p>
        </div>
    </div>

    <div class="cta-section">
        <p>Have questions about shipping? We’re here to help.</p>
        <a href="contact.php" class="cta-button">Contact Us</a>
    </div>
</div>

<script>
    function toggleFAQ(element) {
        const faqItem = element.parentElement;
        const isActive = faqItem.classList.contains('active');
        document.querySelectorAll('.faq-item').forEach(item => {
            item.classList.remove('active');
            item.querySelector('h5 span').innerHTML = '▼';
        });
        if (!isActive) {
            faqItem.classList.add('active');
            element.querySelector('span').innerHTML = '▲';
        }
    }
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>