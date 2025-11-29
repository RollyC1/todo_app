import axios from 'axios'

// API URL configuration
// In production (Vercel), VITE_API_URL will be set to the Railway backend
// In development, it defaults to localhost
const API_BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'

const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  withCredentials: true, // Enable for CORS with credentials
})

// Request interceptor for auth token
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('auth_token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

// Response interceptor for error handling
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('auth_token')
      window.location.href = '/login'
    }
    return Promise.reject(error)
  }
)

export const todoService = {
  // Get all todos with optional filters
  async getAll(params = {}) {
    const response = await api.get('/todos', { params })
    return response.data
  },

  // Get a single todo
  async get(id) {
    const response = await api.get(`/todos/${id}`)
    return response.data
  },

  // Create a new todo
  async create(data) {
    const response = await api.post('/todos', data)
    return response.data
  },

  // Update a todo
  async update(id, data) {
    const response = await api.put(`/todos/${id}`, data)
    return response.data
  },

  // Toggle todo completion
  async toggle(id) {
    const response = await api.patch(`/todos/${id}/toggle`)
    return response.data
  },

  // Delete a todo
  async delete(id) {
    const response = await api.delete(`/todos/${id}`)
    return response.data
  },

  // Get categories
  async getCategories() {
    const response = await api.get('/todos/categories')
    return response.data
  },

  // Get stats
  async getStats() {
    const response = await api.get('/todos/stats')
    return response.data
  },
}

export const authService = {
  async login(credentials) {
    const response = await api.post('/login', credentials)
    return response.data
  },

  async register(data) {
    const response = await api.post('/register', data)
    return response.data
  },

  async logout() {
    const response = await api.post('/logout')
    localStorage.removeItem('auth_token')
    return response.data
  },

  async getUser() {
    const response = await api.get('/user')
    return response.data
  },
}

export default api
