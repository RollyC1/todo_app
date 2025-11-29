<script setup>
import { ref, computed } from 'vue'
import { useDebounceFn } from '@vueuse/core'

const props = defineProps({
  filters: {
    type: Object,
    required: true
  },
  categories: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['update', 'clear'])

const showFilters = ref(false)
const searchInput = ref(props.filters.search)

const hasActiveFilters = computed(() => {
  return props.filters.category || 
         props.filters.completed !== '' || 
         props.filters.priority ||
         props.filters.search
})

const debouncedSearch = useDebounceFn((value) => {
  emit('update', 'search', value)
}, 300)

function handleSearchInput(e) {
  searchInput.value = e.target.value
  debouncedSearch(e.target.value)
}

function clearSearch() {
  searchInput.value = ''
  emit('update', 'search', '')
}

function handleClearAll() {
  searchInput.value = ''
  emit('clear')
}
</script>

<template>
  <div class="filters-container">
    <!-- Search Bar -->
    <div class="search-bar">
      <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="11" cy="11" r="8"/>
        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
      </svg>
      <input
        type="text"
        :value="searchInput"
        placeholder="Search tasks..."
        @input="handleSearchInput"
      />
      <button 
        v-if="searchInput" 
        class="clear-search" 
        @click="clearSearch"
      >
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="18" y1="6" x2="6" y2="18"/>
          <line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
      </button>
      <button 
        :class="['filter-toggle', { active: showFilters || hasActiveFilters }]"
        @click="showFilters = !showFilters"
      >
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
        </svg>
        <span v-if="hasActiveFilters" class="filter-badge"></span>
      </button>
    </div>

    <!-- Filter Panel -->
    <Transition name="slide">
      <div v-if="showFilters" class="filter-panel">
        <div class="filter-row">
          <div class="filter-group">
            <label>Category</label>
            <select 
              :value="filters.category"
              @change="emit('update', 'category', $event.target.value)"
            >
              <option value="">All categories</option>
              <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
            </select>
          </div>

          <div class="filter-group">
            <label>Status</label>
            <select 
              :value="filters.completed"
              @change="emit('update', 'completed', $event.target.value)"
            >
              <option value="">All status</option>
              <option value="false">Pending</option>
              <option value="true">Completed</option>
            </select>
          </div>

          <div class="filter-group">
            <label>Priority</label>
            <select 
              :value="filters.priority"
              @change="emit('update', 'priority', $event.target.value)"
            >
              <option value="">All priorities</option>
              <option value="high">High</option>
              <option value="medium">Medium</option>
              <option value="low">Low</option>
            </select>
          </div>

          <div class="filter-group">
            <label>Sort by</label>
            <select 
              :value="filters.sortBy"
              @change="emit('update', 'sortBy', $event.target.value)"
            >
              <option value="created_at">Date created</option>
              <option value="due_date">Due date</option>
              <option value="priority">Priority</option>
              <option value="title">Title</option>
            </select>
          </div>
        </div>

        <div v-if="hasActiveFilters" class="filter-actions">
          <button class="clear-filters" @click="handleClearAll">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="18" y1="6" x2="6" y2="18"/>
              <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
            Clear all filters
          </button>
        </div>
      </div>
    </Transition>
  </div>
</template>

<style scoped>
.filters-container {
  margin-bottom: 24px;
}

.search-bar {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  background: var(--bg-card);
  border: 1px solid var(--border-color);
  border-radius: 14px;
  backdrop-filter: blur(20px);
}

.search-icon {
  width: 20px;
  height: 20px;
  color: var(--text-muted);
  flex-shrink: 0;
}

.search-bar input {
  flex: 1;
  background: transparent;
  border: none;
  color: var(--text-primary);
  font-family: inherit;
  font-size: 14px;
  outline: none;
}

.search-bar input::placeholder {
  color: var(--text-muted);
}

.clear-search,
.filter-toggle {
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
  position: relative;
}

.clear-search:hover,
.filter-toggle:hover {
  color: var(--text-primary);
  background: var(--border-hover);
}

.filter-toggle.active {
  background: var(--accent-primary);
  color: white;
}

.clear-search svg,
.filter-toggle svg {
  width: 18px;
  height: 18px;
}

.filter-badge {
  position: absolute;
  top: 6px;
  right: 6px;
  width: 8px;
  height: 8px;
  background: var(--danger);
  border-radius: 50%;
}

.filter-toggle.active .filter-badge {
  background: white;
}

.filter-panel {
  margin-top: 12px;
  padding: 20px;
  background: var(--bg-card);
  border: 1px solid var(--border-color);
  border-radius: 14px;
  backdrop-filter: blur(20px);
}

.filter-row {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 16px;
}

.filter-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.filter-group label {
  font-size: 12px;
  font-weight: 600;
  color: var(--text-muted);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.filter-group select {
  width: 100%;
  padding: 10px 14px;
  background: var(--bg-tertiary);
  border: 1px solid var(--border-color);
  border-radius: 10px;
  color: var(--text-primary);
  font-family: inherit;
  font-size: 13px;
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='rgba(255,255,255,0.4)' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 10px center;
  padding-right: 36px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.filter-group select:hover {
  border-color: var(--border-hover);
}

.filter-group select:focus {
  outline: none;
  border-color: var(--accent-primary);
}

.filter-actions {
  margin-top: 16px;
  padding-top: 16px;
  border-top: 1px solid var(--border-color);
}

.clear-filters {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  background: transparent;
  border: 1px solid var(--border-color);
  border-radius: 10px;
  color: var(--text-secondary);
  font-family: inherit;
  font-size: 13px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.clear-filters:hover {
  border-color: var(--danger);
  color: var(--danger);
}

.clear-filters svg {
  width: 16px;
  height: 16px;
}

/* Slide Transition */
.slide-enter-active,
.slide-leave-active {
  transition: all 0.3s ease;
}

.slide-enter-from,
.slide-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

@media (max-width: 640px) {
  .filter-row {
    grid-template-columns: 1fr 1fr;
  }
}
</style>
