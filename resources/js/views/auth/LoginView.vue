<template>
  <div class="min-h-screen bg-[#111827] flex items-center justify-center p-4">
    <div class="bg-white rounded-lg p-8 w-full max-w-md shadow-lg">
      <h1 class="text-xl font-bold text-gray-900 mb-1">
        Task<span class="text-blue-500">Tracker</span>
      </h1>
      <p class="text-sm text-gray-500 mb-6">Masuk ke akun kamu</p>

      <form @submit.prevent="handleLogin">
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
          <input
            v-model="form.email"
            type="email"
            class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            :class="{'border-red-500': errors.email}"
            placeholder="admin@energeek.id"
          />
          <span v-if="errors.email" class="text-xs text-red-500 mt-1 block">{{ errors.email[0] }}</span>
        </div>

        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
          <input
            v-model="form.password"
            type="password"
            class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            :class="{'border-red-500': errors.password}"
            placeholder="••••••••"
          />
          <span v-if="errors.password" class="text-xs text-red-500 mt-1 block">{{ errors.password[0] }}</span>
        </div>

        <button
          type="submit"
          :disabled="isLoading"
          class="w-full bg-blue-500 text-white font-medium py-2 px-4 rounded-md hover:bg-blue-600 transition disabled:opacity-50"
        >
          {{ isLoading ? 'Loading...' : 'Masuk' }}
        </button>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';

const form = ref({ email: '', password: '' });
const errors = ref<Record<string, string[]>>({});
const isLoading = ref(false);

const router = useRouter();
const authStore = useAuthStore();

const handleLogin = async () => {
    isLoading.value = true;
    errors.value = {}; // Reset errors

    try {
        await authStore.login(form.value);
        router.push({ name: 'dashboard' }); // Redirect after success
    } catch (error: any) {
        if (error.response && error.response.status === 422) {
            // Map Laravel's validation errors directly to the form fields
            errors.value = error.response.data.errors;
        } else {
            alert('Terjadi kesalahan pada server. Silakan coba lagi.');
        }
    } finally {
        isLoading.value = false;
    }
};
</script>
