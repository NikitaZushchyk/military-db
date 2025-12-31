<script setup>
import {onMounted, ref} from 'vue'
import axios from 'axios'
import {useRouter} from 'vue-router'

const router = useRouter()
const warehouseItems = ref([])
const loading = ref(true)
const pagination = ref({})

const types = ref([])

const filters = ref({
  search: '',
  status: '',
  type_id: ''
})

const fetchWarehouse = async (page = 1) => {
  loading.value = true
  try {
    const params = {page, ...filters.value}
    Object.keys(params).forEach(key => (params[key] === '' || params[key] == null) && delete params[key]);

    const response = await axios.get('http://localhost:8080/api/warehouse', {params})

    warehouseItems.value = response.data.data

    if (response.data.meta) pagination.value = response.data.meta

    if (response.data.meta_data && response.data.meta_data.types) {
      types.value = response.data.meta_data.types
    }

  } catch (error) {
    console.error('Помилка завантаження складу:', error)
  } finally {
    loading.value = false
  }
}

let timeout = null
const handleSearch = () => {
  clearTimeout(timeout)
  timeout = setTimeout(() => fetchWarehouse(1), 500)
}

const applyFilter = () => fetchWarehouse(1)

const goToDetail = (id) => {
  router.push({name: 'warehouses.update', params: {warehouse: id}})
}

const changePage = (link) => {
  if (!link.url || link.active) return;
  const url = new URL(link.url);
  fetchWarehouse(url.searchParams.get('page'));
}

const getStatusLabel = (status) => {
  const map = {
    'in_stock': 'На складі',
    'issued': 'Видано',
    'maintenance': 'Ремонт',
    'lost': 'Втрачено'
  }
  return map[status] || status
}

const getStatusClass = (status) => {
  const map = {
    'in_stock': 'active',
    'issued': 'info',
    'maintenance': 'warning',
    'lost': 'danger'
  }
  return map[status] || 'inactive'
}

onMounted(() => fetchWarehouse())
</script>

<template>
  <div class="page-container">
    <div class="header-row">
      <div class="title-block">
        <h2>📦 Склад майна</h2>
        <span class="count-badge" v-if="pagination.total">Всього: {{ pagination.total }}</span>
      </div>
      <button class="add-btn" @click="router.push({ name: 'warehouses.store' })">+ Додати майно</button>
    </div>

    <div class="filters-bar">
      <div class="search-group">
        <input
            v-model="filters.search"
            @input="handleSearch"
            type="text"
            placeholder="🔍 Пошук (серійний номер)..."
        >
      </div>

      <div class="filter-group">
        <select v-model="filters.type_id" @change="applyFilter">
          <option value="">Всі типи</option>
          <option v-for="type in types" :key="type.id" :value="type.id">{{ type.name }}</option>
        </select>

        <select v-model="filters.status" @change="applyFilter">
          <option value="">Всі статуси</option>
          <option value="in_stock">На складі</option>
          <option value="issued">Видано</option>
          <option value="maintenance">Ремонт</option>
          <option value="lost">Втрачено</option>
        </select>
      </div>
    </div>

    <div v-if="loading" class="loading-state">
      <div class="spinner"></div>
      <span>Завантаження складу...</span>
    </div>

    <div v-else class="table-wrapper">
      <table>
        <thead>
        <tr>
          <th>Серійний номер</th>
          <th>Тип / Модель</th>
          <th>Статус</th>
        </tr>
        </thead>
        <tbody>
        <tr
            v-for="item in warehouseItems"
            :key="item.id"
            @click="goToDetail(item.id)"
            class="clickable-row"
        >
          <td class="serial-cell">{{ item.serial_number }}</td>
          <td><span class="badge type">{{ item.equipment_type_name || '—' }}</span></td>
          <td>
            <span :class="['status-dot', getStatusClass(item.status)]"></span>
            {{ getStatusLabel(item.status) }}
          </td>
        </tr>
        </tbody>
      </table>
    </div>

    <div class="pagination-container" v-if="pagination.links && pagination.links.length > 3">
      <div class="pagination-links">
        <button
            v-for="(link, i) in pagination.links"
            :key="i"
            :disabled="!link.url"
            :class="{ 'active': link.active }"
            @click.stop="changePage(link)"
            v-html="link.label"
        ></button>
      </div>
    </div>
  </div>
</template>

<style scoped>

.page-container {
  padding-bottom: 40px;
}

.header-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.title-block {
  display: flex;
  align-items: center;
  gap: 12px;
}

h2 {
  color: #1e293b;
  font-size: 24px;
  font-weight: 700;
}

.count-badge {
  background: #f1f5f9;
  padding: 4px 10px;
  border-radius: 20px;
  font-size: 13px;
  color: #64748b;
  font-weight: 600;
  border: 1px solid #e2e8f0;
}

.add-btn {
  background: #3b82f6;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.2s;
  box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);
}

.add-btn:hover {
  background: #2563eb;
  transform: translateY(-1px);
}

.filters-bar {
  display: flex;
  gap: 15px;
  margin-bottom: 24px;
  background: white;
  padding: 16px;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
  border: 1px solid #f1f5f9;
  flex-wrap: wrap;
}

.search-group {
  flex: 1;
  min-width: 200px;
}

.search-group input {
  width: 100%;
  padding: 10px 14px;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  font-size: 14px;
  transition: 0.2s;
  background-color: #f8fafc;
}

.search-group input:focus {
  outline: none;
  border-color: #3b82f6;
  background-color: white;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.filter-group {
  display: flex;
  gap: 10px;
}

.filter-group select {
  padding: 10px 14px;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  background-color: white;
  cursor: pointer;
  color: #475569;
  font-size: 14px;
  transition: 0.2s;
}

.filter-group select:hover {
  border-color: #cbd5e1;
}

.filter-group select:focus {
  outline: none;
  border-color: #3b82f6;
}

.table-wrapper {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  border: 1px solid #f1f5f9;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th {
  background: #f8fafc;
  text-align: left;
  padding: 16px;
  font-weight: 600;
  color: #64748b;
  font-size: 13px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  border-bottom: 1px solid #e2e8f0;
}

td {
  padding: 16px;
  border-bottom: 1px solid #f1f5f9;
  color: #334155;
  vertical-align: middle;
}

.clickable-row {
  cursor: pointer;
  transition: background-color 0.1s;
}

.clickable-row:hover {
  background-color: #f8fafc;
}

.clickable-row:last-child td {
  border-bottom: none;
}

.serial-cell {
  font-weight: 600;
  color: #0f172a;
  font-size: 15px;
  font-family: monospace;
}

.badge {
  padding: 4px 10px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 600;
  display: inline-block;
}

.badge.type {
  background: #f3f4f6;
  color: #4b5563;
  border: 1px solid #e5e7eb;
}

.status-dot {
  display: inline-block;
  width: 8px;
  height: 8px;
  border-radius: 50%;
  margin-right: 8px;
}

.status-dot.active {
  background: #22c55e;
  box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.2);
}

.status-dot.info {
  background: #3b82f6;
  box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
}

.status-dot.warning {
  background: #eab308;
  box-shadow: 0 0 0 2px rgba(234, 179, 8, 0.2);
}

.status-dot.danger {
  background: #ef4444;
  box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.2);
}

.status-dot.inactive {
  background: #94a3b8;
}

.pagination-container {
  margin-top: 24px;
  display: flex;
  justify-content: center;
}

.pagination-links {
  display: flex;
  background: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #e2e8f0;
}

.pagination-links button {
  padding: 10px 16px;
  border: none;
  border-right: 1px solid #e2e8f0;
  background: white;
  color: #64748b;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
  transition: 0.2s;
}

.pagination-links button:last-child {
  border-right: none;
}

.pagination-links button:hover:not(:disabled) {
  background-color: #f8fafc;
  color: #334155;
}

.pagination-links button.active {
  background-color: #3b82f6;
  color: white;
  border-color: #3b82f6;
}

.pagination-links button:disabled {
  opacity: 0.5;
  cursor: default;
  background-color: #f9fafb;
}

.loading-state {
  padding: 40px;
  text-align: center;
  color: #64748b;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
}

.spinner {
  width: 30px;
  height: 30px;
  border: 3px solid #e2e8f0;
  border-top-color: #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
</style>