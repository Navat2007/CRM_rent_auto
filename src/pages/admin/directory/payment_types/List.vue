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

const directoryTitle = 'Виды оплаты';
const directoryUrl = 'payment_types';

const breadcrumbs = ref([
  {
    name: directoryTitle,
    route: null
  },
]);
const statuses = ref(['Активен', 'В архиве']);
const filters = ref({
  id: {operator: FilterOperator.OR, constraints: [{value: null, matchMode: FilterMatchMode.EQUALS}]},
  order: {operator: FilterOperator.OR, constraints: [{value: null, matchMode: FilterMatchMode.EQUALS}]},
  name: {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.STARTS_WITH}]},
  archive: {operator: FilterOperator.OR, constraints: [{value: "Активен", matchMode: FilterMatchMode.EQUALS}]},
});
const filterFields = ref(['id', 'name', 'order', 'archive']);

const handleAddButtonClick = () => {
  router.push(`/Admin/directory/${directoryUrl}/new`);
}
const handleRowClick = (item) => {
  router.push(`/Admin/directory/${directoryUrl}/` + item.id);
}

async function fetchData() {
  items.value = await DirectoryService.getAll({directory: 'directory_' + directoryUrl, company_id: user.company_id});

  let orderArray = items.value.filter(item => parseInt(item.order) !== 0);
  let unOrderArray = items.value.filter(item => parseInt(item.order) === 0);
  orderArray.sort((a, b) => a.order - b.order);

  items.value = [...orderArray, ...unOrderArray];

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
            <InputNumber v-model="filterModel.value" placeholder="Поиск по ID"/>
          </template>
        </Column>
        <Column field="name" header="Название" sortable>
          <template #filter="{ filterModel }">
            <InputText v-model="filterModel.value" type="text" placeholder="Поиск по названию"/>
          </template>
        </Column>
        <Column field="order" header="Порядок отображения" dataType="numeric" sortable>
          <template #filter="{ filterModel }">
            <InputNumber v-model="filterModel.value" placeholder="..."/>
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