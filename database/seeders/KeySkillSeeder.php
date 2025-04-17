<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KeySkill;

class KeySkillSeeder extends Seeder
{
    public function run()
    {
        $skills = [
            // Programming Languages
            'PHP', 'JavaScript', 'TypeScript', 'Python', 'Java', 'C#', 'C++', 'Go', 'Ruby', 'Rust', 'Swift', 'Kotlin',
        
            // Frameworks & Libraries
            'Laravel', 'Symfony', 'CodeIgniter', 'CakePHP',
            'React', 'Vue.js', 'Angular', 'Next.js', 'Nuxt.js',
            'Node.js', 'Express.js', 'NestJS',
            'Django', 'Flask', 'FastAPI',
            'Spring Boot', 'Ruby on Rails',
            '.NET', '.NET Core',
            'jQuery', 'Bootstrap', 'Tailwind CSS',
            'Redux', 'Zustand', 'MobX', 'Vuex',
            'Electron', 'React Native', 'Flutter',
        
            // Databases
            'MySQL', 'PostgreSQL', 'SQLite', 'SQL Server', 'MongoDB', 'Redis', 'Firebase',
        
            // DevOps & Cloud
            'Docker', 'Kubernetes', 'AWS', 'Azure', 'Google Cloud Platform', 'DigitalOcean', 'Heroku',
            'CI/CD', 'GitHub Actions', 'Jenkins', 'Travis CI',
        
            // APIs & Standards
            'REST API', 'GraphQL', 'gRPC', 'WebSockets', 'OpenAPI (Swagger)',
        
            // Tools & Platforms
            'Git', 'GitHub', 'Bitbucket', 'GitLab',
            'VS Code', 'Postman', 'Figma', 'Jira', 'Trello',
        
            // Architecture & Concepts
            'MVC', 'MVVM', 'OOP', 'Design Patterns', 'Microservices', 'Monolith', 'Serverless',
        
            // Testing & QA
            'Unit Testing', 'Integration Testing', 'TDD', 'BDD', 'Jest', 'Mocha', 'Chai', 'PHPUnit', 'Selenium', 'Cypress',
        
            // Operating Systems & Security
            'Linux', 'Windows Server', 'MacOS', 'Networking', 'Cybersecurity', 'SSL/TLS', 'OAuth2', 'JWT',
        
            // Soft Skills
            'Agile', 'Scrum', 'DevOps', 'Project Management', 'Team Lead',
            'Technical Writing', 'Documentation', 'Communication', 'Problem Solving'
        ];
        

        foreach ($skills as $skill) {
            KeySkill::create(['name' => $skill]);
        }
    }
} 