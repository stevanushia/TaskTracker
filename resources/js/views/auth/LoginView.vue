<template>
  <div class="min-h-screen bg-[#111827] flex items-center justify-center p-4">
    <div class="bg-white rounded-lg p-8 w-full max-w-md shadow-lg">
      <h1 class="text-xl font-bold text-gray-900 mb-1">
        Task<span class="text-blue-500">Tracker</span>
      </h1>
      <p class="text-sm text-gray-500 mb-6">Masuk ke akun kamu</p>

      <form @submit.prevent="handleLogin">
        <div class="mb-4">
          <Label>Email</Label>
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
          <Label>Password</Label>
          <input
            v-model="form.password"
            type="password"
            class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            :class="{'border-red-500': errors.password}"
            placeholder="••••••••"
          />
          <span v-if="errors.password" class="text-xs text-red-500 mt-1 block">{{ errors.password[0] }}</span>
        </div>

        <Button type="submit" :isLoading="isLoading">Masuk</Button>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';
import Label from '@/components/Label.vue';
import Button from '@/components/Button.vue';

// ... (logic remains exactly the same as the previous response)
const form = ref({ email: '', password: '' });
const errors = ref<Record<string, string[]>>({});
const isLoading = ref(false);

const router = useRouter();
const authStore = useAuthStore();

const handleLogin = async () => {
    isLoading.value = true;
    errors.value = {};

    try {
        await authStore.login(form.value);
        router.push({ name: 'dashboard' });
    } catch (error: any) {
        if (error.response && error.response.status === 422) {
            errors.value = error.response.data.errors;
        } else {
            alert('Terjadi kesalahan pada server. Silakan coba lagi.');
        }
    } finally {
        isLoading.value = false;
    }
};
</script>
