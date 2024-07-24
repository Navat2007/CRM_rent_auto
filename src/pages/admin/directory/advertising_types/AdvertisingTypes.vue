<script setup>
import {onMounted, ref} from "vue";
import {useAuthStore} from "@stores";
import {useForm} from "vue-hooks-form";
import router from "@router";

import Checkbox from "@components/Inputs/Checkbox.vue";
import Table from "@components/Table/DataTable.vue";
import DirectoryService from "@services/DirectoryService.js";

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

const handleAddButtonClick = (item) => {
  router.push('/admin/directory/advertising_types/new');
}
const handleRowClick = (item) => {
  //router.push({ name: 'AdminEditUser', params: { id: item.id } });
  router.push('/admin/directory/advertising_types/' + item.id);
}

const handleFilters = () => {
  items.value = baseItems.value.filter(item => item.archive === "Активен" || (archive.value && item.archive === "В архиве"));
}

async function fetchData() {
  baseItems.value = await DirectoryService.getAdvertisingTypes(user.company_id);
  handleFilters(archive.value);
  loading.value = false;
}

onMounted(() => {
  fetchData();
  archive.value = false;
});
</script>

<template>
  <div class="w-full h-full p-4">
    <Table title="Виды рекламы" :items="items" :columns="columns" :loading="loading" @onRowClick="handleRowClick">
      <template #columns>
        <Column v-for="col of columns" :key="col.field" :field="col.field" :header="col.header" sortable></Column>
      </template>
      <template #buttons>
        <Button type="button" icon="pi pi-plus" label="Добавить" @click="handleAddButtonClick" />
      </template>
      <template #filter>
        <Checkbox title="Показывать архивные значения?" id="archive" :model="archive" @onChange="handleFilters"/>
      </template>
    </Table>
  </div>
</template>

<style scoped>

</style>