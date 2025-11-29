<script setup>
import { computed } from 'vue'

const props = defineProps({
  todo: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['toggle', 'edit', 'delete'])

const priorityClass = computed(() => {
  return `priority-${props.todo.priority || 'medium'}`
})

const isOverdue = computed(() => {
  if (!props.todo.due_date || props.todo.completed) return false
  const today = new Date().toISOString().split('T')[0]
  return props.todo.due_date < today
})

const formattedDate = computed(() => {
  if (!props.todo.due_date) return null
  const date = new Date(props.todo.due_date)
  const today = new Date()
  const tomorrow = new Date(today)
  tomorrow.setDate(tomorrow.getDate() + 1)
  
  const todoDate = date.toISOString().split('T')[0]
  const todayStr = today.toISOString().split('T')[0]
  const tomorrowStr = tomorrow.toISOString().split('T')[0]
  
  if (todoDate === todayStr) return 'Today'
  if (todoDate === tomorrowStr) return 'Tomorrow'
  
  return date.toLocaleDateString('en-US', { 
    month: 'short', 
    day: 'numeric'
  })
})
</script>

<template>
  <div :class="['todo-item', { completed: todo.completed, overdue: isOverdue }]">
    <div class="todo-main">
      <button 
        class="checkbox" 
        :class="{ checked: todo.completed }"
        @click="emit('toggle', todo.id)"
      >
        <svg v-if="todo.completed" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
          <polyline points="20 6 9 17 4 12"/>
        </svg>
      </button>
      
      <div class="todo-content">
        <div class="todo-header">
          <h3 class="todo-title">{{ todo.title }}</h3>
          <div class="todo-badges">
            <span v-if="todo.category" class="badge category">{{ todo.category }}</span>
            <span :class="['badge', priorityClass]">{{ todo.priority || 'medium' }}</span>
          </div>
        </div>
        
        <p v-if="todo.description" class="todo-description">{{ todo.description }}</p>
        
        <div class="todo-meta">
          <span v-if="formattedDate" :class="['due-date', { overdue: isOverdue }]">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
              <line x1="16" y1="2" x2="16" y2="6"/>
              <line x1="8" y1="2" x2="8" y2="6"/>
              <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            {{ formattedDate }}
          </span>
        </div>
      </div>
    </div>
    
    <div class="todo-actions">
      <button class="action-btn edit" @click="emit('edit', todo)" title="Edit">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
          <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
        </svg>
      </button>
      <button class="action-btn delete" @click="emit('delete', todo.id)" title="Delete">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <polyline points="3 6 5 6 21 6"/>
          <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
        </svg>
      </button>
    </div>
  </div>
</template>

<style scoped>
.todo-item {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 16px;
  padding: 20px;
  background: var(--bg-card);
  border: 1px solid var(--border-color);
  border-radius: 16px;
  backdrop-filter: blur(20px);
  transition: all 0.3s ease;
}

.todo-item:hover {
  border-color: var(--border-hover);
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
}

.todo-item.completed {
  opacity: 0.6;
}

.todo-item.completed .todo-title {
  text-decoration: line-through;
  color: var(--text-muted);
}

.todo-item.overdue:not(.completed) {
  border-color: rgba(239, 68, 68, 0.3);
}

.todo-main {
  display: flex;
  gap: 16px;
  flex: 1;
  min-width: 0;
}

.checkbox {
  width: 24px;
  height: 24px;
  min-width: 24px;
  border: 2px solid var(--border-hover);
  border-radius: 8px;
  background: transparent;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
  margin-top: 2px;
}

.checkbox:hover {
  border-color: var(--accent-primary);
  background: rgba(99, 102, 241, 0.1);
}

.checkbox.checked {
  background: var(--accent-gradient);
  border-color: transparent;
}

.checkbox svg {
  width: 14px;
  height: 14px;
  color: white;
}

.todo-content {
  flex: 1;
  min-width: 0;
}

.todo-header {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  margin-bottom: 6px;
  flex-wrap: wrap;
}

.todo-title {
  font-size: 15px;
  font-weight: 600;
  color: var(--text-primary);
  line-height: 1.4;
}

.todo-badges {
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
}

.badge {
  padding: 3px 10px;
  border-radius: 20px;
  font-size: 11px;
  font-weight: 600;
  text-transform: capitalize;
}

.badge.category {
  background: rgba(99, 102, 241, 0.15);
  color: var(--accent-primary);
}

.badge.priority-high {
  background: rgba(239, 68, 68, 0.15);
  color: var(--danger);
}

.badge.priority-medium {
  background: rgba(245, 158, 11, 0.15);
  color: var(--warning);
}

.badge.priority-low {
  background: rgba(16, 185, 129, 0.15);
  color: var(--success);
}

.todo-description {
  font-size: 13px;
  color: var(--text-secondary);
  line-height: 1.5;
  margin-bottom: 10px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.todo-meta {
  display: flex;
  align-items: center;
  gap: 12px;
}

.due-date {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  color: var(--text-muted);
}

.due-date svg {
  width: 14px;
  height: 14px;
}

.due-date.overdue {
  color: var(--danger);
}

.todo-actions {
  display: flex;
  gap: 4px;
  opacity: 0;
  transition: opacity 0.2s ease;
}

.todo-item:hover .todo-actions {
  opacity: 1;
}

.action-btn {
  width: 36px;
  height: 36px;
  border: none;
  border-radius: 10px;
  background: transparent;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
  color: var(--text-muted);
}

.action-btn:hover {
  background: var(--bg-tertiary);
}

.action-btn.edit:hover {
  color: var(--accent-primary);
}

.action-btn.delete:hover {
  color: var(--danger);
}

.action-btn svg {
  width: 18px;
  height: 18px;
}

@media (max-width: 640px) {
  .todo-item {
    padding: 16px;
  }

  .todo-actions {
    opacity: 1;
  }

  .todo-header {
    flex-direction: column;
    gap: 8px;
  }
}
</style>
