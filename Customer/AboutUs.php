<?php
session_start();
include("Header.php");
include("Nav.php");
?>

<style>
    
    body, html {
        margin: 0;
        padding: 0;
        font-family: 'Open Sans', sans-serif;
        scroll-behavior: smooth; /* Smooth scrolling */
    }

    /* Main Content */
    .main-content {
        padding-top: 0; 
    }

    /* Hero Section */
    .hero {
        position: relative;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        text-align: center;
        color: #fff;
        overflow: hidden;
        background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('../img/AboutPict.webp') no-repeat center center/cover;
        animation: fadeIn 0.5s ease-in-out; 
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .hero-content {
        max-width: 800px;
        margin: 0 auto;
        padding: 0 20px;
        transform: translateY(0);
        transition: transform 1.5s ease-in-out;
    }

    .hero:hover .hero-content {
        transform: translateY(-10px); 
    }

    .hero-content h1 {
        font-size: 4rem;
        margin-bottom: 1rem;
        font-weight: 700;
        text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.5);
        animation: slideInLeft 2s ease-out forwards; 
    }

    .hero-content p {
        font-size: 1.75rem;
        margin-bottom: 2rem;
        font-weight: 400;
        text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.5);
        animation: slideInRight 2s ease-out 0.5s forwards;
    }

    @keyframes slideInLeft {
        from { transform: translateX(-100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }

    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }

    /* About Us Section */
    .about-us {
        padding: 6rem 1rem;
        background-color: #fff;
    }

    .about-us .container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .about-us h2 {
        font-size: 2.75rem;
        margin-bottom: 2rem;
        font-weight: 700;
        color: rgb(8, 48, 3);
        text-align: center;
        position: relative;
        animation: fadeInUp 1.5s ease-in-out;
    }

    .about-us h2::after {
        content: "";
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 100px;
        height: 4px;
        background-color: #18610e;
        border-radius: 2px;
    }

    .about-us h3 {
        font-size: 2rem;
        margin-bottom: 1.5rem;
        font-weight: 600;
        color: #333;
        animation: fadeInUp 1.5s ease-in-out;
    }

    .about-us p {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #555;
        margin-bottom: 1.5rem;
        animation: fadeInUp 1.5s ease-in-out;
    }

    .about-us ul {
        list-style: none;
        padding: 0;
        margin-bottom: 2rem;
    }

    .about-us li {
        margin-bottom: 1rem;
        font-size: 1.1rem;
        color: #555;
        line-height: 1.8;
        animation: fadeInUp 1.5s ease-in-out;
    }

    .about-us strong {
        color: #18610e;
    }

    @keyframes fadeInUp {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .hero-content h1 {
            font-size: 3rem;
        }

        .hero-content p {
            font-size: 1.5rem;
        }

        .about-us h2 {
            font-size: 2.5rem;
        }

        .about-us h3 {
            font-size: 1.75rem;
        }

        .about-us p,
        .about-us li {
            font-size: 1rem;
        }
    }
</style>

<!-- Main Content -->
<div class="main-content">
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>About Velora Mental Wellness</h1>
            <p>Welcome to our journey of healing and growth.</p>
        </div>
    </section>

    <!-- About Us Section -->
    <section class="about-us">
        <div class="container">
            <h2>About Velora Mental Wellness</h2>
            <p>At Velora Mental Wellness, we believe that mental health is the cornerstone of a fulfilling life. Our mission is to provide a safe, compassionate, and empowering space where individuals can embark on their journey toward emotional well-being and personal growth.</p>

            <h3>Who We Are</h3>
            <p>Velora Mental Wellness was founded with the vision of creating an inclusive sanctuary for mental health care. Our team of experienced therapists and counselors is dedicated to helping individuals overcome challenges such as anxiety, depression, trauma, and stress. We are passionate about guiding our clients toward healing and resilience through evidence-based therapies and personalized care.</p>

            <h3>Our Mission</h3>
            <p>Our mission is simple yet profound: to inspire hope, nurture healing, and empower individuals to lead balanced and meaningful lives. We aim to break the stigma surrounding mental health by fostering awareness, acceptance, and open dialogue.</p>

            <h3>What We Offer</h3>
            <p>At Velora, we provide a wide range of services designed to meet the unique needs of each individual:</p>
            <ul>
                <li><strong>Individual Therapy:</strong> Personalized one-on-one sessions to address specific concerns.</li>
                <li><strong>Group Therapy:</strong> A supportive environment for shared healing experiences.</li>
                <li><strong>Family Counseling:</strong> Strengthening relationships through understanding and communication.</li>
                <li><strong>Workshops & Retreats:</strong> Tools for mindfulness, stress management, and personal growth.</li>
            </ul>

            <h3>Why Choose Us?</h3>
            <p><strong>1. Compassionate Care:</strong> Our team is committed to creating a judgment-free space where you feel heard and supported.</p>
            <p><strong>2. Tailored Approach:</strong> We understand that every individual’s journey is unique. That’s why we design customized treatment plans that align with your goals.</p>
            <p><strong>3. Inclusive Environment:</strong> At Velora, everyone is welcome. We celebrate diversity and are proud allies of LGBTQIA+ communities.</p>
            <p><strong>4. Holistic Healing:</strong> Beyond therapy, we integrate mindfulness practices and wellness strategies to promote overall well-being.</p>

            <h3>Our Vision</h3>
            <p>To be a beacon of hope in the mental health landscape by empowering individuals to thrive emotionally, mentally, and socially. At Velora Mental Wellness, we envision a world where mental health care is accessible, stigma-free, and celebrated as an essential part of life.</p>

            <h3>Meet Our Team</h3>
            <p>Our team consists of licensed professionals with diverse expertise in mental health care. Each member brings empathy, professionalism, and dedication to helping you achieve your wellness goals.</p>
        </div>
    </section>

    <?php
    include("Footer.php");
    ?>
</div>