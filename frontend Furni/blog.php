<?php
// furniture/blog.php
$pageTitle = 'NoirLuxe Blog';
require_once __DIR__ . '/includes/header.php';
?>
<style>
    .blog-section {
        max-width: 1000px;
        margin: 2rem auto;
        padding: 2rem;
        background-color: #f8f9fa;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .blog-section h3 {
        font-size: 2.5rem;
        color: #2c3e50;
        margin-bottom: 1.5rem;
        text-align: center;
    }

    .blog-section p {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #34495e;
        margin-bottom: 1rem;
        text-align: center;
    }

    .blog-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-top: 2rem;
    }

    .blog-card {
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: transform 0.3s ease;
    }

    .blog-card:hover {
        transform: translateY(-5px);
    }

    .blog-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .blog-card-content {
        padding: 1.5rem;
    }

    .blog-card-content h4 {
        font-size: 1.3rem;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }

    .blog-card-content .date {
        font-size: 0.9rem;
        color: #3498db;
        margin-bottom: 0.75rem;
    }

    .blog-card-content p {
        font-size: 1rem;
        color: #34495e;
        text-align: left;
        margin-bottom: 1rem;
    }

    .blog-card-content a {
        color: #3498db;
        text-decoration: none;
        font-weight: bold;
    }

    .blog-card-content a:hover {
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
        .blog-section {
            padding: 1rem;
        }

        .blog-section h3 {
            font-size: 2rem;
        }

        .blog-list {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="blog-section">
    <h3>NoirLuxe Blog</h3>
    <p>Explore our latest insights, design tips, and furniture care advice to elevate your living space.</p>

    <div class="blog-list">
        <?php
        $blogPosts = [
            [
                'title' => '5 Tips for Choosing the Perfect Sofa',
                'date' => 'October 5, 2025',
                'image' => 'https://via.placeholder.com/300x200?text=Sofa+Tips',
                'summary' => 'Discover how to select a sofa that complements your style and space, from fabric choices to size considerations.',
                'link' => 'blog-post.php?id=1'
            ],
            [
                'title' => 'Caring for Your Wooden Furniture',
                'date' => 'September 20, 2025',
                'image' => 'https://via.placeholder.com/300x200?text=Wood+Care',
                'summary' => 'Learn expert tips to maintain the beauty and longevity of your wooden furniture with proper care techniques.',
                'link' => 'blog-post.php?id=2'
            ],
            [
                'title' => 'The Art of Custom Furniture Design',
                'date' => 'September 10, 2025',
                'image' => 'https://via.placeholder.com/300x200?text=Custom+Design',
                'summary' => 'Dive into the process of creating bespoke furniture that reflects your unique taste and lifestyle.',
                'link' => 'blog-post.php?id=3'
            ],
            [
                'title' => 'Sustainable Materials in Furniture Making',
                'date' => 'August 25, 2025',
                'image' => 'https://via.placeholder.com/300x200?text=Sustainable+Furniture',
                'summary' => 'Explore how NoirLuxe uses eco-friendly materials to craft sustainable, stylish furniture.',
                'link' => 'blog-post.php?id=4'
            ]
        ];
        foreach ($blogPosts as $post) {
            echo "
                <div class='blog-card'>
                    <img src='{$post['image']}' alt='{$post['title']}'>
                    <div class='blog-card-content'>
                        <h4>{$post['title']}</h4>
                        <div class='date'>{$post['date']}</div>
                        <p>{$post['summary']}</p>
                        <a href='{$post['link']}'>Read More</a>
                    </div>
                </div>";
        }
        ?>
    </div>

    <div class="cta-section">
        <p>Have questions or need inspiration? Contact us to discuss your next project.</p>
        <a href="contact.php" class="cta-button">Get in Touch</a>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>