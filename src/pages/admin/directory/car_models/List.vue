<script setup>
import {onMounted, ref} from "vue";
import {useAuthStore} from "@stores";
import router from "@router";
import {FilterMatchMode, FilterOperator} from "@primevue/core/api";

import Table from "@components/Table/Table.vue";
import DirectoryService from "@services/DirectoryService.js";
import PageContainer from "@components/Containers/Admin/PageContainer.vue";

const {user} = useAuthStore();

const items = ref([]);
const brands = ref([]);
const loading = ref(true);

const directoryTitle = 'Модели авто';
const directoryUrl = 'car_models';

const breadcrumbs = ref([
  {
    name: directoryTitle,
    route: null
  },
]);
const statuses = ref(['Активен', 'В архиве']);
const filters = ref({
  id: {operator: FilterOperator.OR, constraints: [{value: null, matchMode: FilterMatchMode.EQUALS}]},
  name: {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.STARTS_WITH}]},
  brand: {operator: FilterOperator.OR, constraints: [{value: null, matchMode: FilterMatchMode.EQUALS}]},
  archive: {operator: FilterOperator.OR, constraints: [{value: "Активен", matchMode: FilterMatchMode.EQUALS}]},
});
const filterFields = ref(['id', 'name', 'brand', 'archive']);

const handleAddButtonClick = () => {
  router.push(`/Admin/directory/${directoryUrl}/new`);
}
const handleRowClick = (item) => {
  router.push(`/Admin/directory/${directoryUrl}/` + item.id);
}

async function fetchData() {
  items.value = await DirectoryService.getAllModels({company_id: user.company_id});
  brands.value = await DirectoryService.getAll({directory: 'directory_car_brands', company_id: user.company_id});
  brands.value = brands.value.filter(item => item.archive === "Активен").map(item => item.name);
  loading.value = false;
}

onMounted(() => {
  fetchData();
});
</script>

<template>
  <PageContainer :breadcrumbs="breadcrumbs">
    <Table
        :title="directoryTitle" :items="items"
        :filters="filters" :filterFields="filterFields"
        :loading="loading" @onRowClick="handleRowClick"
    >
      <template #columns>
        <Column field="id" header="ID" dataType="numeric" headerStyle="width: 3rem; min-width: 3rem;" sortable>
          <template #filter="{ filterModel }">
            <InputText v-model="filterModel.value" type="number" placeholder="Поиск по ID"/>
          </template>
        </Column>
        <Column field="name" header="Название" sortable>
          <template #filter="{ filterModel }">
            <InputText v-model="filterModel.value" type="text" placeholder="Поиск по названию"/>
          </template>
        </Column>
        <Column field="brand" header="Марка" sortable>
          <template #filter="{ filterModel }">
            <Dropdown v-model="filterModel.value" :options="brands" placeholder="Все" class="p-column-filter"
                      showClear filter/>
          </template>
        </Column>
        <Column field="archive" header="Статус" sortable>
          <template #filter="{ filterModel }">
            <Dropdown v-model="filterModel.value" :options="statuses" placeholder="Все" class="p-column-filter"
                      showClear/>
          </template>
        </Column>
      </template>
      <template #buttons>
        <Button v-if="user.access.directory === 2" type="button" icon="pi pi-plus" label="Добавить" outlined @click="handleAddButtonClick"/>
      </template>
    </Table>
  </PageContainer>
</template>