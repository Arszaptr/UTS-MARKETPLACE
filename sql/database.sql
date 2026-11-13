CREATE TABLE users (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100) NOT NULL, email VARCHAR(100) UNIQUE NOT NULL, password VARCHAR(255) NOT NULL, role ENUM('seller','buyer') DEFAULT 'buyer');
CREATE TABLE categories (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(80) NOT NULL);
CREATE TABLE products (id INT AUTO_INCREMENT PRIMARY KEY, category_id INT NOT NULL, seller_id INT NOT NULL, name VARCHAR(150) NOT NULL, description TEXT, price DECIMAL(12,2) NOT NULL, stock INT NOT NULL DEFAULT 0, image_url VARCHAR(255), created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (category_id) REFERENCES categories(id), FOREIGN KEY (seller_id) REFERENCES users(id));
CREATE TABLE orders (id INT AUTO_INCREMENT PRIMARY KEY, buyer_id INT NULL, customer_name VARCHAR(100) NOT NULL, phone VARCHAR(20) NOT NULL, address TEXT NOT NULL, total DECIMAL(12,2) NOT NULL, status ENUM('pending','paid','shipped','done') DEFAULT 'pending', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (buyer_id) REFERENCES users(id));
CREATE TABLE order_items (id INT AUTO_INCREMENT PRIMARY KEY, order_id INT NOT NULL, product_id INT NOT NULL, qty INT NOT NULL, price DECIMAL(12,2) NOT NULL,
 FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE, FOREIGN KEY (product_id) REFERENCES products(id));
INSERT INTO users (name,email,password,role) VALUES ('Penjual Demo','seller@demo.com','$2b$10$3/dPVW6u2N49IYS6O16bmu1ifSeBrAFa4MCz0pPxQodSrLdLbx0ZK','seller');
INSERT INTO categories (name) VALUES ('Makanan'),('Kerajinan'),('Pakaian');
INSERT INTO products (category_id,seller_id,name,description,price,stock,image_url) VALUES
(1,1,'Keripik Singkong','Keripik singkong renyah khas pasar tradisional.',15000,50,'https://placehold.co/400x300?text=Keripik'),
(2,1,'Tas Anyaman Bambu','Tas anyaman tangan dari bambu pilihan.',85000,20,'https://placehold.co/400x300?text=Tas'),
(3,1,'Kain Batik Tulis','Kain batik tulis 2 meter.',250000,10,'https://placehold.co/400x300?text=Batik');
