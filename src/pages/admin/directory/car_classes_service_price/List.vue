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
const loading = ref(true);

const directoryClasses = ref([]);
const loadingDirectoryClasses = ref(true);

const directoryServices = ref([]);
const loadingDirectoryServices = ref(true);

const directoryTitle = 'Классы авто и цены на услуги';
const directoryUrl = 'car_classes_service_price';

const breadcrumbs = ref([
  {
    name: directoryTitle,
    route: null
  },
]);
const statuses = ref(['Активен', 'В архиве']);
const filters = ref({
  id: {operator: FilterOperator.OR, constraints: [{value: null, matchMode: FilterMatchMode.EQUALS}]},
  classes: {operator: FilterOperator.OR, constraints: [{value: null, matchMode: FilterMatchMode.EQUALS}]},
  service: {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.EQUALS}]},
  price: {operator: FilterOperator.OR, constraints: [{value: null, matchMode: FilterMatchMode.EQUALS}]},
  archive: {operator: FilterOperator.OR, constraints: [{value: "Активен", matchMode: FilterMatchMode.EQUALS}]},
});
const filterFields = ref(['id', 'classes', 'service', 'price', 'archive']);

const handleAddButtonClick = () => {
  router.push(`/Admin/directory/${directoryUrl}/new`);
}
const handleRowClick = (item) => {
  router.push(`/Admin/directory/${directoryUrl}/` + item.id);
}

async function fetchDirectoryCarClasses() {
  directoryClasses.value = await DirectoryService.getAll({directory: 'directory_car_classes', company_id: user.company_id});
  directoryClasses.value = directoryClasses.value.filter(item => item.archive === "Активен").map(item => item.name);
  loadingDirectoryClasses.value = false;
}
async function fetchDirectoryServices() {
  directoryServices.value = await DirectoryService.getAll({directory: 'directory_services', company_id: user.company_id});
  directoryServices.value = directoryServices.value.filter(item => item.archive === "Активен").map(item => item.name);
  loadingDirectoryServices.value = false;
}
async function fetchData() {
  items.value = await DirectoryService.getAllCarClassServicePrices({company_id: user.company_id});
  loading.value = false;
}

onMounted(() => {
  fetchData();
  fetchDirectoryCarClasses();
  fetchDirectoryServices();
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
        <Column field="classes" header="Класс" sortable>
          <template #filter="{ filterModel }">
            <Dropdown v-model="filterModel.value" :options="directoryClasses" placeholder="Все" class="p-column-filter"
                      showClear filter/>
          </template>
        </Column>
        <Column field="service" header="Услуга" sortable>
          <template #filter="{ filterModel }">
            <Dropdown v-model="filterModel.value" :options="directoryServices" placeholder="Все" class="p-column-filter"
                      showClear filter/>
          </template>
        </Column>
        <Column field="price" header="Цена" dataType="numeric" headerStyle="width: 7rem; min-width: 7rem;" sortable>
          <template #filter="{ filterModel }">
            <InputText v-model="filterModel.value" type="number" placeholder="Поиск по цене"/>
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