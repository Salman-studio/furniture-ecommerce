<?php
// furniture/locations.php
$pageTitle = 'Our Store Locations';
require_once __DIR__ . '/includes/header.php';
?>
<style>
    .locations-section {
        max-width: 1000px;
        margin: 2rem auto;
        padding: 2rem;
        background-color: #f8f9fa;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .locations-section h3 {
        font-size: 2.5rem;
        color: #2c3e50;
        margin-bottom: 1.5rem;
        text-align: center;
    }

    .locations-section p {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #34495e;
        margin-bottom: 1rem;
        text-align: center;
    }

    .location-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-top: 2rem;
    }

    .location-card {
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
        padding: 1.5rem;
        transition: transform 0.3s ease;
    }

    .location-card:hover {
        transform: translateY(-5px);
    }

    .location-card h4 {
        font-size: 1.3rem;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }

    .location-card p {
        font-size: 1rem;
        color: #34495e;
        text-align: left;
        margin-bottom: 0.75rem;
    }

    .location-card a {
        color: #3498db;
        text-decoration: none;
        font-weight: bold;
    }

    .location-card a:hover {
        text-decoration: underline;
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
        .locations-section {
            padding: 1rem;
        }

        .locations-section h3 {
            font-size: 2rem;
        }

        .location-list {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="locations-section">
    <h3>Our Store Locations</h3>
    <p>Visit our showrooms in Mumbai and Navi Mumbai to experience the elegance and craftsmanship of NoirLuxe furniture firsthand. Our friendly staff is ready to assist you with custom orders, repairs, or design consultations.</p>

    <div class="location-list">
        <?php
        $locations = [
            [
                'name' => 'NoirLuxe Bandra',
                'address' => 'Shop No. 12, Hill Road, Bandra West, Mumbai, Maharashtra 400050',
                'phone' => '+91-22-2640-1234',
                'email' => 'bandra@noirluxe.com',
                'hours' => 'Mon-Sat: 10:00 AM - 8:00 PM<br>Sun: 11:00 AM - 6:00 PM'
            ],
            [
                'name' => 'NoirLuxe Juhu',
                'address' => 'Unit 5, Juhu Tara Road, Juhu, Mumbai, Maharashtra 400049',
                'phone' => '+91-22-2610-5678',
                'email' => 'juhu@noirluxe.com',
                'hours' => 'Mon-Sat: 10:00 AM - 8:00 PM<br>Sun: Closed'
            ],
            [
                'name' => 'NoirLuxe Vashi',
                'address' => 'Plot No. 17, Sector 19D, Vashi, Navi Mumbai, Maharashtra 400703',
                'phone' => '+91-22-2789-9012',
                'email' => 'vashi@noirluxe.com',
                'hours' => 'Mon-Sat: 10:00 AM - 7:00 PM<br>Sun: 11:00 AM - 5:00 PM'
            ],
            [
                'name' => 'NoirLuxe Thane',
                'address' => 'Shop No. 8, Viviana Mall, Thane West, Navi Mumbai, Maharashtra 400606',
                'phone' => '+91-22-6170-3456',
                'email' => 'thane@noirluxe.com',
                'hours' => 'Mon-Sun: 11:00 AM - 9:00 PM'
            ]
        ];
        foreach ($locations as $location) {
            echo "
                <div class='location-card'>
                    <h4>{$location['name']}</h4>
                    <p><strong>Address:</strong> {$location['address']}</p>
                    <p><strong>Phone:</strong> <a href='tel:{$location['phone']}'>{$location['phone']}</a></p>
                    <p><strong>Email:</strong> <a href='mailto:{$location['email']}'>{$location['email']}</a></p>
                    <p><strong>Hours:</strong> {$location['hours']}</p>
                </div>";
        }
        ?>
    </div>

    <div class="cta-section">
        <p>Can’t visit us in person? Contact us online for inquiries or custom orders.</p>
        <a href="contact.php" class="cta-button">Get in Touch</a>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>