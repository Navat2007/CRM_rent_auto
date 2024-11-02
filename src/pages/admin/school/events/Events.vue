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
const sports = ref([]);
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
    header: 'Название',
    field: 'title',
  },
  {
    header: 'Спорт',
    field: 'sport',
  },
]);
const filters = ref({
  id: {operator: FilterOperator.OR, constraints: [{value: null, matchMode: FilterMatchMode.EQUALS}]},
  title: {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.CONTAINS}]},
  sport: {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.EQUALS}]},
});
const filterFields = ref(['id', 'title', 'sport.title']);

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

  sports.value = items.value
      .map(item => item.sport)
      .filter((item, index, arr) => {
        return arr.indexOf(item) === index;
      })
      .sort();

  console.log(items.value);

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
        <Column field="title" header="Название" sortable>
          <template #filter="{ filterModel }">
            <InputText v-model="filterModel.value" type="text" placeholder="Поиск по названию"/>
          </template>
        </Column>
        <Column field="sport" header="Спорт" sortable>
          <template #filter="{ filterModel }">
            <Dropdown v-model="filterModel.value" :options="sports" placeholder="Все" class="p-column-filter"
                      showClear filter/>
          </template>
        </Column>
      </template>
    </Table>
  </PageContainer>
</template>