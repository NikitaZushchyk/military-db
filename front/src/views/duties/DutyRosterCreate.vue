<script setup>
import {onMounted, ref} from 'vue'
import axios from 'axios'
import {useRouter} from 'vue-router'

const router = useRouter()
const loading = ref(false)
const errors = ref({})

const soldiers = ref([])
const dutyTypes = ref([])

const form = ref({
  soldier_id: '',
  duty_type_id: '',
  start_time: '',
  end_time: ''
})

const fetchData = async () => {
  try {
    const soldiersRes = await axios.get('http://localhost:8080/api/soldiers?status=active&all=true')
    soldiers.value = soldiersRes.data.data
    const typesRes = await axios.get('http://localhost:8080/api/roster')
    if (typesRes.data.meta_data && typesRes.data.meta_data.types) {
      dutyTypes.value = typesRes.data.meta_data.types
    } else {
      alert('Помилка: Не вдалося завантажити типи нарядів з бекенду.')
    }

  } catch (error) {
    console.error('Помилка завантаження даних:', error)
  }
}

const submitForm = async () => {
  loading.value = true
  errors.value = {}

  try {
    await axios.post('http://localhost:8080/api/roster', form.value)
    router.push({name: 'duty-index'})
  } catch (error) {
    if (error.response && error.response.status === 422) {
      errors.value = error.response.data.errors
    } else {
      alert('Сталася помилка при збереженні')
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
      <h2>➕ Призначення в наряд</h2>
    </div>

    <div class="form-card">
      <form @submit.prevent="submitForm">

        <div class="form-group">
          <label>Боєць</label>
          <select v-model="form.soldier_id" :class="{ 'error-input': errors.soldier_id }">
            <option value="" disabled>Оберіть бійця</option>
            <option v-for="s in soldiers" :key="s.id" :value="s.id">
              {{ s.full_name }} ({{ s.rank }})
            </option>
          </select>
          <span v-if="errors.soldier_id" class="error-text">{{ errors.soldier_id[0] }}</span>
        </div>

        <div class="form-group">
          <label>Вид наряду</label>
          <select v-model="form.duty_type_id" :class="{ 'error-input': errors.duty_type_id }">
            <option value="" disabled>Оберіть вид наряду</option>
            <option v-for="t in dutyTypes" :key="t.id" :value="t.id">
              {{ t.name }}
            </option>
          </select>
          <span v-if="errors.duty_type_id" class="error-text">{{ errors.duty_type_id[0] }}</span>
        </div>

        <div class="form-group">
          <label>Початок заступання</label>
          <input
              type="datetime-local"
              v-model="form.start_time"
              :class="{ 'error-input': errors.start_time }"
          >
          <span v-if="errors.start_time" class="error-text">{{ errors.start_time[0] }}</span>
        </div>

        <div class="form-group">
          <label>Кінець наряду</label>
          <input
              type="datetime-local"
              v-model="form.end_time"
              :class="{ 'error-input': errors.end_time }"
          >
          <span v-if="errors.end_time" class="error-text">{{ errors.end_time[0] }}</span>
        </div>

        <button type="submit" class="save-btn" :disabled="loading">
          {{ loading ? 'Збереження...' : 'Призначити' }}
        </button>

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

.form-group input, .form-group select {
  width: 100%;
  padding: 12px;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  font-size: 1rem;
  background-color: #f8fafc;
}

.form-group input:focus, .form-group select:focus {
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