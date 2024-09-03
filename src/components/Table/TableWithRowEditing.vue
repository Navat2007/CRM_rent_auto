<script setup>
import {ref} from "vue";
import AlertModal from "@components/Modals/AlertModal.vue";
import BaseModal from "@components/Modals/BaseModal.vue";

const props = defineProps({
  sending: {
    type: Boolean,
    required: false,
    default: false
  },
  title: {
    type: String,
    required: true
  },
  items: {
    type: Array,
    required: true
  },
  columns: {
    type: Array,
    required: true
  }
});
const emit = defineEmits(['onChange']);

const isAddModalOpen = ref(false);
const isEditModalOpen = ref(false);
const isDeleteModalOpen = ref(false);

const currentRow = ref();
const rows = ref(props.items);
const editingRows = ref([]);

const findIndexById = (id) => {
  let index = -1;
  for (let i = 0; i < rows.value.length; i++) {
    if (rows.value[i].id === id) {
      index = i;
      break;
    }
  }

  return index;
};
const createId = (length = 10) => {
  let id = '';
  let chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
  for (let i = 0; i < length; i++) {
    id += chars.charAt(Math.floor(Math.random() * chars.length));
  }
  return id;
}

const showAddDialog = (data) => {
  currentRow.value = {
    id: createId()
  };

  props.columns.map(column => {
    currentRow.value[column.value] = '';
  });

  isAddModalOpen.value = true;
};
const showEditDialog = (data) => {
  currentRow.value = {...data};
  isEditModalOpen.value = true;
};
const showDeleteDialog = (data) => {
  currentRow.value = data;
  isDeleteModalOpen.value = true;
};

const confirmAdd = () => {
  rows.value.push(currentRow.value);
  currentRow.value = {};
  isAddModalOpen.value = false;
  emit('onChange', rows);
};
const confirmEdit = () => {
  rows.value[findIndexById(currentRow.value.id)] = currentRow.value;
  currentRow.value = {};
  isEditModalOpen.value = false;
  emit('onChange', rows);
};
const confirmDelete = () => {
  rows.value = rows.value.filter(row => row.id !== currentRow.value.id);
  currentRow.value = {};
  isDeleteModalOpen.value = false;
  emit('onChange', rows);
};
</script>

<template>
  <Fieldset :legend="props.title">
    <Toolbar class="mb-6">
      <template #start>
        <Button label="Добавить" icon="pi pi-plus" severity="success" class="mr-2" @click="showAddDialog"/>
      </template>
    </Toolbar>
    <DataTable v-if="rows.length > 0" v-model:editingRows="editingRows" :value="rows" :paginator="rows.length > 5"
               :rows="5">
      <Column v-for="column in props.columns" :field="column.value" :header="column.title">
        <template #editor="{ data, field }">
          <InputText v-model="data[field]"/>
        </template>
      </Column>
      <Column :exportable="false" style="min-width: 2rem">
        <template #body="slotProps">
          <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="showEditDialog(slotProps.data)"/>
          <Button icon="pi pi-trash" outlined rounded severity="danger" @click="showDeleteDialog(slotProps.data)"/>
        </template>
      </Column>
    </DataTable>

    <BaseModal :is-open="isAddModalOpen" @close="isAddModalOpen = false" title="Добавление">
      <div v-for="column in props.columns">
        <label for="description" class="block font-bold mb-3">{{ column.title }}</label>
        <InputText v-model="currentRow[column.value]" fluid/>
        <p v-if="column.required && currentRow[column.value] === ''" class="text-red-500">
          {{ column.title }} должен быть заполнен
        </p>
      </div>
      <Button type="submit" icon="pi pi-save" label="Сохранить" :loading="sending"
              :disabled="columns.filter(column => column.required && currentRow[column.value] === '').length > 0"
              @click="confirmAdd" outlined/>
      <Button icon="pi pi-times" label="Отмена" class="ml-4" severity="secondary" :loading="sending"
              @click="isAddModalOpen = false" outlined/>
    </BaseModal>

    <BaseModal :is-open="isEditModalOpen" @close="isEditModalOpen = false" title="Редактирование">
      <div v-for="column in props.columns">
        <label for="description" class="block font-bold mb-3">{{ column.title }}</label>
        <InputText v-model="currentRow[column.value]" fluid/>
        <p v-if="column.required && currentRow[column.value] === ''" class="text-red-500">
          {{ column.title }} должен быть заполнен
        </p>
      </div>
      <Button type="submit" icon="pi pi-save" label="Сохранить" :loading="sending"
              :disabled="columns.filter(column => column.required && currentRow[column.value] === '').length > 0"
              @click="confirmEdit" outlined/>
      <Button icon="pi pi-times" label="Отмена" class="ml-4" severity="secondary" :loading="sending"
              @click="isEditModalOpen = false" outlined/>
    </BaseModal>

    <AlertModal
        target="#modal2"
        :isOpen="isDeleteModalOpen"
        @close="isDeleteModalOpen = false"
        @accept="confirmDelete"
        title="Вы действительно хотите удалить?"
        withButtons info
    />
  </Fieldset>

</template>

<style scoped>

</style>