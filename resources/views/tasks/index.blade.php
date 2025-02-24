<!DOCTYPE html>
<html lang="en">
<head>
    <title>Tasks</title>
</head>
<body>
<h1>Task List</h1>
 <a href="{{ route('tasks.create') }}">
        <button>Create New Task</button>
    </a>

<ul>
    @foreach ($tasks as $task)
        <li>
            {{ $task->title }} - Assigned to: {{ $task->user->name ?? 'Unknown' }}
            <a href="/tasks/{{ $task->id }}/edit">Edit</a> |

            <form action="{{ route('tasks.destroy', $task) }}" style="display:inline-block;" method="POST" onSubmit="confirm('Are you sure?')">
                @csrf
                @method('DELETE')
                <button>Delete</button>
            </form>
        </li>
    @endforeach
    @if($tasks->isEmpty())
        <h1>Yikes, you dont have any tasks!</h1>
    @endif
</ul>
<form action="{{ route('logout') }}" method="POST" style="display: inline;">
    @csrf
    <button type="submit">Logout</button>
</form>

</body>
</html>
