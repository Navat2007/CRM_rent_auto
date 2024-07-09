<script setup>
import {ref, computed, onUpdated} from "vue";

const props = defineProps({
  itemsCount: {
    type: Number,
    required: true
  },
  pageCount: {
    type: Number,
    required: true
  },
  pageIndex: {
    type: Number,
    required: true,
    default: 1
  },
  perPage: {
    type: Number,
    required: true,
  }
})
const emit = defineEmits(['changePage']);
const pages = computed(() => {
  let array = [];

  for (let i = 1; i <= props.pageCount; i++) {
    array.push(i);
  }

  return array;
})

const handlePageSelect = (pageIndex) => {
  emit('changePage', pageIndex);
};
</script>

<template>
  <nav class="container" aria-label="Навигация таблицы">
    <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
        Показано
        <span class="font-semibold text-gray-900 dark:text-white">
          {{ (pageIndex - 1) * props.perPage + 1 }}-{{ Math.min((pageIndex - 1) * props.perPage + props.perPage, itemsCount) }}
        </span>
        из
        <span class="font-semibold text-gray-900 dark:text-white">{{ itemsCount }}</span>
    </span>
    <ul v-if="pages.length > 1" class="inline-flex items-stretch -space-x-px">
      <li>
        <button
            v-if="pageIndex > 1"
            @click="handlePageSelect(pageIndex - 1)"
            class="flex items-center justify-center h-full py-1.5 px-3 ml-0 text-gray-500 bg-white rounded-l-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white"
        >
          <span class="sr-only">Назад</span>
          <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewbox="0 0 20 20"
               xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd"
                  d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                  clip-rule="evenodd"/>
          </svg>
        </button>
      </li>
      <li
          v-for="(item, index) in pages"
          @click="handlePageSelect(item)"
          :key="index"
          class="item-button"
          :class="{'active': item === pageIndex, 'hide': index !== 0 && index !== pageCount - 1 && (index < pageIndex - 2 || index > pageIndex)}"
      >
        {{ item }}
      </li>
      <li>
        <button
            v-if="pageIndex < pageCount"
            @click="handlePageSelect(pageIndex + 1)"
            class="flex items-center justify-center h-full py-1.5 px-3 leading-tight text-gray-500 bg-white rounded-r-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white"
        >
          <span class="sr-only">Вперед</span>
          <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewbox="0 0 20 20"
               xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd"
                  d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                  clip-rule="evenodd"/>
          </svg>
        </button>
      </li>
    </ul>
  </nav>
</template>

<style scoped lang="scss">
.container {
  @apply flex flex-col md:flex-row justify-between items-start md:items-center space-y-3 md:space-y-0 p-4;
}

.item-button {
  @apply flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white;
}

.active {
  @apply text-primary-600 bg-primary-50 border border-primary-300 hover:bg-primary-100 hover:text-primary-700 dark:border-gray-700 dark:bg-gray-700 dark:text-white;
}

.hide {
  display: none;
}
</style>