<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">{{ $task->title }}</h1>
                <p class="text-sm text-slate-500 mt-1">Task details and information</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('tasks.index') }}" class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">← Back</a>
                <a href="{{ route('tasks.edit', $task) }}" class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">Edit</a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-3xl space-y-6">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Status</p>
                    <x-status-badge :status="$task->status"/>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Priority</p>
                    @if($task->priority === 'high')
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">High</span>
                    @elseif($task->priority === 'medium')
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">Medium</span>
                    @else
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">Low</span>
                    @endif
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Assigned To</p>
                    <span class="text-sm font-medium text-slate-900">{{ $task->assignee->name ?? 'Unassigned' }}</span>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Due Date</p>
                    @php
                        $isOverdue = false;
                        $formattedDate = 'No due date';
                        if (!empty($task->due_date)) {
                            try {
                                $parsedDate = \Carbon\Carbon::parse($task->due_date);
                                $formattedDate = $parsedDate->format('M d, Y');
                                $isOverdue = $parsedDate->isPast() && $task->status != 'completed';
                            } catch (\Exception $e) {
                                $formattedDate = $task->due_date;
                            }
                        }
                    @endphp
                    <span class="text-sm font-medium {{ $isOverdue ? 'text-red-600' : 'text-slate-900' }}">
                        {{ $formattedDate }}
                        @if($isOverdue) <span class="text-xs bg-red-100 text-red-700 px-1.5 py-0.5 rounded ml-1">Overdue</span> @endif
                    </span>
                </div>
            </div>

            <div class="border-t border-slate-100 pt-6">
                <h3 class="text-sm font-semibold text-slate-700 mb-3">Description</h3>
                <div class="bg-slate-50 rounded-lg p-4 text-sm text-slate-700 leading-relaxed">
                    {{ $task->description ?: 'No description provided.' }}
                </div>
            </div>

            <!-- Task Attachment Section -->
            <div class="border-t border-slate-100 pt-6 mt-6">
                <h3 class="text-sm font-semibold text-slate-700 mb-3">Attachment</h3>
                @if($task->attachment)
                    <div class="flex items-center justify-between p-4 bg-indigo-50/50 border border-indigo-100 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="p-2.5 bg-indigo-100 text-indigo-700 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-slate-900">Attached Document</p>
                                <p class="text-xs text-slate-500">Click to open or download the attached file.</p>
                            </div>
                        </div>
                        <a href="{{ asset('storage/' . $task->attachment) }}" target="_blank" 
                           class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg transition-colors">
                            View File
                        </a>
                    </div>
                @else
                    <p class="text-sm text-slate-400 italic bg-slate-50 p-3 rounded-lg border border-slate-100">No file attached to this task.</p>
                @endif
            </div>

            <div class="flex justify-end mt-6 pt-6 border-t border-slate-100">
                <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Delete this task permanently?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors">Delete Task</button>
                </form>
            </div>
        </div>

        <!-- Task Comments Section -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <h3 class="text-lg font-bold text-slate-900 mb-4">Comments</h3>

            <!-- Comment Form -->
            <form action="{{ route('tasks.comments.store', $task->id) }}" method="POST" class="mb-6">
                @csrf
                <div>
                    <textarea name="comment" rows="3" 
                        class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm" 
                        placeholder="Write a comment..." required></textarea>
                    @error('comment')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mt-2 flex justify-end">
                    <button type="submit" 
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg text-sm shadow-sm transition-colors">
                        Post Comment
                    </button>
                </div>
            </form>

            <!-- Comments List -->
            <div class="space-y-4">
                @forelse($task->comments as $comment)
                    <div class="p-4 bg-slate-50 rounded-lg border border-slate-100">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-semibold text-sm text-slate-800">{{ $comment->user->name }}</span>
                            <span class="text-xs text-slate-500">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-slate-700 text-sm whitespace-pre-line">{{ $comment->comment }}</p>
                    </div>
                @empty
                    <p class="text-slate-500 text-sm text-center py-4">No comments yet. Be the first to comment!</p>
                @endforelse
            </div>


            <!-- 🔥 Activity History ကို ဒီအောက်ဆုံးနားမှာ ထည့်ပေးပါမယ် 🔥 -->
            <div class="mt-8">
                    <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Activity History</h3>
                    <ul class="mt-4 divide-y divide-gray-200">
                        @forelse($task->activities as $log)
                            <li class="py-3">
                                <p class="text-sm text-gray-800">{{ $log->description }}</p>
                                <span class="text-xs text-gray-400">{{ $log->created_at->diffForHumans() }}</span>
                            </li>
                        @empty
                            <li class="py-3 text-sm text-gray-500">No activity yet.</li>
                        @endforelse
                    </ul>
                </div>
        </div>
    </div>
</x-app-layout>