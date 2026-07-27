<?php
/**
 * Public Landing Page - Zeal Aesthetic (Massively Expanded)
 */
require_once __DIR__ . '/includes/auth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: /dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="CIE Activity Marks Tracking System Landing Page">
  <title>Zeal College of Engineering & Research — CIE Marks Tracker</title>
  
  <link rel="icon" href="assets/logo.jpg">
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <style>
    /* Reset & Base Variables */
    :root {
      --zeal-blue: #0d3a71;
      --zeal-black: #1e1e1e;
      --zeal-gray: #4A4A4A;
      --zeal-light-gray: #f3f3f1;
      --white: #ffffff;
      
      --font-serif: 'Lora', Georgia, serif;
      --font-sans: 'Inter', Helvetica, Arial, sans-serif;
    }
    
    * { margin: 0; padding: 0; box-sizing: border-box; }
    html { scroll-behavior: smooth; }
    
    body {
      font-family: var(--font-sans);
      color: var(--zeal-black);
      background-color: var(--white);
      line-height: 1.6;
      overflow-x: hidden;
    }
    
    h1, h2, h3, h4, h5, h6 {
      font-family: var(--font-serif);
      font-weight: 700;
      line-height: 1.2;
    }
    
    a { color: var(--zeal-blue); text-decoration: none; transition: color 0.3s; }
    a:hover { color: #092850; text-decoration: underline; }
    
    /* Utility Classes */
    .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
    .text-center { text-align: center; }
    
    .colorful-hr {
      border: none;
      height: 6px;
      background: var(--zeal-blue);
      width: 100px;
      margin: 20px 0;
    }
    .colorful-hr.center { margin: 20px auto; }
    
    /* Scroll Animation Classes */
    .animate-on-scroll {
      opacity: 0;
      transform: translateY(40px);
      transition: opacity 0.8s ease-out, transform 0.8s ease-out;
    }
    .animate-on-scroll.is-visible {
      opacity: 1;
      transform: translateY(0);
    }
    
    /* Header Navigation */
    .header {
      background: var(--white);
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      position: sticky;
      top: 0;
      z-index: 1000;
      padding: 15px 0;
      transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .header.nav-hidden {
      transform: translateY(-100%);
    }
    .header-inner { display: flex; justify-content: space-between; align-items: center; }
    .brand { display: flex; align-items: center; gap: 15px; }
    .brand-icon { font-size: 2.5rem; line-height: 1; }
    .brand-text { display: flex; flex-direction: column; }
    .brand-title {
      font-family: var(--font-serif); font-size: 1.4rem; font-weight: 700;
      color: var(--zeal-black); text-transform: uppercase; letter-spacing: 1px;
    }
    .brand-subtitle { font-size: 0.8rem; color: var(--zeal-gray); font-weight: 500; text-transform: uppercase; }
    
    .nav-links { display: flex; gap: 30px; align-items: center; }
    .nav-links a {
      font-weight: 600; font-size: 0.95rem; color: var(--zeal-black);
      text-transform: uppercase; letter-spacing: 1px; text-decoration: none; position: relative;
    }
    .nav-links a:hover { color: var(--zeal-blue); }
    .nav-links a::after {
      content: ''; position: absolute; width: 0; height: 2px; bottom: -4px; left: 0;
      background-color: var(--zeal-blue); transition: width 0.3s;
    }
    .nav-links a:hover::after { width: 100%; }
    
    .btn-login {
      background: var(--zeal-blue); color: var(--white) !important;
      padding: 10px 24px; font-family: var(--font-sans); font-weight: 600;
      text-transform: uppercase; letter-spacing: 1px; border: none; cursor: pointer; transition: background 0.3s;
    }
    .btn-login:hover { background: #092850; text-decoration: none !important; }
    
    /* Hero Banner */
    .hero {
      position: relative;
      height: 100vh;
      min-height: 700px;
      display: flex;
      align-items: center;
      background-color: var(--zeal-black);
      background-image: url('https://images.unsplash.com/photo-1541339907198-e08756dedf3f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
    }
    .hero::before {
      content: ''; position: absolute; inset: 0;
      background: linear-gradient(90deg, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.3) 100%);
    }
    .hero-content {
      position: relative; z-index: 10;
      max-width: 800px; color: var(--white);
      animation: heroFadeIn 1s ease-out;
    }
    @keyframes heroFadeIn {
      from { opacity: 0; transform: translateX(-50px); }
      to { opacity: 1; transform: translateX(0); }
    }
    .hero h1 { font-size: 5rem; margin-bottom: 20px; line-height: 1.1; }
    .hero p { font-size: 1.4rem; margin-bottom: 40px; font-weight: 300; max-width: 650px; }
    /* Feature Blocks Section (New Smaller Variations) */
    .feature-blocks-section { padding: 80px 0; background: var(--zeal-light-gray); }
    .feature-block { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: center; margin-bottom: 80px; }
    .feature-block:last-child { margin-bottom: 0; }
    .feature-block.reverse .feature-text { order: 2; }
    .feature-block.reverse .feature-img-wrap { order: 1; }
    
    .feature-text h2 { font-size: 2.2rem; margin-bottom: 15px; color: var(--zeal-black); line-height: 1.2; font-family: var(--font-serif); font-weight: 500; }
    .feature-text p { font-size: 1.05rem; color: var(--zeal-gray); margin-bottom: 25px; font-weight: 400; line-height: 1.6; }
    .btn-arrow {
      display: inline-flex; align-items: center; gap: 10px; color: var(--zeal-black);
      font-weight: 700; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 1px;
    }
    .btn-arrow .icon-circle {
      width: 35px; height: 35px; border-radius: 50%; background: var(--zeal-black);
      color: var(--white); display: flex; align-items: center; justify-content: center;
      transition: transform 0.3s, background 0.3s;
    }
    .btn-arrow:hover .icon-circle { transform: translateX(5px); background: var(--zeal-blue); }
    .btn-arrow:hover { text-decoration: none; }
    .feature-img-wrap img { width: 100%; height: auto; aspect-ratio: 4/3; object-fit: cover; border-radius: 8px; filter: drop-shadow(0 10px 20px rgba(0,0,0,0.1)); }
 
    /* Message from the Dean Section (New) */
    .dean-section { padding: 120px 0; background: var(--white); }
    .dean-grid { display: grid; grid-template-columns: 1fr 2fr; gap: 60px; align-items: center; }
    .dean-img { width: 100%; border-radius: 50%; border: 8px solid var(--white); box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
    .dean-quote { font-family: var(--font-serif); font-size: 2rem; font-style: italic; color: var(--zeal-blue); margin-bottom: 20px; line-height: 1.4; }
    .dean-name { font-weight: 700; font-size: 1.2rem; text-transform: uppercase; letter-spacing: 1px; color: var(--zeal-black); }
    .dean-title { color: var(--zeal-gray); }
 
    /* Key Statistics Section (New) */
    .stats-section {
      background: var(--zeal-black);
      color: var(--white);
      padding: 100px 0;
      background-image: url('https://images.unsplash.com/photo-1524178232363-1fb2b075b655?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      position: relative;
    }
    .stats-section::before {
      content: ''; position: absolute; inset: 0;
      background: rgba(13, 58, 113, 0.85); /* Zeal Blue overlay */
    }
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 40px; position: relative; z-index: 10; text-align: center; }
    .stat-number { font-family: var(--font-serif); font-size: 4rem; font-weight: 700; margin-bottom: 10px; }
    .stat-label { font-size: 1.2rem; text-transform: uppercase; letter-spacing: 2px; font-weight: 600; }
 
    /* About Section (Split Layout) */
    .about-section { padding: 120px 0; background: var(--white); }
    .about-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center; }
    .about-img-wrap { position: relative; }
    .about-img-wrap img { width: 100%; height: auto; box-shadow: 0 20px 40px rgba(0,0,0,0.15); display: block; }
    .about-img-wrap::after {
      content: ''; position: absolute; bottom: -20px; right: -20px;
      width: 100%; height: 100%; border: 5px solid var(--zeal-blue); z-index: -1;
    }
    .about-text h2 { font-size: 3rem; margin-bottom: 20px; color: var(--zeal-black); }
    .about-text p { font-size: 1.15rem; color: var(--zeal-gray); margin-bottom: 25px; }
    
    /* Academic Departments Section (New) */
    .departments-section { padding: 120px 0; background: var(--zeal-light-gray); }
    .dept-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 40px; margin-top: 50px; }
    .dept-card { background: var(--white); padding: 40px 30px; text-align: center; border-radius: 12px; border: 1px solid rgba(0,0,0,0.03); transition: border-color 0.4s, transform 0.4s, box-shadow 0.4s; box-shadow: 0 4px 15px rgba(0,0,0,0.03); position: relative; overflow: hidden; display: flex; flex-direction: column; align-items: center; justify-content: flex-start; height: 100%; }
    .dept-card::after { content: ''; position: absolute; bottom: 0; left: 0; width: 100%; height: 5px; background: var(--zeal-blue); transform: scaleX(0); transition: transform 0.4s ease; transform-origin: left; }
    .dept-card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(0,0,0,0.08); border-color: transparent; }
    .dept-card:hover::after { transform: scaleX(1); }
    .dept-icon { margin-bottom: 20px; display: inline-block; }
    .dept-icon img { width: 90px; height: 90px; border-radius: 50%; object-fit: cover; transition: transform 0.4s, box-shadow 0.4s; box-shadow: 0 8px 20px rgba(0,0,0,0.1); }
    .dept-card:hover .dept-icon img { transform: scale(1.1); box-shadow: 0 12px 25px rgba(0,0,0,0.15); }
    .dept-card h3 { font-size: 1.35rem; margin-bottom: 15px; font-weight: 700; color: var(--zeal-black); }
    .dept-card p { color: var(--zeal-gray); font-size: 0.98rem; line-height: 1.6; margin: 0; }
 
    /* Image Gallery Section (New) */
    .gallery-section { padding: 120px 0; background: var(--white); }
    .gallery-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      grid-template-rows: repeat(2, 300px);
      gap: 20px;
      margin-top: 50px;
    }
    .gallery-item { background-size: cover; background-position: center; position: relative; overflow: hidden; }
    .gallery-item:hover::before { content: ''; position: absolute; inset: 0; background: rgba(13, 58, 113, 0.4); }
    .gallery-item-large { grid-column: span 2; grid-row: span 2; }
    
    /* Guidelines Section (Light Mode) */
    .guidelines-section { padding: 120px 0; background: var(--zeal-light-gray); color: var(--zeal-black); }
    .guidelines-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 40px; margin-top: 50px; }
    .guideline-card { background: var(--white); padding: 40px; border-radius: 12px; border-top: 5px solid var(--zeal-blue); border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 4px 20px rgba(0,0,0,0.04); transition: transform 0.3s, box-shadow 0.3s; text-align: center; }
    .guideline-card:hover { transform: translateY(-8px); box-shadow: 0 15px 35px rgba(0,0,0,0.08); }
    .guideline-card .icon { margin-bottom: 20px; font-size: 4rem; }
    .guideline-card .icon img { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; transition: transform 0.3s; border: 3px solid var(--zeal-blue); }
    .guideline-card h3 { font-size: 1.6rem; margin-bottom: 15px; color: var(--zeal-black); }
    .guideline-card p { color: var(--zeal-gray); }
    
    /* Academic Calendar Table Section */
    .calendar-section { padding: 120px 0; background: var(--white); }
    .calendar-table-wrapper { overflow-x: auto; margin-top: 40px; box-shadow: 0 10px 40px rgba(0,0,0,0.08); }
    .zeal-table { width: 100%; border-collapse: collapse; background: var(--white); }
    .zeal-table th { background: var(--zeal-black); color: var(--white); padding: 25px 20px; text-align: left; font-family: var(--font-serif); font-size: 1.3rem; }
    .zeal-table td { padding: 25px 20px; border-bottom: 1px solid #eaeaea; color: var(--zeal-gray); font-size: 1.1rem; }
    .zeal-table tr:hover td { background: var(--zeal-light-gray); }
    .zeal-table td strong { color: var(--zeal-blue); font-family: var(--font-serif); font-size: 1.2rem; }
    
    /* News & Notices Grid */
    .news-section { padding: 120px 0; background: var(--zeal-light-gray); }
    .news-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 40px; margin-top: 50px; }
    .news-card {
      background: var(--white); overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.04); border-radius: 12px; border: 1px solid rgba(0,0,0,0.03);
      transition: transform 0.4s, box-shadow 0.4s; display: flex; flex-direction: column;
    }
    .news-card:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(0,0,0,0.08); }
    .news-img { height: 250px; background-size: cover; background-position: center; position: relative; }
    .news-date {
      position: absolute; bottom: 0; left: 0; background: var(--zeal-blue); color: var(--white);
      padding: 12px 20px; font-weight: 700; font-family: var(--font-serif);
    }
    .news-content { padding: 40px; flex-grow: 1; }
    .news-content h3 { font-size: 1.5rem; margin-bottom: 15px; }
    .news-content p { color: var(--zeal-gray); font-size: 1.05rem; }
    
    /* Support Banner */
    .support-banner { background: var(--zeal-blue); padding: 100px 0; color: var(--white); text-align: center; }
    .support-banner h2 { font-size: 3.5rem; margin-bottom: 20px; }
    .support-banner p { font-size: 1.3rem; margin-bottom: 40px; opacity: 0.9; }
    .btn-outline {
      display: inline-block; padding: 18px 50px; border: 2px solid var(--white); color: var(--white);
      font-weight: 700; text-transform: uppercase; letter-spacing: 1px; transition: background 0.3s, color 0.3s; font-size: 1.1rem;
    }
    .btn-outline:hover { background: var(--white); color: var(--zeal-blue); text-decoration: none; }
    
    /* Footer */
    .footer { background: var(--zeal-black); color: #cccccc; padding: 100px 0 50px; font-family: var(--font-sans); }
    .footer-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 60px; margin-bottom: 80px; }
    .footer-col h4 { color: var(--white); font-size: 1.3rem; margin-bottom: 30px; text-transform: uppercase; letter-spacing: 1px; }
    .footer-col ul { list-style: none; }
    .footer-col ul li { margin-bottom: 15px; }
    .footer-col ul li a { color: #cccccc; font-size: 1.05rem; }
    .footer-col ul li a:hover { color: var(--white); text-decoration: underline; }
    .footer-bottom { text-align: center; padding-top: 40px; border-top: 1px solid #333; font-size: 0.95rem; }
    
    /* Modal Styles */
    .modal-overlay {
      display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 2000;
      align-items: center; justify-content: center; backdrop-filter: blur(5px);
    }
    .modal-content {
      background: var(--white); width: 100%; max-width: 420px; padding: 40px 35px;
      border-top: 6px solid var(--zeal-blue); border-radius: 4px; position: relative;
      animation: modalFadeIn 0.3s ease-out forwards; max-height: 95vh; overflow-y: auto;
    }
    @keyframes modalFadeIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
    .modal-close { position: absolute; top: 15px; right: 20px; background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--zeal-gray); }
    .modal-content h2 { font-size: 1.8rem; margin-bottom: 5px; color: var(--zeal-black); }
    .modal-subtitle { color: var(--zeal-gray); margin-bottom: 20px; font-style: italic; font-family: var(--font-serif); font-size: 1rem; }
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; font-weight: 600; margin-bottom: 6px; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.5px; }
    .form-control { width: 100%; padding: 12px; border: 1px solid #ccc; font-family: var(--font-sans); font-size: 1rem; transition: border-color 0.3s; border-radius: 4px; }
    .form-control:focus { outline: none; border-color: var(--zeal-blue); }
    .btn-submit {
      width: 100%; background: var(--zeal-blue); color: var(--white); border: none; padding: 14px;
      font-size: 1.1rem; font-family: var(--font-serif); font-weight: 700; cursor: pointer; transition: background 0.3s; border-radius: 4px;
    }
    .btn-submit:hover { background: #092850; }
    .login-error { display: none; background: #faeaea; color: var(--zeal-blue); padding: 12px; margin-bottom: 20px; border-left: 4px solid var(--zeal-blue); font-weight: 500; font-size: 0.95rem; }
    
    .demo-accounts { margin-top: 25px; padding-top: 15px; border-top: 1px solid #eee; }
    .demo-item { display: flex; justify-content: space-between; padding: 10px 14px; background: var(--zeal-light-gray); margin-bottom: 8px; cursor: pointer; transition: background 0.2s; font-size: 0.95rem; border-radius: 4px; }
    .demo-item:hover { background: #e5e5e5; }
    .demo-role { color: var(--zeal-blue); font-weight: 700; font-size: 0.85rem; text-transform: uppercase; }
    
    @media (max-width: 992px) {
      .dept-grid { grid-template-columns: repeat(2, 1fr); gap: 30px; }
      .gallery-grid { grid-template-columns: 1fr 1fr; }
      .gallery-item-large { grid-column: span 1; grid-row: span 1; }
    }
    @media (max-width: 768px) {
      .dept-grid { grid-template-columns: 1fr; }
      .nav-links { display: none; }
      .hero h1 { font-size: 3.5rem; }
      .about-grid, .dean-grid { grid-template-columns: 1fr; }
      .about-img-wrap::after { display: none; }
      .gallery-grid { grid-template-columns: 1fr; }
    }

    /* Glassmorphism Login Modal Override */
    #modal-login {
      background: rgba(15, 23, 42, 0.4);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
    }
    #modal-login .modal-content {
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.45);
      border-top: none;
      box-shadow: 0 25px 60px rgba(0, 0, 0, 0.25);
      max-width: 820px;
      padding: 0;
      display: flex;
      flex-direction: row;
      border-radius: 12px;
      overflow: hidden;
    }
    @media (max-width: 768px) {
      #modal-login .modal-content {
        flex-direction: column;
        max-width: 90%;
      }
      .login-brand-panel {
        display: none !important;
      }
      .login-form-panel {
        width: 100% !important;
        padding: 30px 20px !important;
      }
    }
  </style>
</head>
<body>

  <!-- Header -->
  <header class="header">
    <div class="container header-inner">
      <div class="brand">
        <img src="assets/logo.jpg" alt="ZCOER Logo" style="height: 65px; width: auto; object-fit: contain;">
        <div class="brand-text" style="line-height: 1.15; display: flex; flex-direction: column; justify-content: center; margin-left: 10px;">
          <span class="brand-title" style="font-family: var(--font-serif); font-size: 1.25rem; font-weight: 800; color: var(--zeal-blue); letter-spacing: 0.5px; text-transform: uppercase;">ZEAL COLLEGE OF</span>
          <span class="brand-title" style="font-family: var(--font-serif); font-size: 1.25rem; font-weight: 800; color: var(--zeal-blue); letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 2px;">ENGINEERING & RESEARCH</span>
          <span class="brand-subtitle" style="font-size: 0.72rem; color: var(--zeal-gray); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">CIE MARKS TRACKING PORTAL</span>
        </div>
      </div>
      <nav class="nav-links">
        <a href="#about">About</a>
        <a href="#departments">Departments</a>
        <a href="#guidelines">Guidelines</a>
        <a href="#calendar">Calendar</a>
        <a href="#notices">Notices</a>
        <button class="btn-login" onclick="openModal('modal-login')">Portal Login</button>
      </nav>
    </div>
  </header>

  <!-- Hero Section (Massive) -->
  <section class="hero">
    <div class="container">
      <div class="hero-content">
        <hr class="colorful-hr" style="margin: 0 0 30px 0;">
        <h1>Continuous Internal Evaluation</h1>
        <p>A secure, transparent, and rigorous digital platform for managing academic assessments, student performance, and departmental excellence at Zeal College of Engineering & Research, Pune.</p>
        <button class="btn-login" style="padding: 20px 45px; font-size: 1.2rem; border: 2px solid var(--white);" onclick="openModal('modal-login')">Access the Portal</button>
      </div>
    </div>
  </section>

  <!-- Feature Blocks Section (Smaller, Stacked) -->
  <section class="feature-blocks-section">
    <div class="container">
      
      <!-- Block 1 -->
      <div class="feature-block animate-on-scroll">
        <div class="feature-text">
          <h2>Calling all educators</h2>
          <p>Continuous Internal Evaluation is responsible for an estimated 50% of the final academic grading at the university, and serves as the mainstay of our rigorous quality assurance metrics.</p>
          <a href="#guidelines" class="btn-arrow">
            <span class="icon-circle">➔</span>
            Learn about the classic evaluation
          </a>
        </div>
        <div class="feature-img-wrap">
          <img src="https://images.unsplash.com/photo-1532012197267-da84d127e765?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Book Illustration">
        </div>
      </div>

      <!-- Block 2 (Reverse) -->
      <div class="feature-block reverse animate-on-scroll">
        <div class="feature-text">
          <h2>Data-driven insights</h2>
          <p>Utilize powerful analytics to track student cohort performance in real-time. Uncover trends and rapidly address learning gaps before final examinations commence.</p>
          <a href="#about" class="btn-arrow">
            <span class="icon-circle">➔</span>
            Explore analytical tools
          </a>
        </div>
        <div class="feature-img-wrap">
          <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Analytics Graph">
        </div>
      </div>

      <!-- Block 3 -->
      <div class="feature-block animate-on-scroll">
        <div class="feature-text">
          <h2>Streamlined reporting</h2>
          <p>Eliminate manual spreadsheet calculations. The portal automatically aggregates unit tests, project reviews, and vivas into cohesive, exportable academic reports.</p>
          <a href="#departments" class="btn-arrow">
            <span class="icon-circle">➔</span>
            View reporting standards
          </a>
        </div>
        <div class="feature-img-wrap">
          <img src="https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Reporting and Papers">
        </div>
      </div>

      <!-- Block 4 (Reverse) -->
      <div class="feature-block reverse animate-on-scroll">
        <div class="feature-text">
          <h2>Departmental synergy</h2>
          <p>Standardize evaluation criteria across multiple disciplines while retaining the flexibility required by specialized engineering and science labs.</p>
          <a href="#calendar" class="btn-arrow">
            <span class="icon-circle">➔</span>
            Align your schedule
          </a>
        </div>
        <div class="feature-img-wrap">
          <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Team Synergy">
        </div>
      </div>

    </div>
  </section>



  <!-- Key Statistics (New) -->
  <section class="stats-section">
    <div class="container stats-grid animate-on-scroll">
      <div>
        <div class="stat-number">12+</div>
        <div class="stat-label">Academic Departments</div>
      </div>
      <div>
        <div class="stat-number">4,500</div>
        <div class="stat-label">Active Students</div>
      </div>
      <div>
        <div class="stat-number">150k</div>
        <div class="stat-label">Assessments Tracked</div>
      </div>
      <div>
        <div class="stat-number">100%</div>
        <div class="stat-label">Evaluation Transparency</div>
      </div>
    </div>
  </section>

  <!-- About Section (Expanded) -->
  <section id="about" class="about-section">
    <div class="container about-grid">
      <div class="about-text animate-on-scroll">
        <hr class="colorful-hr">
        <h2>Advancing Academic Integrity</h2>
        <p>The Continuous Internal Evaluation (CIE) portal replaces traditional paperwork with a centralized, secure digital framework designed specifically for tracking internal evaluations across all academic departments.</p>
        <p>By enforcing role-based access controls and maintaining rigorous audit trails, the system ensures that every assignment, quiz, and project review is handled with the utmost transparency and precision.</p>
        <p>Faculty members can seamlessly publish activities, while administrators generate comprehensive analytics to monitor cohort progress effortlessly.</p>
        <button class="btn-login" style="margin-top: 20px;" onclick="openModal('modal-login')">Authenticate Now</button>
      </div>
      <div class="about-img-wrap animate-on-scroll">
        <img id="slideshow-img" src="assets/slide1.jpg" alt="Campus Life" style="transition: opacity 0.5s ease-in-out; opacity: 1;">
      </div>
    </div>
  </section>

  <!-- Academic Departments Grid (New) -->
  <section id="departments" class="departments-section">
    <div class="container">
      <div class="text-center animate-on-scroll">
        <hr class="colorful-hr center">
        <h2 class="section-title">Participating Departments</h2>
        <p style="max-width: 700px; margin: 0 auto; color: var(--zeal-gray); font-size: 1.1rem;">The CIE system is universally adopted across our prestigious schools and faculties.</p>
      </div>
      
      <div class="dept-grid animate-on-scroll">
        <a href="dept-cse.php" class="dept-card" style="text-decoration: none; color: inherit;">
          <div class="dept-icon"><img src="https://images.unsplash.com/photo-1550751827-4bd374c3f58b?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80" alt="Department Icon"></div>
          <h3>Computer Science Engineering</h3>
          <p>Tracking algorithms, code reviews, and theoretical exams.</p>
        </a>
        <a href="dept-aiml.php" class="dept-card" style="text-decoration: none; color: inherit;">
          <div class="dept-icon"><img src="https://images.unsplash.com/photo-1555255707-c07966088b7b?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80" alt="Department Icon"></div>
          <h3>Artificial Intelligence & Machine Learning (AIML)</h3>
          <p>Training AI models, machine learning algorithms, deep learning applications, computer vision, and intelligent systems.</p>
        </a>
        <a href="dept-aids.php" class="dept-card" style="text-decoration: none; color: inherit;">
          <div class="dept-icon"><img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80" alt="Department Icon"></div>
          <h3>Artificial Intelligence & Data Science (AIDS)</h3>
          <p>Working with big data, data analytics, predictive modeling, data visualization, and AI-driven insights.</p>
        </a>
        <a href="dept-it.php" class="dept-card" style="text-decoration: none; color: inherit;">
          <div class="dept-icon"><img src="https://images.unsplash.com/photo-1451187580459-43490279c0fa?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80" alt="Department Icon"></div>
          <h3>Information Technology (IT)</h3>
          <p>Managing software development, web technologies, cloud computing, networking, databases, and cybersecurity projects.</p>
        </a>
        <a href="dept-robotics.php" class="dept-card" style="text-decoration: none; color: inherit;">
          <div class="dept-icon"><img src="https://images.unsplash.com/photo-1485827404703-89b55fcc595e?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80" alt="Department Icon"></div>
          <h3>Robotics & Automation</h3>
          <p>Evaluating robotics projects, automation systems, embedded controllers, industrial automation, and control engineering.</p>
        </a>
        <a href="dept-mechanical.php" class="dept-card" style="text-decoration: none; color: inherit;">
          <div class="dept-icon"><img src="https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80" alt="Department Icon"></div>
          <h3>Mechanical Engineering</h3>
          <p>Monitoring lab reports, CAD projects, and thermodynamics vivas.</p>
        </a>
        <a href="dept-civil.php" class="dept-card" style="text-decoration: none; color: inherit;">
          <div class="dept-icon"><img src="https://images.unsplash.com/photo-1503387762-592deb58ef4e?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80" alt="Civil Engineering Icon"></div>
          <h3>Civil Engineering</h3>
          <p>Evaluating structural design projects and material testing labs.</p>
        </a>
        <a href="dept-electrical.php" class="dept-card" style="text-decoration: none; color: inherit;">
          <div class="dept-icon"><img src="https://images.unsplash.com/photo-1473341304170-971dccb5ac1e?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80" alt="Department Icon"></div>
          <h3>Electrical Engineering</h3>
          <p>Assessing power systems, electrical machines, circuit design, renewable energy systems, and control engineering labs.</p>
        </a>
        <a href="dept-entc.php" class="dept-card" style="text-decoration: none; color: inherit;">
          <div class="dept-icon"><img src="https://images.unsplash.com/photo-1518770660439-4636190af475?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80" alt="Department Icon"></div>
          <h3>Electronics & Telecommunication Engineering (ENTC)</h3>
          <p>Evaluating communication systems, signal processing, VLSI design, embedded systems, and wireless networks.</p>
        </a>
      </div>
    </div>
  </section>

  <!-- Image Gallery (New) -->
  <section class="gallery-section">
    <div class="container">
      <div class="text-center animate-on-scroll">
        <hr class="colorful-hr center">
        <h2 class="section-title">Campus Life & Academics</h2>
      </div>
      <div class="gallery-grid animate-on-scroll">
        <div class="gallery-item gallery-item-large" style="background-image: url('https://images.unsplash.com/photo-1541829070764-84a7d30dd3f3?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80');"></div>
        <div class="gallery-item" style="background-image: url('https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80');"></div>
        <div class="gallery-item" style="background-image: url('https://images.unsplash.com/photo-1497633762265-9d179a990aa6?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80');"></div>
      </div>
    </div>
  </section>

  <!-- Guidelines Section (Light Mode) -->
  <section id="guidelines" class="guidelines-section">
    <div class="container">
      <div class="text-center animate-on-scroll">
        <hr class="colorful-hr center">
        <h2 class="section-title" style="margin-bottom: 20px; color: var(--zeal-black);">Evaluation Guidelines</h2>
        <p style="max-width: 600px; margin: 0 auto; color: var(--zeal-gray); font-size: 1.1rem;">Standardized protocols ensuring fairness and clarity in all internal academic assessments.</p>
      </div>
      
      <div class="guidelines-grid animate-on-scroll">
        <a href="guide-submissions.php" class="guideline-card" style="text-decoration: none; color: inherit; display: block;">
          <div class="icon" style="display:flex; justify-content:center; align-items:center; height:60px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="var(--zeal-blue)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><circle cx="12" cy="14" r="4"></circle><polyline points="12 12 12 14 14 14"></polyline></svg>
          </div>
          <h3>Timely Submissions</h3>
          <p>All assignments and reports must be uploaded prior to the stipulated deadlines. Late submissions require formal HOD authorization.</p>
        </a>
        <a href="guide-weightage.php" class="guideline-card" style="text-decoration: none; color: inherit; display: block;">
          <div class="icon" style="display:flex; justify-content:center; align-items:center; height:60px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="var(--zeal-blue)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="3" x2="12" y2="21"></line><path d="M5 6l7-3 7 3"></path><path d="M2 13l3-7 3 7a3 3 0 0 1-6 0z"></path><path d="M16 13l3-7 3 7a3 3 0 0 1-6 0z"></path></svg>
          </div>
          <h3>Weightage Standards</h3>
          <p>Internal marks constitute 50% of the final semester grade. Quizzes, practicals, and vivas are distributed evenly per department rubrics.</p>
        </a>
        <a href="guide-honesty.php" class="guideline-card" style="text-decoration: none; color: inherit; display: block;">
          <div class="icon" style="display:flex; justify-content:center; align-items:center; height:60px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="var(--zeal-blue)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><path d="M9 12l2 2 4-4"></path></svg>
          </div>
          <h3>Academic Honesty</h3>
          <p>Strict plagiarism checks are enforced. Any violations of the academic honor code will result in immediate disciplinary action.</p>
        </a>
      </div>
    </div>
  </section>

  <!-- Academic Calendar Table Section -->
  <section id="calendar" class="calendar-section">
    <div class="container animate-on-scroll">
      <hr class="colorful-hr">
      <h2 class="section-title" style="text-align: left; margin-bottom: 20px;">Academic Calendar: Fall 2026</h2>
      <p style="color: var(--zeal-gray); margin-bottom: 50px; font-size: 1.2rem;">Key dates for internal assessments, project reviews, and final submissions.</p>
      
      <div class="calendar-table-wrapper">
        <table class="zeal-table">
          <thead>
            <tr>
              <th>Date</th>
              <th>Activity / Event</th>
              <th>Applicable Departments</th>
              <th>Action Required</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><strong>Aug 01, 2026</strong></td>
              <td>Commencement of Fall Classes</td>
              <td>All Departments</td>
              <td>Student Onboarding</td>
            </tr>
            <tr>
              <td><strong>Aug 25, 2026</strong></td>
              <td>Assignment 1 Publishing Deadline</td>
              <td>Engineering, Sciences</td>
              <td>Faculty Publish</td>
            </tr>
            <tr>
              <td><strong>Sep 15 - Sep 20, 2026</strong></td>
              <td>Midterm Internal Assessment (Unit Test 1)</td>
              <td>All Departments</td>
              <td>Student Attendance</td>
            </tr>
            <tr>
              <td><strong>Oct 10, 2026</strong></td>
              <td>Project Review Phase 1</td>
              <td>Computer Science, IT</td>
              <td>Presentation Upload</td>
            </tr>
            <tr>
              <td><strong>Nov 05, 2026</strong></td>
              <td>Final Viva & Practical Marks Entry</td>
              <td>All Departments</td>
              <td>Faculty Marks Entry</td>
            </tr>
            <tr>
              <td><strong>Nov 20, 2026</strong></td>
              <td>CIE Marks Freeze & Final Publication</td>
              <td>All Departments</td>
              <td>Admin Verification</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </section>

  <!-- News & Notices Section -->
  <section id="notices" class="news-section">
    <div class="container">
      <div class="text-center animate-on-scroll">
        <hr class="colorful-hr center">
        <h2 class="section-title">Latest University Notices</h2>
      </div>
      
      <div class="news-grid">
        <!-- News Card 1 -->
        <div class="news-card animate-on-scroll">
          <div class="news-img" style="background-image: url('https://images.unsplash.com/photo-1498243691581-b145c3f54a5a?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80');">
            <div class="news-date">July 15, 2026</div>
          </div>
          <div class="news-content">
            <h3>New Portal Version Deployed</h3>
            <p>Version 1.0.0 of the CIE Marks Tracking System has been successfully deployed. Faculty can now utilize the new 'Practical' and 'Project Review' activity types.</p>
          </div>
        </div>
        
        <!-- News Card 2 -->
        <div class="news-card animate-on-scroll" style="transition-delay: 0.1s;">
          <div class="news-img" style="background-image: url('https://images.unsplash.com/photo-1524178232363-1fb2b075b655?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80');">
            <div class="news-date">July 10, 2026</div>
          </div>
          <div class="news-content">
            <h3>HOD Dashboard Upgrades</h3>
            <p>Department heads now have access to advanced automated analytics, allowing for real-time tracking of faculty grading progress and student averages.</p>
          </div>
        </div>
        
        <!-- News Card 3 -->
        <div class="news-card animate-on-scroll" style="transition-delay: 0.2s;">
          <div class="news-img" style="background-image: url('https://images.unsplash.com/photo-1434030216411-0b793f4b4173?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80');">
            <div class="news-date">July 05, 2026</div>
          </div>
          <div class="news-content">
            <h3>Student Profile Standardization</h3>
            <p>All student records must now include both PRN Numbers and Roll Numbers. Coordinators are requested to update missing data via the admin panel.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Support Banner -->
  <section class="support-banner">
    <div class="container animate-on-scroll">
      <h2>Need Assistance?</h2>
      <p>The College IT Help Desk is available 24/7 to assist faculty and students with portal access issues.</p>
      <a href="contact.php" class="btn-outline">Contact IT Support</a>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer">
    <div class="container">
      <div class="footer-grid">
        <div class="footer-col">
          <h4>Zeal College of Engineering & Research</h4>
          <p style="margin-bottom: 10px; font-family: var(--font-serif); font-size: 1.1rem;">Department of Computer Science & Engineering</p>
          <p style="color: #999; line-height: 1.8;">Narhe, Pune - 411041<br>Phone: +91-20-6720 6000<br>Email: support.zcoer@zealedu.in</p>
        </div>
        <div class="footer-col">
          <h4>Portal Resources</h4>
          <ul>
            <li><a href="#about">About CIE Portal</a></li>
            <li><a href="#guidelines">Academic Guidelines</a></li>
            <li><a href="#calendar">Semester Calendar</a></li>
            <li><a href="#" onclick="alert('Help desk is currently offline.')">IT Help Desk</a></li>
          </ul>
        </div>
        <div class="footer-col">
          <h4>Legal & Compliance</h4>
          <ul>
            <li><a href="#">Privacy Policy</a></li>
            <li><a href="#">Terms of Service</a></li>
            <li><a href="#">Accessibility Statement</a></li>
            <li><a href="#">Data Protection Policy</a></li>
          </ul>
        </div>
      </div>
      <div class="footer-bottom">
        &copy; <?= date('Y') ?> Zeal College of Engineering & Research. CIE Marks Tracking System Version 1.2.0.
      </div>
    </div>
  </footer>

  <!-- Login Modal Overlay -->
  <div class="modal-overlay" id="modal-login">
    <div class="modal-content">
      
      <!-- Left Side: Brand Panel -->
      <div class="login-brand-panel" style="width: 40%; background: linear-gradient(135deg, #0d3a71 0%, #061e3d 100%); padding: 40px 30px; color: white; display: flex; flex-direction: column; justify-content: space-between; position: relative; overflow: hidden; border-top-left-radius: 12px; border-bottom-left-radius: 12px;">
        <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; border-radius: 50%; background: rgba(255,255,255,0.03); pointer-events: none;"></div>
        <div style="position: absolute; bottom: -80px; left: -80px; width: 250px; height: 250px; border-radius: 50%; background: rgba(255,255,255,0.02); pointer-events: none;"></div>
        
        <div style="z-index: 2;">
          <img src="assets/logo.jpg" alt="ZCOER Logo" style="height: 65px; width: auto; border-radius: 6px; margin-bottom: 24px; filter: brightness(1.1); box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
          <h3 style="color: white; font-size: 1.6rem; line-height: 1.3; font-family: var(--font-serif); font-weight: 700; margin-bottom: 12px;">CIE Marks Portal</h3>
          <p style="font-size: 0.875rem; opacity: 0.8; font-weight: 300; line-height: 1.5; font-family: var(--font-sans);">Access your academic evaluation records, marks trends, and activity submissions on our secure internal platform.</p>
        </div>
        
        <div style="z-index: 2; font-size: 0.75rem; opacity: 0.6; line-height: 1.4; font-family: var(--font-sans);">
          Zeal College of Engineering & Research<br>
          Narhe, Pune - 411041
        </div>
      </div>

      <!-- Right Side: Form Panel -->
      <div class="login-form-panel" style="width: 60%; padding: 40px 35px; position: relative; display: flex; flex-direction: column; justify-content: center; background: white; border-top-right-radius: 12px; border-bottom-right-radius: 12px;">
        <button class="modal-close" onclick="closeModal('modal-login')" style="position: absolute; top: 15px; right: 20px;">✕</button>
        <h2 style="font-size: 1.6rem; color: var(--zeal-black); margin-bottom: 4px;">Portal Login</h2>
        <p class="modal-subtitle" style="margin-bottom: 24px; font-size: 0.9rem;">Authenticate to access secure academic records</p>
        
        <div class="login-error" id="login-error" style="margin-bottom: 20px; display: none;">
          <span id="login-error-text"></span>
        </div>
        
        <form id="login-form" onsubmit="handleLogin(event)">
          <div class="form-group" style="margin-bottom: 18px;">
            <label for="email" style="font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase;">College Email</label>
            <input type="email" id="email" class="form-control" placeholder="user@zealedu.in" required style="border-radius: 6px; height: 42px;">
          </div>
          
          <div class="form-group" style="margin-bottom: 20px;">
            <label for="password" style="font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase;">Password</label>
            <input type="password" id="password" class="form-control" required placeholder="••••••••" style="border-radius: 6px; height: 42px;">
          </div>
          
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; font-size: 0.85rem;">
            <label style="cursor: pointer; display: flex; align-items: center; gap: 8px;">
              <input type="checkbox" id="remember"> Remember me
            </label>
            <a href="#" onclick="openForgotModal(event)" style="color: var(--zeal-blue); font-weight: 600; text-decoration: none;">Forgot Password?</a>
          </div>
          
          <button type="submit" class="btn-submit" id="login-btn" style="border-radius: 6px; padding: 12px; font-size: 1rem; letter-spacing: 0.5px; width: 100%;">Secure Login</button>
        </form>
        
        <!-- Demo Accounts -->
        <div class="demo-accounts" style="margin-top: 24px; border-top: 1px solid rgba(0,0,0,0.06); padding-top: 16px;">
          <h4 style="margin-bottom: 12px; font-size: 0.8rem; color: var(--zeal-gray); text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Click to Auto-Fill & Test</h4>
          
          <div style="display: flex; flex-wrap: wrap; gap: 8px;">
            <button type="button" class="btn btn-sm btn-secondary" onclick="fillDemoTyping('admin@cie.edu', 'Admin')" style="font-size:0.75rem; padding: 6px 12px; border-radius: 4px; text-transform:none; font-weight: 500;">Admin</button>
            <button type="button" class="btn btn-sm btn-secondary" onclick="fillDemoTyping('hod.cse@cie.edu', 'HOD')" style="font-size:0.75rem; padding: 6px 12px; border-radius: 4px; text-transform:none; font-weight: 500;">HOD</button>
            <button type="button" class="btn btn-sm btn-secondary" onclick="fillDemoTyping('anil.mehta@cie.edu', 'Faculty')" style="font-size:0.75rem; padding: 6px 12px; border-radius: 4px; text-transform:none; font-weight: 500;">Faculty</button>
            <button type="button" class="btn btn-sm btn-secondary" onclick="fillDemoTyping('sneha.patil@cie.edu', 'Coordinator')" style="font-size:0.75rem; padding: 6px 12px; border-radius: 4px; text-transform:none; font-weight: 500;">Coordinator</button>
            <button type="button" class="btn btn-sm btn-secondary" onclick="fillDemoTyping('rahul.verma@cie.edu', 'Student')" style="font-size:0.75rem; padding: 6px 12px; border-radius: 4px; text-transform:none; font-weight: 500;">Student</button>
          </div>
        </div>
      </div>
    </div>
  </div>
 
  <!-- Forgot Password Modal -->
  <div class="modal-overlay" id="modal-forgot">
    <div class="modal-content">
      <button class="modal-close" onclick="closeModal('modal-forgot')">✕</button>
      <h2>Reset Password</h2>
      <p class="modal-subtitle">Secure password recovery protocol</p>
      
      <div id="forgot-alert" style="display: none; padding: 15px; margin-bottom: 25px; font-size: 1.05rem;"></div>
 
      <form onsubmit="handleForgot(event)">
        <div class="form-group">
          <label>College Email</label>
          <input type="email" id="forgot-email" class="form-control" placeholder="user@zealedu.in" required>
        </div>
        <button type="submit" class="btn-submit" id="forgot-btn">Send Recovery Link</button>
      </form>
    </div>
  </div>

  <script>
    /* Navbar Auto-hide Logic (Entire Landing Page) */
    (function() {
      const header = document.querySelector('.header');
      if (!header) return;
      
      let lastScrollY = window.scrollY;
      let ticking = false;

      function updateNavbar() {
        const currentScrollY = window.scrollY;

        if (currentScrollY <= 0) {
          // At the very top, always show
          header.classList.remove('nav-hidden');
        } else {
          if (currentScrollY > lastScrollY) {
            // Scrolling down -> hide
            header.classList.add('nav-hidden');
          } else {
            // Scrolling up -> show
            header.classList.remove('nav-hidden');
          }
        }
        
        lastScrollY = currentScrollY;
        ticking = false;
      }

      window.addEventListener('scroll', () => {
        if (!ticking) {
          window.requestAnimationFrame(updateNavbar);
          ticking = true;
        }
      });
    })();

    /* Scroll Animation Logic */
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
        }
      });
    }, { threshold: 0.1 });

    document.querySelectorAll('.animate-on-scroll').forEach((el) => {
      observer.observe(el);
    });

    /* Vanilla CountUp Animation Logic for Stats Section */
    const statsObserver = new IntersectionObserver((entries, obs) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const stats = entry.target.querySelectorAll('.stat-number');
          stats.forEach(stat => {
            if (stat.getAttribute('data-counted')) return;
            stat.setAttribute('data-counted', 'true');
            
            const text = stat.textContent.trim();
            const numMatch = text.match(/[\d,.]+/);
            if (!numMatch) return;
            
            const numStr = numMatch[0];
            const targetNum = parseFloat(numStr.replace(/,/g, ''));
            const hasComma = numStr.includes(',');
            
            const prefix = text.substring(0, text.indexOf(numStr));
            const suffix = text.substring(text.indexOf(numStr) + numStr.length);
            
            const duration = 2000;
            let startTime = null;
            
            function easeOutQuad(t) {
              return t * (2 - t);
            }
            
            function countAnimation(currentTime) {
              if (!startTime) startTime = currentTime;
              const progress = Math.min((currentTime - startTime) / duration, 1);
              const easeProgress = easeOutQuad(progress);
              
              const currentNum = targetNum * easeProgress;
              
              if (progress < 1) {
                let displayNum = Math.floor(currentNum);
                if (hasComma) displayNum = displayNum.toLocaleString('en-US');
                stat.textContent = prefix + displayNum + suffix;
                window.requestAnimationFrame(countAnimation);
              } else {
                let finalDisplay = targetNum;
                if (hasComma) finalDisplay = finalDisplay.toLocaleString('en-US');
                stat.textContent = prefix + finalDisplay + suffix;
              }
            }
            
            window.requestAnimationFrame(countAnimation);
          });
          obs.unobserve(entry.target);
        }
      });
    }, { threshold: 0.5 });
    
    const statsSection = document.querySelector('.stats-grid');
    if (statsSection) {
      statsObserver.observe(statsSection);
    }

    /* Modal Logic */
    function openModal(id) {
      document.getElementById(id).style.display = 'flex';
      if(id === 'modal-login') document.getElementById('email').focus();
    }
    
    function closeModal(id) {
      document.getElementById(id).style.display = 'none';
      if(id === 'modal-login') {
        document.getElementById('login-form').reset();
        document.getElementById('login-error').style.display = 'none';
      }
    }
    
    function openForgotModal(e) {
      e.preventDefault();
      closeModal('modal-login');
      openModal('modal-forgot');
      document.getElementById('forgot-email').focus();
      document.getElementById('forgot-alert').style.display = 'none';
    }

    // Close modals on clicking overlay background
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
      overlay.addEventListener('click', (e) => {
        if (e.target === overlay) closeModal(overlay.id);
      });
    });

    let typingInterval = null;

    function fillDemoTyping(demoEmail, roleName) {
      if (typingInterval) clearInterval(typingInterval);

      const emailInput = document.getElementById('email');
      const passwordInput = document.getElementById('password');
      const loginBtn = document.getElementById('login-btn');

      emailInput.value = '';
      passwordInput.value = '';
      emailInput.classList.remove('error');
      passwordInput.classList.remove('error');

      let emailIndex = 0;
      let passwordIndex = 0;
      const demoPassword = 'password123';

      emailInput.focus();

      typingInterval = setInterval(() => {
        if (emailIndex < demoEmail.length) {
          emailInput.value += demoEmail[emailIndex];
          emailIndex++;
        } else {
          clearInterval(typingInterval);
          passwordInput.focus();

          typingInterval = setInterval(() => {
            if (passwordIndex < demoPassword.length) {
              passwordInput.value += demoPassword[passwordIndex];
              passwordIndex++;
            } else {
              clearInterval(typingInterval);
              typingInterval = null;
              
              // Highlight submit button
              loginBtn.style.transform = 'scale(1.02)';
              setTimeout(() => { loginBtn.style.transform = ''; }, 150);
            }
          }, 30);
        }
      }, 20);
    }

    function fillDemo(email) {
      fillDemoTyping(email, '');
    }
    
    async function handleLogin(e) {
      e.preventDefault();
      const btn = document.getElementById('login-btn');
      const errorDiv = document.getElementById('login-error');
      const errorText = document.getElementById('login-error-text');
      
      const email = document.getElementById('email').value.trim();
      const password = document.getElementById('password').value;
      
      if (!email || !password) {
        errorText.textContent = 'All fields are required for authentication.';
        errorDiv.style.display = 'block'; 
        return;
      }
      
      btn.disabled = true;
      btn.innerHTML = 'Authenticating...';
      errorDiv.style.display = 'none';
      
      try {
        const response = await fetch('/api/auth.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ action: 'login', email, password })
        });
        const data = await response.json();
        
        if (data.success) {
          window.location.href = '/dashboard.php';
        } else {
          errorText.textContent = data.message || 'Authentication failed.';
          errorDiv.style.display = 'block';
          btn.disabled = false; 
          btn.innerHTML = 'Secure Login';
        }
      } catch (err) {
        errorText.textContent = 'Network communication error.';
        errorDiv.style.display = 'block';
        btn.disabled = false; 
        btn.innerHTML = 'Secure Login';
      }
    }

    async function handleForgot(e) {
      e.preventDefault();
      const btn = document.getElementById('forgot-btn');
      const alertBox = document.getElementById('forgot-alert');
      
      btn.disabled = true;
      btn.textContent = 'Processing...';
      
      setTimeout(() => {
        alertBox.style.display = 'block';
        alertBox.style.background = '#e6f4ea';
        alertBox.style.color = '#1e8e3e';
        alertBox.style.borderLeft = '5px solid #1e8e3e';
        alertBox.textContent = 'If the email exists in the system, a recovery link has been dispatched.';
        btn.disabled = false;
        btn.textContent = 'Send Recovery Link';
      }, 1000);
    }

    /* About Image Slideshow Logic (Changes every 3 seconds) */
    const slideshowImg = document.getElementById('slideshow-img');
    const slides = [
      'assets/slide1.jpg',
      'assets/slide2.jpg'
    ];
    let currentSlide = 0;
    
    setInterval(() => {
      if (slideshowImg) {
        slideshowImg.style.opacity = 0;
        setTimeout(() => {
          currentSlide = (currentSlide + 1) % slides.length;
          slideshowImg.src = slides[currentSlide];
          slideshowImg.style.opacity = 1;
        }, 500);
      }
    }, 3000);
  </script>
</body>
</html>
