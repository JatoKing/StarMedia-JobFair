CREATE TABLE IF NOT EXISTS career_talk_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    speaker VARCHAR(150) NOT NULL,
    session_time DATETIME NOT NULL,
    capacity INT NOT NULL DEFAULT 20,
    seats_taken INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (session_id) REFERENCES career_talk_sessions(id),
    INDEX idx_session (session_id),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS exhibitor_registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(150) NOT NULL,
    contact_person VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    category VARCHAR(50) NOT NULL,
    message TEXT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pending',
    created_at DATETIME NOT NULL,
    INDEX idx_email (email),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS contact_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    message TEXT NOT NULL,
    created_at DATETIME NOT NULL,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed data: sesi job matching / career talk (TODO: tukar ikut jadual sebenar)
INSERT INTO career_talk_sessions (title, speaker, session_time, capacity, seats_taken) VALUES
('Kickstart Kerjaya Dalam Teknologi', 'Encik Faizal Rahman, CTO Nexora Technologies', '2026-09-15 10:00:00', 30, 0),
('Job Matching: Kewangan & Perbankan', 'Puan Suraya Ismail, HR Manager MapleTrust Bank', '2026-09-15 11:30:00', 25, 0),
('Panduan Temuduga Berkesan', 'Encik Zulkifli Hamid, Career Coach', '2026-09-15 14:00:00', 40, 0),
('Peluang Kerjaya Sektor Pembuatan', 'Puan Aisyah Kamal, HR Ferroline Industries', '2026-09-16 10:00:00', 20, 0),
('Job Matching: Runcit & Perkhidmatan', 'Encik Hafiz Aziz, Talent Acquisition Vantro Retail', '2026-09-16 13:00:00', 25, 0),
('Job Matching: Perkhidmatan & Perundingan', 'Puan Aina Zulaikha, HR Manager BrightPath Consulting', '2026-09-16 15:30:00', 25, 0),
('Personal Branding untuk Pencari Kerja', 'Puan Farah Liyana, Personal Branding Coach', '2026-09-16 16:30:00', 35, 0);