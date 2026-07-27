<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timely Submissions | Academic Guidelines | Zeal College</title>
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
            <svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><circle cx="12" cy="14" r="4"></circle><polyline points="12 12 12 14 14 14"></polyline></svg>
        </div>
        <h1>Timely Submissions</h1>
        <p>"Ensuring punctuality and professional responsibility in academic deliverables."</p>
    </section>
    
    <div class="content-container">
        <h2>Policy Overview</h2>
        <p>The policy of <strong>Timely Submissions</strong> represents a core pillar of the academic framework at Zeal College of Engineering & Research. By adhering to these principles, we maintain an environment of fairness, rigor, and respect for the scholarly process. Punctuality is a cornerstone of professional engineering and scientific practice.</p>
        <p>We expect all students, faculty, and administrative staff to uphold these guidelines rigorously. The Continuous Internal Evaluation (CIE) system relies heavily on the consistent enforcement of these standards across all departments and programs.</p>
        <p>Understanding and complying with this policy is not merely a bureaucratic requirement; it is a fundamental aspect of professional development. It prepares our students for the stringent expectations of industry and advanced academic research.</p>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>100%</h3>
                <p>Compliance Required</p>
            </div>
            <div class="stat-card">
                <h3>0</h3>
                <p>Tolerance for Breach</p>
            </div>
            <div class="stat-card">
                <h3>24/7</h3>
                <p>System Monitoring</p>
            </div>
        </div>

        <h2>Detailed Protocols</h2>
        <p>The specific protocols related to Timely Submissions are designed to provide clarity and remove ambiguity in the evaluation process. Students are encouraged to familiarize themselves with these protocols at the beginning of each academic semester.</p>
        
        <h3>Key Regulations</h3>
        <ul>
            <li><strong>Standardization:</strong> All evaluations follow a strictly standardized rubric to ensure uniformity across different subjects and evaluators.</li>
            <li><strong>Documentation:</strong> Any exceptions to the policy must be documented extensively and approved by the respective Head of Department (HOD).</li>
            <li><strong>Transparency:</strong> The criteria for evaluation are made fully transparent to students prior to the commencement of any assessment activity.</li>
            <li><strong>Appeals:</strong> A formalized grievance redressal mechanism is in place for students who believe the guidelines have been misapplied.</li>
        </ul>

        <table class="course-table">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Standard Expectation</th>
                    <th>Consequence of Non-Compliance</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Routine Assessments</td>
                    <td>Adherence to all stipulated deadlines and rubrics.</td>
                    <td>Deduction of marks or complete nullification.</td>
                </tr>
                <tr>
                    <td>Major Projects</td>
                    <td>Original work, properly cited and timely delivered.</td>
                    <td>Disciplinary committee review.</td>
                </tr>
                <tr>
                    <td>Examinations</td>
                    <td>Strict adherence to the honor code.</td>
                    <td>Immediate suspension pending investigation.</td>
                </tr>
                <tr>
                    <td>Faculty Grading</td>
                    <td>Fair, impartial, and timely evaluation.</td>
                    <td>Administrative audit and review.</td>
                </tr>
            </tbody>
        </table>

        <h2>Philosophy and Rationale</h2>
        <p>The rationale behind the strict enforcement of Timely Submissions extends beyond mere academic bookkeeping. It is fundamentally about character building. We view the academic journey as a crucible where the habits of professional life are forged.</p>
        <p>When students learn to respect deadlines, understand the weight of their evaluations, and uphold academic honesty, they are learning the very traits that will make them invaluable assets in their future careers. The discipline required here mirrors the discipline required in the real world.</p>

        <div class="quote-block">
            <h4>"Quality is not an act, it is a habit."</h4>
            <p>Aristotle — The core of our evaluation philosophy</p>
        </div>

        <h2>Implementation via the CIE Portal</h2>
        <p>The digital CIE Marks Tracking Portal has been specifically engineered to automate and enforce these guidelines, removing human error and bias from the equation wherever possible.</p>
        
        <h3>Systematic Enforcement</h3>
        <p>The system employs several mechanisms to ensure adherence:</p>
        <ul>
            <li><strong>Automated Deadlines:</strong> The portal automatically locks submissions exactly at the stipulated time, preventing unauthorized late entries.</li>
            <li><strong>Algorithmic Weightage:</strong> The system calculates final internal scores based on pre-programmed, unalterable weightage formulas set by the university.</li>
            <li><strong>Audit Trails:</strong> Every action taken by a student or faculty member is logged, providing a transparent and immutable history of the evaluation process.</li>
            <li><strong>Plagiarism Integration:</strong> (For relevant submissions) The portal interfaces with academic integrity software to flag potential violations automatically.</li>
        </ul>
        
        <p>By leveraging technology, we ensure that the policy of Timely Submissions is applied universally and impartially across the entire institution.</p>
        
        <h2>Student Resources & Support</h2>
        <p>We recognize that adhering to rigorous academic standards can be challenging. The college provides numerous resources to assist students in meeting these expectations. From time-management workshops to academic writing centers, support is always available.</p>
        <p>If you have any questions regarding the application of these guidelines to a specific course, please consult your course coordinator or the Head of Department. Open communication is actively encouraged to prevent misunderstandings and ensure academic success.</p>
        
        <br><br><br>
    </div>
    
    <footer>
        <p>&copy; 2026 Zeal College of Engineering & Research. Academic Integrity First.</p>
    </footer>
</body>
</html>