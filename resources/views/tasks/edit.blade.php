<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Edit Task</h1>
            <p class="text-sm text-slate-500 mt-1">Update task details and attachments</p>
        </div>
    </x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <!-- enctype="multipart/form-data" ထည့်သွင်းထားသည် -->
            <form action="{{ route('tasks.update', $task->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <x-input-label for="title" :value="__('Title')" class="text-slate-700 font-medium"/>
                    <x-text-input id="title" class="block mt-1.5 w-full rounded-lg" type="text" name="title" :value="old('title', $task->title)" required autofocus />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="description" :value="__('Description')" class="text-slate-700 font-medium"/>
                    <textarea id="description" name="description" rows="3"
                              class="block mt-1.5 w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $task->description) }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <x-input-label for="assigned_to" :value="__('Assign To')" class="text-slate-700 font-medium"/>
                        <select id="assigned_to" name="assigned_to" class="block mt-1.5 w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Unassigned</option>
                            @foreach(\App\Models\User::where('tenant_id', Auth::user()->tenant_id ?? 1)->get() as $member)
                                <option value="{{ $member->id }}" {{ old('assigned_to', $task->assigned_to) == $member->id ? 'selected' : '' }}>{{ $member->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('assigned_to')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="priority" :value="__('Priority')" class="text-slate-700 font-medium"/>
                        <select id="priority" name="priority" class="block mt-1.5 w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="" disabled>Select Priority</option>
                            <option value="low" {{ old('priority', $task->priority) == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ old('priority', $task->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ old('priority', $task->priority) == 'high' ? 'selected' : '' }}>High</option>
                        </select>
                        <x-input-error :messages="$errors->get('priority')" class="mt-2" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <x-input-label for="status" :value="__('Status')" class="text-slate-700 font-medium"/>
                        <select id="status" name="status" required class="block mt-1.5 w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="todo" {{ old('status', $task->status) == 'todo' ? 'selected' : '' }}>To Do</option>
                            <option value="in_progress" {{ old('status', $task->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ old('status', $task->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="due_date" :value="__('Due Date')" class="text-slate-700 font-medium"/>
                        <x-text-input id="due_date" class="block mt-1.5 w-full rounded-lg" type="date" name="due_date" :value="old('due_date', $task->due_date)" />
                        <x-input-error :messages="$errors->get('due_date')" class="mt-2" />
                    </div>
                </div>

                <!-- Existing & New File Attachment -->
                <div>
                    <x-input-label for="attachment" :value="__('Attachment (PDF, Excel, Images)')" class="text-slate-700 font-medium"/>
                    
                    @if($task->attachment)
                        <div class="mb-2 flex items-center justify-between p-3 bg-slate-50 border border-slate-200 rounded-lg text-sm">
                            <span class="text-slate-600 truncate">Current File: <a href="{{ asset('storage/' . $task->attachment) }}" target="_blank" class="text-indigo-600 underline hover:text-indigo-800">View Attached File</a></span>
                        </div>
                    @endif

                    <input type="file" id="attachment" name="attachment" class="mt-1.5 block w-full text-sm text-slate-500
                        file:mr-4 file:py-2.5 file:px-4
                        file:rounded-lg file:border-0
                        file:text-sm file:font-semibold
                        file:bg-indigo-50 file:text-indigo-700
                        hover:file:bg-indigo-100 border border-slate-300 rounded-lg cursor-pointer bg-slate-50
                    "/>
                    <x-input-error :messages="$errors->get('attachment')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('tasks.index') }}" class="px-4 py-2.5 text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors">Cancel</a>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition-colors">Update Task</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>