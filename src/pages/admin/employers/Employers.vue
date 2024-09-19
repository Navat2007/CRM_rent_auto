<script setup>
import {onMounted, ref, computed} from "vue";
import router from "@router";
import {FilterMatchMode, FilterOperator} from "@primevue/core/api";
import {useAuthStore} from "@stores";

import Table from "@components/Table/Table.vue";
import UserService from "@services/UserService.js";
import DirectoryService from "@services/DirectoryService.js";
import PageContainer from "@components/Containers/Admin/PageContainer.vue";

const {user} = useAuthStore();

const items = ref([]);
const positions = ref([]);
const loading = ref(true);

const breadcrumbs = ref([
  {
    name: 'Сотрудники',
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
    header: 'Должность',
    field: 'position',
  },
  {
    header: 'Дата увольнения',
    field: 'firing_date',
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
  position: {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.EQUALS}]},
  status: {operator: FilterOperator.OR, constraints: [{value: "Активен", matchMode: FilterMatchMode.EQUALS}]},
});
const filterFields = ref(['id', 'full_name', 'email', 'position', 'status']);

const handleAddButtonClick = (item) => {
  router.push('/Admin/employers/new');
}
const handleRowClick = (item) => {
  router.push('/Admin/employers/' + item.id);
}

async function fetchData() {
  items.value = await UserService.getUsers(user.company_id);
  positions.value = await DirectoryService.getPositions(user.company_id);
  positions.value = positions.value.filter(position => position.archive === "Активен").map(position => position.name);
  loading.value = false;
}

onMounted(() => {
  fetchData();
});
</script>

<template>
  <PageContainer :breadcrumbs="breadcrumbs">
    <Table
        title="Сотрудники"
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
        <Column field="position" header="Должность" sortable>
          <template #filter="{ filterModel }">
            <Dropdown v-model="filterModel.value" :options="positions" placeholder="Все" class="p-column-filter"
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
        <Button v-if="user.access.employers === 2" type="button" icon="pi pi-plus" label="Добавить" outlined @click="handleAddButtonClick" />
      </template>
    </Table>
  </PageContainer>
</template>

<style scoped>

</style>