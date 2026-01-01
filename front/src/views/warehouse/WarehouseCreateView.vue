<script setup>
import {onMounted, ref} from 'vue'
import axios from 'axios'
import {useRouter} from 'vue-router'

const router = useRouter()
const loading = ref(false)
const types = ref([])
const errors = ref({})

const form = ref({
  serial_number: '',
  equipment_type_id: '',
  status: 'in_stock'
})
const formatSerial = () => {
  if (form.value.serial_number) {
    form.value.serial_number = form.value.serial_number.toLowerCase();
  }
}

const fetchTypes = async () => {
  try {
    const response = await axios.get('http://localhost:8080/api/warehouse')
    if (response.data.meta_data && response.data.meta_data.types) {
      types.value = response.data.meta_data.types
    }
  } catch (error) {
    console.error('Помилка завантаження типів:', error)
  }
}

const submitForm = async () => {
  loading.value = true
  errors.value = {}

  try {
    await axios.post('http://localhost:8080/api/warehouse', form.value)

    console.log('Успішно збережено, редіректимо...');

    await router.push({name: 'warehouses.index'})

  } catch (error) {
    if (error.response && error.response.status === 422) {
      errors.value = error.response.data.errors
    } else {
      console.error('Помилка збереження:', error)
    }
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchTypes()
})
</script>

<template>
  <div class="page-container">
    <div class="header-row">
      <div class="title-block">
        <button class="back-btn" @click="router.back()">← Назад</button>
        <h2>➕ Додати нове майно</h2>
      </div>
    </div>

    <div class="form-card">
      <form @submit.prevent="submitForm">

        <div class="form-group">
          <label>Серійний номер</label>
          <input
              v-model="form.serial_number"
              @input="formatSerial"
              type="text"
              placeholder="Наприклад: nd-22320"
              :class="{ 'error-input': errors.serial_number }"
              maxlength="8"
          >
          <small style="color: #6b7280; display: block; margin-top: 4px;">Формат: xx-00000</small>

          <span v-if="errors.serial_number" class="error-text">{{ errors.serial_number[0] }}</span>
        </div>

        <div class="form-group">
          <label>Тип / Модель</label>
          <select
              v-model="form.equipment_type_id"
              :class="{ 'error-input': errors.equipment_type_id }"
          >
            <option value="" disabled>Оберіть тип обладнання</option>
            <option v-for="type in types" :key="type.id" :value="type.id">
              {{ type.name }}
            </option>
          </select>
          <span v-if="errors.equipment_type_id" class="error-text">{{ errors.equipment_type_id[0] }}</span>
        </div>

        <div class="form-group">
          <label>Початковий статус</label>
          <select v-model="form.status">
            <option value="in_stock">🟢 На складі</option>
            <option value="issued">🔵 Видано</option>
            <option value="broken">🔴 Ремонт</option>
          </select>
        </div>

        <div class="form-actions">
          <button type="submit" class="save-btn" :disabled="loading">
            {{ loading ? 'Збереження...' : 'Зберегти майно' }}
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
  margin-bottom: 25px;
}

.title-block {
  display: flex;
  align-items: center;
  gap: 15px;
}

.back-btn {
  background: none;
  border: none;
  cursor: pointer;
  font-size: 1.1rem;
  color: #666;
}

.back-btn:hover {
  color: #000;
}

.form-card {
  background: white;
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
  max-width: 600px;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: 500;
  color: #374151;
}

.form-group input, .form-group select {
  width: 100%;
  padding: 10px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  font-size: 1rem;
}

.form-group input:focus, .form-group select:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.error-input {
  border-color: #ef4444 !important;
}

.error-text {
  color: #ef4444;
  font-size: 0.85rem;
  margin-top: 5px;
  display: block;
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
  transition: background 0.2s;
}

.save-btn:hover {
  background: #2563eb;
}

.save-btn:disabled {
  background: #9ca3af;
  cursor: not-allowed;
}
</style>