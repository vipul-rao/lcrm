<?php

namespace App\Http\Controllers\Users;

use App\Http\Requests\TaskRequest;
use App\Repositories\OrganizationRepository;
use App\Repositories\TaskRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TaskController extends Controller
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    private $organizationRepository;
    private $taskRepository;

    /**
     * TaskController constructor.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(
        UserRepository $userRepository,
        OrganizationRepository $organizationRepository,
        TaskRepository $taskRepository
    ) {
        parent::__construct();

        $this->userRepository = $userRepository;
        $this->organizationRepository = $organizationRepository;
        $this->taskRepository = $taskRepository;

        view()->share('type', 'task');
    }

    public function index()
    {
        $title = trans('task.tasks');
        $users = $this->organizationRepository->getStaffWithUser()->get()
            ->map(function ($user) {
                return [
                    'name' => $user->full_name.' ( '.$user->email.' )',
                    'id' => $user->id,
                ];
            })
            ->pluck('name', 'id')->prepend(trans('task.user'), '');

        return view('user.task.index', compact('title', 'users'));
    }

    public function store(TaskRequest $request)
    {
        $task = $this->taskRepository->create($request->except('_token', 'full_name'));

        return $task->id;
    }

    public function update($task, Request $request)
    {
        $task = $this->taskRepository->find($task);
        $task->update($request->except('_method', '_token'));
    }

    public function delete($task)
    {
        $task = $this->taskRepository->find($task);
        $task->delete();
    }

    public function data()
    {
        $dateFormat = config('settings.date_format');
        $tasks = $this->taskRepository->orderBy('finished', 'ASC')
            ->orderBy('task_deadline', 'DESC')->with('task_from_users')->all()->where('user_id', $this->getUser()->id)
            ->map(function ($task) use ($dateFormat) {
                return [
                    'task_from' => $task->task_from_users->full_name,
                    'id' => $task->id,
                    'finished' => $task->finished,
                    'task_deadline' => date($dateFormat,strtotime($task->task_deadline)),
                    'task_description' => $task->task_description,
                    'user_id' => $task->user_id,
                    'task_from_user' => $task->task_from_user
                ];
            });

        return $tasks;
    }
}
