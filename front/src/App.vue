<script setup>
import { RouterLink, RouterView } from 'vue-router'
</script>

<template>
  <div class="app-container">
    <aside class="sidebar">
      <div class="logo">
        🛡️ Military DB
      </div>

      <nav class="menu">
        <RouterLink to="/" class="menu-item">📊 Статистика</RouterLink>
        <RouterLink to="/soldiers" class="menu-item">🪖 Особовий склад</RouterLink>
        <RouterLink to="/warehouse" class="menu-item">📦 Склад зброї</RouterLink>
        <RouterLink to="/assignments" class="menu-item">📝 Журнал видач</RouterLink>
        <RouterLink to="/duty" class="menu-item">📅 Наряди</RouterLink>
        <RouterLink to="/logs" class="menu-item">⚠️ Логи</RouterLink>
      </nav>

      <div class="user-info">
        <p>Користувач: Admin</p>
        <button class="logout-btn">Вийти</button>
      </div>
    </aside>

    <main class="content">
      <header class="top-bar">
        <h2>Система обліку</h2>
      </header>

      <div class="page-view">
        <RouterView />
      </div>
    </main>
  </div>
</template>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

body {
  background-color: #f4f6f8;
  color: #333;
}

.app-container {
  display: flex;
  height: 100vh;
  width: 100vw;
}

.sidebar {
  width: 260px;
  background-color: #1e293b;
  color: white;
  display: flex;
  flex-direction: column;
  flex-shrink: 0;
}

.logo {
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  font-weight: bold;
  border-bottom: 1px solid #334155;
  background-color: #0f172a;
}

.menu {
  flex: 1;
  padding: 20px 0;
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.menu-item {
  color: #cbd5e1;
  text-decoration: none;
  padding: 12px 20px;
  font-size: 16px;
  transition: 0.2s;
  border-left: 4px solid transparent;
}

.menu-item:hover {
  background-color: #334155;
  color: white;
}

.router-link-active {
  background-color: #334155;
  color: #4ade80;
  border-left: 4px solid #4ade80;
}

.user-info {
  padding: 20px;
  border-top: 1px solid #334155;
  background-color: #0f172a;
  text-align: center;
}

.logout-btn {
  margin-top: 10px;
  background-color: #ef4444;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 4px;
  cursor: pointer;
  width: 100%;
}

.logout-btn:hover {
  background-color: #dc2626;
}

.content {
  flex: 1;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.top-bar {
  height: 60px;
  background-color: white;
  display: flex;
  align-items: center;
  padding: 0 30px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.page-view {
  flex: 1;
  padding: 30px;
  overflow-y: auto;
}
</style>
