<script setup>
import DataTable from "primevue/datatable";
import {FilterMatchMode} from '@primevue/core/api';
import {onMounted, ref} from "vue";

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

const table = ref();
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
    global: {value: null, matchMode: FilterMatchMode.CONTAINS},
  };
};
const exportCSV = () => {
  table.value.exportCSV();
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
          ref="table" :value="items" @row-click="handleRowClick"
          stateStorage="session" :stateKey="title"
          showGridlines stripedRows paginator :rows="pageSize" :rowsPerPageOptions="[5, 10, 20, 50]"
          resizableColumns columnResizeMode="fit"
          sortField="id" :sortOrder="1" removableSort
          filterDisplay="row" v-model:filters="filters"
      >
        <template #header>
          <div class="flex justify-between">
            <div class="flex space-x-3">
              <slot name="buttons"/>
              <IconField>
                <InputIcon class="pi pi-search"/>
                <InputText v-model="filters['global'].value" placeholder="Поиск"/>
              </IconField>
            </div>
            <div class="flex space-x-3">
              <Button icon="pi pi-download" label="" class="main-button" @click="exportCSV($event)" />
              <Button type="button" icon="pi pi-filter-slash" class="main-button" label="Очистить фильтры"
                      @click="clearFilter()"/>
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

.main-table {

}

.row {
  @apply dark:text-teal-100;
}
</style>