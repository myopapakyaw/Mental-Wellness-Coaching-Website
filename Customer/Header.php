<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Velora Mental Wellness | Mindfulness & Therapy</title>
    <link rel="icon" type="image" href="../Img/11.png" sizes="128x128">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --velora-primary: rgb(65, 226, 156);
            --velora-secondary: rgb(10, 58, 4);
            --velora-light: #f8f9fa;
            --velora-dark: #1a1a2e;
            --velora-accent: #ff6b6b;
        }

        body {
            overflow-x: hidden;
            font-family: 'Open Sans', sans-serif;
        }

        /* Enhanced Hero Section */
        .hero-gradient {
            /* background: linear-gradient(135deg, var(--velora-primary) 0%, var(--velora-secondary) 100%); */
            position: relative;
            overflow: hidden;
            min-height: 90vh;
            display: flex;
            align-items: center;
            padding: 100px 0;
        }

        .video-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            /* Places it behind the content */
        }

        .video-background video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            /* Mimics background-size: cover */
            /* opacity: 0.1; */
            /* Maintains your desired opacity */
        }

        /* Fallback if video doesn't load */
        /* .video-background img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.1;
        } */


        .hero-gradient::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            /* background: url('https://images.unsplash.com/photo-1518604666860-9ed391f76460') center/cover; */
            /* background-image: url('../Img/DaisyHero.jpg'); */
            background-position: center;
            background-size: cover;
            /* opacity: 0.1; */
            display: none;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.2;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .hero-subtitle {
            font-size: 1.5rem;
            margin-bottom: 2rem;
        }

        .hero-btn {
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
        }

        .hero-btn-primary {
            background-color: white;
            color: var(--velora-secondary);
        }

        .hero-btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .hero-btn-outline {
            border: 2px solid white;
            color: white;
        }

        .hero-btn-outline:hover {
            background-color: white;
            color: var(--velora-secondary);
        }

        .feature-card {
            transition: all 0.3s ease;
            border-radius: 12px;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(93, 79, 255, 0.1) !important;
        }

        .service-item {
            background: white;
            border-radius: 12px;
            padding: 30px;
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .service-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(93, 79, 255, 0.1);
        }

        .icon-wrapper {
            width: 60px;
            height: 60px;
            background: rgba(93, 79, 255, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .icon-wrapper i {
            color: var(--velora-primary);
            font-size: 24px;
        }

        .testimonial-item {
            background: white;
            border-radius: 12px;
            padding: 30px;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .about-quote blockquote {
            font-size: 1.75rem;
            font-weight: 300;
            font-style: italic;
            color: var(--velora-dark);
            position: relative;
            padding: 0 2rem;
        }

        .about-quote cite {
            display: block;
            font-size: 1rem;
            font-weight: 400;
            font-style: normal;
            color: var(--velora-secondary);
        }

        .rounded-4 {
            border-radius: 1rem;
        }

        .py-7 {
            padding-top: 5rem;
            padding-bottom: 5rem;
        }

        .mt-7 {
            margin-top: 5rem;
        }

        .btn-primary {
            background-color: var(--velora-primary);
            border-color: var(--velora-primary);
        }

        .btn-outline-primary {
            color: var(--velora-primary);
            border-color: var(--velora-primary);
        }

        .btn-outline-primary:hover {
            background-color: var(--velora-primary);
            color: white;
        }

        /* Responsive adjustments */
        @media (max-width: 992px) {
            .hero-gradient {
                min-height: 80vh;
                padding: 80px 0;
                text-align: center;
            }

            .hero-title {
                font-size: 2.8rem;
            }
        }

        @media (max-width: 768px) {
            .hero-gradient {
                min-height: 70vh;
                padding: 60px 0;
            }

            .hero-title {
                font-size: 2.2rem;
            }

            .hero-subtitle {
                font-size: 1.2rem;
            }
        }
    </style>
</head>