<script setup>
import { onMounted, ref, watch, computed } from 'vue'
import { useTodoStore } from '@/stores/todos'
import TodoItem from '@/components/TodoItem.vue'
import TodoForm from '@/components/TodoForm.vue'
import TodoFilters from '@/components/TodoFilters.vue'
import StatsPanel from '@/components/StatsPanel.vue'

const store = useTodoStore()
const showForm = ref(false)
const editingTodo = ref(null)
const activeTab = ref('all')

const displayedTodos = computed(() => {
  if (activeTab.value === 'pending') return store.pendingTodos
  if (activeTab.value === 'completed') return store.completedTodos
  return store.todos
})

onMounted(async () => {
  await Promise.all([
    store.fetchTodos(),
    store.fetchCategories(),
    store.fetchStats(),
  ])
})

watch(() => store.filters, () => {
  store.fetchTodos()
}, { deep: true })

function openAddForm() {
  editingTodo.value = null
  showForm.value = true
}

function openEditForm(todo) {
  editingTodo.value = { ...todo }
  showForm.value = true
}

async function handleSave(todoData) {
  try {
    if (editingTodo.value) {
      await store.updateTodo(editingTodo.value.id, todoData)
    } else {
      await store.addTodo(todoData)
    }
    showForm.value = false
    editingTodo.value = null
  } catch (e) {
    console.error('Error saving todo:', e)
  }
}

function handleCancel() {
  showForm.value = false
  editingTodo.value = null
}

async function handleToggle(id) {
  await store.toggleTodo(id)
}

async function handleDelete(id) {
  if (confirm('Are you sure you want to delete this task?')) {
    await store.deleteTodo(id)
  }
}
</script>

<template>
  <div class="app-container">
    <!-- Animated Background -->
    <div class="bg-pattern">
      <div class="gradient-orb orb-1"></div>
      <div class="gradient-orb orb-2"></div>
      <div class="gradient-orb orb-3"></div>
    </div>

    <div class="main-content">
      <!-- Header -->
      <header class="app-header">
        <div class="header-content">
          <div class="logo-section">
            <div class="logo-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 11l3 3L22 4"/>
                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
              </svg>
            </div>
            <div>
              <h1>Taskflow</h1>
              <p class="tagline">Organize your day, achieve your goals</p>
            </div>
          </div>
          <button class="add-btn" @click="openAddForm">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <line x1="12" y1="5" x2="12" y2="19"/>
              <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            <span>New Task</span>
          </button>
        </div>
      </header>

      <!-- Stats Panel -->
      <StatsPanel :stats="store.stats" />

      <!-- Filters -->
      <TodoFilters 
        :filters="store.filters"
        :categories="store.categories"
        @update="(key, val) => store.setFilter(key, val)"
        @clear="store.clearFilters"
      />

      <!-- Tab Navigation -->
      <div class="tab-nav">
        <button 
          :class="['tab-btn', { active: activeTab === 'all' }]"
          @click="activeTab = 'all'"
        >
          All Tasks
          <span class="count">{{ store.todos.length }}</span>
        </button>
        <button 
          :class="['tab-btn', { active: activeTab === 'pending' }]"
          @click="activeTab = 'pending'"
        >
          Pending
          <span class="count pending">{{ store.pendingTodos.length }}</span>
        </button>
        <button 
          :class="['tab-btn', { active: activeTab === 'completed' }]"
          @click="activeTab = 'completed'"
        >
          Completed
          <span class="count completed">{{ store.completedTodos.length }}</span>
        </button>
      </div>

      <!-- Todo List -->
      <div class="todo-list">
        <TransitionGroup name="list">
          <TodoItem
            v-for="todo in displayedTodos"
            :key="todo.id"
            :todo="todo"
            @toggle="handleToggle"
            @edit="openEditForm"
            @delete="handleDelete"
          />
        </TransitionGroup>

        <!-- Empty State -->
        <div v-if="displayedTodos.length === 0 && !store.loading" class="empty-state">
          <div class="empty-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
          </div>
          <h3>No tasks yet</h3>
          <p>Create your first task to get started</p>
          <button class="empty-add-btn" @click="openAddForm">
            Create Task
          </button>
        </div>

        <!-- Loading State -->
        <div v-if="store.loading" class="loading-state">
          <div class="spinner"></div>
          <p>Loading tasks...</p>
        </div>
      </div>
    </div>

    <!-- Modal Form -->
    <Transition name="modal">
      <div v-if="showForm" class="modal-overlay" @click.self="handleCancel">
        <div class="modal-content">
          <TodoForm
            :todo="editingTodo"
            :categories="store.categories"
            @save="handleSave"
            @cancel="handleCancel"
          />
        </div>
      </div>
    </Transition>
  </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

:root {
  --bg-primary: #0a0a0f;
  --bg-secondary: #12121a;
  --bg-tertiary: #1a1a24;
  --bg-card: rgba(26, 26, 36, 0.8);
  --border-color: rgba(255, 255, 255, 0.06);
  --border-hover: rgba(255, 255, 255, 0.12);
  --text-primary: #ffffff;
  --text-secondary: rgba(255, 255, 255, 0.7);
  --text-muted: rgba(255, 255, 255, 0.4);
  --accent-primary: #6366f1;
  --accent-secondary: #8b5cf6;
  --accent-gradient: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%);
  --success: #10b981;
  --warning: #f59e0b;
  --danger: #ef4444;
  --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.3);
  --shadow-md: 0 4px 20px rgba(0, 0, 0, 0.4);
  --shadow-lg: 0 8px 40px rgba(0, 0, 0, 0.5);
  --shadow-glow: 0 0 40px rgba(99, 102, 241, 0.15);
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
  background: var(--bg-primary);
  color: var(--text-primary);
  min-height: 100vh;
  -webkit-font-smoothing: antialiased;
}

.app-container {
  min-height: 100vh;
  position: relative;
  overflow-x: hidden;
}

/* Animated Background */
.bg-pattern {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  pointer-events: none;
  overflow: hidden;
  z-index: 0;
}

.gradient-orb {
  position: absolute;
  border-radius: 50%;
  filter: blur(80px);
  opacity: 0.4;
  animation: float 20s infinite ease-in-out;
}

.orb-1 {
  width: 600px;
  height: 600px;
  background: radial-gradient(circle, rgba(99, 102, 241, 0.3) 0%, transparent 70%);
  top: -200px;
  right: -200px;
  animation-delay: 0s;
}

.orb-2 {
  width: 500px;
  height: 500px;
  background: radial-gradient(circle, rgba(139, 92, 246, 0.25) 0%, transparent 70%);
  bottom: -150px;
  left: -150px;
  animation-delay: -7s;
}

.orb-3 {
  width: 400px;
  height: 400px;
  background: radial-gradient(circle, rgba(168, 85, 247, 0.2) 0%, transparent 70%);
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  animation-delay: -14s;
}

@keyframes float {
  0%, 100% { transform: translate(0, 0) scale(1); }
  25% { transform: translate(30px, -30px) scale(1.05); }
  50% { transform: translate(-20px, 20px) scale(0.95); }
  75% { transform: translate(10px, 30px) scale(1.02); }
}

.main-content {
  position: relative;
  z-index: 1;
  max-width: 900px;
  margin: 0 auto;
  padding: 40px 24px 80px;
}

/* Header */
.app-header {
  margin-bottom: 40px;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 20px;
}

.logo-section {
  display: flex;
  align-items: center;
  gap: 16px;
}

.logo-icon {
  width: 52px;
  height: 52px;
  background: var(--accent-gradient);
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: var(--shadow-glow);
}

.logo-icon svg {
  width: 28px;
  height: 28px;
  color: white;
}

.app-header h1 {
  font-size: 28px;
  font-weight: 800;
  letter-spacing: -0.5px;
  background: var(--accent-gradient);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.tagline {
  font-size: 14px;
  color: var(--text-muted);
  margin-top: 2px;
}

.add-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 24px;
  background: var(--accent-gradient);
  border: none;
  border-radius: 12px;
  color: white;
  font-family: inherit;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: var(--shadow-md), var(--shadow-glow);
}

.add-btn:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg), 0 0 60px rgba(99, 102, 241, 0.25);
}

.add-btn svg {
  width: 18px;
  height: 18px;
}

/* Tab Navigation */
.tab-nav {
  display: flex;
  gap: 8px;
  margin-bottom: 24px;
  padding: 6px;
  background: var(--bg-card);
  border-radius: 14px;
  border: 1px solid var(--border-color);
  backdrop-filter: blur(20px);
}

.tab-btn {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 12px 16px;
  background: transparent;
  border: none;
  border-radius: 10px;
  color: var(--text-secondary);
  font-family: inherit;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.tab-btn:hover {
  color: var(--text-primary);
  background: rgba(255, 255, 255, 0.05);
}

.tab-btn.active {
  color: var(--text-primary);
  background: var(--bg-tertiary);
}

.tab-btn .count {
  padding: 2px 8px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
}

.tab-btn .count.pending {
  background: rgba(245, 158, 11, 0.2);
  color: var(--warning);
}

.tab-btn .count.completed {
  background: rgba(16, 185, 129, 0.2);
  color: var(--success);
}

/* Todo List */
.todo-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

/* List Transitions */
.list-enter-active,
.list-leave-active {
  transition: all 0.4s ease;
}

.list-enter-from {
  opacity: 0;
  transform: translateX(-30px);
}

.list-leave-to {
  opacity: 0;
  transform: translateX(30px);
}

.list-move {
  transition: transform 0.4s ease;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 60px 20px;
  color: var(--text-muted);
}

.empty-icon {
  width: 80px;
  height: 80px;
  margin: 0 auto 20px;
  background: var(--bg-tertiary);
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid var(--border-color);
}

.empty-icon svg {
  width: 40px;
  height: 40px;
  color: var(--text-muted);
}

.empty-state h3 {
  font-size: 18px;
  font-weight: 600;
  color: var(--text-secondary);
  margin-bottom: 8px;
}

.empty-state p {
  font-size: 14px;
  margin-bottom: 24px;
}

.empty-add-btn {
  padding: 12px 28px;
  background: var(--accent-gradient);
  border: none;
  border-radius: 10px;
  color: white;
  font-family: inherit;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.empty-add-btn:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-glow);
}

/* Loading State */
.loading-state {
  text-align: center;
  padding: 60px 20px;
  color: var(--text-muted);
}

.spinner {
  width: 40px;
  height: 40px;
  margin: 0 auto 16px;
  border: 3px solid var(--border-color);
  border-top-color: var(--accent-primary);
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Modal */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.7);
  backdrop-filter: blur(8px);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
  z-index: 1000;
}

.modal-content {
  width: 100%;
  max-width: 500px;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-enter-active,
.modal-leave-active {
  transition: all 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-from .modal-content,
.modal-leave-to .modal-content {
  transform: scale(0.9) translateY(20px);
}

/* Responsive */
@media (max-width: 640px) {
  .main-content {
    padding: 24px 16px 60px;
  }

  .header-content {
    flex-direction: column;
    align-items: stretch;
  }

  .logo-section {
    justify-content: center;
    text-align: center;
  }

  .add-btn {
    justify-content: center;
  }

  .tab-nav {
    flex-wrap: wrap;
  }

  .tab-btn {
    min-width: calc(50% - 4px);
  }
}
</style>
