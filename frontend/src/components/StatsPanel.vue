<script setup>
import { computed } from 'vue'

const props = defineProps({
  stats: {
    type: Object,
    default: null
  }
})

const completionRate = computed(() => {
  if (!props.stats || props.stats.total === 0) return 0
  return Math.round((props.stats.completed / props.stats.total) * 100)
})
</script>

<template>
  <div v-if="stats" class="stats-panel">
    <div class="stat-card total">
      <div class="stat-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
      </div>
      <div class="stat-content">
        <span class="stat-value">{{ stats.total }}</span>
        <span class="stat-label">Total Tasks</span>
      </div>
    </div>

    <div class="stat-card pending">
      <div class="stat-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="10"/>
          <polyline points="12 6 12 12 16 14"/>
        </svg>
      </div>
      <div class="stat-content">
        <span class="stat-value">{{ stats.pending }}</span>
        <span class="stat-label">Pending</span>
      </div>
    </div>

    <div class="stat-card completed">
      <div class="stat-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
          <polyline points="22 4 12 14.01 9 11.01"/>
        </svg>
      </div>
      <div class="stat-content">
        <span class="stat-value">{{ stats.completed }}</span>
        <span class="stat-label">Completed</span>
      </div>
    </div>

    <div class="stat-card overdue">
      <div class="stat-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
          <line x1="12" y1="9" x2="12" y2="13"/>
          <line x1="12" y1="17" x2="12.01" y2="17"/>
        </svg>
      </div>
      <div class="stat-content">
        <span class="stat-value">{{ stats.overdue }}</span>
        <span class="stat-label">Overdue</span>
      </div>
    </div>

    <div class="stat-card progress">
      <div class="progress-ring">
        <svg viewBox="0 0 36 36">
          <path
            class="circle-bg"
            d="M18 2.0845
              a 15.9155 15.9155 0 0 1 0 31.831
              a 15.9155 15.9155 0 0 1 0 -31.831"
          />
          <path
            class="circle"
            :stroke-dasharray="`${completionRate}, 100`"
            d="M18 2.0845
              a 15.9155 15.9155 0 0 1 0 31.831
              a 15.9155 15.9155 0 0 1 0 -31.831"
          />
        </svg>
        <span class="progress-value">{{ completionRate }}%</span>
      </div>
      <span class="stat-label">Completion Rate</span>
    </div>
  </div>
</template>

<style scoped>
.stats-panel {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
  gap: 12px;
  margin-bottom: 24px;
}

.stat-card {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 18px;
  background: var(--bg-card);
  border: 1px solid var(--border-color);
  border-radius: 16px;
  backdrop-filter: blur(20px);
  transition: all 0.3s ease;
}

.stat-card:hover {
  border-color: var(--border-hover);
  transform: translateY(-2px);
}

.stat-card.progress {
  flex-direction: column;
  align-items: center;
  text-align: center;
  gap: 8px;
}

.stat-icon {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.stat-icon svg {
  width: 22px;
  height: 22px;
}

.stat-card.total .stat-icon {
  background: rgba(99, 102, 241, 0.15);
  color: var(--accent-primary);
}

.stat-card.pending .stat-icon {
  background: rgba(245, 158, 11, 0.15);
  color: var(--warning);
}

.stat-card.completed .stat-icon {
  background: rgba(16, 185, 129, 0.15);
  color: var(--success);
}

.stat-card.overdue .stat-icon {
  background: rgba(239, 68, 68, 0.15);
  color: var(--danger);
}

.stat-content {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.stat-value {
  font-size: 24px;
  font-weight: 700;
  color: var(--text-primary);
  line-height: 1;
}

.stat-label {
  font-size: 12px;
  color: var(--text-muted);
  font-weight: 500;
}

.progress-ring {
  position: relative;
  width: 70px;
  height: 70px;
}

.progress-ring svg {
  width: 100%;
  height: 100%;
  transform: rotate(-90deg);
}

.circle-bg {
  fill: none;
  stroke: var(--bg-tertiary);
  stroke-width: 3;
}

.circle {
  fill: none;
  stroke: url(#gradient);
  stroke: var(--accent-primary);
  stroke-width: 3;
  stroke-linecap: round;
  transition: stroke-dasharray 0.6s ease;
}

.progress-value {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  font-size: 16px;
  font-weight: 700;
  color: var(--text-primary);
}

@media (max-width: 640px) {
  .stats-panel {
    grid-template-columns: repeat(2, 1fr);
  }

  .stat-card.progress {
    grid-column: span 2;
  }
}
</style>
