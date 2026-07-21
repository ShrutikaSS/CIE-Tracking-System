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
    .dept-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 40px; margin-top: 50px; }
    .dept-card { background: var(--white); padding: 40px 30px; text-align: center; border-radius: 12px; border: 1px solid rgba(0,0,0,0.03); transition: border-color 0.4s, transform 0.4s, box-shadow 0.4s; box-shadow: 0 4px 15px rgba(0,0,0,0.03); position: relative; overflow: hidden; }
    .dept-card::after { content: ''; position: absolute; bottom: 0; left: 0; width: 100%; height: 5px; background: var(--zeal-blue); transform: scaleX(0); transition: transform 0.4s ease; transform-origin: left; }
    .dept-card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(0,0,0,0.08); border-color: transparent; }
    .dept-card:hover::after { transform: scaleX(1); }
    .dept-icon { font-size: 4rem; margin-bottom: 20px; transition: transform 0.4s; }
    .dept-card:hover .dept-icon { transform: scale(1.1); }
    .dept-card h3 { font-size: 1.4rem; margin-bottom: 15px; }
    .dept-card p { color: var(--zeal-gray); }
 
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
    
    /* Guidelines Section */
    .guidelines-section { padding: 120px 0; background: var(--zeal-black); color: var(--white); }
    .guidelines-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 40px; margin-top: 50px; }
    .guideline-card { background: #222; padding: 40px; border-radius: 12px; border-top: 5px solid var(--zeal-blue); transition: transform 0.3s, box-shadow 0.3s; }
    .guideline-card:hover { transform: translateY(-8px); box-shadow: 0 15px 30px rgba(0,0,0,0.3); }
    .guideline-card .icon { font-size: 3.5rem; margin-bottom: 20px; }
    .guideline-card h3 { font-size: 1.6rem; margin-bottom: 15px; color: var(--white); }
    .guideline-card p { color: #cccccc; }
    
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
      .gallery-grid { grid-template-columns: 1fr 1fr; }
      .gallery-item-large { grid-column: span 1; grid-row: span 1; }
    }
    @media (max-width: 768px) {
      .nav-links { display: none; }
      .hero h1 { font-size: 3.5rem; }
      .about-grid, .dean-grid { grid-template-columns: 1fr; }
      .about-img-wrap::after { display: none; }
      .gallery-grid { grid-template-columns: 1fr; } }
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
        <div class="dept-card">
          <div class="dept-icon">💻</div>
          <h3>Computer Science</h3>
          <p>Tracking algorithms, code reviews, and theoretical exams.</p>
        </div>
        <div class="dept-card">
          <div class="dept-icon">⚙️</div>
          <h3>Mechanical Engineering</h3>
          <p>Monitoring lab reports, CAD projects, and thermodynamics vivas.</p>
        </div>
        <div class="dept-card">
          <div class="dept-icon">🏗️</div>
          <h3>Civil Engineering</h3>
          <p>Evaluating structural design projects and material testing labs.</p>
        </div>
        <div class="dept-card">
          <div class="dept-icon">🧬</div>
          <h3>Biotechnology</h3>
          <p>Recording research data, practical lab skills, and thesis reviews.</p>
        </div>
        <div class="dept-card">
          <div class="dept-icon">📊</div>
          <h3>Business Administration</h3>
          <p>Scoring case studies, group presentations, and finance assignments.</p>
        </div>
        <div class="dept-card">
          <div class="dept-icon">⚖️</div>
          <h3>Law & Jurisprudence</h3>
          <p>Assessing mock trials, legal drafting, and constitutional debates.</p>
        </div>
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
        <div class="gallery-item" style="background-image: url('https://images.unsplash.com/photo-1519452314544-fc6a992e5a7b?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80');"></div>
      </div>
    </div>
  </section>

  <!-- Guidelines Section (Dark Mode) -->
  <section id="guidelines" class="guidelines-section">
    <div class="container">
      <div class="text-center animate-on-scroll">
        <hr class="colorful-hr center" style="background: var(--white);">
        <h2 class="section-title" style="margin-bottom: 20px; color: var(--white);">Evaluation Guidelines</h2>
        <p style="max-width: 600px; margin: 0 auto; color: #ccc; font-size: 1.1rem;">Standardized protocols ensuring fairness and clarity in all internal academic assessments.</p>
      </div>
      
      <div class="guidelines-grid animate-on-scroll">
        <div class="guideline-card">
          <div class="icon">📝</div>
          <h3>Timely Submissions</h3>
          <p>All assignments and reports must be uploaded prior to the stipulated deadlines. Late submissions require formal HOD authorization.</p>
        </div>
        <div class="guideline-card">
          <div class="icon">⚖️</div>
          <h3>Weightage Standards</h3>
          <p>Internal marks constitute 50% of the final semester grade. Quizzes, practicals, and vivas are distributed evenly per department rubrics.</p>
        </div>
        <div class="guideline-card">
          <div class="icon">🛡️</div>
          <h3>Academic Honesty</h3>
          <p>Strict plagiarism checks are enforced. Any violations of the academic honor code will result in immediate disciplinary action.</p>
        </div>
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
      <a href="mailto:support.zcoer@zealedu.in" class="btn-outline">Contact IT Support</a>
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
      <button class="modal-close" onclick="closeModal('modal-login')">✕</button>
      <h2>Portal Login</h2>
      <p class="modal-subtitle">Authenticate to access secure academic records</p>
      
      <div class="login-error" id="login-error">
        <span id="login-error-text"></span>
      </div>
      
      <form id="login-form" onsubmit="handleLogin(event)">
        <div class="form-group">
          <label for="email">College Email</label>
          <input type="email" id="email" class="form-control" placeholder="user@zealedu.in" required>
        </div>
        
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" class="form-control" required>
        </div>
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; font-size: 0.95rem;">
          <label style="cursor: pointer; display: flex; align-items: center; gap: 8px;">
            <input type="checkbox" id="remember"> Remember credentials
          </label>
          <a href="#" onclick="openForgotModal(event)">Forgot Password?</a>
        </div>
        
        <button type="submit" class="btn-submit" id="login-btn">Secure Login</button>
      </form>
      
      <!-- Demo Accounts -->
      <div class="demo-accounts">
        <h4 style="margin-bottom: 20px; font-size: 0.9rem; color: var(--zeal-gray); text-transform: uppercase;">Testing Credentials</h4>
        
        <div class="demo-item" onclick="fillDemo('admin@cie.edu')">
          <span>admin@cie.edu</span> <span class="demo-role">Admin</span>
        </div>
        <div class="demo-item" onclick="fillDemo('hod.cse@cie.edu')">
          <span>hod.cse@cie.edu</span> <span class="demo-role">HOD</span>
        </div>
        <div class="demo-item" onclick="fillDemo('anil.mehta@cie.edu')">
          <span>anil.mehta@cie.edu</span> <span class="demo-role">Faculty</span>
        </div>
        <div class="demo-item" onclick="fillDemo('sneha.patil@cie.edu')">
          <span>sneha.patil@cie.edu</span> <span class="demo-role">Coord</span>
        </div>
        <div class="demo-item" onclick="fillDemo('rahul.verma@cie.edu')">
          <span>rahul.verma@cie.edu</span> <span class="demo-role">Student</span>
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

    function fillDemo(email) {
      document.getElementById('email').value = email;
      document.getElementById('password').value = 'password123';
      document.getElementById('email').focus();
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
