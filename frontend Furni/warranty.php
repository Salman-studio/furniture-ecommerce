<?php
// furniture/warranty.php
$pageTitle = 'Warranty Information';
require_once __DIR__ . '/includes/header.php';
?>
<style>
    .warranty-section {
        max-width: 800px;
        margin: 2rem auto;
        padding: 2rem;
        background-color: #f8f9fa;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .warranty-section h3 {
        font-size: 2.5rem;
        color: #2c3e50;
        margin-bottom: 1.5rem;
        text-align: center;
    }

    .warranty-section h4 {
        font-size: 1.6rem;
        color: #3498db;
        margin: 1.5rem 0 1rem;
    }

    .warranty-section p {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #34495e;
        margin-bottom: 1rem;
    }

    .warranty-details ul {
        list-style-type: disc;
        padding-left: 2rem;
        margin-bottom: 1rem;
    }

    .warranty-details ul li {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #34495e;
    }

    .claim-process {
        margin: 2rem 0;
    }

    .claim-step {
        display: flex;
        align-items: flex-start;
        margin-bottom: 1.5rem;
        gap: 1rem;
    }

    .claim-step h5 {
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
        .warranty-section {
            padding: 1rem;
        }

        .warranty-section h3 {
            font-size: 2rem;
        }

        .warranty-section h4 {
            font-size: 1.4rem;
        }

        .claim-step {
            flex-direction: column;
            text-align: center;
        }
    }
</style>

<div class="warranty-section">
    <h3>Warranty Information</h3>
    <p>At NoirLuxe, we stand behind the quality of our custom furniture. Our warranty ensures that your handcrafted pieces are protected against defects in materials and workmanship, giving you peace of mind with every purchase.</p>

    <div class="warranty-details">
        <h4>Warranty Coverage</h4>
        <p>All NoirLuxe furniture comes with a limited warranty, covering:</p>
        <ul>
            <li><strong>Structural Integrity:</strong> 5-year warranty on frames and internal structures for sofas, chairs, and tables.</li>
            <li><strong>Upholstery:</strong> 2-year warranty on fabric and leather upholstery against manufacturing defects.</li>
            <li><strong>Finishes:</strong> 1-year warranty on wood finishes and surface treatments.</li>
        </ul>
        <p><em>Note:</em> The warranty does not cover normal wear and tear, improper use, or damage caused by accidents, neglect, or unauthorized repairs.</p>

        <h4>What’s Not Covered</h4>
        <ul>
            <li>Damage due to misuse, improper cleaning, or exposure to extreme conditions (e.g., direct sunlight, excessive humidity).</li>
            <li>Alterations or modifications made by third parties.</li>
            <li>Natural variations in wood grain, color, or texture, which are inherent to our handcrafted products.</li>
        </ul>
    </div>

    <div class="claim-process">
        <h4>How to File a Warranty Claim</h4>
        <p>If you believe your furniture qualifies for a warranty claim, follow these steps:</p>
        <div class="claim-step">
            <div>
                <h5>1. Contact Us</h5>
                <p>Reach out to our customer service team via email or phone with your order details and a description of the issue.</p>
            </div>
        </div>
        <div class="claim-step">
            <div>
                <h5>2. Provide Documentation</h5>
                <p>Submit photos of the issue along with your purchase receipt or order confirmation.</p>
            </div>
        </div>
        <div class="claim-step">
            <div>
                <h5>3. Assessment</h5>
                <p>Our team will review your claim and may arrange an inspection if needed.</p>
            </div>
        </div>
        <div class="claim-step">
            <div>
                <h5>4. Resolution</h5>
                <p>We’ll repair, replace, or refund the affected item, based on the warranty terms.</p>
            </div>
        </div>
    </div>

    <div class="cta-section">
        <p>Need assistance with a warranty claim? Our team is here to help.</p>
        <a href="#contact" class="cta-button">Contact Us</a>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>