<script setup>
const props = defineProps({
  id: {
    type: String,
    required: true
  },
  title: {
    type: String,
    required: true
  },
  model: {
    type: Object,
    required: true
  },
})
const emit = defineEmits(['onChange']);
</script>

<template>
  <div class="flex items-center">
    <input :id="id" type="checkbox"
           v-model="model.value" :ref="model.ref"
           @change="emit('onChange', model.value)"
           class="input"
    />
    <label :for="id" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
      {{ title }}
    </label>
  </div>
</template>

<style scoped>
.input {
  @apply w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600;
}
</style>