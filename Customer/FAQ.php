<?php
session_start();
include("Header.php");
include("Nav.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - Mental Wellness Coaching</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        /* Reset and Base Styles */
        * {
            /* margin: 0; */
            /* padding: 0; */
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            margin: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #e0f2fe 0%, #dbeafe 100%);
            color: #2d3748;
            line-height: 1.6;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* FAQ Body */
        .faq-body {
            position: relative;
            padding: 80px 20px;
            overflow: hidden;
            flex: 1 0 auto; /* Grow to fill space, push footer down */
        }

        .faq-body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('../Img/faq-background.jpg') no-repeat center center/cover;
            opacity: 0.1;
            z-index: -1;
        }

        /* FAQ Container */
        .faq-container {
            max-width: 900px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            animation: fadeIn 1s ease-in-out;
        }

        /* Title */
        .faq-title {
            font-size: 2.5rem;
            font-weight: 600;
            text-align: center;
            color: #1a3c34;
            margin-bottom: 40px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Category */
        .faq-category {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2a5d54;
            margin: 30px 0 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #a3e4d7;
            text-align: left;
        }

        /* FAQ Item */
        .faq-item {
            background: #ffffff;
            border-radius: 12px;
            margin-bottom: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .faq-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }

        /* Question Button */
        .faq-question {
            width: 100%;
            padding: 20px;
            font-size: 1.1rem;
            font-weight: 400;
            color: #1a3c34;
            background: #f1f8f6;
            border: none;
            text-align: left;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background 0.3s ease, color 0.3s ease;
        }

        .faq-question span {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2a5d54;
            transition: transform 0.3s ease;
        }

        .faq-question:hover {
            background: #e6f0ee;
            color: #134a40;
        }

        .faq-question.active span {
            transform: rotate(45deg);
        }

        /* Answer */
        .faq-answer {
            padding: 20px;
            font-size: 1rem;
            color: #4a5568;
            background: #fff;
            border-top: 1px solid #e2e8f0;
            display: none;
            line-height: 1.8;
        }

        /* Footer Styling */
        footer {
            background: #1a3c34;
            color: #fff;
            padding: 20px;
            text-align: center;
            width: 100%;
            flex-shrink: 0; 
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000; 
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .faq-container {
                padding: 20px;
                margin: 10px;
            }

            .faq-title {
                font-size: 2rem;
            }

            .faq-category {
                font-size: 1.25rem;
            }

            .faq-question {
                font-size: 1rem;
                padding: 15px;
            }

            .faq-answer {
                font-size: 0.9rem;
                padding: 15px;
            }

            footer {
                padding: 15px;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 480px) {
            .faq-body {
                padding: 40px 10px;
            }

            .faq-title {
                font-size: 1.5rem;
            }

            .faq-question span {
                font-size: 1.2rem;
            }

            footer {
                padding: 10px;
                font-size: 0.8rem;
            }
        }
    </style>
</head>

<body>
    <div class="faq-body">
        <div class="faq-container">
            <h1 class="faq-title">Questions & Answers</h1>
            <div class="faq-category">Basics</div>

            <div class="faq-item">
                <button class="faq-question">What is mental wellness coaching? <span>+</span></button>
                <div class="faq-answer">Mental wellness coaching involves working with a trained professional to improve your emotional well-being, manage stress, and achieve personal growth goals through tailored sessions.</div>
            </div>

            <div class="faq-item">
                <button class="faq-question">How do I know if I need therapy or coaching? <span>+</span></button>
                <div class="faq-answer">Therapy often addresses deeper mental health issues, while coaching focuses on goal-setting and self-improvement. Contact us for a consultation to find the right fit for you.</div>
            </div>

            <div class="faq-item">
                <button class="faq-question">Are your services confidential? <span>+</span></button>
                <div class="faq-answer">Yes, all sessions are strictly confidential, adhering to privacy laws and ethical standards. Your personal information is never shared without consent.</div>
            </div>

            <div class="faq-item">
                <button class="faq-question">How long is a typical session? <span>+</span></button>
                <div class="faq-answer">Sessions typically last 45-60 minutes, depending on the service you choose. Check the service description for specific durations.</div>
            </div>

            <div class="faq-item">
                <button class="faq-question">Can I book a session online? <span>+</span></button>
                <div class="faq-answer">Yes, you can book sessions directly through our website. Simply select a service, choose a therapist, and pick an available time slot.</div>
            </div>

            <div class="faq-item">
                <button class="faq-question">What if I need to cancel or reschedule? <span>+</span></button>
                <div class="faq-answer">You can cancel or reschedule up to 24 hours before your session without a fee. Please use the "My Appointments" section in your account.</div>
            </div>

            <div class="faq-category">Payments</div>

            <div class="faq-item">
                <button class="faq-question">Where can I download an invoice? <span>+</span></button>
                <div class="faq-answer">Invoices are available in the "My Orders" section of your account after completing a payment.</div>
            </div>

            <div class="faq-item">
                <button class="faq-question">Why did my payment fail? <span>+</span></button>
                <div class="faq-answer">Payment failures may occur due to insufficient funds, incorrect card details, or bank restrictions. Please double-check your information or try another payment method.</div>
            </div>

            <div class="faq-item">
                <button class="faq-question">Can I pay using KPay? <span>+</span></button>
                <div class="faq-answer">Yes, we accept KPay as a secure and convenient payment option for all services.</div>
            </div>
        </div>

        <?php include("ChatBot.php"); ?>
    </div>

    <?php include("Footer.php"); ?>

    <script src="../JS/script.js"></script>
    <script>
        $(document).ready(function () {
            $(".faq-question").click(function () {
                const $answer = $(this).next(".faq-answer");
                $answer.slideToggle(300);
                const isVisible = $answer.is(":visible");
                $(this).find("span").text(isVisible ? "−" : "+");
                $(this).toggleClass("active", isVisible);
            });
        });
    </script>
</body>
</html>