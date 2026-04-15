<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Task Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .navbar { background: #0d6efd; color: white; }
        .task-card { border-left: 5px solid #0d6efd; margin-bottom: 15px; transition: 0.3s; }
        .task-card.completed { border-left-color: #198754; opacity: 0.7; }
        .task-card.completed .task-title { text-decoration: line-through; }
        .fab { position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px; border-radius: 50%; font-size: 30px; z-index: 1000; }
    </style>
</head>
<body>

<nav class="navbar px-3 shadow-sm">
    <span class="h4 mb-0">Task Manager</span>
    <div class="small">Welcome, <strong><?= session()->get('username') ?></strong> | <a href="/logout" class="text-white">Logout</a></div>
</nav>

<div class="container mt-4">
    <div id="taskList">
        </div>
</div>

<button class="btn btn-primary fab shadow" data-bs-toggle="modal" data-bs-target="#taskModal">+</button>

<div class="modal fade" id="taskModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 id="modalTitle">New Task</h5></div>
            <div class="modal-body">
                <form id="taskForm">
                    <div id="taskError" class="text-danger small mb-2"></div>
                    <input type="text" id="taskTitle" class="form-control mb-3" placeholder="What needs to be done?">
                    <select id="taskPriority" class="form-select mb-3">
                        <option value="Low">Priority: Low</option>
                        <option value="Medium">Priority: Medium</option>
                        <option value="High">Priority: High</option>
                    </select>
                    <input type="date" id="taskDate" class="form-control">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="taskForm" class="btn btn-primary">Save Task</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // 1. VIEW: Load all tasks on startup
    document.addEventListener('DOMContentLoaded', () => {
        loadTasks();
        document.getElementById('taskForm').addEventListener('submit', saveTask);
    });

    async function loadTasks() {
        const container = document.getElementById('taskList');

        try {
            const res = await fetch('/api/tasks');
            if (!res.ok) {
                const error = await res.json().catch(() => ({ message: 'Unable to load tasks.' }));
                container.innerHTML = `<div class="text-center text-danger mt-5"><p>${error.error || error.message || 'Unable to load tasks.'}</p></div>`;
                return;
            }

            const tasks = await res.json();
            if (!Array.isArray(tasks) || tasks.length === 0) {
                container.innerHTML = `<div class="text-center text-muted mt-5"><p>Click + to add your first task!</p></div>`;
                return;
            }

            container.innerHTML = tasks.map(function(t) {
                var completedClass = t.status === 'completed' ? 'completed' : '';
                var checked = t.status === 'completed' ? 'checked' : '';
                var priority = t.priority || 'Low';
                var dueDate = t.due_date || '—';

                return '' +
                    '<div class="card shadow-sm p-3 task-card ' + completedClass + '">' +
                        '<div class="d-flex justify-content-between align-items-center">' +
                            '<div class="d-flex align-items-center">' +
                                '<input type="checkbox" class="form-check-input me-3" ' + checked +
                                       ' onclick="toggleStatus(' + t.id + ', \'' + t.status + '\')">' +
                                '<div>' +
                                    '<span class="h5 task-title">' + t.title + '</span>' +
                                    '<div class="text-muted small">Priority: ' + priority + ' | Due: ' + dueDate + '</div>' +
                                '</div>' +
                            '</div>' +
                            '<button class="btn btn-sm btn-outline-danger" onclick="deleteTask(' + t.id + ')">Delete</button>' +
                        '</div>' +
                    '</div>';
            }).join('');
        } catch (error) {
            container.innerHTML = `<div class="text-center text-danger mt-5"><p>Unable to load tasks: ${error.message}</p></div>`;
        }
    }

    // 2. CREATE: Save new task
    async function saveTask(event) {
        event.preventDefault();
        const errorBox = document.getElementById('taskError');
        errorBox.textContent = '';

        const data = {
            title: document.getElementById('taskTitle').value.trim(),
            priority: document.getElementById('taskPriority').value,
            due_date: document.getElementById('taskDate').value
        };

        if (!data.title || !data.due_date) {
            errorBox.textContent = 'Please enter both a title and due date.';
            return;
        }

        console.log('Sending data:', data);

        const res = await fetch('/api/tasks', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        });

        if (res.ok) {
            const modalElem = document.getElementById('taskModal');
            const modal = bootstrap.Modal.getInstance(modalElem) || new bootstrap.Modal(modalElem);
            modal.hide();
            document.getElementById('taskForm').reset();
            loadTasks();
        } else {
            const error = await res.json().catch(() => ({ message: 'Unknown server error' }));
            errorBox.textContent = error.message || JSON.stringify(error);
        }
    }

    // 3. UPDATE: Toggle Pending/Completed
    async function toggleStatus(id, currentStatus) {
        const newStatus = currentStatus === 'pending' ? 'completed' : 'pending';
        await fetch('/api/tasks/' + id, {
            method: 'PATCH',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ status: newStatus })
        });
        loadTasks();
    }

    // 4. DELETE: Remove task
    async function deleteTask(id) {
        if (confirm('Are you sure?')) {
            await fetch('/api/tasks/' + id, { method: 'DELETE' });
            loadTasks();
        }
    }
</script>
</body>
</html>