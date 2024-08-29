<script setup>
const props = defineProps({
  errors: {
    type: Array,
    required: true,
  },
})
</script>

<template>
  <p v-for="error of errors" :key="error.$uid" class="text-red-500">
    {{ error.$message }}
  </p>
</template>

<style scoped>

</style>