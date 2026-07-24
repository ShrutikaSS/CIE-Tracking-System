<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weightage Standards | Academic Guidelines | Zeal College</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --harvard-crimson: #A51C30;
            --harvard-dark: #1E1E1E;
            --harvard-cream: #FAF9F6;
            --harvard-gray: #4A4A4A;
            --font-serif: 'Lora', Georgia, serif;
            --font-sans: 'Inter', Helvetica, Arial, sans-serif;
        }
        
        body {
            margin: 0;
            padding: 0;
            font-family: var(--font-sans);
            background-color: var(--harvard-cream);
            color: var(--harvard-dark);
            line-height: 1.8;
            font-size: 1.05rem;
        }
        
        header {
            background-color: var(--harvard-dark);
            color: #fff;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 5px solid var(--harvard-crimson);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .header-brand {
            font-family: var(--font-serif);
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        
        .header-brand a {
            color: #fff;
            text-decoration: none;
        }
        
        .back-link {
            color: #fff;
            text-decoration: none;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            transition: color 0.3s;
        }
        
        .back-link:hover {
            color: var(--harvard-crimson);
        }
        
        .hero {
            padding: 140px 40px;
            text-align: center;
            background: linear-gradient(rgba(30,30,30,0.85), rgba(30,30,30,0.85)), url('https://images.unsplash.com/photo-1524178232363-1fb2b075b655?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') center/cover;
            color: #fff;
            border-bottom: 8px solid var(--harvard-crimson);
        }
        
        .hero-icon {
            font-size: 5rem;
            margin-bottom: 20px;
        }
        
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
        <a href="index.php#guidelines" class="back-link">← Return to Guidelines</a>
    </header>
    
    <section class="hero">
        <div class="hero-icon">⚖️</div>
        <h1>Weightage Standards</h1>
        <p>"Transparent and structured evaluation metrics for comprehensive assessment."</p>
    </section>
    
    <div class="content-container">
        <h2>Policy Overview</h2>
        <p>The policy of <strong>Weightage Standards</strong> represents a core pillar of the academic framework at Zeal College of Engineering & Research. By adhering to these principles, we maintain an environment of fairness, rigor, and respect for the scholarly process. A rigorous and fair distribution of marks across all academic activities.</p>
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
                <p>Monitoring</p>
            </div>
        </div>

        <h2>Detailed Protocols</h2>
        <p>The specific protocols related to Weightage Standards are designed to provide clarity and remove ambiguity in the evaluation process. Students are encouraged to familiarize themselves with these protocols at the beginning of each academic semester.</p>
        
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
        <p>The rationale behind the strict enforcement of Weightage Standards extends beyond mere academic bookkeeping. It is fundamentally about character building. We view the academic journey as a crucible where the habits of professional life are forged.</p>
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
        
        <p>By leveraging technology, we ensure that the policy of Weightage Standards is applied universally and impartially across the entire institution.</p>
        
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