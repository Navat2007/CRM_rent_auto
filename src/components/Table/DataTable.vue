<script setup>
import {computed, ref, onMounted} from "vue";
import lodash from "lodash";
import SearchForm from "../SearchForm.vue";
import TablePagination from "./TablePagination.vue";

const props = defineProps({
  title: {
    type: String,
    required: false
  },
  pageSize: {
    type: Number,
    required: false,
    default: 15
  },
  columns: {
    type: Array,
    required: true
  },
  items: {
    type: Array,
    required: true
  }
})
const emit = defineEmits(['onRowClick']);

const searchFilter = ref('');
const order = ref('ASC');
const sortKey = ref('');
const startIndex = ref(0);
const pageIndex = ref(1);

const filteredItems = computed(() => {
  let result = props.items;

  if (searchFilter.value !== '') {
    if (props.items.length > 0) {
      console.log(searchFilter.value);

      props.columns.map(column => {
        switch (column.type) {
          case 'string':
            console.log(column.field);
            console.log(result);
            result = result.filter(item => {
              console.log(item[column.field]);
              return item[column.field].toLowerCase().includes(searchFilter.value.toLowerCase());
            });
            console.log(result);
            break;
          case 'number':
            console.log(column.field);
            console.log(result);
            result = result.filter(item => item[column.field].toString().toLowerCase().includes(searchFilter.value.toLowerCase()));
            console.log(result);
            break;
        }
      })
    }
  }

  return result;
})
const sortedItems = computed(() => {
  return [];
});
const pageCount = computed(() => {
  return Math.ceil(filteredItems.value.length / props.pageSize);
});
const paginatedItems = computed(() => {
  if (sortedItems.value.length > 0 || filteredItems.value.length > 0) {
    return lodash(sortedItems.value.length > 0 ? sortedItems.value : filteredItems.value)
        .slice(startIndex.value)
        .take(props.pageSize)
        .value();
  }

  return [];
});

const handleSearch = (filter) => {
  searchFilter.value = filter
  pageIndex.value = 1
}
const handleChangePage = (index) => {
  startIndex.value = (index - 1) * props.pageSize;
  pageIndex.value = index;
};
</script>

<template>
  <section class="bg-gray-100 dark:bg-gray-900 p-3 sm:p-5">
    <div class="mx-auto max-w-screen-xl px-4 lg:px-12">
      <span class="text-2xl text-gray-900 dark:text-amber-200">
        {{ title }}
      </span>
      <div class="mt-2 bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
          <div class="w-full md:w-1/2">
            <SearchForm @search="handleSearch" :columns="columns"/>
          </div>
          <div class="buttons-container">
            <slot/>
          </div>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-200 dark:bg-gray-700 dark:text-gray-400">
            <tr>
              <th
                  v-for="column in columns"
                  :key="column.field"
                  scope="col"
                  class="px-4 py-3"
              >
                {{ column.label }}
              </th>
            </tr>
            </thead>
            <tbody>
            <tr
                v-for="item in paginatedItems" :key="item.ID"
                @click="emit('onRowClick', item)"
                class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
            >
              <td
                  v-for="column in columns"
                  :key="column.field"
                  class="px-4 py-3"
              >
                {{ item[column.field] }}
              </td>
            </tr>
            </tbody>
          </table>
        </div>
        <TablePagination
            v-if="filteredItems.length > 0"
            :items-count="filteredItems.length"
            :page-count="pageCount"
            :page-index="pageIndex"
            :per-page="pageSize"
            @change-page="handleChangePage"
        />
      </div>
    </div>
  </section>
</template>

<style scoped>
.buttons-container {
  @apply flex flex-col flex-shrink-0 space-y-3 md:flex-row md:items-center justify-end md:space-y-0 md:space-x-3 w-full md:w-1/2;
}
</style>