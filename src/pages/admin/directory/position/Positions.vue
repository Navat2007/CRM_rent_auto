<script setup>
import {onMounted, ref} from "vue";
import {useAuthStore} from "@stores";
import {useForm} from "vue-hooks-form";
import router from "@router";

import Checkbox from "@components/Inputs/Checkbox.vue";
import Table from "@components/Table/DataTable.vue";
import DirectoryService from "@services/DirectoryService.js";
import PageContainer from "@components/Containers/Admin/PageContainer.vue";

const {useField, validateFields} = useForm({
  defaultValues: {
    login: '',
    password: '',
    passwordConfirm: '',
    active: true
  },
  validateMode: 'submit'
});

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
    header: 'Название',
    field: 'name',
    type: 'string',
    filter: "string",
    sorting: true,
  },
  {
    header: 'Статус',
    field: 'archive',
    type: 'string',
    filter: "select",
    sorting: true,
  }
]);
const baseItems = ref([]);
const items = ref([]);
const rowItem = ref({});
const error = ref('');
const loading = ref(true);

const archive = useField('active');
const breadcrumbs = ref([
  {
    name: 'Должности',
    route: null
  },
]);

const handleAddButtonClick = (item) => {
  router.push('/Admin/directory/positions/new');
}
const handleRowClick = (item) => {
  router.push('/Admin/directory/positions/' + item.id);
}

const handleFilters = () => {
  items.value = baseItems.value.filter(item => item.archive === "Активен" || (archive.value && item.archive === "В архиве"));
}

async function fetchData() {
  baseItems.value = await DirectoryService.getPositions(user.company_id);
  handleFilters(archive.value);
  loading.value = false;
}

onMounted(() => {
  fetchData();
  archive.value = false;
});
</script>

<template>
  <PageContainer :breadcrumbs="breadcrumbs">
    <Table title="Должности" :items="items" :columns="columns" :loading="loading" @onRowClick="handleRowClick">
      <template #columns>
        <Column v-for="col of columns" :key="col.field" :field="col.field" :header="col.header" sortable></Column>
      </template>
      <template #buttons>
        <Button type="button" icon="pi pi-plus" label="Добавить" class="main-button" @click="handleAddButtonClick" />
      </template>
      <template #filter>
        <Checkbox title="Показывать архивные значения?" id="archive" :model="archive" @onChange="handleFilters"/>
      </template>
    </Table>
  </PageContainer>
</template>

<style scoped>

</style>