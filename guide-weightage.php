<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weightage Standards | Academic Guidelines | Zeal College</title>
    <link rel="icon" type="image/jpeg" href="assets/logo.jpg">
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
            background: linear-gradient(rgba(0, 43, 73, 0.88), rgba(0, 43, 73, 0.88)), url('https://images.unsplash.com/photo-1524178232363-1fb2b075b655?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') center/cover;
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
                <span style="font-size: 0.68rem; color: #CBD5E1; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">ACADEMIC EVALUATION GUIDELINES</span>
            </div>
        </div>
        <a href="index.php#guidelines" class="back-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            Return to Guidelines
        </a>
    </header>
    
    <section class="hero">
        <div class="hero-icon-wrap">
            <svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="3" x2="12" y2="21"></line><path d="M5 6l7-3 7 3"></path><path d="M2 13l3-7 3 7a3 3 0 0 1-6 0z"></path><path d="M16 13l3-7 3 7a3 3 0 0 1-6 0z"></path></svg>
        </div>
        <h1>Weightage Standards</h1>
        <p>"Proportional distribution of internal assessment criteria to reflect holistic academic growth."</p>
    </section>
    
    <div class="content-container">
        <h2>Policy Overview</h2>
        <p>The <strong>Weightage Standards</strong> policy outlines the exact distribution of marks across Continuous Internal Evaluation (CIE) components at Zeal College of Engineering & Research. Internal evaluation contributes 50% toward final course grading, serving as a critical metric for student performance.</p>
        <p>To ensure balanced academic assessment, weightage is distributed evenly among unit tests, laboratory practicals, quizzes, seminars, and project reviews based on departmental course outcomes.</p>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>50%</h3>
                <p>CIE Weightage</p>
            </div>
            <div class="stat-card">
                <h3>6 Units</h3>
                <p>Curriculum Coverage</p>
            </div>
            <div class="stat-card">
                <h3>100%</h3>
                <p>Algorithmic Accuracy</p>
            </div>
        </div>

        <h2>Assessment Weightage Breakdown</h2>
        <p>The standard distribution of marks for theory and practical-oriented courses is structured as follows:</p>
        
        <table class="course-table">
            <thead>
                <tr>
                    <th>Assessment Type</th>
                    <th>Target Frequency</th>
                    <th>Standard Marks Weightage</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Unit Assessments / Tests</td>
                    <td>2 per Semester</td>
                    <td>40% of CIE Total</td>
                </tr>
                <tr>
                    <td>Assignments & Tutorials</td>
                    <td>Unit-wise (6 Total)</td>
                    <td>25% of CIE Total</td>
                </tr>
                <tr>
                    <td>Laboratory Practicals / Vivas</td>
                    <td>Continuous Evaluation</td>
                    <td>20% of CIE Total</td>
                </tr>
                <tr>
                    <td>Project Reviews / Seminars</td>
                    <td>2 Phase Reviews</td>
                    <td>15% of CIE Total</td>
                </tr>
            </tbody>
        </table>

        <h2>Implementation via the CIE Portal</h2>
        <p>The digital portal automatically enforces predefined weightage calculations upon mark entry by faculty members, eliminating calculation discrepancies and ensuring transparent grade publishing.</p>

        <div class="quote-block">
            <h4>"Fairness is not giving everyone the same thing. It is giving everyone what they need to succeed."</h4>
            <p>Academic Evaluation Board — Zeal College</p>
        </div>

        <br><br><br>
    </div>
    
    <footer>
        <p>&copy; 2026 Zeal College of Engineering & Research. Academic Integrity First.</p>
    </footer>
</body>
</html>