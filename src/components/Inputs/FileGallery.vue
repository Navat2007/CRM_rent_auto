<script setup>
import {computed, reactive, ref, unref, onMounted} from "vue";

const props = defineProps({
  sending: {
    type: Boolean,
    required: false,
    default: false
  },
  items: {
    type: Array,
    required: false
  }
});
const emit = defineEmits(['onSubmit', 'onDelete']);

const state = reactive({
  items: props.items || []
});
</script>

<template>

</template>

<style scoped>

</style>