<?php

namespace App\Controller;

use App\Model\Auth;
use App\Model\Tasks;
use App\Validation\Validator;
use App\View\Template;

class TaskController extends BaseController
{
    private Tasks $model;

    public function __construct()
    {
        $this->model = new Tasks();
    }

    public function control()
    {
        $auth = new Auth();
        if (!$auth->isLoggedIn()) {
            $this->redirect('main', 'login');
        }

        $action = $_GET['action'] ?? 'index';

        if ($action === 'index') {
            $this->list();
        }

        if ($action === 'new') {
            if ($this->isGet()) {
                $this->showNew();
            }

            if ($this->isPost()) {
                $this->create();
            }
        }

        if ($action === 'edit') {
            if ($this->isGet()) {
                $this->showEdit();
            }

            if ($this->isPost()) {
                $this->update();
            }
        }

        if ($action == 'delete') {
            $this->delete();
        }
    }

    public function showNew()
    {
        $template = new Template('admin/base');
        $template->view('admin/task/new');
    }

    public function list()
    {
        $tasks = $this->model->myTasks();

        $template = new Template('admin/base');

        $template->view('admin/task/index', [
            'tasks' => $tasks
        ]);
    }

    public function create()
    {
        $title = $_POST['title'];
        $note = $_POST['note'];
        $time = $_POST['time'];

        $validator = new Validator();

        if (strlen($time) > 0) {
            $validator->isValidDateTime($time);
        }

        $validator->minLength($title, 3, 'Title should have at least 3 characters');

        if (!$validator->isValid()) {
            $this->redirect('task', 'new', ['errors' => $validator->getErrors()]);
        }

        $created = $this->model->insert($title, $note, $time);

        if ($created) {
            $this->redirect('task', 'index', ['success' => 'Task was added successfully.']);
        }
    }

    public function showEdit()
    {
        $task_id = $_GET['id'] ?? null;

        $task = $this->model->getById($task_id);


        if (!$task) {
            $this->redirect('task', 'task', ['error' => 'Task was not found']);
        }

        $template = new Template('admin/base');
        $template->view('admin/task/edit', ['task' => $task]);
    }

    public function update()
    {
        $title = $_POST['title'];
        $note = $_POST['note'];
        $time = $_POST['time'];
        $task_id = $_POST['id'] ?? null;

        $validator = new Validator();

        if (strlen($time) > 0) {
            $validator->isValidDateTime($time);
        }

        $validator->minLength($title, 3, 'Title should have at least 3 characters');

        if (!$validator->isValid()) {
            $this->redirect('task', 'new', ['errors' => $validator->getErrors()]);
        }

        if (!$task_id) {
            $this->redirect('task', 'task', ['error' => 'Could not find the task']);
        }

        $result = $this->model->update($task_id, $title, $note, $time);

        if ($result) {
            $this->redirect('task', 'index');
        }
    }

    public function delete()
    {
        $confirm = boolval($_POST['confirm'] ?? 0);
        $task_id = $_POST['id'] ?? null;

        $task = $this->model->getById($task_id);

        if (!$task_id) {
            $this->redirect('task', 'task', ['error' => 'Could not find the task']);
        }

        if (!$confirm) {
            $template = new Template('admin/base');
            $template->view('admin/task/confirm_delete', ['task' => $task]);
        } else {
            $deleted = $this->model->delete($task_id);

            if ($deleted) {
                $this->redirect('task', 'index', ['success' => 'Task was deleted.']);
            } else {
                $this->redirect('task', 'view', ['id' => $task_id, 'error' => 'Could not delete the task']);
            }
        }
    }
}