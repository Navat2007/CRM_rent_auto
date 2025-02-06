<script setup>
import {onMounted, ref} from "vue";
import {useAuthStore} from "@stores";
import router from "@router";
import {FilterMatchMode, FilterOperator} from "@primevue/core/api";

import Table from "@components/Table/Table.vue";
import DirectoryService from "@services/DirectoryService.js";
import PageContainer from "@components/Containers/Admin/PageContainer.vue";

const {user} = useAuthStore();

const items = ref([]);
const loading = ref(true);

const directoryTitle = 'Ценовые периоды';
const directoryUrl = 'price_periods';

const breadcrumbs = ref([
    {
        name: directoryTitle,
        route: null
    },
]);
const columns = ref([
    {
        header: 'ID',
        field: 'id',
    },
    {
        header: 'Дней от',
        field: 'days_from',
    },
    {
        header: 'Дней до',
        field: 'days_until',
    },
    {
        header: 'Статус',
        field: 'archive',
    }
]);
const statuses = ref(['Активен', 'В архиве']);
const filters = ref({
    id: {operator: FilterOperator.OR, constraints: [{value: null, matchMode: FilterMatchMode.EQUALS}]},
    days_from: {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.STARTS_WITH}]},
    days_until: {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.STARTS_WITH}]},
    archive: {operator: FilterOperator.OR, constraints: [{value: "Активен", matchMode: FilterMatchMode.EQUALS}]},
});
const filterFields = ref(['id', 'days_from', 'days_until', 'archive']);

const handleAddButtonClick = () => {
    router.push(`/Admin/directory/${directoryUrl}/new`);
}
const handleRowClick = (item) => {
    router.push(`/Admin/directory/${directoryUrl}/` + item.id);
}

async function fetchData() {
    items.value = await DirectoryService.getPricePeriods({company_id: user.company_id});
    loading.value = false;
}

onMounted(() => {
    fetchData();
});
</script>

<template>
    <PageContainer :breadcrumbs="breadcrumbs">
        <Table
            :title="directoryTitle" :items="items" :columns="columns"
            :filters="filters" :filterFields="filterFields"
            :loading="loading" @onRowClick="handleRowClick"
        >
            <template #columns>
                <Column field="id" header="ID" dataType="numeric" headerStyle="width: 3rem; min-width: 3rem;" sortable>
                    <template #filter="{ filterModel }">
                        <InputNumber v-model="filterModel.value" min="1" placeholder="Поиск по ID"/>
                    </template>
                </Column>
                <Column field="days_from" header="Дней от" sortable>
                    <template #filter="{ filterModel }">
                        <InputNumber v-model="filterModel.value" min="1" placeholder="Поиск по дней от"/>
                    </template>
                </Column>
                <Column field="days_until" header="Дней до" sortable>
                    <template #filter="{ filterModel }">
                        <InputNumber v-model="filterModel.value" min="1" placeholder="Поиск по дней до"/>
                    </template>
                </Column>
                <Column field="archive" header="Статус" sortable>
                    <template #filter="{ filterModel }">
                        <Dropdown v-model="filterModel.value" :options="statuses" placeholder="Все"
                                  class="p-column-filter"
                                  showClear/>
                    </template>
                </Column>
            </template>
            <template #buttons>
                <Button v-if="user.access.directory === 2" type="button" icon="pi pi-plus" label="Добавить" outlined
                        @click="handleAddButtonClick"/>
            </template>
        </Table>
    </PageContainer>
</template>