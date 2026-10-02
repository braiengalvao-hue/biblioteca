-- Sistema de Gestão de Biblioteca Comunitária (MySQL / MariaDB)
CREATE DATABASE IF NOT EXISTS biblioteca_db
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE biblioteca_db;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS livros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    autor VARCHAR(100) NOT NULL,
    ano_publicacao INT,
    quantidade INT NOT NULL DEFAULT 1,
    UNIQUE KEY uq_livro_titulo_autor (titulo, autor)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS emprestimos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    livro_id INT NOT NULL,
    data_emprestimo DATE NOT NULL,
    data_devolucao DATE NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'Ativo',
    CONSTRAINT fk_emprestimo_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    CONSTRAINT fk_emprestimo_livro FOREIGN KEY (livro_id) REFERENCES livros(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Usuários para teste. Senha de ambos: 123456
INSERT INTO usuarios (nome, email, senha) VALUES
('Maria Souza', 'maria@email.com', '$2y$10$yJ1ljwYy3gjWtDjpo.fOZ.7Y8wlmABhHY.vbB0IVSOd.QZRVBfRYa'),
('João Pereira', 'joao@email.com', '$2y$10$yJ1ljwYy3gjWtDjpo.fOZ.7Y8wlmABhHY.vbB0IVSOd.QZRVBfRYa')
ON DUPLICATE KEY UPDATE nome = VALUES(nome), senha = VALUES(senha);

-- Dados iniciais do acervo (sem empréstimos, para não alterar o estoque).
INSERT IGNORE INTO livros (titulo, autor, ano_publicacao, quantidade) VALUES
('Dom Casmurro', 'Machado de Assis', 1899, 3),
('O Pequeno Príncipe', 'Antoine de Saint-Exupéry', 1943, 2),
('1984', 'George Orwell', 1949, 4),
('Cem Anos de Solidão', 'Gabriel García Márquez', 1967, 1),
('Clean Code', 'Robert C. Martin', 2008, 2),
('Harry Potter e a Pedra Filosofal', 'J. K. Rowling', 1997, 5);
