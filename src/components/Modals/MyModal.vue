<script setup>
const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  }
})
const emit = defineEmits(['close'])
</script>

<template>
  <Teleport to="#modal">
    <transition
        enter-active-class="transition ease-out duration-200 transform"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition ease-in duration-200 transform"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0">
      <div v-if="isOpen"
           class="fixed z-999999 inset-0 bg-black bg-opacity-25 backdrop-blur-sm z-10 w-screen h-screen flex justify-center items-center">
        <transition
            enter-active-class="transition ease-out duration-300 transform "
            enter-from-class="opacity-0 translate-y-10 scale-95"
            enter-to-class="opacity-100 translate-y-0 scale-100"
            leave-active-class="ease-in duration-200"
            leave-from-class="opacity-100 translate-y-0 scale-100"
            leave-to-class="opacity-0 translate-y-10 translate-y-0 scale-95"
        >
          <div class="relative overflow-hidden bg-white px-8 border rounded-lg shadow-xl w-1/3 h-1/3 min-w-72 min-h-72">
            <button class="absolute top-4 right-4" @click="$emit('close')">x</button>
            <slot/>
          </div>
        </transition>
      </div>
    </transition>
  </Teleport>
</template>