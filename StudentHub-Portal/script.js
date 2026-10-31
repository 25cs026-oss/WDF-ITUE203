"use strict";

document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("student-form");
  const formTitle = document.getElementById("student-form-title");
  const nameInput = document.getElementById("full-name");
  const emailInput = document.getElementById("email");
  const courseInput = document.getElementById("course");
  const submitButton = document.getElementById("submit-button");
  const cancelEditButton = document.getElementById("cancel-edit");
  const refreshButton = document.getElementById("refresh-button");
  const message = document.getElementById("api-message");
  const rows = document.getElementById("student-rows");
  const recordCount = document.getElementById("record-count");
  let csrfToken = "";
  let editingStudentId = null;
  let students = [];

  function showMessage(text, type) {
    message.textContent = text;
    message.className = "notice " + type;
    message.hidden = false;
  }

  function setLoading(isLoading) {
    refreshButton.disabled = isLoading;
    submitButton.disabled = isLoading;
    if (isLoading) {
      recordCount.textContent = "Loading student records...";
      rows.innerHTML = '<tr><td class="student-api-empty" colspan="5">Loading records...</td></tr>';
    }
  }

  async function sendRequest(url, options) {
    let response;
    try {
      response = await fetch(url, options);
    } catch (error) {
      throw new Error("Could not reach the student service. Check your connection and try again.");
    }

    let result;
    try {
      result = await response.json();
    } catch (error) {
      throw new Error("The student service returned an invalid response.");
    }
    if (!response.ok || !result.success) {
      const requestError = new Error(result.message || "The request could not be completed.");
      requestError.status = response.status;
      throw requestError;
    }
    return result;
  }

  function renderStudents() {
    recordCount.textContent = students.length + " student record(s)";
    if (!students.length) {
      rows.innerHTML = '<tr><td class="student-api-empty" colspan="5">No student records yet. Add one using the form above.</td></tr>';
      return;
    }

    rows.replaceChildren();
    students.forEach(function (student) {
      const row = document.createElement("tr");
      [student.student_id, student.full_name, student.email, student.course].forEach(function (value) {
        const cell = document.createElement("td");
        cell.textContent = value;
        row.appendChild(cell);
      });

      const actions = document.createElement("td");
      actions.className = "student-actions";
      const editButton = document.createElement("button");
      editButton.type = "button";
      editButton.className = "secondary-button";
      editButton.textContent = "Edit";
      editButton.dataset.editId = student.student_id;
      const deleteButton = document.createElement("button");
      deleteButton.type = "button";
      deleteButton.className = "danger-button";
      deleteButton.textContent = "Delete";
      deleteButton.dataset.deleteId = student.student_id;
      deleteButton.dataset.studentName = student.full_name;
      actions.append(editButton, deleteButton);
      row.appendChild(actions);
      rows.appendChild(row);
    });
  }

  async function loadStudents() {
    setLoading(true);
    try {
      const result = await sendRequest("api.php", { headers: { Accept: "application/json" } });
      csrfToken = result.data.csrf_token;
      students = result.data.students;
      renderStudents();
    } catch (error) {
      recordCount.textContent = "Student records could not be loaded.";
      rows.innerHTML = '<tr><td class="student-api-empty" colspan="5">Unable to load records.</td></tr>';
      showMessage(error.message, "error");
    } finally {
      refreshButton.disabled = false;
      submitButton.disabled = false;
    }
  }

  function resetForm() {
    editingStudentId = null;
    form.reset();
    formTitle.textContent = "Add a student";
    submitButton.textContent = "Add student";
    cancelEditButton.hidden = true;
  }

  form.addEventListener("submit", async function (event) {
    event.preventDefault();
    const payload = {
      full_name: nameInput.value.trim(),
      email: emailInput.value.trim(),
      course: courseInput.value.trim()
    };
    const isEditing = editingStudentId !== null;
    submitButton.disabled = true;
    try {
      const result = await sendRequest(
        isEditing ? "api.php?id=" + encodeURIComponent(editingStudentId) : "api.php",
        {
          method: isEditing ? "PUT" : "POST",
          headers: { "Content-Type": "application/json", "X-CSRF-Token": csrfToken },
          body: JSON.stringify(payload)
        }
      );
      showMessage(result.message, "success");
      resetForm();
      await loadStudents();
    } catch (error) {
      showMessage(error.message, "error");
    } finally {
      submitButton.disabled = false;
    }
  });

  rows.addEventListener("click", async function (event) {
    const target = event.target;
    if (!(target instanceof HTMLElement)) {
      return;
    }
    const editId = target.dataset.editId;
    if (editId) {
      const student = students.find(function (record) {
        return String(record.student_id) === editId;
      });
      if (student) {
        editingStudentId = student.student_id;
        nameInput.value = student.full_name;
        emailInput.value = student.email;
        courseInput.value = student.course;
        formTitle.textContent = "Edit student";
        submitButton.textContent = "Save changes";
        cancelEditButton.hidden = false;
        nameInput.focus();
      }
      return;
    }

    const deleteId = target.dataset.deleteId;
    if (deleteId) {
      const studentName = target.dataset.studentName || "this student";
      if (!window.confirm("Delete " + studentName + "? This action cannot be undone.")) {
        return;
      }
      target.disabled = true;
      try {
        const result = await sendRequest("api.php?id=" + encodeURIComponent(deleteId), {
          method: "DELETE",
          headers: { "X-CSRF-Token": csrfToken }
        });
        showMessage(result.message, "success");
        await loadStudents();
      } catch (error) {
        showMessage(error.message, "error");
        target.disabled = false;
      }
    }
  });

  cancelEditButton.addEventListener("click", resetForm);
  refreshButton.addEventListener("click", loadStudents);
  loadStudents();
});
