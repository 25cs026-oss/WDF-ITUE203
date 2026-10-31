import assert from 'node:assert/strict';
import { after, before, test } from 'node:test';
import { createApp } from '../app.js';

const students = [
  { student_id: 1, full_name: 'Aarav Patel', email: 'aarav@example.test' }
];
let server;
let baseUrl;

const fakePool = {
  async execute(sql, parameters = []) {
    if (sql.startsWith('SELECT') && sql.includes('WHERE student_id = ?')) {
      return [students.filter((student) => student.student_id === parameters[0])];
    }
    if (sql.startsWith('SELECT')) {
      return [[...students].sort((left, right) => left.student_id - right.student_id)];
    }
    if (sql.startsWith('INSERT')) {
      const emailExists = students.some((student) => student.email === parameters[1]);
      if (emailExists) {
        const error = new Error('Duplicate email.');
        error.code = 'ER_DUP_ENTRY';
        throw error;
      }
      const student = {
        student_id: Math.max(0, ...students.map((row) => row.student_id)) + 1,
        full_name: parameters[0],
        email: parameters[1]
      };
      students.push(student);
      return [{ insertId: student.student_id }];
    }
    if (sql.startsWith('UPDATE')) {
      const student = students.find((row) => row.student_id === parameters[2]);
      if (!student) {
        return [{ affectedRows: 0 }];
      }
      if (students.some((row) => row.email === parameters[1] && row.student_id !== student.student_id)) {
        const error = new Error('Duplicate email.');
        error.code = 'ER_DUP_ENTRY';
        throw error;
      }
      student.full_name = parameters[0];
      student.email = parameters[1];
      return [{ affectedRows: 1 }];
    }
    if (sql.startsWith('DELETE')) {
      const index = students.findIndex((student) => student.student_id === parameters[0]);
      if (index === -1) {
        return [{ affectedRows: 0 }];
      }
      students.splice(index, 1);
      return [{ affectedRows: 1 }];
    }
    throw new Error(`Unexpected SQL in API test: ${sql}`);
  }
};

before(async () => {
  server = createApp(fakePool).listen(0);
  await new Promise((resolve) => server.once('listening', resolve));
  baseUrl = `http://127.0.0.1:${server.address().port}`;
});

after(async () => {
  await new Promise((resolve, reject) => {
    server.close((error) => (error ? reject(error) : resolve()));
  });
});

test('lists students and returns a student by ID', async () => {
  const listResponse = await fetch(`${baseUrl}/api/students`);
  assert.equal(listResponse.status, 200);
  assert.equal((await listResponse.json()).data[0].full_name, 'Aarav Patel');

  const detailResponse = await fetch(`${baseUrl}/api/students/1`);
  assert.equal(detailResponse.status, 200);
  assert.equal((await detailResponse.json()).data.student_id, 1);
});

test('creates a student and rejects invalid or duplicate input', async () => {
  const createdResponse = await fetch(`${baseUrl}/api/students`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ full_name: 'Diya Shah', email: 'DIYA@example.test' })
  });
  assert.equal(createdResponse.status, 201);
  assert.equal((await createdResponse.json()).data.email, 'diya@example.test');

  const invalidResponse = await fetch(`${baseUrl}/api/students`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ full_name: 'D', email: 'invalid' })
  });
  assert.equal(invalidResponse.status, 422);

  const duplicateResponse = await fetch(`${baseUrl}/api/students`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ full_name: 'Another Student', email: 'aarav@example.test' })
  });
  assert.equal(duplicateResponse.status, 409);
});

test('updates and deletes a student with appropriate not-found responses', async () => {
  const updateResponse = await fetch(`${baseUrl}/api/students/2`, {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ full_name: 'Diya Shah Updated', email: 'diya.updated@example.test' })
  });
  assert.equal(updateResponse.status, 200);
  assert.equal((await updateResponse.json()).data.full_name, 'Diya Shah Updated');

  const deleteResponse = await fetch(`${baseUrl}/api/students/2`, { method: 'DELETE' });
  assert.equal(deleteResponse.status, 200);

  const missingResponse = await fetch(`${baseUrl}/api/students/2`);
  assert.equal(missingResponse.status, 404);
});

test('rejects invalid IDs and malformed JSON', async () => {
  const invalidIdResponse = await fetch(`${baseUrl}/api/students/0`);
  assert.equal(invalidIdResponse.status, 400);

  const invalidJsonResponse = await fetch(`${baseUrl}/api/students`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: '{'
  });
  assert.equal(invalidJsonResponse.status, 400);
});
