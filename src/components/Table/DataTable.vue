<script setup>
import DataTable from "primevue/datatable";
import { FilterMatchMode } from '@primevue/core/api';
import {ref} from "vue";

const props = defineProps({
  title: {
    type: String,
    required: false
  },
  pageSize: {
    type: Number,
    required: false,
    default: 10
  },
  items: {
    type: Array,
    required: true
  },
  columns: {
    type: Array,
    required: true
  },
  loading: {
    type: Boolean,
    required: false,
    default: false
  },
})
const emit = defineEmits(['onRowClick']);

const filters = ref();
const skeletonItems = ref(new Array(10));

const handleRowClick = (item) => {
  emit('onRowClick', item.data);
}
const clearFilter = () => {
  initFilters();
};
const initFilters = () => {
  filters.value = {
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
  };
};

initFilters();
</script>

<template>
  <Card>
    <template #title>{{ title }}</template>
    <template #content>
      <DataTable v-if="loading" :value="skeletonItems">
        <Column v-for="col of columns" :key="col.field" :field="col.field" :header="col.header">
          <template #body>
            <Skeleton></Skeleton>
          </template>
        </Column>
      </DataTable>
      <DataTable
          v-else
          :value="items" @row-click="handleRowClick"
          showGridlines stripedRows paginator :rows="pageSize" :rowsPerPageOptions="[5, 10, 20, 50]"
          sortField="id" :sortOrder="1" removableSort
          filterDisplay="row"
      >
        <template #header>
          <div class="flex justify-between">
            <Button type="button" icon="pi pi-filter-slash" label="Очистить фильтр" raised @click="clearFilter()" />
            <div class="flex space-x-3">
              <IconField>
                <InputIcon class="pi pi-search" />
                <InputText v-model="filters['global'].value" placeholder="Поиск" />
              </IconField>
              <slot name="buttons"/>
            </div>
          </div>
        </template>
        <template #empty> Результаты не найдены.</template>
        <slot name="columns"/>
      </DataTable>
    </template>
  </Card>
</template>

<style scoped>
.buttons-container {
  @apply flex flex-col flex-shrink-0 space-y-3 md:flex-row md:items-center justify-end md:space-y-0 md:space-x-3 w-full md:w-1/2;
}
</style>