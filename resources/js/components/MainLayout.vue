<template>
  <div class="flex h-screen bg-gray-50 font-sans">
    <aside class="w-64 bg-[#1E293B] text-white flex flex-col justify-between">
      <div>
        <div class="p-6">
          <h1 class="text-xl font-bold">Task<span class="text-blue-400">Tracker</span></h1>
        </div>
        <nav class="mt-2 space-y-1 px-3">
          <router-link :to="{ name: 'dashboard' }" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition" active-class="bg-gray-800/50 text-white" exact-active-class="bg-gray-800/50 text-white">
            <span class="mr-3">📊</span> Dashboard
          </router-link>
          <router-link :to="{ name: 'projects' }" class="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium text-gray-400 hover:text-white hover:bg-gray-800 transition" active-class="bg-gray-800/50 text-white">
            <span class="mr-3">📁</span> Project
          </router-link>
          <router-link to="/" class="flex items-center px-3 py-2.5 hover:bg-gray-800 rounded-lg text-sm font-medium text-gray-300 transition">
            <span class="mr-3">✅</span> Task
          </router-link>
        </nav>
      </div>

      <div class="p-4 border-t border-gray-700">
        <button @click="handleLogout" class="flex items-center w-full px-3 py-2 mb-4 text-sm font-medium text-gray-400 hover:text-white transition">
          <span class="mr-3">🚪</span> Keluar
        </button>
        <div class="flex items-center">
          <div class="w-8 h-8 rounded-full bg-purple-600 flex items-center justify-center text-sm font-bold">
            {{ authStore.user?.name?.charAt(0) || 'U' }}
          </div>
          <div class="ml-3">
            <p class="text-sm font-medium text-white">{{ authStore.user?.name || 'User' }}</p>
            <p class="text-xs text-gray-400">{{ authStore.user?.is_admin ? 'Administrator' : 'Member' }}</p>
          </div>
        </div>
      </div>
    </aside>

    <main class="flex-1 overflow-y-auto p-8">
      <router-view></router-view>
    </main>
  </div>
</template>

<script setup lang="ts">
import { useAuthStore } from '@/stores/authStore';
import { useRouter } from 'vue-router';

const authStore = useAuthStore();
const router = useRouter();

const handleLogout = async () => {
    await authStore.logout();
    router.push({ name: 'login' });
};
</script>
