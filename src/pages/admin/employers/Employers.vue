<script setup>
import {onMounted, ref} from "vue";
import {useAuthStore} from "@stores";

import Table from "@components/Table/Table.vue";
import UserService from "@services/UserService.js";
import PageContainer from "@components/Containers/Admin/PageContainer.vue";
import {FilterMatchMode, FilterOperator} from "@primevue/core/api";

const {user} = useAuthStore();

const items = ref([]);
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
    header: 'Email',
    field: 'email',
  },
  {
    header: 'Статус',
    field: 'status',
  }
]);
const statuses = ref(['Активен', 'В архиве']);
const filters = ref({
  id: {operator: FilterOperator.OR, constraints: [{value: null, matchMode: FilterMatchMode.EQUALS}]},
  email: {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.STARTS_WITH}]},
  status: {operator: FilterOperator.OR, constraints: [{value: "Активен", matchMode: FilterMatchMode.EQUALS}]},
});
const filterFields = ref(['id', 'email', 'status']);

const handleRowClick = (item) => {
  //router.push({ name: 'AdminEditUser', params: { id: item.id } });
}
const handleAddButtonClick = (item) => {
  //router.push({ name: 'AdminEditUser', params: { id: item.id } });
}

async function fetchData() {
  items.value = await UserService.getUsers(user.company_id);
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
        <Column field="email" header="Email" sortable>
          <template #filter="{ filterModel }">
            <InputText v-model="filterModel.value" type="text" placeholder="Поиск по Email"/>
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
        <Button type="button" icon="pi pi-plus" label="Добавить" class="main-button" @click="handleAddButtonClick" />
      </template>
    </Table>
  </PageContainer>
</template>

<style scoped>

</style>