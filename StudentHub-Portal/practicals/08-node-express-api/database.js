import mysql from 'mysql2/promise';

export const pool = mysql.createPool({
  host: process.env.STUDENTHUB_DB_HOST || '127.0.0.1',
  port: Number(process.env.STUDENTHUB_DB_PORT || 3306),
  database: process.env.STUDENTHUB_DB_NAME || 'studenthub_practical8',
  user: process.env.STUDENTHUB_DB_USER || 'root',
  password: process.env.STUDENTHUB_DB_PASSWORD || '',
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0,
  charset: 'utf8mb4'
});
