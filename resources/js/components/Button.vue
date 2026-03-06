<template>
  <button
    :type="type"
    :disabled="isLoading || disabled"
    class="w-full bg-blue-500 text-white font-medium py-2 px-4 rounded-md hover:bg-blue-600 transition disabled:opacity-50"
  >
    <slot v-if="!isLoading"></slot>
    <span v-else>Loading...</span>
  </button>
</template>

<script setup lang="ts">
defineProps({
  type: { type: String as () => 'button' | 'submit' | 'reset', default: 'button' },
  isLoading: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false }
});
</script>
