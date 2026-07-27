<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artificial Intelligence & Data Science | Zeal College Academic Departments</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --zeal-blue: #002B49;
            --zeal-red: #D32F2F;
            --zeal-gray: #4A5568;
            --zeal-light: #F8FAFC;
            --zeal-dark: #0F172A;
            --font-serif: 'Lora', Georgia, serif;
            --font-sans: 'Inter', Helvetica, Arial, sans-serif;
        }
        
        body {
            margin: 0;
            padding: 0;
            font-family: var(--font-sans);
            background-color: var(--zeal-light);
            color: var(--zeal-dark);
            line-height: 1.8;
            font-size: 1.05rem;
        }
        
        header {
            background-color: var(--zeal-blue);
            color: #fff;
            padding: 16px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 4px solid var(--zeal-red);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .back-link {
            color: #fff;
            text-decoration: none;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: rgba(255,255,255,0.12);
            border-radius: 6px;
        }
        
        .back-link:hover {
            background: var(--zeal-red);
            color: #fff;
        }
        
        .hero {
            padding: 100px 40px;
            text-align: center;
            background: linear-gradient(rgba(0, 43, 73, 0.88), rgba(0, 43, 73, 0.88)), url('https://images.unsplash.com/photo-1541829070764-84a7d30dd3f3?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') center/cover;
            color: #fff;
            border-bottom: 6px solid var(--zeal-red);
        }
        
        .hero-icon-wrap {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px auto;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(4px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
        }
        
        .hero h1 {
            font-family: var(--font-serif);
            font-size: 3.8rem;
            margin: 0 0 16px 0;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        
        .hero p {
            font-size: 1.4rem;
            font-family: var(--font-serif);
            font-style: italic;
            max-width: 850px;
            margin: 0 auto;
            color: #E2E8F0;
        }
        
        .content-container {
            max-width: 900px;
            margin: 60px auto;
            padding: 0 40px;
        }
        
        h2 {
            font-family: var(--font-serif);
            font-size: 2.4rem;
            color: var(--zeal-blue);
            margin-top: 60px;
            margin-bottom: 24px;
            border-bottom: 2px solid var(--zeal-red);
            padding-bottom: 10px;
        }
        
        h3 {
            font-family: var(--font-serif);
            font-size: 1.8rem;
            color: var(--zeal-blue);
            margin-top: 40px;
            margin-bottom: 18px;
        }
        
        p {
            margin-bottom: 24px;
            color: var(--zeal-gray);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 24px;
            margin: 60px 0;
        }
        
        .stat-card {
            background: #fff;
            padding: 35px 25px;
            text-align: center;
            border-top: 4px solid var(--zeal-blue);
            border-radius: 8px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.06);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 36px rgba(0,0,0,0.1);
        }
        
        .stat-card h3 {
            font-size: 3.2rem;
            color: var(--zeal-red);
            margin: 0 0 10px 0;
            border: none;
            padding: 0;
        }
        
        .stat-card p {
            font-size: 0.92rem;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            margin: 0;
            font-weight: 700;
            color: var(--zeal-blue);
        }
        
        ul {
            margin-bottom: 30px;
            padding-left: 20px;
        }
        
        li {
            margin-bottom: 14px;
            color: var(--zeal-gray);
        }
        
        .course-table {
            width: 100%;
            border-collapse: collapse;
            margin: 40px 0;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }
        
        .course-table th, .course-table td {
            padding: 18px 20px;
            text-align: left;
            border-bottom: 1px solid #E2E8F0;
        }
        
        .course-table th {
            background-color: var(--zeal-blue);
            color: #fff;
            font-family: var(--font-serif);
            font-size: 1.1rem;
        }
        
        .course-table tr:hover {
            background-color: #F8FAFC;
        }
        
        .quote-block {
            margin: 60px 0;
            padding: 40px 50px;
            background: var(--zeal-blue);
            color: #fff;
            text-align: center;
            border-left: 8px solid var(--zeal-red);
            border-radius: 0 8px 8px 0;
        }
        
        .quote-block h4 {
            font-family: var(--font-serif);
            font-size: 2rem;
            font-style: italic;
            margin: 0 0 16px 0;
            font-weight: 400;
        }
        
        .quote-block p {
            color: #CBD5E1;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 0.85rem;
            margin: 0;
        }
        
        footer {
            background: var(--zeal-blue);
            color: #fff;
            text-align: center;
            padding: 45px 40px;
            font-family: var(--font-sans);
            border-top: 4px solid var(--zeal-red);
        }
    </style>
</head>
<body>
    <header>
        <div style="display: flex; align-items: center; gap: 14px;">
            <img src="assets/logo.jpg" alt="ZCOER Logo" style="height: 50px; width: auto; background: white; padding: 3px; border-radius: 6px;">
            <div style="line-height: 1.15; display: flex; flex-direction: column;">
                <span style="font-family: var(--font-serif); font-size: 1.1rem; font-weight: 800; color: #FFFFFF; letter-spacing: 0.5px; text-transform: uppercase;">ZEAL COLLEGE OF</span>
                <span style="font-family: var(--font-serif); font-size: 1.1rem; font-weight: 800; color: #FFFFFF; letter-spacing: 0.5px; text-transform: uppercase;">ENGINEERING & RESEARCH</span>
                <span style="font-size: 0.68rem; color: #CBD5E1; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">ACADEMIC DEPARTMENTS</span>
            </div>
        </div>
        <a href="index.php#departments" class="back-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            Return to Departments
        </a>
    </header>
    
    <section class="hero">
        <div class="hero-icon-wrap">
            <svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
        </div>
        
        .hero h1 {
            font-family: var(--font-serif);
            font-size: 4.5rem;
            margin: 0 0 20px 0;
            font-weight: 700;
            letter-spacing: -1px;
        }
        
        .hero p {
            font-size: 1.6rem;
            font-family: var(--font-serif);
            font-style: italic;
            max-width: 900px;
            margin: 0 auto;
            color: #E0E0E0;
        }
        
        .content-container {
            max-width: 900px;
            margin: 80px auto;
            padding: 0 40px;
        }
        
        h2 {
            font-family: var(--font-serif);
            font-size: 2.8rem;
            color: var(--harvard-crimson);
            margin-top: 80px;
            margin-bottom: 30px;
            border-bottom: 2px solid var(--harvard-crimson);
            padding-bottom: 10px;
        }
        
        h3 {
            font-family: var(--font-serif);
            font-size: 2rem;
            color: var(--harvard-dark);
            margin-top: 50px;
            margin-bottom: 20px;
        }
        
        p {
            margin-bottom: 25px;
            color: var(--harvard-gray);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 30px;
            margin: 80px 0;
        }
        
        .stat-card {
            background: #fff;
            padding: 40px 30px;
            text-align: center;
            border-top: 5px solid var(--harvard-dark);
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-10px);
        }
        
        .stat-card h3 {
            font-size: 3.5rem;
            color: var(--harvard-crimson);
            margin: 0 0 15px 0;
            border: none;
            padding: 0;
        }
        
        .stat-card p {
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin: 0;
            font-weight: 700;
            color: var(--harvard-dark);
        }
        
        ul {
            margin-bottom: 30px;
            padding-left: 20px;
        }
        
        li {
            margin-bottom: 15px;
            color: var(--harvard-gray);
        }
        
        .course-table {
            width: 100%;
            border-collapse: collapse;
            margin: 40px 0;
            background: #fff;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }
        
        .course-table th, .course-table td {
            padding: 20px;
            text-align: left;
            border-bottom: 1px solid #E0E0E0;
        }
        
        .course-table th {
            background-color: var(--harvard-dark);
            color: #fff;
            font-family: var(--font-serif);
            font-size: 1.2rem;
        }
        
        .course-table tr:hover {
            background-color: #F5F5F5;
        }
        
        .quote-block {
            margin: 80px 0;
            padding: 50px;
            background: var(--harvard-dark);
            color: #fff;
            text-align: center;
            border-left: 10px solid var(--harvard-crimson);
        }
        
        .quote-block h4 {
            font-family: var(--font-serif);
            font-size: 2.2rem;
            font-style: italic;
            margin: 0 0 20px 0;
            font-weight: 400;
        }
        
        .quote-block p {
            color: #ccc;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 0.9rem;
            margin: 0;
        }
        
        footer {
            background: var(--harvard-dark);
            color: #fff;
            text-align: center;
            padding: 60px 40px;
            font-family: var(--font-serif);
            border-top: 5px solid var(--harvard-crimson);
        }
    </style>
</head>
<body>
    <header>
        <div class="header-brand"><a href="index.php">Zeal College</a></div>
        <a href="index.php" class="back-link">← Return to Home</a>
    </header>
    
    <section class="hero">
        <div class="hero-icon">📊</div>
        <h1>Artificial Intelligence & Data Science</h1>
        <p>"Working with big data, data analytics, predictive modeling."</p>
    </section>
    
    <div class="content-container">
        <h2>Department Overview</h2>
        <p>The Department of <strong>Artificial Intelligence & Data Science</strong> represents the pinnacle of academic rigor and theoretical excellence at our institution. Founded on the principles of deep inquiry and practical application, the department has grown into a world-class center for research and education. Bridging the gap between raw data and actionable intelligent insights.</p>
        <p>Our faculty comprises distinguished scholars and industry veterans who are deeply committed to mentoring the next generation of leaders. We believe that true mastery requires both a profound understanding of fundamental principles and the ability to apply that knowledge to complex, real-world challenges.</p>
        <p>Through our comprehensive Continuous Internal Evaluation (CIE) system, we ensure that students are consistently engaged with the material. This system goes beyond traditional end-of-semester exams, incorporating frequent quizzes, in-depth project reviews, practical laboratory sessions, and comprehensive vivas.</p>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>50%</h3>
                <p>Internal Weightage</p>
            </div>
            <div class="stat-card">
                <h3>98%</h3>
                <p>Placement Rate</p>
            </div>
            <div class="stat-card">
                <h3>40+</h3>
                <p>Research Papers</p>
            </div>
        </div>

        <h2>Curriculum & Pedagogy</h2>
        <p>The curriculum for Artificial Intelligence & Data Science is constantly evolving to reflect the latest advancements in the field while maintaining a strong grounding in core fundamentals. Students undergo rigorous training in both theoretical concepts and hands-on practical implementation.</p>
        
        <h3>Core Subjects</h3>
        <ul>
            <li><strong>Foundational Theory:</strong> Deep dives into the mathematical and theoretical underpinnings of the discipline.</li>
            <li><strong>Applied Methodology:</strong> Practical application of theories through intensive laboratory work and project-based learning.</li>
            <li><strong>Advanced Seminars:</strong> Specialized topics reflecting the current cutting-edge research in the field.</li>
            <li><strong>Interdisciplinary Studies:</strong> Encouraging broad perspectives by integrating concepts from related engineering and scientific domains.</li>
        </ul>

        <table class="course-table">
            <thead>
                <tr>
                    <th>Semester</th>
                    <th>Core Focus Area</th>
                    <th>Evaluation Method</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Semester I & II</td>
                    <td>Foundational Principles & Mathematics</td>
                    <td>Unit Tests & Theoretical Exams</td>
                </tr>
                <tr>
                    <td>Semester III & IV</td>
                    <td>Core Engineering Concepts</td>
                    <td>Lab Practicals & Mini Projects</td>
                </tr>
                <tr>
                    <td>Semester V & VI</td>
                    <td>Advanced Applied Engineering</td>
                    <td>Seminars & Industrial Visits</td>
                </tr>
                <tr>
                    <td>Semester VII & VIII</td>
                    <td>Specialization & Research</td>
                    <td>Major Project Reviews & Vivas</td>
                </tr>
            </tbody>
        </table>

        <h2>Research & Innovation</h2>
        <p>Research is at the heart of the Artificial Intelligence & Data Science department. Our state-of-the-art laboratories are equipped with the latest technology, enabling students and faculty to push the boundaries of knowledge. We strongly encourage undergraduate students to participate in ongoing research projects, fostering a culture of innovation and intellectual curiosity.</p>
        <p>Current active research areas include optimizing complex systems, developing sustainable engineering practices, and exploring the societal impacts of emerging technologies. The department regularly publishes in top-tier academic journals and presents at international conferences.</p>

        <div class="quote-block">
            <h4>"The true sign of intelligence is not knowledge but imagination."</h4>
            <p>Albert Einstein — Guiding Principle of our Department</p>
        </div>

        <h2>Continuous Internal Evaluation (CIE) Strategy</h2>
        <p>To uphold our rigorous academic standards, the department strictly adheres to the college's CIE framework. Evaluation is not an event, but a continuous process designed to provide immediate feedback and foster deep learning.</p>
        
        <h3>Evaluation Metrics</h3>
        <p>Students are evaluated based on a diverse set of metrics to ensure a holistic assessment of their capabilities:</p>
        <ul>
            <li><strong>Unit Tests (20%):</strong> Scheduled periodically to test comprehension of theoretical concepts.</li>
            <li><strong>Assignments (10%):</strong> Take-home problems requiring deep analytical thinking and research.</li>
            <li><strong>Practical/Lab Sessions (10%):</strong> Assessment of hands-on skills and technical execution.</li>
            <li><strong>Project Reviews (5%):</strong> Iterative evaluation of semester-long projects, focusing on design, methodology, and implementation.</li>
            <li><strong>Seminars & Vivas (5%):</strong> Testing the student's ability to communicate complex ideas and defend their work under academic scrutiny.</li>
        </ul>
        
        <p>All marks are tracked transparently via the centralized CIE Marks Tracking Portal, ensuring complete accountability and allowing students to monitor their progress in real-time throughout the academic year.</p>
        
        <h2>Alumni & Industry Connections</h2>
        <p>Graduates from the Artificial Intelligence & Data Science department are highly sought after by top-tier corporations, research institutions, and government agencies worldwide. Our extensive alumni network provides invaluable mentorship and networking opportunities for current students.</p>
        <p>The department maintains strong ties with industry leaders, ensuring that our curriculum remains highly relevant and that our students are well-prepared for the professional challenges that lie ahead. Regular guest lectures, industrial visits, and internship programs are integral components of the student experience.</p>
        
        <br><br><br>
    </div>
    
    <footer>
        <p>&copy; 2026 Zeal College of Engineering & Research. Academic Integrity First.</p>
    </footer>
</body>
</html>