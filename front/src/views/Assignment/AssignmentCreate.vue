<script setup>
import {onMounted, ref} from 'vue'
import axios from 'axios'
import {useRouter} from 'vue-router'

const router = useRouter()
const loading = ref(false)
const errors = ref({})

const soldiers = ref([])
const availableItems = ref([])

const form = ref({
  soldier_id: '',
  warehouse_id: ''
})

const fetchData = async () => {
  try {
    const soldiersRes = await axios.get('http://localhost:8080/api/soldiers?status=active&all=true')
    soldiers.value = soldiersRes.data.data

    const warehouseRes = await axios.get('http://localhost:8080/api/warehouse?status=in_stock&all=true')
    availableItems.value = warehouseRes.data.data

  } catch (error) {
    console.error('Помилка завантаження списків:', error)
  }
}

const issueItem = async () => {
  loading.value = true
  errors.value = {}

  try {
    await axios.post('http://localhost:8080/api/assignments/issue', form.value)
    router.push({name: 'assignments.index'})
  } catch (error) {
    if (error.response && error.response.status === 422) {
      errors.value = error.response.data.errors
    } else {
      alert(error.response?.data?.message || 'Помилка видачі')
    }
  } finally {
    loading.value = false
  }
}

onMounted(() => fetchData())
</script>

<template>
  <div class="page-container">
    <div class="header-row">
      <button class="back-btn" @click="router.back()">← Назад</button>
      <h2>📤 Видача майна</h2>
    </div>

    <div class="form-card">
      <form @submit.prevent="issueItem">

        <div class="form-group">
          <label>Оберіть бійця</label>
          <select
              v-model="form.soldier_id"
              :class="{ 'error-input': errors.soldier_id }"
          >
            <option value="" disabled>--- Оберіть зі списку ---</option>
            <option v-for="s in soldiers" :key="s.id" :value="s.id">
              {{ s.full_name }} ({{ s.rank }})
            </option>
          </select>
          <span v-if="errors.soldier_id" class="error-text">{{ errors.soldier_id[0] }}</span>
        </div>

        <div class="form-group">
          <label>Оберіть майно (Тільки те, що на складі)</label>
          <select
              v-model="form.warehouse_id"
              :class="{ 'error-input': errors.warehouse_id }"
          >
            <option value="" disabled>--- Оберіть вільне майно ---</option>
            <option v-for="item in availableItems" :key="item.id" :value="item.id">
              {{ item.equipment_type_name }} | {{ item.serial_number }}
            </option>
          </select>
          <div v-if="availableItems.length === 0" class="hint">
            На складі немає вільного майна (in_stock).
          </div>
          <span v-if="errors.warehouse_id" class="error-text">{{ errors.warehouse_id[0] }}</span>
        </div>

        <div class="form-actions">
          <button type="submit" class="save-btn" :disabled="loading || availableItems.length === 0">
            {{ loading ? 'Обробка...' : 'Підтвердити видачу' }}
          </button>
        </div>

      </form>
    </div>
  </div>
</template>

<style scoped>
.page-container {
  padding: 20px;
  max-width: 600px;
  margin: 0 auto;
}

.header-row {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-bottom: 25px;
}

.back-btn {
  background: none;
  border: none;
  cursor: pointer;
  color: #64748b;
  font-size: 1rem;
}

.form-card {
  background: white;
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  border: 1px solid #e2e8f0;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: 600;
  color: #334155;
}

.form-group select {
  width: 100%;
  padding: 12px;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  font-size: 1rem;
  background-color: #f8fafc;
}

.form-group select:focus {
  outline: none;
  border-color: #3b82f6;
  background-color: white;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.error-input {
  border-color: #ef4444 !important;
  background-color: #fef2f2 !important;
}

.error-text {
  color: #ef4444;
  font-size: 0.85rem;
  margin-top: 6px;
  display: block;
}

.hint {
  font-size: 0.85rem;
  color: #f59e0b;
  margin-top: 5px;
}

.save-btn {
  background: #3b82f6;
  color: white;
  padding: 12px 24px;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  width: 100%;
  margin-top: 10px;
}

.save-btn:hover {
  background: #2563eb;
}

.save-btn:disabled {
  background: #94a3b8;
  cursor: not-allowed;
}
</style>