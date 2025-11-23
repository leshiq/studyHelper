<!-- Quizzes Section -->
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-question-circle"></i> Lesson Quizzes</h5>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createQuizModal">
            <i class="bi bi-plus-circle"></i> Create New Quiz
        </button>
    </div>
    <div class="card-body">
        @if($lesson->quizzes->isEmpty())
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> 
                This lesson doesn't have any quizzes yet. Create one to test students' understanding.
            </div>
        @else
            <div class="accordion" id="quizzesAccordion">
                @foreach($lesson->quizzes as $quizIndex => $quiz)
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button {{ $quizIndex === 0 ? '' : 'collapsed' }}" type="button" 
                                    data-bs-toggle="collapse" data-bs-target="#quiz{{ $quiz->id }}">
                                <div class="d-flex align-items-center gap-2 flex-grow-1">
                                    <span class="badge {{ $quiz->is_active ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $quiz->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                    @if($quiz->is_repeatable)
                                        <span class="badge bg-info">Repeatable</span>
                                    @endif
                                    <strong>{{ $quiz->title }}</strong>
                                    <span class="text-muted small">({{ $quiz->questions->count() }} questions, {{ $quiz->total_points }} pts)</span>
                                </div>
                            </button>
                        </h2>
                        <div id="quiz{{ $quiz->id }}" class="accordion-collapse collapse {{ $quizIndex === 0 ? 'show' : '' }}" 
                             data-bs-parent="#quizzesAccordion">
                            <div class="accordion-body">
                                <!-- Quiz Details -->
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        @if($quiz->description)
                                            <p class="text-muted mb-2">{{ $quiz->description }}</p>
                                        @endif
                                        <div class="small text-muted">
                                            <i class="bi bi-check-circle"></i> Passing Score: {{ $quiz->passing_score }}%
                                            @if($quiz->time_limit)
                                                · <i class="bi bi-clock"></i> Time Limit: {{ $quiz->time_limit }} minutes
                                            @endif
                                            <br>
                                            <i class="bi bi-eye"></i> Show answers: {{ $quiz->show_correct_answers ? 'Yes' : 'No' }}
                                            · <i class="bi bi-arrow-repeat"></i> Allow retakes: {{ $quiz->allow_retakes ? 'Yes' : 'No' }}
                                        </div>
                                    </div>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-primary me-2" 
                                                data-bs-toggle="modal" data-bs-target="#editQuizModal{{ $quiz->id }}">
                                            <i class="bi bi-pencil"></i> Edit
                                        </button>
                                        <form action="{{ route('teacher.courses.lessons.quizzes.destroy', [$course, $lesson, $quiz]) }}" 
                                              method="POST" class="d-inline" 
                                              onsubmit="return confirm('Delete this quiz? This will remove all questions and student attempts.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <!-- Questions List -->
                                @if($quiz->questions->isEmpty())
                                    <div class="alert alert-warning">
                                        <i class="bi bi-exclamation-triangle"></i> 
                                        No questions added yet.
                                    </div>
                                @else
                                    <div class="list-group mb-3">
                                        @foreach($quiz->questions as $qIndex => $question)
                                            <div class="list-group-item">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex align-items-center gap-2 mb-2">
                                                            <span class="badge bg-secondary">Q{{ $qIndex + 1 }}</span>
                                                            <span class="badge bg-info">
                                                                @if($question->type === 'single_choice')
                                                                    Single Choice
                                                                @elseif($question->type === 'multiple_choice')
                                                                    Multiple Choice
                                                                @else
                                                                    Text Input
                                                                @endif
                                                            </span>
                                                            <span class="badge bg-success">{{ $question->points }} pts</span>
                                                        </div>
                                                        <p class="mb-2"><strong>{{ $question->question }}</strong></p>
                                                        
                                        @if($question->type !== 'text_input')
                                            <div class="mb-2">
                                                <strong class="small d-block mb-1">Options:</strong>
                                                <ul class="list-unstyled small mb-0">
                                                    @foreach($question->options as $option)
                                                        <li class="py-1 px-2 rounded mb-1 {{ $option->is_correct ? 'bg-success bg-opacity-10 text-success fw-bold' : 'text-muted' }}">
                                                            <i class="bi bi-{{ $option->is_correct ? 'check-circle-fill' : 'circle' }}"></i>
                                                            {{ $option->option_text }}
                                                            @if($option->is_correct)
                                                                <span class="badge bg-success ms-2">Correct</span>
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @else
                                            <p class="small mb-0">
                                                <strong>Correct answer(s):</strong>
                                                @foreach($question->correctOptions as $option)
                                                    <span class="badge bg-success ms-1">{{ $option->option_text }}</span>
                                                @endforeach
                                            </p>
                                        @endif                                                        @if($question->explanation)
                                                            <p class="small text-info mt-2 mb-0">
                                                                <i class="bi bi-info-circle"></i> {{ $question->explanation }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                    <div class="btn-group me-2">
                                                        <button type="button" class="btn btn-sm btn-outline-primary me-2" 
                                                                data-bs-toggle="modal" data-bs-target="#editQuestionModal{{ $quiz->id }}_{{ $question->id }}">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <form action="{{ route('teacher.courses.lessons.quizzes.questions.destroy', [$course, $lesson, $quiz, $question]) }}" 
                                                              method="POST" class="d-inline" onsubmit="return confirm('Delete this question?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                                        @endforeach
                                    </div>
                                @endif

                                <!-- Add Question Button -->
                                <button type="button" class="btn btn-outline-primary btn-sm" 
                                        data-bs-toggle="modal" data-bs-target="#addQuestionModal{{ $quiz->id }}">
                                    <i class="bi bi-plus-circle"></i> Add Question
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<!-- Create Quiz Modal -->
<div class="modal fade" id="createQuizModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('teacher.courses.lessons.quizzes.store', [$course, $lesson]) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Create New Quiz</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Quiz Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title" required placeholder="e.g., Chapter 1 Quiz">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="2" placeholder="Optional description"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Passing Score (%) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="passing_score" value="70" min="0" max="100" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Time Limit (minutes)</label>
                            <input type="number" class="form-control" name="time_limit" min="1" placeholder="No limit">
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="show_correct_answers" value="1" id="show_correct_answers" checked>
                            <label class="form-check-label" for="show_correct_answers">
                                Show correct answers after submission
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="allow_retakes" value="1" id="allow_retakes" checked>
                            <label class="form-check-label" for="allow_retakes">
                                Allow students to retake the quiz
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_repeatable" value="1" id="is_repeatable">
                            <label class="form-check-label" for="is_repeatable">
                                <strong>Repeatable:</strong> Students can take this quiz at any time (good for practice quizzes)
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" checked>
                            <label class="form-check-label" for="is_active">
                                Quiz is active (visible to students)
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Create Quiz
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Quiz Modals (one for each quiz) -->
@foreach($lesson->quizzes as $quiz)
<div class="modal fade" id="editQuizModal{{ $quiz->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('teacher.courses.lessons.quizzes.update', [$course, $lesson, $quiz]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Quiz</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Quiz Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title" value="{{ $quiz->title }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="2">{{ $quiz->description }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Passing Score (%) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="passing_score" value="{{ $quiz->passing_score }}" min="0" max="100" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Time Limit (minutes)</label>
                            <input type="number" class="form-control" name="time_limit" value="{{ $quiz->time_limit }}" min="1" placeholder="No limit">
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="show_correct_answers" value="1" 
                                   id="edit_show_correct_answers_{{ $quiz->id }}" {{ $quiz->show_correct_answers ? 'checked' : '' }}>
                            <label class="form-check-label" for="edit_show_correct_answers_{{ $quiz->id }}">
                                Show correct answers after submission
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="allow_retakes" value="1" 
                                   id="edit_allow_retakes_{{ $quiz->id }}" {{ $quiz->allow_retakes ? 'checked' : '' }}>
                            <label class="form-check-label" for="edit_allow_retakes_{{ $quiz->id }}">
                                Allow students to retake the quiz
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_repeatable" value="1" 
                                   id="edit_is_repeatable_{{ $quiz->id }}" {{ $quiz->is_repeatable ? 'checked' : '' }}>
                            <label class="form-check-label" for="edit_is_repeatable_{{ $quiz->id }}">
                                <strong>Repeatable:</strong> Students can take this quiz at any time
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                   id="edit_is_active_{{ $quiz->id }}" {{ $quiz->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="edit_is_active_{{ $quiz->id }}">
                                Quiz is active (visible to students)
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Question Modal for this quiz -->
<div class="modal fade" id="addQuestionModal{{ $quiz->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('teacher.courses.lessons.quizzes.questions.store', [$course, $lesson, $quiz]) }}" method="POST" id="addQuestionForm{{ $quiz->id }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Question to "{{ $quiz->title }}"</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Question Type <span class="text-danger">*</span></label>
                        <select class="form-select question-type-select" name="type" data-quiz-id="{{ $quiz->id }}" required>
                            <option value="single_choice">Single Choice</option>
                            <option value="multiple_choice">Multiple Choice</option>
                            <option value="text_input">Text Input</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Question <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="question" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Points</label>
                        <input type="number" class="form-control" name="points" value="1" min="1" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Explanation (optional)</label>
                        <textarea class="form-control" name="explanation" rows="2"></textarea>
                    </div>

                    <div id="optionsSection{{ $quiz->id }}">
                        <label class="form-label">Answer Options <span class="text-danger">*</span></label>
                        <div id="optionsList{{ $quiz->id }}"></div>
                        <button type="button" class="btn btn-sm btn-outline-secondary mt-2 add-option-btn" data-quiz-id="{{ $quiz->id }}">
                            <i class="bi bi-plus-circle"></i> Add Option
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Add Question
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Question Modals (one for each question in each quiz) -->
@foreach($quiz->questions as $question)
<div class="modal fade" id="editQuestionModal{{ $quiz->id }}_{{ $question->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('teacher.courses.lessons.quizzes.questions.update', [$course, $lesson, $quiz, $question]) }}" method="POST" id="editQuestionForm{{ $quiz->id }}_{{ $question->id }}">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Question</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Question Type <span class="text-danger">*</span></label>
                        <select class="form-select edit-question-type-select" name="type" data-quiz-id="{{ $quiz->id }}" data-question-id="{{ $question->id }}" required>
                            <option value="single_choice" {{ $question->type === 'single_choice' ? 'selected' : '' }}>Single Choice</option>
                            <option value="multiple_choice" {{ $question->type === 'multiple_choice' ? 'selected' : '' }}>Multiple Choice</option>
                            <option value="text_input" {{ $question->type === 'text_input' ? 'selected' : '' }}>Text Input</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Question <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="question" rows="3" required>{{ $question->question }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Points</label>
                        <input type="number" class="form-control" name="points" value="{{ $question->points }}" min="1" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Explanation (optional)</label>
                        <textarea class="form-control" name="explanation" rows="2">{{ $question->explanation }}</textarea>
                    </div>

                    <div id="editOptionsSection{{ $quiz->id }}_{{ $question->id }}">
                        <label class="form-label">Answer Options <span class="text-danger">*</span></label>
                        <div id="editOptionsList{{ $quiz->id }}_{{ $question->id }}">
                            @foreach($question->options as $optIndex => $option)
                                <div class="input-group mb-2" data-index="{{ $optIndex }}">
                                    @if($question->type === 'single_choice')
                                        <div class="input-group-text">
                                            <input class="form-check-input mt-0" type="radio" name="options[{{ $optIndex }}][is_correct]" value="1" {{ $option->is_correct ? 'checked' : '' }}>
                                        </div>
                                        <input type="text" class="form-control" name="options[{{ $optIndex }}][text]" value="{{ $option->option_text }}" placeholder="Option text" required>
                                    @elseif($question->type === 'multiple_choice')
                                        <div class="input-group-text">
                                            <input class="form-check-input mt-0" type="checkbox" name="correct_answers[]" value="{{ $optIndex }}" {{ $option->is_correct ? 'checked' : '' }}>
                                        </div>
                                        <input type="text" class="form-control" name="options[{{ $optIndex }}][text]" value="{{ $option->option_text }}" placeholder="Option text" required>
                                    @else
                                        <input type="text" class="form-control" name="options[{{ $optIndex }}][text]" value="{{ $option->option_text }}" placeholder="Correct answer" required>
                                    @endif
                                    <button type="button" class="btn btn-outline-danger remove-option" onclick="this.closest('.input-group').remove()">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary mt-2 edit-add-option-btn" data-quiz-id="{{ $quiz->id }}" data-question-id="{{ $question->id }}">
                            <i class="bi bi-plus-circle"></i> Add Option
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Update Question
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endforeach

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle question type changes for ADD question forms
    const questionTypeSelects = document.querySelectorAll('.question-type-select');
    
    questionTypeSelects.forEach(select => {
        const quizId = select.dataset.quizId;
        const optionsList = document.getElementById('optionsList' + quizId);
        const optionsSection = document.getElementById('optionsSection' + quizId);
        const addOptionBtn = document.querySelector(`.add-option-btn[data-quiz-id="${quizId}"]`);
        
        let optionCount = 0;

        function updateOptionsUI() {
            const type = select.value;
            optionsList.innerHTML = '';
            optionCount = 0;

            if (type === 'text_input') {
                optionsSection.querySelector('label').textContent = 'Correct Answer(s)';
                addOptionBtn.innerHTML = '<i class="bi bi-plus-circle"></i> Add Alternative Answer';
                addOption();
            } else {
                optionsSection.querySelector('label').textContent = 'Answer Options';
                addOptionBtn.innerHTML = '<i class="bi bi-plus-circle"></i> Add Option';
                addOption();
                addOption();
            }
        }

        function addOption() {
            const type = select.value;
            const index = optionCount++;
            const optionDiv = document.createElement('div');
            optionDiv.className = 'input-group mb-2';
            optionDiv.dataset.index = index;

            if (type === 'single_choice') {
                optionDiv.innerHTML = `
                    <div class="input-group-text">
                        <input class="form-check-input mt-0" type="radio" name="options[${index}][is_correct]" value="1">
                    </div>
                    <input type="text" class="form-control" name="options[${index}][text]" placeholder="Option text" required>
                    <button type="button" class="btn btn-outline-danger remove-option" onclick="this.closest('.input-group').remove()">
                        <i class="bi bi-trash"></i>
                    </button>
                `;
            } else if (type === 'multiple_choice') {
                optionDiv.innerHTML = `
                    <div class="input-group-text">
                        <input class="form-check-input mt-0" type="checkbox" name="correct_answers[]" value="${index}">
                    </div>
                    <input type="text" class="form-control" name="options[${index}][text]" placeholder="Option text" required>
                    <button type="button" class="btn btn-outline-danger remove-option" onclick="this.closest('.input-group').remove()">
                        <i class="bi bi-trash"></i>
                    </button>
                `;
            } else {
                optionDiv.innerHTML = `
                    <input type="text" class="form-control" name="options[${index}][text]" placeholder="Correct answer" required>
                    <button type="button" class="btn btn-outline-danger remove-option" onclick="this.closest('.input-group').remove()">
                        <i class="bi bi-trash"></i>
                    </button>
                `;
            }

            optionsList.appendChild(optionDiv);
        }

        select.addEventListener('change', updateOptionsUI);
        addOptionBtn.addEventListener('click', addOption);

        // Initialize
        updateOptionsUI();
    });

    // Handle question type changes for EDIT question forms
    const editQuestionTypeSelects = document.querySelectorAll('.edit-question-type-select');
    
    editQuestionTypeSelects.forEach(select => {
        const quizId = select.dataset.quizId;
        const questionId = select.dataset.questionId;
        const modalId = `${quizId}_${questionId}`;
        const optionsList = document.getElementById('editOptionsList' + modalId);
        const optionsSection = document.getElementById('editOptionsSection' + modalId);
        const addOptionBtn = document.querySelector(`.edit-add-option-btn[data-quiz-id="${quizId}"][data-question-id="${questionId}"]`);
        
        function getOptionCount() {
            return optionsList.querySelectorAll('.input-group').length;
        }

        function updateEditOptionsUI() {
            const type = select.value;
            const currentOptions = Array.from(optionsList.querySelectorAll('.input-group'));
            
            // Clear and rebuild options with new type
            optionsList.innerHTML = '';
            
            currentOptions.forEach((opt, idx) => {
                const textInput = opt.querySelector('input[type="text"]');
                const optionText = textInput ? textInput.value : '';
                const isChecked = opt.querySelector('input[type="radio"], input[type="checkbox"]')?.checked || false;
                
                const optionDiv = document.createElement('div');
                optionDiv.className = 'input-group mb-2';
                optionDiv.dataset.index = idx;

                if (type === 'single_choice') {
                    optionDiv.innerHTML = `
                        <div class="input-group-text">
                            <input class="form-check-input mt-0" type="radio" name="options[${idx}][is_correct]" value="1" ${isChecked ? 'checked' : ''}>
                        </div>
                        <input type="text" class="form-control" name="options[${idx}][text]" value="${optionText}" placeholder="Option text" required>
                        <button type="button" class="btn btn-outline-danger" onclick="this.closest('.input-group').remove()">
                            <i class="bi bi-trash"></i>
                        </button>
                    `;
                } else if (type === 'multiple_choice') {
                    optionDiv.innerHTML = `
                        <div class="input-group-text">
                            <input class="form-check-input mt-0" type="checkbox" name="correct_answers[]" value="${idx}" ${isChecked ? 'checked' : ''}>
                        </div>
                        <input type="text" class="form-control" name="options[${idx}][text]" value="${optionText}" placeholder="Option text" required>
                        <button type="button" class="btn btn-outline-danger" onclick="this.closest('.input-group').remove()">
                            <i class="bi bi-trash"></i>
                        </button>
                    `;
                } else {
                    optionDiv.innerHTML = `
                        <input type="text" class="form-control" name="options[${idx}][text]" value="${optionText}" placeholder="Correct answer" required>
                        <button type="button" class="btn btn-outline-danger" onclick="this.closest('.input-group').remove()">
                            <i class="bi bi-trash"></i>
                        </button>
                    `;
                }
                
                optionsList.appendChild(optionDiv);
            });

            // Update label
            if (type === 'text_input') {
                optionsSection.querySelector('label').textContent = 'Correct Answer(s)';
                addOptionBtn.innerHTML = '<i class="bi bi-plus-circle"></i> Add Alternative Answer';
            } else {
                optionsSection.querySelector('label').textContent = 'Answer Options';
                addOptionBtn.innerHTML = '<i class="bi bi-plus-circle"></i> Add Option';
            }
        }

        function addEditOption() {
            const type = select.value;
            const index = getOptionCount();
            const optionDiv = document.createElement('div');
            optionDiv.className = 'input-group mb-2';
            optionDiv.dataset.index = index;

            if (type === 'single_choice') {
                optionDiv.innerHTML = `
                    <div class="input-group-text">
                        <input class="form-check-input mt-0" type="radio" name="options[${index}][is_correct]" value="1">
                    </div>
                    <input type="text" class="form-control" name="options[${index}][text]" placeholder="Option text" required>
                    <button type="button" class="btn btn-outline-danger" onclick="this.closest('.input-group').remove()">
                        <i class="bi bi-trash"></i>
                    </button>
                `;
            } else if (type === 'multiple_choice') {
                optionDiv.innerHTML = `
                    <div class="input-group-text">
                        <input class="form-check-input mt-0" type="checkbox" name="correct_answers[]" value="${index}">
                    </div>
                    <input type="text" class="form-control" name="options[${index}][text]" placeholder="Option text" required>
                    <button type="button" class="btn btn-outline-danger" onclick="this.closest('.input-group').remove()">
                        <i class="bi bi-trash"></i>
                    </button>
                `;
            } else {
                optionDiv.innerHTML = `
                    <input type="text" class="form-control" name="options[${index}][text]" placeholder="Correct answer" required>
                    <button type="button" class="btn btn-outline-danger" onclick="this.closest('.input-group').remove()">
                        <i class="bi bi-trash"></i>
                    </button>
                `;
            }

            optionsList.appendChild(optionDiv);
        }

        select.addEventListener('change', updateEditOptionsUI);
        addOptionBtn.addEventListener('click', addEditOption);
    });
});
</script>
