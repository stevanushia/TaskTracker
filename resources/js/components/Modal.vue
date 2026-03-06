<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-black/50 p-4">
    <div class="relative w-full max-w-md rounded-xl bg-white shadow-lg">
      <div class="flex items-center justify-between border-b p-4">
        <h3 class="text-lg font-bold text-gray-900">{{ title }}</h3>
        <button @click="$emit('close')" class="text-gray-400 hover:text-gray-900">
          ✕
        </button>
      </div>
      <div class="p-4">
        <slot></slot>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps({
  isOpen: { type: Boolean, required: true },
  title: { type: String, required: true }
});
defineEmits(['close']);
</script>
