<script setup>
import {onMounted, ref, computed} from "vue";
import router from "@router";
import {FilterMatchMode, FilterOperator} from "@primevue/core/api";
import {useAuthStore} from "@stores";

import Table from "@components/Table/Table.vue";
import ClientService from "@services/ClientsService.js";
import DirectoryService from "@services/DirectoryService.js";
import PageContainer from "@components/Containers/Admin/PageContainer.vue";
import moment from "moment/moment.js";

const {user} = useAuthStore();

const items = ref([]);
const advertising_type = ref([]);
const loading = ref(true);

const breadcrumbs = ref([
  {
    name: 'Клиенты',
    route: null
  },
]);
const columns = ref([
  {
    header: 'ID',
    field: 'id',
  },
  {
    header: 'ФИО',
    field: 'full_name',
  },
  {
    header: 'Email',
    field: 'email',
  },
  {
    header: 'Телефон',
    field: 'phone',
  },
  {
    header: 'Дата рождения',
    field: 'birth_date',
  },
  {
    header: 'ИНН',
    field: 'inn',
  },
  {
    header: 'Вид рекламы',
    field: 'advertising_type',
  },
  {
    header: 'Статус',
    field: 'status',
  }
]);
const statuses = ref(['Активен', 'Отключен', 'В архиве']);
const filters = ref({
  id: {operator: FilterOperator.OR, constraints: [{value: null, matchMode: FilterMatchMode.EQUALS}]},
  full_name: {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.CONTAINS}]},
  email: {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.CONTAINS}]},
  phone: {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.CONTAINS}]},
  status: {operator: FilterOperator.OR, constraints: [{value: "Активен", matchMode: FilterMatchMode.EQUALS}]},
  birth_date: {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.DATE_IS}]},
  inn: {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.CONTAINS}]},
  advertising_type: {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.EQUALS}]},
});
const filterFields = ref(['id', 'full_name', 'email', 'phone', 'status', 'inn', 'birth_date', 'advertising_type']);
const selectedColumns = ref(columns.value);
const onToggle = (val) => {
  selectedColumns.value = columns.value.filter(col => val.includes(col));
};

const handleAddButtonClick = (item) => {
  router.push('/Admin/clients/new');
}
const handleRowClick = (item) => {
  router.push('/Admin/clients/' + item.id);
}

const formatDate = (value) => {
  if (value.without_birth_date)
    return '';

  return moment(value.birth_date).format('DD.MM.YYYY');
};

async function fetchData() {
  items.value = await ClientService.getClients(user.company_id);
  items.value.map(item => {
    if (item.birth_date === null) {
      item.without_birth_date = true;
      item.birth_date = new Date('1900-01-01');
    }
    else {
      item.birth_date = new Date(item.birth_date);
      item.without_birth_date = false;
    }
  });

  advertising_type.value = await DirectoryService.getAdvertisingTypes(user.company_id);
  advertising_type.value = advertising_type.value.filter(advertising => advertising.archive === "Активен").map(advertising => advertising.name);
  loading.value = false;
}

onMounted(() => {
  fetchData();
});
</script>

<template>
  <PageContainer :breadcrumbs="breadcrumbs">
    <Table
        title="Клиенты"
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
        <Column field="full_name" header="ФИО" sortable>
          <template #filter="{ filterModel }">
            <InputText v-model="filterModel.value" type="text" placeholder="Поиск по ФИО"/>
          </template>
        </Column>
        <Column field="email" header="Email" sortable>
          <template #filter="{ filterModel }">
            <InputText v-model="filterModel.value" type="text" placeholder="Поиск по Email"/>
          </template>
        </Column>
        <Column field="phone" header="Телефон" sortable>
          <template #filter="{ filterModel }">
            <InputText v-model="filterModel.value" type="text" placeholder="Поиск по телефону"/>
          </template>
        </Column>
        <Column field="birth_date" dataType="date" header="Дата рождения" sortable>
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
        <Column field="inn" header="ИНН" sortable>
          <template #filter="{ filterModel }">
            <InputNumber v-model="filterModel.value" type="text" placeholder="Поиск по ИНН"/>
          </template>
        </Column>
        <Column field="advertising_type" header="Вид рекламы" sortable>
          <template #filter="{ filterModel }">
            <Dropdown v-model="filterModel.value" :options="advertising_type" placeholder="Все" class="p-column-filter"
                      showClear filter/>
          </template>
        </Column>
        <Column field="status" header="Статус" sortable>
          <template #filter="{ filterModel }">
            <Dropdown v-model="filterModel.value" :options="statuses" placeholder="Все" class="p-column-filter"
                      showClear/>
          </template>
        </Column>
      </template>
      <template #buttons>
        <Button v-if="user.access.clients === 2" type="button" icon="pi pi-plus" label="Добавить" outlined
                @click="handleAddButtonClick"/>
      </template>
    </Table>
  </PageContainer>
</template>