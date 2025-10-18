<?php
// furniture/includes/footer.php
// Site footer, closing tags, and JS includes

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$config = require __DIR__ . '/config.php';
$siteName = $config['site']['name'] ?? 'Furniture Shop';
$socialMedia = $config['social_media'] ?? [
    'facebook' => '#',
    'instagram' => '#',
    'twitter' => '#',
    'pinterest' => '#'
];
?>
  </main> <!-- /.container -->

  <!-- Newsletter Section -->
  <section class="newsletter-section bg-light py-5">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8 text-center">
          <h3 class="mb-3">Subscribe to Our Newsletter</h3>
          <p class="text-muted mb-4">Get updates on new products, exclusive offers, and interior design tips.</p>
          <form class="newsletter-form">
            <div class="row justify-content-center">
              <div class="col-12 col-md-8 mb-2">
                <input type="email" class="form-control form-control-lg" placeholder="Your email address" aria-label="Your email address">
              </div>
              <div class="col-12 col-md-4 mb-2">
                <button class="btn btn-primary btn-lg w-100" type="submit">Subscribe</button>
              </div>
            </div>
            <div class="form-check justify-content-center mt-2">
              <input class="form-check-input me-2" type="checkbox" id="newsletterConsent">
              <label class="form-check-label text-muted small" for="newsletterConsent">
                I agree to receive marketing communications from <?php echo e($siteName); ?>
              </label>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="site-footer bg-dark text-light pt-5">
    <div class="container">
      <div class="row">
        <!-- Company Info -->
        <div class="col-12 col-lg-4 mb-4">
          <div class="footer-brand mb-3">
            <span class="h4"><?php echo e($siteName); ?></span>
          </div>
          <p class="mb-3">Crafting premium furniture with attention to detail and dedication to quality since 2010.</p>
          <div class="social-links">
            <a href="<?php echo $socialMedia['facebook']; ?>" class="social-link" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
            <a href="<?php echo $socialMedia['instagram']; ?>" class="social-link" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            <a href="<?php echo $socialMedia['twitter']; ?>" class="social-link" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
            <a href="<?php echo $socialMedia['pinterest']; ?>" class="social-link" aria-label="Pinterest"><i class="fab fa-pinterest-p"></i></a>
          </div>
        </div>

        <!-- Quick Links -->
        <div class="col-6 col-md-4 col-lg-2 mb-4">
          <h5 class="footer-heading">Shop</h5>
          <ul class="list-unstyled">
            <li><a href="products.php" class="footer-link">All Products</a></li>
            <li><a href="categories.php?type=chairs" class="footer-link">Chairs</a></li>
            <li><a href="categories.php?type=tables" class="footer-link">Tables</a></li>
            <li><a href="categories.php?type=sofas" class="footer-link">Sofas</a></li>
            <li><a href="categories.php?type=storage" class="footer-link">Storage</a></li>
          </ul>
        </div>

        <!-- Customer Service -->
        <div class="col-6 col-md-4 col-lg-2 mb-4">
          <h5 class="footer-heading">Support</h5>
          <ul class="list-unstyled">
            <li><a href="contact.php" class="footer-link">Contact Us</a></li>
            <li><a href="faq.php" class="footer-link">FAQ</a></li>
            <li><a href="shipping.php" class="footer-link">Shipping & Returns</a></li>
            <li><a href="warranty.php" class="footer-link">Warranty</a></li>
            <li><a href="repair.php" class="footer-link">Repair Services</a></li>
          </ul>
        </div>

        <!-- Company -->
        <div class="col-6 col-md-4 col-lg-2 mb-4">
          <h5 class="footer-heading">Company</h5>
          <ul class="list-unstyled">
            <li><a href="about.php" class="footer-link">About Us</a></li>
            <li><a href="blog.php" class="footer-link">Blog</a></li>
            <li><a href="careers.php" class="footer-link">Careers</a></li>
            <li><a href="press.php" class="footer-link">Press</a></li>
            <li><a href="locations.php" class="footer-link">Store Locations</a></li>
          </ul>
        </div>

        <!-- Contact Info -->
        <div class="col-6 col-md-12 col-lg-2 mb-4">
          <h5 class="footer-heading">Contact</h5>
          <ul class="list-unstyled">
            <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i> 123 Furniture St, Woodville</li>
            <li class="mb-2"><i class="fas fa-phone me-2"></i> (555) 123-4567</li>
            <li class="mb-2"><i class="fas fa-envelope me-2"></i> info@<?php echo strtolower(str_replace(' ', '', $siteName)); ?>.com</li>
            <li class="mb-2"><i class="fas fa-clock me-2"></i> Mon-Fri: 9am-6pm</li>
            <li class="mb-2"><i class="fas fa-clock me-2"></i> Sat: 10am-4pm</li>
          </ul>
        </div>
      </div>

      <hr class="my-4">

      <!-- Payment Methods & Trust Seals -->
      <div class="row align-items-center">
        <div class="col-12 col-md-6 mb-3 mb-md-0">
          <div class="payment-methods">
            <span class="d-block d-md-inline me-2 mb-2 mb-md-0">We accept:</span>
            <i class="fab fa-cc-visa payment-icon"></i>
            <i class="fab fa-cc-mastercard payment-icon"></i>
            <i class="fab fa-cc-amex payment-icon"></i>
            <i class="fab fa-cc-paypal payment-icon"></i>
            <i class="fab fa-cc-apple-pay payment-icon"></i>
          </div>
        </div>
        <div class="col-12 col-md-6 text-md-end">
          <div class="trust-seals">
            <span class="badge bg-success me-1 mb-1"><i class="fas fa-lock"></i> Secure Checkout</span>
            <span class="badge bg-info me-1 mb-1"><i class="fas fa-truck"></i> Free Shipping</span>
            <span class="badge bg-warning text-dark mb-1"><i class="fas fa-shield-alt"></i> 5-Year Warranty</span>
          </div>
        </div>
      </div>

      <hr class="my-4">

      <!-- Copyright -->
      <div class="row align-items-center">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
          <p class="mb-0 text-center text-md-start">&copy; <?php echo date('Y'); ?> <?php echo e($siteName); ?>. All rights reserved.</p>
        </div>
        <div class="col-12 col-md-6 text-center text-md-end">
          <a href="privacy.php" class="footer-link me-2 me-md-3">Privacy Policy</a>
          <a href="terms.php" class="footer-link me-2 me-md-3">Terms of Service</a>
          <a href="sitemap.php" class="footer-link">Sitemap</a>
        </div>
      </div>
    </div>

    <!-- Back to Top Button -->
    <button id="backToTop" class="btn btn-primary back-to-top">
      <i class="fas fa-arrow-up"></i>
    </button>
  </footer>

  <!-- Vendor JS -->
  <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

  <!-- Local JS -->
  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>
  <script src="assets/js/script.js"></script>
  <script src="assets/js/ajax-cart.js"></script>
  <script src="assets/js/chat-widget.js"></script>
  <script src="assets/js/payment.js"></script>

  <!-- Footer-specific JS -->
  <script>
    // Back to top button
    document.addEventListener('DOMContentLoaded', function() {
      const backToTopButton = document.getElementById('backToTop');
      
      // Show/hide the button based on scroll position
      window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
          backToTopButton.classList.add('show');
        } else {
          backToTopButton.classList.remove('show');
        }
      });
      
      // Scroll to top when clicked
      backToTopButton.addEventListener('click', function() {
        window.scrollTo({
          top: 0,
          behavior: 'smooth'
        });
      });
      
      // Newsletter form validation
      const newsletterForm = document.querySelector('.newsletter-form');
      if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
          e.preventDefault();
          const emailInput = this.querySelector('input[type="email"]');
          const consentCheckbox = this.querySelector('input[type="checkbox"]');
          
          if (!emailInput.value || !isValidEmail(emailInput.value)) {
            showNewsletterMessage('Please enter a valid email address.', 'error');
            return;
          }
          
          if (!consentCheckbox.checked) {
            showNewsletterMessage('Please agree to receive marketing communications.', 'error');
            return;
          }
          
          // Simulate successful subscription
          showNewsletterMessage('Thank you for subscribing to our newsletter!', 'success');
          emailInput.value = '';
          consentCheckbox.checked = false;
        });
      }
      
      function isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
      }
      
      function showNewsletterMessage(message, type) {
        // Remove any existing messages
        const existingAlert = document.querySelector('.newsletter-alert');
        if (existingAlert) {
          existingAlert.remove();
        }
        
        // Create new alert
        const alert = document.createElement('div');
        alert.className = `alert alert-${type === 'success' ? 'success' : 'danger'} newsletter-alert mt-3`;
        alert.textContent = message;
        
        // Insert after the form
        newsletterForm.appendChild(alert);
        
        // Remove after 5 seconds
        setTimeout(() => {
          alert.remove();
        }, 5000);
      }
    });
  </script>

  <style>
    /* Footer Styles */
    .newsletter-section {
      background: linear-gradient(135deg, var(--light-color) 0%, #f8f9fa 100%);
      border-top: 1px solid rgba(0,0,0,0.05);
      border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    
    .site-footer {
      background: linear-gradient(to right, var(--dark-color) 0%, var(--primary-color) 100%);
      position: relative;
    }
    
    .footer-brand {
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    @media (min-width: 768px) {
      .footer-brand {
        justify-content: flex-start;
      }
    }
    
    .footer-heading {
      font-family: 'Playfair Display', serif;
      font-weight: 600;
      font-size: 1.2rem;
      margin-bottom: 1rem;
      position: relative;
      padding-bottom: 0.5rem;
    }
    
    .footer-heading::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 40px;
      height: 2px;
      background-color: var(--secondary-color);
    }
    
    .footer-link {
      color: rgba(255, 255, 255, 0.8);
      text-decoration: none;
      transition: all 0.3s ease;
      display: inline-block;
      margin-bottom: 0.5rem;
      font-size: 0.9rem;
    }
    
    .footer-link:hover {
      color: white;
      transform: translateX(5px);
    }
    
    .social-links {
      display: flex;
      gap: 0.75rem;
      justify-content: center;
    }
    
    @media (min-width: 768px) {
      .social-links {
        justify-content: flex-start;
      }
    }
    
    .social-link {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 40px;
      height: 40px;
      background: rgba(255, 255, 255, 0.1);
      color: white;
      border-radius: 50%;
      text-decoration: none;
      transition: all 0.3s ease;
    }
    
    .social-link:hover {
      background: var(--secondary-color);
      transform: translateY(-3px);
    }
    
    .payment-icon {
      font-size: 1.8rem;
      margin-right: 0.5rem;
      color: rgba(255, 255, 255, 0.7);
      transition: color 0.3s ease;
    }
    
    .payment-icon:hover {
      color: white;
    }
    
    .back-to-top {
      position: fixed;
      bottom: 20px;
      right: 20px;
      width: 45px;
      height: 45px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      opacity: 0;
      visibility: hidden;
      transition: all 0.3s ease;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
      z-index: 1000;
    }
    
    .back-to-top.show {
      opacity: 1;
      visibility: visible;
    }
    
    .back-to-top:hover {
      transform: translateY(-5px);
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
      .footer-heading {
        margin-top: 1.5rem;
        font-size: 1.1rem;
      }
      
      .footer-heading::after {
        width: 30px;
      }
      
      .footer-link {
        font-size: 0.85rem;
      }
      
      .payment-icon {
        font-size: 1.5rem;
        margin-right: 0.3rem;
      }
      
      .trust-seals .badge {
        font-size: 0.7rem;
      }
      
      .back-to-top {
        bottom: 15px;
        right: 15px;
        width: 40px;
        height: 40px;
      }
      
      .form-check {
        display: flex;
        align-items: center;
      }
    }
    
    @media (max-width: 576px) {
      .newsletter-section .btn-lg {
        font-size: 1rem;
        padding: 0.5rem 1rem;
      }
      
      .footer-brand {
        flex-direction: column;
        text-align: center;
      }
      
      .footer-brand img {
        margin-bottom: 0.5rem;
        margin-right: 0;
      }
      
      .social-links {
        justify-content: center;
      }
      
      .payment-methods {
        text-align: center;
      }
      
      .trust-seals {
        text-align: center !important;
      }
      
      .copyright-links {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
      }
    }
  </style>
</body>
</html>