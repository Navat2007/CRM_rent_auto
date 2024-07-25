<script setup>
import {onMounted, ref} from "vue";
import {useAuthStore} from "@stores";

import Table from "@components/Table/DataTable.vue";
import AlertModal from "@components/Modals/AlertModal.vue";
import UserService from "@services/UserService.js";
import PageContainer from "@components/Containers/Admin/PageContainer.vue";

const {user} = useAuthStore();
const columns = ref([
  {
    header: 'ID',
    field: 'id',
    type: 'number',
    filter: "number",
    sorting: true,
  },
  {
    header: 'Email',
    field: 'email',
    type: 'string',
    filter: "string",
    sorting: true,
  },
  {
    header: 'Статус',
    field: 'status',
    type: 'string',
    filter: "select",
    sorting: true,
  }
]);
const items = ref([]);
const rowItem = ref({});
const error = ref('');
const loading = ref(true);
const breadcrumbs = ref([
  {
    name: 'Сотрудники',
    route: null
  },
]);

const isAlertModalOpen = ref(false);
const isSuccessModalOpen = ref(false);

const handleRowClick = (item) => {
  //router.push({ name: 'AdminEditUser', params: { id: item.id } });
}
const handleAddButtonClick = (item) => {
  //router.push({ name: 'AdminEditUser', params: { id: item.id } });
}

onMounted(() => {
  UserService.getUsers(user.company_id, loading).then(data => items.value = data);
});
</script>

<template>
  <PageContainer :breadcrumbs="breadcrumbs">
    <Table title="Сотрудники" :items="items" :columns="columns" :loading="loading" @onRowClick="handleRowClick">
      <template #columns>
        <Column v-for="col of columns" :key="col.field" :field="col.field" :header="col.header" sortable ></Column>
      </template>
      <template #buttons>
        <Button type="button" icon="pi pi-plus" label="Добавить" class="main-button" @click="handleAddButtonClick" />
      </template>
    </Table>
  </PageContainer>

  <AlertModal :isOpen="isSuccessModalOpen" @close="isSuccessModalOpen = false" title="Запрос выполнен" accept/>
  <AlertModal :isOpen="isAlertModalOpen" @close="isAlertModalOpen = false" :title="error" info/>
</template>

<style scoped>

</style>