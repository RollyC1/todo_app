<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
  todo: {
    type: Object,
    default: null
  },
  categories: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['save', 'cancel'])

const form = ref({
  title: '',
  description: '',
  category: '',
  due_date: '',
  priority: 'medium'
})

const errors = ref({})
const newCategory = ref('')
const showNewCategory = ref(false)

const isEditing = computed(() => !!props.todo)

const title = computed(() => isEditing.value ? 'Edit Task' : 'Create New Task')

watch(() => props.todo, (newTodo) => {
  if (newTodo) {
    form.value = {
      title: newTodo.title || '',
      description: newTodo.description || '',
      category: newTodo.category || '',
      due_date: newTodo.due_date || '',
      priority: newTodo.priority || 'medium'
    }
  } else {
    resetForm()
  }
}, { immediate: true })

function resetForm() {
  form.value = {
    title: '',
    description: '',
    category: '',
    due_date: '',
    priority: 'medium'
  }
  errors.value = {}
  newCategory.value = ''
  showNewCategory.value = false
}

function validate() {
  errors.value = {}
  
  if (!form.value.title.trim()) {
    errors.value.title = 'Title is required'
  } else if (form.value.title.length > 255) {
    errors.value.title = 'Title must be less than 255 characters'
  }
  
  if (form.value.description && form.value.description.length > 1000) {
    errors.value.description = 'Description must be less than 1000 characters'
  }
  
  return Object.keys(errors.value).length === 0
}

function handleSubmit() {
  if (validate()) {
    const data = { ...form.value }
    if (showNewCategory.value && newCategory.value.trim()) {
      data.category = newCategory.value.trim()
    }
    if (!data.due_date) {
      data.due_date = null
    }
    emit('save', data)
  }
}

function toggleNewCategory() {
  showNewCategory.value = !showNewCategory.value
  if (!showNewCategory.value) {
    newCategory.value = ''
  } else {
    form.value.category = ''
  }
}
</script>

<template>
  <div class="form-container">
    <div class="form-header">
      <h2>{{ title }}</h2>
      <button class="close-btn" @click="emit('cancel')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="18" y1="6" x2="6" y2="18"/>
          <line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
      </button>
    </div>

    <div class="form-body">
      <div class="form-group">
        <label for="title">Title <span class="required">*</span></label>
        <input
          id="title"
          v-model="form.title"
          type="text"
          placeholder="What needs to be done?"
          :class="{ error: errors.title }"
        />
        <span v-if="errors.title" class="error-message">{{ errors.title }}</span>
      </div>

      <div class="form-group">
        <label for="description">Description</label>
        <textarea
          id="description"
          v-model="form.description"
          placeholder="Add more details..."
          rows="3"
          :class="{ error: errors.description }"
        ></textarea>
        <span v-if="errors.description" class="error-message">{{ errors.description }}</span>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="category">Category</label>
          <div v-if="!showNewCategory" class="select-wrapper">
            <select id="category" v-model="form.category">
              <option value="">Select category</option>
              <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
            </select>
            <button type="button" class="add-category-btn" @click="toggleNewCategory">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
              </svg>
            </button>
          </div>
          <div v-else class="new-category-input">
            <input
              v-model="newCategory"
              type="text"
              placeholder="New category name"
            />
            <button type="button" class="cancel-category-btn" @click="toggleNewCategory">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
              </svg>
            </button>
          </div>
        </div>

        <div class="form-group">
          <label for="priority">Priority</label>
          <select id="priority" v-model="form.priority">
            <option value="low">Low</option>
            <option value="medium">Medium</option>
            <option value="high">High</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label for="due_date">Due Date</label>
        <input
          id="due_date"
          v-model="form.due_date"
          type="date"
        />
      </div>
    </div>

    <div class="form-footer">
      <button type="button" class="btn btn-secondary" @click="emit('cancel')">
        Cancel
      </button>
      <button type="button" class="btn btn-primary" @click="handleSubmit">
        {{ isEditing ? 'Update Task' : 'Create Task' }}
      </button>
    </div>
  </div>
</template>

<style scoped>
.form-container {
  background: var(--bg-secondary);
  border: 1px solid var(--border-color);
  border-radius: 20px;
  overflow: hidden;
}

.form-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 24px 24px 0;
}

.form-header h2 {
  font-size: 20px;
  font-weight: 700;
  color: var(--text-primary);
}

.close-btn {
  width: 36px;
  height: 36px;
  border: none;
  border-radius: 10px;
  background: var(--bg-tertiary);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--text-muted);
  transition: all 0.2s ease;
}

.close-btn:hover {
  background: var(--border-hover);
  color: var(--text-primary);
}

.close-btn svg {
  width: 18px;
  height: 18px;
}

.form-body {
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-group label {
  font-size: 13px;
  font-weight: 600;
  color: var(--text-secondary);
}

.required {
  color: var(--danger);
}

.form-group input[type="text"],
.form-group input[type="date"],
.form-group textarea,
.form-group select {
  width: 100%;
  padding: 12px 16px;
  background: var(--bg-tertiary);
  border: 1px solid var(--border-color);
  border-radius: 12px;
  color: var(--text-primary);
  font-family: inherit;
  font-size: 14px;
  transition: all 0.2s ease;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
  outline: none;
  border-color: var(--accent-primary);
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
}

.form-group input.error,
.form-group textarea.error {
  border-color: var(--danger);
}

.form-group input::placeholder,
.form-group textarea::placeholder {
  color: var(--text-muted);
}

.form-group textarea {
  resize: vertical;
  min-height: 80px;
}

.form-group select {
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='rgba(255,255,255,0.4)' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 12px center;
  padding-right: 40px;
}

.error-message {
  font-size: 12px;
  color: var(--danger);
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.select-wrapper {
  display: flex;
  gap: 8px;
}

.select-wrapper select {
  flex: 1;
}

.add-category-btn,
.cancel-category-btn {
  width: 44px;
  min-width: 44px;
  height: 44px;
  border: 1px solid var(--border-color);
  border-radius: 12px;
  background: var(--bg-tertiary);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--text-muted);
  transition: all 0.2s ease;
}

.add-category-btn:hover {
  border-color: var(--accent-primary);
  color: var(--accent-primary);
}

.cancel-category-btn:hover {
  border-color: var(--danger);
  color: var(--danger);
}

.add-category-btn svg,
.cancel-category-btn svg {
  width: 18px;
  height: 18px;
}

.new-category-input {
  display: flex;
  gap: 8px;
}

.new-category-input input {
  flex: 1;
}

.form-footer {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding: 20px 24px;
  background: var(--bg-primary);
  border-top: 1px solid var(--border-color);
}

.btn {
  padding: 12px 24px;
  border: none;
  border-radius: 10px;
  font-family: inherit;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-secondary {
  background: var(--bg-tertiary);
  color: var(--text-secondary);
}

.btn-secondary:hover {
  background: var(--border-hover);
  color: var(--text-primary);
}

.btn-primary {
  background: var(--accent-gradient);
  color: white;
}

.btn-primary:hover {
  transform: translateY(-1px);
  box-shadow: var(--shadow-glow);
}

@media (max-width: 640px) {
  .form-row {
    grid-template-columns: 1fr;
  }

  .form-footer {
    flex-direction: column;
  }

  .btn {
    width: 100%;
    justify-content: center;
  }
}
</style>
