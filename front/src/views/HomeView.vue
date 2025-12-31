<script setup>
import {onMounted, ref} from 'vue'
import axios from 'axios'

const stats = ref({
  soldier_count: 0,
  free_weapons_count: 0,
  issued_weapons_count: 0,
  in_duty_count: 0
})

const loading = ref(true)

const fetchStats = async () => {
  try {
    const response = await axios.get('http://localhost:8080/api/stats')

    stats.value = response.data.data
  } catch (error) {
    console.error('Помилка завантаження статистики:', error)
    alert('Не вдалося оновити дані')
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchStats()
})
</script>

<template>
  <div class="dashboard">
    <h2>Оперативна статистика</h2>

    <div v-if="loading" class="loading-text">Оновлення даних...</div>

    <div v-else class="stats-grid">
      <div class="card blue">
        <h3>Особовий склад</h3>
        <p class="number">{{ stats.soldier_count }}</p>
        <span class="label">Всього бійців</span>
      </div>

      <div class="card green">
        <h3>Склад майна</h3>
        <p class="number">{{ stats.free_weapons_count }}</p>
        <span class="label">Доступно одиниць</span>
      </div>

      <div class="card orange">
        <h3>Видана зброя</h3>
        <p class="number">{{ stats.issued_weapons_count }}</p>
        <span class="label">На руках</span>
      </div>

      <div class="card red">
        <h3>Наряд</h3>
        <p class="number">{{ stats.in_duty_count }}</p>
        <span class="label">Зараз на службі</span>
      </div>
    </div>
  </div>
</template>

<style scoped>
.loading-text {
  font-size: 18px;
  color: #64748b;
  margin-bottom: 20px;
}

.dashboard {
  padding: 0;
}

h2 {
  margin-bottom: 24px;
  color: #334155;
  font-size: 24px;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 24px;
}

.card {
  background: white;
  padding: 24px;
  border-radius: 12px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  border-bottom: 4px solid transparent;
  transition: transform 0.2s;
}

.card:hover {
  transform: translateY(-2px);
}

.card.blue {
  border-bottom-color: #3b82f6;
}

.card.green {
  border-bottom-color: #22c55e;
}

.card.orange {
  border-bottom-color: #f97316;
}

.card.red {
  border-bottom-color: #ef4444;
}

.card h3 {
  font-size: 14px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: #64748b;
  margin-bottom: 8px;
  font-weight: 600;
}

.number {
  font-size: 48px;
  font-weight: 700;
  color: #0f172a;
  line-height: 1;
  margin-bottom: 8px;
}

.label {
  font-size: 14px;
  color: #94a3b8;
}
</style>