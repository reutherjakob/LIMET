CREATE TABLE users (
                       id INT AUTO_INCREMENT PRIMARY KEY,
                       username VARCHAR(50) NOT NULL UNIQUE,
                       password VARCHAR(255) NOT NULL, #hash pw
                       attempts INT DEFAULT 0,
                       last_attempt DATETIME DEFAULT NULL,
                       must_change_pw TINYINT(1) DEFAULT 1,
                       role VARCHAR(20) DEFAULT 'user' # 0: admin, 1: internal_rb_user, 2: external_rb_user, 3: spargefeld_ext_users
);

CREATE TABLE login_attempts (
                                id INT AUTO_INCREMENT PRIMARY KEY,
                                ip VARCHAR(45) NOT NULL,
                                attempt_time DATETIME NOT NULL,
                                success TINYINT(1) NOT NULL
);