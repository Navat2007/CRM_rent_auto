<script setup>
import {onMounted, ref} from "vue";
import router from "@router";
import {FilterMatchMode, FilterOperator} from "@primevue/core/api";
import {useAuthStore} from "@stores";

import Table from "@components/Table/Table.vue";
import PageContainer from "@components/Containers/Admin/PageContainer.vue";
import BookingService from "@services/BookingService.js";

const {user} = useAuthStore();

const items = ref([]);
const loading = ref(true);

const breadcrumbs = ref([
  {
    name: 'Бронирование',
    route: null
  },
  {
    name: 'Договора проката',
    route: '/Admin/booking/rentalContracts/'
  },
]);
const statuses = ref(['Активен', 'В архиве']);
const filters = ref({
  id: {operator: FilterOperator.OR, constraints: [{value: null, matchMode: FilterMatchMode.EQUALS}]},
  state_number: {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.CONTAINS}]},
  brand: {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.CONTAINS}]},
  model: {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.CONTAINS}]},
  status: {operator: FilterOperator.OR, constraints: [{value: null, matchMode: FilterMatchMode.EQUALS}]},
  archive: {operator: FilterOperator.OR, constraints: [{value: "Активен", matchMode: FilterMatchMode.EQUALS}]},
  body_condition: {operator: FilterOperator.OR, constraints: [{value: null, matchMode: FilterMatchMode.EQUALS}]},
  interior_condition: {operator: FilterOperator.OR, constraints: [{value: null, matchMode: FilterMatchMode.EQUALS}]},
  trunk_condition: {operator: FilterOperator.OR, constraints: [{value: null, matchMode: FilterMatchMode.EQUALS}]},
  need_refuel: {operator: FilterOperator.OR, constraints: [{value: null, matchMode: FilterMatchMode.EQUALS}]},
  need_service: {operator: FilterOperator.OR, constraints: [{value: null, matchMode: FilterMatchMode.EQUALS}]},
  has_failure: {operator: FilterOperator.OR, constraints: [{value: null, matchMode: FilterMatchMode.EQUALS}]},
});
const filterFields = ref([
  'id',
  'state_number',
  'brand',
  'model',
  'status',
  'body_condition',
  'interior_condition',
  'trunk_condition',
  'need_refuel',
  'need_service',
  'has_failure',
  'archive'
]);

const handleAddButtonClick = (item) => {
  router.push('/Admin/booking/rentalContracts/new');
}
const handleRowClick = (item) => {
  router.push('/Admin/booking/rentalContracts/' + item.id);
}

async function fetchData() {
  items.value = await BookingService.getAll({company_id: user.company_id});

  loading.value = false;
}

onMounted(() => {
  fetchData();
});
</script>

<template>
  <PageContainer :breadcrumbs="breadcrumbs">
    <Table
        title="Договора проката"
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
        <Column field="state_number" header="Гос номер" sortable headerStyle="width: 15rem; min-width: 10rem;">
          <template #filter="{ filterModel }">
            <InputText v-model="filterModel.value" type="text" placeholder="Поиск по гос. номеру"/>
          </template>
        </Column>
        <Column field="status" header="Статус" sortable headerStyle="width: 10rem; min-width: 10rem;">
          <template #filter="{ filterModel }">
            <Dropdown v-model="filterModel.value" :options="carStatuses" placeholder="Все" class="p-column-filter"
                      showClear/>
          </template>
        </Column>
        <Column field="body_condition" headerStyle="max-width: 3.5rem; min-width: 3.5rem;">
          <template #filterheader>
            <label for="state_number"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Состояние кузова</label>
          </template>
          <template #filter="{ filterModel }">
            <Dropdown v-model="filterModel.value" :options="conditions" option-label="label" option-value="value"
                      placeholder="Все" class="p-column-filter"
                      showClear/>
          </template>
          <template #body="{data}" class="flex justify-center items-center">
            <div class="flex items-center justify-center">
              <Icon size="24" v-tooltip.top="conditions.find(item => item.value === data.body_condition)?.label">
                <component :is="getCarCondition('body', data)"/>
              </Icon>
            </div>
          </template>
        </Column>
        <Column field="interior_condition" headerStyle="max-width: 3.5rem; min-width: 3.5rem;">
          <template #filterheader>
            <label for="state_number"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Состояние салона</label>
          </template>
          <template #filter="{ filterModel }">
            <Dropdown v-model="filterModel.value" :options="conditions" option-label="label" option-value="value"
                      placeholder="Все" class="p-column-filter"
                      showClear/>
          </template>
          <template #body="{data}" class="flex justify-center items-center">
            <div class="flex items-center justify-center">
              <Icon size="24" v-tooltip.top="conditions.find(item => item.value === data.interior_condition)?.label">
                <component :is="getCarCondition('interior', data)"/>
              </Icon>
            </div>
          </template>
        </Column>
        <Column field="trunk_condition" headerStyle="max-width: 3.5rem; min-width: 3.5rem;">
          <template #filterheader>
            <label for="state_number"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Состояние багажника</label>
          </template>
          <template #filter="{ filterModel }">
            <Dropdown v-model="filterModel.value" :options="conditions" option-label="label" option-value="value"
                      placeholder="Все" class="p-column-filter"
                      showClear/>
          </template>
          <template #body="{data}" class="flex justify-center items-center">
            <div class="flex items-center justify-center">
              <Icon size="24" v-tooltip.top="conditions.find(item => item.value === data.trunk_condition)?.label">
                <component :is="getCarCondition('trunk', data)"/>
              </Icon>
            </div>
          </template>
        </Column>
        <Column field="need_refuel" headerStyle="max-width: 3.5rem; min-width: 3.5rem;">
          <template #filterheader>
            <label for="state_number"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Уровень топлива</label>
          </template>
          <template #filter="{ filterModel }">
            <Dropdown v-model="filterModel.value" :options="fuelLevels" option-label="label" option-value="value"
                      placeholder="Все" class="p-column-filter"
                      showClear/>
          </template>
          <template #body="{data}" class="flex justify-center items-center">
            <div class="flex items-center justify-center">
              <Icon size="24" v-tooltip.top="fuelLevels.find(item => item.value === data.need_refuel)?.label">
                <component :is="getCarCondition('fuel', data)"/>
              </Icon>
            </div>
          </template>
        </Column>
        <Column field="need_service" headerStyle="max-width: 3.5rem; min-width: 3.5rem;">
          <template #filterheader>
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Нужно обслужить?</label>
          </template>
          <template #filter="{ filterModel }">
            <Dropdown v-model="filterModel.value" :options="needService" option-label="label" option-value="value"
                      placeholder="Все" class="p-column-filter"
                      showClear/>
          </template>
          <template #body="{data}" class="flex justify-center items-center">
            <div class="flex items-center justify-center">
              <Icon size="24" v-tooltip.top="data.need_service === 0 ? 'Обслужен' : 'Нужно обслужить'">
                <component :is="getCarCondition('to', data)"/>
              </Icon>
            </div>
          </template>
        </Column>
        <Column field="has_failure" header="Неисправность" headerStyle="width: 12rem; min-width: 12rem;">
          <template #filterheader>
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Есть неисправность?</label>
          </template>
          <template #filter="{ filterModel }">
            <Dropdown v-model="filterModel.value" :options="hasFailure" option-label="label" option-value="value"
                      placeholder="Все" class="p-column-filter"
                      showClear/>
          </template>
          <template #body="{data}">
            <div class="grid grid-flow-row-dense grid-cols-5">
              <Icon v-if="data.failure != ''" v-tooltip.top="{ value: data.failure, autoHide: false }" size="24">
                <Failure/>
              </Icon>
              <Icon v-if="data.critical_failure != ''" v-tooltip.top="{ value: data.critical_failure, autoHide: false }" size="24">
                <CriticalFailure/>
              </Icon>
            </div>
          </template>
        </Column>
        <Column field="archive" header="Архив" sortable headerStyle="width: 10rem; min-width: 10rem;">
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