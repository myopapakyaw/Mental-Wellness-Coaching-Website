<footer class="site-footer">
    <div class="footer-container">
        <!-- Footer Columns -->
        <div class="footer-column">
            <h4>Explore</h4>
            <ul>
                <li><a href="AboutUs.php">About Us</a></li>
                <li><a href="Therapists.php">Our Therapists</a></li>
                <li><a href="Feedback.php">Feedbacks</a></li>
            </ul>
        </div>

        <div class="footer-column">
            <h4>Services</h4>
            <ul>
                <li><a href="ServicePage.php">Treatments</a></li>
                <li><a href="FAQ.php">FAQ</a></li>
                <li><a href="Blog.php">Blog</a></li>
            </ul>
        </div>

        <div class="footer-column">
            <h4>Contact Us</h4>
            <ul>
                <li><a href="mailto:info@veloramentalwellness.com">info@veloramentalwellness.com</a></li>
                <li><a href="tel:+959262240737">+959 262 240 737</a></li>
                <!-- <li>123 Wellness St, Suite 101</li> -->
                <!-- <li>City, State, ZIP</li> -->
                <li><a href="ContactUs.php">Contact Us</a></li>
            </ul>
        </div>

        <div class="footer-column">
            <h4>Follow Us</h4>
            <div class="social-icons">
                <a href="https://facebook.com" target="_blank" aria-label="Facebook" class="social-icon">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="https://twitter.com" target="_blank" aria-label="Twitter" class="social-icon">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="https://instagram.com" target="_blank" aria-label="Instagram"
                    class="social-icon">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="https://linkedin.com" target="_blank" aria-label="LinkedIn"
                    class="social-icon">
                    <i class="fab fa-linkedin-in"></i>
                </a>
                <a href="https://tiktok.com" target="_blank" aria-label="TikTok"
                    class="social-icon">
                    <i class="fab fa-tiktok"></i>
                </a>
                <a href="https://youtube.com" target="_blank" aria-label="Youtube"
                    class="social-icon">
                    <i class="fab fa-youtube"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Copyright Notice -->
    <div class="footer-bottom">
        <p>© <?php echo date("Y"); ?> Velora Mental Wellness. All rights reserved.</p>
        <p><a href="PrivacyPolicy.php">Privacy Policy</a> | <a href="Term&Condition.php">Terms and Condition</a></p>
    </div>

    <!-- Back to Top Button -->
    <!-- <button id="back-to-top" aria-label="Back to Top">
        <i class="fas fa-arrow-up"></i>
    </button> -->
</footer>

<!-- Link to Font Awesome for Icons -->
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> -->

<style>
    .site-footer {
        background: linear-gradient(135deg, #1a3c34 0%, #2d6a4f 100%);
        /* Calming gradient */
        color: #e0e0e0;
        padding: 60px 0 20px;
        font-family: 'Open Sans', sans-serif;
        position: relative;
        overflow: hidden;
    }

    .footer-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
        gap: 30px;
    }

    .footer-column {
        padding: 10px;
    }

    .footer-column h4 {
        font-size: 1.25rem;
        font-weight: 600;
        color: #74c69d;
        margin-bottom: 20px;
        position: relative;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .footer-column h4::after {
        content: '';
        position: absolute;
        bottom: -5px;
        left: 0;
        width: 0.1px;
        height: 2px;
        background: #74c69d;
        transition: width 0.3s ease;
    }

    .footer-column:hover h4::after {
        width: 250px;
    }

    .footer-column ul {
        list-style: none;
        padding: 0;
    }

    .footer-column ul li {
        margin-bottom: 12px;
    }

    .footer-column ul li a {
        color: #e0e0e0;
        text-decoration: none;
        font-size: 0.95rem;
        transition: color 0.3s ease, transform 0.3s ease;
    }

    .footer-column ul li a:hover {
        color: #b4c687;
        transform: translateX(5px);
    }

    .social-icons {
        display: flex;
        gap: 20px;
        justify-content: flex-start;
        margin-top: 10px;
    }

    .social-icons a {
        color: #e0e0e0;
        font-size: 1.5rem;
        transition: color 0.3s ease, transform 0.3s ease;
    }

    .social-icons a:hover {
        color: #74c69d;
        transform: scale(1.2);
    }

    .newsletter {
        margin-top: 20px;
    }

    .newsletter h4 {
        font-size: 1.25rem;
        margin-bottom: 15px;
    }

    .newsletter form {
        display: flex;
        gap: 10px;
    }

    .newsletter input {
        padding: 10px;
        border: none;
        border-radius: 5px;
        font-size: 0.9rem;
        width: 100%;
        max-width: 200px;
        background: #fff;
        color: #333;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .newsletter button {
        padding: 10px 20px;
        background: #74c69d;
        color: #fff;
        border: none;
        border-radius: 5px;
        font-size: 0.9rem;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .newsletter button:hover {
        background: #40916c;
    }

    .footer-bottom {
        text-align: center;
        margin-top: 40px;
        padding-top: 20px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        font-size: 0.9rem;
    }

    .footer-bottom p {
        margin: 5px 0;
    }

    .footer-bottom a {
        color: #b4c687;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .footer-bottom a:hover {
        color: #74c69d;
    }

    /* Back to Top Button */
    /* #back-to-top {
        position: fixed;
        bottom: 30px;
        right: 30px;
        background: #74c69d;
        color: #fff;
        border: none;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        font-size: 1.2rem;
        cursor: pointer;
        display: none;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        transition: transform 0.3s ease, background 0.3s ease;
    }

    #back-to-top:hover {
        background: #40916c;
        transform: scale(1.1);
    } */

    .site-footer {
        position: relative;
        z-index: 99;
        /* Ensure footer stays above other elements */
    }

    /* Update the back-to-top button styles */
    #back-to-top {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background-color: var(--primary, #0c821aff);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        cursor: pointer;
        transition: all 0.3s ease;
        z-index: 100;
        border: none;
        font-size: 1.2rem;
    }

    #back-to-top:hover {
        background-color: var(--secondary, #07b329ff);
        transform: translateY(-3px) scale(1.05);
    }

    

    /* Responsive Design */
    @media (max-width: 768px) {
        .footer-container {
            grid-template-columns: 1fr;
            text-align: center;
        }

        .social-icons {
            justify-content: center;
        }

        .newsletter form {
            flex-direction: column;
            align-items: center;
        }

        .newsletter input {
            max-width: 100%;
            margin-bottom: 10px;
        }
    }

    /* Subtle Animation for Footer Load */
    .footer-column {
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInUp 0.8s ease-out forwards;
    }

    .footer-column:nth-child(1) {
        animation-delay: 0.1s;
    }

    .footer-column:nth-child(2) {
        animation-delay: 0.2s;
    }

    .footer-column:nth-child(3) {
        animation-delay: 0.3s;
    }

    .footer-column:nth-child(4) {
        animation-delay: 0.4s;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<!-- Back to Top Script -->
<!-- <script>
    const backToTopButton = document.getElementById('back-to-top');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 300) {
            backToTopButton.style.display = 'flex';
        } else {
            backToTopButton.style.display = 'none';
        }
    });
    backToTopButton.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
</script> -->