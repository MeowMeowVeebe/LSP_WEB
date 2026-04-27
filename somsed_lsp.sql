CREATE DATABASE IF NOT EXISTS lsp_db;
USE lsp_db;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    bio TEXT,
    photo LONGBLOB,
    photo_type VARCHAR(100),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    text VARCHAR(250) NOT NULL,

    image LONGBLOB,
    image_type VARCHAR(100),

    file LONGBLOB,
    file_name VARCHAR(255),
    file_type VARCHAR(100),

    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    text VARCHAR(250) NOT NULL,

    image LONGBLOB,
    image_type VARCHAR(100),

    file LONGBLOB,
    file_name VARCHAR(255),
    file_type VARCHAR(100),

    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);



INSERT INTO users (username, password, bio, photo, photo_type) VALUES
('andi', '$2y$10$wH8yWlLxYz9Yj8m0y3o0kuv0qz6M6FqC7UqF5Ck9U0bFqQzYvT8bC', 'Belajar PHP native dan Tailwind CSS.', NULL, NULL),
('budi', '$2y$10$wH8yWlLxYz9Yj8m0y3o0kuv0qz6M6FqC7UqF5Ck9U0bFqQzYvT8bC', 'Suka ngoding backend dan database.', NULL, NULL),
('citra', '$2y$10$wH8yWlLxYz9Yj8m0y3o0kuv0qz6M6FqC7UqF5Ck9U0bFqQzYvT8bC', 'Frontend learner, CSS enjoyer.', NULL, NULL),
('dewi', '$2y$10$wH8yWlLxYz9Yj8m0y3o0kuv0qz6M6FqC7UqF5Ck9U0bFqQzYvT8bC', 'Lagi bikin mini sosial media.', NULL, NULL),
('eko', '$2y$10$wH8yWlLxYz9Yj8m0y3o0kuv0qz6M6FqC7UqF5Ck9U0bFqQzYvT8bC', 'Belajar CRUD untuk LSP.', NULL, NULL),
('fajar', '$2y$10$wH8yWlLxYz9Yj8m0y3o0kuv0qz6M6FqC7UqF5Ck9U0bFqQzYvT8bC', 'Ngulik login, session, dan auth.', NULL, NULL),
('gina', '$2y$10$wH8yWlLxYz9Yj8m0y3o0kuv0qz6M6FqC7UqF5Ck9U0bFqQzYvT8bC', 'Suka desain UI simple.', NULL, NULL),
('hadi', '$2y$10$wH8yWlLxYz9Yj8m0y3o0kuv0qz6M6FqC7UqF5Ck9U0bFqQzYvT8bC', 'Belajar upload file di PHP.', NULL, NULL),
('intan', '$2y$10$wH8yWlLxYz9Yj8m0y3o0kuv0qz6M6FqC7UqF5Ck9U0bFqQzYvT8bC', 'Mencoba fitur komentar.', NULL, NULL),
('joko', '$2y$10$wH8yWlLxYz9Yj8m0y3o0kuv0qz6M6FqC7UqF5Ck9U0bFqQzYvT8bC', 'Database dan SQL enthusiast.', NULL, NULL);


INSERT INTO posts (user_id, text, image, image_type, file, file_name, file_type) VALUES
(1, 'Hari ini belajar PHP native untuk routing sederhana #php #lsp', NULL, NULL, NULL, NULL, NULL),
(2, 'Membuat tabel users, posts, dan comments pakai foreign key #sql #database', NULL, NULL, NULL, NULL, NULL),
(3, 'Tailwind CSS membantu bikin UI lebih cepat #tailwind #css', NULL, NULL, NULL, NULL, NULL),
(4, 'Mini sosial media ini mulai mirip Twitter sederhana #webdev #project', NULL, NULL, NULL, NULL, NULL),
(5, 'CRUD postingan sudah bisa insert dan delete #php #crud', NULL, NULL, NULL, NULL, NULL),
(6, 'Session dipakai untuk cek user sudah login atau belum #auth #php', NULL, NULL, NULL, NULL, NULL),
(7, 'Form upload file wajib pakai enctype multipart form data #upload #php', NULL, NULL, NULL, NULL, NULL),
(8, 'Filter hashtag bisa pakai query LIKE di SQL #hashtag #sql', NULL, NULL, NULL, NULL, NULL),
(9, 'Belajar controller biar kode lebih rapi #mvc #php', NULL, NULL, NULL, NULL, NULL),
(10, 'Profile user bisa punya bio dan foto profil #profile #webdev', NULL, NULL, NULL, NULL, NULL),
(1, 'Hari kedua belajar LSP, fokus ke debugging #lsp #debugging', NULL, NULL, NULL, NULL, NULL),
(3, 'CSS layout pakai flex dan grid itu penting banget #css #frontend', NULL, NULL, NULL, NULL, NULL),
(5, 'Password harus disimpan pakai password_hash #security #php', NULL, NULL, NULL, NULL, NULL),
(7, 'Komentar bisa dibuat dengan relasi post_id dan user_id #database #comments', NULL, NULL, NULL, NULL, NULL),
(9, 'Router native PHP ternyata konsepnya mirip Laravel #router #php', NULL, NULL, NULL, NULL, NULL);


INSERT INTO comments (post_id, user_id, text, image, image_type, file, file_name, file_type) VALUES
(1, 2, 'Mantap, routing native memang penting #php', NULL, NULL, NULL, NULL, NULL),
(1, 3, 'Aku juga lagi belajar ini #lsp', NULL, NULL, NULL, NULL, NULL),
(2, 1, 'Foreign key bikin data lebih rapi #database', NULL, NULL, NULL, NULL, NULL),
(2, 4, 'Relasi tabelnya jadi jelas #sql', NULL, NULL, NULL, NULL, NULL),
(3, 5, 'Tailwind enak banget buat styling cepat #tailwind', NULL, NULL, NULL, NULL, NULL),
(3, 6, 'CSS tetap harus paham basic juga #css', NULL, NULL, NULL, NULL, NULL),
(4, 7, 'Project ini cocok buat latihan LSP #project', NULL, NULL, NULL, NULL, NULL),
(4, 8, 'Mirip Twitter versi sederhana ya #webdev', NULL, NULL, NULL, NULL, NULL),
(5, 9, 'CRUD wajib banget dikuasai #crud', NULL, NULL, NULL, NULL, NULL),
(5, 10, 'Jangan lupa validasi input #security', NULL, NULL, NULL, NULL, NULL),

(6, 1, 'Session_start jangan lupa dipanggil #php', NULL, NULL, NULL, NULL, NULL),
(6, 3, 'Auth check bisa dibuat static method #auth', NULL, NULL, NULL, NULL, NULL),
(7, 2, 'enctype itu sering kelupaan #upload', NULL, NULL, NULL, NULL, NULL),
(7, 4, 'Kalau tidak pakai enctype, file tidak masuk #php', NULL, NULL, NULL, NULL, NULL),
(8, 5, 'LIKE cocok buat filter hashtag sederhana #hashtag', NULL, NULL, NULL, NULL, NULL),
(8, 6, 'Nanti bisa dikembangkan jadi tabel hashtags #database', NULL, NULL, NULL, NULL, NULL),
(9, 7, 'MVC bikin struktur project lebih bersih #mvc', NULL, NULL, NULL, NULL, NULL),
(9, 8, 'Controller buat logic, view buat tampilan #php', NULL, NULL, NULL, NULL, NULL),
(10, 9, 'Profile foto nanti bisa pakai LONGBLOB #profile', NULL, NULL, NULL, NULL, NULL),
(10, 10, 'Bio user juga penting buat sosmed #webdev', NULL, NULL, NULL, NULL, NULL),

(11, 2, 'Debugging itu bagian paling penting #debugging', NULL, NULL, NULL, NULL, NULL),
(11, 4, 'Echo URL bisa bantu cek router #php', NULL, NULL, NULL, NULL, NULL),
(12, 1, 'Flex dan grid sering dipakai di layout #css', NULL, NULL, NULL, NULL, NULL),
(12, 5, 'Frontend jadi lebih gampang kalau paham layout #frontend', NULL, NULL, NULL, NULL, NULL),
(13, 6, 'password_hash lebih aman dari md5 #security', NULL, NULL, NULL, NULL, NULL),
(13, 7, 'Login ceknya pakai password_verify #php', NULL, NULL, NULL, NULL, NULL),
(14, 8, 'Relasi comments ke posts pakai post_id #comments', NULL, NULL, NULL, NULL, NULL),
(14, 9, 'Kalau post dihapus, comments bisa cascade #database', NULL, NULL, NULL, NULL, NULL),
(15, 10, 'Route native PHP jadi gampang kalau sudah paham #router', NULL, NULL, NULL, NULL, NULL),
(15, 1, 'Konsepnya mirip routes/web.php Laravel #php', NULL, NULL, NULL, NULL, NULL);