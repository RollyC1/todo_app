import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { todoService } from '@/services/api'

export const useTodoStore = defineStore('todos', () => {
  // State
  const todos = ref([])
  const categories = ref([])
  const stats = ref(null)
  const loading = ref(false)
  const error = ref(null)
  const filters = ref({
    search: '',
    category: '',
    completed: '',
    priority: '',
    sortBy: 'created_at',
    sortOrder: 'desc',
  })

  // Getters
  const filteredTodos = computed(() => {
    return todos.value
  })

  const pendingTodos = computed(() => {
    return todos.value.filter(t => !t.completed)
  })

  const completedTodos = computed(() => {
    return todos.value.filter(t => t.completed)
  })

  const overdueTodos = computed(() => {
    const today = new Date().toISOString().split('T')[0]
    return todos.value.filter(t => !t.completed && t.due_date && t.due_date < today)
  })

  // Actions
  async function fetchTodos() {
    loading.value = true
    error.value = null
    try {
      const response = await todoService.getAll(filters.value)
      todos.value = response.data
    } catch (e) {
      error.value = e.message || 'Failed to fetch todos'
      throw e
    } finally {
      loading.value = false
    }
  }

  async function fetchCategories() {
    try {
      const response = await todoService.getCategories()
      categories.value = response.data
    } catch (e) {
      console.error('Failed to fetch categories:', e)
    }
  }

  async function fetchStats() {
    try {
      const response = await todoService.getStats()
      stats.value = response.data
    } catch (e) {
      console.error('Failed to fetch stats:', e)
    }
  }

  async function addTodo(todoData) {
    loading.value = true
    error.value = null
    try {
      const response = await todoService.create(todoData)
      todos.value.unshift(response.data)
      await fetchStats()
      await fetchCategories()
      return response.data
    } catch (e) {
      error.value = e.response?.data?.message || 'Failed to create todo'
      throw e
    } finally {
      loading.value = false
    }
  }

  async function updateTodo(id, todoData) {
    loading.value = true
    error.value = null
    try {
      const response = await todoService.update(id, todoData)
      const index = todos.value.findIndex(t => t.id === id)
      if (index !== -1) {
        todos.value[index] = response.data
      }
      await fetchStats()
      await fetchCategories()
      return response.data
    } catch (e) {
      error.value = e.response?.data?.message || 'Failed to update todo'
      throw e
    } finally {
      loading.value = false
    }
  }

  async function toggleTodo(id) {
    try {
      const response = await todoService.toggle(id)
      const index = todos.value.findIndex(t => t.id === id)
      if (index !== -1) {
        todos.value[index] = response.data
      }
      await fetchStats()
      return response.data
    } catch (e) {
      error.value = e.response?.data?.message || 'Failed to toggle todo'
      throw e
    }
  }

  async function deleteTodo(id) {
    try {
      await todoService.delete(id)
      todos.value = todos.value.filter(t => t.id !== id)
      await fetchStats()
      await fetchCategories()
    } catch (e) {
      error.value = e.response?.data?.message || 'Failed to delete todo'
      throw e
    }
  }

  function setFilter(key, value) {
    filters.value[key] = value
  }

  function clearFilters() {
    filters.value = {
      search: '',
      category: '',
      completed: '',
      priority: '',
      sortBy: 'created_at',
      sortOrder: 'desc',
    }
  }

  return {
    // State
    todos,
    categories,
    stats,
    loading,
    error,
    filters,
    // Getters
    filteredTodos,
    pendingTodos,
    completedTodos,
    overdueTodos,
    // Actions
    fetchTodos,
    fetchCategories,
    fetchStats,
    addTodo,
    updateTodo,
    toggleTodo,
    deleteTodo,
    setFilter,
    clearFilters,
  }
})
