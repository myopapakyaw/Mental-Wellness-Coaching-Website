<?php
ob_start();
session_start();
date_default_timezone_set('Asia/Yangon');

include("Header.php");
include("Nav.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - Velora Mental Wellness</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e0e8e4 100%);
            color: #2d2d2d;
            margin: 0;
            padding: 0;
            line-height: 1.5;
        }

        .blog-wrapper {
            max-width: 900px;
            margin: 60px auto;
            padding: 0 20px;
        }

        .blog-header {
            text-align: center;
            margin-bottom: 50px;
            position: relative;
        }

        .blog-header h1 {
            font-size: 2.8rem;
            font-weight: 700;
            color: #062802;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
            background: linear-gradient(to right, #062802, #1a4d1a);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .blog-header p {
            font-size: 1.2rem;
            color: #6b7280;
            margin: 15px 0 0;
            font-weight: 300;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .blog-container {
            display: flex;
            flex-direction: column; 
            gap: 40px;
        }

        .blog-post {
            background: #ffffff;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(6, 40, 2, 0.1);
            padding: 30px;
            transition: all 0.3s ease;
            width: 100%; 
            position: relative;
            overflow: hidden;
        }

        .blog-post::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(to right, #062802, #2e7d32);
            transition: width 0.3s ease;
        }

        .blog-post:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 35px rgba(6, 40, 2, 0.15);
        }

        .blog-post:hover::before {
            width: 100%;
        }

        .blog-post h2 {
            font-size: 1.8rem;
            font-weight: 600;
            color: #062802;
            margin: 0 0 15px;
            line-height: 1.3;
        }

        .blog-meta {
            font-size: 0.95rem;
            color: #7f8c8d;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-style: italic;
        }

        .blog-meta i {
            color: #2e7d32;
            font-size: 1rem;
        }

        .blog-content {
            font-size: 1.05rem;
            color: #4b5563;
            line-height: 1.7;
            margin-bottom: 25px;
            text-align: justify;
        }

        .read-more {
            display: inline-flex;
            align-items: center;
            padding: 10px 20px;
            background: linear-gradient(135deg, #062802 0%, #2e7d32 100%);
            color: #fff;
            text-decoration: none;
            border-radius: 25px;
            font-size: 0.95rem;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(6, 40, 2, 0.2);
        }

        .read-more i {
            margin-left: 8px;
            transition: transform 0.3s ease;
        }

        .read-more:hover {
            background: linear-gradient(135deg, #0c0c0c, #1a4d1a);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(6, 40, 2, 0.3);
        }

        .read-more:hover i {
            transform: translateX(4px);
        }

        @media (max-width: 768px) {
            .blog-wrapper {
                padding: 0 15px;
                margin: 40px auto;
            }

            .blog-header h1 {
                font-size: 2.2rem;
            }

            .blog-header p {
                font-size: 1rem;
            }

            .blog-post {
                padding: 20px;
            }

            .blog-post h2 {
                font-size: 1.5rem;
            }

            .blog-content {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="blog-wrapper">
        <div class="blog-header">
            <h1>Velora Wellness Blog</h1>
            <p>Explore insights, tips, and stories to nurture your mental well-being</p>
        </div>

        <div class="blog-container">
            <div class="blog-post">
                <h2>The Power of Morning Meditation: Starting Your Day with Intention</h2>
                <div class="blog-meta">
                    <i class="fas fa-user"></i> Dr. Arkar Lwin
                    <i class="fas fa-calendar-alt"></i> March 15, 2025
                </div>
                <div class="blog-content">
                    In our fast-paced world, taking just 10 minutes each morning to center yourself can make a profound difference in your mental wellbeing. Our 10-Minute Morning Meditation service at Velora is designed to help you cultivate calm, clarity, and positivity before your day begins. Research shows that regular morning meditation can reduce stress and anxiety levels, improve focus and productivity throughout the day, enhance emotional resilience, and promote better sleep patterns. At Velora, we guide you through simple yet powerful techniques that fit seamlessly into even the busiest schedules. Whether you're new to meditation or a seasoned practitioner, this practice helps ground you in the present moment, setting a positive tone for whatever comes your way. Try this simple technique tomorrow morning: Sit comfortably, close your eyes, and focus on your breath for just 5 minutes. Notice how it changes your day.
                </div>
                <!-- <a href="#" class="read-more">Read More <i class="fas fa-arrow-right"></i></a> -->
            </div>

            <div class="blog-post">
                <h2>Understanding and Managing Anxiety: A Therapist's Perspective</h2>
                <div class="blog-meta">
                    <i class="fas fa-user"></i> Dr. Thidar
                    <i class="fas fa-calendar-alt"></i> February 28, 2025
                </div>
                <div class="blog-content">
                    Anxiety is one of the most common mental health concerns we see at Velora Mental Wellness, especially among young professionals in our community. While occasional anxiety is normal, persistent worry that interferes with daily life may indicate an anxiety disorder. In our one-on-one therapy sessions, we help clients identify the root causes of their anxiety, triggers and patterns in anxious thinking, practical coping mechanisms, and long-term strategies for emotional regulation. One technique we often recommend is the "5-4-3-2-1" grounding exercise: When feeling anxious, name 5 things you can see, 4 you can touch, 3 you can hear, 2 you can smell, and 1 you can taste. This simple practice brings you back to the present moment. Remember, seeking help is a sign of strength, not weakness. Our team is here to support you on your journey to greater peace of mind.
                </div>
                <!-- <a href="#" class="read-more">Read More <i class="fas fa-arrow-right"></i></a> -->
            </div>

            <div class="blog-post">
                <h2>The Mind-Body Connection: How Physical Health Impacts Mental Wellness</h2>
                <div class="blog-meta">
                    <i class="fas fa-user"></i> Dr. TheOhnmar
                    <i class="fas fa-calendar-alt"></i> January 10, 2025
                </div>
                <div class="blog-content">
                    At Velora, we take a holistic approach to mental health because we understand how deeply interconnected our physical and emotional wellbeing truly are. Our 4-week wellness program combines counseling with physical practices because the data is clear: movement heals. Key connections we explore in our program: How regular exercise can be as effective as medication for mild to moderate depression, the gut-brain axis and how nutrition affects mood, the stress-reducing benefits of yoga and tai chi, and sleep's critical role in emotional regulation. This month, we're launching new group sessions that combine gentle movement with mindfulness practices. Whether you're dealing with stress, depression, or just want to maintain good mental health, joining our community can help you develop sustainable habits for total wellbeing. Remember: Small, consistent changes often create the most lasting results. Start with just 10 minutes of movement today and notice how you feel.
                </div>
                <!-- <a href="#" class="read-more">Read More <i class="fas fa-arrow-right"></i></a> -->
            </div>
        </div>
    </div>

    <?php include("Footer.php"); ?>
    
</body>
</html>
<?php ob_end_flush(); ?>