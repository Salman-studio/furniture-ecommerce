<?php
// furniture/faq.php
$pageTitle = 'Frequently Asked Questions';
require_once __DIR__ . '/includes/header.php';
?>
<style>
    .faq-section {
        max-width: 800px;
        margin: 2rem auto;
        padding: 2rem;
        background-color: #f8f9fa;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .faq-section h3 {
        font-size: 2.5rem;
        color: #2c3e50;
        margin-bottom: 1.5rem;
        text-align: center;
    }

    .faq-item {
        margin-bottom: 1rem;
    }

    .faq-item dt {
        font-size: 1.2rem;
        font-weight: bold;
        color: #2c3e50;
        cursor: pointer;
        padding: 0.75rem;
        background-color: #ffffff;
        border-radius: 4px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
        transition: background-color 0.3s ease;
    }

    .faq-item dt:hover {
        background-color: #e9ecef;
    }

    .faq-item dt span {
        font-size: 1rem;
        color: #3498db;
    }

    .faq-item dd {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #34495e;
        padding: 1rem;
        background-color: #ffffff;
        border-radius: 4px;
        margin-top: 0.5rem;
        display: none;
    }

    .faq-item.active dd {
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
        .faq-section {
            padding: 1rem;
        }

        .faq-section h3 {
            font-size: 2rem;
        }

        .faq-item dt {
            font-size: 1.1rem;
        }

        .faq-item dd {
            font-size: 1rem;
        }
    }
</style>

<div class="faq-section">
    <h3>Frequently Asked Questions</h3>
    <?php
    $faqs = [
        [
            'question' => 'What materials do you use for your furniture?',
            'answer' => 'We use solid wood, premium upholstery fabrics, and sustainable materials such as reclaimed timber and eco-friendly finishes. Each piece is crafted to ensure durability and aesthetic appeal.'
        ],
        [
            'question' => 'Do you offer repairs for NoirLuxe furniture?',
            'answer' => 'Yes, we provide repair services for all NoirLuxe furniture. Visit our <a href="repair.php">Repair page</a> for more details or contact us to schedule a consultation.'
        ],
        [
            'question' => 'Can I customize the size and color of my furniture?',
            'answer' => 'Absolutely! Our custom work process allows you to choose dimensions, colors, fabrics, and finishes to match your vision. Learn more on our <a href="custom-work.php">Custom Work page</a>.'
        ],
        [
            'question' => 'How long does it take to receive a custom order?',
            'answer' => 'Custom orders typically take 4-6 weeks for standard shipping and 5-7 weeks for white glove delivery, depending on your location and order specifications.'
        ],
        [
            'question' => 'What is your return policy?',
            'answer' => 'We accept returns for non-custom items within 30 days of delivery, provided they are in original condition. Custom orders are non-returnable but can be repaired or adjusted if needed.'
        ],
        [
            'question' => 'Do you ship internationally?',
            'answer' => 'Yes, we offer international shipping to select countries. Please contact us for availability, costs, and estimated delivery times.'
        ]
    ];
    foreach ($faqs as $faq) {
        echo "
            <div class='faq-item'>
                <dt onclick='toggleFAQ(this)'>{$faq['question']}<span>▼</span></dt>
                <dd>{$faq['answer']}</dd>
            </div>";
    }
    ?>
    <div class="cta-section">
        <p>Have more questions? We’re here to help.</p>
        <a href="contact.php" class="cta-button">Contact Us</a>
    </div>
</div>

<script>
    function toggleFAQ(element) {
        const faqItem = element.parentElement;
        const isActive = faqItem.classList.contains('active');
        document.querySelectorAll('.faq-item').forEach(item => {
            item.classList.remove('active');
            item.querySelector('dt span').innerHTML = '▼';
        });
        if (!isActive) {
            faqItem.classList.add('active');
            element.querySelector('span').innerHTML = '▲';
        }
    }
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>