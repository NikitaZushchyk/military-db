<script setup>
import {onMounted, ref} from 'vue'
import axios from 'axios'
import {useRoute, useRouter} from 'vue-router'

const route = useRoute()
const router = useRouter()

const warehouseId = route.params.warehouse

const loading = ref(true)
const saving = ref(false)
const types = ref([])
const errors = ref({})

const form = ref({
  serial_number: '',
  equipment_type_id: '',
  status: ''
})

const fetchTypes = async () => {
  try {
    const response = await axios.get('http://localhost:8080/api/warehouse')
    if (response.data.meta_data && response.data.meta_data.types) {
      types.value = response.data.meta_data.types
    }
  } catch (error) {
    console.error('Не вдалося завантажити типи:', error)
  }
}

const fetchWarehouse = async () => {
  loading.value = true
  try {
    const response = await axios.get(`http://localhost:8080/api/warehouse/${warehouseId}`)
    const data = response.data.data

    form.value = {
      serial_number: data.serial_number,
      equipment_type_id: data.equipment_type_id,
      status: data.status
    }
  } catch (error) {
    console.error('Помилка завантаження майна:', error)
    alert('Не вдалося знайти цей запис')
    router.push({name: 'warehouses.index'})
  } finally {
    loading.value = false
  }
}

const formatSerial = () => {
  if (form.value.serial_number) {
    form.value.serial_number = form.value.serial_number.toLowerCase()
  }
}

const updateWarehouse = async () => {
  saving.value = true
  errors.value = {}

  try {
    await axios.put(`http://localhost:8080/api/warehouse/${warehouseId}`, form.value)
    router.push({name: 'warehouses.index'})
  } catch (error) {
    if (error.response && error.response.status === 422) {
      errors.value = error.response.data.errors
    } else {
      alert('Сталася невідома помилка при збереженні')
      console.error(error)
    }
  } finally {
    saving.value = false
  }
}

const deleteWarehouse = async () => {
  if (!confirm('Ви точно хочете списати/видалити цей предмет? Ця дія незворотна.')) return

  try {
    await axios.delete(`http://localhost:8080/api/warehouse/${warehouseId}`)
    router.push({name: 'warehouses.index'})
  } catch (error) {
    alert('Помилка видалення')
    console.error(error)
  }
}

onMounted(async () => {
  await fetchTypes()
  await fetchWarehouse()
})
</script>

<template>
  <div class="page-container">
    <div class="header-row">
      <div class="title-block">
        <button class="back-btn" @click="router.push({ name: 'warehouses.index' })">← Назад</button>
        <h2>✏️ Редагування майна #{{ warehouseId }}</h2>
      </div>
      <button class="delete-btn" @click="deleteWarehouse" :disabled="loading">
        🗑 Видалити
      </button>
    </div>

    <div v-if="loading" class="loading-state">
      <div class="spinner"></div>
      <span>Завантаження даних...</span>
    </div>

    <div v-else class="form-card">
      <form @submit.prevent="updateWarehouse">

        <div class="form-group">
          <label>Серійний номер <small>(нижній регістр)</small></label>
          <input
              v-model="form.serial_number"
              @input="formatSerial"
              type="text"
              placeholder="xx-00000"
              :class="{ 'error-input': errors.serial_number }"
              maxlength="8"
          >
          <span v-if="errors.serial_number" class="error-text">{{ errors.serial_number[0] }}</span>
        </div>

        <div class="form-group">
          <label>Тип / Модель</label>
          <select
              v-model="form.equipment_type_id"
              :class="{ 'error-input': errors.equipment_type_id }"
          >
            <option value="" disabled>Оберіть тип</option>
            <option v-for="type in types" :key="type.id" :value="type.id">
              {{ type.name }}
            </option>
          </select>
          <span v-if="errors.equipment_type_id" class="error-text">{{ errors.equipment_type_id[0] }}</span>
        </div>

        <div class="form-group">
          <label>Поточний статус</label>
          <select
              v-model="form.status"
              :class="['status-select', form.status]"
          >
            <option value="in_stock">🟢 На складі</option>
            <option value="issued">🔵 Видано</option>
            <option value="broken">🟠 Ремонт (Broken)</option>
          </select>
          <span v-if="errors.status" class="error-text">{{ errors.status[0] }}</span>
        </div>

        <div class="form-actions">
          <button type="submit" class="save-btn" :disabled="saving">
            {{ saving ? 'Збереження...' : 'Зберегти зміни' }}
          </button>
        </div>

      </form>
    </div>
  </div>
</template>

<style scoped>
.page-container {
  padding: 20px;
  max-width: 800px;
  margin: 0 auto;
}

.header-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 25px;
}

.title-block {
  display: flex;
  align-items: center;
  gap: 15px;
}

h2 {
  font-size: 1.5rem;
  color: #1e293b;
  margin: 0;
}

.back-btn {
  background: none;
  border: none;
  cursor: pointer;
  font-size: 1rem;
  color: #64748b;
  font-weight: 500;
}

.back-btn:hover {
  color: #0f172a;
  text-decoration: underline;
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
  font-size: 0.95rem;
}

.form-group label small {
  color: #94a3b8;
  font-weight: 400;
}

.form-group input, .form-group select {
  width: 100%;
  padding: 12px;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.2s;
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

.status-select.in_stock {
  border-left: 5px solid #22c55e;
}

.status-select.issued {
  border-left: 5px solid #3b82f6;
}

.status-select.broken {
  border-left: 5px solid #f59e0b;
}

.form-actions {
  margin-top: 30px;
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
  font-size: 1rem;
  transition: background 0.2s;
}

.save-btn:hover {
  background: #2563eb;
}

.save-btn:disabled {
  background: #94a3b8;
  cursor: not-allowed;
}

.delete-btn {
  background: #fee2e2;
  color: #dc2626;
  border: none;
  padding: 10px 16px;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

.delete-btn:hover {
  background: #fecaca;
}

.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 50px;
  color: #64748b;
}

.spinner {
  width: 30px;
  height: 30px;
  border: 3px solid #e2e8f0;
  border-top-color: #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 10px;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
</style>