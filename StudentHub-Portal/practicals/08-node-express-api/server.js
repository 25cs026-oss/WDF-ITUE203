import { createApp } from './app.js';
import { pool } from './database.js';

const port = Number(process.env.PORT || 3000);

try {
  await pool.query('SELECT 1');
  const server = createApp(pool).listen(port, () => {
    console.log(`StudentHub Practical 8 API listening on http://localhost:${port}`);
  });

  server.on('error', (error) => {
    console.error('StudentHub Practical 8 API could not start.', error);
    process.exitCode = 1;
  });
} catch (error) {
  console.error('StudentHub Practical 8 API could not connect to MySQL. Check the database settings and schema.', error);
  process.exitCode = 1;
}
