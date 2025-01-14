<script setup>
import {onMounted, ref} from "vue";
import router from "@router";
import {FilterMatchMode, FilterOperator} from "@primevue/core/api";
import {useAuthStore} from "@stores";

import Table from "@components/Table/Table.vue";
import PageContainer from "@components/Containers/Admin/PageContainer.vue";
import BookingService from "@services/BookingService.js";
import DirectoryService from "@services/DirectoryService.js";
import moment from "moment";

const {user} = useAuthStore();

const items = ref([]);
const models = ref([]);
const brands = ref([]);
const classes = ref([]);
const loading = ref(true);

const breadcrumbs = ref([
  {
    name: 'Бронирование',
    route: null
  },
  {
    name: 'Договора проката',
    route: null
  },
]);
const statuses = ref(['Активен', 'В архиве']);
const filters = ref({
  id: {operator: FilterOperator.OR, constraints: [{value: null, matchMode: FilterMatchMode.EQUALS}]},
  state_number: {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.CONTAINS}]},
  brand: {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.CONTAINS}]},
  model: {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.CONTAINS}]},
  class: {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.CONTAINS}]},
  fio: {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.CONTAINS}]},
  start_date: {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.DATE_IS}]},
  end_date: {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.DATE_IS}]},
});
const filterFields = ref([
  'id',
  'state_number',
  'brand',
  'model',
  'class',
  'fio',
  'start_date',
  'end_date',
]);

const handleAddButtonClick = (item) => {
  router.push('/Admin/booking/rentalContracts/new');
}
const handleRowClick = (item) => {
  router.push('/Admin/booking/rentalContracts/' + item.id);
}

async function fetchData() {
  items.value = await BookingService.getAll({company_id: user.company_id});

  items.value.map(item => {
    item.start_date = new Date(item.start_date);
    item.end_date = new Date(item.end_date);
  });

  models.value = await DirectoryService.getAll({directory: 'directory_car_models', company_id: user.company_id});
  models.value = models.value.filter(item => item.archive === "Активен").map(item => item.name);
  brands.value = await DirectoryService.getAll({directory: 'directory_car_brands', company_id: user.company_id});
  brands.value = brands.value.filter(item => item.archive === "Активен").map(item => item.name);
  classes.value = await DirectoryService.getAll({directory: 'directory_car_classes', company_id: user.company_id});
  classes.value = classes.value.filter(item => item.archive === "Активен").map(item => item.name);

  loading.value = false;
}

onMounted(() => {
  fetchData();
});
</script>

<template>
  <PageContainer :breadcrumbs="breadcrumbs">
    <Table
        title=""
        :items="items"
        :filters="filters" :filterFields="filterFields"
        :loading="loading" @onRowClick="handleRowClick"
    >
      <template #columns>
        <Column field="id" header="ID" dataType="numeric" headerStyle="width: 7rem; min-width: 7rem;" sortable>
          <template #filter="{ filterModel }">
            <InputText v-model="filterModel.value" type="number" placeholder="Поиск по ID"/>
          </template>
        </Column>
        <Column field="state_number" header="Гос номер" sortable headerStyle="width: 15rem; min-width: 10rem;">
          <template #filter="{ filterModel }">
            <InputText v-model="filterModel.value" type="text" placeholder="Поиск по гос. номеру"/>
          </template>
        </Column>
        <Column field="brand" header="Марка" sortable headerStyle="width: 15rem; min-width: 10rem;">
          <template #filter="{ filterModel }">
            <Dropdown v-model="filterModel.value" :options="brands" placeholder="Все" class="p-column-filter"
                      showClear filter/>
          </template>
        </Column>
        <Column field="model" header="Модель" sortable headerStyle="width: 15rem; min-width: 10rem;">
          <template #filter="{ filterModel }">
            <Dropdown v-model="filterModel.value" :options="models" placeholder="Все" class="p-column-filter"
                      showClear filter/>
          </template>
        </Column>
        <Column field="class" header="Класс" sortable headerStyle="width: 15rem; min-width: 10rem;">
          <template #filter="{ filterModel }">
            <Dropdown v-model="filterModel.value" :options="classes" placeholder="Все" class="p-column-filter"
                      showClear filter/>
          </template>
        </Column>
        <Column field="fio" header="Клиент" sortable headerStyle="width: 15rem; min-width: 10rem;">
          <template #filter="{ filterModel }">
            <InputText v-model="filterModel.value" type="text" placeholder="Поиск по ФИО"/>
          </template>
        </Column>
        <Column field="start_date" dataType="date" header="Начало" sortable>
          <template #body="{ data }">
            {{ moment(data.start_date).format('DD.MM.YYYY HH:mm') }}
          </template>
          <template #filter="{ filterModel }">
            <DatePicker
                v-model="filterModel.value"
                dateFormat="dd.mm.yy" placeholder="дд.мм.гг"
            />
          </template>
        </Column>
        <Column field="end_date" dataType="date" header="Возврат" sortable>
          <template #body="{ data }">
            {{ moment(data.end_date).format('DD.MM.YYYY HH:mm') }}
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
        <Button v-if="user.access.booking === 2" type="button" icon="pi pi-plus" label="Добавить" outlined
                @click="handleAddButtonClick"/>
      </template>
    </Table>
  </PageContainer>
</template>