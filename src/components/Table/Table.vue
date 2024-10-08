<script setup>
import {ref} from "vue";
import DataTable from "primevue/datatable";
import {FilterMatchMode} from '@primevue/core/api';

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
  filters: {
    type: Object,
    required: false,
  },
  filterFields: {
    type: Array,
    required: false,
  },
  loading: {
    type: Boolean,
    required: false,
    default: false
  },
})
const emit = defineEmits(['onRowClick']);

const table = ref();
const tableSaveKey = ref(props.title + "_table");
const finalFilters = ref();
const skeletonItems = ref(new Array(10));

const handleRowClick = (item) => {
  emit('onRowClick', item.data);
}
const clearFilter = () => {
  initFilters();
};
const initFilters = () => {
  finalFilters.value = {
    ...props.filters,
    global: {value: null, matchMode: FilterMatchMode.CONTAINS}
  };
};
const exportCSV = () => {
  table.value.exportCSV();
};

initFilters();

//console.log(finalFilters);
</script>

<template>
  <Card>
    <template #title>
      <Toolbar class="mb-2">
        <template #start>
          <slot name="buttons"/>
        </template>
        <template #end>
          <!--              <Button icon="pi pi-download" label="" class="main-button" @click="exportCSV($event)" />-->
          <Button type="button" icon="pi pi-filter-slash" outlined label="Очистить"
                  @click="clearFilter()"/>
        </template>
      </Toolbar>
      <slot name="checkbox"/>
    </template>

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
          stateStorage="local" :stateKey="tableSaveKey" size="small"
          showGridlines stripedRows :paginator="items.length > pageSize" :rows="pageSize"
          :rowsPerPageOptions="[10, 20, 50]"
          resizableColumns columnResizeMode="fit" reorderableColumns
          sortField="id" :sortOrder="1" removableSort rowHover
          filterDisplay="menu" v-model:filters="finalFilters" :globalFilterFields="filterFields"
      >
        <template #header>
          <div class="flex justify-between items-center">
            <h2 class="text-xl">{{ title }}</h2>
            <IconField>
              <InputIcon class="pi pi-search"/>
              <InputText v-model="finalFilters['global'].value" placeholder="Поиск"/>
            </IconField>
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