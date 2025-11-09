<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PSCTV - Panabo Cable and Fiber</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        :root {
            --primary: #10b981;
            --primary-dark: #059669;
            --primary-light: #34d399;
            --secondary: #f59e0b;
            --dark: #1f2937;
            --light: #ffffff;
            --gray: #6b7280;
            --gray-light: #f9fafb;
            --transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            --shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            background-color: var(--light);
            color: var(--dark);
            line-height: 1.6;
            overflow-x: hidden;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        section {
            padding: 100px 0;
        }

        h1, h2, h3, h4 {
            margin-bottom: 1rem;
            line-height: 1.2;
            font-weight: 700;
        }

        p {
            margin-bottom: 1.5rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 14px 32px;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn:hover {
            background-color: var(--primary-dark);
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
        }

        .btn-secondary {
            background-color: var(--secondary);
        }

        .btn-secondary:hover {
            background-color: #d97706;
        }

        .btn-outline {
            background-color: transparent;
            border: 2px solid var(--primary);
            color: var(--primary);
        }

        .btn-outline:hover {
            background-color: var(--primary);
            color: white;
        }

        .btn-login {
            background-color: transparent;
            border: 2px solid var(--primary);
            color: var(--primary);
            padding: 10px 24px;
            margin-left: 15px;
        }

        .btn-login:hover {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--primary);
            transform: none;
        }

        .section-title {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-title h2 {
            font-size: 2.8rem;
            position: relative;
            display: inline-block;
        }

        .section-title h2::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background-color: var(--primary);
            border-radius: 2px;
        }

        .section-title p {
            max-width: 600px;
            margin: 30px auto 0;
            font-size: 1.1rem;
            color: var(--gray);
        }

        /* Header & Navigation */
        header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            background-color: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            box-shadow: 0 2px 30px rgba(0, 0, 0, 0.1);
            transition: var(--transition);
        }

        header.scrolled {
            padding: 5px 0;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 0;
            transition: var(--transition);
        }

        .logo {
            display: flex;
            align-items: center;
        }

        .logo img {
            height: 50px;
            margin-right: 12px;
            transition: var(--transition);
        }

        .logo-text {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--primary);
            letter-spacing: -0.5px;
        }

        .nav-links {
            display: flex;
            list-style: none;
            align-items: center;
        }

        .nav-links li {
            margin-left: 30px;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--dark);
            font-weight: 500;
            transition: var(--transition);
            position: relative;
        }

        .nav-links a:hover {
            color: var(--primary);
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background-color: var(--primary);
            transition: var(--transition);
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .mobile-menu {
            display: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--dark);
        }

        /* Hero Section */
        .hero {
            height: 100vh;
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.9), rgba(5, 150, 105, 0.85)), url('https://images.unsplash.com/photo-1621905252507-b35492cc74b4?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2069&q=80') center/cover no-repeat;
            position: relative;
            overflow: hidden;
            color: white;
        }

        .hero-content {
            max-width: 650px;
            position: relative;
            z-index: 2;
        }

        .hero h1 {
            font-size: 3.8rem;
            margin-bottom: 1.5rem;
            opacity: 0;
            transform: translateY(30px);
            animation: fadeUp 1s forwards 0.5s;
            line-height: 1.1;
        }

        .hero p {
            font-size: 1.3rem;
            margin-bottom: 2.5rem;
            opacity: 0;
            transform: translateY(30px);
            animation: fadeUp 1s forwards 0.8s;
        }

        .hero-btns {
            opacity: 0;
            transform: translateY(30px);
            animation: fadeUp 1s forwards 1.1s;
            display: flex;
            gap: 15px;
        }

        .hero-image {
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 50%;
            height: 80%;
            opacity: 0;
            animation: fadeInRight 1s forwards 1.4s;
        }

        .hero-image::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: url('https://images.unsplash.com/photo-1558494949-ef010cbdcc31?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2074&q=80') center/cover no-repeat;
            border-radius: 20px 0 0 20px;
            box-shadow: var(--shadow-lg);
        }

        /* AI Chat Widget */
        .ai-chat-widget {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
        }

        .chat-toggle {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            box-shadow: var(--shadow-lg);
            transition: var(--transition);
            border: none;
        }

        .chat-toggle:hover {
            transform: scale(1.1);
        }

        .chat-container {
            position: absolute;
            bottom: 70px;
            right: 0;
            width: 350px;
            height: 500px;
            background: white;
            border-radius: 15px;
            box-shadow: var(--shadow-lg);
            display: none;
            flex-direction: column;
            overflow: hidden;
        }

        .chat-container.active {
            display: flex;
        }

        .chat-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .chat-header h3 {
            margin: 0;
            font-size: 1.2rem;
        }

        .chat-close {
            background: none;
            border: none;
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
        }

        .chat-messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .message {
            padding: 12px 16px;
            border-radius: 18px;
            max-width: 80%;
            line-height: 1.4;
        }

        .message.bot {
            background: var(--gray-light);
            align-self: flex-start;
            border-bottom-left-radius: 5px;
        }

        .message.user {
            background: var(--primary);
            color: white;
            align-self: flex-end;
            border-bottom-right-radius: 5px;
        }

        .chat-input {
            padding: 15px;
            border-top: 1px solid #eee;
            display: flex;
            gap: 10px;
        }

        .chat-input input {
            flex: 1;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 25px;
            outline: none;
        }

        .chat-input button {
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            cursor: pointer;
            transition: var(--transition);
        }

        .chat-input button:hover {
            background: var(--primary-dark);
        }

        /* Video Section */
        .video-section {
            background: var(--gray-light);
            padding: 80px 0;
        }

        .video-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            align-items: center;
        }

        .video-content h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }

        .video-player {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
        }

        .video-player video {
            width: 100%;
            display: block;
        }

        /* Map Section */
        .map-section {
            padding: 80px 0;
        }

        .map-container {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            height: 400px;
        }

        #map {
            width: 100%;
            height: 100%;
        }

        /* Contact Form Updates */
        .g-recaptcha {
            margin: 20px 0;
        }

        .form-status {
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
            display: none;
        }

        .form-status.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            display: block;
        }

        .form-status.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            display: block;
        }

        /* Services Section */
        .services {
            background-color: var(--gray-light);
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .service-card {
            background-color: white;
            border-radius: 12px;
            padding: 40px 30px;
            box-shadow: var(--shadow);
            transition: var(--transition);
            text-align: center;
            opacity: 0;
            transform: translateY(30px);
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .service-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            transform: scaleX(0);
            transform-origin: left;
            transition: var(--transition);
        }

        .service-card.animate {
            opacity: 1;
            transform: translateY(0);
        }

        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-lg);
        }

        .service-card:hover::before {
            transform: scaleX(1);
        }

        .service-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            color: white;
            font-size: 2rem;
            transition: var(--transition);
        }

        .service-card:hover .service-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .service-card h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        /* Plans Section */
        .plans-container {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 30px;
        }

        .plan-card {
            background-color: white;
            border-radius: 12px;
            padding: 40px 30px;
            box-shadow: var(--shadow);
            transition: var(--transition);
            text-align: center;
            flex: 1;
            min-width: 280px;
            max-width: 350px;
            opacity: 0;
            transform: translateY(30px);
            position: relative;
            overflow: hidden;
        }

        .plan-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
        }

        .plan-card.animate {
            opacity: 1;
            transform: translateY(0);
        }

        .plan-card.featured {
            transform: scale(1.05);
            border: 2px solid var(--primary);
            position: relative;
        }

        .plan-card.featured::after {
            content: 'Most Popular';
            position: absolute;
            top: 20px;
            right: -30px;
            background-color: var(--secondary);
            color: white;
            padding: 5px 30px;
            font-size: 0.8rem;
            font-weight: 600;
            transform: rotate(45deg);
        }

        .plan-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-lg);
        }

        .plan-card.featured:hover {
            transform: scale(1.05) translateY(-10px);
        }

        .plan-name {
            font-size: 1.8rem;
            margin-bottom: 10px;
            color: var(--primary);
        }

        .plan-price {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 20px;
            color: var(--dark);
            position: relative;
            display: inline-block;
        }

        .plan-price::before {
            content: '₱';
            font-size: 1.5rem;
            position: absolute;
            top: 10px;
            left: -20px;
        }

        .plan-price span {
            font-size: 1rem;
            color: var(--gray);
            font-weight: 500;
        }

        .plan-features {
            list-style: none;
            margin-bottom: 30px;
        }

        .plan-features li {
            padding: 12px 0;
            border-bottom: 1px solid #eee;
            position: relative;
            padding-left: 25px;
        }

        .plan-features li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: var(--primary);
            font-weight: bold;
        }

        .plan-features li:last-child {
            border-bottom: none;
        }

        /* About Section */
        .about-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }

        .about-text {
            opacity: 0;
            transform: translateX(-50px);
        }

        .about-text.animate {
            opacity: 1;
            transform: translateX(0);
            transition: all 0.8s ease;
        }

        .about-image {
            opacity: 0;
            transform: translateX(50px);
        }

        .about-image.animate {
            opacity: 1;
            transform: translateX(0);
            transition: all 0.8s ease;
        }

        .about-image img {
            width: 100%;
            border-radius: 12px;
            box-shadow: var(--shadow-lg);
            transition: var(--transition);
        }

        .about-image:hover img {
            transform: scale(1.02);
        }

        /* Stats Section */
        .stats {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            text-align: center;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
        }

        .stat-item {
            opacity: 0;
            transform: translateY(30px);
        }

        .stat-item.animate {
            opacity: 1;
            transform: translateY(0);
        }

        .stat-number {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .stat-text {
            font-size: 1.2rem;
            font-weight: 500;
        }

        /* Contact Section */
        .contact {
            background-color: var(--gray-light);
        }

        .contact-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
        }

        .contact-info {
            opacity: 0;
            transform: translateX(-50px);
        }

        .contact-info.animate {
            opacity: 1;
            transform: translateX(0);
            transition: all 0.8s ease;
        }

        .contact-form {
            opacity: 0;
            transform: translateX(50px);
        }

        .contact-form.animate {
            opacity: 1;
            transform: translateX(0);
            transition: all 0.8s ease;
        }

        .contact-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 30px;
        }

        .contact-icon {
            width: 50px;
            height: 50px;
            background-color: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-control {
            width: 100%;
            padding: 16px 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: var(--transition);
            background-color: white;
        }

        .form-control:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
        }

        textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }

        /* Footer */
        footer {
            background-color: var(--dark);
            color: white;
            padding: 80px 0 30px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            margin-bottom: 50px;
        }

        .footer-logo {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .footer-logo img {
            height: 40px;
            margin-right: 12px;
        }

        .footer-logo-text {
            font-size: 1.4rem;
            font-weight: 800;
            color: white;
        }

        .footer-about p {
            color: #d1d5db;
        }

        .social-icons {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .social-icons a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            color: white;
            transition: var(--transition);
        }

        .social-icons a:hover {
            background-color: var(--primary);
            transform: translateY(-3px);
        }

        .footer-links h3, .footer-contact h3 {
            font-size: 1.3rem;
            margin-bottom: 25px;
            position: relative;
        }

        .footer-links h3::after, .footer-contact h3::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 40px;
            height: 3px;
            background-color: var(--primary);
            border-radius: 2px;
        }

        .footer-links ul {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 12px;
        }

        .footer-links a {
            color: #d1d5db;
            text-decoration: none;
            transition: var(--transition);
            display: inline-block;
        }

        .footer-links a:hover {
            color: var(--primary);
            transform: translateX(5px);
        }

        .footer-contact p {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            color: #d1d5db;
        }

        .footer-contact i {
            margin-right: 12px;
            color: var(--primary);
            width: 20px;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid #374151;
            color: #9ca3af;
        }

        /* Animations */
        @keyframes fadeUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInRight {
            to {
                opacity: 1;
                transform: translateX(0) translateY(-50%);
            }
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .hero-image {
                width: 45%;
            }
        }

        @media (max-width: 992px) {
            .hero-content {
                max-width: 100%;
                text-align: center;
            }
            
            .hero h1 {
                font-size: 3.2rem;
            }
            
            .hero-image {
                display: none;
            }
            
            .about-content, .contact-container, .video-container {
                grid-template-columns: 1fr;
            }
            
            .about-text, .about-image, .contact-info, .contact-form {
                transform: translateY(30px);
            }
            
            .about-text.animate, .about-image.animate, .contact-info.animate, .contact-form.animate {
                transform: translateY(0);
            }

            .chat-container {
                width: 300px;
                height: 450px;
            }
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 15px 0;
            }
            
            .nav-links {
                position: fixed;
                top: 80px;
                left: -100%;
                width: 100%;
                height: calc(100vh - 80px);
                background-color: white;
                flex-direction: column;
                align-items: center;
                justify-content: flex-start;
                padding-top: 50px;
                transition: var(--transition);
                box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            }
            
            .nav-links.active {
                left: 0;
            }
            
            .nav-links li {
                margin: 20px 0;
            }
            
            .mobile-menu {
                display: block;
            }
            
            .hero h1 {
                font-size: 2.5rem;
            }
            
            .hero p {
                font-size: 1.1rem;
            }
            
            .section-title h2 {
                font-size: 2.2rem;
            }
            
            .plan-card.featured {
                transform: scale(1);
            }
            
            .plan-card.featured:hover {
                transform: translateY(-10px);
            }
            
            .hero-btns {
                flex-direction: column;
                align-items: center;
            }
            
            .hero-btns .btn {
                width: 100%;
                max-width: 250px;
                margin-bottom: 15px;
            }

            .ai-chat-widget {
                bottom: 20px;
                right: 20px;
            }

            .chat-container {
                width: 280px;
                height: 400px;
            }
        }

        @media (max-width: 576px) {
            .hero h1 {
                font-size: 2.2rem;
            }
            
            .services-grid {
                grid-template-columns: 1fr;
            }
            
            .plan-card {
                min-width: 100%;
            }
            
            .stat-number {
                font-size: 2.8rem;
            }

            .chat-container {
                width: 260px;
                right: -20px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header id="header">
        <div class="container">
            <nav class="navbar">
                <div class="logo">
                    <img src="{{ asset('images/logo.png') }}" alt="PSCTV Logo">
                    <div class="logo-text">PSCTV</div>
                </div>
                <ul class="nav-links">
                    <li><a href="#home">Home</a></li>
                    <li><a href="#services">Services</a></li>
                    <li><a href="#plans">Plans</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#contact">Contact</a></li>
                    <li><a href="{{ route('login.form') }}" class="btn btn-login ms-3">Login</a></li>
                </ul>
                <div class="mobile-menu">
                    <i class="fas fa-bars"></i>
                </div>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="container">
            <div class="hero-content">
                <h1>Premium Cable & Fiber Internet</h1>
                <p>Experience lightning-fast internet speeds and crystal-clear television with PSCTV's advanced fiber optic technology. Serving Panabo City with reliable connectivity.</p>
                <div class="hero-btns">
                    <a href="#plans" class="btn">View Plans</a>
                    <a href="#contact" class="btn btn-outline">Contact Us</a>
                </div>
            </div>
            <div class="hero-image"></div>
        </div>
    </section>

    <!-- AI Chat Widget -->
    <div class="ai-chat-widget">
        <button class="chat-toggle">
            <i class="fas fa-robot"></i>
        </button>
        <div class="chat-container">
            <div class="chat-header">
                <h3>PSCTV Assistant</h3>
                <button class="chat-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="chat-messages" id="chatMessages">
                <div class="message bot">
                    Hello! I'm PSCTV Assistant. How can I help you today?
                </div>
                <div class="message bot">
                    Please provide your email address so we can contact you with more information.
                </div>
            </div>
            <div class="chat-input">
                <input type="text" id="chatInput" placeholder="Type your email or message...">
                <button id="sendMessage">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Video Section -->
    <section class="video-section">
        <div class="container">
            <div class="video-container">
                <div class="video-content">
                    <h2>Experience PSCTV Quality</h2>
                    <p>Watch our introduction video to learn more about our premium cable and fiber internet services in Panabo City.</p>
                    <p>Discover why thousands of customers trust PSCTV for their connectivity needs with our state-of-the-art infrastructure and dedicated customer support.</p>
                    <a href="#contact" class="btn">Get Started Today</a>
                </div>
                <div class="video-player">
                    <video controls poster="https://images.unsplash.com/photo-1558494949-ef010cbdcc31?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2074&q=80">
                        <source src="{{ asset('video/videomp4') }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services" id="services">
        <div class="container">
            <div class="section-title">
                <h2>Our Services</h2>
                <p>Discover the range of services we offer to keep you connected and entertained</p>
            </div>
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-broadcast-tower"></i>
                    </div>
                    <h3>High-Speed Internet</h3>
                    <p>Blazing fast fiber optic internet with speeds up to 1Gbps for seamless browsing, streaming, and gaming.</p>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-tv"></i>
                    </div>
                    <h3>Cable Television</h3>
                    <p>Over 100 channels including local and international content in high definition for your entertainment.</p>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <h3>Digital Phone</h3>
                    <p>Crystal clear digital phone service with unlimited calling to keep you connected with loved ones.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">5,000+</div>
                    <div class="stat-text">Happy Customers</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">99%</div>
                    <div class="stat-text">Uptime Guarantee</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">24/7</div>
                    <div class="stat-text">Customer Support</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">10+</div>
                    <div class="stat-text">Years of Service</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Plans Section -->
    <section id="plans">
        <div class="container">
            <div class="section-title">
                <h2>Internet Plans</h2>
                <p>Choose the perfect plan for your home or business needs</p>
            </div>
            <div class="plans-container">
                <div class="plan-card">
                    <h3 class="plan-name">Basic</h3>
                    <div class="plan-price">999<span>/month</span></div>
                    <ul class="plan-features">
                        <li>25 Mbps Internet Speed</li>
                        <li>50+ Cable Channels</li>
                        <li>Free Installation</li>
                        <li>24/7 Customer Support</li>
                    </ul>
                    <a href="#contact" class="btn btn-outline">Get Started</a>
                </div>
                <div class="plan-card featured">
                    <h3 class="plan-name">Premium</h3>
                    <div class="plan-price">1,499<span>/month</span></div>
                    <ul class="plan-features">
                        <li>100 Mbps Internet Speed</li>
                        <li>100+ Cable Channels</li>
                        <li>Free Digital Phone</li>
                        <li>Free Wi-Fi Router</li>
                        <li>24/7 Customer Support</li>
                    </ul>
                    <a href="#contact" class="btn">Get Started</a>
                </div>
                <div class="plan-card">
                    <h3 class="plan-name">Ultimate</h3>
                    <div class="plan-price">2,499<span>/month</span></div>
                    <ul class="plan-features">
                        <li>500 Mbps Internet Speed</li>
                        <li>150+ Cable Channels</li>
                        <li>Free Digital Phone</li>
                        <li>Premium Wi-Fi Router</li>
                        <li>Priority Support</li>
                    </ul>
                    <a href="#contact" class="btn btn-outline">Get Started</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="map-section">
        <div class="container">
            <div class="section-title">
                <h2>Visit Our Location</h2>
                <p>Find us at our main office in Panabo City</p>
            </div>
            <div class="map-container">
                <div id="map"></div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about">
        <div class="container">
            <div class="about-content">
                <div class="about-text">
                    <h2>About PSCTV</h2>
                    <p>PSCTV has been serving the Panabo community since 2010, providing reliable cable television and internet services. Our commitment to quality and customer satisfaction has made us the leading provider in the region.</p>
                    <p>With our state-of-the-art fiber optic network, we deliver unparalleled internet speeds and crystal-clear television signals to homes and businesses throughout Panabo City and surrounding areas.</p>
                    <p>Our team of dedicated professionals is always ready to assist you with installation, troubleshooting, and any other needs you may have.</p>
                    <a href="#contact" class="btn">Learn More</a>
                </div>
                <div class="about-image">
                    <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80" alt="PSCTV Network Infrastructure">
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact" id="contact">
        <div class="container">
            <div class="section-title">
                <h2>Contact Us</h2>
                <p>Get in touch with us for inquiries, installation requests, or support</p>
            </div>
            <div class="contact-container">
                <div class="contact-info">
                    <h3>Get In Touch</h3>
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h4>Our Location</h4>
                            <p>519 Quezon St., New Pandan, Panabo City, Davao Del Norte</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div>
                            <h4>Call Us</h4>
                            <p>+639328476222</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <h4>Email Us</h4>
                            <p>psctvincacctg@gmail.com</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <h4>Business Hours</h4>
                            <p>Monday - Saturday: 8:00 AM - 6:00 PM</p>
                        </div>
                    </div>
                </div>
                <div class="contact-form">
                    <h3>Send Us a Message</h3>
                    <form id="contactForm" action="{{ route('contact.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <input type="text" class="form-control" name="name" placeholder="Your Name" required>
                        </div>
                        <div class="form-group">
                            <input type="email" class="form-control" name="email" placeholder="Your Email" required>
                        </div>
                        <div class="form-group">
                            <input type="tel" class="form-control" name="phone" placeholder="Your Phone Number">
                        </div>
                        <div class="form-group">
                            <select class="form-control" name="service" required>
                                <option value="">Select Service</option>
                                <option value="internet">Internet</option>
                                <option value="cable">Cable TV</option>
                                <option value="phone">Digital Phone</option>
                                <option value="bundle">Bundle Package</option>
                                <option value="support">Technical Support</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <textarea class="form-control" name="message" placeholder="Your Message" required></textarea>
                        </div>
                        <div class="g-recaptcha" data-sitekey="6LcVAAcsAAAAACrCk-6mwFUuYHTP7WQen0TrPL1Y"></div>
                        <div class="form-status" id="formStatus"></div>
                        <button type="submit" class="btn">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-about">
                    <div class="footer-logo">
                        <img src="{{ asset('images/logo.png') }}" alt="PSCTV Logo">
                        <div class="footer-logo-text">PSCTV</div>
                    </div>
                    <p>Panabo's premier cable and fiber internet provider, delivering reliable connectivity and entertainment to homes and businesses since 1996.</p>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="footer-links">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="#home">Home</a></li>
                        <li><a href="#services">Services</a></li>
                        <li><a href="#plans">Plans</a></li>
                        <li><a href="#about">About Us</a></li>
                        <li><a href="#contact">Contact</a></li>
                        <li><a href="{{ route('login.form') }}">Login</a></li>
                    </ul>
                </div>
                <div class="footer-contact">
                    <h3>Contact Info</h3>
                    <p><i class="fas fa-map-marker-alt"></i> 519 Quezon St., New Pandan, Panabo City, Davao Del Norte</p>
                    <p><i class="fas fa-phone"></i> +639328476222</p>
                    <p><i class="fas fa-envelope"></i> psctvincacctg@gmail.com</p>
                    <p><i class="fas fa-clock"></i> Mon-Sat: 8:00 AM - 6:00 PM</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 by Panabo Satellite Cable Television Inc. Est. 1996</p>
            </div>
        </div>
    </footer>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Mobile Menu Toggle
        const mobileMenu = document.querySelector('.mobile-menu');
        const navLinks = document.querySelector('.nav-links');

        mobileMenu.addEventListener('click', () => {
            navLinks.classList.toggle('active');
            mobileMenu.querySelector('i').classList.toggle('fa-bars');
            mobileMenu.querySelector('i').classList.toggle('fa-times');
        });

        // Header Scroll Effect
        window.addEventListener('scroll', () => {
            const header = document.getElementById('header');
            if (window.scrollY > 100) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });

        // Smooth Scrolling for Anchor Links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                if(targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if(targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });
                    
                    // Close mobile menu if open
                    navLinks.classList.remove('active');
                    mobileMenu.querySelector('i').classList.add('fa-bars');
                    mobileMenu.querySelector('i').classList.remove('fa-times');
                }
            });
        });

        // Animation on Scroll
        const animateOnScroll = () => {
            const elements = document.querySelectorAll('.service-card, .plan-card, .about-text, .about-image, .contact-info, .contact-form, .stat-item');
            
            elements.forEach(element => {
                const elementPosition = element.getBoundingClientRect().top;
                const screenPosition = window.innerHeight / 1.2;
                
                if(elementPosition < screenPosition) {
                    element.classList.add('animate');
                }
            });
        };

        window.addEventListener('scroll', animateOnScroll);
        window.addEventListener('load', animateOnScroll);

        // AI Chat Functionality
        const chatToggle = document.querySelector('.chat-toggle');
        const chatContainer = document.querySelector('.chat-container');
        const chatClose = document.querySelector('.chat-close');
        const chatMessages = document.getElementById('chatMessages');
        const chatInput = document.getElementById('chatInput');
        const sendMessage = document.getElementById('sendMessage');

        chatToggle.addEventListener('click', () => {
            chatContainer.classList.toggle('active');
        });

        chatClose.addEventListener('click', () => {
            chatContainer.classList.remove('active');
        });

        function addMessage(message, isUser = false) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${isUser ? 'user' : 'bot'}`;
            messageDiv.textContent = message;
            chatMessages.appendChild(messageDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        function handleUserMessage() {
            const message = chatInput.value.trim();
            if (!message) return;

            addMessage(message, true);
            chatInput.value = '';

            // Simulate AI response
            setTimeout(() => {
                if (message.includes('@') && message.includes('.')) {
                    addMessage("Thank you! We've received your email and will contact you soon. Is there anything specific you'd like to know about our services?");
                } else {
                    addMessage("I'd love to help you! Could you please provide your email address so we can follow up with more information about our services?");
                }
            }, 1000);
        }

        sendMessage.addEventListener('click', handleUserMessage);
        chatInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                handleUserMessage();
            }
        });

        // Leaflet Map
        function initMap() {
            // PSCTV Location Coordinates (Panabo City)
            const psctvLocation = [7.3086, 125.6840];
            
            // Initialize map
            const map = L.map('map').setView(psctvLocation, 15);
            
            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            
            // Add custom marker
            const customIcon = L.divIcon({
                className: 'custom-marker',
                html: '<i class="fas fa-map-marker-alt" style="color: #10b981; font-size: 30px;"></i>',
                iconSize: [30, 30],
                iconAnchor: [15, 30]
            });
            
            // Add marker to map
            L.marker(psctvLocation, {icon: customIcon}).addTo(map)
                .bindPopup(`
                    <div style="text-align: center;">
                        <h4 style="margin: 0 0 10px 0; color: #10b981;">PSCTV Office</h4>
                        <p style="margin: 0; font-size: 14px;">519 Quezon St., New Pandan<br>Panabo City, Davao Del Norte</p>
                    </div>
                `)
                .openPopup();
        }

        // Initialize map when page loads
        document.addEventListener('DOMContentLoaded', initMap);

        // Contact Form Submission with reCAPTCHA
        const contactForm = document.getElementById('contactForm');
        const formStatus = document.getElementById('formStatus');

        contactForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            // Check if reCAPTCHA is filled
            const recaptchaResponse = grecaptcha.getResponse();
            if (!recaptchaResponse) {
                formStatus.textContent = 'Please complete the reCAPTCHA verification.';
                formStatus.className = 'form-status error';
                return;
            }

            // Show loading state
            const submitBtn = contactForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Sending...';
            submitBtn.disabled = true;

            try {
                // Get form data
                const formData = new FormData(contactForm);
                
                // Send to Laravel backend
                const response = await fetch(contactForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const result = await response.json();
                
                if (response.ok) {
                    formStatus.textContent = 'Thank you! Your message has been sent successfully. We will contact you soon.';
                    formStatus.className = 'form-status success';
                    contactForm.reset();
                    grecaptcha.reset();
                } else {
                    formStatus.textContent = 'Sorry, there was an error sending your message. Please try again.';
                    formStatus.className = 'form-status error';
                }
            } catch (error) {
                formStatus.textContent = 'Network error. Please check your connection and try again.';
                formStatus.className = 'form-status error';
            } finally {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }
        });
    </script>
</body>
</html>