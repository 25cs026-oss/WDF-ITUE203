import express from 'express';

const studentFields = 'student_id, full_name, email';

function validateStudent(body) {
  const errors = [];
  const fullName = typeof body.full_name === 'string' ? body.full_name.trim() : '';
  const email = typeof body.email === 'string' ? body.email.trim().toLowerCase() : '';

  if (fullName.length < 2 || fullName.length > 100) {
    errors.push('Name must contain between 2 and 100 characters.');
  }
  if (email.length > 254 || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
    errors.push('Enter a valid email address.');
  }

  return { errors, student: { full_name: fullName, email } };
}

function isValidId(value) {
  return /^[1-9]\d*$/.test(value);
}

export function createApp(pool) {
  const app = express();
  app.disable('x-powered-by');
  app.use(express.json({ limit: '16kb' }));

  app.get('/api/students', async (request, response, next) => {
    try {
      const [students] = await pool.execute(
        `SELECT ${studentFields} FROM students ORDER BY student_id`
      );
      response.json({ data: students });
    } catch (error) {
      next(error);
    }
  });

  app.get('/api/students/:id', async (request, response, next) => {
    try {
      if (!isValidId(request.params.id)) {
        return response.status(400).json({ error: 'Student ID must be a positive integer.' });
      }
      const [students] = await pool.execute(
        `SELECT ${studentFields} FROM students WHERE student_id = ?`,
        [Number(request.params.id)]
      );
      if (students.length === 0) {
        return response.status(404).json({ error: 'Student not found.' });
      }
      response.json({ data: students[0] });
    } catch (error) {
      next(error);
    }
  });

  app.post('/api/students', async (request, response, next) => {
    const body = request.body && typeof request.body === 'object' && !Array.isArray(request.body)
      ? request.body
      : {};
    const { errors, student } = validateStudent(body);
    if (errors.length > 0) {
      return response.status(422).json({ error: 'Validation failed.', details: errors });
    }

    try {
      const [result] = await pool.execute(
        'INSERT INTO students (full_name, email) VALUES (?, ?)',
        [student.full_name, student.email]
      );
      response.status(201).json({
        message: 'Student created successfully.',
        data: { student_id: result.insertId, ...student }
      });
    } catch (error) {
      if (error.code === 'ER_DUP_ENTRY') {
        return response.status(409).json({ error: 'A student with this email already exists.' });
      }
      next(error);
    }
  });

  app.put('/api/students/:id', async (request, response, next) => {
    if (!isValidId(request.params.id)) {
      return response.status(400).json({ error: 'Student ID must be a positive integer.' });
    }
    const body = request.body && typeof request.body === 'object' && !Array.isArray(request.body)
      ? request.body
      : {};
    const { errors, student } = validateStudent(body);
    if (errors.length > 0) {
      return response.status(422).json({ error: 'Validation failed.', details: errors });
    }

    try {
      const studentId = Number(request.params.id);
      const [existing] = await pool.execute(
        `SELECT ${studentFields} FROM students WHERE student_id = ?`,
        [studentId]
      );
      if (existing.length === 0) {
        return response.status(404).json({ error: 'Student not found.' });
      }

      await pool.execute(
        'UPDATE students SET full_name = ?, email = ? WHERE student_id = ?',
        [student.full_name, student.email, studentId]
      );
      response.json({
        message: 'Student updated successfully.',
        data: { student_id: studentId, ...student }
      });
    } catch (error) {
      if (error.code === 'ER_DUP_ENTRY') {
        return response.status(409).json({ error: 'A student with this email already exists.' });
      }
      next(error);
    }
  });

  app.delete('/api/students/:id', async (request, response, next) => {
    try {
      if (!isValidId(request.params.id)) {
        return response.status(400).json({ error: 'Student ID must be a positive integer.' });
      }
      const [result] = await pool.execute(
        'DELETE FROM students WHERE student_id = ?',
        [Number(request.params.id)]
      );
      if (result.affectedRows === 0) {
        return response.status(404).json({ error: 'Student not found.' });
      }
      response.json({ message: 'Student deleted successfully.' });
    } catch (error) {
      next(error);
    }
  });

  app.use((request, response) => {
    response.status(404).json({ error: 'API route not found.' });
  });

  app.use((error, request, response, next) => {
    if (response.headersSent) {
      return next(error);
    }
    if (error instanceof SyntaxError && error.status === 400 && 'body' in error) {
      return response.status(400).json({ error: 'Request body must contain valid JSON.' });
    }
    console.error('StudentHub Practical 8 API request failed.', error);
    response.status(500).json({ error: 'The API could not complete the request.' });
  });

  return app;
}
