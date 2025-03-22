-- SQLite veritabanı şeması

-- Kullanıcılar tablosu
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    ad VARCHAR(100) NOT NULL,
    soyad VARCHAR(100) NOT NULL,
    telefon VARCHAR(20) NOT NULL,
    user_type VARCHAR(10) NOT NULL DEFAULT 'user' CHECK(user_type IN ('admin', 'danisman', 'user')),
    status VARCHAR(10) NOT NULL DEFAULT 'active' CHECK(status IN ('pending', 'active', 'rejected')),
    myk_belgesi VARCHAR(255),
    vergi_levhasi VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- İlanlar tablosu
CREATE TABLE IF NOT EXISTS ilanlar (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    ilan_basligi VARCHAR(255) NOT NULL,
    metrekare INTEGER NOT NULL,
    fiyat DECIMAL(15,2) NOT NULL,
    ada VARCHAR(50) NOT NULL,
    parsel VARCHAR(50) NOT NULL,
    il VARCHAR(50) NOT NULL,
    ilce VARCHAR(50) NOT NULL,
    mahalle VARCHAR(100) NOT NULL,
    tkgm_link VARCHAR(255),
    foto VARCHAR(255),
    status VARCHAR(10) NOT NULL DEFAULT 'aktif' CHECK(status IN ('aktif', 'pasif')),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Blog yazıları tablosu
CREATE TABLE IF NOT EXISTS blog (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    baslik VARCHAR(255) NOT NULL,
    icerik TEXT NOT NULL,
    gorsel VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Arazi talepleri tablosu
CREATE TABLE IF NOT EXISTS arazi_talepleri (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    il VARCHAR(50) NOT NULL,
    ilce VARCHAR(50) NOT NULL,
    mahalle VARCHAR(100) NOT NULL,
    ada VARCHAR(50) NOT NULL,
    parsel VARCHAR(50) NOT NULL,
    status VARCHAR(15) NOT NULL DEFAULT 'beklemede' CHECK(status IN ('beklemede', 'teklif_verildi', 'iptal')),
    teklif_miktar DECIMAL(15,2),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- İletişim mesajları tablosu
CREATE TABLE IF NOT EXISTS iletisim (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    ad_soyad VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    telefon VARCHAR(20) NOT NULL,
    konu VARCHAR(255) NOT NULL,
    mesaj TEXT NOT NULL,
    okundu INTEGER NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Admin hesabı oluştur (şifre: admin123)
INSERT INTO users (email, password, ad, soyad, telefon, user_type, status) 
VALUES ('admin@admin.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'User', '5551234567', 'admin', 'active');