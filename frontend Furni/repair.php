<?php
// furniture/repair.php
$pageTitle = 'Furniture Repair Services';
require_once __DIR__ . '/includes/header.php';
?>
<style>
    .repair-section {
        max-width: 800px;
        margin: 2rem auto;
        padding: 2rem;
        background-color: #f8f9fa;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .repair-section h3 {
        font-size: 2.5rem;
        color: #2c3e50;
        margin-bottom: 1.5rem;
        text-align: center;
    }

    .repair-section h4 {
        font-size: 1.6rem;
        color: #3498db;
        margin: 1.5rem 0 1rem;
    }

    .repair-section p {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #34495e;
        margin-bottom: 1rem;
    }

    .repair-details ul {
        list-style-type: disc;
        padding-left: 2rem;
        margin-bottom: 1rem;
    }

    .repair-details ul li {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #34495e;
    }

    .repair-process {
        margin: 2rem 0;
    }

    .repair-step {
        display: flex;
        align-items: flex-start;
        margin-bottom: 1.5rem;
        gap: 1rem;
    }

    .repair-step h5 {
        font-size: 1.2rem;
        color: #2c3e50;
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

    @media (max-width: 600px) {
        .repair-section {
            padding: 1rem;
        }

        .repair-section h3 {
            font-size: 2rem;
        }

        .repair-section h4 {
            font-size: 1.4rem;
        }

        .repair-step {
            flex-direction: column;
            text-align: center;
        }
    }
</style>

<div class="repair-section">
    <h3>Furniture Repair Services</h3>
    <p>At NoirLuxe, we understand that your furniture is an investment in quality and style. Our expert repair services are designed to restore your pieces to their original beauty, whether they need minor touch-ups or extensive refurbishment.</p>

    <div class="repair-details">
        <h4>Our Repair Services</h4>
        <p>We offer comprehensive repair solutions for all NoirLuxe furniture, including:</p>
        <ul>
            <li><strong>Upholstery Repair:</strong> Fixing tears, reupholstering worn fabrics, or replacing faded leather.</li>
            <li><strong>Structural Repairs:</strong> Reinforcing frames, tightening joints, or repairing broken components.</li>
            <li><strong>Surface Refinishing:</strong> Restoring wood finishes, removing scratches, or refinishing to match your decor.</li>
            <li><strong>Custom Restoration:</strong> Reviving heirloom pieces or customizing existing furniture to your specifications.</li>
        </ul>
        <p><em>Note:</em> Repairs covered under warranty are processed at no cost, subject to our <a href="warranty.php">warranty terms</a>. Non-warranty repairs are quoted based on the scope of work.</p>
    </div>

    <div class="repair-process">
        <h4>How to Request a Repair</h4>
        <p>Our repair process is simple and customer-focused:</p>
        <div class="repair-step">
            <div>
                <h5>1. Submit a Request</h5>
                <p>Contact our team via email or phone with details of the damage and your order information.</p>
            </div>
        </div>
        <div class="repair-step">
            <div>
                <h5>2. Send Photos</h5>
                <p>Provide clear photos of the furniture and the issue to help us assess the repair needs.</p>
            </div>
        </div>
        <div class="repair-step">
            <div>
                <h5>3. Receive a Quote</h5>
                <p>We’ll provide a detailed quote for non-warranty repairs or confirm warranty coverage.</p>
            </div>
        </div>
        <div class="repair-step">
            <div>
                <h5>4. Repair & Delivery</h5>
                <p>Our artisans will restore your piece, and we’ll arrange for pickup or delivery as needed.</p>
            </div>
        </div>
    </div>

    <div class="cta-section">
        <p>Ready to restore your furniture? Let us bring it back to life.</p>
        <a href="contact.php" class="cta-button">Request a Repair</a>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>