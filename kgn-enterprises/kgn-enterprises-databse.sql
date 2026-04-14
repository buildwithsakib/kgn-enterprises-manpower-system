-- Create database
CREATE DATABASE IF NOT EXISTS kgn_enterprises;
USE kgn_enterprises;

-- Table: admin_users (for admin panel)
CREATE TABLE IF NOT EXISTS admin_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    role ENUM('super_admin', 'admin', 'editor') DEFAULT 'admin',
    is_active BOOLEAN DEFAULT TRUE,
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default admin (password: admin123)
INSERT INTO admin_users (username, password_hash, full_name, email, role) 
VALUES ('sakib2006', '$2y$10$R1yjDv1zQyOVGcaZAWfNc.8CAGOrT9kd9CqrzRifCJRpUEpdXOMq.', 'Sakib Shaikh', 'sakibbhaisk7@gmail.com', 'super_admin');

-- Table: login_attempts (for brute force protection)
CREATE TABLE IF NOT EXISTS login_attempts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ip_address VARCHAR(45) NOT NULL,
    attempt_time DATETIME NOT NULL,
    INDEX idx_ip_time (ip_address, attempt_time)
);

-- Table: services
CREATE TABLE IF NOT EXISTS services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(200) UNIQUE,
    description TEXT NOT NULL,
    features TEXT,
    icon VARCHAR(100) DEFAULT 'fas fa-briefcase',
    category VARCHAR(100) DEFAULT 'General',
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_active_order (is_active, display_order)
);

-- Insert sample services
INSERT INTO services (title, slug, description, features, icon, category, display_order) VALUES
('Skilled Manpower', 'skilled-manpower', 'Highly trained and experienced professionals for specialized roles including technicians, engineers, supervisors, and skilled workers with technical expertise.', 'Technical Staff & Engineers,IT Professionals,Administrative Staff,Supervisory Roles', 'fas fa-user-graduate', 'Skilled', 1),
('Unskilled Manpower', 'unskilled-manpower', 'Reliable workforce for general labor, production, packaging, and other entry-level positions across various industries with quick deployment.', 'General Laborers,Production Workers,Packaging Staff,Warehouse Workers', 'fas fa-users', 'General', 2),
('Facility Management', 'facility-management', 'Comprehensive facility management services including maintenance, security, and administrative support for smooth operations of your premises.', 'Housekeeping Staff,Security Personnel,Maintenance Crew,Administrative Support', 'fas fa-building', 'Facility', 3),
('IT Professionals', 'it-professionals', 'Qualified IT professionals including developers, network engineers, support staff, and technical specialists for your technology needs and projects.', 'Software Developers,IT Support Staff,Network Engineers,Technical Specialists', 'fas fa-laptop-code', 'IT', 4),
('Housekeeping Services', 'housekeeping-services', 'Professional housekeeping and sanitation staff for maintaining hygiene and cleanliness in offices, hospitals, and industrial premises.', 'Office Cleaning,Hospital Housekeeping,Industrial Cleaning,Sanitation Staff', 'fas fa-broom', 'Cleaning', 5),
('Industrial Workers', 'industrial-workers', 'Skilled and semi-skilled workers for manufacturing, production, warehouse, and logistics operations across various industries.', 'Factory Workers,Production Line,Warehouse Staff,Quality Control', 'fas fa-industry', 'Industrial', 6);

-- Table: clients
CREATE TABLE IF NOT EXISTS clients (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(200) NOT NULL,
    logo VARCHAR(255),
    industry VARCHAR(100),
    testimonial TEXT,
    rating DECIMAL(3,2) DEFAULT 0.00,
    website VARCHAR(255),
    since_date DATE,
    is_featured BOOLEAN DEFAULT FALSE,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_featured_order (is_featured, display_order)
);

-- Insert sample clients
INSERT INTO clients (name, industry, testimonial, rating, is_featured, display_order) VALUES
('NIYATI PVT. LTD', 'Manufacturing', 'Provided excellent manpower support for our project. Professional and reliable service.', 5.0, TRUE, 1),
('Tejas Pawale Industries', 'Manufacturing', 'Quick deployment of skilled workers. Great replacement policy and professional team.', 4.5, TRUE, 2),
('Rohit Kinker Solutions', 'IT Services', 'Best manpower service in Pune. They understand client requirements perfectly.', 5.0, TRUE, 3),
('Kute Healthcare Group', 'Healthcare', 'Professional housekeeping staff that maintain high hygiene standards in our hospitals.', 4.8, TRUE, 4),
('Manish Logistics', 'Logistics', 'Reliable workforce for our warehouse operations. Good communication and support.', 4.7, TRUE, 5),
('Sarthak Manufacturing Co.', 'Industrial', 'Consistent quality in skilled manpower supply for our production unit.', 4.6, TRUE, 6),
('Tech Solutions India', 'IT Services', 'Excellent IT professionals with relevant skills and good work ethics.', 4.9, TRUE, 7),
('City Hospital Pune', 'Healthcare', 'Professional facility management team that understands hospital requirements.', 4.8, TRUE, 8);

-- Table: testimonials
CREATE TABLE IF NOT EXISTS testimonials (
    id INT PRIMARY KEY AUTO_INCREMENT,
    client_name VARCHAR(200) NOT NULL,
    client_designation VARCHAR(200),
    company VARCHAR(200),
    testimonial TEXT NOT NULL,
    rating INT DEFAULT 5 CHECK (rating BETWEEN 1 AND 5),
    service_type VARCHAR(100),
    client_image VARCHAR(255),
    email VARCHAR(100),
    phone VARCHAR(20),
    is_approved BOOLEAN DEFAULT FALSE,
    featured BOOLEAN DEFAULT FALSE,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_approved_featured (is_approved, featured, display_order)
);
-- Insert sample testimonials
INSERT INTO testimonials (client_name, client_designation, company, testimonial, rating, service_type,client_image,email,phone, is_approved, featured) VALUES
('OMKAR UGLE', 'Manager', 'NIYATI PVT. LTD', 'Provided excellent manpower support for our project. Professional and reliable service.', 5, 'Skilled Manpower', TRUE, TRUE),
('TEJAS PAWALE', 'HR Manager', 'Manufacturing Unit', 'Quick deployment of skilled workers. Great replacement policy and professional team.', 5, 'Industrial Workers', TRUE, TRUE),
('ROHIT KINKER', 'Operations Head', 'IT Company', 'Best manpower service in Pune. They understand client requirements perfectly.', 5, 'IT Professionals', TRUE, TRUE),
('VIVEK KUTE', 'HR Manager', 'IT Services Pune', 'The IT professionals provided by KGN ENTERPRISES are well-qualified and skilled.', 5, 'IT Staffing', TRUE, TRUE),
('SARTHAK ARUDE', 'Production Head', 'Pune Manufacturing Unit', 'KGN ENTERPRISES has been our reliable manpower partner for 3 years. Their skilled workforce helped us increase production efficiency by 40%.', 5, 'Industrial Manpower', TRUE, TRUE),
('MANISH', 'Facility Manager', 'Hospital Chain', 'The housekeeping and facility management staff provided by KGN are well-trained and professional. They maintain high hygiene standards.', 5, 'Facility Management', TRUE, TRUE);

-- Table: team_members
CREATE TABLE IF NOT EXISTS team_members (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(200) NOT NULL,
    designation VARCHAR(200) NOT NULL,
    description TEXT,
    photo VARCHAR(255),
    social_linkedin VARCHAR(255),
    social_instagram VARCHAR(255),
    social_twitter VARCHAR(255),
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_active_order (is_active, display_order)
);

-- Insert team members
INSERT INTO team_members (name, designation, description, display_order, is_active) VALUES
('Mr. Javed Turuk', 'Founder & Director', 'With over 5 years of experience in the manpower industry, Mr. Javed leads KGN ENTERPRISES with vision and expertise in staffing solutions.', 1, TRUE),
('Mr. Sakib Shaikh', 'Business Development Manager', 'Expertise in operations management and client relations with 8+ years of experience in manpower deployment and facility management.', 2, TRUE),
('Mr. Swapnil Salunke', 'Operations Manager', 'Specialized in talent acquisition and workforce management with expertise in screening and deploying quality manpower.', 3, TRUE);

-- Table: jobs
CREATE TABLE IF NOT EXISTS jobs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(200) UNIQUE,
    description TEXT NOT NULL,
    job_type ENUM('full_time', 'part_time', 'contract', 'internship') DEFAULT 'full_time',
    location VARCHAR(200) NOT NULL,
    experience VARCHAR(100) NOT NULL,
    qualification VARCHAR(200) NOT NULL,
    salary_range VARCHAR(100) NOT NULL,
    deadline DATE,
    is_active BOOLEAN DEFAULT TRUE,
    posted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_active_deadline (is_active, deadline)
);

-- Insert sample jobs
INSERT INTO jobs (title, slug, location, salary_range, description) VALUES
('Cook- Semiskilled', 'cook-semiskilled', 'Pune', '18000-20000', 'Cook for reputed company\'s Guest house in Pune...'),
('Data Entry Operator', 'data-entry-operator', 'Pune', '12,000 - 18,000', 'Immediate openings for Data Entry Operators with good typing speed....'),
('HR Executive', 'hr-executive', 'Pune', '20000', 'We are looking for an experienced HR Executive to manage payroll of employees....'),
('Programmer', 'programmer', 'Pune', '25000', 'Programmer position available with required skills for development tasks...');

-- Table: job_applications
CREATE TABLE IF NOT EXISTS job_applications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    job_id INT,
    full_name VARCHAR(200) NOT NULL,
    email VARCHAR(200) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    position VARCHAR(200) NOT NULL,
    experience VARCHAR(100) NOT NULL,
    qualification VARCHAR(200) NOT NULL,
    current_ctc VARCHAR(100),
    expected_ctc VARCHAR(100) NOT NULL,
    resume_path VARCHAR(255) NOT NULL,
    cover_letter TEXT,
    status ENUM('pending', 'reviewed', 'shortlisted', 'rejected') DEFAULT 'pending',
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE SET NULL,
    INDEX idx_email_status (email, status),
    INDEX idx_applied_at (applied_at)
);

-- Table: gallery
CREATE TABLE IF NOT EXISTS gallery (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    image_url VARCHAR(255) NOT NULL,
    category VARCHAR(100) DEFAULT 'general',
    location VARCHAR(200),
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category_active (category, is_active, display_order)
);

-- Insert sample gallery items
INSERT INTO gallery (title, description, image_url, category, display_order) VALUES
('Team Meeting', 'Our team planning and strategy session for upcoming projects', 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=600&h=400&fit=crop', 'events', 1),
('Industrial Workforce', 'Skilled workers at manufacturing plant deployment', 'https://images.unsplash.com/photo-1577962917302-cd874c4e31d2?w=600&h=400&fit=crop', 'workforce', 2),
('Facility Management', 'Professional facility management team at work', 'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=600&h=400&fit=crop', 'facility', 3),
('Office Administration', 'Our skilled office administration professionals', 'https://images.unsplash.com/photo-1559028012-481c04fa702d?w=600&h=400&fit=crop', 'team', 4),
('Training Session', 'Skill development training for workforce', 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=600&h=400&fit=crop', 'events', 5);

-- Table: contact_submissions
CREATE TABLE IF NOT EXISTS contact_submissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(200) NOT NULL,
    email VARCHAR(200) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    subject VARCHAR(200),
    message TEXT NOT NULL,
    service_type VARCHAR(100),
    source VARCHAR(100) DEFAULT 'direct',
    status ENUM('pending', 'contacted', 'resolved', 'spam') DEFAULT 'pending',
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status_date (status, submitted_at),
    INDEX idx_email (email)
);

-- Table: website_settings
CREATE TABLE IF NOT EXISTS website_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('text', 'textarea', 'image', 'boolean', 'number') DEFAULT 'text',
    category VARCHAR(100) DEFAULT 'general',
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category_key (category, setting_key)
);

-- Insert default settings
INSERT INTO website_settings (setting_key, setting_value, setting_type, category) VALUES
('company_name', 'KGN ENTERPRISES', 'text', 'general'),
('company_email', 'kgnenterprises9670@gmail.com', 'text', 'contact'),
('contact_phone1', '+91 9881901568', 'text', 'contact'),
('contact_phone2', '+91 9423042591', 'text', 'contact'),
('company_address', '185 Ground, Javed Turuk, Shendewadi Phata, Kadus, Khed, Pune - 412404, India', 'textarea', 'contact'),
('about_us', 'KGN ENTERPRISES is a premier manpower services provider with 5+ years of excellence in job placement, labour supply, and contract staffing. We deploy verified skilled and unskilled professionals including programmers, office clerks, data entry operators, administrative staff, housekeeping personnel, sanitation workers, sweepers, cleaning staff, and facility management workforce for government, industrial, commercial, and institutional establishments across Maharashtra.', 'textarea', 'about'),
('linkedin_url', 'https://www.linkedin.com/in/sakib-shaikh-b5755b397', 'text', 'social'),
('instagram_url', 'https://www.instagram.com/sakib__.2006', 'text', 'social'),
('github_url', 'https://github.com/sakib92s', 'text', 'social'),
('whatsapp_number', '+919881901568', 'text', 'contact'),
('google_maps_embed', '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3780.349493835528!2d73.85694967497264!3d18.651545382480434!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bc2c9c9b8c5a5a5%3A0x8b8c5a5a5a5a5a5!2sPune%2C%20Maharashtra!5e0!3m2!1sen!2sin!4v1647851234567!5m2!1sen!2sin" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>', 'textarea', 'contact'),
('meta_description', 'KGN ENTERPRISES provides premium manpower services, job placement, and contract staffing with 5+ years experience. We supply skilled and unskilled workforce, facility management, and industrial labour across Pune and Maharashtra.', 'textarea', 'seo'),
('meta_keywords', 'manpower services Pune, job placement, labour supply, contract staffing, workforce provider, recruitment agency, manpower consultancy, facility management services, staffing services in Pune, industrial labour supply', 'textarea', 'seo'),
('logo_path', 'uploads/settings/logo.png', 'image', 'general'),
('favicon_path', 'uploads/settings/logo.png', 'image', 'general');

-- Create views for statistics
CREATE VIEW vw_dashboard_stats AS
SELECT 
    (SELECT COUNT(*) FROM contact_submissions WHERE status = 'pending') as pending_contacts,
    (SELECT COUNT(*) FROM job_applications WHERE status = 'pending') as pending_applications,
    (SELECT COUNT(*) FROM testimonials WHERE is_approved = FALSE) as pending_testimonials,
    (SELECT COUNT(*) FROM clients) as total_clients,
    (SELECT COUNT(*) FROM jobs WHERE is_active = TRUE) as active_jobs,
    (SELECT COUNT(*) FROM services WHERE is_active = TRUE) as active_services;

-- Create indexes for performance
CREATE INDEX idx_contact_submissions_source ON contact_submissions(source);
CREATE INDEX idx_jobs_posted_at ON jobs(posted_at);
CREATE INDEX idx_testimonials_created_at ON testimonials(created_at);
CREATE INDEX idx_gallery_created_at ON gallery(created_at);
CREATE INDEX idx_job_applications_status ON job_applications(status);

-- Create stored procedure for getting monthly stats
DELIMITER //
CREATE PROCEDURE GetMonthlyStats(IN year_param INT)
BEGIN
    SELECT 
        MONTH(submitted_at) as month,
        COUNT(*) as total_submissions,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN status = 'contacted' THEN 1 ELSE 0 END) as contacted,
        SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved
    FROM contact_submissions
    WHERE YEAR(submitted_at) = year_param
    GROUP BY MONTH(submitted_at)
    ORDER BY month;
END //
DELIMITER ;

-- Create trigger for updating job application status
DELIMITER //
CREATE TRIGGER before_job_application_insert
BEFORE INSERT ON job_applications
FOR EACH ROW
BEGIN
    IF NEW.position IS NULL OR NEW.position = '' THEN
        SET NEW.position = 'General Application';
    END IF;
END //
DELIMITER ;

-- Create function to calculate average rating
DELIMITER //
CREATE FUNCTION CalculateAverageRating() 
RETURNS DECIMAL(3,2)
DETERMINISTIC
BEGIN
    DECLARE avg_rating DECIMAL(3,2);
    SELECT AVG(rating) INTO avg_rating FROM testimonials WHERE is_approved = TRUE;
    RETURN IFNULL(avg_rating, 0.00);
END //
DELIMITER ;