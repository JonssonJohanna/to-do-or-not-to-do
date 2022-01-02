<?php

declare(strict_types=1);

//this file contains functions

function redirect(string $path)
{
    header("Location: ${path}");
    exit;
}

function is_logged_in()
{
    if (isset($_SESSION['user'])) {
        $user = $_SESSION['user'];
        return $user;
    }
}

function welcome_message()
{
    if (isset($_SESSION['user'])) {
        $name = $_SESSION['user']['name'];
        return 'Welcome, ' . $name . '!';
    }
}

function show_error()
{
    if (isset($_SESSION['errors'])) {
        foreach ($_SESSION['errors'] as $error) {
            unset($_SESSION['errors']);
            return $error;
        }
        unset($_SESSION['errors']);
    }
}

function show_message()
{
    if (isset($_SESSION['messages'])) {
        foreach ($_SESSION['messages'] as $message) {
            unset($_SESSION['messages']);
            return $message;
        }
        unset($_SESSION['messages']);
    }
}

function get_image_url()
{
    $database = new PDO(sprintf('sqlite:%s/database/database.db', __DIR__));

    $user_id = $_SESSION['user']['id'];

    $statement = $database->prepare("SELECT image_url FROM users WHERE id = :id");
    $statement->bindParam(':id', $user_id, PDO::PARAM_INT);

    $statement->execute();
    $image = $statement->fetch(PDO::FETCH_ASSOC);
    $image_url = $image['image_url'];

    if ($image_url === null) {
        return '/images/rubber_duck.png';
    }

    return '/uploads/' . $image_url;
}

function get_user_info(object $database)
{
    $user_id = $_SESSION['user']['id'];

    $statement = $database->prepare("SELECT * FROM users WHERE id = :id");
    $statement->bindParam(':id', $user_id, PDO::PARAM_INT);
    $statement->execute();

    $user = $statement->fetch(PDO::FETCH_ASSOC);
    $user_info = [
        'username' => $user['username'],
        'email' => $user['email'],
        'id' => $user['id'],
    ];
    return $user_info;
}

function get_lists(object $database)
{
    $user_id = $_SESSION['user']['id'];

    $statement = $database->prepare("SELECT * FROM lists WHERE user_id = :id");
    $statement->bindParam(':id', $user_id, PDO::PARAM_INT);
    $statement->execute();

    $lists = $statement->fetchAll(PDO::FETCH_ASSOC);

    return $lists;
}

function get_tasks_from_list(object $database, $list_id)
{
    $user_id = $_SESSION['user']['id'];

    $statement = $database->prepare("SELECT * FROM tasks WHERE user_id = :user_id AND list_id = :list_id");
    $statement->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $statement->bindParam(':list_id', $list_id, PDO::PARAM_INT);
    $statement->execute();

    $tasks = $statement->fetchAll(PDO::FETCH_ASSOC);

    return $tasks;
}

function get_tasks(object $database)
{
    $user_id = $_SESSION['user']['id'];

    $statement = $database->prepare(
        "SELECT tasks.id, tasks.list_id, tasks.user_id, tasks.name,
        tasks.description, tasks.deadline_at, tasks.completed_at, lists.title
    FROM tasks
    LEFT OUTER JOIN lists
    ON tasks.list_id = lists.id
    WHERE tasks.user_id = :user_id ORDER BY tasks.deadline_at"
    );
    $statement->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $statement->execute();

    $tasks = $statement->fetchAll(PDO::FETCH_ASSOC);

    return $tasks;
}

function get_single_task(PDO $database, $list_id, $task_id)
{
    $user_id = $_SESSION['user']['id'];

    $statement = $database->prepare(
        "SELECT tasks.id, tasks.list_id, tasks.user_id, tasks.name,
        tasks.description, tasks.deadline_at, tasks.completed_at, lists.title
    FROM tasks
    LEFT OUTER JOIN lists
    ON tasks.list_id = lists.id
    WHERE tasks.user_id = :user_id AND tasks.id = :task_id AND tasks.list_id = :list_id"
    );
    $statement->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $statement->bindParam(':task_id', $task_id, PDO::PARAM_INT);
    $statement->bindParam(':list_id', $list_id, PDO::PARAM_INT);
    $statement->execute();

    $task = $statement->fetchAll(PDO::FETCH_ASSOC);

    return $task;
}

function task_status($task)
{
    if (isset($task['completed_at'])) {
        $status['completed'] = 'checked';
        $status['uncompleted'] = '';
    } else {
        $status['completed'] = '';
        $status['uncompleted'] = 'checked';
    }

    return $status;
}

function count_tasks($tasks)
{
    $task_count = array(
        'completed' => [],
        'uncompleted' => [],
    );

    foreach ($tasks as $task) {
        if (isset($task['completed_at'])) {
            $task_count['completed'][] = $task['name'];
        } else {
            $task_count['uncompleted'][] = $task['name'];
        }
    }
    return $task_count;
}
