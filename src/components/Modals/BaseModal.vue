<script setup>
import {onClickOutside} from '@vueuse/core'
import {ref} from "vue";

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false
    },
    closeOnClickOutside: {
        type: Boolean,
        default: true
    },
    title: {
        type: String,
        required: false
    },
    withFooter: {
        type: Boolean,
        required: false
    },
    toModal: {
        type: String,
        required: false,
        default: "#modal"
    }
})
const emit = defineEmits(['close'])
const target = ref(null)

onClickOutside(target, () => {
    if(props.closeOnClickOutside)
        emit('close');
})
</script>

<template>
    <Teleport :to="toModal">
        <transition
            enter-active-class="transition ease-out duration-200 transform"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition ease-in duration-200 transform"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0">
            <div
                v-if="isOpen"
                class="background"
            >
                <div
                    v-if="isOpen" tabindex="-1"
                    class="modal"
                >
                    <div class="relative p-4 w-full max-h-full">
                        <!-- Modal content -->
                        <div ref="target" class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                            <!-- Modal header -->
                            <div
                                class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                    {{ title }}
                                </h3>
                                <button
                                    @click="$emit('close')"
                                    type="button"
                                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                >
                                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                         fill="none"
                                         viewBox="0 0 14 14">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                              stroke-width="2"
                                              d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                    </svg>
                                    <span class="sr-only">Закрыть окно</span>
                                </button>
                            </div>
                            <!-- Modal body -->
                            <div class="p-4 md:p-5 space-y-4">
                                <slot/>
                            </div>
                            <!-- Modal footer -->
                            <div v-if="withFooter"
                                 class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                                <button type="button"
                                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                    I accept
                                </button>
                                <button type="button"
                                        class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                                    Decline
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
    </Teleport>
</template>

<style scoped>
.background {
    @apply fixed z-9999 inset-0 bg-black bg-opacity-25 backdrop-blur-sm w-screen h-screen flex justify-center items-center;
}

.modal {
    @apply overflow-y-auto overflow-x-hidden min-w-72 min-h-72 md:w-10/12 lg:w-8/12 xl:w-6/12;
}
</style>