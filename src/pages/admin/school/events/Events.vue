<script setup>
import {onMounted, ref, computed} from "vue";
import moment from "moment/moment.js";
import router from "@router";
import {FilterMatchMode, FilterOperator} from "@primevue/core/api";
import {useAuthStore} from "@stores";

import Table from "@components/Table/Table.vue";
import PageContainer from "@components/Containers/Admin/PageContainer.vue";
import EventsService from "@services/admin/school/EventsService";

const {user} = useAuthStore();

const items = ref([]);
const advertising_type = ref([]);
const loading = ref(true);

const breadcrumbs = ref([
  {
    name: 'Мероприятия',
    route: null
  },
]);
const columns = ref([
  {
    header: 'ID',
    field: 'id',
  },
  {
    header: 'Название мероприятия',
    field: 'title',
  },
]);
const filters = ref({
  id: {operator: FilterOperator.OR, constraints: [{value: null, matchMode: FilterMatchMode.EQUALS}]},
  title: {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.CONTAINS}]},
  sport: {operator: FilterOperator.OR, constraints: [{value: null, matchMode: FilterMatchMode.EQUALS}]},
});
const filterFields = ref(['id', 'title', 'sport']);
const selectedColumns = ref(columns.value);
const onToggle = (val) => {
  selectedColumns.value = columns.value.filter(col => val.includes(col));
};

const handleAddButtonClick = (item) => {
  //router.push('/Admin/clients/new');
}
const handleRowClick = (item) => {
  //router.push('/Admin/clients/' + item.id);
}

const formatDate = (value) => {
  if (value.without_birth_date)
    return '';

  return moment(value.birth_date).format('DD.MM.YYYY');
};

async function fetchData() {
  items.value = await EventsService.getAll(loading);
  loading.value = false;
}

onMounted(() => {
  fetchData();
});
</script>

<template>
  <PageContainer :breadcrumbs="breadcrumbs">
    <Table
        title="Мероприятия"
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
        <Column field="title" header="Мероприятие" sortable>
          <template #filter="{ filterModel }">
            <InputText v-model="filterModel.value" type="text" placeholder="Поиск по названию"/>
          </template>
        </Column>
      </template>
    </Table>
  </PageContainer>
</template>