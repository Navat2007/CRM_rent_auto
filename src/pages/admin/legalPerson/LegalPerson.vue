<script setup>
import {onMounted, ref, computed} from "vue";
import router from "@router";
import {FilterMatchMode, FilterOperator} from "@primevue/core/api";
import {useAuthStore} from "@stores";

import Table from "@components/Table/Table.vue";
import PageContainer from "@components/Containers/Admin/PageContainer.vue";
import LegalPersonsService from "@services/LegalPersonsService.js";
import moment from "moment";

const {user} = useAuthStore();

const items = ref([]);
const loading = ref(true);

const breadcrumbs = ref([
  {
    name: 'Юр лица',
    route: null
  },
]);
const columns = ref([
  {
    header: 'ID',
    field: 'id',
  },
  {
    header: 'Название',
    field: 'full_name',
  },
  {
    header: 'ОГРН',
    field: 'ogrn',
  },
  {
    header: 'ИНН',
    field: 'inn',
  },
  {
    header: 'Статус',
    field: 'status',
  },
  {
    header: 'Дата архивации',
    field: 'updated_at',
  }
]);
const statuses = ref(['Активен', 'Отключен', 'В архиве']);
const filters = ref({
  id: {operator: FilterOperator.OR, constraints: [{value: null, matchMode: FilterMatchMode.EQUALS}]},
  full_name: {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.CONTAINS}]},
  ogrn: {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.CONTAINS}]},
  inn: {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.CONTAINS}]},
  status: {operator: FilterOperator.OR, constraints: [{value: "Активен", matchMode: FilterMatchMode.EQUALS}]},
  updated_at: {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.DATE_IS}]},
});
const filterFields = ref(['id', 'full_name', 'ogrn', 'inn', 'status', 'updated_at']);

const formatDate = (value) => {
  if(value.archive === 0)
    return '';

  return moment(value.updated_at).format('DD.MM.YYYY');
};

const handleAddButtonClick = (item) => {
  router.push('/Admin/legalPerson/new');
}
const handleRowClick = (item) => {
  router.push('/Admin/legalPerson/' + item.id);
}

async function fetchData() {
  items.value = await LegalPersonsService.getLegalPersons(user.company_id);

  items.value.map(item => {
    if(item.archive === 0) {
      item.updated_at = new Date('1900-01-01');
    }
    else{
      item.updated_at = new Date(item.updated_at);
    }
  });

  loading.value = false;
}

onMounted(() => {
  fetchData();
});
</script>

<template>
  <PageContainer :breadcrumbs="breadcrumbs">
    <Table
        title="Юр лица"
        :items="items" :columns="columns"
        :filters="filters" :filterFields="filterFields"
        :loading="loading" @onRowClick="handleRowClick"
    >
      <template #columns>
        <Column field="id" header="ID" dataType="numeric" headerStyle="width: 3rem; min-width: 3rem;" sortable>
          <template #filter="{ filterModel }">
            <InputText v-model="filterModel.value" type="number" placeholder="Поиск по ID"/>
          </template>
        </Column>
        <Column field="full_name" header="Название" sortable>
          <template #filter="{ filterModel }">
            <InputText v-model="filterModel.value" type="text" placeholder="Поиск по названию"/>
          </template>
        </Column>
        <Column field="ogrn" header="ОГРН" sortable>
          <template #filter="{ filterModel }">
            <InputNumber v-model="filterModel.value" type="text" placeholder="Поиск по ОГРН"/>
          </template>
        </Column>
        <Column field="inn" header="ИНН" sortable>
          <template #filter="{ filterModel }">
            <InputNumber v-model="filterModel.value" type="text" placeholder="Поиск по ИНН"/>
          </template>
        </Column>
        <Column field="status" header="Статус" sortable>
          <template #filter="{ filterModel }">
            <Dropdown v-model="filterModel.value" :options="statuses" placeholder="Все" class="p-column-filter"
                      showClear/>
          </template>
        </Column>
        <Column field="updated_at" dataType="date" header="Дата архивации" sortable>
          <template #body="{ data }">
            {{ formatDate(data) }}
          </template>
          <template #filter="{ filterModel }">
            <DatePicker
                v-model="filterModel.value"
                dateFormat="dd.mm.yy" placeholder="дд.мм.гг"
            />
          </template>
        </Column>
      </template>
      <template #buttons>
        <Button v-if="user.access.clients === 2" type="button" icon="pi pi-plus" label="Добавить" outlined @click="handleAddButtonClick" />
      </template>
    </Table>
  </PageContainer>
</template>