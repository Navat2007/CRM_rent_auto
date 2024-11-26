<script setup>
import { onClickOutside } from '@vueuse/core'
import {ref} from "vue";

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    required: false
  },
  withButtons: {
    type: Boolean,
    required: false
  },
  accept: {
    type: Boolean,
    required: false
  },
  info: {
    type: Boolean,
    required: false,
  },
  target: {
    type: String,
    required: false,
    default: '#modal'
  },
  invertButtons: {
    type: Boolean,
    required: false,
  },
  clickOutsideClose: {
    type: Boolean,
    required: false,
    default: true
  }
});
const emit = defineEmits(['close', 'accept'])
const target = ref(null)

onClickOutside(target, () => {
  if(props.clickOutsideClose)
    emit('close');
})
</script>

<template>
  <Teleport to="#modal2">
    <transition
        enter-active-class="transition ease-out duration-200 transform"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition ease-in duration-200 transform"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0">
      <div
          v-if="isOpen"
          class="fixed z-999999 inset-0 bg-black bg-opacity-25 backdrop-blur-sm w-screen h-screen flex justify-center items-center"
      >
        <div
            v-if="isOpen" tabindex="-1" aria-hidden="true"
            class="overflow-y-auto overflow-x-hidden min-w-96 min-h-72 lg:w-5/12"
        >
          <div class="relative p-4 w-full max-h-full">
            <!-- Modal content -->
            <div ref="target" class="relative bg-white rounded-lg shadow dark:bg-gray-700">
              <button @click="$emit('close')" type="button"
                      class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                      data-modal-hide="popup-modal">
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                     viewBox="0 0 14 14">
                  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
                <span class="sr-only">Close modal</span>
              </button>
              <div class="p-4 md:p-5 text-center">
                <svg v-if="accept" class="mx-auto mb-4 w-12 h-12 text-green-700 dark:text-green-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                  <path fill-rule="evenodd" d="M15.03 9.684h3.965c.322 0 .64.08.925.232.286.153.532.374.717.645a2.109 2.109 0 0 1 .242 1.883l-2.36 7.201c-.288.814-.48 1.355-1.884 1.355-2.072 0-4.276-.677-6.157-1.256-.472-.145-.924-.284-1.348-.404h-.115V9.478a25.485 25.485 0 0 0 4.238-5.514 1.8 1.8 0 0 1 .901-.83 1.74 1.74 0 0 1 1.21-.048c.396.13.736.397.96.757.225.36.32.788.269 1.211l-1.562 4.63ZM4.177 10H7v8a2 2 0 1 1-4 0v-6.823C3 10.527 3.527 10 4.176 10Z" clip-rule="evenodd"/>
                </svg>
                <svg v-if="info" class="mx-auto mb-4 text-red-600 w-12 h-12 dark:text-red-500" aria-hidden="true"
                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
                <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">{{ title }}</h3>
                <!-- Your modal content here -->
                <slot name="body"/>
                <div v-if="$slots.buttons" class="flex flex-col gap-4 mt-4">
                  <slot name="buttons"/>
                </div>
                <div v-if="withButtons && !invertButtons">
                  <button
                      @click="$emit('accept')"
                      data-modal-hide="popup-modal" type="button"
                      class="text-white bg-primary-600 hover:bg-primary-500 focus:ring-4 focus:outline-none focus:ring-primary-300 dark:focus:ring-primary-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center"
                  >
                    Да
                  </button>
                  <button
                      @click="$emit('close')"
                      data-modal-hide="popup-modal" type="button"
                      class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                  >
                    Нет
                  </button>
                </div>
                <div v-if="withButtons && invertButtons">
                  <button
                      @click="$emit('close')"
                      data-modal-hide="popup-modal" type="button"
                      class="py-2.5 px-5  text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                  >
                    Нет
                  </button>
                  <button
                      @click="$emit('accept')"
                      data-modal-hide="popup-modal" type="button"
                      class="ms-3 text-white bg-primary-600 hover:bg-primary-500 focus:ring-4 focus:outline-none focus:ring-primary-300 dark:focus:ring-primary-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center"
                  >
                    Да
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </Teleport>
</template>