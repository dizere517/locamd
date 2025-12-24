USE depannage_cdl;

CREATE TABLE IF NOT EXISTS machine_documents (
  id INT AUTO_INCREMENT PRIMARY KEY,
  num_machine VARCHAR(50) NOT NULL,
  stored_name VARCHAR(255) NOT NULL,
  original_name VARCHAR(255) NOT NULL,
  mime_type VARCHAR(120) DEFAULT NULL,
  size_bytes INT DEFAULT NULL,
  uploaded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_machine_documents_num ON machine_documents (num_machine);
